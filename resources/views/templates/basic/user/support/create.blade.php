@extends($activeTemplate.'layouts.master')
@section('content')
<div class="ticket__wrapper">
    <div class="d-flex flex-wrap justify-content-between align-items-center">
        <h6 class="ticket__wrapper-title mb-4 me-3">@lang('Create Ticket')</h6>
        <a href="{{ route('ticket') }}" class="cmn--btn mb-4">@lang('My Tickets')</a>
    </div>
    <div class="message__chatbox__body">
        <form class="message__chatbox__form row" action="{{ route('ticket.store') }}" method="post"
            enctype="multipart/form-data" onsubmit="return submitUserForm();">
            @csrf
            <div class="form--group col-sm-6">
                <label for="fname" class="form--label">@lang('Your Name')</label>
                <input type="text" id="fname" name="name" class="form-control form--control"
                    value="{{ @$user->firstname . ' ' . @$user->lastname }}" readonly>
            </div>
            <div class="form--group col-sm-6">
                <label for="username" class="form--label">@lang('Your Username')</label>
                <input type="text" id="username" name="email" class="form-control form--control"
                    value="{{ @$user->email }}" readonly>
            </div>
            <div class="form--group col-sm-12">
                <label for="subject" class="form--label">@lang('Your Subject')</label>
                <input type="text" id="subject" class="form-control form--control" name="subject"
                    value="{{ old('subject') }}">
            </div>
            <div class="form--group col-sm-12">
                <label for="subject" class="form--label">@lang('Priority')</label>
                <select name="priority" class="form-control form--control">
                    <option value="3">@lang('High')</option>
                    <option value="2">@lang('Medium')</option>
                    <option value="1">@lang('Low')</option>
                </select>
            </div>
            <div class="form--group col-sm-12">
                <label for="message" class="form--label">@lang('Message')</label>
                <textarea name="message" id="message" class="form-control form--control">{{ old('message') }}</textarea>
            </div>
            <div class="form--group col-sm-12">
                <div class="d-flex">
                    <div class="left-group col p-0">
                        <label for="file2" class="form--label">@lang('Attachments')</label>
                        <input type="file" name="attachments[]" class="overflow-hidden form-control form--control" id="inputAttachments">
                    </div>
                    <div class="add-area">
                        <label class="form--label d-block">&nbsp;</label>
                        <button class="btn btn--base btn--sm bg--base ms-2 ms-md-4 form--control addFile" type="button">
                            <i class="las la-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="fileUploadsContainer"></div>
            <div class="mb-3">
                <span class="info fs--14">
                    @lang('Allowed File Extensions'):.@lang('jpg'),.@lang('jpeg'), .@lang('png'), .@lang('pdf'), .@lang('doc'), .@lang('docx')
                </span>
            </div>

            <div class="form--group col-sm-12 mb-0">
                <button type="submit" class="cmn--btn w-100">@lang('Submit')</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script>
    (function($) {
            "use strict";
            $('.addFile').on('click', function() {
                $("#fileUploadsContainer").append(`
                <div class="form--group col-sm-12">
                    <div class="input-group">
                        <div class="left-group col p-0">
                            <input type="file" name="attachments[]" class="form-control form--control"/>
                        </div>
                        <div class="add-area">
                            <button class="btn btn--danger btn--sm bg--danger ms-2 ms-md-4 form--control remove-btn" type="button">
                                <i class="las la-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
                `)
            });
            $(document).on('click', '.remove-btn', function() {
                $(this).closest('.input-group').remove();
            });
        })(jQuery);
</script>
@endpush