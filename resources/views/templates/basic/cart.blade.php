@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="cart-section pt-60 bg-white">
    <div class="container">
        <div class="cart-header">
            <h4 class="title mb-3">@lang('My Cart')</h4>
        </div>

        <table class="table cmn--table cart-table">
            <thead>
                <tr>
                    <th>@lang('Product')</th>
                    <th>@lang('Unit Price')</th>
                    <th>@lang('Quantity')</th>
                    <th>@lang('Milliliter')</th>
                    <th>@lang('Price Per ml')</th>
                    <th>@lang('Subtotal')</th>
                    <th>@lang('Remove')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($carts as $cart)

                @php
                $user = auth()->user() ?? null;
                $product_id = ($user) ? $cart->product_id : $cart->product_id ;
                $image = ($user) ? @$cart->product->image : $cart->image ;
                $name = ($user) ? @$cart->product->name : $cart->name ;
                if($user){
                $price = productPrice($cart->product);
                }else{
                $price = showDiscountPrice($cart->price,$cart->discount,$cart->discount_type);
                }

                if ($cart->price_per_milliliter) {

                $subTotal = $cart->price_per_milliliter * $cart->quantity;

                }else {
                // $ppml = $cart->price_per_milliliter;
                $subTotal = $price * $cart->quantity;
                }

                @endphp

                <tr>
                    <td data-label="@lang('Product')">
                        <div class="product-item">
                            <div class="product-thumb">
                                <img src="{{ getImage(imagePath()['product']['thumb']['path'].'/'.$image,imagePath()['product']['thumb']['size']) }}"
                                    alt="products">
                            </div>
                            <div class="product-content">
                                <h6 class="name">
                                    <a href="{{ route('product.detail',['id'=>$product_id,'name'=>slug($name)]) }}"
                                        class="productName" data-product_id="{{ $product_id }}">{{ __($name) }}</a>
                                </h6>
                            </div>
                        </div>
                    </td>

                    <td data-label="@lang('Unit Price')">
                        <span class="price">
                            {{ $general->cur_sym }}{{ getAmount($price) }}
                        </span>
                    </td>
                    <td data-label="@lang('Quantity')">
                        <div class="cart-plus-minus">
                            <div class="cart-decrease qtybutton dec">
                                <i class="las la-minus"></i>
                            </div>
                            <input type="number" class="form-control" name="quantity" value="{{ $cart->quantity }}">
                            <div class="cart-increase qtybutton inc active">
                                <i class="las la-plus"></i>
                            </div>
                        </div>
                    </td>


                    <td data-label="@lang('Ml')">
                        <span class="milliliter">
                            {{ $cart->milliliter }}
                        </span>
                    </td>

                    <td data-label="@lang('Price Per ml')">
                        <span class="pppp">
                            {{ $general->cur_sym }}
                            <span class="ppml">
                                {{ getAmount($cart->price_per_milliliter) }}
                            </span>
                        </span>
                    </td>
                    <td data-label="@lang('Subtotal')">

                        <span class="subtotal">
                            {{ $general->cur_sym }}{{ getAmount($subTotal) }}
                        </span>
                    </td>
                    <td data-label="@lang('Remove')">
                        <button class="btn btn-sm btn--danger remove-btn"><i class="las la-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="100%" class="text-center text--danger">{{ __($emptyMessage) }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div class="row gy-4 pt-5 justify-content-between">
            <div class="col-md-5 col-xl-3">
                <a href="{{ route('products') }}" class="cmn--btn btn-lg fs-6 w-100">@lang('Continue Shopping ')
                    <i class="las la-long-arrow-alt-right ms-3"></i>
                </a>
            </div>
            <div class="col-md-5 col-xl-4">
                <form class="coupon-form">
                    <div class="input-group">
                        <input class="form-control form--control coupon" name="coupon"
                            placeholder="@lang('Enter your coupon code')">
                        <button class="btn btn--base coupon-apply">@lang('Apply')</button>
                    </div>
                </form>
            </div>
            <div class="col-xl-4">
                <ul class="cart-details">
                    <li>
                        <h6 class="title text-muted">@lang('Subtotal')</h6>
                        <h6 class="value subtotal-price text--base">{{ $general->cur_sym }}0.00</h6>
                    </li>
                    <li class="coupon-show d-none">
                        <h6 class="title text-muted">@lang('Discount')</h6>
                        <h6 class="value total discount-price text--base">{{ $general->cur_sym }}0.00</h6>
                    </li>
                    <li class="total-show d-none">
                        <h6 class="title text-muted">@lang('Total')</h6>
                        <h6 class="value total total-price text--base">{{ $general->cur_sym }}0.00</h6>
                    </li>
                    <li>
                        <form action="{{route('user.checkout')}}" method="POST">

                            @csrf
                            <input type="hidden" name="total" value="">
                            <button class="cmn--btn w-100">Proceed To Checkout</button>

                        </form>
                        {{-- <a href="{{ route('user.checkout') }}" class="cmn--btn w-100">@lang('Proceed to Checkout')</a> --}}
                    </li>

                </ul>
            </div>
            <div class="col-xl-4 home-service d-none">
                <ul class="cart-details">

                    <li class="">
                        <h6 class="title text-muted">@lang('Total')</h6>
                        <h6 class="value total total-price-hs text--base">{{ $general->cur_sym }}0.00</h6>
                    </li>
                    <li>
                        <form action="{{route('user.mil.checkout')}}" method="POST">
                            @csrf
                            <input type="hidden" name="total_hs" value="">
                            <button class="cmn--btn w-100">@lang('Home service')</button>
                        </form>
                        {{-- <a href="{{ route('user.mil.checkout') }}" class="cmn--btn w-100">@lang('Home service')</a> --}}
                    </li>
                    <hr>
                    <li>
                        <p>You are eligible for Home service discount.</p>
                    </li>

                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="removeCartModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg--base">
                <strong class="modal-title">@lang('Confirmation Alert!')</strong>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
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
        "use script";
        let currentRow;
        let quantity
        $('.cart-decrease').click(function(){
            currentRow = $(this).closest("tr");
            quantity = currentRow.find('input[name="quantity"]').val();

            let productPrice = currentRow.find('.price').text();
            let splitPrice = productPrice.split("{{ $general->cur_sym }}");
            let price = parseFloat(splitPrice[1]);
            let ppMilliliter = currentRow.find('.ppml').text();
            let decPrice = 0;

            if(quantity > 0){

                if (ppMilliliter && parseFloat(ppMilliliter) > 0) {
                decPrice = quantity * parseFloat(ppMilliliter);
                } else {
                    decPrice = price * quantity;
                }
                CartCalculation(currentRow, decPrice)
            }else{
                currentRow.find('input[name="quantity"]').val(1)
                notify('error', 'You have to order a minimum amount of one.');
            }
        });
        $('.cart-increase').click(function(){
            currentRow = $(this).closest("tr");
            let quantity = currentRow.find('input[name="quantity"]').val();
            let productPrice = currentRow.find('.price').text();
            let splitPrice = productPrice.split("{{ $general->cur_sym }}");
            let price = parseFloat(splitPrice[1]);
            let ppMilliliter = currentRow.find('.ppml').text();

            let incPrice = 0;

            if (ppMilliliter && parseFloat(ppMilliliter) > 0) {
                incPrice = quantity * parseFloat(ppMilliliter);
            } else {
                incPrice = price * quantity;
            }

            console.log(quantity);
            console.log(price);
            console.log(ppMilliliter);
            console.log(incPrice);

            CartCalculation(currentRow, incPrice);
        });
        $('input[name="quantity"]').on('focusout',function(){
            currentRow = $(this).closest("tr");
            quantity = currentRow.find('input[name="quantity"]').val();
            if(quantity > 0){
                CartCalculation(currentRow)
            }else{
                currentRow.find('input[name="quantity"]').val(1)
                CartCalculation(currentRow)
                notify('error', 'You have to order a minimum amount of one.');
            }
        });

        function CartCalculation(currentRow, incPrice, decPrice){

            let product_id = currentRow.find('.productName').data('product_id');
            let quantity = currentRow.find('input[name="quantity"]').val();
            let productPrice = currentRow.find('.price').text();
            let splitPrice = productPrice.split("{{ $general->cur_sym }}");
            let price = parseFloat(splitPrice[1]);

            let ppMilliliter = currentRow.find('.ppml').text();
            let milliliter = currentRow.find('.milliliter').text();

            let totalPrice;

            // Use incPrice if passed, otherwise calculate using ppMilliliter or fallback to price
            if (incPrice != 0) {
                totalPrice = incPrice;
            } else if (decPrice != 0) {
                totalPrice = decPrice;
            } else if (ppMilliliter && parseFloat(ppMilliliter) > 0) {
                totalPrice = quantity * parseFloat(ppMilliliter);
            }else {
                totalPrice = quantity * price;
            }

            // Update the subtotal in the UI
            currentRow.find('.subtotal').text("{{ $general->cur_sym }}" + totalPrice.toFixed(2));


            $('.coupon-show').addClass('d-none')
            $('.total-show').addClass('d-none')
            $('.coupon').val('');

            subTotal();
            $.ajax({
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
                method: "POST",
                url: "{{ route('update-cart') }}",
                data: {product_id:product_id,quantity:quantity},
                success: function (response) {
                    if(response.success) {
                        notify('success', response.success);
                    }else{
                        notify('error', response.error);
                    }
                }
            });
        }
        getCartCount();
        function getCartCount(){
            $.ajax({
                type: "GET",
                url: "{{ route('get-cart-count') }}",
                success: function (response) {
                    $('.show-cart-count').text(response);
                }
            });
        }
        subTotal();
        function subTotal(){
            var totalArr = [];
            var subtotal = 0;
            $('.cart-table tr').each(function(index, tr) {
                $(tr).find('td').each (function (index, td) {
                    $(td).find('.subtotal').each (function (index, value) {
                        var productPrice = $(value).text();
                        var splitPrice = productPrice.split("{{ $general->cur_sym }}");
                        console.log(parseFloat(splitPrice[1]));
                        var price = parseFloat(splitPrice[1]);
                        totalArr.push(price);
                    });
                });
            });
            for (var i = 0; i < totalArr.length; i++) {
                subtotal += totalArr[i];
            }
            let sub = parseFloat(subtotal * 0.95);
            $('input[name="total_hs"]').val(sub);

            $('.subtotal-price').text("{{ $general->cur_sym }}"+subtotal.toFixed(2));
            $('.total-price').text("{{ $general->cur_sym }}"+subtotal.toFixed(2));
            $('.total-price-hs').text("{{ $general->cur_sym }}"+sub.toFixed(2));
        }

        let removeableItem = null;
        let modal = $('#removeCartModal');

        $('.remove-btn').on('click',function () {
            removeableItem = $(this).closest("tr");
            modal = $('#removeCartModal');
            modal.modal('show');
        });

        $(".remove-product").on('click',function(){
            let product_id = removeableItem.find('.productName').data('product_id');
            $('.coupon-show').addClass('d-none')
            $('.total-show').addClass('d-none')
            $('.coupon').val('');
            $.ajax({
                method: "GET",
                url: "{{ route('delete-cart') }}",
                data: {product_id:product_id},
                success: function (response) {
                    if(response.success) {
                        subTotal();
                        getCartCount();
                        notify('success', response.success);
                        removeableItem.remove();
                    }else{
                        notify('error', response.error);
                    }
                }
            });
            modal.modal('hide');
        });

        $('.coupon-apply').click(function(e){
            e.preventDefault();
            let coupon = $('.coupon').val();
            $.ajax({
                method: "GET",
                url: "{{ route('coupon-apply') }}",
                data: {coupon:coupon},
                success: function (response) {
                    if(response.success) {
                        console.log(response)
                        notify('success', response.success);
                        $('.coupon-show').removeClass('d-none')
                        $('.total-show').removeClass('d-none')
                        let total = parseFloat(response.totalAmount).toFixed(2);
                        let subtotal = parseFloat(response.subtotal).toFixed(2);
                        let discount = parseFloat(response.discount).toFixed(2);
                        $('.discount-price').text("{{ $general->cur_sym }}"+discount);
                        $('.subtotal-price').text("{{ $general->cur_sym }}"+subtotal);
                        $('.total-price').text("{{ $general->cur_sym }}"+total);
                    }else{
                        notify('error', response.error);
                    }
                }
            });

        })
    })(jQuery);

</script>
@endpush
