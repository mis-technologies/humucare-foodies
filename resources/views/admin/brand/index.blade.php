@extends('admin.layouts.app')
@section('panel')

<div class="row">
    <div class="col-lg-12">
        <div class="card b-radius--10 ">
            <div class="card-body p-0">
                <div class="table-responsive--sm table-responsive">
                    <table class="table table--light style--two">
                        <thead>
                            <tr>
                                <th>@lang('S.N.')</th>
                                <th>@lang('Name')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Top Brand')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($brands as $brand)
                            <tr>
                                <td data-label="@lang('S.N.')">{{ $loop->index + $brands->firstItem() }}</td>
                                <td data-label="@lang('Name')">
                                    <div class="user">
                                        <div class="thumb">
                                            <img src="{{ getImage(imagePath()['brand']['path'] . '/' . $brand->image, imagePath()['brand']['size']) }}"
                                                alt="@lang('image')">
                                        </div>
                                        <span class="name">{{ __($brand->name) }}</span>
                                    </div>
                                </td>
                                <td data-label="@lang('Status')">
                                    @if ($brand->status == 1)
                                    <span class="text--small badge font-weight-normal badge--success">
                                        @lang('Enable')
                                    </span>
                                    @else
                                    <span class="text--small badge font-weight-normal badge--danger">
                                        @lang('Disabled')
                                    </span>
                                    @endif

                                </td>
                                <td data-label="@lang('Top Brand')">
                                    @if ($brand->featured == 1)
                                    <span class="text--small badge font-weight-normal badge--primary">
                                        @lang('Yes')
                                    </span>
                                    @else
                                    <span class="text--small badge font-weight-normal badge--danger">
                                        @lang('No')
                                    </span>
                                    @endif

                                </td>
                                <td data-label="@lang('Action')">
                                    <button class="icon-btn editButton" data-toggle="modal"
                                        data-id="{{ $brand->id }}" data-name="{{ __($brand->name) }}"
                                        data-status="{{ __($brand->status) }}"
                                        data-featured="{{ __($brand->featured) }}" data-image="{{ __($brand->image) }}"
                                        data-target="#editBrand" data-original-title="@lang('Edit')">
                                        <i class="la la-pencil"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table><!-- table end -->
                </div>
            </div>
            @if ($brands->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($brands) }}
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>
<div id="createBrand" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @lang('Add New Brand')
                </h5>
            </div>
            <form action="{{ route('admin.brand.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">
                            @lang('Name') <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" />
                    </div>

                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">@lang('Image') <span
                                class="text-danger">*</span></label>
                        <div class="image-upload">
                            <div class="thumb">
                                <div class="avatar-preview">
                                    <div class="profilePicPreview"
                                        style="background-image: url({{ getImage('/', imagePath()['brand']['size']) }})">
                                        <button type="button" class="remove-image">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="avatar-edit">
                                    <input type="file" class="profilePicUpload" name="image" id="profilePicUpload1"
                                        accept=".png, .jpg, .jpeg">
                                    <label for="profilePicUpload1" class="bg--primary">@lang('Upload
                                        Image')</label>
                                    <small class="mt-2 text-facebook">@lang('Supported files'):
                                        <b>@lang('jpeg'), @lang('jpg'), @lang('png').</b>
                                        @lang('Image will be resized into 70x70')
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">
                            @lang('Top Brands')
                        </label>
                        <input type="checkbox" data-width="100%" data-onstyle="-success" data-offstyle="-danger"
                            data-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')" name="featured">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editBrand" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    @lang('Update brand')
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">
                            @lang('Name') <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control border-radius-5" value="" />
                    </div>
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">@lang('Image') <span
                                class="text-danger">*</span></label>
                        <div class="image-upload">
                            <div class="thumb">
                                <div class="avatar-preview">
                                    <div class="profilePicPreview"
                                        style="background-image: url({{ getImage('/', imagePath()['brand']['size']) }})">
                                        <button type="button" class="remove-image">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="avatar-edit">
                                    <input type="file" class="profilePicUpload" name="image" id="profilePicUpload2"
                                        accept=".png, .jpg, .jpeg">
                                    <label for="profilePicUpload2" class="bg--primary">@lang('Upload
                                        Image')</label>
                                    <small class="mt-2 text-facebook">@lang('Supported files'):
                                        <b>@lang('jpeg'), @lang('jpg'), @lang('png').</b>
                                        @lang('Image will be resized into 70x70')
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">
                            @lang('Status')</label>
                        <input type="checkbox" id="status" data-width="100%" data-onstyle="-success"
                            data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')"
                            data-off="@lang('Disabled')" name="status">
                    </div>
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">
                            @lang('Top Brands')
                        </label>
                        <input type="checkbox" id="featured" data-width="100%" data-onstyle="-success"
                            data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Yes')" data-off="@lang('No')"
                            name="featured">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')

<a data-toggle="modal" href="#createBrand" class="btn btn-sm btn--primary mr-2 d-flex align-items-center add-new-btn">
    <i class="las la-plus"></i>
    @lang('Add new')
</a>

<form method="GET" class="form-inline float-sm-right bg--white search-form">
    <div class="input-group has_append">
        <input type="text" name="search" id="mySearch" class="form-control" placeholder="@lang('Brand name')"
            value="{{ request()->search }}">
        <div class="input-group-append">
            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>

@endpush
@push('script')
<script>
    (function($) {
            "use strict"
            $('.editButton').on('click', function() {
                var modal = $('#editBrand');
                var featured = $(this).data('featured');
                var status = $(this).data('status');
                modal.find('form').attr('action', `{{ route('admin.brand.store','') }}/${$(this).data('id')}`);
                modal.find('input[name=name]').val($(this).data('name'));

                if ($(this).data('status') == 1) {
                    modal.find('input[name=status]').bootstrapToggle('on');
                } else {
                    modal.find('input[name=status]').bootstrapToggle('off');
                }

                if ($(this).data('featured') == 1) {
                    modal.find('input[name=featured]').bootstrapToggle('on');
                } else {
                    modal.find('input[name=featured]').bootstrapToggle('off');
                }
                var x = $(this).data('image');
                $(".profilePicPreview").css("background-image",
                    `url({{ asset('assets/images/brand/${x}') }})`);

            });
            $('.deleteButton').on('click', function() {
                var modal = $('#deletebrand');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=status]').val($(this).data('status'));
                modal.find('.modal-detail').text(`@lang('Are you sure to delete this brand?')`);
            });
            $('.restoreButton').on('click', function() {
                var modal = $('#restorebrand');
                modal.find('input[name=id]').val($(this).data('id'));
                modal.find('input[name=status]').val($(this).data('status'));
                modal.find('.modal-detail').text(`@lang('Are you sure to restore this brand?')`);
            });
        })(jQuery);
</script>
@endpush
