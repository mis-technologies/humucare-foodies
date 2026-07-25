@php
    $specials = App\Models\Product::active()->where('hot_deals', 1)->latest()->take(2)->get();
    if ($specials->count() < 2) {
        $specials = App\Models\Product::active()->where('today_deals', 1)->latest()->take(2)->get();
    }
    if ($specials->count() < 2) {
        $specials = App\Models\Product::active()->latest()->take(2)->get();
    }
@endphp
@if ($specials->count() > 0)
<section class="fd-section">
    <div class="fd-container">
        <div class="fd-shead">
            <h2 class="fd-shead__title">@lang("This Week's Specials")</h2>
        </div>
        <div class="fd-specials">
            @foreach ($specials as $sp)
                @php $detailUrl = route('product.detail', ['id' => $sp->id, 'name' => slug($sp->slug)]); @endphp
                <div class="fd-special">
                    <div class="fd-special__head">
                        <h3 class="fd-special__name">{{ __($sp->name) }}</h3>
                        @if ($sp->discount != 0 || $sp->today_deals == 1)
                            <span class="fd-special__disc">({{ trim(strip_tags(discountText($sp, $general))) }} @lang('Off'))</span>
                        @endif
                    </div>
                    <div class="fd-special__media">
                        <a href="{{ $detailUrl }}">
                            <img src="{{ getImage(imagePath()['product']['thumb']['path'] . '/' . $sp->image, imagePath()['product']['thumb']['size']) }}" alt="{{ __($sp->name) }}">
                        </a>
                    </div>
                    <div class="fd-special__foot">
                        <a href="{{ $detailUrl }}" class="fd-btn">@lang('Order Now') <i class="las la-arrow-right"></i></a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
