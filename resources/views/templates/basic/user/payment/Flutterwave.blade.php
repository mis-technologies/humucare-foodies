@extends($activeTemplate.'layouts.master')
@section('content')
<div class="col-md-12 col-lg-12 col-xl-12 col-xxl-10 m-auto">
    <div class="container">
        <div class="card mt-3 deposit-preview">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="title text--base mb-2"><span>@lang('Payment Preview')</span></h6>
                        <img src="{{$deposit->gatewayCurrency()->methodImage()}}" class="card-img-top"
                            alt="@lang('Image')" class="w-100">
                    </div>
                    <div class="col-md-8 ps-5 mt-3">
                        <div class="deposit-content w-100">
                            <ul>
                                <li>
                                    @lang('Please Pay:') <span class="text--success">
                                        {{showAmount($deposit->final_amo)}} {{__($deposit->method_currency)}}
                                    </span>
                                </li>
                                <li>
                                    @lang('To Get:') <span class="text--danger">
                                        {{showAmount($deposit->amount)}} {{__($general->cur_text)}}
                                    </span>
                                </li>
                            </ul>
                        </div>
                        <button type="button" class="cmn--btn w-100 mt-3" id="btn-confirm" onClick="payWithRave()">
                            @lang('Pay Now')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
<script src="https://api.ravepay.co/flwv3-pug/getpaidx/api/flwpbf-inline.js"></script>
<script>
    "use strict"
        var btn = document.querySelector("#btn-confirm");
        btn.setAttribute("type", "button");
        const API_publicKey = "{{$data->API_publicKey}}";

        function payWithRave() {
            var x = getpaidSetup({
                PBFPubKey: API_publicKey,
                customer_email: "{{$data->customer_email}}",
                amount: "{{$data->amount }}",
                customer_phone: "{{$data->customer_phone}}",
                currency: "{{$data->currency}}",
                txref: "{{$data->txref}}",
                onclose: function () {
                },
                callback: function (response) {
                    var txref = response.tx.txRef;
                    var status = response.tx.status;
                    var chargeResponse = response.tx.chargeResponseCode;
                    if (chargeResponse == "00" || chargeResponse == "0") {
                        window.location = '{{ url('ipn/flutterwave') }}/' + txref + '/' + status;
                    } else {
                        window.location = '{{ url('ipn/flutterwave') }}/' + txref + '/' + status;
                    }
                        // x.close(); // use this to close the modal immediately after payment.
                    }
                });
        }
</script>
@endpush