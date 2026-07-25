@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="checkout-section pt-60 pb-60 bg-white">
    <div class="container">
        <form id="checkout-form" action="{{ route('user.checkout.order') }}" method="POST">
            @csrf
            <div class="row gy-4 gy-sm-5">
                <div class="col-lg-7">
                    <div class="checkout-wrapper order-summary cmn--card">
                        @php $orderCfg = orderConfig(); $openState = restaurantOpen(); @endphp

                        @unless ($openState['open'])
                            <div class="alert alert-warning d-flex align-items-center" role="alert" style="gap:10px">
                                <i class="las la-clock" style="font-size:22px"></i>
                                <span>{{ $openState['reason'] ?: __('We are closed right now and cannot take orders.') }}</span>
                            </div>
                        @endunless

                        {{-- Delivery vs Collection --}}
                        @if ($orderCfg['delivery_enabled'] || $orderCfg['collection_enabled'])
                        <div class="fd-fulfil mb-4">
                            @if ($orderCfg['delivery_enabled'])
                            <label class="fd-fulfil__opt">
                                <input type="radio" name="fulfilment_type" value="delivery" checked>
                                <span class="fd-fulfil__card">
                                    <i class="las la-shipping-fast"></i>
                                    <b>@lang('Delivery')</b>
                                    <small>@lang('Bring it to my door')</small>
                                </span>
                            </label>
                            @endif
                            @if ($orderCfg['collection_enabled'])
                            <label class="fd-fulfil__opt">
                                <input type="radio" name="fulfilment_type" value="collection"
                                    {{ $orderCfg['delivery_enabled'] ? '' : 'checked' }}>
                                <span class="fd-fulfil__card">
                                    <i class="las la-store"></i>
                                    <b>@lang('Collection')</b>
                                    <small>@lang('I will pick it up')</small>
                                </span>
                            </label>
                            @endif
                        </div>
                        @endif

                        <h6 class="mb-4">@lang('Billing details')</h6>
                        <div class="row">
                            @if (Route::currentRouteName() == 'user.mil.checkout' || Route::currentRouteName() ==
                            'user.checkout')
                            <input type="hidden" required
                                name="{{ Route::currentRouteName() == 'user.mil.checkout' ? 'hs_price' : 'price' }}"
                                value="{{ $data['subtotal'] }}">
                            @endif

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('First name')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" required class="form-control form--control" name="firstname" required=""
                                        value="{{ Auth::check() ? Auth::user()->firstname : '' }}" >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Last name')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" required class="form-control form--control" name="lastname" required=""
                                        value="{{ Auth::check() ? Auth::user()->lastname : '' }}" >
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Phone')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="tel" required class="form-control form--control" name="mobile" required=""
                                        value="{{ Auth::check() ? Auth::user()->mobile : '' }}" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Email address')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="email" required name="email" class="form-control form--control" required=""
                                        value="{{ Auth::check() ? Auth::user()->email : '' }}" >
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Country')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <select required name="country" class="form-control form--control">
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($countries as $country)
                                        <option value="{{ $country->country }}" @if(old('country')==$country->country)
                                            selected="selected" @endif>
                                            {{__($country->country) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Shipping address')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <select name="shipping_method" required class="form-select form--control shipping-type">
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($shippingMethod as $method)
                                        <option value="{{ $method->id }}">{{ __($method->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Address')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" required class="form-control form--control" name="address" required=""
                                        value="{{ old('address') }}">
                                </div>
                            </div>
                            <div class=" col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('County')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" required class="form-control form--control" name="state" required=""
                                        value="{{ old('state') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('City')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" required class="form-control form--control" name="city" required=""
                                        value="{{ old('city') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Post Code')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" required class="form-control form--control" name="zip" required=""
                                        value="{{ old('zip') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- @php
                dd($data['total']);

                @endphp --}}
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h6 class="border-bottom pb-2">@lang('Order detail')</h6>
                        <ul class="subtotal-area">
                            <li class="border-bottom my-3">
                                <h6 class="title">@lang('Subtotal')</h6>
                                <b class="text--base fs--14px"><span>{{ $general->cur_sym }}{{
                                        getAmount($data['subtotal']) }}</span></b>
                            </li>
                            <li class="border-bottom my-3 @if($data['discount'] == 0) d-none @endif">
                                <h6 class="title">@lang('Discount')</h6>
                                <b class="text--base fs--14px"><span>{{ $general->cur_sym }}{{
                                        getAmount($data['discount'])}}</span></b>
                            </li>
                            <li class="border-bottom my-3 @if($data['discount'] == 0) d-none @endif">
                                <h6 class="title">@lang('Total')</h6>
                                <b class="text--base fs--14px"><span>{{ $general->cur_sym }}{{ getAmount($data['total'])
                                        }}</span></b>
                            </li>
                        </ul>
                        <ul class="subtotal-area mt-3 grand-total d-none">
                            <li class="border-bottom my-3">
                                <h6 class="title">@lang('Shipping Charge')</h6>
                                <b class="shipping-price text--base">
                                    <span>{{ $general->cur_sym }}0.00</span>
                                </b>
                            </li>
                            <li class="border-bottom my-3">
                                <h6 class="title">@lang('Grand Total')</h6>
                                <b class="grand-total-price text--base">
                                    <span>{{ $general->cur_sym }}{{ $data['total']}}</span>
                                </b>
                            </li>
                        </ul>


                        <div class="payment-methods mt-4">
                            <h6 class="border-bottom pb-2">@lang('Payment methods')
                                <span class="text--danger">*</span>
                            </h6>
                            <div class="payment-methods d-flex flex-wrap mt-3" style="gap:10px">
                                <div class="d-flex flex-wrap" style="gap:25px">
                                    <div class="form-check form--check">
                                        <input id="onlinePayment" type="radio" class="form-check-input"
                                            name="payment_type" value="1">
                                        <label for="onlinePayment" class="form-check-label">@lang('Online
                                            Payment')</label>
                                    </div>

                                    <div class="form-check form--check">
                                        <input id="cashOnDelivery" type="radio" name="payment_type"
                                            class="form-check-input" value="2">
                                        <label for="cashOnDelivery" class="form-check-label">@lang('Cash On
                                            Delivery')</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="cmn--btn w-100 btn--sm mt-4" form="checkout-form">@lang('Place
                            order')</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
@push('style')
<style>
    .shipping-method {
        text-align: end;
    }
    .fd-fulfil { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .fd-fulfil__opt { margin: 0; cursor: pointer; }
    .fd-fulfil__opt input { position: absolute; opacity: 0; }
    .fd-fulfil__card {
        display: flex; flex-direction: column; align-items: center; gap: 2px;
        border: 2px solid #ececf3; border-radius: 14px; padding: 16px 10px; text-align: center;
        transition: border-color .15s ease, background .15s ease;
    }
    .fd-fulfil__card i { font-size: 26px; color: #f5a623; }
    .fd-fulfil__card b { font-family: "Fredoka", sans-serif; color: #171733; }
    .fd-fulfil__card small { color: #8a8aa3; font-size: 12px; }
    .fd-fulfil__opt input:checked + .fd-fulfil__card { border-color: #f5a623; background: rgba(245,166,35,.07); }
    .fd-fulfil__opt input:focus-visible + .fd-fulfil__card { box-shadow: 0 0 0 3px rgba(245,166,35,.3); }
</style>
@endpush
@push('script')
<script>
    (function ($) {
    'use script';
    // Delivery needs an address + courier; collection hides them and drops the fee.
    var deliveryNames = ['country','shipping_method','address','state','city','zip'];
    function applyFulfilment() {
        var mode = $('input[name=fulfilment_type]:checked').val() || 'delivery';
        var isDelivery = mode === 'delivery';
        deliveryNames.forEach(function (n) {
            var $f = $('[name="' + n + '"]');
            $f.closest('.col-md-6').toggle(isDelivery);
            if (isDelivery) { $f.attr('required', 'required'); }
            else { $f.removeAttr('required'); }
        });
        if (!isDelivery) {
            // collection => no delivery charge line
            $('.grand-total').addClass('d-none');
        }
    }
    $(document).on('change', 'input[name=fulfilment_type]', applyFulfilment);
    applyFulfilment();

    $('.shipping-type').change(function (e) {
        e.preventDefault();
        var ship_id = $(this).val();
        var totalAmount = parseFloat("{{ $data['total'] }}");
        $.ajax({
            type: "GET",
            url: "{{ route('user.shipping.method') }}",
            data: {ship_id:ship_id,totalAmount:totalAmount},
            success: function (response) {
                if(response.success) {
                    var shippingPrice = parseFloat(response.shippingPrice).toFixed(2);
                    var grandTotal = parseFloat(response.grandTotal).toFixed(2);
                    $('.grand-total').removeClass('d-none');
                    $('.shipping-price').text("{{ $general->cur_sym }}"+shippingPrice);
                    $('.grand-total-price').text("{{ $general->cur_sym }}"+grandTotal);
                }else{
                    notify('error', response.error);
                }
            }
        });
    });
})(jQuery);
</script>
@endpush
