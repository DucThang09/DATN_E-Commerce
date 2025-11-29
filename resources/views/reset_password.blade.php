<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cập nhật mật khẩu</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

</head>
<body>
   
   @include('components.user_header')
   @if(session('success'))
    <div class="alert alert-success" style="font-size: 1.5em; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 5px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger" style="font-size: 1.5em; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
            {{ session('error') }}
        </div>
    @endif
   <section class="form-container">
    <form action="" method="post">
        @csrf
        <h3>Đặt lại mật khẩu</h3>

        <input type="password" name="password" required placeholder="Mật khẩu mới " maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        @error('password')
            <div class="error" style="color: red; font-size: 15px; margin-top: 5px;">
                {{ $message }} <!-- Hiển thị lỗi cho password -->
            </div>
        @enderror
        <!-- Xác nhận mật khẩu -->
        <input type="password" name="password_confirmation" required placeholder="Nhập lại mật khẩu mới" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
        <input type="submit" value="Cập nhật" class="btn">
    </form>
 </section>
 
   
   @include('components.footer')


<script src="{{ asset('assets') }}/js/script.js"></script>

</body>
</html>