<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Cá Nhân</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- css chung của bạn -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">

    <!-- css riêng trang profile -->
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/user_profile.css">
</head>

<body>

    @include('components.user_header')
@php
                    $active =
                        old('section') === 'security' || $errors->has('old_pass') || $errors->has('new_pass')
                            ? 'security'
                            : 'personal';
                @endphp
    {{-- Top bar giống ảnh --}}
    <div class="profile-topbar">
        <a href="{{ route('home') }}" class="ptb-back" aria-label="Quay lại">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div class="ptb-title">Thông tin cá nhân</div>
        <div class="ptb-spacer"></div>
    </div>

    <main class="profile-page">
        <div class="profile-grid">

            {{-- LEFT CARD --}}
            <aside class="pcard">
                <div class="pcard-banner"></div>

                <div class="pcard-avatar-wrap">
                    @php
                        // Nếu bạn có cột avatar thì thay logic ở đây.
                        $avatar = $user->avatar ?? null;
                        $avatarSrc = $avatar
                            ? asset('storage/' . ltrim($avatar, '/'))
                            : asset('assets/images/no-image.png');
                    @endphp

                    <div class="pcard-avatar">
                        <img src="{{ $avatarSrc }}" alt="Avatar">
                    </div>

                    {{-- Nút camera (UI thôi; muốn upload thật thì nối route sau) --}}
                    <button type="button" class="pcard-camera" title="Đổi ảnh">
                        <i class="fa-solid fa-camera"></i>
                    </button>
                </div>

                <div class="pcard-user">
                    <h2 class="pcard-name">{{ $user->name }}</h2>
                    <div class="pcard-email">{{ $user->email }}</div>
                </div>

                {{-- Stats (bạn truyền từ controller: $productCount, $orderCount, $revenue) --}}
                <div class="pcard-stats">
                    <div class="stat">
                        <div class="stat-num">{{ $productCount ?? 0 }}</div>
                        <div class="stat-label">Sản phẩm</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num">{{ $orderCount ?? 0 }}</div>
                        <div class="stat-label">Đơn hàng</div>
                    </div>
                    <div class="stat">
                        <div class="stat-num">
                            {{ isset($revenue) ? number_format($revenue, 0, ',', '.') . 'đ' : '0đ' }}
                        </div>
                        <div class="stat-label">Doanh thu</div>
                    </div>
                </div>

                <div class="pcard-joined">
                    <i class="fa-regular fa-calendar"></i>
                    <span>Tham gia từ {{ optional($user->created_at)->format('d/m/Y') }}</span>
                </div>
            </aside>

            {{-- RIGHT PANEL --}}
            <section class="ppanel">
                {{-- Tabs --}}
                <div class="ptabs">
                    <button type="button" class="ptab {{ $active==='personal'?'is-active':'' }}" data-target="#tab-personal">
                        <i class="fa-regular fa-user"></i>
                        <span>Thông Tin Cá Nhân</span>
                    </button>
                    <button type="button" class="ptab {{ $active==='security'?'is-active':'' }}" data-target="#tab-security">
                        <i class="fa-solid fa-lock"></i>
                        <span>Bảo Mật</span>
                    </button>
                </div>

                {{-- Flash --}}
                <div class="pflash">
                    @if (session('message'))
                        <div class="palert palert-success">
                            <i class="fa-solid fa-circle-check"></i>
                            <span>{{ session('message') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="palert palert-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif
                </div>

                {{-- TAB: PERSONAL --}}
                <div id="tab-personal" class="ptab-panel {{ $active==='personal'?'is-active':'' }}">
                    <div class="psection-head">
                        <div>
                            <h3 class="psection-title">Thông Tin Cá Nhân</h3>
                        </div>

                        <button type="button" class="pbtn pbtn-danger js-toggle-edit">
                            <i class="fa-solid fa-pen"></i>
                            <span>Chỉnh Sửa</span>
                        </button>
                    </div>

                    {{-- VIEW MODE (giống ảnh: icon + label + value) --}}
                    <div class="pview-mode">
                        <div class="info-list">
                            <div class="info-item">
                                <div class="info-ic"><i class="fa-regular fa-user"></i></div>
                                <div class="info-main">
                                    <div class="info-label">Họ và Tên</div>
                                    <div class="info-value">{{ $user->name }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-ic"><i class="fa-regular fa-envelope"></i></div>
                                <div class="info-main">
                                    <div class="info-label">Email</div>
                                    <div class="info-value">{{ $user->email }}</div>
                                </div>
                            </div>

                            {{-- Các field dưới đây là UI (nếu bạn chưa có cột thì sẽ rỗng) --}}
                            <div class="info-item">
                                <div class="info-ic"><i class="fa-solid fa-phone"></i></div>
                                <div class="info-main">
                                    <div class="info-label">Số Điện Thoại</div>
                                    <div class="info-value">{{ $user->phone ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-ic"><i class="fa-solid fa-location-dot"></i></div>
                                <div class="info-main">
                                    <div class="info-label">Địa Chỉ</div>
                                    <div class="info-value">{{ $user->address ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-ic"><i class="fa-regular fa-building"></i></div>
                                <div class="info-main">
                                    <div class="info-label">Công Ty</div>
                                    <div class="info-value">{{ $user->company ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-ic"><i class="fa-solid fa-briefcase"></i></div>
                                <div class="info-main">
                                    <div class="info-label">Chức Vụ</div>
                                    <div class="info-value">{{ $user->title ?? '—' }}</div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-ic"><i class="fa-solid fa-globe"></i></div>
                                <div class="info-main">
                                    <div class="info-label">Website</div>
                                    <div class="info-value">
                                        @if (!empty($user->website))
                                            <a class="info-link" href="{{ $user->website }}" target="_blank"
                                                rel="noopener">
                                                {{ $user->website }}
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-ic"><i class="fa-regular fa-file-lines"></i></div>
                                <div class="info-main">
                                    <div class="info-label">Giới Thiệu</div>
                                    <div class="info-value">{{ $user->bio ?? '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- EDIT MODE (form) --}}
                    <div class="pedit-mode">
                        <form action="{{ route('profile.update') }}" method="post" class="pform">
                            @csrf
                            <input type="hidden" name="section" value="personal">

                            <div class="pgrid2">
                                <label class="pfield">
                                    <span class="plabel">Họ và Tên</span>
                                    <input type="text" name="name" maxlength="50"
                                        value="{{ old('name', $user->name) }}"
                                        class="pinput @error('name') is-invalid @enderror" required>
                                    @error('name')
                                        <small class="perr">{{ $message }}</small>
                                    @enderror
                                </label>

                                <label class="pfield">
                                    <span class="plabel">Email</span>
                                    <input type="email" name="email" maxlength="80"
                                        value="{{ old('email', $user->email) }}"
                                        class="pinput @error('email') is-invalid @enderror" required>
                                    @error('email')
                                        <small class="perr">{{ $message }}</small>
                                    @enderror
                                </label>
                            </div>

                            <div class="pgrid2">
                                <label class="pfield">
                                    <span class="plabel">Số Điện Thoại</span>
                                    <input type="text" name="phone" maxlength="20"
                                        value="{{ old('phone', $user->phone ?? '') }}" class="pinput">
                                </label>

                                <label class="pfield">
                                    <span class="plabel">Website</span>
                                    <input type="text" name="website" maxlength="255"
                                        value="{{ old('website', $user->website ?? '') }}" class="pinput">
                                </label>
                            </div>

                            <label class="pfield">
                                <span class="plabel">Địa Chỉ</span>
                                <input type="text" name="address" maxlength="255"
                                    value="{{ old('address', $user->address ?? '') }}" class="pinput">
                            </label>

                            <div class="pgrid2">
                                <label class="pfield">
                                    <span class="plabel">Công Ty</span>
                                    <input type="text" name="company" maxlength="120"
                                        value="{{ old('company', $user->company ?? '') }}" class="pinput">
                                </label>

                                <label class="pfield">
                                    <span class="plabel">Chức Vụ</span>
                                    <input type="text" name="title" maxlength="120"
                                        value="{{ old('title', $user->title ?? '') }}" class="pinput">
                                </label>
                            </div>

                            <label class="pfield">
                                <span class="plabel">Giới Thiệu</span>
                                <textarea name="bio" rows="4" class="pinput ptextarea">{{ old('bio', $user->bio ?? '') }}</textarea>
                            </label>

                            <div class="pactions">
                                <button type="submit" class="pbtn pbtn-danger">
                                    <i class="fa-solid fa-floppy-disk"></i>
                                    <span>Lưu</span>
                                </button>

                                <button type="button" class="pbtn pbtn-ghost js-cancel-edit">
                                    <i class="fa-solid fa-xmark"></i>
                                    <span>Hủy</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- TAB: SECURITY --}}
                
                <div id="tab-security" class="ptab-panel {{ $active==='security'?'is-active':'' }}">
                    <div class="psection-head">
                        <div>
                            <h3 class="psection-title">Bảo Mật</h3>
                        </div>
                    </div>

                    <form action="{{ route('profile.update') }}" method="post" class="pform">
                        @csrf
                        <input type="hidden" name="section" value="security">

                        <label class="pfield">
                            <span class="plabel">Mật khẩu cũ</span>
                            <input type="password" name="old_pass" maxlength="50"
                                class="pinput @error('old_pass') is-invalid @enderror" placeholder="Nhập mật khẩu cũ">
                            @error('old_pass')
                                <small class="perr">{{ $message }}</small>
                            @enderror
                        </label>

                        <label class="pfield">
                            <span class="plabel">Mật khẩu mới</span>
                            <input type="password" name="new_pass" maxlength="50"
                                class="pinput @error('new_pass') is-invalid @enderror"
                                placeholder="Nhập mật khẩu mới">
                            @error('new_pass')
                                <small class="perr">{{ $message }}</small>
                            @enderror
                        </label>

                        <label class="pfield">
                            <span class="plabel">Xác nhận mật khẩu mới</span>
                            <input type="password" name="new_pass_confirmation" maxlength="50" class="pinput"
                                placeholder="Xác nhận mật khẩu mới">
                        </label>

                        <div class="prules">
                            <div class="prules-title">Yêu cầu mật khẩu</div>
                            <ul class="prules-list">
                                <li><span class="dot"></span>Ít nhất 8 ký tự</li>
                                <li><span class="dot"></span>Có chữ hoa và chữ thường</li>
                                <li><span class="dot"></span>Có ít nhất 1 số</li>
                                <li><span class="dot"></span>Có ít nhất 1 ký tự đặc biệt</li>
                            </ul>
                        </div>

                        <div class="pactions">
                            <button type="submit" class="pbtn pbtn-danger">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Cập nhật</span>
                            </button>

                            <a href="{{ route('home') }}" class="pbtn pbtn-ghost">
                                <i class="fa-solid fa-arrow-left"></i>
                                <span>Quay lại</span>
                            </a>
                        </div>
                    </form>
                </div>

            </section>
        </div>
    </main>

    @include('components.footer')

    <script src="{{ asset('assets') }}/js/script.js"></script>
    <script src="{{ asset('assets') }}/js/guest/profile_tabs.js"></script>
</body>

</html>
