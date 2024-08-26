@extends($activeTemplate.'layouts.master')
@section('content')
<div class="col-md-12 col-lg-12 col-xl-12 col-xxl-10 m-auto">
    <div class="container">
        <div class="card deposit-preview mt-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <h6 class="title text--base mb-2"><span>@lang('Payment Preview')</span></h6>
                        <img src="{{$deposit->gatewayCurrency()->methodImage()}}" class="card-img-top"
                            alt="@lang('Image')" class="w-100">
                    </div>
                    <div class="col-md-8">
                        <form action="{{ route('ipn.'.$deposit->gateway->alias) }}" method="POST" class="text-center">
                            @csrf

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

                            <button type="button" class="mt-4 cmn--btn w-100 custom-success text-center btn-lg"
                                id="btn-confirm">
                                @lang('Pay Now')
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('script')
    <script src="//pay.voguepay.com/js/voguepay.js"></script>
    <script>
        "use strict";
        var closedFunction = function() {
        }
        var successFunction = function(transaction_id) {
            window.location.href = '{{ route(gatewayRedirectUrl()) }}';
        }
        var failedFunction=function(transaction_id) {
            window.location.href = '{{ route(gatewayRedirectUrl()) }}' ;
        }

        function pay(item, price) {
            //Initiate voguepay inline payment
            Voguepay.init({
                v_merchant_id: "{{ $data->v_merchant_id}}",
                total: price,
                notify_url: "{{ $data->notify_url }}",
                cur: "{{$data->cur}}",
                merchant_ref: "{{ $data->merchant_ref }}",
                memo:"{{$data->memo}}",
                recurrent: true,
                frequency: 10,
                developer_code: '60a4ecd9bbc77',
                custom: "{{ $data->custom }}",
                customer: {
                  name: 'Customer name',
                  country: 'Country',
                  address: 'Customer address',
                  city: 'Customer city',
                  state: 'Customer state',
                  zipcode: 'Customer zip/post code',
                  email: 'example@example.com',
                  phone: 'Customer phone'
                },
                closed:closedFunction,
                success:successFunction,
                failed:failedFunction
            });
        }

        (function ($) {
            
            $('#btn-confirm').on('click', function (e) {
                e.preventDefault();
                pay('Buy', {{ $data->Buy }});
            });

        })(jQuery);
    </script>
@endpush
