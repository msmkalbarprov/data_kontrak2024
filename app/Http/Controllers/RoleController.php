<?php

namespace App\Http\Controllers;

use App\Http\Requests\RoleRequest;
use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
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
        return view('peran.index');
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
        $query = DB::table('roles');

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
                $btn = '<a href="' . route("peran.edit", Crypt::encrypt($row->uuid)) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';
                $btn .= '<a onclick="hapus(\'' . $row->uuid . '\')" class="btn btn-md btn-danger">Delete</a>';
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
        $akses_tipe1 = Permission::where('parent', '')->get();
        $akses_tipe2 = Permission::where('parent', '!=', '')->get();

        return view('peran.create', compact('akses_tipe1', 'akses_tipe2'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        DB::beginTransaction();
        try {
            $role = Role::create([
                'name' => $request->name,
            ]);

            $role->syncPermissions([$request->akses, $request->check1]);

            DB::commit();
            return redirect()
                ->route('peran.index')
                ->with('message', 'Peran berhasil ditambahkan!');
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput();
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

        $data = Role::findById($id);

        $akses_tipe1 = Permission::where('parent', '')->get();

        $akses_tipe2 = Permission::where('parent', '!=', '')->get();

        $permission = $data->permissions->pluck('uuid')->all();

        return view('peran.edit', compact('data', 'akses_tipe1', 'akses_tipe2', 'permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        DB::beginTransaction();
        try {
            $role = Role::findOrFail($id);

            $role->update([
                'name' => $request->name
            ]);

            $role->syncPermissions([$request->akses, $request->check1]);

            DB::commit();
            return redirect()
                ->route('peran.index')
                ->with('message', 'Peran berhasil diupdate!');
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
            $userRole = DB::table('model_has_roles')
                ->select('model_has_roles.model_id')
                ->where('model_has_roles.role_id', $id)
                ->count();

            if ($userRole > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Peran telah digunakan di User!'
                ], 200);
            }

            Role::findById($id)
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
}
