@extends($activeTemplate . 'layouts.frontend')
@section('content')

    <section class="fd-section fd-req-page">
        <div class="fd-container">
            <div class="fd-shead fd-shead--left">
                <span class="fd-shead__kicker">@lang('Catering &amp; bulk orders')</span>
                <h2 class="fd-shead__title">@lang('Ask us for a special offer')</h2>
            </div>

            <div class="fd-req-page__cols">
                <div class="fd-req-page__form">
                    @include($activeTemplate . 'partials.special_request_form')
                </div>

                <aside class="fd-req-page__aside">
                    <h3>@lang('How it works')</h3>
                    <ol class="fd-req-steps">
                        <li>
                            <span>1</span>
                            <div>
                                <strong>@lang('Tell us what you need')</strong>
                                <p>@lang('Any dish on our menu — or one that is not — in any quantity.')</p>
                            </div>
                        </li>
                        <li>
                            <span>2</span>
                            <div>
                                <strong>@lang('We send you a price')</strong>
                                <p>@lang('Usually within 24 hours, by email and phone.')</p>
                            </div>
                        </li>
                        <li>
                            <span>3</span>
                            <div>
                                <strong>@lang('You confirm')</strong>
                                <p>@lang('Only then do you pay. Delivery or collection, your choice.')</p>
                            </div>
                        </li>
                    </ol>

                    <div class="fd-req-page__help">
                        <p>@lang('Prefer to talk it through?')</p>
                        @php $contact = getContent('contact_us.content', true); @endphp
                        @if (@$contact->data_values->contact_number)
                            <a href="tel:{{ $contact->data_values->contact_number }}" class="fd-btn fd-btn--light">
                                <i class="las la-phone"></i> {{ $contact->data_values->contact_number }}
                            </a>
                        @else
                            <a href="{{ route('contact') }}" class="fd-btn fd-btn--light">
                                <i class="las la-envelope"></i> @lang('Contact us')
                            </a>
                        @endif
                    </div>
                </aside>
            </div>
        </div>
    </section>

@endsection
