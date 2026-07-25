@extends('admin.layouts.app')
@section('panel')

<form action="{{ route('admin.ordering.update') }}" method="POST">
    @csrf
    <div class="row">
        {{-- master switches --}}
        <div class="col-lg-4">
            <div class="card b-radius--10 mb-4">
                <div class="card-header"><h5 class="card-title mb-0">@lang('Order Availability')</h5></div>
                <div class="card-body">
                    <div class="form-group d-flex align-items-center justify-content-between">
                        <label class="mb-0"><b>@lang('Accepting orders')</b><br>
                            <small class="text-muted">@lang('Master switch. Turn off to pause all ordering.')</small>
                        </label>
                        <input type="checkbox" data-width="80" data-onstyle="success" data-toggle="toggle"
                            data-on="@lang('On')" data-off="@lang('Off')" name="accepting_orders" value="1"
                            @checked($config['accepting_orders'])>
                    </div>
                    <hr>
                    <div class="form-group d-flex align-items-center justify-content-between">
                        <label class="mb-0"><b>@lang('Delivery')</b></label>
                        <input type="checkbox" data-width="80" data-toggle="toggle" data-on="@lang('On')"
                            data-off="@lang('Off')" name="delivery_enabled" value="1" @checked($config['delivery_enabled'])>
                    </div>
                    <div class="form-group d-flex align-items-center justify-content-between mb-0">
                        <label class="mb-0"><b>@lang('Collection / Pickup')</b></label>
                        <input type="checkbox" data-width="80" data-toggle="toggle" data-on="@lang('On')"
                            data-off="@lang('Off')" name="collection_enabled" value="1" @checked($config['collection_enabled'])>
                    </div>
                    <small class="text-muted d-block mt-3">
                        <i class="las la-info-circle"></i> @lang('At least one of Delivery or Collection must stay on.')
                    </small>
                </div>
            </div>
        </div>

        {{-- opening hours --}}
        <div class="col-lg-8">
            <div class="card b-radius--10 mb-4">
                <div class="card-header"><h5 class="card-title mb-0">@lang('Opening Hours')</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table--light style--two mb-0">
                            <thead>
                                <tr><th>@lang('Day')</th><th>@lang('Closed')</th><th>@lang('Opens')</th><th>@lang('Closes')</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($days as $key => $label)
                                @php $h = $config['hours'][$key]; @endphp
                                <tr>
                                    <td data-label="@lang('Day')"><b>{{ __($label) }}</b></td>
                                    <td data-label="@lang('Closed')">
                                        <input type="checkbox" name="hours[{{ $key }}][closed]" value="1"
                                            data-toggle="toggle" data-onstyle="danger" data-size="sm"
                                            data-on="@lang('Closed')" data-off="@lang('Open')" @checked($h['closed'])>
                                    </td>
                                    <td data-label="@lang('Opens')">
                                        <input type="time" class="form-control" name="hours[{{ $key }}][open]" value="{{ $h['open'] }}">
                                    </td>
                                    <td data-label="@lang('Closes')">
                                        <input type="time" class="form-control" name="hours[{{ $key }}][close]" value="{{ $h['close'] }}">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <small class="text-muted d-block mt-3">
                        <i class="las la-clock"></i>
                        @lang('A close time earlier than the open time is treated as past-midnight (e.g. 18:00 – 02:00).')
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="text-end">
        <button type="submit" class="btn btn--primary w-100">@lang('Save Settings')</button>
    </div>
</form>
@endsection
