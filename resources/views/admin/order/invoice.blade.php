<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@lang('Invoice')</title>
    <!-- favicon -->
    <link rel="icon" href="{{ getImage(imagePath()['logoIcon']['path'] . '/favicon.png', '?' . time()) }}" sizes="16x16" type="image/png" />
</head>
<style>
    @page {
        size: 8.27in 11.7in;
        margin: .5in;
    }

    body {
        font-family: "Arial", sans-serif;
        font-size: 14px;
        line-height: 1.5;
        color: #023047;
    }

    /* Typography */
    .strong {
        font-weight: 700;
    }

    .fw-md {
        font-weight: 500;
    }

    .primary-text {
        color: #219ebc;
    }

    h1,
    .h1 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 67px;
        line-height: 1.2;
        font-weight: 500;
    }

    h2,
    .h2 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 50px;
        line-height: 1.2;
        font-weight: 500;
    }

    h3,
    .h3 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 38px;
        line-height: 1.2;
        font-weight: 500;
    }

    h4,
    .h4 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 28px;
        line-height: 1.2;
        font-weight: 500;
    }

    h5,
    .h5 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 20px;
        line-height: 1.2;
        font-weight: 500;
    }

    h6,
    .h6 {
        font-family: "Arial", sans-serif;
        margin-top: 8px;
        margin-bottom: 8px;
        font-size: 16px;
        line-height: 1.2;
        font-weight: 500;
    }

    .text-uppercase {
        text-transform: uppercase;
    }

    .text-end {
        text-align: right;
    }

    .text-center {
        text-align: center;
    }

    /* List Style */
    ul {
        list-style: none;
        margin: 0;
        padding: 0;
    }

    /* Utilities */
    .d-block {
        display: block;
    }

    .mt-0 {
        margin-top: 0;
    }

    .m-0 {
        margin: 0;
    }

    .mt-3 {
        margin-top: 16px;
    }

    .mt-4 {
        margin-top: 24px;
    }

    .mb-3 {
        margin-bottom: 16px;
    }

    /* Title */
    .title {
        display: inline-block;
        letter-spacing: 0.05em;
    }

    /* Table Style */
    table {
        width: 7.27in;
        caption-side: bottom;
        border-collapse: collapse;
        border: 1px solid #ffffff;
        color: #000000;
        vertical-align: top;
    }

    table td {
        padding: 5px 15px;
    }

    table th {
        padding: 5px 15px;
    }
    table, td, th {  
        border: 1px solid #ddd;
    }
    table th:last-child {
        text-align: right !important;
    }

    .table> :not(caption)>*>* {
        padding: 12px 24px;
        background-color: #ffffff;
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px #ffffff;
    }

    .table>tbody {
        vertical-align: inherit;
        border: 1px solid #eafbff;
    }

    .table>thead {
        vertical-align: bottom;
        background: #219ebc;
        color: #000;
    }

    .table>thead th {
        font-family: "Arial", sans-serif;
        text-align: left;
        font-size: 16px;
        letter-spacing: 0.03em;
        font-weight: 500;
    }

    .table td:last-child {
        text-align: right;
    }

    .table th:last-child {
        text-align: right;
    }

    .table> :not(:first-child) {
        border-top: 0;
    }

    .table-sm> :not(caption)>*>* {
        padding: 5px;
    }

    .table-bordered> :not(caption)>* {
        border-width: 1px 0;
    }

    .table-bordered> :not(caption)>*>* {
        border-width: 0 1px;
    }

    .table-borderless> :not(caption)>*>* {
        border-bottom-width: 0;
    }

    .table-borderless> :not(:first-child) {
        border-top-width: 0;
    }

    .table-striped>tbody>tr:nth-of-type(even)>* {
        background: #eafbff;
    }


    /* Logo */
    .logo {
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 200px;
        height: 50px;
        font-size: 24px;
        text-transform: capitalize;
    }

    .logo-img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .info {
        justify-content: space-between;
        padding-top: 15px;
        padding-bottom: 15px;
        border-top: 1px solid #023047;
        border-bottom: 1px solid #023047;
    }

    .address {
        padding-top: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #023047;
    }

    header {
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .body {
        padding-top: 30px;
        padding-bottom: 30px;
    }

    footer {
        padding-bottom: 15px;
    }

    .badge {
        display: inline-block;
        padding: 2px 15px;
        font-size: 10px;
        line-height: 1;
        border-radius: 15px;
    }

    .badge--success {
        color: white;
        background: #02c39a;
    }

    .badge--warning {
        color: white;
        background: #ffb703;
    }

    .align-items-center {
        align-items: center;
    }

    .footer-link {
        text-decoration: none;
        color: #219ebc;
    }

    .footer-link:hover {
        text-decoration: none;
        color: #219ebc;
    }

    .list--row {
        overflow: auto
    }

    .list--row::after {
        content: '';
        display: block;
        clear: both;
    }

    .float-left {
        float: left;
    }

    .float-right {
        float: right;
    }

    .d-block {
        display: block;
    }

    .d-inline-block {
        display: inline-block;
    }
</style>

<body onload="window.print()">
    <header>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="list--row">
                        <div class="logo float-left">
                            <img src="{{ getImage('assets/images/logoIcon/logo.png', '183x54') }}" alt="image" class="logo-img" />
                        </div>
                        <h4 class="m-0 float-right">@lang('Invoice')</h4>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <main>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="info list--row">
                        <div class="info-left float-left">
                            <div class="list list--row">
                                <span class="strong">@lang('Order Date'):</span>
                                <span>  {{showDateTime($order->created_at, 'd/m/Y')}} </span>
                            </div>
                        </div>
                        <div class="info-right float-right">
                            <div class="list list--row text-right">
                                <span class="strong">@lang('Order No'):</span>
                                <span> {{ __($order->order_no) }} </span>
                            </div>
                        </div>
                    </div>
                    <div class="address list--row">
                        <div class="address-to float-left">
                            <h5 class="text-uppercase">@lang('Invoice To')</h5>
                            <ul class="list" style="--gap: 0.3rem">
                                @php
                                    $address = json_decode($order->address);
                                @endphp
                                <li>
                                    <div class="list list--row" style="--gap: 0.5rem">
                                        <span class="strong">@lang('Name'):</span>
                                        <span>{{ __($order->user->fullname) }}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="list list--row" style="--gap: 0.5rem">
                                        <span class="strong">@lang('Phone') :</span>
                                        <span>{{ __($order->user->mobile) }}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="list list--row" style="--gap: 0.5rem">
                                        <span class="strong">@lang('Address :')</span>
                                        <span>{{ __($address->address) }}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="list list--row" style="--gap: 0.5rem">
                                        <span class="strong">@lang('Country :')</span>
                                        <span>{{ __($address->country) }}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="list list--row" style="--gap: 0.5rem">
                                        <span class="strong">@lang('State') :</span>
                                        <span>{{ __($address->state) }}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="list list--row" style="--gap: 0.5rem">
                                        <span class="strong">@lang('City') :</span>
                                        <span>{{ __($address->city) }}</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="list list--row" style="--gap: 0.5rem">
                                        <span class="strong">@lang('Zip') :</span>
                                        <span>{{ __($address->zip) }}</span>
                                    </div>
                                </li>
                                
                            </ul>
                        </div>
                        <div class="address-form float-right">
                            <ul class="text-end">
                                <li>
                                    <h5 class="text-uppercase">@lang('Invoice To')</h5>
                                </li>
                                <li>
                                    <span class="d-inline-block strong">@lang('Total Amount') :</span>
                                    <span class="d-inline-block ">{{ showAmount($order->total) }} {{ $general->cur_text }}</span>
                                </li>
                                <li>
                                    <span class="d-inline-block strong">@lang('Payment Type') :</span>
                                    <span class="d-inline-block">
                                        @if ($order->payment_type == 1)
                                        <span class="d-inline-block">@lang('Online payment gateway')</span>
                                        @else
                                        <span class="d-inline-block">@lang('Cash on delivery')</span>
                                        @endif </span>
                                </li>
                                <li>
                                    <span class="d-inline-block strong">@lang('Shipping Area')</span>
                                    <span class="d-inline-block">{{ __(@$order->shipping->name) }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="body">
                        <div class="text-center mt-4 mb-3">
                            <div class="title-inset">
                                <h6 class="title m-0 text-uppercase">@lang('Order Details')</h6>
                            </div>
                        </div>
                        <table class="table table-striped">
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
                                        <td>
                                            <span>{{ __(@$detail->product->name) }}</span>
                                        </td>
                                        <td>
                                            <span>{{ $detail->quantity }}</span>
                                        </td>

                                        <td>
                                            <span>{{ showAmount($detail->price) }} {{ $general->cur_text }}</span>
                                        </td>

                                        <td>
                                            <span>{{ showAmount($detail->price * $detail->quantity) }} {{ $general->cur_text }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                                    </tr>
                                    @endforelse
                                <tr>
                                    <td colspan="3" class="text-end">@lang('Subtotal')</td>
                                    <td>{{ showAmount($order->subtotal) }} {{ $general->cur_text }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">@lang('Shipping Charge')</td>
                                    <td>{{ showAmount($order->shipping_charge) }} {{ $general->cur_text }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">@lang('Discount')</td>
                                    <td>{{ showAmount($order->discount) }} {{ $general->cur_text }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end">@lang('Total Amount')</td>
                                    <td><span>{{ showAmount($order->total) }} {{ $general->cur_text }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <span class="d-block text-center">
                        @lang('Copyright') &copy; @php date('Y') @endphp @lang('All Right Reserved By')
                        <a href="#" class="footer-link">{{ $general->sitename }}</a>
                    </span>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>