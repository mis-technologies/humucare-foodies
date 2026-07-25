@php
    $products = App\Models\Product::active()->where('sale_count', '!=', 0)->orderBy('sale_count', 'desc')->latest()->with('reviews', 'category')->take(8)->get();
@endphp
@if ($products->count() > 0)
<section class="fd-section">
    <div class="fd-container">
        <div class="fd-shead">
            <h2 class="fd-shead__title">@lang('Best Selling')</h2>
        </div>
        <div class="row g-3 justify-content-center">
            @include($activeTemplate.'products.display_products')
        </div>
        <div class="text-center mt-5">
            <a href="{{ route('best-selling.products') }}" class="fd-btn fd-btn--lg fd-btn--ghost">@lang('View All') <i class="las la-arrow-right"></i></a>
        </div>
    </div>
</section>
@endif
