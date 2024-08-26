@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="account-section pt-60 bg-white">
    <div class="container">
        <div class="account-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card cmn--card">
                        <div class="card-body p-3 p-sm-4">
                            <div class="account-header mb-0 text-center">
                                <h5 class="title mt-0">@lang('Reset Password')</h5>
                            </div>
                            <form class="account-form" method="POST" action="{{ route('user.password.email') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="email" class="form--label form-label">@lang('Select One')</label>
                                    <select class="form-control form--control" name="type">
                                        <option value="email">@lang('E-Mail Address')</option>
                                        <option value="username">@lang('Username')</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="email" class="form--label form-label my_value"></label>
                                    <input type="text" id="email"
                                        class="form-control form--control @error('value') is-invalid @enderror"
                                        name="value" value="{{ old('value') }}" required autofocus="off">
                                    @error('value')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <div class="mt-4">
                                    <button class="cmn--btn w-100" type="submit">@lang('Submit')</button>
                                </div>
                            </form>
                        </div>
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

            myVal();
            $('select[name=type]').on('change', function() {
                myVal();
            });

            function myVal() {
                $('.my_value').text($('select[name=type] :selected').text());
            }
        })(jQuery)
</script>
@endpush