@extends($activeTemplate.'layouts.frontend')
@section('content')
@include($activeTemplate.'sections.advertise')
<section class="top-brands-section pt-60 pb-120">
    <div class="container">
        <div class="row gy-5">
            <div class="col-lg-12">
                <div class="section__header">
                    <h5 class="title">@lang('All Categories')</h5>
                </div>
                <div class="row g-3">
                    @foreach ($categoryList as $category)
                    <div class="col-sm-12 col-md-6 col-lg-4">
                        <a class="brand__item"
                            href="{{ route('category.products',['id'=>$category->id,'name'=>slug($category->name)]) }}">
                            <div class="brand__item-img">
                                <img src="{{ getImage(imagePath()['category']['path'].'/'. $category->image,imagePath()['category']['size']) }}"
                                    alt="products">
                            </div>
                            <div class="brand__item-cont">
                                <span>{{ __($category->name) }}</span>
                                <span><i class="las la-angle-right"></i></span>
                            </div>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        {{ $categoryList->links() }}
    </div>
</section>
@endsection