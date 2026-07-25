@php
    $services = getContent('service.element', false, null, true);

    // Map a service to a Line Awesome icon: prefer an explicit `icon` stored on
    // the record, else infer one from the title, else a sensible default.
    $serviceIcon = function ($service) {
        if (!empty($service->data_values->icon)) {
            return $service->data_values->icon;
        }
        $title = strtolower(__($service->data_values->title ?? ''));
        return match (true) {
            str_contains($title, 'deliver')                                   => 'las la-shipping-fast',
            str_contains($title, 'fresh') || str_contains($title, 'ingredi')  => 'las la-leaf',
            str_contains($title, 'support') || str_contains($title, '24')     => 'las la-headset',
            str_contains($title, 'price') || str_contains($title, 'value')    => 'las la-tags',
            str_contains($title, 'secure') || str_contains($title, 'safe')    => 'las la-shield-alt',
            str_contains($title, 'pay')                                       => 'las la-credit-card',
            default                                                           => 'las la-concierge-bell',
        };
    };
@endphp
@if ($services && count($services) > 0)
<div class="fd-services">
    <div class="fd-container fd-services__grid">
        @foreach ($services as $service)
        <div class="fd-service">
            <span class="icon">
                <i class="{{ $serviceIcon($service) }}"></i>
            </span>
            <div>
                <span class="subtitle">{{ __($service->data_values->title) }}</span>
                <p>{{ __($service->data_values->short_detail) }}</p>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif
