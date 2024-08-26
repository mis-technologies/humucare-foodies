@extends($activeTemplate.'layouts.frontend')
@section('content')
@include($activeTemplate.'sections.advertise')
<section class="top-brands-section pt-60 pb-120">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-12">
                <div class="section__header">
                    <h5 class="title">@lang('All Brands')</h5>
                </div>
                <div class="row g-3">
                    @foreach ($brands as $brand)
                    <div class="col-sm-6 col-md-6 col-lg-3">
                        <a class="brand__item"
                            href="{{ route('brand.products',['id'=>$brand->id,'name'=>slug($brand->name)]) }}">
                            <div class="brand__item-img">
                                <img src="{{ getImage(imagePath()['brand']['path'].'/'. $brand->image,imagePath()['brand']['size']) }}"
                                    alt="products">
                            </div>
                            <div class="brand__item-cont">
                                <span>{{ __($brand->name) }}</span>
                                <span><i class="las la-angle-right"></i></span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{ $brands->links() }}
    </div>
</section>
@endsection