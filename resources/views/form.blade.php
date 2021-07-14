<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Создать клиента</title>

        <link rel="stylesheet" href="{{ asset('app.css')}}">

    </head>
    <body >
        <div class="container">
            <div class="page_header">
                <h1>
                    Создать клиента
                </h1>
            </div>
            @if (session()->has('errors'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach ( $errors->all() as $error )
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @elseif(session()->has('success'))
                <div class="alert alert-success">
                    <ul>
                        <li>{{ session()->get('success') }}</li>
                    </ul>
                </div>
            @endif
            <div class="form_wrapper">
                <form action="{{ route('contact.store') }}" method="post" class="form">
                    @csrf
                    <div class="form_item">
                        <label for="name">
                            Имя:
                            <input type="text" id="name" name="name">
                        </label>
                    </div>
                    <div class="form_item">
                        <label for="name">
                            Email:
                            <input type="text" id="email" name="email">
                        </label>
                    </div>
                    <div class="form_item">
                        <label for="name">
                            Телефон:
                            <input type="text" id="name" name="phone">
                        </label>
                    </div>
                    <div class="form_item">
                        <input type="submit" value="Сохранить">
                    </div>
                </form>
            </div>
        </div>
    </body>
</html>
