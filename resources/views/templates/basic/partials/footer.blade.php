@php
$footerAddress = getContent('contact_us.content',true);
$paymentOption = getContent('footer.element',false,null,true);
$footerContent = getContent('footer.content',true);
$socialIcons = getContent('social_icon.element',false,null,true);
$categoryList = App\Models\Category::where('status',1)->with('subcategories')->latest()->limit(6)->get();
$policyPages = getContent('policy_pages.element', false, null, true);
@endphp
@include($activeTemplate.'partials.footer.footer_top')

<footer>

    <div style="position: fixed; bottom: 20px; left: 20px; z-index: 999;">


        <a href="tel:+4401214484501"
        style="
         height: 50px;
         padding: 10px;
         background-color: rgb(225, 225, 225);
         border-radius: 20px;"
        class="{{ menuActive('track-order') }}">
            @lang('Call For Party Services')  <i class="fa fa-phone"></i>
        </a>
     </div>
    @if ($categoryList->count() > 0)
    <div class="footer-top">
        <div class="container">
            <div class="footer__wrapper">
                @foreach ($categoryList as $category)
                <div class="footer__widget">
                    <h6 class="title">{{ __($category->name) }}</h6>
                    <ul>
                        @foreach ($category->subcategories->take(4) as $subcategory)
                        <li>
                            <a href="{{ route('subcategory.products',['id'=>$subcategory->id,'name'=>slug($subcategory->name)]) }}">
                                {{ __($subcategory->name) }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
    <div class="container">
        <div class="footer-bottom">
            <div class="footer__wrapper">
                <div class="footer__bottom__widget">
                    <h6 class="title">@lang('Our Address')</h6>
                    <p>{{ __(@$footerAddress->data_values->address) }}</p>
                </div>
                <div class="footer__bottom__widget">
                    <h6 class="title">@lang('Payment Methods')</h6>
                    <div class="d-flex flex-wrap">
                        @foreach ($paymentOption as $payment)
                        <div class="pay-img">
                            <img src="{{ getImage('assets/images/frontend/footer/'.@$payment->data_values->image,'70x40') }}" alt="payment">
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="footer__bottom__widget">
                    <h6 class="title">@lang('Subscribe Newsletter')</h6>
                    <p class="mb-4">{{ __(@$footerContent->data_values->subscribe_title) }}</p>
                    <form class="newletter-form">
                        <div class="input-group">
                            <input type="text" class="form-control subscribe-email" placeholder="@lang('Enter Your Email')" required>
                            <button type="submit" class="cmn--btn subscribe-btn"><i class="las la-paper-plane"></i></button>
                        </div>
                    </form>
                </div>
                <div class="footer__bottom__widget">
                    <h6 class="title">@lang('Connect With')</h6>
                    <p class="mb-3">{{ __(@$footerContent->data_values->connect_title) }}</p>
                    <ul class="social-icons justify-content-start">
                        @foreach ($socialIcons as $social)
                        <li>
                            <a href="{{ @$social->data_values->url }}">
                                @php echo $social->data_values->social_icon @endphp
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="bg--dark">
        <div class="container">
            <div class="copyright-area justify-content-beetween">
                <div class="copyright">
                    @lang('Copyright') &copy; @lang('All Right Reserved by')
                    <a href="{{ route('home') }}" class="text--base">{{__($general->sitename)}}</a>
                </div>
                <div class="policy-page">
                    @foreach ($policyPages as $policy)
                        <a href="{{ route('page.details', [$policy->id, slug($policy->data_values->title)]) }}" class="text-white">{{ __(@$policy->data_values->title) }}{{ $loop->last ? '' : ',' }}</a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</footer>
