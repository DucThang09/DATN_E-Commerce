<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

    {{-- CSS riêng cho trang quên mật khẩu --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/forgot_password.css">
</head>

<body>
   

    <main class="fp-page">
        <section class="fp-card">
            <div class="fp-head">
                <div class="fp-icon">
                    <i class="fa-solid fa-lock"></i>
                </div>

                <h1 class="fp-title">Quên mật khẩu?</h1>
                <p class="fp-sub">
                    Đừng lo lắng, chúng tôi sẽ giúp bạn lấy lại
                </p>
            </div>

            <div class="fp-body">
                <form action="" method="post" class="fp-form">
                    @csrf

                    <label class="fp-label" for="fpEmail">Email đăng ký</label>

                    <div class="fp-input">
                        <i class="fa-regular fa-envelope"></i>
                        <input id="fpEmail" type="email" name="email" value="{{ old('email') }}" required
                            placeholder="Nhập email của bạn" maxlength="50"
                            oninput="this.value = this.value.replace(/\s/g, '')">
                    </div>

                    <p class="fp-hint">
                        Nhập email bạn đã đăng ký, chúng tôi sẽ gửi link đặt lại mật khẩu
                    </p>

                    @if ($errors->any())
                        <div class="alert alert-danger"
                            style="font-size: 1.5em; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
                            <ul style="margin: 0; padding: 0; list-style: none;">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success"
                            style="font-size: 1.5em; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 5px; margin-bottom: 20px;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger"
                            style="font-size: 1.5em; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
                            {{ session('error') }}
                        </div>
                    @endif

                    <button type="submit" class="fp-btn">
                        <i class="fa-solid fa-paper-plane"></i>
                        Gửi link đặt lại mật khẩu
                    </button>

                    <div class="fp-divider"></div>

                    <a href="{{ url()->previous() }}" class="fp-back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Quay lại đăng nhập
                    </a>
                </form>
            </div>
        </section>
    </main>
    <script src="{{ asset('assets') }}/js/script.js"></script>
</body>

</html>
