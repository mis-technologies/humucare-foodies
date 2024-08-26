@extends($activeTemplate.'layouts.master')
@section('content')
<table class="table cmn--table">
    <thead>
        <tr>
            <th scope="col">@lang('Product Name')</th>
            <th scope="col">@lang('Image')</th>
            <th scope="col">@lang('Price')</th>
            <th scope="col">@lang('Rating')</th>
            <th scope="col">@lang('Action')</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($products as $product)
            @php
                $price = productPrice($product);
            @endphp
            <tr id="tr_{{$product->id}}">
                <td data-label="@lang('Product Name')">{{ __($product->name) }}</td>
                <td data-label="@lang('Image')">
                    <img src="{{ getImage(imagePath()['product']['thumb']['path'] . '/' . $product->image, imagePath()['product']['thumb']['size']) }}" alt="@lang('image')" class="show-img" width="40px">
                </td>
                <td data-label="@lang('Price')" class="text--base">
                    <strong>{{ $general->cur_sym }}{{ showAmount($price) }}</strong>
                </td>
                <td data-label="@lang('Rating')">
                    <div class="ratings">
                        @php
                            $star = showProductRatings($product->avg_rate);
                            echo $star;
                        @endphp
                    </div>
                </td>
                <td data-label="@lang('Action')">
                    <a href="{{ route('user.review.create',[$product->id,slug($product->name)]) }}" class="btn btn-sm btn--base @if($product->reviews->count() > 0) disabled @endif" data-bs-toggle="tooltip" data-bs-position="top" title="@lang('Add Review')">
                        <i class="las la-star-of-david"></i>
                    </a>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="100%" class="justify-content-center text-center text--danger">{{ __($emptyMessage) }}</td>
            </tr>
        @endforelse
    </tbody>
</table>
{{ $products->links() }}
@endsection





