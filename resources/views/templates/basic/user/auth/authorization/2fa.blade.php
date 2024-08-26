@extends($activeTemplate .'layouts.frontend')
@section('content')
<section class="account-section pt-60 bg-white">
    <div class="container">
        <div class="account-wrapper">
            <div class="row">
                <div class="col-lg-6 col-md-8 m-auto">
                    <div class="card cmn--card">
                        <div class="card-body p-3 p-sm-4">
                            <div class="">
                                <div class="account-header mb-0 text-center">
                                    <h5 class="title mt-0">@lang('2FA Verification')</h5>
                                </div>
                                <form class="account-form" action="{{ route('user.go2fa.verify') }}" method="POST">
                                    @csrf
                                    <div class="form-group">
                                        <label for="code" class="form--label form-label">
                                            @lang('Verification Code')
                                        </label>
                                        <input type="text" class="form-control form--control" name="code" id="code"
                                            required autofocus="off">
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