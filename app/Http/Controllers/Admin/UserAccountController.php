<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class UserAccountController extends Controller
{
    // ================== DANH SÁCH NGƯỜI DÙNG ==================
    public function index(Request $request)
    {
        // Kiểm tra admin đã đăng nhập
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $query = User::query();

        // ====== TÌM KIẾM ======
        if ($search = $request->get('search_query')) {
            $type = $request->get('search_type', 'name');

            switch ($type) {
                case 'email':
                    $query->where('email', 'like', "%{$search}%");
                    break;

                case 'id':
                    $query->where('id', $search); // tìm chính xác ID
                    break;

                default: // name
                    $query->where('name', 'like', "%{$search}%");
                    break;
            }
        }

        // ====== LỌC TRẠNG THÁI ======
        if ($status = $request->get('status')) {
            // ví dụ: active / locked
            $query->where('status', $status);
        }

        // Phân trang + giữ query string
        $accounts = $query
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users_accounts', compact('accounts'));
    }

    // ================== THÊM NGƯỜI DÙNG MỚI ==================
    public function store(Request $request)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Validate dữ liệu
        $request->validate([
            'name'                  => 'required|string|max:100',
            'email'                 => 'required|email|max:100|unique:users,email',
            'password'              => 'required|min:6|confirmed',
            'status'                => 'nullable|in:active,locked',
        ], [
            'name.required'         => 'Vui lòng nhập tên người dùng.',
            'email.required'        => 'Vui lòng nhập email.',
            'email.email'           => 'Email không hợp lệ.',
            'email.unique'          => 'Email này đã tồn tại.',
            'password.required'     => 'Vui lòng nhập mật khẩu.',
            'password.min'          => 'Mật khẩu phải có ít nhất :min ký tự.',
            'password.confirmed'    => 'Mật khẩu nhập lại không khớp.',
        ]);

        // Tạo user mới (mặc định coi là khách hàng)
        $user = new User();
        $user->name     = $request->name;
        $user->email    = $request->email;
        $user->password = bcrypt($request->password);      // hash mật khẩu
        $user->status   = $request->status ?? 'active';    // active / locked

        $user->save();

        return redirect()
            ->route('admin.users_accounts')
            ->with('success', 'Thêm người dùng thành công!');
    }

    // ================== CẬP NHẬT NGƯỜI DÙNG ==================
    public function update(Request $request, $id)
    {
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $user = User::findOrFail($id);

        $request->validate([
            'name'   => 'required|string|max:100',
            'email'  => 'required|email|max:100|unique:users,email,' . $user->id,
            'status' => 'nullable|in:active,locked',
        ], [
            'name.required'  => 'Vui lòng nhập tên người dùng.',
            'email.required' => 'Vui lòng nhập email.',
            'email.email'    => 'Email không hợp lệ.',
            'email.unique'   => 'Email này đã tồn tại.',
        ]);

        $user->name   = $request->name;
        $user->email  = $request->email;
        $user->status = $request->status ?? $user->status;

        $user->save();

        return redirect()
            ->route('admin.users_accounts')
            ->with('success', 'Cập nhật người dùng thành công!');
    }

    // ================== XÓA TÀI KHOẢN NGƯỜI DÙNG ==================
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Xóa thông tin liên quan (nếu có định nghĩa quan hệ)
        $user->orders()->delete();
        $user->messages()->delete();
        $user->cart()->delete();
        $user->wishlist()->delete();

        $user->delete();

        return redirect()
            ->route('admin.users_accounts')
            ->with('success', 'Xóa tài khoản người dùng thành công!');
    }

    // ================== TÌM KIẾM RIÊNG (KHÔNG BẮT BUỘC) ==================
    public function search(Request $request)
    {
        $searchType  = $request->input('search_type');   // id / name
        $searchQuery = $request->input('search_query');  // từ khóa

        $query = User::query();

        if ($searchType === 'id' && is_numeric($searchQuery)) {
            $query->where('id', $searchQuery);
        } elseif ($searchType === 'name') {
            $query->where('name', 'like', "%{$searchQuery}%");
        }

        $accounts = $query->get();

        return view('admin.users_accounts', compact('accounts'));
    }
}
