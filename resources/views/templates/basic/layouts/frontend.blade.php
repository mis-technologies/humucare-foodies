@extends($activeTemplate.'layouts.app')
@section('app')
@include($activeTemplate.'partials.header')

@include('sweetalert::alert')

@yield('content')

@include($activeTemplate.'partials.footer')

<div class="modal fade" id="quickView">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content py-4">
            <span data-bs-dismiss="modal" class="modal-close-btn"><i class="las la-times"></i></span>
            <div class="modal-body" id="productmodalView">

            </div>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    'use strict';
    (function ($) {
        // Subscribe Post Method
        $('.newletter-form').on('submit' , function(e){
            e.preventDefault()
            var email    = $('.subscribe-email').val();
            $.ajax({
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
                url:"{{ route('subscribe.post') }}",
                method:"POST",
                data:{email:email},
                success:function(response)
                {
                    if(response.success) {
                        $('.subscribe-email').val('')
                        notify('success', response.success);
                    }else{
                        notify('error', response.error);
                    }
                }
            });
        });
        // modal show
        $(document).on('click','.quickView',function (e) {
            e.preventDefault();
            var product_id = parseInt($(this).data('product_id'));
            $.ajax({
                type: "get",
                url: "{{ route('product.quickView') }}",
                data:{product_id:product_id},
                success: function (response) {
                    $("#productmodalView").html(response);
                }
            });

        });
        // add to cart
        getCartCount();
        getWishlistCount();

        $(document).on('click','.add-to-cart',function (e){
            e.preventDefault();
            var product_id = $(this).data('product_id');
            var quantity = $('.productQuantity').val();
            var liter = $('.productLiter').val();
            var milliliter = $('.productMilliliter').val();
            var pricePerLiter = parseInt($('.price_per_liter').val());
            var pricePerMilliliter = parseInt($('.price_per_milliliter').val());
            if(quantity == undefined){
                quantity = 1;
            }
            if(liter == undefined){
                liter = 0;
                pricePerLiter = 0;
            }
            if(milliliter == undefined){
                milliliter = 0;
                pricePerMilliliter = 0;
            }
            console.log(pricePerLiter);

            $.ajax({
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
                method: "POST",
                url: "{{ route('add-to-cart') }}",
                data: {product_id:product_id,quantity:quantity,liter:liter,milliliter:milliliter,pricePerLiter:pricePerLiter,pricePerMilliliter:pricePerMilliliter},
                success: function (response) {
                    if(response.success) {
                        notify('success', response.success);
                        getCartCount();
                    }else{
                        notify('error', response.error);
                    }
                }
            });
        })
        // add tocart for related products to avoid sending info of the referenced products to the database.
        $(document).on('click','.add-to-cart-related',function (e){
            e.preventDefault();
            var product_id = $(this).data('product_id');
            var quantity = $('.productQuantity').val();

            if(quantity == undefined){
                quantity = 1;
            }

            $.ajax({
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
                method: "POST",
                url: "{{ route('add-to-cart') }}",
                data: {product_id:product_id,quantity:quantity},
                success: function (response) {
                    if(response.success) {
                        notify('success', response.success);
                        getCartCount();
                    }else{
                        notify('error', response.error);
                    }
                }
            });
        })
        // fetch cart count
        function getCartCount(){
            $.ajax({
               type: "GET",
               url: "{{ route('get-cart-count') }}",
               success: function (response) {
                   $('.show-cart-count').text(response);
               }
           });
        }
        // add-wishlist

        $(document).on('click','.add-wishlist',function(e){
            e.preventDefault();
            var product_id = $(this).data('product_id');

            $.ajax({
                type: "GET",
                url: "{{ route('add-wishlist') }}",
                data: {product_id:product_id},
                success: function (response) {
                    if(response.success) {
                        notify('success', response.success);
                        getWishlistCount();
                    }else{
                        notify('error', response.error);
                    }
                }
            });
        })

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
        // Subscribe Post Method
        $('.track-btn').on('click' , function(e){
            e.preventDefault()
            var orderNo = $('[name=order_no]').val();
            $.ajax({
                headers: {"X-CSRF-TOKEN": "{{ csrf_token() }}",},
                url:"{{ route('get.track-order') }}",
                method:"POST",
                data:{orderNo:orderNo},
                success:function(response)
                {
                    if(response.error) {
                        $('#show_track').html(``)
                        notify('error', response.error);
                    }else{
                        $('#show_track').html(response)
                    }
                }
            });
        });

    })(jQuery);
</script>
@endpush
