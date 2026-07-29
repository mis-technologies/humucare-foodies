@extends($activeTemplate.'layouts.frontend')
@section('content')

    @include($activeTemplate.'sections.banner')

    @include($activeTemplate.'sections.menu')

    @include($activeTemplate.'sections.specials')

    @include($activeTemplate.'sections.special_request')

    @include($activeTemplate.'sections.why_choose')

@endsection
