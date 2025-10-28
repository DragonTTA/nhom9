<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email_username' => ['required'],
                'password' => ['required'],
            ]);

            $auth = [
                'email' => $credentials['email_username'],
                'password' => $credentials['password'],
            ];
            if (Auth::attempt($auth)) {
                if(Auth::user()->status != 1){
                    Auth::logout();
                    toast("Tài khoản của bạn đang bị khóa hoặc chưa được kích hoạt!", 'warning');
                    return redirect()->route('login.view');
                }
                $request->session()->regenerate();

                return redirect()->route('dashboard');
            }

            // Nếu sai thông tin
            return back()->withErrors([
                'email_username' => 'Email hoặc mật khẩu không chính xác.',
            ])->onlyInput('email_username');

        } catch (\Exception $exception) {
            \Log::error('Login error: '.$exception->getMessage());
            return back()->withErrors([
                'email_username' => 'Đã xảy ra lỗi, vui lòng thử lại.',
            ])->onlyInput('email_username');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'role_id' => 'required'
        ]);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 0,
        ]);
        $user->assignRole(Role::find($request->role_id)->name);
//        Auth::login($user);
        toast('Hãy liên hệ với quản trị viên để duyệt Account!', 'success');
        return redirect('/');
        //        return route('dashboard');
    }
}
