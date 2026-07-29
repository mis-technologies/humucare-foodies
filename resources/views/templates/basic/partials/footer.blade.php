@php
    $footerAddress = getContent('contact_us.content', true);
    $paymentOption = getContent('footer.element', false, null, true);
    $footerContent = getContent('footer.content', true);
    $socialIcons = getContent('social_icon.element', false, null, true);
    $categoryList = App\Models\Category::where('status', 1)->with('subcategories')->latest()->limit(6)->get();
    $policyPages = getContent('policy_pages.element', false, null, true);
@endphp

@include($activeTemplate.'partials.footer.footer_top')

<footer class="fd-footer">
    <div class="fd-container fd-footer__main">
        <div class="fd-footer__brand">
            <img src="{{ getImage(imagePath()['logoIcon']['path'] . '/foodie-logo.png') }}" alt="{{ __($general->sitename) }}">
            <p>@lang('Freshly cooked, chef-made meals delivered to your door. Enjoy your online food shopping with') {{ __($general->sitename) }}.</p>
            @if ($socialIcons && count($socialIcons) > 0)
            <ul class="social-icons">
                @foreach ($socialIcons as $social)
                    <li><a href="{{ @$social->data_values->url }}">@php echo $social->data_values->social_icon @endphp</a></li>
                @endforeach
            </ul>
            @endif
        </div>

        @if ($categoryList->count() > 0)
        <div>
            <h6 class="fd-footer__title">@lang('Menu')</h6>
            <ul class="fd-footer__links">
                @foreach ($categoryList->take(5) as $category)
                    <li><a href="{{ route('category.products', ['id' => $category->id, 'name' => slug($category->name)]) }}">{{ __($category->name) }}</a></li>
                @endforeach
            </ul>
        </div>
        @endif

        <div>
            <h6 class="fd-footer__title">@lang('Company')</h6>
            <ul class="fd-footer__links">
                <li><a href="{{ route('contact') }}">@lang('Contact Us')</a></li>
                <li><a href="{{ route('special.request') }}">@lang('Catering &amp; Bulk Orders')</a></li>
                <li><a href="{{ route('track-order') }}">@lang('Track Order')</a></li>
                <li><a href="{{ route('wishlist') }}">@lang('My Wishlist')</a></li>
                @foreach ($policyPages as $policy)
                    <li><a href="{{ route('page.details', [$policy->id, slug($policy->data_values->title)]) }}">{{ __(@$policy->data_values->title) }}</a></li>
                @endforeach
            </ul>
        </div>

        <div>
            <h6 class="fd-footer__title">@lang('Subscribe Newsletter')</h6>
            <p style="font-size:14px;margin-bottom:14px">{{ __(@$footerContent->data_values->subscribe_title) }}</p>
            <form class="newletter-form">
                <div class="input-group">
                    <input type="text" class="form-control subscribe-email" placeholder="@lang('Enter Your Email')" required>
                    <button type="submit" class="cmn--btn subscribe-btn"><i class="las la-paper-plane"></i></button>
                </div>
            </form>
            @if (@$footerAddress->data_values->address)
            <p style="font-size:13px;margin-top:18px;color:rgba(255,255,255,.55)">
                <i class="las la-map-marker-alt"></i> {{ __(@$footerAddress->data_values->address) }}
            </p>
            @endif
            @if ($paymentOption && count($paymentOption) > 0)
            <div class="d-flex flex-wrap mt-3">
                @foreach ($paymentOption as $payment)
                    <div class="pay-img"><img src="{{ getImage('assets/images/frontend/footer/' . @$payment->data_values->image, '70x40') }}" alt="payment"></div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="fd-container">
        <div class="fd-footer__bottom">
            <div>@lang('Copyright') &copy; @lang('All Rights Reserved by') <a href="{{ route('home') }}">{{ __($general->sitename) }}</a></div>
            <div class="policy-page">
                @foreach ($policyPages as $policy)
                    <a href="{{ route('page.details', [$policy->id, slug($policy->data_values->title)]) }}">{{ __(@$policy->data_values->title) }}{{ $loop->last ? '' : ' ·' }}</a>
                @endforeach
            </div>
        </div>
    </div>
</footer>

<a href="tel:+44 7485 705519" class="fd-call-fab">
    <i class="las la-phone"></i> @lang('Call for Party Services')
</a>
