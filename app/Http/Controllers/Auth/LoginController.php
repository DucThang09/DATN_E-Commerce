<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;   // THÊM DÒNG NÀY

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('user_login-register');
    }

    public function login(Request $request)
    {
        // Validate đầu vào
        $request->validate([
            'email'    => 'required|email|max:50',
            'password' => 'required|string',
        ]);

        // ====== CHECK TRƯỚC: TÀI KHOẢN BỊ KHÓA HAY KHÔNG ======
        $user = User::where('email', $request->email)->first();

        if ($user && $user->status === 'locked') {   // giá trị đúng với cột status của bạn
            return redirect()
                ->back()
                ->withErrors([
                    'error' => 'Tài khoản của bạn đang tạm khóa, vui lòng liên hệ quản trị viên.',
                ])
                ->withInput();
        }

        // ====== THỬ ĐĂNG NHẬP CHỈ VỚI TÀI KHOẢN ACTIVE ======
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
            'status'   => 'active',   // chỉ cho login nếu status = active
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Kiểm tra email đã được xác thực
            if (is_null($user->email_verified_at)) {
                Auth::logout(); // Đăng xuất
                return redirect()
                    ->back()
                    ->withErrors(['email_not_verified' => 'Tài khoản chưa xác thực']);
            }

            // Đăng nhập thành công
            return redirect()
                ->route('home')
                ->with('success', 'Đăng nhập thành công!');
        }

        // Thông báo lỗi đăng nhập
        return redirect()
            ->back()
            ->withErrors(['error' => 'Email hoặc mật khẩu không chính xác.'])
            ->withInput();
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_seen_at = null;
            $user->save();
        }

        // Đăng xuất đúng guard user
        Auth::guard('web')->logout();

        // KHÔNG invalidate toàn bộ session nữa
        // $request->session()->invalidate();

        // Chỉ cần đổi lại CSRF token để tránh CSRF
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
