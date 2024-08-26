@php
$advertise = App\Models\Frontend::where('data_keys','advertise.element')->inRandomOrder()->limit(1)->first();
@endphp
@if($advertise)
<div class="banner-promo">
    <a href="{{ __(@$advertise->data_values->url) }}">
        <img src="{{ getImage('assets/images/frontend/advertise/'.@$advertise->data_values->image,'1880x302') }}" alt="banner">
    </a>
</div>
@endif