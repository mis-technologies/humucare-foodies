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
                                <th> @lang('Name')</th>
                                <th>@lang('Category')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subcategories as $subcategory)
                            <tr>
                                <td data-label="@lang('S.N.')">{{ $loop->index + $subcategories->firstItem() }}</td>
                                <td data-label="@lang('Name')">
                                    <div class="thumb">
                                        <span class="name">{{ __($subcategory->name) }}</span>
                                    </div>
                                </td>
                                <td data-label="@lang('Category')">
                                    <div class="thumb">
                                        <span class="name">{{ __($subcategory->category->name ?? '') }}</span>
                                    </div>
                                </td>
                                <td data-label="@lang('Status')">
                                    @if ($subcategory->status == 1)
                                    <span class="text--small badge font-weight-normal badge--success">
                                        @lang('Enabled')
                                    </span>
                                    @else
                                    <span class="text--small badge font-weight-normal badge--danger">
                                        @lang('Disabled')
                                    </span>
                                    @endif
                                </td>
                                <td data-label="@lang('Action')">
                                    <button class="icon-btn editButton" data-toggle="modal"
                                        data-id="{{ $subcategory->id }}" data-name="{{ __($subcategory->name) }}"
                                        data-status="{{ __($subcategory->status) }}"
                                        data-featured="{{ __($subcategory->featured) }}"
                                        data-category="{{ $subcategory->category_id }}" data-target="#editSubcategory"
                                        data-original-title="@lang('Edit')">
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
                    </table>
                </div>
            </div>
            @if ($subcategories->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($subcategories) }}
            </div>
            @endif
        </div>
    </div>
</div>

<div id="createSubcategory" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Add New Subcategory')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.subcategory.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">@lang('Category Name') <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-control">
                            <option value="" selected disabled>@lang('Select One')</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">@lang('Name') <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" />
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn--primary w-100">@lang('Submit')</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="editSubcategory" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Update Subcategory')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">@lang('Category Name') <span class="text-danger">*</span></label>
                        <select name="category_id" id="edit_category_id" class="form-control">
                            <option value="" selected disabled>@lang('Select one')</option>
                            @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold">@lang('Name')<span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="" />
                    </div>
                    <div class="form-group">
                        <label class="form-control-label font-weight-bold"> @lang('Status')</label>
                        <input type="checkbox" id="status" data-width="100%" data-onstyle="-success"
                            data-offstyle="-danger" data-toggle="toggle" data-on="@lang('Enable')"
                            data-off="@lang('Disabled')" name="status">
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
<a data-toggle="modal" href="#createSubcategory" class="btn btn-sm btn--primary mr-2 d-flex align-items-center add-new-btn">
    <i class="las la-plus"></i> @lang('Add new')
</a>

<form method="GET" class="form-inline float-sm-right bg--white search-form">
    <div class="input-group has_append">
        <input type="text" name="search" id="mySearch" class="form-control" placeholder="@lang('Search here')"
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
            var modal = $('#editSubcategory');
            var featured = $(this).data('featured');
            var status = $(this).data('status');
            modal.find('form').attr('action', `{{ route('admin.subcategory.store','') }}/${$(this).data('id')}`);
            modal.find('input[name=name]').val($(this).data('name'));
            modal.find('input[name=image]').val($(this).data('image'));
            modal.find('select[name=category_id]').val($(this).data('category'));

            if ($(this).data('status') == 1) {
                modal.find('input[name=status]').bootstrapToggle('on');
            } else {
                modal.find('input[name=status]').bootstrapToggle('off');
            }
        });
    })(jQuery);
</script>

@endpush
