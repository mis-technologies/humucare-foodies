@php
$services = getContent('service.element', false, null, true);
@endphp
<div class="tos-links-section pt-60 pb-60 bg-white">
    <div class="container">
        <div class="tos-links row gy-4">
            @foreach ($services as $service)
            <div class="col-lg-3 col-md-6">
                <a href="javascript:void(0)">
                    <div class="icon">
                        <img src="{{ getImage('assets/images/frontend/service/'.$service->data_values->image,'50x50') }}" alt="icon">
                    </div>
                    <div class="content">
                        <span class="subtitle">{{ __($service->data_values->title) }}</span>
                        <p>{{ __($service->data_values->short_detail) }}</p>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    </div>
</div>