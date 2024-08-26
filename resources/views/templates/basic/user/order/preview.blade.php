@extends($activeTemplate.'layouts.frontend')
@section('content')
@include($activeTemplate.'partials.user_dashboard')
                <div class="col-md-10">
                    <div class="deposit-preview">
                        <div class="deposit-thumb">
                            <img src="{{ $data->gatewayCurrency()->methodImage() }}" alt="@lang('Image')">
                        </div>
                        <div class="deposit-content">
                            <ul>
                                <li>
                                    @lang('Amount:') <span class="text--success">{{showAmount($data->total)}}  {{__($general->cur_text)}}</span>
                                </li>
                                <li>
                                    @lang('Charge:') <span class="text--danger">{{showAmount($charge)}} {{__($general->cur_text)}}</span>
                                </li>
                                <li>
                                    @lang('Payable:') <span class="text--warning">{{showAmount($payable)}} {{__($general->cur_text)}}</span>
                                </li>
                                <li>
                                    @lang('Conversion Rate:') <span class="text--info">1 {{__($general->cur_text)}} @lang('=') {{showAmount($data->gatewayCurrency()->rate)}}  {{__($data->baseCurrency())}}</span>
                                </li>
                                <li>
                                    @lang('In') {{$data->baseCurrency()}}: <span class="text--primary">{{showAmount($final_amo)}}</span>
                                </li>
                            </ul>
                            @if( 1000 > $data->method_code)
                                <a href="{{route('user.deposit.confirm')}}" class="cmn--btn">@lang('pay now')</a>
                            @else
                                <a href="{{route('user.deposit.manual.confirm')}}" class="cmn--btn">@lang('Pay Now')</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection


