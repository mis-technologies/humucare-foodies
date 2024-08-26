@extends('admin.layouts.app')
@section('panel')
<div class="row">
    <div class="col-lg-12">
        <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="card">
                <div class="card-header">
                    <h5>@lang('Product Information')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-6">
                            <label class="form-control-label font-weight-bold">@lang('Name')
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" class="form-control"  value="{{ old('name') }}" required/>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-control-label font-weight-bold">@lang('Brands')
                                <span class="text-danger">*</span>
                            </label>
                            <select class="form-control" name="brand" required>
                                <option value="" selected disabled>@lang('Select one')</option>
                                @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @if (old('brand')==$brand->id) selected="selected"
                                    @endif>
                                    {{ __($brand->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-control-label font-weight-bold">@lang('Category')
                                <span class="text-danger">*</span></label>

                            <select class="form-control" name="category_id" id="category">
                                <option value="" selected disabled>@lang('Select one')</option>
                                @foreach ($allCategory as $category)
                                <option data-subcategories="{{ $category->subcategories }}" value="{{ $category->id }}" @if (old('category_id')==$category->id)
                                    selected="selected" @endif>
                                    {{ __($category->name) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label class="form-control-label font-weight-bold">@lang('Subcategory')
                                <span class="text-danger">*</span>
                            </label>
                            <select id="subcategory" name="subcategory_id" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('Price')
                                    <span class="text-danger">*</span>
                                </label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ $general->cur_sym }}</span>
                                    </div>
                                    <input type="number" step="any" class="form-control" name="price" value="{{ old('price') }}" min="0" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('Product SKU')
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="product_sku" class="form-control" value="{{ old('product_sku') }}" required/>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group ">
                                <label class="form-control-label font-weight-bold">@lang('Stock Quantity')
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}" required/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('Discount')</label>
                                <div class="input-group">
                                    <input type="number" step="any" name="discount" class="form-control" value="0"/>
                                    <div class="input-group-append">
                                        <select name="discount_type" class="input-group-text">
                                            <option value="1">{{ __($general->cur_sym) }}</option>
                                            <option value="2">@lang('%')</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card my-2">
                <div class="card-header">
                    <h5>@lang('Digital Item')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('Is Digital') <span
                                        class="text-danger">*</span></label>
                                <div class="input-group">
                                    <select class="form-control" id="digital_item" name="digital_item">
                                        <option value="1">@lang('Yes')</option>
                                        <option value="0" selected>@lang('No')</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-none" id="inputSection">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('Select File Type')
                                    <span class="text-danger">*</span></label>
                                <select class="form-control" id="file_type" name="file_type">
                                    <option value="" selected disabled>@lang('Select one')</option>
                                    <option value="1">@lang('File Upload')</option>
                                    <option value="2">@lang('Link')</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 d-none" id="fileSection">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('Upload File') <span
                                        class="text-danger">*</span></label>
                                <div class="custom-file">
                                    <input type="file" name="digi_file" class="custom-file-input" id="inputGroupFile01"
                                        aria-describedby="inputGroupFileAddon01">
                                    <label class="custom-file-label" for="inputGroupFile01">@lang('Choose file')</label>
                                </div>
                                <small class="mt-2 text-facebook">@lang('Supported files'): <b>@lang('pdf'),
                                        @lang('docx'), @lang('txt'), @lang('zip'), @lang('xlx'), @lang('csv'),
                                        @lang('ai'), @lang('psd'), @lang('pptx') </b></small>
                            </div>
                        </div>
                        <div class="col-md-4 d-none" id="linkSection">
                            <div class="form-group">
                                <label class="form-control-label font-weight-bold">@lang('Link Address') <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="digi_link" class="form-control" placeholder="@lang('Link')">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card my-2">
                <div class="card-header">
                    <h5>@lang('Product Description')</h5>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">@lang('Summary')
                            <span class="text-danger">*</span>
                        </label>
                        <textarea name="summary" class="form-control" cols="2" rows="2" required>{{ old('summary') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">@lang('Description')
                            <span class="text-danger">*</span>
                        </label>
                        <textarea rows="5" class="form-control border-radius-5 nicEdit" name="description">{{ old('description') }}</textarea>
                    </div>
                </div>
            </div>
            <div class="card my-2">
                <div class="card-header">
                    <h5 class="d-inline-block">@lang('Product Specification')</h5>
                    <button type="button" class="btn btn-sm btn--primary float-right addFeatureData">
                        <i class="la la-fw la-plus"></i>@lang('Add New')
                    </button>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <div class="col-md-12">
                            <div class="addedFeature">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card my-2">
                <div class="card-header">
                    <h5>@lang('Image Section')</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-3">
                            <div class="payment-method-item">
                                <div class="payment-method-header">
                                    <div class="thumb">
                                        <div class="avatar-preview">
                                            <div class="profilePicPreview" style="background-image: url('{{ getImage('/', imagePath()['product']['thumb']['size']) }}')"></div>
                                        </div>
                                        <div class="avatar-edit">
                                            <input type="file" name="image" class="profilePicUpload" id="image" accept=".png, .jpg, .jpeg"/>
                                            <label for="image" class="bg--primary"><i class="la la-pencil"></i></label>
                                        </div>
                                    </div>
                                </div>
                                <small class="mt-2 text-facebook">@lang('Supported files'):
                                    <b>@lang('jpeg'), @lang('jpg').</b> @lang('Image will be resized into') {{  imagePath()['product']['thumb']['size'] }}@lang('px')
                                </small>
                            </div>
                        </div>
                        <div class="col-xl-9">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <label class="form-control-label font-weight-bold">@lang('Display Hot Deals')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="hot_deals">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form-control-label font-weight-bold">@lang('Display Featured Product')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="featured_product">
                                </div>
                                <div class="form-group col-sm-6">
                                    <label class="form-control-label font-weight-bold">@lang('Display Today Deals')</label>
                                    <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                                        data-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="today_deals">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card my-2">
                <div class="card-header">
                    <h5 class="d-inline-block">@lang('Gallery Image')</h5>
                    <button type="button" class="btn btn-sm btn--primary float-right addUserData text-light">
                        <i class="la la-fw la-plus"></i>@lang('Add New')
                    </button>
                </div>
                <div class="card-body">
                    <div class="row addedField"></div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn--primary btn-block">@lang('Submit')</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('breadcrumb-plugins')
<a href="{{ route('admin.product.index') }}" class="btn btn-sm btn--primary box--shadow1 text--small">
    <i class="la la-fw la-backward"></i> @lang('Go Back')
</a>
@endpush

@push('script')
<script>
    (function ($) {
    "use strict";

        $('[name=category_id]').on('change', function() {
            let subcategories = $(this).find(':selected').data('subcategories');
            let html = `<option value='' disabled selected>@lang('Select one')</option>`;
            $.each(subcategories, function(i, val) {
                html += `<option value="${val.id}">${val.name}</option>`
            });
            $('[name=subcategory_id]').html(html);
        }).change();

        $('input[name=currency]').on('input', function() {
            $('.currency_symbol').text($(this).val());
        });
        $('.addUserData').on('click', function() {

            var randomId = Math.floor(Math.random() * 100);
            var html = `
            <div class="col-md-3 user-data">
                <div class="form-group">
                    <div class="image-upload">
                        <div class="thumb">
                            <div class="avatar-preview">
                                <div class="profilePicPreview" style="background-image: url({{ getImage('/', imagePath()['product']['gallery']['size']) }})">
                                    <button type="button" class="remove-image removeBtn d-block"><i class="fa fa-times"></i></button>
                                </div>
                            </div>
                            <div class="avatar-edit">
                                <input type="file" class="profilePicUpload" name="files[]" id="${randomId}" accept=".png, .jpg, .jpeg">
                                <label for="${randomId}" class="bg--success">@lang('Upload Image')</label>
                                <small class="mt-2 text-facebook">@lang('Supported files'): <b>@lang('jpeg'), @lang('jpg'), @lang('png').</b> @lang('Image will be resized into') {{  imagePath()['product']['gallery']['size'] }}@lang('px') </small>
                            </div>

                        </div>
                    </div>
                </div>
            </div>`;

            $('.addedField').append(html);
        });

        $('.addFeatureData').on('click', function() {
            var html = `
            <div class="feature-data">
                <div class="form-group">
                    <div class="mb-4 row">
                        <div class="col-md-5 mb-2 mb-md-0">
                            <input name="feature_title[]" class="form-control" type="text" required placeholder="@lang('Title')">
                        </div>
                        <div class="col-md-5 mb-2 mb-md-0">
                            <input name="feature_desc[]" class="form-control" type="text" required placeholder="@lang('Description')">
                        </div>
                        <div class="col-md-2 mt-md-0 mt-2 text-right">
                            <span class="input-group-btn">
                                <button class="btn btn--danger btn-lg remove-Btn w-100" type="button">
                                    <i class="fa fa-times"></i>
                                </button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>`;

            $('.addedFeature').append(html);
        });

        $(document).on('click', '.removeBtn', function() {
            $(this).closest('.user-data').remove();
        });
        $(document).on('click', '.remove-Btn', function() {
            $(this).closest('.feature-data').remove();
        });
        @if (old('currency'))
            $('input[name=currency]').trigger('input');
        @endif

        $("#digital_item").change(function(){
            var data = $(this).val();
            if(data == 1){
                $('#inputSection').addClass('d-block');
                $('#inputSection').removeClass('d-none');
            }else{
                $('#inputSection').addClass('d-none');
                $('#inputSection').removeClass('d-block');
                $('#linkSection').addClass('d-none');
                $('#linkSection').removeClass('d-block');
                $('#fileSection').addClass('d-none');
                $('#fileSection').removeClass('d-block');
            }
        });

        $("#file_type").change(function(){
            var data = $(this).val();
            if(data == 1){
                $('#linkSection').addClass('d-none');
                $('#linkSection').removeClass('d-block');
                $('#fileSection').addClass('d-block');
                $('#fileSection').removeClass('d-none');
            }else{
                $('#fileSection').addClass('d-none');
                $('#fileSection').removeClass('d-block');
                $('#linkSection').addClass('d-block');
                $('#linkSection').removeClass('d-none');
            }
        });
    })(jQuery);
</script>

@endpush
