@php
    use App\Models\Category;
    use App\Models\Product;
    use App\Models\ProductPricePerLiter;
    use App\Models\ProductPricePerMilliliter;
    use App\Models\ProductPricePerVolume;

    // Tabs = featured categories (fallback: latest). Only categories that
    // actually have active dishes become tabs, so no tab is ever a dead row.
    $menuCategories = Category::active()->where('featured', 1)->latest()->take(6)->get();
    if ($menuCategories->count() == 0) {
        $menuCategories = Category::active()->latest()->take(6)->get();
    }

    $menuGroups = [];
    foreach ($menuCategories as $cat) {
        $items = Product::active()->where('category_id', $cat->id)
            ->latest()->with('reviews', 'category')->take(8)->get();
        if ($items->count()) {
            $menuGroups[] = ['cat' => $cat, 'items' => $items];
        }
    }

    // Flag dishes that carry size/volume pricing so their card opens Quick View
    // instead of blind-adding a default variant. One query per pricing table.
    $allIds = collect($menuGroups)->flatMap(fn ($g) => $g['items']->pluck('id'))->all();
    $variantIds = collect();
    if (count($allIds)) {
        $variantIds = ProductPricePerLiter::whereIn('product_id', $allIds)->pluck('product_id')
            ->merge(ProductPricePerMilliliter::whereIn('product_id', $allIds)->pluck('product_id'))
            ->merge(ProductPricePerVolume::whereIn('product_id', $allIds)->pluck('product_id'))
            // dishes carrying modifier groups must also pick options first.
            // Only ACTIVE groups count — otherwise a dish attached solely to a
            // disabled group shows "Choose" with nothing to choose.
            ->merge(
                DB::table('product_option_group')
                    ->join('option_groups', 'option_groups.id', '=', 'product_option_group.option_group_id')
                    ->whereIn('product_option_group.product_id', $allIds)
                    ->where('option_groups.status', 1)
                    ->pluck('product_option_group.product_id')
            )
            ->unique();
    }
@endphp

@if (count($menuGroups))
<section class="fd-section fd-section--dark" id="menu">
    <div class="fd-container">
        <div class="fd-shead">
            <h2 class="fd-shead__title">@lang('Our Menu')</h2>
        </div>

        {{-- Tabs filter the dishes in place (no page reload) --}}
        <div class="fd-tabs" role="tablist" aria-label="@lang('Menu categories')">
            @foreach ($menuGroups as $i => $g)
                <button type="button" class="fd-tab {{ $i == 0 ? 'active' : '' }}" role="tab"
                    aria-selected="{{ $i == 0 ? 'true' : 'false' }}"
                    data-cat="{{ $g['cat']->id }}">{{ __($g['cat']->name) }}</button>
            @endforeach
        </div>

        <div class="fd-menu">
            <button type="button" class="fd-menu__arrow fd-menu__arrow--prev" aria-label="@lang('Previous')">
                <i class="las la-angle-left"></i>
            </button>

            @foreach ($menuGroups as $i => $g)
                <div class="fd-menu-scroll fd-menu__track {{ $i == 0 ? 'active' : '' }}"
                    data-cat="{{ $g['cat']->id }}" role="tabpanel" @if ($i != 0) hidden @endif>
                    @foreach ($g['items'] as $product)
                        @include($activeTemplate.'products.card', ['product' => $product, 'variantIds' => $variantIds])
                    @endforeach

                    {{-- trailing "see everything in this category" card --}}
                    <a class="fd-menu-more"
                        href="{{ route('category.products', ['id' => $g['cat']->id, 'name' => slug($g['cat']->name)]) }}">
                        <span class="fd-menu-more__ic"><i class="las la-arrow-right"></i></span>
                        <span>@lang('See all') {{ __($g['cat']->name) }}</span>
                    </a>
                </div>
            @endforeach

            <button type="button" class="fd-menu__arrow fd-menu__arrow--next" aria-label="@lang('Next')">
                <i class="las la-angle-right"></i>
            </button>
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('products') }}" class="fd-btn fd-btn--pill fd-btn--lg">@lang('See Full Menu') <i class="las la-arrow-right"></i></a>
        </div>
    </div>
</section>

<script>
    (function () {
        var section = document.getElementById('menu');
        if (!section) return;
        var tabs   = section.querySelectorAll('.fd-tab');
        var tracks = section.querySelectorAll('.fd-menu__track');
        var prev   = section.querySelector('.fd-menu__arrow--prev');
        var next   = section.querySelector('.fd-menu__arrow--next');

        function activeTrack() { return section.querySelector('.fd-menu__track.active'); }

        function syncArrows() {
            var t = activeTrack();
            if (!t) return;
            var atStart = t.scrollLeft <= 2;
            var atEnd   = t.scrollLeft + t.clientWidth >= t.scrollWidth - 2;
            var overflows = t.scrollWidth > t.clientWidth + 2;
            prev.classList.toggle('is-hidden', !overflows || atStart);
            next.classList.toggle('is-hidden', !overflows || atEnd);
        }

        function selectTab(tab) {
            var cat = tab.getAttribute('data-cat');
            tabs.forEach(function (t) {
                var on = t === tab;
                t.classList.toggle('active', on);
                t.setAttribute('aria-selected', on ? 'true' : 'false');
            });
            tracks.forEach(function (tr) {
                var on = tr.getAttribute('data-cat') === cat;
                tr.classList.toggle('active', on);
                tr.hidden = !on;
                if (on) tr.scrollLeft = 0;
            });
            syncArrows();
        }

        // rAF tween: per-frame scrollLeft assignments work everywhere, unlike the
        // smooth-scroll options object / CSS scroll-behavior (which no-op in some
        // engines). Gives a smooth glide and is fully deterministic.
        function animateScroll(t, to, ms) {
            var from = t.scrollLeft, delta = to - from, start = null;
            if (Math.abs(delta) < 1) { t.scrollLeft = to; return; }
            function step(ts) {
                if (start === null) start = ts;
                var p = Math.min(1, (ts - start) / ms);
                var ease = 1 - Math.pow(1 - p, 3);        // easeOutCubic
                t.scrollLeft = from + delta * ease;
                if (p < 1) requestAnimationFrame(step); else syncArrows();
            }
            requestAnimationFrame(step);
        }

        function scrollByCards(dir) {
            var t = activeTrack();
            if (!t) return;
            var card = t.querySelector('.fd-card');
            var step = card ? card.getBoundingClientRect().width + 22 : t.clientWidth * 0.8;
            var max = t.scrollWidth - t.clientWidth;
            animateScroll(t, Math.max(0, Math.min(max, t.scrollLeft + dir * step * 2)), 350);
        }

        tabs.forEach(function (tab) { tab.addEventListener('click', function () { selectTab(tab); }); });
        prev.addEventListener('click', function () { scrollByCards(-1); });
        next.addEventListener('click', function () { scrollByCards(1); });
        tracks.forEach(function (t) { t.addEventListener('scroll', syncArrows, { passive: true }); });
        window.addEventListener('resize', syncArrows);
        syncArrows();
    })();
</script>
@endif
