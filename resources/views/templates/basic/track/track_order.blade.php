@extends($activeTemplate.'layouts.frontend')
@section('content')
<div class="order-tracking pt-60 pb-60">
    <div class="container">
        <h6 class="text-center mb-4">@lang('Track Your Order')</h6>
        <div class="search-tracking">
            <form class="track-search">
                <div class="input-group">
                    <input type="text" class="form-control form--control" name="order_no" placeholder="@lang('Enter your order number here.')" autocomplete="off">
                    <button type="submit" class="btn btn--base btn--round track-btn">@lang('Track Order')</button>
                </div>
            </form>
        </div>
        <div id="show_track"></div>
    </div>
</div>
@endsection