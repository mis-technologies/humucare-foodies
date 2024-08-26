@extends($activeTemplate.'layouts.frontend')
@section('content')
@include($activeTemplate.'partials.user_dashboard')
                <div class="col-md-10">
                    <div class="row justify-content-center g-4">
                        @foreach($gatewayCurrency as $data)
                        <div class="col-sm-6 col-md-4 col-xl-3 dashboard--item-width">
                            <div class="card card cmn--card bg--body">
                                <div class="card-header text-center">
                                    <h6 class="card-title">{{__($data->name)}}</h6>
                                </div>
                                <div class="card-body">
                                    <img src="{{$data->methodImage()}}" alt="{{__($data->name)}}">
                                </div>
                                <div class="card-footer text-center">
                                    <a href="{{route('user.payment.preview') }}" data-id="{{$data->id}}"
                                    data-name="{{$data->name}}"
                                    data-currency="{{$data->currency}}"
                                    data-method_code="{{$data->method_code}}"
                                    data-min_amount="{{showAmount($data->min_amount)}}"
                                    data-max_amount="{{showAmount($data->max_amount)}}"
                                    data-base_symbol="{{$data->baseSymbol()}}"
                                    data-fix_charge="{{showAmount($data->fixed_charge)}}"
                                    data-percent_charge="{{showAmount($data->percent_charge)}}" data-bs-toggle="modal" data-bs-target="#depositModal" class="cmn--btn custom-success deposit">@lang('Pay Now')</a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

    <div class="modal fade" id="depositModal"  tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <strong class="modal-title method-name" id="depositModalLabel"></strong>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('user.deposit.insert') }}" method="post">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <input type="hidden" name="currency" class="edit-currency">
                            <input type="hidden" name="method_code" class="edit-method-code">
                            <input type="hidden" name="fixed_charge" class="edit-fixed_charge">
                            <input type="hidden" name="percent_charge" class="edit-percent_charge">
                        </div>
                        <div class="form-group">
                            <label class="text-dark">@lang('Payment Amount'):</label>
                            <div class="input-group">
                                <input type="text" class="form-control form-control-lg" name="amount" placeholder="@lang('Amount')" required  value="{{ getAmount($orderAmount->total) }}" readonly>
                                <div class="input-group-append">
                                    <span class="input-group-text h-100">{{__($general->cur_text)}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="cmn--btn--close" data-bs-dismiss="modal">@lang('Close')</button>
                        <div class="prevent-double-click">
                            <button type="submit" class="cmn--btn confirm-btn">@lang('Confirm')</button>
                        </div>
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

                var depositLimit = `@lang('Deposit Limit'): ${minAmount} - ${maxAmount}  ${baseSymbol}`;
                $('.depositLimit').text(depositLimit);
                var depositCharge = `@lang('Charge'): ${fixCharge} ${baseSymbol}  ${(0 < percentCharge) ? ' + ' +percentCharge + ' % ' : ''}`;
                $('.depositCharge').text(depositCharge);
                $('.method-name').text(`@lang('Payment By ') ${name}`);
                $('.currency-addon').text(baseSymbol);
                $('.edit-currency').val(currency);
                $('.edit-method-code').val(method_code);
                $('.edit-fixed_charge').val(fixCharge);
                $('.edit-percent_charge').val(percentCharge);
            });

            // $('.prevent-double-click').on('click',function(){
            //     $(this).addClass('button-none');
            //     $(this).html('<i class="fas fa-spinner fa-spin"></i> @lang('Processing')...');
            // });
        })(jQuery);
    </script>
@endpush


@push('style')
<style type="text/css">

</style>
@endpush
