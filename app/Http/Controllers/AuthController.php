<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate input
            $credentials = $request->validate([
                'email_username' => ['required'],
                'password' => ['required'],
            ]);

            // Chuẩn hóa dữ liệu cho Auth
            $auth = [
                'email' => $credentials['email_username'], // nếu login bằng email
                'password' => $credentials['password'],
            ];
            // Thử đăng nhập
            if (Auth::attempt($auth)) {
                $request->session()->regenerate();

                return redirect()->route('dashboard');
            }

            // Nếu sai thông tin
            return back()->withErrors([
                'email_username' => 'Email hoặc mật khẩu không chính xác.',
            ])->onlyInput('email_username');

        } catch (\Exception $exception) {
            // Nếu muốn, bạn có thể log lỗi
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
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'status' => 0,

        ]);
        Auth::login($user);
        return route('dashboard');
    }
}
