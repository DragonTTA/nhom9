<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userService;
    public function __construct(UserService $userService){
        $this->userService = $userService;
    }
    public function index(Request $request)
    {
        $params = $request->all();
        if(!empty($params['status']) && $params['status'] == "-1"){
            unset($params['status']);
        }
        $users = $this->userService->getListByFilter($params,['roles']);
        $roles = Role::pluck('name', 'id');
        return view('admin.users', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role_id' => 'required'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'status' => 0,
            'password' => bcrypt($request->password)
        ]);

        $user->assignRole(Role::find($request->role_id)->name);
        toast('Create User Success!', 'success');

        return response()->json(['status' => true, 'message' => 'User created successfully!']);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role_id' => 'required'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => bcrypt($request->password)]);
        }
        $role = Role::find($request->role_id);
        $user->syncRoles([$role]);
        toast('User updated successfully!', 'success');
        return response()->json(['status' => true, 'message' => 'User updated successfully!']);
    }
    public function toggleStatus($id, Request $request)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found']);
        }
        $user->status = $request->status;
        $user->save();
        return response()->json([
            'status' => true,
            'message' => $user->status ? 'User activated successfully.' : 'User deactivated successfully.'
        ]);
    }

}
