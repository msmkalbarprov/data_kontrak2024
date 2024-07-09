<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class KontrakController extends Controller
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
        $data = [
            'dataTtd' => DB::connection('simakda')
                ->table('ms_ttd')
                ->where(['kd_skpd' => Auth::user()->kd_skpd])
                ->whereIn('kode', ['PA', 'KPA'])
                ->get(),
            'dataPpk' => DB::connection('simakda')
                ->table('ms_ttd')
                ->where(['kd_skpd' => Auth::user()->kd_skpd])
                ->whereIn('kode', ['PPK'])
                ->get()
        ];

        return view('kontrak.index')->with($data);
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
        $query = DB::table('trhkontrak as a')
            ->selectRaw("a.*, (SELECT COUNT(*) FROM trhkontrak b WHERE b.nomorkontraklalu=a.nomorkontrak and a.kodeskpd=b.kodeskpd and b.adendum !=?) as cekAdendum", ['0'])
            ->selectRaw("(select count(*) from trhbast c where c.nomorkontrak=a.nomorkontrak and c.idkontrak=a.idkontrak and c.kodeskpd=a.kodeskpd) as cekBast")
            ->where(['a.kodeskpd' => Auth::user()->kd_skpd])
            ->where(function ($query) {
                $query->where('adendum', '0')
                    ->orWhereNull('adendum');
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
                $btn = '<a href="' . route("kontrak.edit", ['id' => Crypt::encrypt($row->idkontrak), 'kd_skpd' => Crypt::encrypt($row->kodeskpd)]) . '" class="btn btn-sm btn-warning" style="margin:0px 4px"><i class="fadeIn animated bx bx-edit"></i></a>';

                if ($row->cekAdendum == 0 && $row->cekBast == 0) {
                    $btn .= '<a onclick="hapus(\'' . $row->idkontrak . '\',\'' . $row->nomorkontrak . '\',\'' . $row->kodeskpd . '\')" class="btn btn-sm btn-danger" style="margin:0px 4px"><i class="fadeIn animated bx bx-trash"></i></a>';
                }

                $btn .= '<a onclick="cetak(\'' . $row->idkontrak . '\',\'' . $row->nomorkontrak . '\',\'' . $row->kodeskpd . '\')" class="btn btn-sm btn-success" style="margin:0px 4px"><i class="fadeIn animated bx bx-printer"></i></a>';

                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        // $daftar_rekening = $this->connection
        //     ->table('ms_rekening_bank_online')
        //     ->select('rekening', 'bank', 'nm_bank', 'npwp', 'nmrekan')
        //     ->where(['kd_skpd' => Auth::user()->kd_skpd])
        //     ->get();

        $skpd = $this->connection
            ->table('ms_skpd')
            ->select('kd_skpd', 'nm_skpd')
            ->where(['kd_skpd' => Auth::user()->kd_skpd])
            ->first();

        $tahun = $this->tahun;

        $status_anggaran = "M";

        if ($status_anggaran == '0') {
            return redirect()
                ->route('kontrak.index')
                ->with('message', 'Anggaran belum disahkan, hubungi Anggaran!');;
        }

        return view('kontrak.create', compact('skpd', 'tahun', 'status_anggaran'));
    }

    public function store(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();

        try {
            $nomorKontrak = $data['tipe'] == 1 ? $data['no_kontrak'] : $data['no_pesanan'];

            $skpd = $this->connection
                ->table('ms_skpd')
                ->select('kd_skpd', 'nm_skpd')
                ->where(['kd_skpd' => Auth::user()->kd_skpd])
                ->first();

            $urut = DB::table('trhkontrak')
                ->selectRaw("ISNULL(MAX(urut),0)+1 as urut")
                ->where(['kodeskpd' => Auth::user()->kd_skpd, 'adendum' => '0'])
                ->first()
                ->urut;

            $idkontrak = $urut . "/KONTRAK" . "/" . $skpd->kd_skpd . "/" . $this->tahun;

            $cekIdKontrak = DB::table('trhkontrak')
                ->where(['idkontrak' => $idkontrak, 'kodeskpd' => $skpd->kd_skpd])
                ->count();

            if ($cekIdKontrak > 0) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error, ID Kontrak telah digunakan!',
                ], 400);
            }

            $cekNomorKontrak = DB::table('trhkontrak')
                ->where(['nomorkontrak' => $nomorKontrak, 'kodeskpd' => $skpd->kd_skpd])
                ->count();

            if ($cekNomorKontrak > 0) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error, Nomor Kontrak telah digunakan!',
                ], 400);
            }

            if ($data['jenis'] == 1 && floatval($data['total_rincian_kontrak']) > 15000000) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error, Nilai melebihi 15 juta untuk Kontrak UP/GU!',
                ], 400);
            }

            DB::table('trhkontrak')
                ->insert([
                    'idkontrak' => $idkontrak,
                    'nomorkontrak' => $nomorKontrak,
                    'nomorpesanan' => $data['no_pesanan'],
                    'tanggalkontrak' => $data['tgl_kontrak'],
                    'adendum' => '0',
                    'nomorkontraklalu' => $nomorKontrak,
                    'nilaikontrak' => floatval($data['total_rincian_kontrak']),
                    'kodeskpd' => $skpd->kd_skpd,
                    'namaskpd' => $skpd->nm_skpd,
                    'pekerjaan' => $data['nm_kerja'],
                    // 'rekanan' => $data['rekanan'],
                    // 'pimpinan' => $data['pimpinan'],
                    // 'rekening' => $data['rekening'],
                    // 'bank' => $data['bank'],
                    // 'npwp' => $data['npwp'],
                    'urut' => $urut,
                    'jns_ang' => $data['status_anggaran'],
                    'statusAdendum' => '0',
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_username' => Auth::user()->username,
                    'jenisspp' => $data['jenis'],
                    'tipe' => $data['tipe'],
                    'pihakketiga' => $data['pihak_ketiga'],
                    'namaperusahaan' => $data['nama_perusahaan'],
                    'alamatperusahaan' => $data['alamat_perusahaan'],
                    'tanggalawal' => $data['tanggal_awal'],
                    'tanggalakhir' => $data['tanggal_akhir'],
                    'ketentuansanksi' => $data['sanksi'],
                    'carapembayaran' => $data['pembayaran'],
                    'metodepengadaan' => $data['metode']
                ]);

            $data['kontrak'] = json_decode($data['kontrak'], true);

            $validationDetailKontrak = cekDetailKontrak($data['kontrak']);
            if ($validationDetailKontrak) {
                return response()->json([
                    'status' => false,
                    'error' => $validationDetailKontrak,
                ], 400);
            }

            $validationSumberDana = $this->cekNilaiSumber($data['kontrak'], $data['kd_skpd'], $data['status_anggaran']);

            if ($validationSumberDana) {
                return response()->json([
                    'status' => false,
                    'error' => $validationSumberDana,
                ], 400);
            }

            if (isset($data['kontrak'])) {
                DB::table('trdkontrak')
                    ->insert(array_map(function ($value) use ($idkontrak, $skpd, $nomorKontrak) {
                        return [
                            'idkontrak' => $idkontrak,
                            'nomorkontrak' => $nomorKontrak,
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
                            'kodeskpd' => $skpd->kd_skpd,
                            'namaskpd' => $skpd->nm_skpd,
                            'detailkontrak' => json_encode(dataDetailKontrak($value['detail']))
                        ];
                    }, $data['kontrak']));
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil tersimpan!'
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

    public function edit($id, $kd_skpd)
    {
        $id = Crypt::decrypt($id);
        $kd_skpd = Crypt::decrypt($kd_skpd);

        // $daftar_rekening = $this->connection
        //     ->table('ms_rekening_bank_online')
        //     ->select('rekening', 'bank', 'nm_bank', 'npwp', 'nmrekan')
        //     ->where(['kd_skpd' => $kd_skpd])
        //     ->get();

        $kontrak = DB::table('trhkontrak')
            ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'adendum' => '0'])
            ->first();

        $tahun = $this->tahun;

        $detail_kontrak1 = DB::table('trdkontrak as a')
            ->join('trhkontrak as b', function ($join) {
                $join->on('a.idkontrak', '=', 'b.idkontrak');
                $join->on('a.kodeskpd', '=', 'b.kodeskpd');
                $join->on('a.nomorkontrak', '=', 'b.nomorkontrak');
            })
            ->where(['b.idkontrak' => $id, 'b.kodeskpd' => $kd_skpd, 'b.adendum' => '0', 'b.nomorkontrak' => $kontrak->nomorkontrak]);

        $detail_kontrak = $detail_kontrak1->get();

        $kd_sub_kegiatan = $detail_kontrak1->first()->kodesubkegiatan;

        $status_anggaran = status_anggaran();

        if ($status_anggaran == '0') {
            return redirect()
                ->route('kontrak.index')
                ->with('message', 'Anggaran belum disahkan, hubungi Anggaran!');
        }

        $cekKontrakAdendum = DB::table('trhkontrak')
            ->where(['nomorkontraklalu' => $kontrak->nomorkontrak, 'kodeskpd' => $kd_skpd])
            ->where('adendum', '!=', '0')
            ->count();

        $cekBast = DB::table('trhbast')
            ->where(['nomorkontrak' => $kontrak->nomorkontrak, 'kodeskpd' => $kd_skpd, 'idkontrak' => $id])
            ->count();

        return view('kontrak.edit', compact('tahun', 'kontrak', 'detail_kontrak', 'kd_sub_kegiatan', 'cekKontrakAdendum', 'cekBast'));
    }

    public function update(Request $request)
    {
        $data = $request->data;
        // dd($data);
        DB::beginTransaction();

        try {
            $nomorKontrak = $data['tipe'] == 1 ? $data['no_kontrak'] : $data['no_pesanan'];

            $nomor_kontrak_lama = DB::table('trhkontrak')
                ->where(['idkontrak' => $data['id_kontrak'], 'kodeskpd' => $data['kd_skpd'], 'adendum' => '0'])
                ->first()
                ->nomorkontrak;

            $cekKontrakAdendum = DB::table('trhkontrak')
                ->where(['idkontrak' => $data['id_kontrak'], 'nomorkontraklalu' => $nomor_kontrak_lama, 'kodeskpd' => $data['kd_skpd']])
                ->where('adendum', '!=', '0')
                ->count();

            if ($cekKontrakAdendum > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error, Kontrak telah diadendum!',
                ], 400);
            }

            $cekNomorKontrak = DB::table('trhkontrak')
                ->where(['nomorkontrak' => $nomorKontrak, 'kodeskpd' => $data['kd_skpd']])
                ->count();

            if (($nomor_kontrak_lama != $nomorKontrak) && $cekNomorKontrak > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error, Nomor kontrak telah ada!',
                ], 400);
            }

            if ($data['jenis'] == 1 && floatval($data['total_rincian_kontrak']) > 15000000) {
                return response()->json([
                    'status' => false,
                    'error' => 'Error, Nilai melebihi 15 juta untuk Kontrak UP/GU!',
                ], 400);
            }

            DB::table('trhkontrak')
                ->where(['idkontrak' => $data['id_kontrak'], 'kodeskpd' => $data['kd_skpd']])
                ->update([
                    'nomorkontrak' => $nomorKontrak,
                    'tanggalkontrak' => $data['tgl_kontrak'],
                    'nomorkontraklalu' => $nomorKontrak,
                    'nomorpesanan' => $data['no_pesanan'],
                    'nilaikontrak' => floatval($data['total_rincian_kontrak']),
                    'pekerjaan' => $data['nm_kerja'],
                    // 'rekanan' => $data['rekanan'],
                    // 'pimpinan' => $data['pimpinan'],
                    // 'rekening' => $data['rekening'],
                    // 'bank' => $data['bank'],
                    // 'npwp' => $data['npwp'],
                    'pihakketiga' => $data['pihak_ketiga'],
                    'namaperusahaan' => $data['nama_perusahaan'],
                    'alamatperusahaan' => $data['alamat_perusahaan'],
                    'jenisspp' => $data['jenis'],
                    'tipe' => $data['tipe'],
                    'tanggalawal' => $data['tanggal_awal'],
                    'tanggalakhir' => $data['tanggal_akhir'],
                    'ketentuansanksi' => $data['sanksi'],
                    'carapembayaran' => $data['pembayaran'],
                    'metodepengadaan' => $data['metode']
                ]);

            $data['kontrak'] = json_decode($data['kontrak'], true);

            $validationDetailKontrak = cekDetailKontrak($data['kontrak']);
            if ($validationDetailKontrak) {
                return response()->json([
                    'status' => false,
                    'error' => $validationDetailKontrak,
                ], 400);
            }

            $validationSumberDana = $this->cekNilaiSumber($data['kontrak'], $data['kd_skpd'], $data['status_anggaran']);

            if ($validationSumberDana) {
                return response()->json([
                    'status' => false,
                    'error' => $validationSumberDana,
                ], 400);
            }

            DB::table('trdkontrak')
                ->where(['idkontrak' => $data['id_kontrak'], 'nomorkontrak' => $nomor_kontrak_lama, 'kodeskpd' => $data['kd_skpd']])
                ->delete();

            $skpd = $this->connection->table('ms_skpd')
                ->where(['kd_skpd' => $data['kd_skpd']])
                ->first();

            if (isset($data['kontrak'])) {
                DB::table('trdkontrak')
                    ->insert(array_map(function ($value) use ($data, $skpd, $nomorKontrak) {
                        return [
                            'idkontrak' => $data['id_kontrak'],
                            'nomorkontrak' => $nomorKontrak,
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
                            'kodeskpd' => $skpd->kd_skpd,
                            'namaskpd' => $skpd->nm_skpd,
                            'detailkontrak' => json_encode(dataDetailKontrak($value['detail']))
                        ];
                    }, $data['kontrak']));
            }

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil terupdate!'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Error, Data tidak berhasil diupdate!',
                'e' => $e->getMessage()
            ], 400);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $nomorkontrak = $request->nomorkontrak;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();

        try {
            $cekKontrakAdendum = DB::table('trhkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd])
                ->where('adendum', '!=', '0')
                ->count();

            if ($cekKontrakAdendum > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kontrak telah diadendum, tidak bisa dihapus!'
                ], 400);
            }

            $cekBast = DB::table('trhbast')
                ->where(['nomorkontrak' => $nomorkontrak, 'kodeskpd' => $kd_skpd, 'idkontrak' => $id])
                ->count();

            if ($cekBast > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Kontrak telah ada di BAP/BAST, tidak bisa dihapus!'
                ], 400);
            }

            DB::table('trhkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'nomorkontrak' => $nomorkontrak])
                ->delete();

            DB::table('trdkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'nomorkontrak' => $nomorkontrak])
                ->delete();

            DB::commit();
            return response()->json([
                'status' => true,
                'message' => 'Data berhasil dihapus'
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Data tidak berhasil dihapus'
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

            $realisasiKontrak = DB::table('trdbapbast as a')
                ->join('trhbast as b', function ($join) {
                    // $join->on('a.nomorpesanan', '=', 'b.nomorpesanan');
                    $join->on('a.nomorbapbast', '=', 'b.nomorbapbast');
                    $join->on('a.kodeskpd', '=', 'b.kodeskpd');
                })
                ->where([
                    'a.kodeskpd' => $kd_skpd,
                    'a.kodesubkegiatan' => $item['kd_sub_kegiatan'],
                    'a.kodeakun' => $item['kd_rek6'],
                    'a.kodebarang' => $item['kd_barang'],
                    'a.header' => $item['header'],
                    'a.subheader' => $item['sub_header'],
                    'a.kodesumberdana' => $item['sumber'],
                ])
                ->selectRaw("ISNULL(sum(volume1),0) as volume1,ISNULL(sum(volume2),0) as volume2,ISNULL(sum(volume3),0) as volume3,ISNULL(sum(volume4),0) as volume4")
                ->first();

            if ($item['volume1'] > $sumber->volume1) {
                $message .= "Input volume 1 melebihi anggaran volume 1, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }

            if ($item['volume2'] > $sumber->volume2) {
                $message .= "Input volume 2 melebihi anggaran volume 2, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }

            if ($item['volume3'] > $sumber->volume3) {
                $message .= "Input volume 3 melebihi anggaran volume 3, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }

            if ($item['volume4'] > $sumber->volume4) {
                $message .= "Input volume 4 melebihi anggaran volume 4, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }

            if ($item['total'] > $sumber->total) {
                $message .= "Total inputan melebihi total anggaran, dengan Kode Barang : " . $item['kd_barang'] . " <br/> ";
            }

            // PROTEKSI REALISASI TERHADAP ANGGARAN SAAT INI (AKHIR)
            if (floatval($item['volume1']) > ($sumber->volume1 - $realisasiKontrak->volume1)) {
                $message .= "Input volume 1 melebihi sisa anggaran volume 1 : " . rupiah($sumber->volume1 - $realisasiKontrak->volume1) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
            }

            if (floatval($item['volume2']) > ($sumber->volume2 - $realisasiKontrak->volume2)) {
                $message .= "Input volume 2 melebihi sisa anggaran volume 2 : " . rupiah($sumber->volume2 - $realisasiKontrak->volume2) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
            }

            if (floatval($item['volume3']) > ($sumber->volume3 - $realisasiKontrak->volume3)) {
                $message .= "Input volume 3 melebihi sisa anggaran volume 3 : " . rupiah($sumber->volume3 - $realisasiKontrak->volume3) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
            }

            if (floatval($item['volume4']) > ($sumber->volume4 - $realisasiKontrak->volume4)) {
                $message .= "Input volume 4 melebihi sisa anggaran volume 4 : " . rupiah($sumber->volume4 - $realisasiKontrak->volume4) . ". Jenis Anggaran : " . namaAnggaran($status_anggaran) . " <br/> ";
            }
            // PROTEKSI REALISASI TERHADAP ANGGARAN SAAT INI (AKHIR)
        }

        return $message;
    }

    // public function cekDetailKontrak($request)
    // {
    //     $message = '';

    //     foreach ($request as $item) {
    //         if (!empty($item['detail'])) {
    //             if ($item['detail']['kelompok'] == '5201') {
    //                 if (!$item['detail']['nomor_sertifikat']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi nomor sertifikat! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['tanggal_sertifikat']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi tanggal sertifikat! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['panjang']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi panjang! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['lebar']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi lebar! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['luas']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi luas! <br/><br/>";
    //                 }

    //                 if ($item['detail']['panjang'] == 0) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Panjang tidak boleh 0! <br/><br/>";
    //                 }

    //                 if ($item['detail']['lebar'] == 0) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Lebar tidak boleh 0! <br/><br/>";
    //                 }

    //                 if ($item['detail']['luas'] == 0) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Luas tidak boleh 0! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['status_tanah']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan pilih status tanah! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['penggunaan']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Penggunaan tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if ($message != '') {
    //                     return $message;
    //                 }
    //             }

    //             if ($item['detail']['kelompok'] == '5202') {
    //                 if (!$item['detail']['merk']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Merk tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['ukuran']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Ukuran tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['pabrik']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Pabrik tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['rangka']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Rangka tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['mesin']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Mesin tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['polisi']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Polisi tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['bpkb']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .BPKB tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['bahan']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Bahan tidak boleh kosong! <br/>";
    //                 }

    //                 if ($message != '') {
    //                     return $message;
    //                 }
    //             }

    //             if ($item['detail']['kelompok'] == '5203') {
    //                 if (!$item['detail']['bertingkat'] && !$item['detail']['beton']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan pilih kontruksi bangunan! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['panjang']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi panjang! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['lebar']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi lebar! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['luas']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi luas! <br/><br/>";
    //                 }

    //                 if ($item['detail']['panjang'] == 0) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Panjang tidak boleh 0! <br/><br/>";
    //                 }

    //                 if ($item['detail']['lebar'] == 0) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Lebar tidak boleh 0! <br/><br/>";
    //                 }

    //                 if ($item['detail']['luas'] == 0) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Luas tidak boleh 0! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['status_tanah']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan pilih status tanah! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['penggunaan']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Penggunaan tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if ($message != '') {
    //                     return $message;
    //                 }
    //             }

    //             if ($item['detail']['kelompok'] == '5204') {
    //                 if (!$item['detail']['panjang']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi panjang! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['lebar']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi lebar! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['luas']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi luas! <br/><br/>";
    //                 }

    //                 if ($item['detail']['panjang'] == 0) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Panjang tidak boleh 0! <br/><br/>";
    //                 }

    //                 if ($item['detail']['lebar'] == 0) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Lebar tidak boleh 0! <br/><br/>";
    //                 }

    //                 if ($item['detail']['luas'] == 0) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Luas tidak boleh 0! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['status_tanah']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan pilih status tanah! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['penggunaan']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Penggunaan tidak boleh kosong! <br/><br/>";
    //                 }

    //                 if ($message != '') {
    //                     return $message;
    //                 }
    //             }

    //             if ($item['detail']['kelompok'] == '5205') {
    //                 if (!$item['detail']['judul_buku']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi judul buku/perpustakaan! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['pencipta_buku']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi pencipta buku/perpustakaan! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['spesifikasi_buku']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi spesifikasi buku/perpustakaan! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['asal_daerah']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi asal daerah barang bercorak! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['pencipta_daerah']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi pencipta barang bercorak! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['bahan_daerah']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi bahan barang bercorak! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['jenis_hewan']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi jenis hewan/ternak tumbuhan! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['ukuran_hewan']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi ukuran hewan/ternak tumbuhan! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['nik_hewan']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi NIK! <br/><br/>";
    //                 }


    //                 if ($message != '') {
    //                     return $message;
    //                 }
    //             }

    //             if ($item['detail']['kelompok'] == '5206') {
    //                 if (!$item['detail']['nama_aplikasi']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi nama aplikasi! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['judul_aplikasi']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi judul aplikasi! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['pencipta_aplikasi']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi pencipta aplikasi! <br/><br/>";
    //                 }

    //                 if (!$item['detail']['spesifikasi_aplikasi']) {
    //                     $message .= "Kegiatan " . $item['kd_sub_kegiatan'] . " ,Rekening " . $item['kd_rek6'] . " ,Kode Barang " . $item['kd_barang'] . " .Silahkan isi spesifikasi aplikasi! <br/><br/>";
    //                 }


    //                 if ($message != '') {
    //                     return $message;
    //                 }
    //             }
    //         }
    //     }

    //     return $message;
    // }

    // public function dataDetailKontrak($request)
    // {
    //     if (!empty($request)) {
    //         if ($request['kelompok'] == '5201') {
    //             $detailKontrak = [
    //                 'kelompok' => $request['kelompok'],
    //                 'nomor_sertifikat' => $request['nomor_sertifikat'],
    //                 'tanggal_sertifikat' => $request['tanggal_sertifikat'],
    //                 'status_tanah' => $request['status_tanah'],
    //                 'penggunaan' => $request['penggunaan'],
    //                 'panjang' => $request['panjang'],
    //                 'lebar' => $request['lebar'],
    //                 'luas' => $request['luas'],
    //             ];
    //         } else if ($request['kelompok'] == '5202') {
    //             $detailKontrak = [
    //                 'kelompok' => $request['kelompok'],
    //                 'merk' => $request['merk'],
    //                 'ukuran' => $request['ukuran'],
    //                 'pabrik' => $request['pabrik'],
    //                 'rangka' => $request['rangka'],
    //                 'mesin' => $request['mesin'],
    //                 'polisi' => $request['polisi'],
    //                 'bpkb' => $request['bpkb'],
    //                 'bahan' => $request['bahan'],
    //             ];
    //         } else if ($request['kelompok'] == '5203') {
    //             $detailKontrak = [
    //                 'kelompok' => $request['kelompok'],
    //                 'status_tanah' => $request['status_tanah'],
    //                 'penggunaan' => $request['penggunaan'],
    //                 'panjang' => $request['panjang'],
    //                 'lebar' => $request['lebar'],
    //                 'luas' => $request['luas'],
    //                 'bertingkat' => $request['bertingkat'],
    //                 'beton' => $request['beton'],
    //             ];
    //         } else if ($request['kelompok'] == '5204') {
    //             $detailKontrak = [
    //                 'kelompok' => $request['kelompok'],
    //                 'status_tanah' => $request['status_tanah'],
    //                 'penggunaan' => $request['penggunaan'],
    //                 'panjang' => $request['panjang'],
    //                 'lebar' => $request['lebar'],
    //                 'luas' => $request['luas'],
    //             ];
    //         } else if ($request['kelompok'] == '5205') {
    //             $detailKontrak = [
    //                 'kelompok' => $request['kelompok'],
    //                 'judul_buku' => $request['judul_buku'],
    //                 'pencipta_buku' => $request['pencipta_buku'],
    //                 'spesifikasi_buku' => $request['spesifikasi_buku'],
    //                 'asal_daerah' => $request['asal_daerah'],
    //                 'pencipta_daerah' => $request['pencipta_daerah'],
    //                 'bahan_daerah' => $request['bahan_daerah'],
    //                 'jenis_hewan' => $request['jenis_hewan'],
    //                 'ukuran_hewan' => $request['ukuran_hewan'],
    //                 'nik_hewan' => $request['nik_hewan'],
    //             ];
    //         } else if ($request['kelompok'] == '5206') {
    //             $detailKontrak = [
    //                 'kelompok' => $request['kelompok'],
    //                 'nama_aplikasi' => $request['nama_aplikasi'],
    //                 'judul_aplikasi' => $request['judul_aplikasi'],
    //                 'pencipta_aplikasi' => $request['pencipta_aplikasi'],
    //                 'spesifikasi_aplikasi' => $request['spesifikasi_aplikasi'],
    //             ];
    //         } else {
    //             $detailKontrak = [];
    //         }
    //     } else {
    //         $detailKontrak = [];
    //     }

    //     return $detailKontrak;
    // }
}
