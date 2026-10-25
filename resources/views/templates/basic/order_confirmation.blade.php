@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
    $isCollection = isset($address->type) && $address->type === 'collection';
    $firstName    = strtok((string) $order->customer_name, ' ') ?: __('there');
    $paidOnline   = $order->payment_type == 1 && $order->payment_status == 1;
@endphp

<section class="fd-confirm">
    <div class="fd-confirm__card">
        @if ($pending)
            <div class="fd-confirm__badge fd-confirm__badge--wait" aria-hidden="true"><i class="las la-hourglass-half"></i></div>
            <h1 class="fd-confirm__title">@lang('Confirming your payment…')</h1>
            <p class="fd-confirm__lead" role="status">
                @if ($checks < 10)
                    @lang('This only takes a moment. Please keep this page open.')
                @else
                    @lang('Your bank is taking a little longer than usual. Your order is saved, and we\'ll email you as soon as the payment is confirmed.')
                @endif
            </p>
        @else
            <div class="fd-confirm__badge" aria-hidden="true"><i class="las la-check"></i></div>
            <h1 class="fd-confirm__title">@lang('Thank you'), {{ $firstName }}!</h1>
            <p class="fd-confirm__lead">
                @if ($awaitingTransfer)
                    @lang('We\'ve received your order and your transfer details. We\'ll start preparing it as soon as the payment is verified.')
                @elseif ($isCollection)
                    @lang('Your order is in. We\'ll let you know when it\'s ready to collect.')
                @else
                    @lang('Your order is in and the kitchen has it. We\'ll be in touch when it\'s on its way.')
                @endif
            </p>
        @endif

        <div class="fd-confirm__ref">
            <span class="fd-confirm__ref-label">@lang('Order number')</span>
            <span class="fd-confirm__ref-no">{{ $order->order_no }}</span>
            @if ($order->customer_email)
                <span class="fd-confirm__ref-note">@lang('A receipt is on its way to') <strong>{{ $order->customer_email }}</strong></span>
            @endif
        </div>

        @if ($order->orderDetail->count())
            <ul class="fd-confirm__items">
                @foreach ($order->orderDetail as $line)
                    <li class="fd-confirm__item">
                        <span class="fd-confirm__qty">{{ $line->quantity }}×</span>
                        <span class="fd-confirm__name">
                            {{ __(optional($line->product)->name ?? __('Item')) }}
                            @if (optionSummary($line->options))
                                <small>{{ optionSummary($line->options) }}</small>
                            @endif
                        </span>
                        <span class="fd-confirm__price">{{ $general->cur_sym }}{{ showAmount($line->price * $line->quantity, 2, false) }}</span>
                    </li>
                @endforeach
            </ul>
        @endif

        <dl class="fd-confirm__sum">
            <div><dt>@lang('Subtotal')</dt><dd>{{ $general->cur_sym }}{{ showAmount($order->subtotal, 2, false) }}</dd></div>
            @if ($order->discount > 0)
                <div><dt>@lang('Discount')</dt><dd>−{{ $general->cur_sym }}{{ showAmount($order->discount, 2, false) }}</dd></div>
            @endif
            <div><dt>{{ $isCollection ? __('Collection') : __('Delivery') }}</dt><dd>{{ $order->shipping_charge > 0 ? $general->cur_sym . showAmount($order->shipping_charge, 2, false) : __('Free') }}</dd></div>
            <div class="fd-confirm__total"><dt>@lang('Total')</dt><dd>{{ $general->cur_sym }}{{ showAmount($order->total, 2, false) }}</dd></div>
        </dl>

        <div class="fd-confirm__meta">
            <div>
                <span class="fd-confirm__meta-label">@lang('Payment')</span>
                <span>
                    @if ($order->payment_type == 2)
                        @lang('Pay on delivery')
                    @elseif ($paidOnline)
                        @lang('Paid online')
                    @elseif ($awaitingTransfer)
                        @lang('Bank transfer, awaiting verification')
                    @else
                        @lang('Being confirmed')
                    @endif
                </span>
            </div>
            <div>
                <span class="fd-confirm__meta-label">{{ $isCollection ? __('Collection') : __('Delivering to') }}</span>
                <span>
                    @if ($isCollection)
                        @lang('Collect from the restaurant')
                    @else
                        {{ collect([@$address->address, @$address->city, @$address->zip])->filter()->implode(', ') }}
                    @endif
                </span>
            </div>
        </div>

        <div class="fd-confirm__actions">
            <a href="{{ route('track-order') }}" class="fd-confirm__btn fd-confirm__btn--primary">@lang('Track your order')</a>
            <a href="{{ route('home') }}" class="fd-confirm__btn">@lang('Back to the menu')</a>
        </div>

        <p class="fd-confirm__account">
            @lang('Want quicker checkout next time?')
            <a href="{{ route('user.register') }}">@lang('Create an account')</a>
        </p>
    </div>
</section>
@endsection

@if ($pending && $checks < 10)
    @push('script')
    <script>
        // The card is charged; the gateway confirms to us a moment later.
        setTimeout(function () {
            window.location.replace("{{ route('order.confirmation') }}?check={{ $checks + 1 }}");
        }, 3000);
    </script>
    @endpush
@endif

@push('style')
<style>
    .fd-confirm { background: var(--fd-bg); padding: 48px 16px 72px; }
    .fd-confirm__card {
        max-width: 560px; margin: 0 auto; background: var(--fd-surface);
        border-radius: var(--fd-radius); box-shadow: var(--fd-shadow); padding: 36px 28px; text-align: center;
    }
    .fd-confirm__badge {
        width: 64px; height: 64px; margin: 0 auto 16px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: #e7f7ee; color: #1f9d55; font-size: 32px;
        animation: fd-confirm-pop .4s cubic-bezier(.2, .9, .3, 1.2) both;
    }
    .fd-confirm__badge--wait { background: #fff4df; color: var(--fd-amber-d); animation: none; }
    @keyframes fd-confirm-pop { from { transform: scale(.6); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    @media (prefers-reduced-motion: reduce) { .fd-confirm__badge { animation: none; } }
    .fd-confirm__title { font-family: var(--fd-font-display); color: var(--fd-ink); font-size: 28px; line-height: 1.15; letter-spacing: -.01em; margin: 0 0 8px; }
    .fd-confirm__lead { color: var(--fd-body); margin: 0 auto 24px; max-width: 420px; }
    .fd-confirm__ref { background: var(--fd-bg); border-radius: var(--fd-radius-sm); padding: 16px; margin-bottom: 24px; display: grid; gap: 4px; }
    .fd-confirm__ref-label { color: var(--fd-muted); font-size: 12px; text-transform: uppercase; letter-spacing: .08em; }
    .fd-confirm__ref-no { font-family: var(--fd-font-display); color: var(--fd-indigo); font-size: 24px; letter-spacing: .04em; user-select: all; }
    .fd-confirm__ref-note { color: var(--fd-body); font-size: 13px; overflow-wrap: anywhere; }
    .fd-confirm__items { list-style: none; padding: 0; margin: 0 0 8px; text-align: left; }
    .fd-confirm__item { display: flex; gap: 12px; align-items: baseline; padding: 10px 0; border-bottom: 1px solid var(--fd-line); }
    .fd-confirm__qty { color: var(--fd-muted); min-width: 28px; font-variant-numeric: tabular-nums; }
    .fd-confirm__name { flex: 1; color: var(--fd-ink); }
    .fd-confirm__name small { display: block; color: var(--fd-muted); }
    .fd-confirm__price { color: var(--fd-ink); font-variant-numeric: tabular-nums; }
    .fd-confirm__sum { margin: 8px 0 24px; text-align: left; }
    .fd-confirm__sum div { display: flex; justify-content: space-between; padding: 4px 0; color: var(--fd-body); }
    .fd-confirm__sum dt { font-weight: 400; }
    .fd-confirm__sum dd { margin: 0; font-variant-numeric: tabular-nums; }
    .fd-confirm__total { border-top: 1px solid var(--fd-line); margin-top: 6px; padding-top: 10px !important; color: var(--fd-ink) !important; font-weight: 600; font-size: 18px; }
    .fd-confirm__meta { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; text-align: left; margin-bottom: 28px; }
    .fd-confirm__meta > div { display: grid; gap: 2px; color: var(--fd-ink); font-size: 14px; }
    .fd-confirm__meta-label { color: var(--fd-muted); font-size: 12px; text-transform: uppercase; letter-spacing: .08em; }
    .fd-confirm__actions { display: grid; gap: 10px; }
    .fd-confirm__btn {
        display: flex; align-items: center; justify-content: center; min-height: 48px; border-radius: 999px;
        border: 1px solid var(--fd-line); color: var(--fd-ink); font-weight: 500; transition: transform .1s ease-out, background .15s;
    }
    .fd-confirm__btn:hover { color: var(--fd-ink); background: var(--fd-bg); }
    .fd-confirm__btn:active { transform: scale(.97); }
    .fd-confirm__btn--primary { background: var(--fd-amber); border-color: var(--fd-amber); color: var(--fd-dark); }
    .fd-confirm__btn--primary:hover { background: var(--fd-amber-d); color: var(--fd-dark); }
    .fd-confirm__account { margin: 20px 0 0; color: var(--fd-muted); font-size: 14px; }
    .fd-confirm__account a { color: var(--fd-indigo); font-weight: 500; }
    @media (max-width: 480px) {
        .fd-confirm { padding-top: 24px; }
        .fd-confirm__card { padding: 28px 20px; }
        .fd-confirm__meta { grid-template-columns: 1fr; }
    }
</style>
@endpush
