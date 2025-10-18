<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    // ================= ROLE CRUD =================

    public function listRoles() {
        $roles = Role::all();
        return response()->json($roles);
    }

    public function createRole(Request $request) {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'array'
        ]);

        $role = Role::create(['name' => $request->name]);
        if ($request->permissions) {
            foreach ($request->permissions as $permissionName) {
                $permission = Permission::firstOrCreate(['name' => $permissionName]);
                $role->givePermissionTo($permission);
            }
        }
        return response()->json([
            'role' => $role,
            'permissions' => $role->permissions()->pluck('name')
        ]);
    }
    public function updateRolePermissions(Request $request, $roleId) {
        $request->validate([
            'permissions' => 'required|array'
        ]);
        $role = Role::findOrFail($roleId);
        $role->syncPermissions($request->permissions);
        return response()->json([
            'role' => $role,
            'permissions' => $role->permissions()->pluck('name')
        ]);
    }

    public function updateRole(Request $request, $id) {
        $request->validate(['name' => 'required|unique:roles,name,' . $id]);
        $role = Role::findOrFail($id);
        $role->name = $request->name;
        $role->save();
        return response()->json($role);
    }

    public function deleteRole($id) {
        $role = Role::findOrFail($id);
        $role->delete();
        return response()->json(['message' => 'Role deleted']);
    }

    // ================= PERMISSION CRUD =================

    public function listPermissions() {
        $permissions = Permission::all();
        return response()->json($permissions);
    }

    public function createPermission(Request $request) {
        $request->validate(['name' => 'required|unique:permissions,name']);
        $permission = Permission::create(['name' => $request->name]);
        return response()->json($permission);
    }

    public function updatePermission(Request $request, $id) {
        $request->validate(['name' => 'required|unique:permissions,name,' . $id]);
        $permission = Permission::findOrFail($id);
        $permission->name = $request->name;
        $permission->save();
        return response()->json($permission);
    }

    public function deletePermission($id) {
        $permission = Permission::findOrFail($id);
        $permission->delete();
        return response()->json(['message' => 'Permission deleted']);
    }

    // ================= GÁN ROLE / PERMISSION =================

    // Gán Role cho User
    public function assignRoleToUser(Request $request, $userId) {
        $request->validate(['role' => 'required|exists:roles,name']);
        $user = User::findOrFail($userId);
        $user->assignRole($request->role);
        return response()->json(['message' => 'Role assigned to user']);
    }

    // Gán Permission cho Role
    public function assignPermissionToRole(Request $request, $roleId) {
        $request->validate(['permission' => 'required|exists:permissions,name']);
        $role = Role::findOrFail($roleId);
        $role->givePermissionTo($request->permission);
        return response()->json(['message' => 'Permission assigned to role']);
    }
}
