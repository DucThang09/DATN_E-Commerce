<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
class ContactController extends Controller
{
    public function showForm()
    {
        return view('contact');
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'name' => 'required|max:20',
            'email' => 'required|email|max:50',
            'number' => 'required|numeric|digits_between:1,10',
            'msg' => 'required|string',
        ]);

        // Kiểm tra nếu tin nhắn đã tồn tại
        $existingMessage = Message::where('name', $request->name)
                                  ->where('email', $request->email)
                                  ->where('number', $request->number)
                                  ->where('message', $request->msg)
                                  ->first();

        if ($existingMessage) {
            return back()->with('message', 'Already sent message!');
        } else {
            // Lưu tin nhắn mới vào cơ sở dữ liệu
            Message::create([
                'user_id' => Auth::id() ?? null, // Nếu có user đã đăng nhập
                'name' => $request->name,
                'email' => $request->email,
                'number' => $request->number,
                'message' => $request->msg,
            ]);

            return back()->with('message', 'Sent message successfully!');
        }
    }
}
