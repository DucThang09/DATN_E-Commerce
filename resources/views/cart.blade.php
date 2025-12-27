<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/style.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/css/guest/cart.css">


</head>

<body>
    @include('components.user_header')
    @php
        $currentStep = request()->routeIs('checkout.selected') || old('name') || $errors->any() ? 2 : 1;
    @endphp
    <section class="cartpage">
        <div class="cartpage-title"> Giỏ hàng của bạn</div>
        <div class="cartpage-grid">
            {{-- LEFT --}}
            <div class="checkout-steps-wrap">
                <form action="{{ route('checkout.selected') }}" method="POST" id="formCheckoutSelected">
                    @csrf

                    <div class="checkout-step-panel is-show" data-step="1">
                        <div class="cartcard">
                            <div class="cartcard-head cartcard-head--selectall">
                                <label class="carthead-selectall">
                                    <input type="checkbox" class="js-select-all">
                                    <span class="cartcard-head__title">Sản phẩm ({{ $cartItemsWP->count() }})</span>
                                </label>

                                <button type="button" class="carthead-delete js-remove-selected">
                                    <i class="fa-solid fa-trash"></i>
                                    <span>Xóa các sản phẩm đã chọn</span>
                                </button>
                            </div>

                            <div class="cartlist">
                                @if ($cartItemsWP->isNotEmpty())
                                    @foreach ($cartItemsWP as $item)
                                        @php
                                            $sub_total = $item->final_price * $item->quantity;
                                        @endphp

                                        <div class="cartitem" data-id="{{ $item->id }}"
                                            data-price="{{ (int) $item->final_price }}"
                                            data-qty="{{ (int) $item->quantity }}"
                                            data-inv="{{ (int) ($item->v_inventory ?? 0) }}"
                                            data-name="{{ $item->name }}">
                                            <label class="cartcheck">
                                                <input type="checkbox" class="js-select-item" name="selected_items[]"
                                                    value="{{ $item->id }}" @checked(!empty($preselected) && in_array((int) $item->id, $preselected, true))>
                                            </label>
                                            <div class="cartthumb">
                                                <div class="cartthumb">
                                                    @php
                                                        $img = $item->image_path ?? ($item->image ?? '');

                                                        if (
                                                            $img &&
                                                            (str_starts_with($img, 'http://') ||
                                                                str_starts_with($img, 'https://'))
                                                        ) {
                                                            $src = $img;
                                                        } else {
                                                            $img = ltrim($img, '/');
                                                            $img = preg_replace('#^(storage/|public/)#', '', $img);
                                                            $src = $img
                                                                ? asset('storage/' . $img)
                                                                : asset('assets/images/no-image.png');
                                                        }
                                                    @endphp
                                                    @php
                                                        $detailUrl = route('quick.view', ['pid' => $item->pid]);
                                                    @endphp
                                                    <a href="{{ $detailUrl }}" class="cartlink">
                                                        <img src="{{ $src }}" alt="{{ $item->name }}">
                                                    </a>
                                                </div>
                                            </div>

                                            <div class="cartinfo">
                                                <a href="{{ $detailUrl }}" class="cartname">
                                                    {{ $item->name }}
                                                </a>
                                                <div class="cartprice">
                                                    <div class="cartprice-row">
                                                        <span class="cartprice-now">
                                                            {{ number_format($item->final_price, 0, ',', '.') }} đ
                                                        </span>

                                                        @if ((int) ($item->discount ?? 0) > 0)
                                                            <span class="cartprice-old">
                                                                {{ number_format($item->base_price, 0, ',', '.') }} đ
                                                            </span>
                                                        @endif
                                                    </div>

                                                    @if (!empty($item->color_name))
                                                        <div class="cartcolor">Màu: {{ $item->color_name }}</div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="cartqty">
                                                <button type="button" class="qtybtn js-qty" data-action="dec"
                                                    aria-label="Giảm">−</button>
                                                <input class="qtyinput js-qty-input" type="text"
                                                    value="{{ $item->quantity }}" readonly>
                                                <button type="button" class="qtybtn js-qty" data-action="inc"
                                                    aria-label="Tăng">+</button>
                                            </div>

                                            <div class="carttotal">
                                                <div class="cartline js-line-total">
                                                    {{ number_format($sub_total, 0, ',', '.') }} đ
                                                </div>

                                                <button type="button" class="icon-remove js-remove-one"
                                                    data-id="{{ $item->id }}" aria-label="Xóa sản phẩm">
                                                    <i class="fa-regular fa-trash-can"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="cart-empty-center">
                                        <img src="{{ asset('assets') }}/images/Search-Empty.webp" alt="">
                                    </div>
                                    <div class="cartempty">Giỏ hàng của bạn đang trống</div>
                                @endif
                            </div>

                            <div class="cartcontinue">
                                <button type="submit" class="btn-continue">
                                    Tiếp tục
                                </button>
                            </div>
                        </div>
                    </div>
                </form>

            </div>

            @include('components.cart_summary', [
                'grandTotal' => $grandTotal,
                'vatRate' => 0,
                'shipping' => 0,
            ])
        </div>
    </section>
    @include('components.footer')
    <script src="{{ asset('assets') }}/js/guest/cart.js" defer></script>
    <script>
        window.CART_ROUTES = {
            checkoutSelected: "{{ route('checkout.selected') }}",
            removeSelected: "{{ route('remove.selected') }}",
            updateQty: "{{ route('cart.update') }}",
        };
    </script>
</body>

</html>
