@extends($activeTemplate.'layouts.master')
@section('content')
<div class="profile-wrapper">
    <form class="profile-edit-form row" action="" method="post" enctype="multipart/form-data">
        @csrf
        <div class="profile-user mb-xl-0">
            <div class="thumb">
                <img class="profile-user-path" src="{{ getImage(imagePath()['profile']['user']['path'].'/'. $user->image,null,true) }}" alt="user">
                <label class="btn btn--base profile-img-upload" for="profile-image"><i class="las la-pen"></i></label>
                <input type="file" name="image" class="form-control form--control" id="profile-image" hidden>
            </div>
            
            {{-- <div class="remove-image">
                <i class="las la-times"></i>
            </div> --}}

        </div>
        <div class="profile-form-area">
            <div class="row">
                <div class="col-md-6">
                    <div class="form--group">
                        <label class="form--label" for="first-name">@lang('First Name')</label>
                        <input type="text" name="firstname" class="form-control form--control" id="first-name"
                            value="{{ $user->firstname }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form--group">
                        <label class="form--label" for="last-name">@lang('Last Name')</label>
                        <input type="text" name="lastname" class="form-control form--control" id="last-name"
                            value="{{ $user->lastname }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form--group">
                        <label class="form--label" for="email">@lang('Your Email')</label>
                        <input type="text" name="email" class="form-control form--control" id="email"
                            value="{{ $user->email }}" readonly>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form--group">
                        <label class="form--label" for="username">@lang('Username')</label>
                        <input type="text" name="username" class="form-control form--control" id="username"
                            value="{{ $user->username }}" readonly>
                    </div>
                </div>


                <div class="col-md-6">
                    <div class="form--group">
                        <label class="form--label" for="address">@lang('Address')</label>
                        <input type="text" name="address" class="form-control form--control" id="address"
                            value="{{ @$user->address->address }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form--group">
                        <label class="form--label" for="state">@lang('State')</label>
                        <input type="text" name="state" class="form-control form--control" id="state"
                            value="{{ @$user->address->state }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form--group">
                        <label class="form--label" for="zip">@lang('Zip Code')</label>
                        <input type="text" name="zip" class="form-control form--control" id="zip"
                            value="{{ @$user->address->zip }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form--group">
                        <label class="form--label" for="city">@lang('City')</label>
                        <input type="text" name="city" class="form-control form--control" id="city"
                            value="{{ @$user->address->city }}">
                    </div>
                </div>
                <div class="col-md-12">
                    <button type="submit" class="cmn--btn w-100 btn--md">@lang('Update Profile')</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('script')
<script>
    (function ($) {
        "use strict"
        var prevImg = $('.profile-user .thumb').html();
        function proPicURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function (e) {
                    var preview = $('.profile-user').find('.profile-user-path');
                    preview.attr('src',`${e.target.result}`);
                    preview.fadeIn(650);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#profile-image").on('change', function () {
            proPicURL(this);
        });
    })(jQuery);
</script>
@endpush