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

            var volume = $(this).data('volume');
            var price = $(this).data('price');

            if(quantity == undefined){
                quantity = 1;
            }
            if(volume == undefined){
                volume = 0;
            }
            if(price == undefined){
                price = 0;
            }

            $.ajax({
                headers: {"X-CSRF-TOKEN": "{{   csrf_token() }}",},
                method: "POST",
                url: "{{ route('add-to-cart') }}",
                data: {product_id:product_id,quantity:quantity,volume:volume,price:price},
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

            quantity = 1;

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

        // home service div
        $(document).ready(function() {
            // Function to check total price and show/hide the home service div
            function checkTotalPrice() {
                let totalPriceText = $('.total-price').text();
                let splitPrice = totalPriceText.split("{{ $general->cur_sym }}");
                let totalPrice = parseFloat(splitPrice[1]);

                $('input[name="total"]').val(totalPrice);
                
                if (totalPrice >= 220) {

                    $('.home-service').removeClass('d-none'); // Make home service div visible

                    notify('success', 'You are eligible for Home Service discount, Use the home service discount button to checkout');
                } else {
                    $('.home-service').addClass('d-none'); // Hide home service div if not 220
                }
            }

            // Check when the page is loaded
            checkTotalPrice();

            // Monitor changes in total price by setting a timer to check periodically
            setInterval(checkTotalPrice, 10000); // Check every second (you can adjust the interval if needed)
        });


    })(jQuery);
</script>
@endpush
