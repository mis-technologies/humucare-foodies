@php
    $sliders = getContent('banner.element', false, null, true);
    $hero = $sliders->first();
@endphp
<section class="fd-hero">
    {{-- full-bleed curved photo panel (sits behind the floating header) --}}
    <div class="fd-hero__media">
        @if ($hero)
            <img src="{{ getImage('assets/images/frontend/banner/' . @$hero->data_values->image, '1292x474') }}" alt="banner">
        @else
            <video autoplay muted loop playsinline>
                <source src="/foodies.mp4" type="video/mp4">
            </video>
        @endif
    </div>

    <div class="fd-container">
        <div class="fd-hero__content">
            <h1 class="fd-hero__title">@lang('The best meals for your taste buds at your convenience')</h1>

            <a href="{{ route('products') }}" class="fd-btn fd-btn--pill fd-btn--xl">@lang('Explore Our Menu')</a>

            <form action="{{ route('products') }}" method="GET" class="fd-hero__search">
                <i class="las la-search"></i>
                <input type="text" name="search" placeholder="@lang('Enter food to search')" value="{{ request()->search ?? null }}">
                <button type="submit">@lang('Search')</button>
            </form>
        </div>
    </div>
</section>
