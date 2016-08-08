<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="{{ csrf_token() }}">

    <title>Музей института Нефти и Газа СКФУ</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">

    <!-- jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Styles -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">

    <link href='https://fonts.googleapis.com/css?family=Open+Sans:100,300,400,700&subset=latin,cyrillic' rel='stylesheet' type='text/css'>
    {{-- <link href="{{ elixir('css/app.css') }}" rel="stylesheet"> --}}
    <link rel="stylesheet" href="{{ URL::asset('assets/css/app.css') }}" />

    <style>
        body {
            font-family: 'Open Sans';
        }

        .fa-btn {
            margin-right: 6px;
        }
    </style>
</head>
<body id="app-layout" style="background-color: #f3f3f3">
    <nav class="navbar navbar-default navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <h1 class="logo">
                    <a class="navbar-brand" href="{{ url('/') }}">
                        <i class="fa fa-diamond fa-2" aria-hidden="true"></i> Главная
                    </a>
                </h1>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">
                <!-- Left Side Of Navbar -->
                <ul class="nav navbar-nav">
                    <li{{ Request::is('minerals') ? ' class=active' : null }}><a href="{{ url('/minerals') }}"> Все минералы</a></li>
                    @if (!is_null(request()->user()) AND request()->user()->is('admin|moderator|editor'))
                        <li{{ Request::is('minerals/create') ? ' class=active' : null }}><a href="{{ url('/minerals/create') }}"> Добавить минерал</a></li>
                    @endif
                    <li{{ Request::is('about') ? ' class=active' : null }}><a href="{{ url('/about') }}"> О нас</a></li>
                </ul>

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li{{ Request::is('login') ? ' class=active' : null }}><a href="{{ url('/login') }}">Вход</a></li>
                        <li{{ Request::is('register') ? ' class=active' : null }}><a href="{{ url('/register') }}">Регистрация</a></li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                @if (!is_null(request()->user()) AND request()->user()->isAdmin())
                                    <li{{ Request::is('admin') ? ' class=active' : null }}><a href="{{ url('/admin') }}"><i class="fa fa-btn fa-dashboard"></i>Панель администратора</a></li>
                                @endif
                                <li{{ Request::is('profile') ? ' class=active' : null }}><a href="{{ url('/profile') }}"><i class="fa fa-btn fa-user"></i>Мой профиль</a></li>
                                <li class="divider"></li>
                                <li><a href="{{ url('/logout') }}"><i class="fa fa-btn fa-sign-out"></i>Выйти</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @yield('content')

    <footer class="footer">
    <div style="background-color: #2e3038; width: 100%; min-height: 100%;">
        <div class="container">
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-4 footer-col">
                        <ul>
                            <li{{ Request::is('/') ? ' class=active' : null }}><a href="{{ url('/') }}">Главная</a></li>
                            <li{{ Request::is('minerals') ? ' class=active' : null }}><a href="{{ url('/minerals') }}">Все минералы</a></li>
                            @if (!is_null(request()->user()) AND request()->user()->is('admin|moderator|editor'))
                                <li{{ Request::is('minerals/create') ? ' class=active' : null }}><a href="{{ url('/minerals/create') }}">Добавить минерал</a></li>
                            @endif
                            <li{{ Request::is('about') ? ' class=active' : null }}><a href="{{ url('/about') }}">О нас</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4 footer-col">Вторая колонка<br>Some shit</div>
                    <div class="col-md-4 footer-col">Третья колонка<br>Some shit/path 2</div>
                </div>
            </div>
            <div class="row">
                <hr>
                <div class="col-md-10 col-md-offset-1">
                    <p>&copy; 2016 mooagi.ru - "Музей Института Нефти и Газа СКФУ". Все права защищены. Копирование, распространение или использование материала допускается только с разрешения администрации.</p>
                </div>
            </div>
        </div>
        </div>
    </footer>
    <!-- JavaScripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.3/jquery.min.js" integrity="sha384-I6F5OKECLVtK/BL+8iSLDEHowSAfUo76ZL9+kGAgTRdiByINKJaqTPH/QVNS1VDb" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/js/bootstrap.min.js" integrity="sha384-0mSbJDEHialfmuBBQP6A4Qrprq5OVfW37PRR3j5ELqxss1yVqOtnepnHVP9aJ7xS" crossorigin="anonymous"></script>
    {{-- <script src="{{ elixir('js/app.js') }}"></script> --}}
    <script>
    $(function () {
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') }
        });
    });
    </script>
</body>
</html>
