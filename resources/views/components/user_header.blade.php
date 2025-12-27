<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banana.Com</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- CSS chính của bạn --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/user_header.css">

    {{-- Font Awesome để dùng các icon fas fa-... --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
        integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkTtDVIltj0bI5xZVxYl9FkRuqVIxZC1b8x7V0d2ZJ4xGqk5xI5Dqg3g=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>

    {{-- ========== HEADER ========== --}}
    @if (isset($messages))
        @foreach ($messages as $message)
            <div class="message">
                <span>{{ $message }}</span>
                <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>
        @endforeach
    @endif

    @php
        $total_cart_counts = Auth::check() ? \App\Models\Cart::where('user_id', Auth::id())->count() : 0;
    @endphp

    <header class="main-header">
        <div class="main-header__inner">

            {{-- LOGO --}}
            <a href="{{ route('home') }}" class="main-header__logo">
                <span class="logo-text">Banana<span>.Com</span></span>
                <span class="logo-icon">
                    <i class="fas fa-mobile-alt"></i>
                </span>
            </a>

            {{-- MENU TRÁI: Danh mục / Khuyến mãi / Thương hiệu --}}
            <nav class="main-header__nav">
                {{-- Danh mục (có dropdown) --}}
                <div class="nav-category">
                    <button type="button" class="nav-category__btn">
                        Danh mục
                        <i class="fas fa-chevron-down"></i>
                    </button>

                    <div class="nav-category__dropdown">
                        @foreach ($categories as $cat)
                            <a href="{{ route('products.category', $cat->slug) }}">
                                {{ $cat->category_name }}
                            </a>
                        @endforeach
                    </div>

                </div>
            </nav>

            {{-- Ô TÌM KIẾM Ở GIỮA --}}
            <form action="{{ route('search') }}" method="GET" class="main-header__search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="Tìm kiếm điện thoại, laptop, phụ kiện...">
            </form>

            {{-- ICON BÊN PHẢI: Giỏ hàng / Yêu thích / Tài khoản --}}
            <div class="main-header__right">

                {{-- Giỏ hàng --}}
                <a href="{{ route('cart.index') }}" class="icon-btn">
                    <i class="fas fa-shopping-cart"></i>

                    {{-- ✅ Luôn có badge để JS update, chỉ ẩn/hiện bằng style --}}
                    <span class="icon-badge js-cart-count" style="{{ $total_cart_counts > 0 ? '' : 'display:none;' }}">
                        {{ $total_cart_counts }}
                    </span>
                </a>

                {{-- Tài khoản --}}
                <div class="account-dropdown">
                    <button type="button" class="account-btn">
                        <i class="fas fa-user"></i>

                        @if (Auth::check())
                            <span>{{ Auth::user()->name }}</span>
                        @else
                            <span>Tài khoản</span>
                        @endif

                        <i class="fas fa-chevron-down chevron"></i>
                    </button>


                    <div class="account-menu">
                        @if (Auth::check())
                            @php $user = Auth::user(); @endphp

                            <div class="account-menu__info">
                                <span class="account-name">{{ $user->name }}</span>
                            </div>

                            <a href="{{ route('profile.update.form') }}" class="account-menu__link">
                                Cập nhật hồ sơ
                            </a>
                            <a href="{{ route('orders.history') }}" class="account-menu__link">
                                Lịch sử đơn hàng
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="account-menu__link account-menu__logout"
                                    onclick="return confirm('Logout from the website?');">
                                    Đăng xuất
                                </button>
                            </form>
                        @else
                            <p class="account-menu__text">Vui lòng đăng nhập để tiếp tục</p>
                            <a href="{{ route('login') }}" class="account-menu__link">Đăng nhập</a>
                            <a href="{{ route('login', ['mode' => 'register']) }}" class="account-menu__link">
                                Đăng ký
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </header>
    <div id="chatbot" style="position:fixed;right:16px;bottom:16px;width:320px;background:#fff;border:1px solid #ddd;border-radius:12px;overflow:hidden;box-shadow:0 10px 30px rgba(0,0,0,.12);z-index:9999;">
  <div style="padding:10px 12px;font-weight:600;border-bottom:1px solid #eee;">Chat hỗ trợ</div>
  <div id="chatList" style="height:280px;overflow:auto;padding:10px;display:flex;flex-direction:column;gap:8px;"></div>

  <form id="chatForm" style="display:flex;gap:8px;padding:10px;border-top:1px solid #eee;">
    <input id="chatInput" type="text" placeholder="Nhập tin nhắn..." style="flex:1;padding:10px;border:1px solid #ddd;border-radius:10px;">
    <button type="submit" style="padding:10px 12px;border:0;border-radius:10px;background:#111;color:#fff;">Gửi</button>
  </form>
</div>

<script>
(() => {
  const list = document.getElementById('chatList');
  const form = document.getElementById('chatForm');
  const input = document.getElementById('chatInput');
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  function addBubble(text, who) {
    const div = document.createElement('div');
    div.textContent = text;
    div.style.maxWidth = '85%';
    div.style.padding = '10px';
    div.style.borderRadius = '12px';
    div.style.whiteSpace = 'pre-wrap';
    div.style.alignSelf = who === 'me' ? 'flex-end' : 'flex-start';
    div.style.background = who === 'me' ? '#111' : '#f3f4f6';
    div.style.color = who === 'me' ? '#fff' : '#111';
    list.appendChild(div);
    list.scrollTop = list.scrollHeight;
  }

  addBubble('Chào bạn! Mình có thể hỗ trợ gì về sản phẩm/đơn hàng?', 'bot');

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const msg = input.value.trim();
    if (!msg) return;

    addBubble(msg, 'me');
    input.value = '';

    try {
      const r = await fetch('{{ route('ai.chat') }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'X-Requested-With': 'XMLHttpRequest',
        },
        body: JSON.stringify({ message: msg })
      });

      const ct = r.headers.get('content-type') || '';
      let data;

      if (ct.includes('application/json')) {
        data = await r.json();
      } else {
        const t = await r.text();
        console.error('Non-JSON response:', r.status, t.slice(0, 300));
        throw new Error('Server returned non-JSON');
      }

      if (!r.ok || !data.ok) {
        console.error('AI error:', r.status, data);
        throw new Error(data.error || ('HTTP ' + r.status));
      }

      addBubble(data.reply, 'bot');
    } catch (err) {
      console.error(err);
      addBubble('Xin lỗi, hiện chatbot đang lỗi. Vui lòng thử lại sau.', 'bot');
    }
  });
})();
</script>

    <script src="{{ asset('assets') }}/js/guest/user_header.js"></script>
</body>

</html>
