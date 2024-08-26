@php
$products = App\Models\Product::active()->where('hot_deals',1)->latest()->with('reviews')->take(8)->get();
@endphp
<section class="hot-deal-section pt-120 pb-60">
    <div class="container">
        <div class="section__header">
            <h5 class="title">@lang('Hot Deals')</h5>
            <div class="view-all">
                <a href="{{ route('hot_deals.products') }}" class="view--all">@lang('Show All')</a>
            </div>
        </div>
        <div class="row justify-content-center g-3">
            @if ($products->count() > 0)   
                @include($activeTemplate.'products.display_products')
            @endif
        </div>
    </div>
</section>