<?php

namespace App\Http\Controllers\Admin;

// use App\Models\Permission;

use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use App\Repositories\PermissionRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class PermissionController extends Controller
{

    public function __construct()
    {
        $this->middleware('permission: permissions.allow')->only(['index', 'show']);
        $this->middleware('permission: permissions.create')->only(['store']);
        $this->middleware('permission: permissions.edit')->only(['update']);
        $this->middleware('permission: permissions.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $search = $request->input('search.value');

            $query = Permission::query()->select(['id', 'name', 'guard_name']);

            // 🔍 Search filter
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            }

            // Count total
            $totalRecords = Permission::count();

            // Count filtered
            $totalFiltered = $query->count();

            // 🔥 APPLY PAGINATION (very important)
            if (isset($filters['start']) && !empty($filters['length'])) {
                $query->take($filters['length'])
                    ->skip($filters['start']);
            }

            // Fetch data
            $data = $query->get();

            return [
                "draw"                 => intval($request->draw),
                "iTotalRecords"        => $totalRecords,
                "iTotalDisplayRecords" => $totalFiltered,
                "aaData"               => $data,
            ];
        }

        return view('admin.permission.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $baseName = strtolower($request->name);

        try {
            DB::beginTransaction();

            // If checkbox is ON → create full permission set
            if ($request->has('full_set')) {

                $types = ['allow', 'create', 'edit', 'delete'];

                foreach ($types as $type) {
                    Permission::firstOrCreate([
                        'name' => $baseName . '.' . $type,
                        'guard_name' => 'web',
                    ]);
                }

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Full permission set created successfully',
                ], 201);
            }

            // Else: create only single permission
            Permission::firstOrCreate([
                'name' => $baseName,
                'guard_name' => 'web',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully',
            ], 201);
        } catch (\Throwable $th) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        if ($id != 'new') {
            $permissionData = Permission::find($id);
            $html = view('admin.permission.permission-modal', ['permissionData' => $permissionData])->render();
        } else {
            $html = view('admin.permission.permission-modal')->render();
        }
        return response()->json(['success' => true, 'html' => $html], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $request->validate([
            'name' => ['required', 'string', "unique:permissions,name,$id,id"]
        ]);

        try {
            DB::beginTransaction();
            $permission = Permission::find($id);
            $permission->fill($data);
            $permission->save();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Permission updated successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            // dd($th);
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $permission = Permission::findOrFail($id);
            $permission->delete();
            DB::commit();
            return response()->json(['success' => true, 'message' => 'Permission deleted successfully'], 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            // dd($th);
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }
}
