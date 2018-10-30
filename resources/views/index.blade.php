@extends('layout')

@section('content')
    <div class="container">
        <form method="POST" enctype="multipart/form-data" action="/process">
            <h3>@lang('task1.form_title')</h3>
            @csrf
            <label for="file">
                <span>@lang('task1.form_prompt')</span>
                <input type="file" name="file" id="file">
            </label>
            <br>
            <input type="submit" value="@lang('task1.form_button')">
        </form>
    </div>
@endsection
