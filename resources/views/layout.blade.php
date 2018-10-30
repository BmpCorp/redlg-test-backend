<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Backend test</title>

        <style type="text/css">
            .container {
                width: 100%;
            }

            form {
                border: 1px solid black;
                width: 400px;
                margin: 10px auto;
                padding: 10px;
            }

            .error {
                color: red;
            }
        </style>
    </head>
    <body>
        @yield('content')
    </body>
</html>
