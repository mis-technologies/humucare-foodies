@php
$sliders = getContent('banner.element',false,null,true);
// dd($sliders);
@endphp
<section class="banner-section my-4">
    <div class="container-fluid">
        <div class="banner__wrapper">
            <div class="banner__wrapper-category d-none d-lg-block">
                <div class="banner__wrapper-category-inner">
                    <h6 class="banner__wrapper-category-inner-header">@lang('Categories')</h6>
                    @include($activeTemplate.'partials.navbar')
                </div>
            </div>
            <div class="banner__wrapper-content">
                <div class="banner-slider owl-theme owl-carousel">
                    @foreach ($sliders as $slider)
                    <div class="banner__wrapper-content-inner">
                        <a href="{{ __($slider->data_values->url) }}">
                            <img src="{{ getImage('assets/images/frontend/banner/'.$slider->data_values->image,'1292x474') }}" alt="banner">
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @if ($todayDealProducts->count() > 0)
            <div class="banner__wrapper-products">
                <div class="banner__wrapper-products-inner">
                    <h6 class="banner__wrapper-products-inner-header">@lang('Today\'s Deal')</h6>
                    <div class="banner__wrapper-products-inner-body">
                        <div class="product-max-xl-slider">
                            @foreach ($todayDealProducts as $product)
                            @php
                                $price = productPrice($product);
                            @endphp
                            <a href="{{ route('product.detail',['id'=>$product->id,'name'=>slug($product->slug)]) }}" class="deal__item">
                                <div class="deal__item-img">
                                    <img src="{{ getImage(imagePath()['product']['thumb']['path'].'/'. $product->image,imagePath()['product']['thumb']['size']) }}" alt="banner/products">
                                </div>
                                <div class="deal__item-cont">
                                    <h6 class="price text--base">{{ $general->cur_sym }}{{ showAmount($price) }}</h6>
                                    <del class="old-price">{{ $general->cur_sym }}{{ showAmount($product->price) }}</del>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
