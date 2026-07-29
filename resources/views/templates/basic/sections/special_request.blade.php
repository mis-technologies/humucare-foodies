{{-- "Need something bigger?" — bulk portions and party catering are quotes,
     not menu items, so this collects an enquiry instead of adding to the cart. --}}
<section class="fd-section fd-req-cta">
    <div class="fd-container">
        <div class="fd-req-cta__inner">
            <div class="fd-req-cta__copy">
                <span class="fd-req-cta__kicker">@lang('Catering &amp; bulk orders')</span>
                <h2 class="fd-req-cta__title">@lang('Need a litre of soup, or rice for fifty?')</h2>
                <p class="fd-req-cta__sub">
                    @lang('Tell us what you need and how much — parties, office lunches, family gatherings. We will cook it to order and send you a price. No payment until you say yes.')
                </p>

                <ul class="fd-req-cta__list">
                    <li><i class="las la-check-circle"></i> @lang('Any dish, any quantity')</li>
                    <li><i class="las la-check-circle"></i> @lang('Free quote within 24 hours')</li>
                    <li><i class="las la-check-circle"></i> @lang('Delivery or collection')</li>
                </ul>

                <div class="fd-req-cta__actions">
                    <button type="button" class="fd-btn fd-btn--lg" data-bs-toggle="modal"
                        data-bs-target="#specialRequestModal">
                        <i class="las la-utensils"></i> @lang('Request a special offer')
                    </button>
                    <a href="{{ route('special.request') }}" class="fd-btn fd-btn--lg fd-btn--ghost">
                        @lang('See how it works')
                    </a>
                </div>
            </div>

            <div class="fd-req-cta__cards" aria-hidden="true">
                <div class="fd-req-cta__card">
                    <span class="fd-req-cta__emoji">🍲</span>
                    <strong>@lang('1 litre of soup')</strong>
                    <small>@lang('Egusi, Okra, Pepper soup')</small>
                </div>
                <div class="fd-req-cta__card fd-req-cta__card--lift">
                    <span class="fd-req-cta__emoji">🍚</span>
                    <strong>@lang('A tray of Jollof')</strong>
                    <small>@lang('Serves 20–50 guests')</small>
                </div>
                <div class="fd-req-cta__card">
                    <span class="fd-req-cta__emoji">🎉</span>
                    <strong>@lang('Full party menu')</strong>
                    <small>@lang('Built around your budget')</small>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- The modal lives outside the section so the dark background never clips it --}}
<div class="modal fade fd-req-modal" id="specialRequestModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="fd-req-modal__head">
                <div>
                    <h3>@lang('Request a special offer')</h3>
                    <p>@lang('Fill this in and we will get straight back to you with a price.')</p>
                </div>
                <button type="button" class="fd-req-modal__close" data-bs-dismiss="modal" aria-label="@lang('Close')">
                    <i class="las la-times"></i>
                </button>
            </div>
            <div class="modal-body">
                @include($activeTemplate . 'partials.special_request_form')
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        'use strict';
        (function ($) {
            // Submit over AJAX so the customer never loses the home page.
            // The standalone /special-request page posts normally.
            $(document).on('submit', '#specialRequestModal .fd-req__form', function (e) {
                e.preventDefault();

                var $form = $(this);
                var $btn = $form.find('button[type=submit]');
                var original = $btn.html();

                $form.find('.fd-field--error').removeClass('fd-field--error');
                $btn.prop('disabled', true).html('<i class="las la-spinner la-spin"></i> @lang('Sending…')');

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: $form.serialize(),
                    success: function (response) {
                        notify('success', response.success);
                        $form[0].reset();
                        $('#specialRequestModal').modal('hide');
                    },
                    error: function (xhr) {
                        var errors = (xhr.responseJSON || {}).errors || {};
                        var first = null;

                        $.each(errors, function (field, messages) {
                            $form.find('[name="' + field + '"]').closest('.fd-field').addClass('fd-field--error');
                            if (first === null) { first = messages[0]; }
                        });

                        notify('error', first || 'Something went wrong. Please try again.');
                    },
                    complete: function () {
                        $btn.prop('disabled', false).html(original);
                    }
                });
            });
        })(jQuery);
    </script>
@endpush
