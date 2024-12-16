@extends('layouts.app')
@section('title','КиноКлад')
@section('content')
    <div class="container">
        @if(session()->has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif
        @auth
            @if(!auth()->user()->hasVerifiedEmail())
                <div class="row justify-content-center mb-4">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">{{ __('Verify Your Email Address') }}</div>

                            <div class="card-body">
                                @if (session('resent'))
                                    <div class="alert alert-success" role="alert">
                                        {{ __('A fresh verification link has been sent to your email address.') }}
                                    </div>
                                @endif

                                {{ __('Before proceeding, please check your email for a verification link.') }}
                                {{ __('If you did not receive the email') }},
                                <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                                    @csrf
                                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">
                                        {{ __('click here to request another') }}
                                    </button>.
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endauth
        <div class="row">
            <div class="filter-section mb-4 col-12">
                <form id="searchForm" method="POST" action="{{ route('movies.search') }}">
                    @csrf
                    <div class="form-row d-flex">
                        <div class="col">
                            <div class="select-wrap">
                                <select name="year" id="year" class="custom-select @error('year') is-invalid @enderror" onchange="toggleCloseButton(this)">
                                    <option value="">Год</option>
                                    @foreach ($years as $year)
                                        <option value="{{ $year }}" @if($selectedYear == $year) selected @endif >
                                            {{ $year }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button"
                                        class="close-btn"
                                        aria-label="Close"
                                        style="display: none;">
                                    <span class="close-icon">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="col mx-lg-3 mx-sm-0">
                            <div class="select-wrap">
                                <select name="country" id="country" class="custom-select @error('country') is-invalid @enderror" onchange="toggleCloseButton(this)">
                                    <option value="">Страна</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country }}" @if($selectedCountry == $country) selected @endif>
                                            {{ $country }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="button"
                                        class="close-btn"
                                        aria-label="Close"
                                        style="display: none;">
                                    <span class="close-icon">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="col mx-lg-2 mx-sm-0">
                            <div class="select-wrap">
                                <select name="rating" id="rating" class="custom-select @error('rating') is-invalid @enderror" onchange="toggleCloseButton(this)">
                                    <option value="">Рейтинг от:</option>
                                    @for ($i = 1; $i < 10; $i++)
                                        <option value="{{ $i }}" @if($selectedRating == $i) selected @endif>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                                <button type="button"
                                        class="close-btn"
                                        aria-label="Close"
                                        style="display: none;">
                                    <span class="close-icon">&times;</span>
                                </button>
                            </div>
                        </div>
                        <div class="col ml-lg-2">
                            <button type="submit" class="btn btn-gradient w-100"> Найти </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="movies-section col-12">
                <div class="pagination justify-content-center mb-2">
                    {{ $movies->onEachSide(1)->links('vendor.pagination.custom') }}
                </div>
                <div class="row">
                    @forelse($movies as $movie)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card h-100">
                                <div class="position-relative">
                                    <div class="position-absolute ratings">
                                        <small class="text-white d-flex align-items-center rating mb-1">
                                            <div class="kinopoisk-logo mr-2"></div>
                                            {{ $movie->rating }}
                                        </small>
                                        @if($movie->ratings_avg_rating !== null && $movie->ratings_avg_rating > 0)
                                            <small class="d-flex align-items-center rating-orange">
                                                <div class="film-icon mr-2"></div>
                                                {{ number_format($movie->ratings_avg_rating, 1) }}
                                            </small>
                                        @endif
                                    </div>
                                    <a href="{{ route('movies.show', $movie->slug) }}">
                                        <img class="card-img-top img-fluid poster"
                                             src="{{ asset('storage/posters/'.$movie->kinopoisk_id.'.jpg') }}"
                                             alt="Постер фильма">
                                    </a>
                                </div>
                                <div class="card-body d-flex flex-column">
                                    <a href="{{ route('movies.show', $movie->slug) }}">
                                        <h5 class="card-title">{{ $movie->title }}</h5>
                                    </a>
                                    <p class="card-text">{{ Str::limit($movie->description, 100) }}</p>
                                    <div class="mt-auto">
                                        <a href="{{ route('movies.show', $movie->slug) }}" class="btn btn-movie btn-block">
                                            Подробнее
                                        </a>
                                    </div>
                                </div>
                                <div class="card-footer d-flex align-items-center justify-content-around">
                                    <small class="text-muted">
                                        {{ $movie->year }}
                                    </small>
                                    <div class="globe-icon"></div>
                                    <small class="text-muted ">
                                        {{ $movie->country }}
                                    </small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty col-12">
                            <h4 class="text-center">Фильмы не найдены. Попробуйте другие параметры поиска...</h4>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="pagination justify-content-center">
            {{ $movies->onEachSide(1)->links('vendor.pagination.custom') }}
        </div>
    </div>
@endsection
@section('script')
    <script src="{{ asset('assets/js/filters.js') }}" defer></script>
@endsection
