@extends($activeTemplate.'layouts.master')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-12 col-lg-12 col-xl-12 col-xxl-10">
        <div class="card">
            <div class="card-header py-3 text-center bg--base">
                <h5 class="m-0 title text-light">@lang('Stripe Payment')</h5>
            </div>
            <div class="card-wrapper mt-3"></div>
            <br><br>
            <form role="form" id="payment-form" method="{{$data->method}}" action="{{$data->url}}">
                @csrf
                <input type="hidden" value="{{$data->track}}" name="track">
                <div class="card-body py-4">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="name">@lang('Name on Card')</label>
                            <div class="input-group">
                                <input type="text" class="form-control form--control custom-input" name="name"
                                    placeholder="@lang('Name on Card')" autocomplete="off" autofocus />
                                <div class="input-group-append">
                                    <span class="input-group-text h-100"><i class="fa fa-font"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="cardNumber">@lang('Card Number')</label>
                            <div class="input-group">
                                <input type="tel" class="form-control form--control custom-input" name="cardNumber"
                                    placeholder="@lang('Valid Card Number')" autocomplete="off" required autofocus />
                                <div class="input-group-append">
                                    <span class="input-group-text h-100"><i class="fa fa-credit-card"></i></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label for="cardExpiry">@lang('Expiration Date')</label>
                            <input type="tel" class="form-control form--control input-sz custom-input" name="cardExpiry"
                                placeholder="@lang('MM / YYYY')" autocomplete="off" required />
                        </div>
                        <div class="col-md-6 ">
                            <label for="cardCVC">@lang('CVC Code')</label>
                            <input type="tel" class="form-control form--control input-sz custom-input" name="cardCVC"
                                placeholder="@lang('CVC')" autocomplete="off" required />
                        </div>
                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="cmn--btn w-100"> @lang('PAY NOW')</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection


@push('script')
<script src="{{ asset('assets/global/js/card.js') }}"></script>
<script>
    (function ($) {
            "use strict";
            var card = new Card({
                form: '#payment-form',
                container: '.card-wrapper',
                formSelectors: {
                    numberInput: 'input[name="cardNumber"]',
                    expiryInput: 'input[name="cardExpiry"]',
                    cvcInput: 'input[name="cardCVC"]',
                    nameInput: 'input[name="name"]'
                }
            });
        })(jQuery);
</script>
@endpush