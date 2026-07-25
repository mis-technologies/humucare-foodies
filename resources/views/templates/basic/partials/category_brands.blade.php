@php
    $categories = App\Models\Category::active()->where('featured', 1)->latest()->take(6)->get();
    $brands = App\Models\Brand::where('status', 1)->where('featured', 1)->latest()->take(6)->get();
@endphp
@if ($categories->count() > 0 || $brands->count() > 0)
<section class="fd-section fd-section--tint">
    <div class="fd-container">
        <div class="row gy-5">
            @if ($categories->count() > 0)
            <div class="col-lg-6">
                <div class="fd-shead fd-shead--left d-flex align-items-center justify-content-between" style="margin-bottom:24px">
                    <h2 class="fd-shead__title" style="font-size:28px">@lang('Top Categories')</h2>
                    <a href="{{ route('all.category') }}" class="fd-shead__link">@lang('Show All')</a>
                </div>
                <div class="row g-3">
                    @foreach ($categories as $category)
                    <div class="col-sm-6">
                        <a class="fd-chip" href="{{ route('category.products', ['id' => $category->id, 'name' => slug($category->name)]) }}">
                            <img class="fd-chip__img" src="{{ getImage(imagePath()['category']['path'] . '/' . $category->image, imagePath()['category']['size']) }}" alt="{{ __($category->name) }}">
                            <span class="fd-chip__name">{{ __($category->name) }}</span>
                            <i class="las la-angle-right"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
            @if ($brands->count() > 0)
            <div class="col-lg-6">
                <div class="fd-shead fd-shead--left d-flex align-items-center justify-content-between" style="margin-bottom:24px">
                    <h2 class="fd-shead__title" style="font-size:28px">@lang('Top Brands')</h2>
                    <a href="{{ route('all.brands') }}" class="fd-shead__link">@lang('Show All')</a>
                </div>
                <div class="row g-3">
                    @foreach ($brands as $brand)
                    <div class="col-sm-6">
                        <a class="fd-chip" href="{{ route('brand.products', ['id' => $brand->id, 'name' => slug($brand->name)]) }}">
                            <img class="fd-chip__img" src="{{ getImage(imagePath()['brand']['path'] . '/' . $brand->image, imagePath()['brand']['size']) }}" alt="{{ __($brand->name) }}">
                            <span class="fd-chip__name">{{ __($brand->name) }}</span>
                            <i class="las la-angle-right"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</section>
@endif
