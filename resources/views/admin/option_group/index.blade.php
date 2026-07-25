@extends('admin.layouts.app')
@section('panel')

<div class="row">
    <div class="col-lg-12">
        <div class="alert alert-info" role="alert">
            <i class="las la-info-circle"></i>
            @lang('Modifier groups let a dish offer choices — sizes, extras, spice level. Groups are reusable: build "Choose your size" once and attach it to as many dishes as you like. A dish with any modifier group asks the customer to choose before it can be added to the basket.')
        </div>
    </div>

    <div class="col-lg-12">
        @forelse($groups as $group)
        <div class="card b-radius--10 mb-4">
            <div class="card-header d-flex flex-wrap align-items-center justify-content-between" style="gap:10px">
                <div>
                    <h5 class="d-inline-block mb-0">{{ __($group->name) }}</h5>
                    <span class="badge badge--{{ $group->type == 'single' ? 'primary' : 'info' }} ml-2">
                        {{ $group->type == 'single' ? __('Pick one') : __('Pick up to') . ' ' . $group->max_select }}
                    </span>
                    @if ($group->is_required)
                        <span class="badge badge--warning">@lang('Required')</span>
                    @else
                        <span class="badge badge--secondary">@lang('Optional')</span>
                    @endif
                    <span class="badge badge--{{ $group->status ? 'success' : 'danger' }}">
                        {{ $group->status ? __('Active') : __('Disabled') }}
                    </span>
                    <small class="text-muted d-block mt-1">
                        <i class="las la-utensils"></i>
                        @lang('Used on') {{ $group->products_count }} @lang('dish(es)')
                    </small>
                </div>
                <div class="d-flex flex-wrap" style="gap:6px">
                    <button class="btn btn-sm btn--primary editGroupBtn"
                        data-id="{{ $group->id }}" data-name="{{ $group->name }}" data-type="{{ $group->type }}"
                        data-required="{{ $group->is_required }}" data-min="{{ $group->min_select }}"
                        data-max="{{ $group->max_select }}" data-sort="{{ $group->sort_order }}"
                        data-status="{{ $group->status }}">
                        <i class="las la-pen"></i> @lang('Edit')
                    </button>
                    <button class="btn btn-sm btn--success assignBtn" data-id="{{ $group->id }}"
                        data-name="{{ $group->name }}"
                        data-products="{{ $group->products->pluck('id')->implode(',') }}">
                        <i class="las la-link"></i> @lang('Assign dishes')
                    </button>
                    <form action="{{ route('admin.optiongroup.status', $group->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn--{{ $group->status ? 'danger' : 'success' }}">
                            <i class="las la-{{ $group->status ? 'eye-slash' : 'eye' }}"></i>
                            {{ $group->status ? __('Disable') : __('Enable') }}
                        </button>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table--light style--two mb-0">
                        <thead>
                            <tr>
                                <th>@lang('Choice')</th>
                                <th>@lang('Extra price')</th>
                                <th>@lang('Order')</th>
                                <th>@lang('Status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($group->allOptions as $option)
                            <tr>
                                <td data-label="@lang('Choice')">{{ __($option->name) }}</td>
                                <td data-label="@lang('Extra price')">
                                    @if ($option->price > 0)
                                        <strong>+{{ $general->cur_sym }}{{ showAmount($option->price) }}</strong>
                                    @else
                                        <span class="text-muted">@lang('Free')</span>
                                    @endif
                                </td>
                                <td data-label="@lang('Order')">{{ $option->sort_order }}</td>
                                <td data-label="@lang('Status')">
                                    <span class="badge badge--{{ $option->status ? 'success' : 'danger' }}">
                                        {{ $option->status ? __('Active') : __('Hidden') }}
                                    </span>
                                </td>
                                <td data-label="@lang('Action')">
                                    <button class="btn btn-sm btn--primary editOptionBtn"
                                        data-id="{{ $option->id }}" data-group="{{ $group->id }}"
                                        data-name="{{ $option->name }}" data-price="{{ getAmount($option->price) }}"
                                        data-sort="{{ $option->sort_order }}" data-status="{{ $option->status }}">
                                        <i class="las la-pen"></i>
                                    </button>
                                    <form action="{{ route('admin.optiongroup.option.delete', $option->id) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('{{ __('Remove this choice?') }}')">
                                        @csrf
                                        <button class="btn btn-sm btn--danger"><i class="las la-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="100%" class="text-muted text-center">
                                    @lang('No choices yet — add one so customers have something to pick.')
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <button class="btn btn-sm btn--primary mt-3 addOptionBtn" data-group="{{ $group->id }}"
                    data-groupname="{{ $group->name }}">
                    <i class="las la-plus"></i> @lang('Add choice')
                </button>
            </div>
        </div>
        @empty
        <div class="card b-radius--10">
            <div class="card-body text-center text-muted">{{ __($emptyMessage) }}</div>
        </div>
        @endforelse

        @if ($groups->hasPages())
            <div class="card-footer py-4">{{ paginateLinks($groups) }}</div>
        @endif
    </div>
</div>

{{-- floating create button, matching the other admin screens --}}
<div class="float-sm-right mt-3">
    <button class="btn btn--primary box--shadow1 text--small addGroupBtn">
        <i class="las la-plus"></i> @lang('New modifier group')
    </button>
</div>

{{-- ---------------- group modal ---------------- --}}
<div class="modal fade" id="groupModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="" method="POST" id="groupForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Modifier group')</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Group name') <span class="text--danger">*</span></label>
                        <input type="text" name="name" class="form-control" required
                            placeholder="@lang('e.g. Choose your size')">
                    </div>
                    <div class="form-group">
                        <label>@lang('Selection type')</label>
                        <select name="type" class="form-control" id="groupType">
                            <option value="single">@lang('Pick one (radio)')</option>
                            <option value="multi">@lang('Pick several (checkbox)')</option>
                        </select>
                    </div>
                    <div class="form-group" id="maxWrap" style="display:none">
                        <label>@lang('Maximum choices')</label>
                        <input type="number" name="max_select" class="form-control" value="3" min="1" max="20">
                    </div>
                    <div class="form-group">
                        <label>@lang('Customer must choose?')</label>
                        <select name="is_required" class="form-control">
                            <option value="0">@lang('Optional')</option>
                            <option value="1">@lang('Required')</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>@lang('Display order')</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="form-group statusWrap" style="display:none">
                        <label>@lang('Status')</label>
                        <select name="status" class="form-control">
                            <option value="1">@lang('Active')</option>
                            <option value="0">@lang('Disabled')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary">@lang('Save')</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ---------------- option modal ---------------- --}}
<div class="modal fade" id="optionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="" method="POST" id="optionForm">
            @csrf
            <input type="hidden" name="option_group_id" id="optionGroupId">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Choice') — <span id="optionGroupName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>@lang('Choice name') <span class="text--danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="@lang('e.g. Large')">
                    </div>
                    <div class="form-group">
                        <label>@lang('Extra price')</label>
                        <div class="input-group">
                            <div class="input-group-prepend"><span class="input-group-text">{{ $general->cur_sym }}</span></div>
                            <input type="number" step="0.01" min="0" name="price" class="form-control" value="0" required>
                        </div>
                        <small class="text-muted">@lang('Added on top of the dish price. Use 0 for a free choice.')</small>
                    </div>
                    <div class="form-group">
                        <label>@lang('Display order')</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="form-group optStatusWrap" style="display:none">
                        <label>@lang('Status')</label>
                        <select name="status" class="form-control">
                            <option value="1">@lang('Active')</option>
                            <option value="0">@lang('Hidden')</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary">@lang('Save')</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ---------------- assign modal ---------------- --}}
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <form action="" method="POST" id="assignForm">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Assign') “<span id="assignGroupName"></span>” @lang('to dishes')</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                </div>
                <div class="modal-body" style="max-height:60vh;overflow-y:auto">
                    @forelse($products as $product)
                    <div class="form-check">
                        <input class="form-check-input assignProduct" type="checkbox" name="products[]"
                            value="{{ $product->id }}" id="assign_{{ $product->id }}">
                        <label class="form-check-label" for="assign_{{ $product->id }}">{{ __($product->name) }}</label>
                    </div>
                    @empty
                    <p class="text-muted">@lang('No dishes found.')</p>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-dismiss="modal">@lang('Close')</button>
                    <button type="submit" class="btn btn--primary">@lang('Save')</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('script')
<script>
    (function ($) {
        "use strict";

        function toggleMax() {
            $('#maxWrap').toggle($('#groupType').val() === 'multi');
        }
        $('#groupType').on('change', toggleMax);

        // --- create group
        $('.addGroupBtn').on('click', function () {
            var f = $('#groupForm');
            f.attr('action', "{{ route('admin.optiongroup.store') }}")[0].reset();
            f.find('.statusWrap').hide();
            toggleMax();
            $('#groupModal').modal('show');
        });

        // --- edit group
        $('.editGroupBtn').on('click', function () {
            var d = $(this).data();
            var f = $('#groupForm');
            f.attr('action', "{{ url('admin/option-group/store') }}/" + d.id);
            f.find('[name=name]').val(d.name);
            f.find('[name=type]').val(d.type);
            f.find('[name=is_required]').val(d.required);
            f.find('[name=max_select]').val(d.max);
            f.find('[name=sort_order]').val(d.sort);
            f.find('[name=status]').val(d.status);
            f.find('.statusWrap').show();
            toggleMax();
            $('#groupModal').modal('show');
        });

        // --- add choice
        $('.addOptionBtn').on('click', function () {
            var f = $('#optionForm');
            f.attr('action', "{{ route('admin.optiongroup.option.store') }}")[0].reset();
            $('#optionGroupId').val($(this).data('group'));
            $('#optionGroupName').text($(this).data('groupname'));
            f.find('.optStatusWrap').hide();
            $('#optionModal').modal('show');
        });

        // --- edit choice
        $('.editOptionBtn').on('click', function () {
            var d = $(this).data();
            var f = $('#optionForm');
            f.attr('action', "{{ url('admin/option-group/option/store') }}/" + d.id);
            $('#optionGroupId').val(d.group);
            $('#optionGroupName').text('');
            f.find('[name=name]').val(d.name);
            f.find('[name=price]').val(d.price);
            f.find('[name=sort_order]').val(d.sort);
            f.find('[name=status]').val(d.status);
            f.find('.optStatusWrap').show();
            $('#optionModal').modal('show');
        });

        // --- assign to dishes
        $('.assignBtn').on('click', function () {
            var d = $(this).data();
            $('#assignForm').attr('action', "{{ url('admin/option-group/assign') }}/" + d.id);
            $('#assignGroupName').text(d.name);
            var ids = String(d.products === undefined ? '' : d.products).split(',');
            $('.assignProduct').each(function () {
                $(this).prop('checked', ids.indexOf($(this).val()) > -1);
            });
            $('#assignModal').modal('show');
        });
    })(jQuery);
</script>
@endpush
