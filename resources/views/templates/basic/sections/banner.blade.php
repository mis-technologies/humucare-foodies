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

                        {{-- <div class="home-service-btn" style="position: absolute; top: 60%; left: 75%; transform: translate(-50%, -50%); z-index: 2;">

                            <div style="background-color: rgba(0, 0, 0, 0.7); padding: 15px; border-radius: 10px; text-align: center;">
                                <h6 style="color: #FFFFFFEE; font-weight: bold; font-size: 1.2rem;">No more <del style="color: #FFFFFF;">£500</del></h6>
                                <p style="color: #FFF200; font-size: 1.5rem; font-weight: bold;">Get 15 packs of 500mL for <span style="color: #FFFFFF">£220</span></p>

                                <h6 style="color: #FFFFFFEE; font-weight: bold; font-size: 1.2rem;" class="mt-1">No more <del style="color: #FFFFFF;">£750</del></h6>
                                <p style="color: #FFF200; font-size: 1.5rem; font-weight: bold;">15 packs of 1lt meals for <span style="color: #FFFFFF">£350</span></p>
                            </div>

                            <a href="#" style="border-radius:30px; color: #F5c10C; background-color: rgba(0, 0, 0, 0.7); " class=" mt-2">
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="m-2">Coming Soon....</p>

                                </div>
                            </a>
                            <a href="" style="border-radius:30px; color: #F5c10C; background-color: rgba(0, 0, 0, 0.7); " class=" mt-2">
                                <div class="d-flex align-items-center justify-content-center">
                                    <p class="m-2">Home Services.</p>
                                    <i class="fa fa-arrow-alt-circle-right m-2"></i>
                                </div>
                            </a>
                        </div> --}}
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
