<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
class AdminProfileController extends Controller
{
    public function edit()
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $admin = Auth::guard('admin')->user(); // Sử dụng guard cho admin
        return view('admin.update_profile', compact('admin'));
    }

    public function update(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:20',
        'old_pass' => 'nullable|string|max:20',
        'new_pass' => 'nullable|string|max:20|confirmed',
    ]);

    $admin = Auth::guard('admin')->user();
    
    // Kiểm tra mật khẩu cũ
    if ($request->input('old_pass') && !Hash::check($request->input('old_pass'), $admin->password)) {
        return back();
    }
    
    // Cập nhật tên và mật khẩu mới nếu có
    $dataToUpdate = [
        'name' => $request->input('name'),
    ];

    if ($request->filled('new_pass')) {
        $dataToUpdate['password'] = Hash::make($request->input('new_pass'));
    }

    // Sử dụng update() để cập nhật dữ liệu
    Admin::where('id', $admin->id)->update($dataToUpdate);

    return redirect()->route('admin.profile_edit');
}
}
