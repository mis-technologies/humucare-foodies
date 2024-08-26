@extends($activeTemplate.'layouts.master')

@section('content')
<div class="card cmn--card h-100">
    <div class="card-header bg--base">
        <h4 class="card-title">{{ __($pageTitle) }}</h4>
    </div>
    <div class="card-body">
        <form action="{{ route('user.deposit.manual.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-12 text-center">
                    <p class="text-center mt-2">@lang('You have requested') <b class="text-success">{{ showAmount($data['amount'])  }} {{__($general->cur_text)}}</b> , @lang('Please pay')
                        <b class="text-success">{{showAmount($data['final_amo']) .' '.$data['method_currency'] }} </b> @lang('for successful payment')
                    </p>
                    <h4 class="text-center text--base">@lang('Please follow the instruction below')</h4>
                    <p class="my-4 text-center">@php echo $data->gateway->description @endphp</p>

                </div>
                @if($method->gateway_parameter)
                    @foreach(json_decode($method->gateway_parameter) as $k => $v)
                        @if($v->type == "text")
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><strong>{{__(inputTitle($v->field_level))}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                    <input type="text" class="form-control form--control" name="{{$k}}" value="{{old($k)}}" placeholder="{{__($v->field_level)}}">
                                </div>
                            </div>
                        @elseif($v->type == "textarea")
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>{{__(inputTitle($v->field_level))}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                        <textarea name="{{$k}}"  class="form-control"  placeholder="{{__($v->field_level)}}" rows="3">{{old($k)}}</textarea>

                                    </div>
                                </div>
                        @elseif($v->type == "file")
                            <div class="col-md-12">
                                <label><strong>{{__($v->field_level)}} @if($v->validation == 'required') <span class="text-danger">*</span>  @endif</strong></label>
                                <input type="file" name="{{$k}}" class="form-control form--control" id="profile-image" accept="image/*">
                            </div>
                        @endif
                    @endforeach
                @endif
                <div class="col-md-12 mt-3">
                    <div class="form-group">
                        <button type="submit" class="cmn--btn w-100">@lang('Pay Now')</button>
                    </div>
                </div>

            </div>

        </form>
    </div>
</div>
@endsection
@push('style')
<style>
    .withdraw-thumbnail{
        max-width: 220px;
        max-height: 220px
    }
</style>
@endpush
@push('script-lib')
<script src="{{asset($activeTemplateTrue.'/js/bootstrap-fileinput.js')}}"></script>
@endpush
@push('style-lib')
<link rel="stylesheet" href="{{asset($activeTemplateTrue.'/css/bootstrap-fileinput.css')}}">
@endpush
@push('script')
    <script>
        "use strict"
        var prevImg = $('.profile-user .thumb').html();

        function proPicURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = $('.profile-user').find('.thumb');
                    preview.html(`<img src="${e.target.result}" alt="user" class="w-25">`);
                    preview.addClass('has-image');
                    preview.hide();
                    preview.fadeIn(650);
                    $(".remove-image").show();
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
        $("#profile-image").on('change', function() {
            proPicURL(this);
        });
        $(".remove-image").on('click', function() {
            $(".profile-user .thumb").html(prevImg);
            $(".profile-user .thumb").removeClass('has-image');
            $(this).hide();
        })
    </script>
@endpush