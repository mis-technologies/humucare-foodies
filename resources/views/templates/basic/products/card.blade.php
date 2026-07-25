@php
    $price = productPrice($product);
    $detailUrl = route('product.detail', ['id' => $product->id, 'name' => slug($product->slug)]);
    $hasDiscount = $product->discount != 0 || $product->today_deals == 1;
    $isOutOfStock = $general->display_stock == 1 && $product->quantity == 0;
    // Products with size/volume pricing must not be blind-added — the caller
    // may pass $variantIds (a collection of such product ids) so the button
    // opens Quick View to choose options instead. Absent = treat as simple.
    $needsOptions = isset($variantIds) && $variantIds->contains($product->id);
@endphp
<div class="fd-card">
    <div class="fd-card__media">
        @if ($hasDiscount)
            @php echo discountText($product, $general); @endphp
        @endif
        <a href="{{ $detailUrl }}">
            <img src="{{ getImage(imagePath()['product']['thumb']['path'] . '/' . $product->image, imagePath()['product']['thumb']['size']) }}"
                alt="{{ __($product->name) }}">
        </a>
        <div class="fd-card__tools">
            <a href="#0" data-bs-toggle="modal" data-bs-target="#quickView" class="quickView"
                data-product_id="{{ $product->id }}" aria-label="@lang('Quick view')">
                <i class="las la-expand-arrows-alt"></i>
            </a>
            <a href="#0" data-product_id="{{ $product->id }}" class="add-wishlist" aria-label="@lang('Wishlist')">
                <i class="las la-heart"></i>
            </a>
        </div>
    </div>
    <div class="fd-card__body">
        <span class="fd-card__rating">
            <i class="las la-star"></i>{{ showAmount($product->avg_rate, 1) }}
        </span>

        <div class="fd-card__row">
            <h6 class="fd-card__title"><a href="{{ $detailUrl }}">{{ __($product->name) }}</a></h6>
            <span class="fd-card__price">
                @if ($hasDiscount)
                    <del>{{ $general->cur_sym }}{{ showAmount($product->price) }}</del>
                @endif
                {{ $general->cur_sym }}{{ showAmount($price) }}
            </span>
        </div>

        @if ($isOutOfStock)
            <span class="fd-card__stock">@lang('Out of Stock')</span>
        @endif

        <div class="fd-card__foot">
            @if (optional($product->category)->name)
                <span class="fd-card__tag">{{ __($product->category->name) }}</span>
            @else
                <span></span>
            @endif

            @if ($isOutOfStock)
                <span class="fd-card__add fd-card__add--disabled" aria-disabled="true">@lang('Sold out')</span>
            @elseif ($needsOptions)
                <a href="#0" class="fd-card__add quickView" data-bs-toggle="modal" data-bs-target="#quickView"
                    data-product_id="{{ $product->id }}">
                    <i class="las la-sliders-h"></i> @lang('Choose')
                </a>
            @else
                <a href="#0" class="fd-card__add add-to-cart" data-product_id="{{ $product->id }}">
                    <i class="las la-shopping-cart"></i> @lang('Add')
                </a>
            @endif
        </div>
    </div>
</div>
