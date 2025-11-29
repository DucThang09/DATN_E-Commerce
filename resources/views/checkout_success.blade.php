<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Đặt hàng thành công</title>
   <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <style>
       .body {
           display: flex;
           align-items: center;
           justify-content: center;
           height: 100vh;
           margin: 0;
           font-family: Arial, sans-serif;
           background-color: #f4f8fb;
           color: #333;
       }

       .success-container {
           text-align: center;
           background-color: #fff;
           padding: 40px; /* Tăng padding */
           border-radius: 10px;
           box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
           max-width: 700px; /* Tăng kích thước tối đa */
           width: 90%;
       }

       .success-container h1 {
           color: #4caf50;
           font-size: 28px; /* Tăng kích thước chữ */
           margin-bottom: 15px;
       }

       .success-container p {
           font-size: 18px; /* Tăng kích thước chữ */
           margin-bottom: 25px;
           color: #666;
       }

       .success-container a {
           display: inline-block;
           padding: 12px 25px; /* Tăng kích thước nút */
           color: #fff;
           background-color: #4caf50;
           text-decoration: none;
           border-radius: 5px;
           transition: background-color 0.3s;
           font-weight: bold;
           font-size: 16px; /* Tăng kích thước chữ của nút */
       }

       .success-container a:hover {
           background-color: #43a047;
       }
   </style>

</head>
<body>
    @include('components.user_header')

    <div class="body">
        <div class="success-container">
        <h1>Đơn hàng của bạn đã được đặt thành công!</h1>
        <p>Cảm ơn bạn đã mua sắm với chúng tôi. Đơn hàng sẽ được xử lý sớm nhất có thể.</p>
        <a href="{{ route('home') }}">Quay lại trang chủ</a>
        </div>
    </div>

   @include('components.footer')
</body>
<script src="{{ asset('assets') }}/js/script.js"></script>
</html>
