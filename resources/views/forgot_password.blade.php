<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

</head>
<body>
   
   @include('components.user_header')
   

   <section class="form-container">
       <form action="" method="post">
           @csrf
           <h3>Quên mật khẩu</h3>
           <h2>Hãy nhập email của bạn vào bên dưới để bắt đầu quá trình khôi phục mật khẩu.</h2>
           
           <input type="email" name="email" value="{{ old('email') }}" required placeholder="Nhập email của bạn" maxlength="50" class="box" oninput="this.value = this.value.replace(/\s/g, '')">  
           @if ($errors->any())
                <div class="alert alert-danger" style="font-size: 1.5em; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding: 0; list-style: none;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success" style="font-size: 1.5em; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 5px; margin-bottom: 20px;">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" style="font-size: 1.5em; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 5px; margin-bottom: 20px;">
                    {{ session('error') }}
                </div>
            @endif

           <input type="submit" value="Gửi" class="btn" name="submit">
       </form>
       
   </section>
   
   @include('components.footer')

<script src="{{ asset('assets') }}/js/script.js"></script>

</body>
</html>