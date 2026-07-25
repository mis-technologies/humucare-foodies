@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
    $deliveryFee = App\Models\ShippingMethod::where('status', 1)->orderBy('price')->first();

    // Guests get a stdClass (session cart round-tripped through json_encode),
    // logged-in users get an Eloquent Collection. Normalise so the view can
    // count and iterate either shape.
    $cartItems = $carts instanceof \Illuminate\Support\Collection
        ? $carts
        : collect((array) $carts);
    $itemCount = $cartItems->count();

    // Guest carts hold only raw values, so the category tag needs the Product.
    // Fetch them all at once instead of one query per row (N+1).
    $guestProducts = collect();
    if (!auth()->check() && $itemCount) {
        $guestProducts = App\Models\Product::with('category')
            ->whereIn('id', $cartItems->pluck('product_id')->filter()->all())
            ->get()->keyBy('id');
    }
@endphp

<section class="fd-cart">
    {{-- ------------------------------------------------ items --}}
    <div class="fd-cart__main">
        <div class="fd-cart__inner">
            <div class="fd-cart__head">
                <h1 class="fd-cart__title">@lang('My Cart')</h1>
                <span class="fd-cart__count">{{ $itemCount }} {{ $itemCount == 1 ? __('item') : __('items') }}</span>
            </div>

            @if ($itemCount)
            <div class="fd-cart__cols">
                <span>@lang('Product detail')</span>
                <span>@lang('Quantity')</span>
                <span>@lang('Price')</span>
                <span>@lang('Total')</span>
            </div>
            @endif

            <div class="fd-cart__rows">
                @forelse ($cartItems as $cartKey => $cart)
                    @php
                        $user       = auth()->user() ?? null;
                        $product_id = $cart->product_id;
                        $image      = $user ? @$cart->product->image : $cart->image;
                        $name       = $user ? @$cart->product->name  : $cart->name;

                        if ($user) {
                            $price = productPrice($cart->product);
                        } else {
                            $price = showDiscountPrice($cart->price, $cart->discount, $cart->discount_type);
                        }

                        // chosen modifiers add to the unit price
                        $optionsPrice = (float) (@$cart->options_price ?: 0);
                        $optionsList  = @$cart->options;
                        $price        = $price + $optionsPrice;

                        // volume-priced items bill per millilitre, not per unit
                        $subTotal = @$cart->price_per_milliliter
                            ? $cart->price_per_milliliter * $cart->quantity
                            : $price * $cart->quantity;

                        $productModel = $user ? @$cart->product : $guestProducts->get($product_id);
                        $categoryName = optional(optional($productModel)->category)->name;
                    @endphp

                    {{-- cart_key identifies THIS line (a dish may appear on
                         several lines with different modifiers) --}}
                    <div class="fd-cart-row" data-cart_key="{{ $user ? $cart->id : $cartKey }}">
                        <div class="fd-cart-row__product">
                            <a href="{{ route('product.detail', ['id' => $product_id, 'name' => slug($name)]) }}" class="fd-cart-row__thumb">
                                <img src="{{ getImage(imagePath()['product']['thumb']['path'].'/'.$image, imagePath()['product']['thumb']['size']) }}"
                                    alt="{{ __($name) }}">
                            </a>
                            <div class="fd-cart-row__info">
                                <h6 class="fd-cart-row__name">
                                    <a href="{{ route('product.detail', ['id' => $product_id, 'name' => slug($name)]) }}"
                                        class="productName" data-product_id="{{ $product_id }}">{{ __($name) }}</a>
                                </h6>
                                @if (optionSummary($optionsList))
                                    <span class="fd-cart-row__opts">{{ optionSummary($optionsList) }}</span>
                                @endif
                                @if ($categoryName)
                                    <span class="fd-cart-row__tag">{{ __($categoryName) }}</span>
                                @endif
                                <button type="button" class="fd-cart-row__remove remove-btn">@lang('Remove')</button>
                            </div>
                        </div>

                        <div class="fd-cart-row__qty">
                            <div class="fd-qty">
                                <button type="button" class="fd-qty__btn cart-decrease" aria-label="@lang('Decrease')">
                                    <i class="las la-minus"></i>
                                </button>
                                <input type="number" name="quantity" value="{{ $cart->quantity }}" min="1" aria-label="@lang('Quantity')">
                                <button type="button" class="fd-qty__btn cart-increase" aria-label="@lang('Increase')">
                                    <i class="las la-plus"></i>
                                </button>
                            </div>
                        </div>

                        <div class="fd-cart-row__price">
                            <span class="price">{{ $general->cur_sym }}{{ showAmount($price, 2, false) }}</span>
                        </div>

                        <div class="fd-cart-row__total">
                            <span class="subtotal">{{ $general->cur_sym }}{{ showAmount($subTotal, 2, false) }}</span>
                        </div>

                        {{-- volume data the quantity maths reads; not shown in this layout --}}
                        <span class="milliliter d-none">{{ @$cart->milliliter }}</span>
                        <span class="ppml d-none">{{ getAmount(@$cart->price_per_milliliter) }}</span>
                    </div>
                @empty
                    <div class="fd-cart__empty">
                        <i class="las la-shopping-basket"></i>
                        <p>{{ __($emptyMessage) }}</p>
                        <a href="{{ route('products') }}" class="fd-btn fd-btn--pill fd-btn--lg">@lang('Browse the menu')</a>
                    </div>
                @endforelse
            </div>

            <a href="{{ route('products') }}" class="fd-cart__back">
                <i class="las la-arrow-left"></i> @lang('Go back')
            </a>
        </div>
    </div>

    {{-- ------------------------------------------------ summary --}}
    <aside class="fd-cart__summary">
        <div class="fd-sum">
            <h2 class="fd-sum__title">@lang('Order Summary')</h2>

            <div class="fd-sum__row">
                <span>@lang('Items'): <span class="fd-sum__items">{{ $itemCount }}</span></span>
                <b class="subtotal-price">{{ $general->cur_sym }}0.00</b>
            </div>

            <div class="fd-sum__row coupon-show d-none">
                <span>@lang('Discount')</span>
                <b class="discount-price">{{ $general->cur_sym }}0.00</b>
            </div>

            <div class="fd-sum__block">
                <label class="fd-sum__label">@lang('Delivery')</label>
                {{-- informational: the courier is chosen on the checkout page --}}
                <div class="fd-sum__field fd-sum__field--static">
                    @if ($deliveryFee)
                        {{ $general->cur_sym }}{{ showAmount($deliveryFee->price, 2, false) }}
                        <small>@lang('from') · @lang('chosen at checkout')</small>
                    @else
                        <small>@lang('Calculated at checkout')</small>
                    @endif
                </div>
            </div>

            <div class="fd-sum__block">
                <label class="fd-sum__label">@lang('Promo Code')</label>
                <form class="coupon-form fd-sum__promo">
                    <input class="coupon" name="coupon" placeholder="@lang('Promo Code')">
                    <button type="submit" class="coupon-apply">@lang('Apply')</button>
                </form>
            </div>

            <a href="{{ route('hot_deals.products') }}" class="fd-sum__more">
                <span class="ic"><i class="las la-plus"></i></span> @lang('Get More Promotions')
            </a>

            <div class="fd-sum__total">
                <span>@lang('Total')</span>
                <b class="total-price">{{ $general->cur_sym }}0.00</b>
            </div>

            @if ($itemCount)
            <form action="{{ route('user.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="total" value="">
                <button type="submit" class="fd-sum__checkout">@lang('Checkout') <i class="las la-arrow-right"></i></button>
            </form>
            @endif

            {{-- shown by the layout script once the basket qualifies --}}
            <div class="home-service d-none">
                <div class="fd-sum__total">
                    <span>@lang('Home service total')</span>
                    <b class="total-price-hs">{{ $general->cur_sym }}0.00</b>
                </div>
                <form action="{{ route('user.mil.checkout') }}" method="POST">
                    @csrf
                    <input type="hidden" name="total_hs" value="">
                    <button type="submit" class="fd-sum__checkout fd-sum__checkout--alt">@lang('Home service')</button>
                </form>
                <p class="fd-sum__note">@lang('You are eligible for the Home service discount.')</p>
            </div>
        </div>
    </aside>
</section>

<div class="modal fade" id="removeCartModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg--base">
                <strong class="modal-title">@lang('Confirmation Alert!')</strong>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>@lang('Are you sure to remove this product?')</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('No')</button>
                <button type="button" class="btn btn--base remove-product">@lang('Yes')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict";
        // Rows are now cards, not <tr>. Every lookup below is scoped to
        // .fd-cart-row so the quantity / subtotal maths behaves as before.
        var ROW = '.fd-cart-row';
        var currentRow;

        function rowPrice(row) {
            var txt = row.find('.price').text().split("{{ $general->cur_sym }}");
            return parseFloat(txt[1]) || 0;
        }

        $(document).on('click', '.cart-decrease', function () {
            currentRow = $(this).closest(ROW);
            var input = currentRow.find('input[name="quantity"]');
            var quantity = parseInt(input.val(), 10) || 0;

            if (quantity > 1) {
                input.val(quantity - 1);
                CartCalculation(currentRow);
            } else {
                input.val(1);
                notify('error', 'You have to order a minimum amount of one.');
            }
        });

        $(document).on('click', '.cart-increase', function () {
            currentRow = $(this).closest(ROW);
            var input = currentRow.find('input[name="quantity"]');
            input.val((parseInt(input.val(), 10) || 0) + 1);
            CartCalculation(currentRow);
        });

        $(document).on('focusout', 'input[name="quantity"]', function () {
            currentRow = $(this).closest(ROW);
            var quantity = parseInt($(this).val(), 10) || 0;
            if (quantity < 1) {
                $(this).val(1);
                notify('error', 'You have to order a minimum amount of one.');
            }
            CartCalculation(currentRow);
        });

        function CartCalculation(row) {
            var product_id  = row.find('.productName').data('product_id');
            var quantity    = parseInt(row.find('input[name="quantity"]').val(), 10) || 1;
            var ppMilliliter = parseFloat(row.find('.ppml').text()) || 0;
            var totalPrice  = ppMilliliter > 0 ? quantity * ppMilliliter : quantity * rowPrice(row);

            row.find('.subtotal').text("{{ $general->cur_sym }}" + totalPrice.toFixed(2));

            // any applied coupon is invalidated once the basket changes
            $('.coupon-show').addClass('d-none');
            $('.coupon').val('');

            subTotal();

            $.ajax({
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}"},
                method: "POST",
                url: "{{ route('update-cart') }}",
                data: {product_id: product_id, quantity: quantity, cart_key: row.data('cart_key')},
                success: function (response) {
                    if (response.success) { notify('success', response.success); }
                    else { notify('error', response.error); }
                }
            });
        }

        function getCartCount() {
            $.ajax({
                type: "GET",
                url: "{{ route('get-cart-count') }}",
                success: function (response) { $('.show-cart-count').text(response); }
            });
        }

        function subTotal() {
            var subtotal = 0;
            $(ROW).find('.subtotal').each(function () {
                var parts = $(this).text().split("{{ $general->cur_sym }}");
                subtotal += parseFloat(parts[1]) || 0;
            });

            var sub = parseFloat(subtotal * 0.95);
            $('input[name="total_hs"]').val(sub);

            $('.subtotal-price').text("{{ $general->cur_sym }}" + subtotal.toFixed(2));
            $('.total-price').text("{{ $general->cur_sym }}" + subtotal.toFixed(2));
            $('.total-price-hs').text("{{ $general->cur_sym }}" + sub.toFixed(2));

            // keep both item counters in step with the rows actually on screen
            var n = $(ROW).length;
            $('.fd-cart__count').text(n + ' ' + (n === 1 ? "@lang('item')" : "@lang('items')"));
            $('.fd-sum__items').text(n);
        }

        var removeableItem = null;
        var modal = $('#removeCartModal');

        $(document).on('click', '.remove-btn', function () {
            removeableItem = $(this).closest(ROW);
            modal.modal('show');
        });

        $(".remove-product").on('click', function () {
            var product_id = removeableItem.find('.productName').data('product_id');
            $('.coupon-show').addClass('d-none');
            $('.coupon').val('');
            $.ajax({
                method: "GET",
                url: "{{ route('delete-cart') }}",
                data: {product_id: product_id, cart_key: removeableItem.data('cart_key')},
                success: function (response) {
                    if (response.success) {
                        removeableItem.remove();
                        subTotal();
                        getCartCount();
                        notify('success', response.success);
                        if ($(ROW).length === 0) { window.location.reload(); }
                    } else {
                        notify('error', response.error);
                    }
                }
            });
            modal.modal('hide');
        });

        $('.coupon-apply').on('click', function (e) {
            e.preventDefault();
            $.ajax({
                method: "GET",
                url: "{{ route('coupon-apply') }}",
                data: {coupon: $('.coupon').val()},
                success: function (response) {
                    if (response.success) {
                        notify('success', response.success);
                        $('.coupon-show').removeClass('d-none');
                        $('.discount-price').text("{{ $general->cur_sym }}" + parseFloat(response.discount).toFixed(2));
                        $('.subtotal-price').text("{{ $general->cur_sym }}" + parseFloat(response.subtotal).toFixed(2));
                        $('.total-price').text("{{ $general->cur_sym }}" + parseFloat(response.totalAmount).toFixed(2));
                    } else {
                        notify('error', response.error);
                    }
                }
            });
        });

        getCartCount();
        subTotal();
    })(jQuery);
</script>
@endpush
