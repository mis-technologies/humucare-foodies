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
                                <th>@lang('Name')</th>
                                <th>@lang('Product SKU')</th>
                                <th>@lang('Price')</th>
                                <th>@lang('Stock Quantity')</th>
                                <th>@lang('status')</th>
                                <th>@lang('Action')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                            <tr>
                                <td data-label="@lang('Name')">
                                    <div class="user">
                                        <div class="thumb">
                                            <img src="{{ getImage(imagePath()['product']['thumb']['path'] . '/' . $product->image, imagePath()['product']['thumb']['size']) }}"
                                                alt="@lang('image')">
                                        </div>
                                        <span class="name">{{ __($product->name) }}</span>
                                    </div>
                                </td>
                                <td data-label="@lang('Product SKU')">
                                    <div class="user">
                                        <span class="name">{{ __($product->product_sku) }}</span>
                                    </div>
                                </td>
                                <td data-label="@lang('Price')">
                                    <div class="user">
                                        <span class="name">{{ __($general->cur_sym) }}{{ showAmount($product->price)
                                            }}</span>
                                    </div>
                                </td>
                                <td data-label="@lang('Stock Quantity')">
                                    <div class="user">
                                        <span class="name">{{ $product->quantity }}</span>
                                    </div>
                                </td>
                                <td data-label="@lang('Status')">
                                    @if ($product->status == 1)
                                    <span class="text--small badge font-weight-normal badge--success">
                                        @lang('Enable')</span>
                                    @else
                                    <span class="text--small badge font-weight-normal badge--warning">
                                        @lang('Disable')</span>
                                    @endif

                                </td>
                                <td data-label="@lang('Action')">
                                    <a href="{{ route('admin.product.reviews', $product->id) }}" class="icon-btn btn--info" data-toggle="tooltip" title="" data-original-title="@lang('Reviews')">
                                        <i class="la la-star"></i>
                                    </a>
                                    <a href="{{ route('admin.product.edit', $product->id) }}" class="icon-btn ml-1" data-toggle="tooltip" title="" data-original-title="@lang('Edit')">
                                        <i class="la la-pencil"></i>
                                    </a>
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
            @if ($products->hasPages())
            <div class="card-footer py-4">
                {{ paginateLinks($products) }}
            </div>
            @endif
        </div><!-- card end -->
    </div>
</div>



<div id="todayDealDiscount" class="modal fade" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('Today Deal Discount')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.product.today.deal') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold">@lang('Amount') <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="discount" class="form-control" value="{{ getAmount($general->discount) }}" />
                            <div class="input-group-append">
                                <select name="discount_type" class="input-group-text">
                                    <option value="1" {{ $general->discount_type = 1 ?'selected':'' }}>{{ __($general->cur_text) }}</option>
                                    <option value="2" {{ $general->discount_type = 2 ?'selected':'' }}>@lang('%')</option>
                                </select>
                            </div>
                        </div>
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
@if(request()->routeIs('admin.product.index'))
<a href="{{ route('admin.product.create') }}"
    class="btn btn-sm btn--primary mr-2 d-flex align-items-center add-new-btn"><i class="las la-plus"></i>
    @lang('Add New Product')
</a>
@else
<a data-toggle="modal" href="#todayDealDiscount" class="btn btn-sm btn--primary mr-2 d-flex align-items-center add-new-btn">
    @lang('Today Deal Discount')
</a>
@endif
<form method="GET" class="form-inline float-sm-right bg--white">
    <div class="input-group has_append">
        <input type="text" name="search" id="mySearch" class="form-control" placeholder="@lang('Product name or SKU')"
            value="{{ request()->search }}">
        <div class="input-group-append">
            <button class="btn btn--primary" type="submit"><i class="fa fa-search"></i></button>
        </div>
    </div>
</form>
@endpush