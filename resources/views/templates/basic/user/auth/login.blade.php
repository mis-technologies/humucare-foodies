@php
$content = getContent('login.content', true);
@endphp
@extends($activeTemplate.'layouts.frontend')
@section('content')


<section class="account-section pt-60 bg-white">
    <div class="container">
        <div class="account-wrapper">
            <div class="row gy-5 align-items-center">
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="account-thumb rtl">
                        <img src="{{ getImage('assets/images/frontend/login/'.@$content->data_values->image,'636x648') }}" alt="thumb">
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="account-right ps-lg-5">
                        <div class="account-header mb-4">
                            <h5 class="title mb-1">{{ __(@$content->data_values->heading) }}</h5>
                            <p class="mb-0 fs--14px">{{ __(@$content->data_values->sub_heading) }}</p>
                        </div>
                        <form class="account-form" method="POST" action="{{ route('user.login') }}" onsubmit="return submitUserForm();">
                            @csrf
                            <div class="form-group">
                                <label for="email" class="form--label form-label">@lang('Email or Username')<span class="text--danger">*</span></label>
                                <input type="text" id="email" class="form-control form--control" name="username" value="{{ old('username') }}">
                            </div>
                            <div class="form-group">
                                <label for="password" class="form--label form-label">@lang('Password')<span class="text--danger">*</span></label>
                                <div class="position-relative">
                                    <input type="password" id="password" class="form-control form--control" name="password">
                                </div>
                            </div>

                            <div class="form--group">
                                @php echo loadReCaptcha() @endphp
                            </div>
                            @include($activeTemplate.'partials.custom_captcha')

                            <div class="d-flex flex-wrap justify-content-between">
                                <div class="form-check form--check">
                                    <input type="checkbox" class="form-check-input" id="exampleCheck1" name="remember"
                                        {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="exampleCheck1">@lang('Remember Me')</label>
                                </div>
                                <a href="{{ route('user.password.request') }}" class="text--base fs--14px">
                                    @lang('Forgot Password?')
                                </a>
                            </div>
                            <div class="mt-4">
                                <button class="cmn--btn w-100" type="submit">@lang('Sign In')</button>
                            </div>
                            <div class="mt-3 fs--14px">
                                @lang('Don\'t have an account ?')
                                <a href="{{ route('user.register') }}" class="text--base">@lang('Create account now')</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@push('script')
<script>
    "use strict";
        function submitUserForm() {
            var response = grecaptcha.getResponse();
            if (response.length == 0) {
                document.getElementById('g-recaptcha-error').innerHTML =
                    '<span class="text-danger">@lang("Captcha field is required.")</span>';
                return false;
            }
            return true;
        }
</script>
@endpush