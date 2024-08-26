@extends($activeTemplate.'layouts.frontend')
@section('content')
<section class="pt-60 pb-60">
    <div class="container">
        <div class="row">
            <h6 class="text-center mb-5">{{ __($page->data_values->title) }}</h6>
        </div>
        <div class="row">
            @php echo $page->data_values->details @endphp
        </div>
    </div>
</section>
@endsection