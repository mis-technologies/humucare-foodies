@extends('admin.layouts.app')

@section('panel')
<div class="row mb-none-30">
    <div class="col-xl-3 col-lg-5 col-md-5 mb-30">
        <div class="card b-radius--10 overflow-hidden box--shadow1">
            <div class="card-body p-0">
                <div class="p-3 bg--white">
                    <div class="">
                        <img src="{{ getImage(imagePath()['profile']['user']['path'] . '/' . @$order->user->image, imagePath()['profile']['user']['size']) }}"
                            alt="@lang('Profile Image')" class="b-radius--10 w-100">
                    </div>
                    <div class="mt-15">
                        <h4 class="">{{ @$order->user->fullname }}</h4>
                        <p class="">{{ @$order->user->email }}</p>
                        <span class="text--small">@lang('Joined At')
                            <strong>{{ showDateTime(@$order->user->created_at, 'd M, Y h:i A') }}</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card b-radius--10 overflow-hidden mt-30 box--shadow1">
            <div class="card-body">
                <h5 class="mb-20 text-muted">@lang('Action')</h5>
                <a href="{{ route('admin.orders.invoice',$order->id) }}" class="btn btn--primary btn--shadow btn-block btn-lg" target="_blank">
                    @lang('Print Invoice')
                </a>
                <a href="{{ route('admin.users.email.single', @$order->user->id) }}" class="btn btn--info btn--shadow btn-block btn-lg">
                    @lang('Send Email')
                </a>
            </div>
        </div>
    </div>

    <div class="col-xl-9 col-lg-7 col-md-7 mb-30">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title border-bottom pb-3">@lang('Order detail of') {{ __($order->order_no) }}</h5>

                <div class="row g-3 my-5">
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Order No')
                                <span class="font-weight-bold">{{ __($order->order_no) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Total Price')
                                <span class="font-weight-bold">{{ showAmount($order->total) }} {{ $general->cur_text }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Payment')
                                @if ($order->payment_type == 1)
                                <span class="font-weight-bold">{{ __(@$order->deposit->gateway->name) }} @lang('payment gateway')</span>
                                @else
                                <span class="font-weight-bold">@lang('Cash on delivery')</span>
                                @endif  
                            </li>
                            @if (@$order->deposit->trx)   
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Payment Trx')
                                <span class="font-weight-bold">{{ @$order->deposit->trx }}</span>
                            </li>
                            @endif
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Order Date')
                                <span class="font-weight-bold">{{ showDateTime($order->created_at) }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Order Status')
                                @php
                                    echo $order->StatusText
                                @endphp
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Shipping Area')
                                <span class="font-weight-bold">{{ __(@$order->shipping->name) }}</span>
                            </li>
                            @if($order->discount != 0) 
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Coupon')
                                <span class="font-weight-bold">{{ __(@$order->coupon->name) }}</span>
                            </li>
                            @endif
                            @php
                                $address = json_decode($order->address);
                            @endphp
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Delivery Address')
                                <span class="font-weight-bold">
                                    {{ __($address->address) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Country & State')
                                <span class="font-weight-bold">
                                    {{ __($address->country) }} @lang('&') {{ __($address->state) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('City & Zip')
                                <span class="font-weight-bold">
                                    {{ __($address->city) }} @lang('&') {{ __($address->zip) }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                @lang('Payment Status')
                                @php
                                    echo $order->PaymentText
                                @endphp
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Product Name')</th>
                                <th>@lang('Quantity')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Subtotal')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($order->orderDetail as $detail)
                            <tr>
                                <td data-label="@lang('Product Name')">
                                    <a href="javascript:void(0)" class="text--dark">
                                        {{ __(@$detail->product->name) }}
                                    </a><br>
                                    @if (@$detail->product->digital_item == 1)
                                        @if(@$detail->product->file_type == 1)
                                        <a href="{{ route('admin.product.digital.file.download', @$detail->product->id) }}" class="text--info">
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

                                <td data-label="@lang('Price')">
                                    <strong>{{ showAmount($detail->price) }} {{ $general->cur_text }}</strong>
                                </td>

                                <td data-label="@lang('Subtotal')">
                                    <strong>{{ showAmount($detail->price * $detail->quantity) }} {{ $general->cur_text }}</strong>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3"></td>
                                <td data-label="@lang('Subtotal')"><span>@lang('Subtotal :')</span><strong> {{ showAmount($order->subtotal) }} {{ $general->cur_text }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td data-label="@lang('Shipping Charge')"><span>@lang('Shipping Charge :')</span><strong> {{ showAmount($order->shipping_charge) }} {{ $general->cur_text }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td data-label="@lang('Discount')"><span>@lang('Discount :')</span><strong> {{ showAmount($order->discount) }} {{ $general->cur_text }}</strong></td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                                <td data-label="@lang('Total')"><span>@lang('Total :')</span><strong> {{ showAmount($order->total) }} {{ $general->cur_text }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div> 
            </div>
        </div>
    </div>
</div>
@endsection
@push('breadcrumb-plugins')
<a href="{{ route('admin.orders.all') }}" class="btn btn-sm btn--primary"><i class="las la-undo"></i> @lang('Back')</a>
@endpush