{{-- Shared by the home-page modal and the standalone /special-request page.
     Both post to the same route; the modal submits over AJAX. --}}
@php
    $types = \App\Models\SpecialRequest::types();
    // marks a field red when it comes back from a failed non-AJAX submit;
    // the modal adds the same class client-side from the 422 payload
    $err = fn ($field) => $errors->has($field) ? ' fd-field--error' : '';
@endphp

<form class="fd-req__form" action="{{ route('special.request.store') }}" method="POST">
    @csrf

    {{-- honeypot: hidden from people, irresistible to bots. Server rejects any
         submission where this is filled. --}}
    <div class="fd-req__hp" aria-hidden="true">
        <label>@lang('Website')</label>
        <input type="text" name="website" tabindex="-1" autocomplete="off">
    </div>

    <div class="fd-req__grid">
        <div class="fd-field{{ $err('name') }}">
            <label for="sr-name">@lang('Your name') <span>*</span></label>
            <input type="text" id="sr-name" name="name" value="{{ old('name', optional(auth()->user())->fullname) }}"
                placeholder="@lang('e.g. Amara Okoye')" required>
        </div>

        <div class="fd-field{{ $err('phone') }}">
            <label for="sr-phone">@lang('Phone number') <span>*</span></label>
            <input type="text" id="sr-phone" name="phone" value="{{ old('phone', optional(auth()->user())->mobile) }}"
                placeholder="@lang('So we can call you back')" required>
        </div>

        <div class="fd-field fd-field--full{{ $err('email') }}">
            <label for="sr-email">@lang('Email address') <span>*</span></label>
            <input type="email" id="sr-email" name="email" value="{{ old('email', optional(auth()->user())->email) }}"
                placeholder="@lang('We send your quote here')" required>
        </div>

        <div class="fd-field{{ $err('item') }}">
            <label for="sr-item">@lang('What would you like?') <span>*</span></label>
            <input type="text" id="sr-item" name="item" value="{{ old('item') }}"
                placeholder="@lang('e.g. Jollof rice, Egusi soup')" required>
        </div>

        <div class="fd-field{{ $err('quantity') }}">
            <label for="sr-quantity">@lang('How much?') <span>*</span></label>
            <input type="text" id="sr-quantity" name="quantity" value="{{ old('quantity') }}"
                placeholder="@lang('e.g. 5 litres, 2 large trays')" required>
        </div>

        <div class="fd-field{{ $err('type') }}">
            <label for="sr-type">@lang('Kind of request') <span>*</span></label>
            <select id="sr-type" name="type" required>
                @foreach ($types as $key => $label)
                    {{-- plain ternary: the @selected directive is Laravel 9+ --}}
                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>@lang($label)</option>
                @endforeach
            </select>
        </div>

        <div class="fd-field{{ $err('needed_on') }}">
            <label for="sr-date">@lang('When do you need it?')</label>
            <input type="date" id="sr-date" name="needed_on" value="{{ old('needed_on') }}"
                min="{{ now()->toDateString() }}">
        </div>

        <div class="fd-field{{ $err('people') }}">
            <label for="sr-people">@lang('How many people?')</label>
            <input type="number" id="sr-people" name="people" value="{{ old('people') }}" min="1" max="5000"
                placeholder="@lang('Optional')">
        </div>

        <div class="fd-field{{ $err('budget') }}">
            <label for="sr-budget">@lang('Your budget') ({{ $general->cur_sym }})</label>
            <input type="number" id="sr-budget" name="budget" value="{{ old('budget') }}" min="0" step="0.01"
                placeholder="@lang('Optional')">
        </div>

        <div class="fd-field fd-field--full{{ $err('details') }}">
            <label for="sr-details">@lang('Anything else we should know?')</label>
            <textarea id="sr-details" name="details" rows="4"
                placeholder="@lang('Allergies, spice level, delivery address, collection time…')">{{ old('details') }}</textarea>
        </div>
    </div>

    <div class="fd-req__actions">
        <button type="submit" class="fd-btn fd-btn--lg">
            <i class="las la-paper-plane"></i> @lang('Send my request')
        </button>
        <p class="fd-req__note">
            <i class="las la-lock"></i>
            @lang('No payment now — we will send you a price first.')
        </p>
    </div>
</form>
