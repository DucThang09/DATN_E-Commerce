<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPassword;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\PasswordResetToken; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Mail\VerifyAccount;
use Illuminate\Support\Facades\Mail;
use App\Models\AdminNotification;
use Carbon\Carbon;


class RegisterController extends Controller
{
    public function create()
    {
        return redirect()->route('login', ['mode' => 'register']);
    }


    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:20',
                'regex:/^[\pL\s]+$/u'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:50',
                'regex:/^[a-zA-Z0-9._%+-]+@((gmail\.com)|(yahoo\.com)|(outlook\.com))$/'
            ],
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if (User::where('email', $request->email)->exists()) {
            return redirect()->back()
                ->withErrors(['email' => 'Email này đã được đăng ký.'])
                ->withInput();
        }

        try {
            $data = $request->only('name', 'email');
            $data['password'] = bcrypt($request->password);
            $acc = User::create($data);

            // Gửi email xác thực
            Mail::to($acc->email)->send(new VerifyAccount($acc));

            // 👉 TẠO THÔNG BÁO CHO ADMIN (sẽ được JS bên admin đọc)
            AdminNotification::create([
                'title'   => 'Khách hàng mới đăng ký',
                'message' => 'Tài khoản ' . $acc->name . ' (' . $acc->email . ') vừa đăng ký.',
                'type'    => 'user_registered',
                'data'    => ['user_id' => $acc->id], // nếu cột data là JSON
            ]);

            return redirect()
                ->route('login')
                ->with('success', 'Đăng ký thành công, vui lòng kiểm tra email để xác nhận.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('no', 'Đã xảy ra lỗi trong quá trình đăng ký: ' . $e->getMessage());
        }
    }


    public function verify($email)
    {
        // Tìm user theo email trong link
        $user = User::where('email', $email)->firstOrFail();

        // Nếu đã xác thực rồi thì khỏi tạo thông báo thêm
        if ($user->email_verified_at) {
            return redirect()
                ->route('login')
                ->with('success', 'Tài khoản của bạn đã được xác thực trước đó, hãy đăng nhập.');
        }

        // Đánh dấu đã xác thực
        $user->email_verified_at = Carbon::now();
        // Nếu bạn có cột status = 'inactive' trước đó thì bật nó lên
        if (isset($user->status)) {
            $user->status = 'active';
        }
        $user->save();

        // ⭐ LÚC NÀY MỚI TẠO THÔNG BÁO CHO ADMIN
        AdminNotification::create([
            'title'   => 'Tài khoản mới đã xác thực',
            'message' => 'Khách hàng ' . $user->name . ' (' . $user->email . ') vừa xác thực tài khoản thành công.',
            'type'    => 'user_verified',              // kiểu thông báo, tùy bạn đặt
            'data'    => ['user_id' => $user->id],     // data thêm (JSON trong cột data)
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Xác thực thành công, bây giờ bạn có thể đăng nhập.');
    }

    public function forgot_password() {
        return view('forgot_password');
    }

    public function check_forgot_password(Request $request)
        {
            $request->validate([
                'email' => 'required|email|max:50',
            ]);

            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return back()->with('error', 'Email không tồn tại.');
            }

            // throttle 1 phút
            $existing = PasswordResetToken::where('email', $request->email)->first();
            if ($existing && $existing->created_at && $existing->created_at->diffInMinutes(now()) < 1) {
                return back()->with('error', 'Bạn vừa yêu cầu đổi mật khẩu. Hãy thử lại sau ít nhất 1 phút.');
            }

            $token = Str::random(64); // 64 ký tự cho “dài hơi” hơn

            // cập nhật nếu đã có dòng cùng email, ngăn duplicate key
            PasswordResetToken::updateOrCreate(
                ['email' => $request->email],
                ['token' => $token, 'created_at' => now()]
            );

            Mail::to($request->email)->send(new ForgotPassword($user, $token));

            return back()->with('success', 'Gửi thành công, hãy kiểm tra email và đổi mật khẩu trong 1 phút.');
        }
    

    public function reset_password($token) {
        $tokenData = PasswordResetToken::where('token', $token)->firstOrFail();
    
        if ($tokenData->created_at->diffInMinutes(now()) > 1) {
            PasswordResetToken::where('token', $token)->delete();
            
            abort(404, 'Token đã hết hạn.');
        }
    
        $user = User::where('email', $tokenData->email)->firstOrFail();
    
        return view('reset_password');
    }
    
    public function check_reset_password($token)
    {
        request()->validate([
            'password' => 'required|string|min:8|confirmed', 
        ], [
            'password.min' => 'Mật khẩu phải từ 8 ký tự.',  
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.', 
        ]);

        $tokenData = PasswordResetToken::where('token', $token)->firstOrFail();
        $user = User::where('email', $tokenData->email)->firstOrFail();
        $data = [
            'password' => bcrypt(request('password'))  
        ];
        $check = $user->update($data); 

        if ($check) {
            PasswordResetToken::where('token', $token)->delete();
            return redirect()->route('login')->with('success', 'Đổi mật khẩu thành công');
        }
        return redirect()->back()->with('error', 'Lỗi, vui lòng thử lại');
    }

}
