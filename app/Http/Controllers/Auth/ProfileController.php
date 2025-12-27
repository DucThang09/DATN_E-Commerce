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
        /** @var User $user */
        $user = Auth::user();
        $section = $request->input('section', 'personal');

        if ($section === 'personal') {
            $request->validate([
                'name'  => 'required|string|max:50',
                'email' => 'required|email|max:80|unique:users,email,' . $user->id,
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->save();
            return back()->with('message', 'Cập nhật thông tin cá nhân thành công!');
        }

        if ($section === 'security') {
            $request->validate([
                'old_pass' => 'required',
                'new_pass' => 'required|confirmed|min:8|max:50',
            ]);

            if (!Hash::check($request->old_pass, $user->password)) {
                return back()->withErrors(['old_pass' => 'Mật khẩu cũ không đúng'])
                    ->withInput(['section' => 'security']);
            }

            $user->password = Hash::make($request->new_pass);
            $user->save();

            return back()->with('message', 'Cập nhật mật khẩu thành công!');
            
        }

        return back()->with('error', 'Yêu cầu không hợp lệ.');
    }
}
