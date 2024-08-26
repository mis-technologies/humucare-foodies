@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="checkout-section pt-60 pb-60 bg-white">
    <div class="container">
        <form id="checkout-form" action="{{ route('user.checkout.order') }}" method="POST">
            @csrf
            <div class="row gy-4 gy-sm-5">
                <div class="col-lg-7">
                    <div class="checkout-wrapper order-summary cmn--card">
                        <h6 class="mb-4">@lang('Billing details')</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('First name')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form--control" name="firstname" required=""
                                        value="{{ Auth::user()->firstname }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Last name')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form--control" name="lastname" required=""
                                        value="{{ Auth::user()->lastname }}" readonly>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Phone')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control form--control" name="mobile" required=""
                                        value="{{ Auth::user()->mobile }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Email address')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="email" name="email" class="form-control form--control" required=""
                                        value="{{ Auth::user()->email }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Country')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <select name="country" class="form-control form--control">
                                        <option value="" selected disabled>@lang('Select One')</option>
                                        @foreach ($countries as $country)
                                        <option value="{{ $country->country }}" @if(old('country')==$country->country) selected="selected" @endif>
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
                                    <select name="shipping_method" class="form-select form--control shipping-type">
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
                                    <input type="text" class="form-control form--control" name="address" required=""
                                        value="{{ old('address') }}">
                                </div>
                            </div>
                            <div class=" col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('State')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form--control" name="state" required=""
                                        value="{{ old('state') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('City')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form--control" name="city" required=""
                                        value="{{ old('city') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form--label">@lang('Zip')
                                        <span class="text--danger">*</span>
                                    </label>
                                    <input type="text" class="form-control form--control" name="zip" required=""
                                        value="{{ old('zip') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="col-lg-5">
                    <div class="order-summary">
                        <h6 class="border-bottom pb-2">@lang('Order detail')</h6>
                        <ul class="subtotal-area">
                            <li class="border-bottom my-3">
                                <h6 class="title">@lang('Subtotal')</h6>
                                <b class="text--base fs--14px"><span>{{ $general->cur_sym }}{{ getAmount($data['subtotal']) }}</span></b>
                            </li>
                            <li class="border-bottom my-3 @if($data['discount'] == 0) d-none @endif">
                                <h6 class="title">@lang('Discount')</h6>
                                <b class="text--base fs--14px"><span>{{ $general->cur_sym }}{{ getAmount($data['discount'])}}</span></b>
                            </li>
                            <li class="border-bottom my-3 @if($data['discount'] == 0) d-none @endif">
                                <h6 class="title">@lang('Total')</h6>
                                <b class="text--base fs--14px"><span>{{ $general->cur_sym }}{{ getAmount($data['total']) }}</span></b>
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
                                        <input id="onlinePayment" type="radio" class="form-check-input" name="payment_type" value="1">
                                        <label for="onlinePayment" class="form-check-label">@lang('Online Payment')</label>
                                    </div>

                                    <div class="form-check form--check">
                                        <input id="cashOnDelivery" type="radio" name="payment_type" class="form-check-input" value="2">
                                        <label for="cashOnDelivery" class="form-check-label">@lang('Cash On Delivery')</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="cmn--btn w-100 btn--sm mt-4" form="checkout-form">@lang('Place order')</button>
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
</style>
@endpush
@push('script')
<script>
(function ($) {
    'use script';
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