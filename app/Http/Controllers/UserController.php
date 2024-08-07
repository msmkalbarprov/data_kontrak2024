<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function __construct()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('user.index');
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
        $query = DB::table('users');

        // Search
        $search = $request->search;
        $query = $query->where(function ($query) use ($search) {
            $query->orWhere('name', 'like', "%" . $search . "%");
        });

        $orderByName = 'name';
        switch ($orderColumnIndex) {
            case '0':
                $orderByName = 'name';
                break;
        }
        $query = $query->orderBy($orderByName, $orderBy);
        $recordsFiltered = $recordsTotal = $query->count();
        $users = $query->skip($skip)->take($pageLength)->get();

        return Datatables::of($users)
            ->addColumn('aksi', function ($row) {
                $btn = '<a href="' . route("user.edit", Crypt::encrypt($row->id)) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';
                $btn .= '<a onclick="hapus(\'' . $row->id . '\')" class="btn btn-md btn-danger">Delete</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kd_skpd = DB::connection('simakda')
            ->table('ms_skpd')
            ->select('kd_skpd', 'nm_skpd')
            ->orderBy('kd_skpd')
            ->get();

        $daftar_peran = Role::all();

        return view('user.create', compact('kd_skpd', 'daftar_peran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'username' => $request->username,
                'kd_skpd' => $request->kd_skpd,
                'password' => Hash::make($request->password),
                'tipe' => $request->tipe,
                'status_aktif' => $request->status_aktif,
                'role' => $request->role,
                'jabatan' => $request->jabatan,
            ]);

            $user->syncRoles($request->role);

            DB::commit();
            return redirect()
                ->route('user.index')
                ->with('message', 'User berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('user.create')->withInput()->with('message', 'Data gagal disimpan');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $id = Crypt::decrypt($id);

        $kd_skpd = DB::connection('simakda')
            ->table('ms_skpd')
            ->select('kd_skpd', 'nm_skpd')
            ->orderBy('kd_skpd')
            ->get();

        $data = User::find($id);
        // dd($data->id);
        $daftar_peran = Role::all();

        return view('user.edit', compact('kd_skpd', 'daftar_peran', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $user = User::find($id);

            $user
                ->update([
                    'name' => $request->name,
                    'username' => $request->username,
                    'kd_skpd' => $request->kd_skpd,
                    // 'password' => Hash::make($request->password),
                    'tipe' => $request->tipe,
                    'status_aktif' => $request->status_aktif,
                    'role' => $request->role,
                    'jabatan' => $request->jabatan,
                ]);

            $user->syncRoles($request->role);

            DB::commit();
            return redirect()
                ->route('user.index')
                ->with('message', 'User berhasil diupdate!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try {
            User::find($id)
                ->delete();

            DB::commit();
            return response()->json([
                'status' => true,
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back();
        }
    }

    // GANTI SKPD
    public function ubahSkpd()
    {
        $daftarSkpd = DB::connection('simakda')
            ->table('ms_skpd')
            ->select('kd_skpd', 'nm_skpd')
            ->groupBy('kd_skpd', 'nm_skpd')
            ->get();

        return view('ubahskpd.index', compact('daftarSkpd'));
    }

    public function simpanUbahSkpd(Request $request)
    {
        $id = Auth::user()->id;

        DB::beginTransaction();
        try {
            $user = User::find($id);

            $user
                ->update([
                    'kd_skpd' => $request->kodeskpd,
                ]);

            DB::commit();
            return redirect()
                ->route('ubah_skpd.index')
                ->with('message', 'SKPD berhasil diubah');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
        }
    }

    // UBAH PASSWORD
    public function ubahPassword()
    {
        $daftarSkpd = DB::connection('simakda')
            ->table('ms_skpd')
            ->select('kd_skpd', 'nm_skpd')
            ->groupBy('kd_skpd', 'nm_skpd')
            ->get();

        return view('ubahpassword.index', compact('daftarSkpd'));
    }

    public function simpanUbahPassword(Request $request)
    {
        $id = Auth::user()->id;

        $validated = $request->validate([
            'old_password' => ['required'],
            'new_password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised()],
            'confirmation_password' => ['required', Password::min(8)
                ->letters()
                ->mixedCase()
                ->numbers()
                ->symbols()
                ->uncompromised(), 'same:new_password'],
        ]);

        $password_lama = DB::table('users')
            ->where(['id' => Auth::user()->id])
            ->first()
            ->password;

        if (!Hash::check($request->old_password, $password_lama)) {
            return redirect()->route('ubah_password.index')->withInput()->with('error', 'Pasword lama tidak sesuai');
        }

        DB::beginTransaction();
        try {
            $user = User::find($id);

            $user
                ->update([
                    'password' => Hash::make($request->new_password)
                ]);

            DB::commit();
            return redirect()
                ->route('ubah_password.index')
                ->with('message', 'Password berhasil diubah');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('ubah_password.index')->withInput()->with('error', 'Ubah passsword gagal');
        }
    }
}
