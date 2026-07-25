<header class="fd-header {{ request()->routeIs('home') ? 'fd-header--overlay' : '' }}">
    <div class="fd-container fd-header__bar">
        <a href="{{ route('home') }}" class="fd-header__logo">
            <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/foodie-logo.png') }}" alt="@lang('logo')">
        </a>

        {{-- Menu / About / Contact, per the hero design. Track Order stays
             reachable from the footer and the mobile drawer. --}}
        <ul class="fd-nav">
            <li><a href="{{ route('products') }}" class="{{ menuActive('products') }}">@lang('Menu')</a></li>
            <li><a href="{{ route('pages', 'about-us') }}">@lang('About')</a></li>
            <li><a href="{{ route('contact') }}" class="{{ menuActive('contact') }}">@lang('Contact')</a></li>
        </ul>

        {{-- Two circular actions only (bag + account), per the design.
             Wishlist stays reachable from the footer and the mobile drawer. --}}
        <div class="fd-header__actions">
            <a href="{{ route('cart') }}" class="fd-icon-btn" aria-label="@lang('Cart')">
                <i class="las la-shopping-bag"></i>
                <span class="qty show-cart-count">0</span>
            </a>
            <a href="{{ auth()->check() ? route('user.home') : route('user.login') }}" class="fd-icon-btn fd-icon-btn--user"
                aria-label="{{ auth()->check() ? __(auth()->user()->username) : __('Login') }}">
                <i class="las la-user"></i>
            </a>
            <button class="fd-burger" type="button" aria-label="@lang('Menu')" id="fdBurger">
                <span></span><span></span><span></span>
            </button>
        </div>
    </div>
</header>

{{-- Mobile slide-in navigation --}}
<div class="fd-backdrop" id="fdBackdrop"></div>
<nav class="fd-mobile-nav" id="fdMobileNav">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <a href="{{ route('home') }}"><img src="{{ getImage(imagePath()['logoIcon']['path'] . '/foodie-logo.png') }}" alt="logo" style="height:42px"></a>
        <button class="fd-mobile-nav__close" id="fdNavClose" aria-label="Close">&times;</button>
    </div>
    <form action="{{ route('products') }}" method="GET" class="fd-hero__search mb-3" style="box-shadow:none">
        <i class="las la-search"></i>
        <input type="text" name="search" placeholder="@lang('Search')" value="{{ request()->search ?? null }}">
        <button type="submit">@lang('Go')</button>
    </form>
    <a href="{{ route('home') }}" class="{{ menuActive('home') }}">@lang('Home')</a>
    <a href="{{ route('products') }}" class="{{ menuActive('products') }}">@lang('Menu')</a>
    <a href="{{ route('pages', 'about-us') }}">@lang('About')</a>
    <a href="{{ route('contact') }}">@lang('Contact')</a>
    <a href="{{ route('track-order') }}">@lang('Track Order')</a>
    <a href="{{ route('wishlist') }}">@lang('Wishlist') (<span class="show-wishlist-count">0</span>)</a>
    <a href="{{ route('cart') }}">@lang('Cart') (<span class="show-cart-count">0</span>)</a>
    @auth
        <a href="{{ route('user.home') }}">{{ __(auth()->user()->username) }}</a>
    @else
        <a href="{{ route('user.login') }}">@lang('Login')</a>
        <a href="{{ route('user.register') }}">@lang('Register')</a>
    @endauth
    <div class="change-language mt-3">
        <select class="language langSel form-control">
            @foreach ($language as $item)
                <option value="{{ $item->code }}" @if (session('lang') == $item->code) selected @endif>{{ __($item->name) }}</option>
            @endforeach
        </select>
    </div>
</nav>

<script>
    (function () {
        var burger = document.getElementById('fdBurger');
        var nav = document.getElementById('fdMobileNav');
        var backdrop = document.getElementById('fdBackdrop');
        var closeBtn = document.getElementById('fdNavClose');
        function openNav() { nav.classList.add('open'); backdrop.classList.add('open'); }
        function closeNav() { nav.classList.remove('open'); backdrop.classList.remove('open'); }
        if (burger) burger.addEventListener('click', openNav);
        if (closeBtn) closeBtn.addEventListener('click', closeNav);
        if (backdrop) backdrop.addEventListener('click', closeNav);

        // Hide the cart badge while it reads 0. The count is written by the
        // AJAX in layouts/frontend, so watch for changes rather than coupling
        // to it — keeps the badge accurate without editing that script.
        document.querySelectorAll('.fd-icon-btn .qty').forEach(function (badge) {
            var sync = function () {
                var n = parseInt((badge.textContent || '').trim(), 10);
                badge.classList.toggle('is-zero', !n || isNaN(n));
            };
            sync();
            new MutationObserver(sync).observe(badge, { childList: true, characterData: true, subtree: true });
        });
    })();
</script>
