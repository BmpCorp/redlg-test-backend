@extends('layout')

@section('content')
    <div class="container">
        <form method="POST" enctype="multipart/form-data" action="/process">
            <h3>@lang('task1.form_title')</h3>
            @csrf
            <label for="file">
                <span>@lang('task1.form_prompt')</span>
                <input type="file" name="file" id="file" required>
            </label>
            <br>
            <input type="submit" value="@lang('task1.form_button')">
        </form>
        <form method="POST" enctype="multipart/form-data" action="/ticket">
            <h3>@lang('task2.form_title')</h3>
            @csrf
            <label for="name">
                <span>@lang('task2.form_name')</span>
                <input type="text" name="name" id="name" required>
            </label>
            <br>
            <label for="email">
                <span>@lang('task2.form_email')</span>
                <input type="email" name="email" id="email" required>
            </label>
            <br>
            <label for="phone">
                <span>@lang('task2.form_phone')</span>
                <input type="tel" name="phone" id="phone">
            </label>
            <br>
            <label for="file">
                <span>@lang('task2.form_file')</span>
                <input type="file" name="file" id="file">
            </label>
            <br>
            <input type="submit" value="@lang('task2.form_button')">
        </form>
    </div>
@endsection
