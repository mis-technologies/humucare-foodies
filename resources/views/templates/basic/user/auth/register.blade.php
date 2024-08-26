@extends($activeTemplate.'layouts.frontend')
@section('content')
@php
$content = getContent('register.content',true);
$policyPages = getContent('policy_pages.element', false, null, true);
@endphp
<section class="account-section pt-60 bg-white">
    <div class="container">
        <div class="account-wrapper">
            <div class="row gy-5 align-items-center">
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="account-thumb rtl">
                        <img src="{{ getImage('assets/images/frontend/register/'.@$content->data_values->image,'523x660') }}"
                            alt="thumb">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="account-right ps-xl-5">
                        <div class="account-header mb-4">
                            <h5 class="title mb-1">{{ __(@$content->data_values->heading) }}</h5>
                            <p class="mb-0 fs--14px">{{ __(@$content->data_values->sub_heading) }}</p>
                        </div>
                        <form class="account-form row" action="{{ route('user.register') }}" method="POST"
                            onsubmit="return submitUserForm();">
                            @csrf
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group">
                                    <label for="fname" class="form--label form-label">@lang('First Name')<span class="text--danger">*</span></label>
                                    <input type="text" id="fname" class="form-control form--control" name="firstname" value="{{ old('firstname') }}" required>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group">
                                    <label for="lname" class="form--label form-label">@lang('Last Name')<span class="text--danger">*</span></label>
                                    <input type="text" id="lname" class="form-control form--control" name="lastname" value="{{ old('lastname') }}" required>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group">
                                    <label for="uname" class="form--label form-label">@lang('Username')<span class="text--danger">*</span></label>
                                    <input type="text" id="uname" class="form-control form--control checkUser" name="username" value="{{ old('username') }}" required>
                                    <small class="text-danger usernameExist"></small>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group">
                                    <label for="email" class="form--label form-label">@lang('Email')<span class="text--danger">*</span></label>
                                    <input type="email" id="email" class="form-control form--control checkUser"  name="email" value="{{ old('email') }}" required>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group">
                                    <label for="country" class="form--label form-label">@lang('Country')<span class="text--danger">*</span></label>
                                    <select name="country" id="country" class="form-select form--control">
                                        @foreach ($countries as $key => $country)
                                        <option data-mobile_code="{{ $country->dial_code }}"
                                            value="{{ $country->country }}" data-code="{{ $key }}"
                                            @if(old('country')==$country->country) selected="selected" @endif>
                                            {{__($country->country) }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-6">
                                <input type="hidden" name="mobile_code">
                                <input type="hidden" name="country_code">
                                <div class="form-group">
                                    <label for="mobile" class="form--label form-label">@lang('Mobile')<span class="text--danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text mobile-code"></span>
                                        <input type="tel" id="mobile" class="form-control form--control checkUser" value="{{ old('mobile') }}" name="mobile">
                                    </div>
                                    <small class="text-danger mobileExist"></small>
                                </div>
                            </div>

                            <div class="col-xl-6 col-md-6">
                                <div class="form-group hover-input-popup">
                                    <label for="password" class="form--label form-label">@lang('Password')<span class="text--danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" id="password" class="form-control form--control" name="password" required>
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
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6">
                                <div class="form-group">
                                    <label for="cpassword" class="form--label form-label">@lang('Confirm Password')<span class="text--danger">*</span></label>
                                    <div class="position-relative">
                                        <input type="password" id="cpassword" class="form-control form--control" name="password_confirmation" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form--group">
                                @php echo loadReCaptcha() @endphp
                            </div>
                            @include($activeTemplate.'partials.custom_captcha')

                            <div class="col-xl-12 col-md-12">
                                <div class="form--check form-check">
                                    <input id="agree" type="checkbox" class="m-0 acceptPolicy form-check-input d-none"
                                        name="agree">
                                    <label for="agree" class="form--label form-label form-check-label mb-0">@lang('I accept all') 
                                        @foreach($policyPages as $page)
                                        <a href="{{ route('page.details', [$page->id, slug($page->data_values->title)]) }}"
                                            class="policy-link-page text--base">
                                            {{ $page->data_values->title }}{{ $loop->last ? '' : ',' }}
                                        </a>
                                        @endforeach</label>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button class="cmn--btn mt-3 w-100" type="submit">@lang('Sign Up')</button>
                            </div>
                            <div class="col-md-12">
                                <p class="mt-3 mb-0 fs--14px">@lang('Already have an account ?')
                                    <a href="{{ route('user.login') }}" class="text--base">@lang('Login now')</a>
                                </p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="existModalCenter" tabindex="-1" role="dialog" aria-labelledby="existModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg--base">
                <h5 class="modal-title" id="existModalLongTitle">@lang('You are with us.')</h5>
            </div>
            <div class="modal-body">
                <h6 class="text-center">@lang('You already have an account please Sign in ')</h6>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn--dark btn-sm" data-bs-dismiss="modal">@lang('Close')</button>
                <a href="{{ route('user.login') }}" class="btn btn--base btn-sm">@lang('Login')</a>
            </div>
        </div>
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
        (function($) {
            @if ($mobile_code)
                $(`option[data-code={{ $mobile_code }}]`).attr('selected','');
            @endif

            $('select[name=country]').change(function() {
                $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
                $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
                $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            });
            $('input[name=mobile_code]').val($('select[name=country] :selected').data('mobile_code'));
            $('input[name=country_code]').val($('select[name=country] :selected').data('code'));
            $('.mobile-code').text('+' + $('select[name=country] :selected').data('mobile_code'));
            @if ($general->secure_password)
                $('input[name=password]').on('input',function(){
                secure_password($(this));
                });
            @endif

            $('.checkUser').on('focusout', function(e) {
                var url = '{{ route('user.checkUser') }}';
                var value = $(this).val();
                var token = '{{ csrf_token() }}';
                if ($(this).attr('name') == 'mobile') {
                    var mobile = `${$('.mobile-code').text().substr(1)}${value}`;
                    var data = {
                        mobile: mobile,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'email') {
                    var data = {
                        email: value,
                        _token: token
                    }
                }
                if ($(this).attr('name') == 'username') {
                    var data = {
                        username: value,
                        _token: token
                    }
                }
                $.post(url, data, function(response) {
                    if (response['data'] && response['type'] == 'email') {
                        $('#existModalCenter').modal('show');
                    } else if (response['data'] != null) {
                        $(`.${response['type']}Exist`).text(`${response['type']} already exist`);
                    } else {
                        $(`.${response['type']}Exist`).text('');
                    }
                });
            });

        })(jQuery);
</script>
@endpush