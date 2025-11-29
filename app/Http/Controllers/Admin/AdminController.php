<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.admin_login');
    }
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        // Tìm admin theo email trước để báo lỗi rõ ràng hơn
        $admin = \App\Models\Admin::where('email', $request->email)->first();

        if (!$admin) {
            return back()
                ->withErrors(['email' => 'Email không tồn tại trong hệ thống.'])
                ->onlyInput('email');
        }

        // Nếu có cột status: chặn tài khoản bị khóa
        if ($admin->status === 'locked') {
            return back()
                ->withErrors(['email' => 'Tài khoản này đang bị tạm khóa, vui lòng liên hệ quản trị viên.'])
                ->onlyInput('email');
        }

        // Thử đăng nhập qua guard admin
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
            'status'   => 'active', // chỉ cho admin đang active login
        ];

        if (Auth::guard('admin')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()
            ->withErrors(['email' => 'Email hoặc mật khẩu không đúng.'])
            ->onlyInput('email');
    }

    public function signOut(Request $request)
    {
        Auth::guard('admin')->logout();

        // Xoá toàn bộ session cũ + tạo CSRF token mới
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
