@extends($activeTemplate.'layouts.master')
@section('content')

<div class="ticket__wrapper">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h6 class="me-2">
            @if ($my_ticket->status == 0)
            <span class="badge badge--success">@lang('Open')</span>
            @elseif($my_ticket->status == 1)
            <span class="badge badge--primary">@lang('Answered')</span>
            @elseif($my_ticket->status == 2)
            <span class="badge badge--warning">@lang('Replied')</span>
            @elseif($my_ticket->status == 3)
            <span class="badge badge--dark">@lang('Closed')</span>
            @endif
            <span>[@lang('Ticket')#{{ $my_ticket->ticket }}] </span>
            <span> {{ $my_ticket->subject }}</span>
        </h6>
        <a href="#0" class="btn btn--danger mb-2 btn--sm" title="@lang('Close Ticket')" data-bs-toggle="modal"
            data-bs-target="#DelModal">
            <i class="las la-times"></i>
        </a>
    </div>
    <div class="message__chatbox__body">
        @if ($my_ticket->status != 4)
        <form method="post" action="{{ route('ticket.reply', $my_ticket->id) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="replayTicket" value="1">
            <div class="form--group col-sm-12">
                <label for="message" class="form--label">@lang('Your Message')</label>
                <textarea id="message" name="message" id="inputMessage" class="form-control form--control">{{ old('message') }}</textarea>
            </div>
            <div class="form--group col-sm-12">
                <div class="d-flex">
                    <div class="left-group col p-0">
                        <label for="file2" class="form--label">@lang('Attachments')</label>
                        <input type="file" class="overflow-hidden form-control form--control mb-2" name="attachments[]"
                            id="inputAttachments">
                    </div>
                    <div class="add-area">
                        <label class="form--label d-block">&nbsp;</label>
                        <button type="button" class="btn btn--base btn--sm bg--base ms-2 ms-md-4 form--control addFile"><i class="las la-plus"></i></button>
                    </div>
                </div>
            </div>
            <div id="fileUploadsContainer"></div>
            <div class="mb-3">
                <span class="info fs--14">
                    @lang('Allowed File Extensions'): .@lang('jpg'), .@lang('jpeg'), .@lang('png'), .@lang('pdf'), .@lang('doc'), .@lang('docx')
                </span>
            </div>
            <div class="form--group col-sm-12 mb-0">
                <button type="submit" class="cmn--btn w-100">@lang(' Reply')</button>
            </div>
        </form>
        @endif
    </div>
</div>

<div class="ticket__wrapper mt-5">
    <div class="message__chatbox__body">
        <ul class="reply-message-area">
            @forelse($messages as $message)
            @if ($message->admin_id == 0)
            <li>
                <div class="reply-item">
                    <div class="name-area">
                        <div class="reply-thumb">
                            <img src="{{ getImage(imagePath()['profile']['user']['path'] . '/' . auth()->user()->image, imagePath()['profile']['user']['size']) }}"
                                alt="user">
                        </div>
                        <h6 class="title">{{ __($message->ticket->name) }}</h6>
                    </div>
                    <div class="content-area">
                        <span class="meta-date">
                            @lang('Posted on') <span class="cl-theme">{{ $message->created_at->format('l, dS F Y @ H:i')}}</span>
                        </span>
                        <p> {{ $message->message }}</p>
                        @if ($message->attachments()->count() > 0)
                        <div class="mt-2">
                            @foreach ($message->attachments as $k => $image)
                            <a href="{{ route('ticket.download', encrypt($image->id)) }}" class="mr-3 text--base">
                                <i class="las la-file"></i>@lang('Attachment') {{ ++$k }}
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </li>
            @else
            <li>
                <div class="reply-item">
                    <div class="name-area">
                        <div class="reply-thumb">
                            <img src="{{ getImage(imagePath()['profile']['admin']['path'] . '/' . $message->admin->image, imagePath()['profile']['admin']['size']) }}"
                                alt="user">
                        </div>
                        <h6 class="title">{{ $message->admin->name }}</h6>
                    </div>
                    <div class="content-area">
                        <span class="meta-date">
                            @lang('Posted on') <span class="cl-theme">{{ $message->created_at->format('l, dS F Y @ H:i')}}</span>
                        </span>
                        <p>
                            {{ $message->message }}
                        </p>
                        @if ($message->attachments()->count() > 0)
                        <div class="mt-2">
                            @foreach ($message->attachments as $k => $image)
                            <a href="{{ route('ticket.download', encrypt($image->id)) }}" class="mr-3"><i
                                    class="las la-file"></i>
                                @lang('Attachment') {{ ++$k }} </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </li>
            @endif
            @empty
            <li>{{ __($emptyMessage) }}</li>
            @endforelse
        </ul>
    </div>

</div>
<div class="modal fade" id="DelModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" action="{{ route('ticket.reply', $my_ticket->id) }}">
                @csrf
                <input type="hidden" name="replayTicket" value="2">
                <div class="modal-header bg--base">
                    <h5 class="modal-title"> @lang('Confirmation!')</h5>
                    <button type="button" class="btn-close text-white" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <strong class="text--dark">@lang('Are you sure to close this support ticket?')</strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--danger btn-sm" data-bs-dismiss="modal">@lang('No')</button>
                    <button type="submit" class="btn btn--base btn-sm">@lang("Yes")</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($) {
            "use strict";
            $('.delete-message').on('click', function(e) {
                $('.message_id').val($(this).data('id'));
            });
            $('.addFile').on('click', function() {
                $("#fileUploadsContainer").append(
                    `<div class="form--group col-sm-12">
                        <div class="input-group">
                            <div class="left-group col p-0">
                                <input type="file" name="attachments[]" class="form-control form--control mb-2" required />
                            </div>
                            <div class="add-area">
                                <button class="btn btn--danger btn--sm bg--danger ms-2 ms-md-4 form--control remove-btn" type="button">
                                    <i class="las la-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>`
                )
            });
            $(document).on('click', '.remove-btn', function() {
                $(this).closest('.input-group').remove();
            });
        })(jQuery);
</script>
@endpush
