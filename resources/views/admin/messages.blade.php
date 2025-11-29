<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Messages</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="stylesheet" href="{{ asset('assets') }}/css/admin/admin_style.css">

</head>
<body>

@include('components.admin_header')
<section class="contacts">
    <h1 class="heading">Tin nhắn</h1>

    <!-- Tìm kiếm theo tên người dùng -->
    <form method="GET" action="{{ route('admin.search') }}" class="search-form">
        <input type="text" name="search" placeholder="Tìm kiếm tên người dùng" value="{{ request()->input('search') }}">
        <button type="submit" class="search-btn">Tìm kiếm</button>
    </form>

    <!-- Sắp xếp theo thời gian -->
    <form method="GET" action="{{ route('admin.sort') }}" class="sort-form">
        <select name="sort_by" onchange="this.form.submit()">
            <option value="desc" {{ request()->input('sort_by') == 'desc' ? 'selected' : '' }}>Mới nhất</option>
            <option value="asc" {{ request()->input('sort_by') == 'asc' ? 'selected' : '' }}>Cũ nhất</option>
        </select>
    </form>

    <div class="box-container">
        @if($messages->count() > 0)
            <table border="1" cellspacing="0" cellpadding="10" class="message-table">
                <thead>
                    <tr>
                        <th>ID người dùng</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Nội dung tin nhắn</th>
                        <th>Thời gian</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($messages as $message)
                        <tr>
                            <td>{{ $message->user_id }}</td>
                            <td>{{ $message->name }}</td>
                            <td>{{ $message->email }}</td>
                            <td>{{ $message->number }}</td>
                            <td>{{ $message->message }}</td>
                            <td>{{ $message->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <a href="{{ route('admin.messages.delete', $message->id) }}" onclick="return confirm('Xóa tin nhắn này?');" class="delete-btn">Xóa</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p class="empty">Bạn không có tin nhắn nào</p>
        @endif
    </div>
</section>


<script src="{{ asset('assets') }}/js/admin_script.js"></script>
   
</body>
</html>