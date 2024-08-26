@extends($activeTemplate.'layouts.master')
@section('content')
<div class="row g-4">
    <div class="col-xl-6">
        @if (Auth::user()->ts)
        <div class="card cmn--card h-100">
            <div class="card-header bg--base">
                <h4 class="card-title">@lang('2FA Security')</h4>
            </div>
            <div class="card-body">
                <div class="two-factor-content">
                    <div class="text-center">
                        <a href="#0" class="cmn--btn w-100" data-bs-toggle="modal"
                            data-bs-target="#disableModal">@lang('Disable 2FA Security')</a>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="card cmn--card h-100">
            <div class="card-header bg--base">
                <h4 class="card-title">@lang('2FA Security')</h4>
            </div>
            <div class="card-body">
                <div class="two-factor-content">
                    <div class="input-group mb-4">
                        <input type="text" name="key" class="form-control form--control" value="{{ $secret }}" readonly id="referral-link">
                        <button class="cmn--btn copyBtn" type="button"><i class="las la-copy"></i></button>
                    </div>
                    <div class="two-factor-scan text-center mb-4">
                        <img class="two-factor-thumb" src="{{ $qrCodeUrl }}" alt="images">
                    </div>
                    <div class="text-center">
                        <a href="#0" class="cmn--btn w-100" data-bs-toggle="modal" data-bs-target="#enableModal">
                            @lang('Enable 2FA Security')
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="col-xl-6">
        <div class="card cmn--card h-100">
            <div class="card-header bg--base">
                <h4 class="card-title">@lang('Google Authenticator')</h4>
            </div>
            <div class="card-body">
                <div class="two-factor-content">
                    <h6 class="subtitle"> @lang('GOOGLE AUTHENTICATOR TO SCAN THE QR CODE THE CODE')</h6>
                    <p class="two__fact__text">
                        @lang('Google Authenticator is a multifactor app for mobile devices. It generates timed
                        codes used during the 2-step verification process. To use Google Authenticator, install the
                        Google Authenticator application on your mobile device.')
                    </p>
                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en"
                        target="_blank" class="cmn--btn w-100">@lang('Download App')</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!--Enable Modal -->
<div id="enableModal" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog ">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg--base">
                <h4 class="modal-title">@lang('Verify Your Otp')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.twofactor.enable') }}" method="POST">
                @csrf
                <div class="modal-body ">
                    <div class="form-group">
                        <input type="hidden" name="key" value="{{ $secret }}">
                        <input type="text" class="form-control" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--base">@lang('verify')</button>
                </div>
            </form>
        </div>

    </div>
</div>
<!--Disable Modal -->
<div id="disableModal" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header bg--base">
                <h4 class="modal-title">@lang('Verify Your Otp Disable')</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('user.twofactor.disable') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <input type="text" class="form-control" name="code" placeholder="@lang('Enter Google Authenticator Code')">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--base">@lang('Verify')</button>
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

            $('.copytext').on('click', function() {
                var copyText = document.getElementById("referralURL");
                copyText.select();
                copyText.setSelectionRange(0, 99999);
                document.execCommand("copy");
                iziToast.success({
                    message: "Copied: " + copyText.value,
                    position: "topRight"
                });
            });
        })(jQuery);
</script>
@endpush