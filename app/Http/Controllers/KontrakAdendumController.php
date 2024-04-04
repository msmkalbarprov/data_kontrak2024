<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KontrakAdendumController extends Controller
{
    protected $connection;
    protected $tahun;

    public function __construct()
    {
        $this->connection = DB::connection('simakda');
        $this->tahun = tahun();
    }

    public function index()
    {
        return view('kontrak_adendum.index');
    }

    public function load(Request $request)
    {
        // Page Length
        $pageNumber = ($request->start / $request->length) + 1;
        $pageLength = $request->length;
        $skip       = ($pageNumber - 1) * $pageLength;

        // Page Order
        $orderColumnIndex = $request->order[0]['column'] ?? '0';
        $orderBy = $request->order[0]['dir'] ?? 'desc';

        // get data from products table
        $query = DB::table('trhkontrak')
            ->where(function ($query) {
                $query->where('adendum', '!=', '0');
            });

        // Search
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('nomorkontrak', 'like', "%" . $search . "%");
            $query->orWhere('namaskpd', 'like', "%" . $search . "%");
        });

        $orderByName = 'urut';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'urut';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $users = $query->skip($skip)->take($pageLength)->get();


        return DataTables::of($users)
            ->addColumn('aksi', function ($row) {
                $cekKontrakAdendumSelanjutnya = DB::table('trhkontrak')
                    ->where(['idkontrak' => $row->idkontrak, 'kodeskpd' => $row->kodeskpd, 'nomorkontraklalu' => $row->nomorkontrak])
                    ->where('adendum', '>', $row->adendum)
                    ->count();

                $btn = '<a href="' . route("kontrak_adendum.edit", ['id' => Crypt::encrypt($row->idkontrak), 'nomor' => Crypt::encrypt($row->nomorkontrak), 'kd_skpd' => Crypt::encrypt($row->kodeskpd)]) . '" class="btn btn-sm btn-warning" style="margin-right:4px"><i class="fadeIn animated bx bx-edit"></i></a>';

                if ($cekKontrakAdendumSelanjutnya == 0) {
                    $btn .= '<a onclick="hapus(\'' . $row->idkontrak . '\',\'' . $row->nomorkontrak . '\',\'' . $row->nomorkontraklalu . '\',\'' . $row->kodeskpd . '\')" class="btn btn-sm btn-danger"><i class="fadeIn animated bx bx-trash"></i></a>';
                }

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $daftar_rekening = $this->connection
            ->table('ms_rekening_bank_online')
            ->select('rekening', 'bank', 'nm_bank', 'npwp', 'nmrekan')
            ->where(['kd_skpd' => Auth::user()->kd_skpd])
            ->get();

        $skpd = $this->connection
            ->table('ms_skpd')
            ->select('kd_skpd', 'nm_skpd')
            ->where(['kd_skpd' => Auth::user()->kd_skpd])
            ->first();

        $daftar_kontrak_awal = DB::table('trhkontrak')
            ->where(['kodeskpd' => Auth::user()->kd_skpd, 'statusAdendum' => '0'])
            ->get();

        $tahun = $this->tahun;

        $status_anggaran = status_anggaran();

        if ($status_anggaran == '0') {
            return redirect()
                ->route('kontrak_adendum.index')
                ->with('message', 'Anggaran belum disahkan, hubungi Anggaran!');;
        }

        return view('kontrak_adendum.create', compact('daftar_rekening', 'skpd', 'tahun', 'daftar_kontrak_awal', 'status_anggaran'));
    }

    public function store(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();

        try {
            $dataKontrakLama = DB::table('trhkontrak')
                ->where(['nomorkontrak' => $data['kontrak_awal'], 'kodeskpd' => $data['kd_skpd'], 'idkontrak' => $data['id_kontrak']])
                ->first();

            $adendum = DB::table('trhkontrak')
                ->selectRaw("ISNULL(MAX(adendum),0)+1 as adendum")
                ->where(['kodeskpd' => $dataKontrakLama->kodeskpd, 'nomorkontraklalu' => $dataKontrakLama->nomorkontraklalu])
                ->first()
                ->adendum;

            $cekNomorKontrak = DB::table('trhkontrak')
                ->where(['nomorkontrak' => $data['no_kontrak'], 'kodeskpd' => $data['kd_skpd']])
                ->count();

            if ($cekNomorKontrak > 0) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error, Nomor Kontrak telah digunakan!',
                ], 400);
            }

            DB::table('trhkontrak')
                ->insert([
                    'idkontrak' => $dataKontrakLama->idkontrak,
                    'nomorkontrak' => $data['no_kontrak'],
                    'tanggalkontrak' => $data['tgl_kontrak'],
                    'adendum' => $adendum,
                    'nomorkontraklalu' => $data['kontrak_awal'],
                    'nilaikontrak' => floatval($data['total_rincian_kontrak']),
                    'kodeskpd' => $dataKontrakLama->kodeskpd,
                    'namaskpd' => $dataKontrakLama->namaskpd,
                    'pekerjaan' => $dataKontrakLama->pekerjaan,
                    'rekanan' => $dataKontrakLama->rekanan,
                    'pimpinan' => $dataKontrakLama->pimpinan,
                    'rekening' => $dataKontrakLama->rekening,
                    'bank' => $dataKontrakLama->bank,
                    'npwp' => $dataKontrakLama->npwp,
                    'urut' => $dataKontrakLama->urut,
                    'jns_ang' => $data['status_anggaran'],
                    'statusAdendum' => '0'
                ]);

            $data['kontrak'] = json_decode($data['kontrak'], true);

            $validationSumberDana = $this->cekNilaiSumber($data['kontrak'], $data['kd_skpd'], $data['status_anggaran']);

            if ($validationSumberDana) {
                return response()->json([
                    'status' => false,
                    'error' => $validationSumberDana,
                ], 400);
            }

            if (isset($data['kontrak'])) {
                DB::table('trdkontrak')
                    ->insert(array_map(function ($value) use ($dataKontrakLama, $data) {
                        return [
                            'idkontrak' => $dataKontrakLama->idkontrak,
                            'nomorkontrak' => $data['no_kontrak'],
                            'kodesubkegiatan' => $value['kd_sub_kegiatan'],
                            'namasubkegiatan' => $value['nm_sub_kegiatan'],
                            'kodeakun' => $value['kd_rek6'],
                            'namaakun' => $value['nm_rek6'],
                            'kodebarang' => $value['kd_barang'],
                            'idtrdpo' => $value['id'],
                            'nomorpo' => $value['no_po'],
                            'header' => $value['header'],
                            'subheader' => $value['sub_header'],
                            'uraianbarang' => $value['uraian'],
                            'spek' => strval($value['spesifikasi']),
                            'harga' => floatval($value['harga']),
                            'volume1' => floatval($value['volume1']),
                            'volume2' => floatval($value['volume2']),
                            'volume3' => floatval($value['volume3']),
                            'volume4' => floatval($value['volume4']),
                            'satuan1' => $value['satuan1'],
                            'satuan2' => $value['satuan2'],
                            'satuan3' => $value['satuan3'],
                            'satuan4' => $value['satuan4'],
                            'nilai' => floatval($value['total']),
                            'kodesumberdana' => $value['sumber'],
                            'namasumberdana' => $value['nm_sumber'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_username' => Auth::user()->username,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_username' => Auth::user()->username,
                            'kodeskpd' => $dataKontrakLama->kodeskpd,
                            'namaskpd' => $dataKontrakLama->namaskpd,
                        ];
                    }, $data['kontrak']));
            }

            DB::table('trhkontrak')
                ->where(['nomorkontrak' => $data['kontrak_awal'], 'kodeskpd' => $data['kd_skpd'], 'idkontrak' => $data['id_kontrak']])
                ->update([
                    'statusAdendum' => '1'
                ]);

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil ditambahkan!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'error' => 'Error, Data tidak berhasil ditambahkan!',
                'e' => $e->getMessage()
            ], 400);
        }
    }

    public function edit($id, $nomor, $kd_skpd)
    {
        $id = Crypt::decrypt($id);
        $nomor = Crypt::decrypt($nomor);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        $daftar_rekening = $this->connection
            ->table('ms_rekening_bank_online')
            ->select('rekening', 'bank', 'nm_bank', 'npwp', 'nmrekan')
            ->where(['kd_skpd' => $kd_skpd])
            ->get();

        $dataKontrak = DB::table('trhkontrak')
            ->where(['idkontrak' => $id, 'nomorkontrak' => $nomor, 'kodeskpd' => $kd_skpd])
            ->first();

        $kontrak = DB::table('trdkontrak as a')
            ->join('trhkontrak as b', function ($join) {
                $join->on('a.idkontrak', '=', 'b.idkontrak');
                $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
                $join->on('a.kodeskpd', '=', 'b.kodeskpd');
            })
            ->where(['b.idkontrak' => $id, 'b.nomorkontrak' => $nomor, 'b.kodeskpd' => $kd_skpd, 'b.adendum' => $dataKontrak->adendum]);

        $detailKontrak = $kontrak->get();

        $kd_sub_kegiatan = $kontrak->first()->kodesubkegiatan;

        $tahun = $this->tahun;

        $status_anggaran = status_anggaran();

        $cekKontrakAdendumSelanjutnya = DB::table('trhkontrak')
            ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'nomorkontraklalu' => $dataKontrak->nomorkontrak])
            ->where('adendum', '>', $dataKontrak->adendum)
            ->count();

        if ($status_anggaran == '0') {
            return redirect()
                ->route('kontrak_adendum.index')
                ->with('message', 'Anggaran belum disahkan, hubungi Anggaran!');;
        }

        return view('kontrak_adendum.edit', compact('daftar_rekening', 'tahun', 'dataKontrak', 'detailKontrak', 'kd_sub_kegiatan', 'cekKontrakAdendumSelanjutnya'));
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $nomorkontrak = $request->nomorkontrak;
        $nomorkontraklalu = $request->nomorkontraklalu;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();

        try {
            $dataKontrak = DB::table('trhkontrak')
                ->where(['idkontrak' => $id, 'nomorkontrak' => $nomorkontrak, 'kodeskpd' => $kd_skpd])
                ->first();

            $cekKontrakAdendumSelanjutnya = DB::table('trhkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'nomorkontraklalu' => $nomorkontrak])
                ->where('adendum', '>', $dataKontrak->adendum)
                ->count();

            if ($cekKontrakAdendumSelanjutnya > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kontrak telah diadendum, tidak bisa dihapus!'
                ], 400);
            }

            DB::table('trhkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'nomorkontrak' => $nomorkontrak])
                ->delete();

            DB::table('trdkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'nomorkontrak' => $nomorkontrak])
                ->delete();

            DB::table('trhkontrak')
                ->where(['nomorkontrak' => $nomorkontraklalu, 'idkontrak' => $id, 'kodeskpd' => $kd_skpd])
                ->update(
                    [
                        'statusAdendum' => '0'
                    ]
                );

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Data tidak berhasil dihapus',
                'e' => $e->getMessage()
            ], 400);
        }
    }

    public function update(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();

        try {
            $dataKontrakLama = DB::table('trhkontrak')
                ->where(['idkontrak' => $data['idKontrak'], 'nomorkontrak' => $data['nomorKontrakTersimpan'], 'kodeskpd' => $data['kd_skpd']])
                ->first();

            $cekKontrakAdendumSelanjutnya = DB::table('trhkontrak')
                ->where(['idkontrak' => $dataKontrakLama->idkontrak, 'kodeskpd' => $dataKontrakLama->kodeskpd, 'nomorkontraklalu' => $dataKontrakLama->nomorkontrak])
                ->where('adendum', '>', $dataKontrakLama->adendum)
                ->count();

            if ($cekKontrakAdendumSelanjutnya > 0) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error, Kontrak telah diadendum!',
                ], 400);
            }

            $cekNomorKontrak = DB::table('trhkontrak')
                ->where(['nomorkontrak' => $data['no_kontrak'], 'kodeskpd' => $data['kd_skpd']])
                ->count();

            if (($data['nomorKontrakTersimpan'] != $data['no_kontrak']) && $cekNomorKontrak > 0) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error, Nomor kontrak telah ada!',
                ], 400);
            }

            DB::table('trhkontrak')
                ->where(['idkontrak' => $dataKontrakLama->idkontrak, 'nomorkontrak' => $dataKontrakLama->nomorkontrak, 'kodeskpd' => $dataKontrakLama->kodeskpd])
                ->update([
                    'nomorkontrak' => $data['no_kontrak'],
                    'tanggalkontrak' => $data['tgl_kontrak'],
                    'nilaikontrak' => floatval($data['total_rincian_kontrak']),
                ]);

            $data['kontrak'] = json_decode($data['kontrak'], true);

            $validationSumberDana = $this->cekNilaiSumber($data['kontrak'], $data['kd_skpd'], $data['status_anggaran']);

            if ($validationSumberDana) {
                return response()->json([
                    'status' => false,
                    'error' => $validationSumberDana,
                ], 400);
            }

            DB::table('trdkontrak')
                ->where(['idkontrak' => $dataKontrakLama->idkontrak, 'nomorkontrak' => $dataKontrakLama->nomorkontrak, 'kodeskpd' => $data['kd_skpd']])
                ->delete();

            if (isset($data['kontrak'])) {
                DB::table('trdkontrak')
                    ->insert(array_map(function ($value) use ($dataKontrakLama, $data) {
                        return [
                            'idkontrak' => $dataKontrakLama->idkontrak,
                            'nomorkontrak' => $data['no_kontrak'],
                            'kodesubkegiatan' => $value['kd_sub_kegiatan'],
                            'namasubkegiatan' => $value['nm_sub_kegiatan'],
                            'kodeakun' => $value['kd_rek6'],
                            'namaakun' => $value['nm_rek6'],
                            'kodebarang' => $value['kd_barang'],
                            'idtrdpo' => $value['id'],
                            'nomorpo' => $value['no_po'],
                            'header' => $value['header'],
                            'subheader' => $value['sub_header'],
                            'uraianbarang' => $value['uraian'],
                            'spek' => strval($value['spesifikasi']),
                            'harga' => floatval($value['harga']),
                            'volume1' => floatval($value['volume1']),
                            'volume2' => floatval($value['volume2']),
                            'volume3' => floatval($value['volume3']),
                            'volume4' => floatval($value['volume4']),
                            'satuan1' => $value['satuan1'],
                            'satuan2' => $value['satuan2'],
                            'satuan3' => $value['satuan3'],
                            'satuan4' => $value['satuan4'],
                            'nilai' => floatval($value['total']),
                            'kodesumberdana' => $value['sumber'],
                            'namasumberdana' => $value['nm_sumber'],
                            'created_at' => date('Y-m-d H:i:s'),
                            'created_username' => Auth::user()->username,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_username' => Auth::user()->username,
                            'kodeskpd' => $dataKontrakLama->kodeskpd,
                            'namaskpd' => $dataKontrakLama->namaskpd,
                        ];
                    }, $data['kontrak']));
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil ditambahkan!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'error' => 'Error, Data tidak berhasil ditambahkan!',
                'e' => $e->getMessage()
            ], 400);
        }
    }

    public function cekNilaiSumber($request, $kd_skpd, $status_anggaran)
    {
        $message = '';

        foreach ($request as $item) {
            $sumber = $this->connection
                ->table('trdpo as a')
                ->join('trdpo_rinci as b', function ($join) {
                    $join->on('a.jns_ang', '=', 'b.jns_ang');
                    $join->on('a.no_trdrka', '=', 'b.no_trdrka');
                    $join->on('a.header', '=', 'b.header');
                })
                ->where([
                    'a.kd_skpd' => $kd_skpd,
                    'a.kd_sub_kegiatan' => $item['kd_sub_kegiatan'],
                    'a.kd_rek6' => $item['kd_rek6'],
                    'a.jns_ang' => $status_anggaran,
                    'b.kd_barang' => $item['kd_barang'],
                    'b.header' => $item['header'],
                    'b.sub_header' => $item['sub_header'],
                ])
                ->select('a.sumber', 'a.nm_sumber', 'b.volume1', 'b.volume2', 'b.volume3', 'b.volume4', 'b.satuan1', 'b.satuan2', 'b.satuan3', 'b.satuan4', 'b.harga', 'b.total', 'b.id', 'b.no_po', 'b.uraian', 'b.spesifikasi')
                ->first();

            if ($item['volume1'] > $sumber->volume1) {
                $message .= "Input volume 1 melebihi anggaran volume 1, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }

            if ($item['volume2'] > $sumber->volume2) {
                $message .= "Input volume 2 melebihi anggaran volume 2, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }

            if ($item['volume2'] > $sumber->volume2) {
                $message .= "Input volume 3 melebihi anggaran volume 3, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }

            if ($item['volume2'] > $sumber->volume2) {
                $message .= "Input volume 4 melebihi anggaran volume 4, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }

            if ($item['total'] > $sumber->total) {
                $message .= "Total inputan melebihi total anggaran, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }
        }

        return $message;
    }
}
