<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Profile</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/admin_style.css">

</head>
<body>

   @include('components.admin_header')

<section class="form-container">

   <form action="{{ route('admin.profile_update') }}" method="post">
       @csrf
       <h3>Cập nhật hồ sơ</h3>
       <input type="hidden" name="prev_pass" value="{{ $admin->password }}">
       <input type="text" name="name" value="{{ $admin->name }}" required placeholder="Nhập tên người dùng" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
       <input type="password" name="old_pass" placeholder="Nhập mật khẩu cũ" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
       <input type="password" name="new_pass" placeholder="Nhập mật khẩu mới" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
       <input type="password" name="new_pass_confirmation" placeholder="Nhập lại mật khẩu mới" maxlength="20" class="box" oninput="this.value = this.value.replace(/\s/g, '')">
       <input type="submit" value="Cập nhật" class="btn" name="submit">
   </form>

   @if ($errors->any())
       <div class="alert alert-danger">
           <ul>
               @foreach ($errors->all() as $error)
                   <li>{{ $error }}</li>
               @endforeach
           </ul>
       </div>
   @endif

   @if (session('message'))
       <div class="alert alert-success">
           {{ session('message') }}
       </div>
   @endif

</section>


<script src="{{ asset('assets') }}/js/admin_script.js"></script>
   
</body>
</html>