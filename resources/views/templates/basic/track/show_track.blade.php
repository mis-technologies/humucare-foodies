@php
    if ($order->order_status == 0) {
        $width = 0;
    } elseif($order->order_status == 1) {
        $width = 4;
    } elseif($order->order_status == 2) {
        $width = 8;
    } else{
        $width = 12;
    }   
    $orderCheck =  $order->order_status;
    $orderTimes = 3 - $orderCheck;
@endphp
@if ($order->order_status != 9)
<div class="tracking-wrapper">
    <div class="tracking-header d-flex flex-wrap justify-content-between align-items-center">
        <h6 class="order-id">@lang('Order No:') <span class="text--base">{{ __($order->order_no) }}</span></h6>
    </div>
    
    <div class="tracking-body">
        <div class="progress tracking-progress">
            <div class="progress-bar bg--success progress-bar-striped progress-bar-animated"
                role="progressbar" style="width: calc((100% / 12) * {{ $width }})" aria-valuenow="20" aria-valuemin="0"
                aria-valuemax="100">
            </div>
            <div class="options">
                @for($i = 0 ; $i <= $orderCheck; $i++)
                    <span class="breakpoint  bg--success"><i class="fas fa-check"></i></span>
                @endfor
                @for($i = 1 ; $i <= $orderTimes; $i++)
                <span class="breakpoint  bg--danger"><i class="fas fa-times"></i></span>
                @endfor
            </div>
        </div>
    </div>
    <div class="tracking-footer">
        <ul class="tracking-process d-flex flex-sm-nowrap justify-content-between text-center">
            <li class="tracking-process-item">
                <div class="icon"><img src="{{ asset($activeTemplateTrue.'images/order-process/1.png') }}" alt=""></div>
                <h6 class="title">@lang('Pending')</h6>
            </li>
            <li class="tracking-process-item">
                <div class="icon"><img src="{{ asset($activeTemplateTrue.'images/order-process/2.png') }}" alt=""></div>
                <h6 class="title">@lang('Confirmed')</h6>
            </li>
            <li class="tracking-process-item">
                <div class="icon"><img src="{{ asset($activeTemplateTrue.'images/order-process/3.png') }}" alt=""></div>
                <h6 class="title">@lang('Shipped')</h6>
            </li>
            <li class="tracking-process-item">
                <div class="icon"><img src="{{ asset($activeTemplateTrue.'images/order-process/4.png') }}" alt=""></div>
                <h6 class="title">@lang('Delivered')</h6>
            </li>
        </ul>
    </div>
</div>
@else
<div class="tracking-wrapper">
    <div class="tracking-header justify-content-center">
        <h6 class="text-center text--danger">{{ __($emptyMessage) }}</h6>
    </div>
</div>
@endif
