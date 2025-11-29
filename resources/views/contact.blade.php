<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Contact</title>
   
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

</head>
<body>
   
   @include('components.user_header')

   <section class="contact">
      @if(session('message'))
      <p>{{ session('message') }}</p>
   @endif
      <form action="{{ route('contact.send') }}" method="post">
         @csrf
         <h3>Liên hệ với chúng tôi</h3>
         <input type="text" name="name" placeholder="Nhập tên của bạn" required maxlength="20" class="box" value="{{ old('name') }}">
         @error('name')
            <div class="text-danger">{{ $message }}</div>
         @enderror

         <input type="email" name="email" placeholder="Nhập email" required maxlength="50" class="box" value="{{ old('email') }}">
         @error('email')
            <div class="text-danger">{{ $message }}</div>
         @enderror

         <input type="number" name="number" min="0" max="9999999999" placeholder="Nhập số điện thoại" required onkeypress="if(this.value.length == 10) return false;" class="box" value="{{ old('number') }}">
         @error('number')
            <div class="text-danger">{{ $message }}</div>
         @enderror

         <textarea name="msg" class="box" placeholder="Nhập tin nhắn của bạn" cols="30" rows="10">{{ old('msg') }}</textarea>
         @error('msg')
            <div class="text-danger">{{ $message }}</div>
         @enderror

         <input type="submit" value="Gửi tin nhắn" name="send" class="btn">
      </form>

   </section>

   @include('components.footer')


<script src="{{ asset('assets') }}/js/script.js"></script>

</body>
</html>