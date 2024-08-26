@extends($activeTemplate.'layouts.master')
@section('content')
<div class="d-block text-end mb-2">
    <a href="{{ route('ticket.open') }}" class="btn btn--base fs--14px">@lang('Open New Ticket')</a>
</div>
<table class="table cmn--table">
    <thead>
        <tr>
            <th scope="col">@lang('Subject')</th>
            <th scope="col">@lang('Status')</th>
            <th scope="col">@lang('Priority')</th>
            <th scope="col">@lang('Last Reply')</th>
            <th scope="col">@lang('Action')</th>
        </tr>
    </thead>
    <tbody>
        @forelse($supports as $key => $support)
        <tr>
            <td data-label="@lang('Subject')">
                <a href="{{ route('ticket.view', $support->ticket) }}" class="text--base"> [@lang('Ticket')#{{ __($support->ticket) }}]
                    {{ __($support->subject) }} 
                </a>
            </td>
            <td data-label="@lang('Status')">
                @if ($support->status == 0)
                <span class="badge badge--success">@lang('Open')</span>
                @elseif($support->status == 1)
                <span class="badge badge--primary">@lang('Answered')</span>
                @elseif($support->status == 2)
                <span class="badge badge--warning">@lang('Customer Reply')</span>
                @elseif($support->status == 3)
                <span class="badge badge--dark">@lang('Closed')</span>
                @endif
            </td>
            <td data-label="@lang('Priority')">
                @if ($support->priority == 1)
                <span class="badge badge--dark">@lang('Low')</span>
                @elseif($support->priority == 2)
                <span class="badge badge--success">@lang('Medium')</span>
                @elseif($support->priority == 3)
                <span class="badge badge--primary">@lang('High')</span>
                @endif
            </td>
            <td data-label="@lang('Last Reply')">
                <div>
                    {{ \Carbon\Carbon::parse($support->last_reply)->diffForHumans() }}
                </div>
            </td>
            <td data-label="@lang('Action')">
                <div>
                    <a href="{{ route('ticket.view', $support->ticket) }}" class="btn btn-sm btn--base">
                        <i class="las la-desktop"></i>
                    </a>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="100%" class="justify-content-center text-center text--danger">{{ __($emptyMessage) }}</td>
        </tr>
        @endforelse
    </tbody>
</table>
{{ $supports->links() }}
@endsection
