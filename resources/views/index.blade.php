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
        </style>
    </head>
    <body>
        <div class="container">
            <form method="POST" enctype="multipart/form-data" action="/process">
                <h3>Задание 1</h3>
                @csrf
                <label for="file">
                    <span>Выберите файл: </span>
                    <input type="file" name="file" id="file">
                </label>
                <br>
                <input type="submit" value="Загрузить и обработать">
            </form>
        </div>
    </body>
</html>
