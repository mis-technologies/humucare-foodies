@php
$captcha = loadCustomCaptcha();
@endphp
@if($captcha)
<div class="form-group">
    @php echo $captcha @endphp
</div>
<div class="form-group">
    <label for="email" class="form--label form-label">@lang('Enter Code')</label>
    <input type="text" name="captcha" class="form-control form--control">
</div>
@endif