<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminAccountController extends Controller
{
    public function index()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Chỉ lấy admin thường, ẩn mọi tài khoản super_admin
        $accounts = Admin::where('role', '!=', 'super_admin')->get();

        return view('admin.admin_accounts', compact('accounts'));
    }


    public function destroy($id)
    {
        $current = Auth::guard('admin')->user();
        $admin   = Admin::findOrFail($id);

        // Không cho tự xoá chính mình
        if ($current && $current->id === $admin->id) {
            return redirect()
                ->route('admin.accounts')
                ->with('error', 'Bạn không thể tự xóa tài khoản của chính mình!');
        }

        // Không cho xoá super_admin (đặc biệt là tài khoản quản lý hệ thống)
        if ($admin->role === 'super_admin') {
            return redirect()
                ->route('admin.accounts')
                ->with('error', 'Không thể xóa tài khoản super admin!');
        }

        $admin->delete();

        return redirect()
            ->route('admin.accounts')
            ->with('success', 'Xóa tài khoản quản trị viên thành công!');
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:20|unique:admins,name',
            'pass' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $admin = new Admin();
        $admin->name     = $request->name;
        $admin->password = Hash::make($request->pass);
        $admin->status   = 'active';      // mặc định hoạt động
        $admin->role     = 'admin';       // tài khoản mới là admin thường
        $admin->save();

        return redirect()
            ->route('admin.accounts')
            ->with('success', 'Đăng ký quản trị viên mới thành công!');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name'   => 'required|string|max:100',
            'pass'   => 'required|string|min:6|confirmed', // pass + pass_confirmation
            'status' => 'nullable|in:active,locked',
        ], [
            'name.required'  => 'Vui lòng nhập tên quản trị viên.',
            'pass.required'  => 'Vui lòng nhập mật khẩu.',
            'pass.min'       => 'Mật khẩu phải có ít nhất :min ký tự.',
            'pass.confirmed' => 'Mật khẩu nhập lại không khớp.',
        ]);

        Admin::create([
            'name'     => $request->name,
            'password' => Hash::make($request->pass),
            'status'   => $request->status ?? 'active',
            'role'     => 'admin', // mọi admin tạo qua UI đều là admin thường
        ]);

        return redirect()
            ->route('admin.accounts')
            ->with('success', 'Thêm tài khoản quản trị viên thành công!');
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:100',
            'status' => 'nullable|in:active,locked',
        ], [
            'name.required' => 'Vui lòng nhập tên quản trị viên.',
            'status.in'     => 'Trạng thái không hợp lệ.',
        ]);

        $admin->name   = $request->name;
        $admin->status = $request->status ?? $admin->status;

        $admin->save();

        return redirect()
            ->route('admin.accounts')
            ->with('form', 'edit')
            ->with('success', 'Cập nhật tài khoản quản trị viên thành công!');
    }
}
