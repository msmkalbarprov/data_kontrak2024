<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function __construct()
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    public function routeList()
    {
        $routeList1 = Route::getRoutes();

        $routeList = [];
        foreach ($routeList1 as $value) {
            $routeList[] = $value->getName();
        }

        return $routeList;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('akses.index');
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
        $query = DB::table('permissions');

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
                $btn = '<a href="' . route("akses.edit", Crypt::encrypt($row->uuid)) . '" class="btn btn-md btn-warning" style="margin-right:4px">Edit</a>';
                $btn .= '<a onclick="hapus(\'' . $row->uuid . '\')" class="btn btn-md btn-danger">Delete</a>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);

        // return response()->json(["draw" => $request->draw, "recordsTotal" => $recordsTotal, "recordsFiltered" => $recordsFiltered, 'data' => $users], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $permissions = Permission::where('parent', '')->get();

        return view('akses.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PermissionRequest $request)
    {
        $routeList = $this->routeList();

        if ($request->link && !in_array($request->link, $routeList)) {
            return redirect()->route('akses.create')->withInput()->with('message', 'Link belum ada, silahkan hubungi Administrator');
        }

        DB::beginTransaction();
        try {
            Permission::create([
                'name' => $request->name,
                'tipe' => $request->tipe,
                'link' => $request->tipe == '1' ? '' : $request->link,
                'parent' => $request->parent == '-' ? '' : $request->parent,
            ]);

            DB::commit();
            return redirect()->route('akses.index')->with('message', 'Akses berhasil ditambahkan!');
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

        $permissions = Permission::where('parent', '')->get();

        $data = Permission::findById($id);

        return view('akses.edit', compact('permissions', 'data'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, string $id)
    {
        $routeList = $this->routeList();

        if ($request->link && !in_array($request->link, $routeList)) {
            return redirect()->route('akses.edit', Crypt::encrypt($id))->withInput()->with('message', 'Link belum ada, silahkan hubungi Administrator');
        }

        DB::beginTransaction();
        try {
            Permission::findById($id)
                ->update([
                    'name' => $request->name,
                    'tipe' => $request->tipe,
                    'link' => $request->tipe == '1' ? '' : $request->link,
                    'parent' => $request->parent == '-' ? '' : $request->parent,
                ]);

            DB::commit();
            return redirect()->route('akses.index')->with('message', 'Akses berhasil diperbaharui!');
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
            $rolePermissions = DB::table('role_has_permissions')
                ->where('role_has_permissions.permission_id', $id)
                ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
                ->count();

            if ($rolePermissions > 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'Akses telah digunakan di Peran!'
                ], 200);
            }

            Permission::findById($id)
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
