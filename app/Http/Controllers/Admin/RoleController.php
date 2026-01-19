<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission: role.allow')->only(['index', 'show']);
        $this->middleware('permission: role.create')->only(['store']);
        $this->middleware('permission: role.edit')->only(['update']);
        $this->middleware('permission: role.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        if (request()->ajax()) {
            $data = $request->all();
            $where_str    = "1 = ?";
            $where_params = array(1);
            if (!empty($data['search']['value'])) {
                $search     = $data['search']['value'];
                $where_str .= " and (name like \"%{$search}%\""
                    . ")";
            }
            $columns = ['id', 'name'];
            $tableDataCount = Role::select($columns)
                ->whereRaw($where_str, $where_params)
                ->count();

            $tableData = Role::select($columns)
                ->whereRaw($where_str, $where_params);

            if ($data['start'] != '' && $data['length'] != '') {
                $tableData = $tableData->take($data['length'])
                    ->skip($data['start']);
            }

            if (isset($data['order']) && !empty($data['order'])) {
                $order = head($data['order']);
                $columnIndex = $order['column'] ?? 0;
                $direction = $order['dir'] ?? 'asc';

                if (isset($columns[$columnIndex])) {
                    $column = $columns[$columnIndex];
                    $tableData = $tableData->orderBy($column, $direction);
                }
            } else {
                // Default order if none provided
                $tableData = $tableData->orderBy('id', 'desc');
            }

            $tableData = $tableData->get();
            // dd($tableData->toArray());
            $response['iTotalDisplayRecords'] = $tableDataCount;
            $response['iTotalRecords'] = $tableDataCount;
            $response['draw'] = intval(collect($data)->get('draw'));
            $response['aaData'] = $tableData->toArray();

            return $response;
        }
        return view('admin.role.index');
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $request->validate([
            'name' => 'required|unique:roles,name'
        ]);
        try {
            $role = new Role();
            $role->fill($data);
            $role->save();
            return response()->json(['success' => true, 'message' => 'Role created successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    public function show($id)
    {
        if ($id != 'new') {
            $roleData = Role::find($id);
            $html = view('admin.role.role-modal', ['roleData' => $roleData])->render();
        } else {
            $html = view('admin.role.role-modal')->render();
        }
        return response()->json(['success' => true, 'html' => $html], 200);
    }

    public function update(Request $request, $id)
    {
        $data = $request->all();
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);
        try {
            $role = Role::find($id);
            $role->fill($data);
            $role->save();
            return response()->json(['success' => true, 'message' => 'Role created successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();
            return response()->json(['success' => true, 'message' => 'Role deleted successfully'], 200);
        } catch (\Throwable $th) {
            Log::info($th->getMessage());
            return response()->json(['success' => false, 'message' => 'Something went wrong!'], 500);
        }
    }

    public function addPermission($id)
    {
        $role = Role::findOrFail($id);

        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('.', $item->name)[0]; // module
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        $html = view('admin.role.give-permission-modal', compact(
            'role',
            'permissions',
            'rolePermissions'
        ))->render();

        return response()->json(['success' => true, 'html' => $html], 200);
    }

    public function givePermission(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $permissions = $request->input('permissions', []);

        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        $permissions = array_filter($permissions);

        // 1️⃣ Update Role Permissions
        $role->syncPermissions($permissions);

        // 2️⃣ Apply to users if asked
        $applyToUsers = $request->apply_to_users ?? 0;

        if ($applyToUsers == 1) {
            $users = $role->users()->get();  // all users using this role

            foreach ($users as $user) {
                $user->syncPermissions($permissions);
            }

            return response()->json([
                'success' => true,
                'status' => "role_and_users_updated",
                'message' => "Role and all related users permissions updated successfully"
            ]);
        }

        return response()->json([
            'success' => true,
            'status' => "role_updated_only",
            'message' => "Role permissions updated successfully"
        ]);
    }

    public function getRolePermission($id)
    {
        $role = Role::findOrFail($id);

        $permissions = $role->permissions->pluck('name')->toArray();

        $grouped = [];

        foreach ($permissions as $perm) {
            [$module, $type] = explode('.', $perm);
            $grouped[$module][] = $type;
        }

        return response()->json([
            'success' => true,
            'rolePermission' => $grouped
        ], 200);
    }
}
