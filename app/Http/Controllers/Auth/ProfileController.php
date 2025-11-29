<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ProfileController extends Controller
{
    public function showUpdateForm()
    {
        $user = Auth::user();
        return view('update_user', compact('user'));
    }

    public function updateProfile(Request $request)
{
    $user = Auth::user();
    
    $request->validate([
        'name' => 'required|string|max:20',
        'email' => 'required|email|max:50|unique:users,email,' . $user->id,
        'old_pass' => 'nullable|string|max:20',
        'new_pass' => 'nullable|string|max:20|confirmed',
    ]);
    
    // Tạo mảng để lưu trữ dữ liệu cần cập nhật
    $dataToUpdate = [
        'name' => $request->name,
        'email' => $request->email,
    ];

    // Kiểm tra mật khẩu cũ
    if ($request->filled('old_pass')) {
        if (!Hash::check($request->old_pass, $user->password)) {
            return back()->withErrors(['old_pass' => 'Mật khẩu cũ không đúng']);
        }

        // Cập nhật mật khẩu mới
        if ($request->filled('new_pass')) {
            $dataToUpdate['password'] = Hash::make($request->new_pass);
        }
    }

    // Sử dụng model User để cập nhật dữ liệu
    User::where('id', $user->id)->update($dataToUpdate);

    return back()->with('message', 'Cập nhật thông tin thành công!');
}


}
