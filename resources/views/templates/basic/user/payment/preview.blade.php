@extends($activeTemplate.'layouts.master')
@section('content')
<div class="deposit-preview cmn--card">
    <div class="deposit-thumb">
        <img src="{{ $data->gatewayCurrency()->methodImage() }}" alt="@lang('Image')">
    </div>
    <div class="deposit-content">
        <ul>
            <li>
                @lang('Amount:') <span class="text--success">{{showAmount($data->amount)}}
                    {{__($general->cur_text)}}</span>
            </li>
            <li>
                @lang('Charge:') <span class="text--danger">{{showAmount($data->charge)}}
                    {{__($general->cur_text)}}</span>
            </li>
            <li>
                @lang('Payable:') <span class="text--warning">{{showAmount($data->amount + $data->charge)}}
                    {{__($general->cur_text)}}</span>
            </li>
            <li>
                @lang('Conversion Rate:')
                <span class="text--info">
                    1 {{__($general->cur_text)}} @lang('=') {{showAmount($data->rate)}}
                    {{__($data->baseCurrency())}}
                </span>
            </li>
            <li>
                @lang('In') {{$data->baseCurrency()}}: <span
                    class="text--primary">{{showAmount($data->final_amo)}}</span>
            </li>
        </ul>
        @if( 1000 >$data->method_code)
        <a href="{{route('user.deposit.confirm')}}" class="cmn--btn w-100">@lang('pay now')</a>
        @else
        <a href="{{route('user.deposit.manual.confirm')}}" class="cmn--btn w-100">@lang('Pay Now')</a>
        @endif
    </div>
</div>
@endsection