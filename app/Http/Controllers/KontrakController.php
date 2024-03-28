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
        return view('kontrak.index');
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
                $btn = '<a href="' . route("kontrak.edit", ['id' => Crypt::encrypt($row->idkontrak), 'kd_skpd' => Crypt::encrypt($row->kodeskpd)]) . '" class="btn btn-sm btn-warning" style="margin-right:4px"><i class="fadeIn animated bx bx-edit"></i></a>';
                $btn .= '<a onclick="hapus(\'' . $row->idkontrak . '\',\'' . $row->kodeskpd . '\')" class="btn btn-sm btn-danger"><i class="fadeIn animated bx bx-trash"></i></a>';
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

        $tahun = $this->tahun;

        return view('kontrak.create', compact('daftar_rekening', 'skpd', 'tahun'));
    }

    public function store(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();

        try {
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

            DB::table('trhkontrak')
                ->insert([
                    'idkontrak' => $idkontrak,
                    'nomorkontrak' => $data['no_kontrak'],
                    'tanggalkontrak' => $data['tgl_kontrak'],
                    'adendum' => '0',
                    'nomorkontraklalu' => $data['no_kontrak'],
                    'nilaikontrak' => floatval($data['total_rincian_kontrak']),
                    'kodeskpd' => $skpd->kd_skpd,
                    'namaskpd' => $skpd->nm_skpd,
                    'pekerjaan' => $data['nm_kerja'],
                    'rekanan' => $data['rekanan'],
                    'pimpinan' => $data['pimpinan'],
                    'rekening' => $data['rekening'],
                    'bank' => $data['bank'],
                    'npwp' => $data['npwp'],
                    'urut' => $urut,
                ]);

            $data['kontrak'] = json_decode($data['kontrak'], true);

            if (isset($data['kontrak'])) {
                DB::table('trdkontrak')
                    ->insert(array_map(function ($value) use ($idkontrak, $data) {
                        return [
                            'idkontrak' => $idkontrak,
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
                            'spek' => $value['spesifikasi'],
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

        $daftar_rekening = $this->connection
            ->table('ms_rekening_bank_online')
            ->select('rekening', 'bank', 'nm_bank', 'npwp', 'nmrekan')
            ->where(['kd_skpd' => $kd_skpd])
            ->get();

        $kontrak = DB::table('trhkontrak')
            ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd, 'adendum' => '0'])
            ->first();

        $tahun = $this->tahun;

        $detail_kontrak1 = DB::table('trdkontrak as a')
            ->join('trhkontrak as b', function ($join) {
                $join->on('a.idkontrak', '=', 'b.idkontrak');
            })
            ->where(['b.idkontrak' => $id, 'b.kodeskpd' => $kd_skpd, 'b.adendum' => '0']);

        $detail_kontrak = $detail_kontrak1->get();

        $kd_sub_kegiatan = $detail_kontrak1->first()->kodesubkegiatan;

        return view('kontrak.edit', compact('daftar_rekening', 'tahun', 'kontrak', 'detail_kontrak', 'kd_sub_kegiatan'));
    }

    public function update(Request $request)
    {
        $data = $request->data;

        DB::beginTransaction();

        try {
            $nomor_kontrak_lama = DB::table('trhkontrak')
                ->where(['idkontrak' => $data['id_kontrak'], 'kodeskpd' => $data['kd_skpd'], 'adendum' => '0'])
                ->first()
                ->nomorkontrak;

            DB::table('trhkontrak')
                ->where(['idkontrak' => $data['id_kontrak'], 'kodeskpd' => $data['kd_skpd']])
                ->update([
                    'nomorkontrak' => $data['no_kontrak'],
                    'tanggalkontrak' => $data['tgl_kontrak'],
                    'nomorkontraklalu' => $data['no_kontrak'],
                    'nilaikontrak' => floatval($data['total_rincian_kontrak']),
                    'pekerjaan' => $data['nm_kerja'],
                    'rekanan' => $data['rekanan'],
                    'pimpinan' => $data['pimpinan'],
                    'rekening' => $data['rekening'],
                    'bank' => $data['bank'],
                    'npwp' => $data['npwp'],
                ]);

            $data['kontrak'] = json_decode($data['kontrak'], true);

            DB::table('trdkontrak')
                ->where(['idkontrak' => $data['id_kontrak'], 'nomorkontrak' => $nomor_kontrak_lama])
                ->delete();

            if (isset($data['kontrak'])) {
                DB::table('trdkontrak')
                    ->insert(array_map(function ($value) use ($data) {
                        return [
                            'idkontrak' => $data['id_kontrak'],
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
                            'spek' => $value['spesifikasi'],
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
                'message' => 'Error, Data tidak berhasil ditambahkan!',
            ], 400);
        }
    }

    public function delete(Request $request)
    {
        $id = $request->id;
        $kd_skpd = $request->kd_skpd;

        DB::beginTransaction();

        try {
            DB::table('trhkontrak')
                ->where(['idkontrak' => $id, 'kodeskpd' => $kd_skpd])
                ->delete();

            DB::table('trdkontrak')
                ->where(['idkontrak' => $id])
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
}
