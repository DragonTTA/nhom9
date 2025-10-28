<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $maxAttempts = config('auth.login.max_attempts', 5);
        $lockMinutes = config('auth.login.lock_minutes', 30);

        try {
            $credentials = $request->validate([
                'email_username' => ['required'],
                'password' => ['required'],
            ]);
            $user = \App\Models\User::where('email', $credentials['email_username'])
                ->first();

            if (!$user) {
                toast("Tài khoản không tồn tại.", 'error');
                return back();
            }
            if ($user->locked_until && now()->lessThan($user->locked_until)) {
                $diff = now()->diff($user->locked_until);
                $minutes = $diff->i;
                $seconds = $diff->s;
                $remaining = '';
                if ($minutes > 0) {
                    $remaining .= $minutes . ' phút ';
                }
                if ($seconds > 0) {
                    $remaining .= $seconds . ' giây';
                }
                toast("Tài khoản bị khóa. Vui lòng thử lại sau $remaining.", 'error');
                return back();
            }

            $auth = ['email' => $user->email, 'password' => $credentials['password']];
            if (Auth::attempt($auth)) {
                $user->update([
                    'login_attempts' => 0,
                    'locked_until' => null,
                ]);
                if ($user->status != 1) {
                    Auth::logout();
                    toast("Tài khoản của bạn đang bị khóa hoặc chưa được kích hoạt!", 'warning');
                    return redirect()->route('login.view');
                }

                $request->session()->regenerate();
                return redirect()->route('dashboard');
            }

            $user->increment('login_attempts');

            if ($user->login_attempts >= $maxAttempts) {
                $user->update([
                    'locked_until' => now()->addMinutes($lockMinutes),
                ]);
                toast("Tài khoản bị khóa 30 phút vì nhập sai quá nhiều lần!", 'error');
            } else {
                $remaining = 5 - $user->login_attempts;
                toast("Sai mật khẩu! Còn {$remaining} lần thử.", 'error');
            }
            return back()->withErrors([
                'email_username' => 'Email hoặc mật khẩu không chính xác.',
            ])->onlyInput('email_username');

        } catch (\Exception $exception) {
            Log::error('Login error: '.$exception->getMessage());
            toast("Đã xảy ra lỗi hệ thống, vui lòng thử lại.", 'error');
            return back();
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
