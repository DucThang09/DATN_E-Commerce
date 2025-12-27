<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lại mật khẩu</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

    {{-- CSS riêng --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/reset_password.css">
</head>

<body>
    <main class="rp-page">
        <section class="rp-card">
            {{-- HEAD --}}
            <div class="rp-head">
                <div class="rp-icon">
                    <i class="fa-solid fa-key"></i>
                </div>
                <h1 class="rp-title">Đặt lại mật khẩu</h1>
                <p class="rp-sub">Tạo mật khẩu mới cho tài khoản của bạn</p>
            </div>

            {{-- BODY --}}
            <div class="rp-body">

                {{-- Alerts --}}
                @if (session('success'))
                    <div class="rp-alert rp-alert--success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session('error'))
                    <div class="rp-alert rp-alert--danger">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="" method="post" class="rp-form" novalidate>
                    @csrf

                    {{-- Password --}}
                    <label class="rp-label" for="rpPassword">Mật khẩu mới</label>
                    <div class="rp-input @error('password') is-invalid @enderror">
                        <i class="fa-solid fa-lock"></i>

                        <input id="rpPassword" type="password" name="password" required maxlength="50"
                            placeholder="Nhập mật khẩu mới" oninput="this.value = this.value.replace(/\s/g, '')"
                            autocomplete="new-password">

                        <button class="rp-eye" type="button" data-toggle="#rpPassword" aria-label="Hiện/ẩn mật khẩu">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>

                    @error('password')
                        <div class="rp-error">{{ $message }}</div>
                    @enderror

                    {{-- Strength --}}
                    <div class="rp-strength">
                        <div class="rp-strength-top">
                            <span class="rp-strength-label">Độ mạnh mật khẩu:</span>
                            <span class="rp-strength-text" id="rpStrengthText">—</span>
                        </div>
                        <div class="rp-strength-bars" id="rpStrengthBars">
                            <span></span><span></span><span></span><span></span>
                        </div>
                    </div>

                    {{-- Confirm --}}
                    <label class="rp-label" for="rpConfirm">Xác nhận mật khẩu</label>
                    <div class="rp-input">
                        <i class="fa-solid fa-lock"></i>

                        <input id="rpConfirm" type="password" name="password_confirmation" required maxlength="50"
                            placeholder="Nhập lại mật khẩu mới" oninput="this.value = this.value.replace(/\s/g, '')"
                            autocomplete="new-password">

                        <button class="rp-eye" type="button" data-toggle="#rpConfirm" aria-label="Hiện/ẩn mật khẩu">
                            <i class="fa-regular fa-eye"></i>
                        </button>
                    </div>

                    {{-- Requirements box --}}
                    <div class="rp-req">
                        <div class="rp-req-title">
                            <i class="fa-solid fa-circle-info"></i>
                            <span>Yêu cầu mật khẩu:</span>
                        </div>

                        <ul class="rp-req-list" id="rpReqList">
                            <li data-rule="len"><span class="dot"></span>Ít nhất 8 ký tự</li>
                            <li data-rule="mix"><span class="dot"></span>Có chữ hoa và chữ thường</li>
                            <li data-rule="num"><span class="dot"></span>Có ít nhất 1 số</li>
                            <li data-rule="spec"><span class="dot"></span>Có ít nhất 1 ký tự đặc biệt</li>
                        </ul>
                    </div>

                    <button type="submit" class="rp-btn">
                        <i class="fa-solid fa-check"></i>
                        Đặt lại mật khẩu
                    </button>

                    <div class="rp-divider"></div>

                    <a href="{{ url()->previous() }}" class="rp-back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Quay lại đăng nhập
                    </a>
                </form>
            </div>
        </section>
    </main>

    {{-- JS riêng cho trang này --}}
    <script>
        (function() {
            const rules = {
                len: (s) => s.length >= 8,
                mix: (s) => /[a-z]/.test(s) && /[A-Z]/.test(s),
                num: (s) => /\d/.test(s),
                spec: (s) => /[^A-Za-z0-9]/.test(s),
            };
            const form = document.querySelector('.rp-form');
            const pwd = document.getElementById('rpPassword');
            const strengthText = document.getElementById('rpStrengthText');
            const barsWrap = document.getElementById('rpStrengthBars');
            const bars = barsWrap ? Array.from(barsWrap.querySelectorAll('span')) : [];
            const reqList = document.getElementById('rpReqList');

            function allPass(s) {
                return rules.len(s) && rules.mix(s) && rules.num(s) && rules.spec(s);
            }

            if (form && pwd) {
                form.addEventListener('submit', (e) => {
                    const s = (pwd.value || '').trim();

                    if (!allPass(s)) {
                        e.preventDefault();

                        // (tuỳ chọn) hiện thông báo nhanh
                        if (strengthText) {
                            strengthText.textContent = 'Chưa đạt yêu cầu';
                            strengthText.dataset.level = '1';
                        }

                        // focus vào ô mật khẩu
                        pwd.focus();
                    }
                });
            }

            function scorePassword(s) {
                let score = 0;
                if (rules.len(s)) score++;
                if (rules.mix(s)) score++;
                if (rules.num(s)) score++;
                if (rules.spec(s)) score++;
                return score; // 0..4
            }

            function setStrength(score) {
                const labels = ['Rất yếu', 'Yếu', 'Trung bình', 'Mạnh', 'Rất mạnh'];
                strengthText.textContent = labels[score] || '—';

                bars.forEach((b, i) => {
                    b.classList.toggle('on', i < score);
                });

                // màu chữ theo mức (CSS sẽ tự tô)
                strengthText.dataset.level = String(score);
            }

            function updateReq(s) {
                if (!reqList) return;
                reqList.querySelectorAll('li').forEach(li => {
                    const key = li.getAttribute('data-rule');
                    const ok = rules[key] ? rules[key](s) : false;
                    li.classList.toggle('ok', ok);
                });
            }

            function onInput() {
                const s = pwd.value || '';
                const sc = scorePassword(s);
                setStrength(sc);
                updateReq(s);
            }

            if (pwd) {
                pwd.addEventListener('input', onInput);
                onInput();
            }

            // toggle eye
            document.querySelectorAll('.rp-eye').forEach(btn => {
                btn.addEventListener('click', () => {
                    const sel = btn.getAttribute('data-toggle');
                    const input = sel ? document.querySelector(sel) : null;
                    if (!input) return;
                    const isPwd = input.type === 'password';
                    input.type = isPwd ? 'text' : 'password';
                    const icon = btn.querySelector('i');
                    if (icon) {
                        icon.className = isPwd ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
                    }
                });
            });
        })();
    </script>

    <script src="{{ asset('assets') }}/js/script.js"></script>
</body>

</html>
