@php
$products = App\Models\Product::active()->where('featured_product',1)->latest()->with('reviews')->take(8)->get();
@endphp
<section class="latest-products-section pt-60 pb-60">
    <div class="container">
        <div class="section__header">
            <h5 class="title">@lang('Featured Products')</h5>
            <div class="view-all">
                <a href="{{ route('featured.products') }}" class="view--all">@lang('Show All')</a>
            </div>
        </div>
        <div class="row g-3 justify-content-center">
            @if ($products->count() > 0)   
                @include($activeTemplate.'products.display_products')
            @endif
        </div>
    </div>
</section>