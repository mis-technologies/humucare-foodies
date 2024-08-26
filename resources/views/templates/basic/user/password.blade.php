@extends($activeTemplate.'layouts.master')
@section('content')
<div class="row justify-content-center">
    <div class="col-md-10 col-lg-11 col-xl-9 col-xxl-8">
        <form class="card cmn--card" action="" method="post">
            @csrf
            <div class="card-header py-3 text-center bg--base">
                <h5 class="m-0 title">@lang('Change Password')</h5>
            </div>
            <div class="card-body py-4">
                <div class="form-group mb-3">
                    <label for="old-password" class="form--label">@lang('Old Password')</label>
                    <input type="password" name="current_password" id="current_password"
                        class="form-control form--control" required autocomplete="current-password">
                </div>
                <div class="form-group mb-3 hover-input-popup">
                    <label for="new-password" class="form--label">@lang('New Password')</label>
                    <input type="password" name="password" id="password" class="form-control form--control" required
                        autocomplete="current-password">
                    @if ($general->secure_password)
                    <div class="input-popup">
                        <p class="error lower">@lang('1 small letter minimum')</p>
                        <p class="error capital">@lang('1 capital letter minimum')</p>
                        <p class="error number">@lang('1 number minimum')</p>
                        <p class="error special">@lang('1 special character minimum')</p>
                        <p class="error minimum">@lang('6 character password')</p>
                    </div>
                    @endif
                </div>
                <div class="form-group mb-0">
                    <label for="confirm-password" class="form--label">@lang('Confirm New Password')</label>
                    <input type="password" name="password_confirmation" id="confirm-password"
                        class="form-control form--control" required autocomplete="current-password">
                </div>
            </div>
            <div class="card-footer text-end">
                <button type="submit" class="cmn--btn w-100">@lang('Submit')</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('style-lib')
<link rel="stylesheet" href="{{ asset($activeTemplateTrue.'css/custom.css') }}">
@endpush

@push('script-lib')
<script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
@endpush
@push('script')
<script>
    (function($) {
            "use strict";
            @if ($general->secure_password)
                $('input[name=password]').on('input',function(){
                secure_password($(this));
                });
            @endif
        })(jQuery);
</script>
@endpush