@extends($activeTemplate.'layouts.master')
@section('content')
<div class="row justify-content-center g-4">
    @foreach($gatewayCurrency as $data)
    <div class="col-sm-6 col-md-4 col-lg-6 col-xl-4 co-xxl-3">
        <div class="card card cmn--card bg--body">
            <div class="card-header text-center bg--base">
                <h6 class="card-title">{{__($data->name)}}</h6>
            </div>
            <div class="card-body">
                <img src="{{$data->methodImage()}}" alt="{{__($data->name)}}">
            </div>
            <div class="card-footer text-center">
                <a href="javascript:void(0)" data-id="{{$data->id}}" data-name="{{$data->name}}"
                    data-currency="{{$data->currency}}" data-method_code="{{$data->method_code}}"
                    data-min_amount="{{showAmount($data->min_amount)}}"
                    data-max_amount="{{showAmount($data->max_amount)}}" data-base_symbol="{{$data->baseSymbol()}}"
                    data-fix_charge="{{showAmount($data->fixed_charge)}}"
                    data-percent_charge="{{showAmount($data->percent_charge)}}" data-price="{{ $order->total }}"
                    data-bs-toggle="modal" data-bs-target="#depositModal"
                    class="cmn--btn custom-success deposit">@lang('Payment Now')</a>
            </div>
        </div>
    </div>
    @endforeach
</div>


<div class="modal fade" id="depositModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg--base">
                <strong class="modal-title method-name" id="depositModalLabel"></strong>
                <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form action="{{route('user.deposit.insert')}}" method="post">
                @csrf
                <div class="modal-body">
                    <p class="text-danger depositLimit"></p>
                    <p class="text-danger depositCharge"></p>
                    <div class="form-group">
                        <input type="hidden" name="currency" class="edit-currency">
                        <input type="hidden" name="method_code" class="edit-method-code">
                    </div>
                    <div class="form-group">
                        <label class="text-dark">@lang('Order Amount'):</label>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-lg price" name="amount"
                                placeholder="@lang('Amount')" required value="{{old('amount')}}" readonly>
                            <div class="input-group-append">
                                <span class="input-group-text h-100">{{__($general->cur_text)}}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--base">@lang('Confirm')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function ($) {
            "use strict";
            $('.deposit').on('click', function () {
                var name = $(this).data('name');
                var currency = $(this).data('currency');
                var method_code = $(this).data('method_code');
                var minAmount = $(this).data('min_amount');
                var maxAmount = $(this).data('max_amount');
                var baseSymbol = "{{$general->cur_text}}";
                var fixCharge = $(this).data('fix_charge');
                var percentCharge = $(this).data('percent_charge');
                var price = $(this).data('price');

                var depositLimit = `@lang('Payment Limit'): ${minAmount} - ${maxAmount}  ${baseSymbol}`;
                $('.depositLimit').text(depositLimit);
                var depositCharge = `@lang('Charge'): ${fixCharge} ${baseSymbol}  ${(0 < percentCharge) ? ' + ' +percentCharge + ' % ' : ''}`;

                $('.depositCharge').text(depositCharge);
                $('.method-name').text(`@lang('Payment By ') ${name}`);
                $('.currency-addon').text(baseSymbol);
                $('.edit-currency').val(currency);
                $('.edit-method-code').val(method_code);
                $('.price').val(parseFloat(price).toFixed(2));
            });
        })(jQuery);
</script>
@endpush


@push('style')
<style type="text/css">

</style>
@endpush
