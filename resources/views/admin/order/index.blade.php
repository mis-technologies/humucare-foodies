@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--md  table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Order NO')</th>
                                <th>@lang('User')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Created At')</th>
                                <th>@lang('Payment Type')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($orders as $order)
                            <tr>
                                <td data-label="@lang('Order NO')">
                                    <span class="small">
                                        {{ __($order->order_no) }}
                                    </span>
                                </td>

                                <td data-label="@lang('User')">
                                    <a href="{{ route('admin.users.detail', @$order->user->id) }}">{{ @$order->user->username }}</a><br>{{ @$order->user->email }}
                                </td>
                                
                                <td data-label="@lang('Price')">
                                    <strong>{{ showAmount($order->total) }} {{ $general->cur_text }}</strong>
                                </td>

                                <td data-label="@lang('Created At')">
                                    {{ showDateTime($order->created_at) }} <br>
                                    {{ diffForHumans($order->created_at) }}
                                </td>

                                <td data-label="@lang('Payment Type')">
                                    @if ($order->payment_type == 1)
                                    <strong>@lang('Online Payment')</strong>
                                    @else
                                    <strong>@lang('Cash On Delivery')</strong>
                                    @endif
                                </td>

                                <td data-label="@lang('Status')">
                                    @php
                                        echo $order->StatusText
                                    @endphp
                                </td>

                                <td data-label="@lang('Action')">
                                    <a href="{{ route('admin.orders.detail', $order->id) }}" class="icon-btn btn--dark ml-1"
                                        data-toggle="tooltip" title="" data-original-title="@lang('Details')">
                                        <i class="las la-desktop text--shadow"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="icon-btn ml-1 @if($order->order_status == 0) btn--warning @elseif($order->order_status == 1) btn--success @elseif($order->order_status == 2) btn--info @elseif($order->order_status == 3) disabled-order @endif  @if($order->order_status == 3 || $order->order_status == 9)  disabled-order @else orderStatusModal @endif" data-toggle="tooltip" data-toggle="tooltip" title="" data-original-title="@if($order->order_status == 0 || $order->order_status == 1 || $order->order_status == 2) @lang('Mark as') @php echo $order->StatusTextButton @endphp @endif" data-url="{{ route('admin.orders.status',$order->id) }}" data-order_status={{ $order->order_status }}>
                                        @if($order->order_status == 0 || $order->order_status == 9)
                                            <i class="las la-spinner"></i>
                                        @elseif($order->order_status == 1)
                                            <i class="lar la-check-circle"></i>
                                        @elseif($order->order_status == 2)
                                            <i class="las la-truck"></i>
                                        @elseif($order->order_status == 3)
                                            <i class="las la-home"></i>
                                        @endif
                                    </a>
                                    <a href="javascript:void(0)" class="icon-btn ml-1 @if($order->order_status == 3 || $order->order_status == 9) disabled-cancel-order @else btn--danger cancelOrderModal @endif" data-toggle="tooltip" data-toggle="tooltip" title="" data-original-title="@if($order->order_status == 0 || $order->order_status == 1 || $order->order_status == 2) @lang('Cancel')@endif"  data-url="{{ route('admin.orders.status',$order->id) }}">
                                        <i class="lar la-times-circle"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>
            @if ($orders->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($orders) }}
            </div>
            @endif
        </div>
    </div>
</div>
<div id="orderStatusModal" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Confirmation Alert!')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="modal-detail"></p>
                    <input type="hidden" name="order_status">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--primary">@lang('Yes')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
<form method="GET" class="form-inline float-sm-right bg--white">
    <div class="input-group has_append">
        <input type="text" name="search" id="mySearch" class="form-control" placeholder="@lang('Search here')" value="{{ request()->search }}">
        <div class="input-group-append">
            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
@endpush

@push('style')
    <style>
        .disabled-order{
            background-color: #6952bd4d;
            cursor: no-drop;
        }
        .disabled-order:hover{
            background-color: #6952bd4d;
        }
        .disabled-cancel-order{
            background-color: #ea545575;
            cursor: no-drop;
        }
        .disabled-cancel-order:hover{
            background-color: #ea545575;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($){
            "use strict";
            $('.orderStatusModal').on('click', function () {
                var modal = $('#orderStatusModal');
                var url = $(this).data('url');
                var orderStatus = $(this).data('order_status');
                if(orderStatus == 0){
                    status = 1;
                }else if(orderStatus == 1){
                    status = 2;
                }else if(orderStatus == 2){
                    status = 3;
                }
                modal.find('form').attr('action', url);
                modal.find('[name=order_status]').val(status);
                modal.find('.modal-detail').text(`@lang('Are you sure to change the order status?')`);
                modal.modal('show');
            });
            $('.cancelOrderModal').on('click', function () {
                var modal = $('#orderStatusModal');
                var url = $(this).data('url');
                var orderStatus = 9;
                modal.find('form').attr('action', url);
                modal.find('[name=order_status]').val(orderStatus);
                modal.find('.modal-detail').text(`@lang('Are you sure to cancel this order?')`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush