<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        // Kiểm tra xem người dùng đã đăng nhập
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        $messages = Message::all();
        return view('admin.messages', compact('messages'));
    }

    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $message->delete();

        return redirect()->route('admin.messages')->with('message', 'Message deleted successfully!');
    }

    public function message_sort(Request $request)
    {
        $query = Message::query();

        // Sắp xếp theo thời gian
        if ($request->has('sort_by')) {
            $sortBy = $request->input('sort_by');
            $query->orderBy('created_at', $sortBy);
        } else {
            // Mặc định là sắp xếp mới nhất
            $query->orderBy('created_at', 'desc');
        }

        $messages = $query->get();

        return view('admin.messages', compact('messages'));
    }
    public function message_search(Request $request)
    {
        $query = Message::query();

        // Tìm kiếm theo tên
        if ($request->has('search') && $request->input('search') != '') {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }
        $messages = $query->get();

        return view('admin.messages', compact('messages'));
    }
    

}
