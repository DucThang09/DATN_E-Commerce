<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    {{-- CSS giao diện login --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/admin_login.css">
</head>
<body>

    {{-- Hiển thị lỗi --}}
    @if ($errors->any())
        <div class="message">
            @foreach ($errors->all() as $error)
                <span>{{ $error }}</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            @endforeach
        </div>
    @endif

    <div class="login-page">
        <div class="login-wrapper">
            {{-- Logo + tên app --}}
            <div class="text-center">
                <div class="login-logo-icon">▲</div> {{-- thay bằng logo thật nếu có --}}
            </div>

            {{-- Tiêu đề hợp lý cho admin --}}
            <div class="login-title">Đăng nhập quản trị</div>

            <form action="{{ route('admin.login_post') }}" method="post">
                @csrf

                <div class="form-group">
                    <label for="email">Email đăng nhập</label>
                    <input type="email"
                        id="email"
                        name="email"
                        required
                        maxlength="100"
                        placeholder="Nhập email đăng nhập"
                        class="login-input">
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password"
                        id="password"
                        name="password"
                        required
                        maxlength="20"
                        placeholder="Nhập mật khẩu"
                        class="login-input">
                </div>


                <div class="form-remember">
                    <label class="remember-label">
                        <input type="checkbox" name="remember" class="remember-checkbox">
                        Remember me
                    </label>
                </div>

                <button type="submit" class="btn-login">
                    Đăng Nhập
                </button>

                <div class="login-footer">
                    <div>
                        <i class="fa-solid fa-lock"></i>
                        <a href="#">Quên mật khẩu?</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
