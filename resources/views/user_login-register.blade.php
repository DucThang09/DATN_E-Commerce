<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login / Register</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/login.css">
   {{-- Nếu bạn có file css riêng cho form 2 panel thì link thêm ở đây --}}
   <link rel="stylesheet" href="{{ asset('assets') }}/css/auth-panel.css">
</head>
<body>
   

   @if(session('success'))
      <div class="alert alert-success" style="font-size: 1.5em; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 5px; margin-bottom: 20px;">
         {{ session('success') }}
      </div>
   @endif

   {{-- ===== FORM LOGIN + REGISTER 2 PANEL ===== --}}
   <section class="auth-wrapper">
      <div class="container" id="container">
         
         {{-- ==== PANEL ĐĂNG KÝ ==== --}}
         <div class="form-container sign-up-container">
            <form action="{{ route('register') }}" method="post">
               @csrf
               <h1>Tạo tài khoản</h1>

               <span>Đăng ký bằng email của bạn</span>

               <input type="text" name="name" placeholder="Họ và tên" maxlength="20"
                      value="{{ old('name') }}" />
               @error('name')
                  <div style="color:red; font-size: 14px;">Vui lòng nhập tên hợp lệ (tối đa 20 ký tự).</div>
               @enderror

               <input type="email" name="email" placeholder="Email" maxlength="50"
                      value="{{ old('email') }}" oninput="this.value = this.value.replace(/\s/g, '')">
               @if ($errors->has('email'))
                  <div style="color:red; font-size: 14px;">{{ $errors->first('email') }}</div>
               @endif

               <input type="password" name="password" placeholder="Mật khẩu" maxlength="20"
                      oninput="this.value = this.value.replace(/\s/g, '')">
               @error('password')
                  <div style="color:red; font-size: 14px;">Mật khẩu phải từ 8 ký tự.</div>
               @enderror

               <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu" maxlength="20"
                      oninput="this.value = this.value.replace(/\s/g, '')">

               <button type="submit">Đăng ký</button>
            </form>
         </div>

         {{-- ==== PANEL ĐĂNG NHẬP ==== --}}
         <div class="form-container sign-in-container">
            <form action="{{ route('login.attempt') }}" method="post">
               @csrf
               <h1>Đăng nhập</h1>

               @if ($errors->any())
                  <div class="alert alert-danger" style="font-size: 1.1em; padding: 10px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px; margin-bottom: 15px;">
                     <ul style="margin: 0; padding-left: 18px;">
                        @foreach ($errors->all() as $error)
                           <li>{{ $error }}</li>
                        @endforeach
                     </ul>
                  </div>
               @endif

               <span>Đăng nhập bằng tài khoản của bạn</span>

               <input type="email" name="email" value="{{ old('email') }}"
                      required placeholder="Nhập email của bạn" maxlength="50"
                      oninput="this.value = this.value.replace(/\s/g, '')">
               <input type="password" name="password" required
                      placeholder="Nhập mật khẩu" maxlength="20"
                      oninput="this.value = this.value.replace(/\s/g, '')">

               <a href="{{ route('forgot') }}">Quên mật khẩu?</a>
               <button type="submit">Đăng nhập</button>
            </form>
         </div>

         {{-- ==== OVERLAY ==== --}}
         <div class="overlay-container">
            <div class="overlay">
               <div class="overlay-panel overlay-left">
                  <h1>Chào mừng trở lại!</h1>
                  <p>Để tiếp tục, hãy đăng nhập bằng thông tin của bạn.</p>
                  <button class="ghost" id="signIn">Đăng nhập</button>
               </div>
               <div class="overlay-panel overlay-right">
                  <h1>Xin chào, bạn mới!</h1>
                  <p>Nhập thông tin cá nhân và bắt đầu hành trình cùng chúng tôi.</p>
                  <button class="ghost" id="signUp">Đăng ký</button>
               </div>
            </div>
         </div>
      </div>
   </section>

   <script src="{{ asset('assets') }}/js/script.js"></script>

   {{-- JS chuyển panel --}}
   <script>
    const signUpButton = document.getElementById('signUp');
    const signInButton = document.getElementById('signIn');
    const container = document.getElementById('container');

    if (signUpButton && signInButton && container) {
        signUpButton.addEventListener('click', () => {
            container.classList.add("right-panel-active");
        });

        signInButton.addEventListener('click', () => {
            container.classList.remove("right-panel-active");
        });
    }

    // Nếu URL có ?mode=register thì mở sẵn panel Đăng ký
    const params = new URLSearchParams(window.location.search);
    if (params.get('mode') === 'register') {
        container.classList.add('right-panel-active');
    }
</script>


</body>
</html>
