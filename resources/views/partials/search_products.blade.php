@foreach ($products as $product)
    @php
        $sale = $product->price * (1 - $product->discount / 100);
        $href = route('quick.view', ['pid' => $product->id]);
        $discount = (int) ($product->discount ?? 0);
    @endphp

    <form action="{{ route('cart.add') }}" method="post" class="p-card">
        @csrf
        @php
            $img = $product->v_image_01 ?? ($product->image_01 ?? '');
            $img = ltrim($img, '/');
            $img = preg_replace('#^(storage/|public/)#', '', $img);
        @endphp
        <input type="hidden" name="pid" value="{{ $product->id }}">
        <input type="hidden" name="name" value="{{ $product->name }}">
        <input type="hidden" name="price" value="{{ $sale }}">
        <input type="hidden" name="variant_id" value="{{ $product->variant_id ?? '' }}">
        <input type="hidden" name="image" value="{{ $img }}">
        <div class="p-top">
            <a class="p-media" href="{{ $href }}">
                <span class="p-badge">Giảm {{ $discount }}%</span>
                <span class="p-badge">Giảm {{ $discount }}%</span>
                @php
                    $img = $product->v_image_01 ?? ($product->image_01 ?? '');
                    $img = ltrim($img, '/');
                    $img = preg_replace('#^(storage/|public/)#', '', $img);
                @endphp

                <img src="{{ $img ? asset('storage/' . $img) : asset('assets/images/no-image.png') }}"
                    alt="{{ $product->name }}">
            </a>
        </div>

        <div class="p-body">
            <a class="p-title" href="{{ $href }}" title="{{ $product->name }}">
                {{ $product->name }}
            </a>

            <div class="p-prices">
                <span class="p-sale">{{ number_format($sale, 0, ',', '.') }}đ</span>
                <span class="p-old">{{ number_format($product->price, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </form>
@endforeach
