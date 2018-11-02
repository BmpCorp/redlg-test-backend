@extends('layout')

@section('content')
    <div class="container">
        <h3>@lang('task1.process_results')</h3>
        <p class="{{ $bHasError ? 'error' : '' }}">{{ $sResultMessage }}</p>
        @if(isset($obJSONData))
            <p>@lang('task1.json_obtained')</p>
            <pre>{{ json_encode($obJSONData, JSON_PRETTY_PRINT) }}</pre>
        @endif
    </div>
@endsection
