@php
    $whyServices = getContent('service.element', false, null, true);
@endphp
<section class="fd-section fd-section--dark">
    <div class="fd-container">
        <div class="fd-why">
            <div>
                <h2 class="fd-why__title">@lang('Why you should choose Foodies')</h2>
                <p class="fd-why__sub">@lang('Foodies is the best place to satisfy all your cravings — fast, fresh and affordable.')</p>
                <ul class="fd-why__list">
                    @if ($whyServices && count($whyServices) > 0)
                        @foreach ($whyServices->take(5) as $service)
                            <li><span class="ic"><i class="las la-check"></i></span> {{ __($service->data_values->title) }}</li>
                        @endforeach
                    @else
                        <li><span class="ic"><i class="las la-check"></i></span> @lang('Time saving and affordable')</li>
                        <li><span class="ic"><i class="las la-check"></i></span> @lang('Freshly cooked meals')</li>
                        <li><span class="ic"><i class="las la-check"></i></span> @lang('Best ingredients used')</li>
                        <li><span class="ic"><i class="las la-check"></i></span> @lang('Different amazing delicacies')</li>
                        <li><span class="ic"><i class="las la-check"></i></span> @lang('Prepared to your taste')</li>
                    @endif
                </ul>
                <a href="{{ route('products') }}" class="fd-btn fd-btn--lg">@lang('Order Now') <i class="las la-arrow-right"></i></a>
            </div>
            <div class="fd-why__media">
                {{-- managed in Admin > Home Page Video; falls back to the
                     bundled clip when nothing has been uploaded --}}
                <video autoplay muted loop playsinline>
                    <source src="{{ homepageVideoUrl() }}" type="video/mp4">
                </video>
            </div>
        </div>
    </div>
</section>
