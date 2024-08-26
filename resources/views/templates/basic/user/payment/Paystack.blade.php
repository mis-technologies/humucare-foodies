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
                                id="btn-confirm">@lang('Pay Now')</button>
                            <script src="//js.paystack.co/v1/inline.js" data-key="{{ $data->key }}"
                                data-email="{{ $data->email }}" data-amount="{{$data->amount}}"
                                data-currency="{{$data->currency}}" data-ref="{{ $data->ref }}"
                                data-custom-button="btn-confirm">
                            </script>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection