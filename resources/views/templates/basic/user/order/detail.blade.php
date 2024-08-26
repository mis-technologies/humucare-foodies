@extends($activeTemplate.'layouts.master')
@section('content')
<div class="col-xxl-12 col-lg-12">
    <div class="dashboard-wrapper">
        <div class="row g-3 mb-5">
            <h6>@lang('Order Details')</h6>
            <div class="col-md-6">
                <div class="deposit-preview">
                    <div class="deposit-content w-100">
                        <ul>
                            <li>
                                @lang('Order No')
                                <span>{{ __($order->order_no) }}</span>
                            </li>
                            <li>
                                @lang('Total Price')
                                <span>{{ showAmount($order->total) }} {{ $general->cur_text }}</span>
                            </li>
                            <li>
                                @lang('Payment Type')
                                @if ($order->payment_type == 1)
                                <span>{{ __(@$order->deposit->gateway->name) }} @lang('payment gateway')</span>
                                @else
                                <span>@lang('Cash on delivery')</span>
                                @endif  
                            </li>
                            @if (@$order->deposit->trx)                   
                            <li>
                                @lang('Payment Trx')
                                <span>{{ @$order->deposit->trx }}</span>
                            </li>
                            @endif
                            <li>
                                @lang('Order Date')
                                <span>{{ showDateTime($order->created_at) }}</span>
                            </li>
                            <li>
                                @lang('Order Status')
                                @php
                                    echo $order->StatusText
                                @endphp
                            </li>                          
                        </ul> 
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="deposit-preview ">
                    <div class="deposit-content w-100">
                        <ul>
                            <li>
                                @lang('Shipping Area')
                                <span>{{ __(@$order->shipping->name) }}</span>
                            </li>
                            @if($order->discount != 0) 
                            <li>
                                @lang('Coupon')
                                <span>{{ __(@$order->coupon->name) }}</span>
                            </li>
                            @endif
                            @php
                                $address = json_decode($order->address);
                            @endphp
                            <li>
                                @lang('Delivery Address')
                                <span>
                                    {{ __($address->address) }}
                                </span>
                            </li>
                            <li>
                                @lang('Country & State')
                                <span>
                                    {{ __($address->country) }} @lang('&') {{ __($address->state) }}
                                </span>
                            </li>
                            <li>
                                @lang('City & Zip')
                                <span>
                                    {{ __($address->city) }} @lang('&') {{ __($address->zip) }}
                                </span>
                            </li>
                            <li>
                                @lang('Payment Status')
                                @php
                                    echo $order->PaymentText
                                @endphp
                            </li>
                        </ul> 
                    </div>
                </div>
            </div>
        </div>

        <table class="table cmn--table">
            <thead>
                <tr>
                    <tr>
                        <th scope="col">@lang('Product Name')</th>
                        <th scope="col">@lang('Quantity')</th>
                        <th scope="col">@lang('Price')</th>
                        <th scope="col">@lang('Subtotal')</th>
                    </tr>
                </tr>
            </thead>
            <tbody>
                @forelse($order->orderDetail as $detail)
                <tr>
                    <td data-label="@lang('Product Name')">
                        <a href="javascript:void(0)" class="text--base">
                            {{ __(@$detail->product->name) }}
                        </a><br>

                        @if ($order->order_status == 3)
                            @if(@$detail->product->file_type == 1)
                            <a href="{{ route('user.digital.file.download',[@$detail->product->id,$order->id]) }}" class="text--info">
                                <i class="las la-file"></i> @lang('Digital file')
                            </a>
                            @elseif(@$detail->product->file_type == 2)
                                <a href="{{ @$detail->product->digi_link }}" target="_blank" class="text--info">@lang('Digital file link')</a>
                            @endif
                        @endif

                    </td>
                    <td data-label="@lang('Quantity')">
                        <strong>{{ $detail->quantity }}</strong>
                    </td>
                    <td data-label="@lang('Price')" class="text--base">
                        <strong>{{ showAmount($detail->price) }} {{ $general->cur_text }}</strong>
                    </td>
                    <td data-label="@lang('Subtotal')">
                        <strong>{{ showAmount($detail->price * $detail->quantity) }} {{ $general->cur_text }}</strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="100%" class="text--danger text-center">{{ __($emptyMessage) }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="total-wrapper">
            <div class="d-flex flex-wrap justify-content-between">
                <strong>@lang('Subtotal :')</strong><strong> {{ showAmount($order->subtotal) }} {{ $general->cur_text }}</strong></strong>
            </div>
            <div class="d-flex flex-wrap justify-content-between">
                <strong>@lang('Shipping Charge :')</strong><strong> {{ showAmount($order->shipping_charge) }} {{ $general->cur_text }}</strong>
            </div>
            @if($order->discount != 0) 
            <div class="d-flex flex-wrap justify-content-between">
                <strong>@lang('Discount :')</strong><strong> {{ showAmount($order->discount) }} {{ $general->cur_text }}</strong>
            </div>
            @endif
            <div class="d-flex flex-wrap justify-content-between border-0">
                <strong>@lang('Total :')</strong><strong> {{ showAmount($order->total) }} {{ $general->cur_text }}</strong>
            </div>
        </div>
    </div>
</div>
@endsection
@push('style')
    <style>
        .total-wrapper {
            max-width: 300px;
            margin-left: auto;
            margin-top: 15px;
            font-size: 14px;
            margin-right: 20px;
        }
        @media (max-width:575px) {
            .total-wrapper {
                margin-right: 0;
            }
        }

        .total-wrapper > div {
            padding: 6px 0;
            border-bottom: 1px dashed #ddd;
        }
    </style>
@endpush