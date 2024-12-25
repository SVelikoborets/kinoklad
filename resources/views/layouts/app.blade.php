<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Собрание лучшего семейного кино.</title>
    <meta name="description" content="КиноКлад - фильмы для всей семьи. Вместе смотреть интереснее!">
    <meta name="keywords" content="фильмы, кино, семейное кино">
    <link rel="icon" href="{{ asset('assets/images/film-icon.png') }}" type="image/png" style="height: 16px;width: 16px">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
</head>
<body>
<header>
    <nav class="navbar navbar-light bg-light">
        <a class="navbar-brand d-flex align-items-center" href="{{ route('movies.index') }}">
            <div class="film-icon mr-2"></div>
            <span>КиноКлад</span>
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Вход</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Регистрация</a>
                    </li>
                @else
                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();
                                document.getElementById('logout-form').submit();">
                                {{ __('Выход') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </nav>
</header>
<div class="container-fluid main-section">
    <div class="container">
        <div class="row align-items-center justify-content-center justify-content-md-start">
            <div class="col-md-9 main-text text-center text-md-left">
                <h2>Зима -  </h2>
                <p class="lead">Время уютных семейных вечеров <span class="star-icon"></span><br>
                    - Как провести их вместе? <br>
                    - Скорее открывайте КиноКлад! <br>
                </p>
            </div>
            <div class="col-md-3 justify-content-center d-flex justify-content-md-end">
                <div class="clad">
                    <img src="{{ asset('assets/images/clad2.png') }}" alt="Клад"
                         style="height: 250px; object-fit: cover;">
                </div>
            </div>
        </div>
    </div>
</div>
<main>
    <div class="container-fluid mt-4 mb-5">
        @yield('content')
    </div>
</main>
<footer class="text-center text-lg-start">
    <div class="container p-4">
        <div class="row">
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                <h5 class="text-uppercase">КиноКлад</h5>
                <p>
                    Устрой волшебный кино-вечер для всей семьи!
                </p>
            </div>
            <div class="col-lg-6 col-md-12 mb-4 mb-md-0 ">
                <h5 class="text-uppercase">Контакты</h5>
                <a href="https://velikoborets-portfolio.ru"  style="color:white" >
                    <div class="link-icon mr-1"></div>
                    velikoborets-portfolio.ru
                </a>
            </div>
        </div>
    </div>
    <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.2);">
        2024 КиноКлад
    </div>
</footer>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

@yield('script')

</body>
</html>