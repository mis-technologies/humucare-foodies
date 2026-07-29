@extends('admin.layouts.app')
@section('panel')

<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info" role="alert">
            <i class="las la-info-circle"></i>
            @lang('Customers use the "Request a special offer" form on the home page to ask for things the menu does not sell by the plate — a litre of soup, a tray of jollof, catering for a party. Nothing is charged here: read the request, send a price, and the customer confirms.')
        </div>
    </div>

    <div class="col-lg-12">
        <div class="card b-radius--10">
            <div class="card-body p-0">
                <div class="table-responsive--md table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('Reference')</th>
                                <th>@lang('Customer')</th>
                                <th>@lang('Request')</th>
                                <th>@lang('Needed')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($requests as $req)
                            <tr>
                                <td data-label="@lang('Reference')">
                                    <strong>{{ $req->request_no }}</strong>
                                    <br>
                                    <small class="text-muted">{{ showDateTime($req->created_at, 'd M Y h:i A') }}</small>
                                </td>

                                <td data-label="@lang('Customer')">
                                    {{ __($req->name) }}
                                    <br>
                                    <small class="text-muted">
                                        <a href="mailto:{{ $req->email }}">{{ $req->email }}</a><br>
                                        <a href="tel:{{ $req->phone }}">{{ $req->phone }}</a>
                                    </small>
                                </td>

                                <td data-label="@lang('Request')">
                                    <strong>{{ $req->quantity }}</strong> @lang('of') {{ __($req->item) }}
                                    <br>
                                    <span class="badge badge--dark">{{ __($req->type_name) }}</span>
                                    @if ($req->people)
                                        <span class="badge badge--secondary">{{ $req->people }} @lang('guests')</span>
                                    @endif
                                    @if ($req->budget)
                                        <span class="badge badge--secondary">
                                            @lang('Budget') {{ $general->cur_sym }}{{ showAmount($req->budget) }}
                                        </span>
                                    @endif
                                </td>

                                <td data-label="@lang('Needed')">
                                    {{ $req->needed_on ? $req->needed_on->format('d M Y') : __('Not specified') }}
                                </td>

                                <td data-label="@lang('Status')">
                                    <span class="badge badge--{{ $req->status_class }}">{{ __($req->status_name) }}</span>
                                    @if ($req->replied_at)
                                        <br><small class="text-muted">
                                            @lang('Quoted') {{ showDateTime($req->replied_at, 'd M Y') }}
                                        </small>
                                    @endif
                                </td>

                                <td data-label="@lang('Action')">
                                    <button class="btn btn-sm btn--primary viewBtn"
                                        data-request="{{ json_encode([
                                            'id'        => $req->id,
                                            'no'        => $req->request_no,
                                            'name'      => $req->name,
                                            'email'     => $req->email,
                                            'phone'     => $req->phone,
                                            'type'      => $req->type_name,
                                            'item'      => $req->item,
                                            'quantity'  => $req->quantity,
                                            'needed_on' => $req->needed_on ? $req->needed_on->format('d M Y') : null,
                                            'people'    => $req->people,
                                            'budget'    => $req->budget ? $general->cur_sym . showAmount($req->budget) : null,
                                            'details'   => $req->details,
                                            'reply'     => $req->admin_reply,
                                            'status'    => $req->status,
                                        ]) }}">
                                        <i class="las la-desktop"></i> @lang('View &amp; reply')
                                    </button>
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

            @if ($requests->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($requests) }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- One modal reused for every row; the View button injects that row's data. --}}
<div class="modal fade" id="requestModal">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Request') <span class="req-no"></span></h5>
                <button type="button" class="close" data-bs-dismiss="modal"><i class="las la-times"></i></button>
            </div>
            <div class="modal-body">
                <ul class="list-group mb-4">
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Customer')</span> <span class="fw-bold req-name"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Email')</span> <span class="fw-bold req-email"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Phone')</span> <span class="fw-bold req-phone"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Wants')</span> <span class="fw-bold req-item"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Kind of request')</span> <span class="fw-bold req-type"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Needed on')</span> <span class="fw-bold req-date"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Guests')</span> <span class="fw-bold req-people"></span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between">
                        <span>@lang('Budget')</span> <span class="fw-bold req-budget"></span>
                    </li>
                    <li class="list-group-item">
                        <span class="d-block mb-1">@lang('Details')</span>
                        <span class="fw-bold req-details"></span>
                    </li>
                </ul>

                <form action="" method="POST" class="replyForm mb-3">
                    @csrf
                    <div class="form-group">
                        <label>@lang('Send a quote to the customer')</label>
                        <textarea name="reply" rows="4" class="form-control req-reply"
                            placeholder="@lang('e.g. 5 litres of egusi is £60, ready for Saturday 2pm. Delivery £8.')" required></textarea>
                        <small class="text-muted">
                            @lang('This is emailed to the customer and marks the request as Quoted.')
                        </small>
                    </div>
                    <button type="submit" class="btn btn--primary">
                        <i class="las la-paper-plane"></i> @lang('Send quote')
                    </button>
                </form>

                <div class="d-flex flex-wrap align-items-center" style="gap:8px">
                    <form action="" method="POST" class="statusForm d-flex align-items-center" style="gap:8px">
                        @csrf
                        <select name="status" class="form-control req-status">
                            @foreach ($statuses as $value => $meta)
                                <option value="{{ $value }}">{{ __($meta[0]) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn--success">@lang('Update status')</button>
                    </form>

                    <form action="" method="POST" class="deleteForm ml-auto"
                        onsubmit="return confirm('@lang('Delete this request permanently?')')">
                        @csrf
                        <button type="submit" class="btn btn--danger">
                            <i class="las la-trash"></i> @lang('Delete')
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
<form action="" method="GET" class="form-inline float-sm-right bg--white">
    <div class="input-group has_append">
        <input type="text" name="search" class="form-control" placeholder="@lang('Reference / name / phone')"
            value="{{ request()->search }}">
        <div class="input-group-append">
            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
@endpush

@push('script')
<script>
    'use strict';
    (function ($) {
        // Route templates generated once with a placeholder id, then re-pointed
        // per row. Keeps route() out of the loop and the markup small.
        var replyUrl  = "{{ route('admin.special.request.reply', 0) }}";
        var statusUrl = "{{ route('admin.special.request.status', 0) }}";
        var deleteUrl = "{{ route('admin.special.request.delete', 0) }}";

        // swap the trailing id segment rather than slicing a fixed number of
        // characters, so this survives any change to the route prefix
        function urlFor(template, id) {
            return template.slice(0, template.lastIndexOf('/') + 1) + id;
        }

        $('.viewBtn').on('click', function () {
            var d = $(this).data('request');
            var $m = $('#requestModal');

            $m.find('.req-no').text(d.no);
            $m.find('.req-name').text(d.name);
            $m.find('.req-email').text(d.email);
            $m.find('.req-phone').text(d.phone);
            $m.find('.req-item').text(d.quantity + ' of ' + d.item);
            $m.find('.req-type').text(d.type);
            $m.find('.req-date').text(d.needed_on || '@lang('Not specified')');
            $m.find('.req-people').text(d.people || '@lang('Not specified')');
            $m.find('.req-budget').text(d.budget || '@lang('Not specified')');
            $m.find('.req-details').text(d.details || '@lang('None')');
            $m.find('.req-reply').val(d.reply || '');
            $m.find('.req-status').val(d.status);

            $m.find('.replyForm').attr('action', urlFor(replyUrl, d.id));
            $m.find('.statusForm').attr('action', urlFor(statusUrl, d.id));
            $m.find('.deleteForm').attr('action', urlFor(deleteUrl, d.id));

            $m.modal('show');
        });
    })(jQuery);
</script>
@endpush
