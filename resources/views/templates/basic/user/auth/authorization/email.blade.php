@extends($activeTemplate .'layouts.frontend')
@section('content')
<section class="account-section pt-60 bg-white">
    <div class="container">
        <div class="account-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card cmn--card">
                        <div class="card-body p-3 p-sm-4">
                            <div class="account-header mb-0 text-center">
                                <h5 class="title mt-0">@lang('Please Verify Your Email to Get Access')</h5>
                            </div>
                            <form class="account-form" action="{{ route('user.verify.email') }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <p class="text-center">@lang('Your Email'):
                                        <strong>{{ auth()->user()->email }}</strong>
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form--label form-label">@lang('Verification Code')</label>
                                    <input type="text" class="form-control form--control" name="email_verified_code"maxlength="7" id="code">
                                </div>
                                <div class="mt-4">
                                    <button class="cmn--btn w-100" type="submit">@lang('Submit')</button>
                                </div>
                                <div class="mt-3">
                                    <p class="mb-0 text--dark fs--14px">@lang('Please check including your Junk/Spam Folder. if not found, you can')
                                        <a href="{{ route('user.send.verify.code') }}?type=email" class="forget-pass text--base">
                                            @lang('Resend code')
                                        </a>
                                    </p>
                                    @if ($errors->has('resend'))
                                    <br />
                                    <small class="text--danger">{{ $errors->first('resend') }}</small>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('script')
<script>
    (function($) {
            "use strict";
            $('#code').on('input change', function() {
                var xx = document.getElementById('code').value;

                $(this).val(function(index, value) {
                    value = value.substr(0, 7);
                    return value.replace(/\W/gi, '').replace(/(.{3})/g, '$1 ');
                });

            });
        })(jQuery)
</script>
@endpush