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
                        <form action="{{$data->url}}" method="{{$data->method}}">
                            <input type="hidden" custom="{{$data->custom}}" name="hidden">
                            <script src="{{$data->checkout_js}}" 
                                @foreach($data-> val as $key=>$value)
                                    data-{{$key}}="{{$value}}"
                                @endforeach >
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@push('script')
<script>
    (function ($) {
            "use strict";
            $('input[type="submit"]').addClass("mt-4 cmn--btn w-100 custom-success text-center btn-lg");
        })(jQuery);
</script>
@endpush