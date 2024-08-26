@extends($activeTemplate.'layouts.frontend')
@section('content')
@include($activeTemplate.'sections.advertise')
<section class="products-single-section pt-80 pb-80">
    <div class="container">
        <div class="bg--section p-3 border rounded">
            <div class="row">
                <div class="col-lg-5">
                    <div class="sync1 owl-carousel owl-theme">
                        @foreach ($product->productGallery as $gallery)
                        <div class="thumbs">
                            <img src="{{ getImage(imagePath()['product']['gallery']['path'].'/'. $gallery->image,imagePath()['product']['gallery']['size']) }}"
                                alt="product/details">
                        </div>
                        @endforeach
                    </div>
                    <div class="sync2 owl-carousel owl-theme">
                        @foreach ($product->productGallery as $gallery)
                        <div class="thumbs">
                            <img src="{{ getImage(imagePath()['product']['gallery']['path'].'/'. $gallery->image,imagePath()['product']['gallery']['size']) }}"
                                alt="product/details">
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-lg-7 ps-lg-5">
                    <div class="product-details-content">
                        <div class="repeat--item">
                            <h5 class="title">{{ __($product->name) }}</h5>
                            <div class="ratings-area">
                                @if ($general->display_stock == 1)
                                <div class="{{ $product->quantity == 0 ? 'badge badge--danger' : 'badge badge--success' }} badge-sm badge-warning me-3">
                                    {{$product->quantity == 0 ? 'Out of Stock' : 'In Stock' }}
                                </div>
                                @endif
                                <div class="ratings">
                                    @php
                                    $star = showProductRatings($product->avg_rate);
                                    echo $star;
                                    @endphp
                                </div>
                                <span class="ms-2 me-auto">({{ $product->reviews->count() }})</span>
                            </div>
                        </div>
                        @php
                        $price = productPrice($product);
                        $features = json_decode($product->features);
                        @endphp
                        <div class="repeat--item border-0">
                            <ul class="lists">
                                <li>
                                    <span class="name">@lang('Price')</span>
                                    <h5 class="m-0 text--base product-price">
                                        {{ $general->cur_sym }}{{ showAmount($price) }}
                                    </h5>
                                </li>
                                <li>
                                    <span class="name">@lang('Categories')</span>
                                    <p class="mb-1">{{ $product->category->name }}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="repeat--item">
                            <ul class="lists">
                                <li>
                                    <span class="name">@lang('Summary')</span>
                                    <p class="summary m-0 ps-5">{{ __($product->summary) }}</p>
                                </li>
                            </ul>
                        </div>
                        <div class="repeat--item">
                            <ul class="lists">
                                <li>
                                    <span class="name">@lang('Total Price')</span>
                                    <h5 class="info m-0 total-price">
                                        @if ($product->discount != 0)
                                        {{ $general->cur_sym }}{{ showAmount($price) }}
                                        @else
                                        {{ $general->cur_sym }}{{ showAmount($product->price) }}
                                        @endif
                                    </h5>
                                </li>
                            </ul>
                        </div>
                        <div class="repeat--item">
                            <div class="single-add-cart-area">
                                <div class="cart-plus-minus">
                                    <div class="cart-decrease qtybutton dec">
                                        <i class="las la-minus"></i>
                                    </div>
                                    <input type="number" class="form-control productQuantity" name="quantity" value="1">
                                    <div class="cart-increase qtybutton inc active">
                                        <i class="las la-plus"></i>
                                    </div>
                                </div>
                                @if ($general->display_stock == 1)
                                <div class="quantity--amount">
                                    (<span class="amount">{{ $product->quantity }}</span>)
                                </div>
                                @endif
                                <a href="#0" class="cmn--btn add-to-cart" data-product_id="{{ $product->id }}">
                                    @lang('Add To Cart')
                                </a>
                            </div>
                        </div>
                        <div class="repeat--item">
                            <ul class="lists">
                                <li class="mt-2">
                                    <span class="name">@lang('Share')</span>
                                    <ul class="social-icons">
                                        <li>
                                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}"
                                                title="@lang('Facebook')">
                                                <i class="lab la-facebook-f"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="https://twitter.com/intent/tweet?text={{ __($product->name) }}%0A{{ url()->current() }}"
                                                title="@lang('twitter')">
                                                <i class="lab la-twitter"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://www.linkedin.com/shareArticle?mini=true&amp;url={{urlencode(url()->current()) }}&amp;title=my share text&amp;summary=dit is de linkedin summary"
                                                title="@lang('Linkedin')">
                                                <i class="lab la-linkedin-in"></i>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="http://pinterest.com/pin/create/button/?url={{urlencode(url()->current()) }}&description={{ __($product->name) }}&media={{ getImage('assets/images/product/'. $product->image) }}"
                                                title="@lang('Pinterest')">
                                                <i class="lab la-pinterest"></i>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pt-80">
            <div class="row flex-wrap-reverse gy-5">
                @if ($topProducts->count() > 0)
                <div class="col-lg-4 col-xl-3">
                    <div class="filter__widget">
                        <h5 class="filter__widget-title">@lang('Top Products')</h5>
                        <div class="filter__widget-body">
                            <div class="deal__wrapper">
                                @foreach ($topProducts as $topProduct)
                                @php
                                $price = productPrice($topProduct);
                                @endphp
                                <a href="{{ route('product.detail',['id'=>$topProduct->id,'name'=>slug($topProduct->slug)]) }}"
                                    class="deal__item">
                                    <div class="deal__item-img">
                                        <img src="{{ getImage(imagePath()['product']['thumb']['path'].'/'. $topProduct->image,imagePath()['product']['thumb']['size']) }}"
                                            alt="banner/products">
                                    </div>
                                    <div class="deal__item-cont">
                                        <div class="ratings">
                                            @php
                                            $star = showProductRatings($topProduct->avg_rate);
                                            echo $star;
                                            @endphp
                                        </div>
                                        <h6 class="price">{{ $general->cur_sym }}{{ showAmount($price) }}</h6>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                <div class="@if ($topProducts->count() > 0) col-lg-8 col-xl-9 @else col-lg-12 col-xl-12 @endif">
                    <div class="description-wrapper bg--section">
                        <div class="description__header">
                            <ul class="nav van-tabs nav--tabs">
                                <li>
                                    <a href="#desc" data-bs-toggle="tab" class="active">@lang('Description')</a>
                                </li>
                                @if ($features)
                                <li>
                                    <a href="#feature" data-bs-toggle="tab">@lang('Specification')</a>
                                </li>
                                @endif
                                <li>
                                    <a href="#rating" data-bs-toggle="tab">@lang('Reviews')</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="desc">
                                <div class="description__body">
                                    <p>@php echo $product->description; @endphp</p>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="feature">
                                <div class="description__body border-0 p-0">
                                    <table class="feature-table table">
                                        <tbody>
                                            @foreach ($features as $feature)
                                            <tr>
                                                <th>{{ $feature->feature_title }}</th>
                                                <td>{{ $feature->feature_desc }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="rating">
                                <div class="description__body">
                                    <div class="review-area">
                                        @forelse ($product->reviews as $review)
                                        <div class="review-item">
                                            <div class="thumb">
                                                <img src="{{ getImage(imagePath()['profile']['user']['path'].'/'. @$review->user->image) }}"
                                                    alt="review">
                                            </div>
                                            <div class="content">
                                                <div class="entry-meta">
                                                    <h6 class="posted-on">
                                                        <span class="text--base">{{ __(@$review->user->username)
                                                            }}</span>
                                                        <span>@lang('Posted on') {{ showDateTime($review->create_at)
                                                            }}</span>
                                                    </h6>
                                                    <div class="ratings">
                                                        @for($i = 1; $i <= $review->stars; $i++)
                                                            <i class="las la-star"></i>
                                                        @endfor

                                                        @for($k = 1; $k <= 5-$review->stars; $k++)
                                                            <i class="lar la-star"></i>
                                                        @endfor
                                                    </div>
                                                </div>
                                                <div class="entry-content">
                                                    <p>{{ __($review->review_comment) }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="review-item">
                                            <strong class="text--danger">{{ __($emptyMessage) }}</strong>
                                        </div>
                                        @endforelse

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if ($relatedProduct->count() > 0)
                    <div class="mt-5">
                        <h4 class="mb-3">@lang('Related Products')</h4>
                        <div class="row gy-4 justify-content-center">
                            @foreach ($relatedProduct as $product)
                            <div class="col-lg-3 col-xxl-3 col-md-6 col-sm-10">
                                @php
                                    $price = productPrice($product);
                                @endphp
                                <div class="product__item">
                                    <div class="product__item-img">
                                        <a
                                            href="{{ route('product.detail',['id'=>$product->id,'name'=>slug($product->slug)]) }}">
                                            <img src="{{ getImage(imagePath()['product']['thumb']['path'].'/'. $product->image,imagePath()['product']['thumb']['size']) }}"
                                                alt="product">
                                        </a>
                                        @php
                                            if($product->discount != 0 || $product->today_deals == 1){
                                                $discount = discountText($product,$general);
                                                echo $discount;
                                            }
                                        @endphp
                                        <div class="product-right-btn">
                                            <a href="#0" data-bs-toggle="modal" data-bs-target="#quickView"
                                                class="quickView" data-product_id="{{ $product->id }}">
                                                <i class="las la-expand-arrows-alt"></i>
                                            </a>
                                            <a href="#0" data-product_id="{{ $product->id }}" class="add-wishlist">
                                                <i class="las la-heart"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="product__item-cont">
                                        <h6 class="title">
                                            <a href="{{ route('product.detail',['id'=>$product->id,'name'=>slug($product->slug)]) }}">
                                                {{__($product->name)}}
                                            </a>
                                        </h6>
                                        @if ($general->display_stock == 1)
                                        <span class="info {{ $product->quantity == 0 ? 'text--danger' : 'text--success' }}">
                                            {{$product->quantity == 0 ? 'Out of Stock' : 'In Stock' }}
                                        </span>
                                        @endif
                                        <div class="d-flex justify-content-between align-items-center @if ($general->display_stock != 1) mt-2 @endif">
                                            <div class="ratings">
                                                @php
                                                $star = showProductRatings($product->avg_rate);
                                                echo $star;
                                                @endphp
                                            </div>

                                            <h6 class="m-0 price">
                                                {{ $general->cur_sym }}{{ showAmount($price) }}
                                                @if ($product->discount != 0)
                                                <del class="text--danger">{{ $general->cur_sym
                                                    }}{{showAmount($product->price) }}</del>
                                                @elseif($product->today_deals == 1)
                                                <del class="text--danger">{{ $general->cur_sym }}{{
                                                    showAmount($product->price) }}</del>
                                                @endif
                                            </h6>
                                        </div>
                                        <div class="hover-cont-wrapper">
                                            <div class="hover-cont-area">
                                                <a href="#0" class="cmn--btn cart-number-btn add-to-cart"
                                                    data-product_id="{{ $product->id }}">
                                                    @lang('Add To Cart')
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('script')
<script>
    (function ($) {
        "use script";

        $('.cart-decrease').on('click',function () {
            var quantity = $('input[name="quantity"]').val();
            if(quantity > 0){
                TotalPrice();
            }else{
                $('input[name="quantity"]').val(1);
                notify('error', 'You have to order a minimum amount of one.');
            }
        });
        $('.cart-increase').on('click',function () {
            TotalPrice();
        });
        $('input[name="quantity"]').on('focusout', function() {
            var quantity = $(this).val();
            if(quantity > 0){
                TotalPrice();
            }else{
                $('input[name="quantity"]').val(1);
                TotalPrice()
                notify('error', 'You have to order a minimum amount of one.');
            }
        });

        function TotalPrice(){
            var quantity = $('input[name="quantity"]').val();
            var productPrice = $('.product-price').text();
            var splitPrice = productPrice.split("{{ $general->cur_sym }}");
            var price = parseFloat(splitPrice[1]);
            var totalPrice = quantity * price;
            $('.total-price').text("{{ $general->cur_sym }}"+totalPrice.toFixed(2));
        }
    })(jQuery);
</script>
@endpush
