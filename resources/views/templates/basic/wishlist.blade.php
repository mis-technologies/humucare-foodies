@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="cart-section pt-60 bg-white">
    <div class="container">
        <div class="cart-header">
            <h6 class="title mb-3">@lang('My Wishlist Products')</h6>
        </div>
        <table class="table cmn--table cart-table">
            <thead>
                <tr>
                    <th>@lang('Product')</th>
                    <th>@lang('Price')</th>
                    <th>@lang('Ratings')</th>
                    <th>@lang('More')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($products as $product)

                @php
                    $price = productPrice($product);
                @endphp

                <tr>
                    <td data-label="@lang('Product')">
                        <div class="product-item">
                            <div class="product-thumb">
                                <img src="{{ getImage(imagePath()['product']['thumb']['path'].'/'.$product->image,imagePath()['product']['thumb']['size']) }}" alt="products">
                            </div>
                            <div class="product-content">
                                <h6 class="name">
                                    <a href="{{ route('product.detail',['id'=>$product->id,'name'=>slug($product->name)]) }}" class="productName" data-product_id="{{ $product->id }}">{{ __($product->name) }}</a>
                                </h6>
                            </div>
                        </div>
                    </td>

                    <td data-label="@lang('Price')">
                        <span class="price">
                            {{ $general->cur_sym }}{{ showAmount($price) }}
                        </span>
                    </td>
                    <td data-label="@lang('Rating')">
                        <div class="ratings">
                            @php
                                $star = showProductRatings($product->avg_rate);
                                echo $star;
                            @endphp
                        </div>
                    </td>
                    <td data-label="@lang('Remove')">
                        <button class="btn btn-sm btn--base add-to-cart" data-product_id="{{ $product->id }}"><i class="las la-cart-plus"></i></button>
                        <button class="btn btn-sm btn--danger remove-wishlist-data" data-product_id="{{ $product->id }}"><i class="las la-trash"></i></button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="100%" class="text-center text--danger">{{ __($emptyMessage) }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="removeWishlistModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
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
                <button type="button" class="btn btn--base remove-single-product">@lang('Yes')</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script>
        (function ($) {
        "use script";
            getWishlistCount();
            function getWishlistCount(){
                $.ajax({
                    type: "GET",
                    url: "{{ route('get-wishlist-count') }}",
                    success: function (response) {
                            var total = Object.keys(response).length;
                            $.each(response, function (indexInArray, value) { 
                                $(document).find(`[data-product_id='${value.product_id}']`).closest('.add-wishlist').addClass('active');
                            });
                        $('.show-wishlist-count').text(total);
                    }
                });
            }

            let removeableWishlistItem = null;
            let modal = $('#removeWishlistModal');

            $('.remove-wishlist-data').on('click',function(){
                removeableWishlistItem = $(this).closest("tr");
                let modal = $('#removeWishlistModal');
                modal.modal('show');
            });

            $('.remove-single-product').on('click',function(){
                let product_id = removeableWishlistItem.find('.productName').data('product_id');
                $.ajax({
                    method: "GET",
                    url: "{{ route('remove-wishlist') }}",
                    data: {product_id:product_id},
                    success: function (response) {
                        if(response.success) {
                            removeableWishlistItem.remove();
                            getWishlistCount();
                            notify('success', response.success);
                        }else{
                            notify('error', response.error);
                        }
                    }
                });
                modal.modal('hide');
            });
        })(jQuery);
    </script>
@endpush