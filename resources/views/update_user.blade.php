<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

</head>
<body>
   
   @include('components.user_header')

<section class="form-container">
   <form action="{{ route('profile.update') }}" method="post">
       @csrf
       <h3>Cập nhật thông tin</h3>

       <input type="text" name="name" required placeholder="Nhập tên người dùng" maxlength="20" class="box" value="{{ old('name', $user->name) }}">
       @error('name')
           <p>{{ $message }}</p>
       @enderror

       <input type="email" name="email" required placeholder="Nhập email" maxlength="50" class="box" value="{{ old('email', $user->email) }}">
       @error('email')
           <p>{{ $message }}</p>
       @enderror

       <input type="password" name="old_pass" placeholder="Nhập mật khẩu cũ" maxlength="20" class="box">
       @error('old_pass')
           <p>{{ $message }}</p>
       @enderror

       <input type="password" name="new_pass" placeholder="Nhập mật khẩu mới" maxlength="20" class="box">
       <input type="password" name="new_pass_confirmation" placeholder="Xác nhận mật khẩu mới" maxlength="20" class="box">
       @error('new_pass')
           <p>{{ $message }}</p>
       @enderror

       <input type="submit" value="Cập nhật" class="btn">
       
       @if(session('message'))
           <p>{{ session('message') }}</p>
       @endif
   </form>
</section>

@include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>

</body>
</html>