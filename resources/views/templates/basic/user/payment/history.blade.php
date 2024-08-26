@extends($activeTemplate.'layouts.master')
@section('content')
<div class="col-xxl-12 col-lg-12">
    <div class="dashboard-wrapper">
        <table class="table cmn--table">
            <thead>
                <tr>
                    <th scope="col">@lang('Trx No')</th>
                    <th scope="col">@lang('Detail')</th>
                    <th scope="col">@lang('Amount')</th>
                    <th scope="col">@lang('Created_At')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                <tr>
                    <td data-label="@lang('Trx No')">{{ $payment->trx }}</td>
                    <td data-label="@lang('Detail')">
                        <span>{{__($payment->details)}}</span>
                    </td>
                    <td data-label="@lang('Amount')" class="text--base">
                        <strong>{{ showAmount($payment->amount) }} {{$general->cur_text }}</strong>
                    </td>
                    <td data-label="@lang('Created_At')">
                        <strong>{{ showDateTime($payment->created_at) }}  </strong>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="100%" class="text--danger justify-content-center text-center">{{ __($emptyMessage) }}</td>
                </tr>
                @endforelse
            </tbody>
           
        </table>
        {{ $payments->links() }}
    </div>
</div>

@endsection
