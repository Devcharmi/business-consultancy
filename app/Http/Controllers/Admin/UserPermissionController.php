<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserPermissionController extends Controller
{
    public function index()
    {
        $users = User::select('id', 'name', 'email')->get();

        return view('user_permission.index', compact('users'));
    }

    public function loadRolePermissionForm(Request $request)
    {
        $roleId = $request->get('roleId');
        $role = Role::findOrFail($roleId);

        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('.', $item->name)[0];
        });

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        $userPermissions = $rolePermissions;

        $html = view(
            'admin.user_manager.permissions.permission-form',
            compact('role', 'permissions', 'userPermissions')
        )->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function loadModal($user_id)
    {
        $user = User::findOrFail($user_id);
        $roles = Role::all();

        $permissions = Permission::all()->groupBy(function ($item) {
            return explode('.', $item->name)[0];
        });

        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();
        Log::info('Permissions - ', $userPermissions);
        $assigned = $userPermissions;

        $html = view('admin.user_manager.permissions.user-permission-modal', compact(
            'user',
            'roles',
            'permissions',
            'assigned',
            'userPermissions'
        ))->render();

        return response()->json(['html' => $html]);
    }


    public function update(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $permissions = $request->input('permissions');

        // If it is a single string, convert it to array
        if (is_string($permissions)) {
            $permissions = [$permissions];
        }

        // If null, convert to empty array
        if (!is_array($permissions)) {
            $permissions = [];
        }

        // Remove empty values
        $permissions = array_filter($permissions, function ($item) {
            return !empty($item);
        });

        // Sync permissions
        $user->syncPermissions($permissions);

        return response()->json([
            'success' => true,
            'message' => 'User permissions updated successfully'
        ]);
    }
}
