<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function show()
    {
        return view('admin.login');
    }

    public function login(Request $r)
    {
        $data = $r->validate(['email' => 'required|email', 'password' => 'required|string']);
        $key = 'login:'.strtolower($data['email']).'|'.$r->ip();
        $ip = 'login-ip:'.$r->ip();
        if (RateLimiter::tooManyAttempts($key, 5) || RateLimiter::tooManyAttempts($ip, 20)) {
            throw ValidationException::withMessages(['email' => 'محاولات كثيرة. حاول بعد دقيقة.']);
        }RateLimiter::hit($key, 60);
        RateLimiter::hit($ip, 60);
        if (! Auth::attempt([...$data, 'is_admin' => true])) {
            throw ValidationException::withMessages(['email' => 'بيانات الدخول غير صحيحة.']);
        }RateLimiter::clear($key);
        $r->session()->regenerate();

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $r)
    {
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();

        return redirect()->route('login');
    }
}
