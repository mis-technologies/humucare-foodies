@php
    // Flag dishes with size/volume pricing so their card opens Quick View
    // instead of blind-adding a default variant (same rule as the home menu).
    $ids = $products->pluck('id')->all();
    $variantIds = collect();
    if (count($ids)) {
        $variantIds = App\Models\ProductPricePerLiter::whereIn('product_id', $ids)->pluck('product_id')
            ->merge(App\Models\ProductPricePerMilliliter::whereIn('product_id', $ids)->pluck('product_id'))
            ->merge(App\Models\ProductPricePerVolume::whereIn('product_id', $ids)->pluck('product_id'))
            // dishes carrying modifier groups must also pick options first.
            // Only ACTIVE groups count — otherwise a dish attached solely to a
            // disabled group shows "Choose" with nothing to choose.
            ->merge(
                DB::table('product_option_group')
                    ->join('option_groups', 'option_groups.id', '=', 'product_option_group.option_group_id')
                    ->whereIn('product_option_group.product_id', $ids)
                    ->where('option_groups.status', 1)
                    ->pluck('product_option_group.product_id')
            )
            ->unique();
    }
@endphp

@if ($products->count())
    <div class="fd-plist">
        @foreach ($products as $product)
            @include($activeTemplate.'products.card', ['product' => $product, 'variantIds' => $variantIds])
        @endforeach
    </div>

    @if ($products->hasPages())
        <nav class="fd-pagination">{{ $products->links() }}</nav>
    @endif
@else
    <div class="fd-plist-empty">
        <i class="las la-utensils"></i>
        <h5>{{ __($emptyMessage) }}</h5>
        <p>@lang('Try removing a filter or widening your price range.')</p>
    </div>
@endif
