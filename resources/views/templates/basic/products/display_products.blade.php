@forelse($products as $product)
    <div class="col-xl-3 col-lg-4 col-md-4 col-sm-6 mb-2">
        @include($activeTemplate.'products.card', ['product' => $product])
    </div>
@empty
    <div class="col-12 text-center">
        <strong class="text--danger">{{ __($emptyMessage) }}</strong>
    </div>
@endforelse
