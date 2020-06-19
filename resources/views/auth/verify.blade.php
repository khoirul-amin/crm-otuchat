<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CRM Otu!') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/custom.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme-orange.css') }}" rel="stylesheet">
    <link href="{{ asset('css/theme.css') }}" rel="stylesheet">
</head>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-8">
            <div class="card">
                @if (!$baru)
                    <div class="card-header">{{ __('Cek OTP') }}</div>
                    <div class="card-body">
                        <form method="POST" action="/otpvalidate">
                            @csrf
                            <div class="form-group row">
                                <label for="key" class="col-md-4 col-form-label text-md-right">{{ __('Key Authenticator') }}</label>

                                <div class="col-md-6">
                                    <input id="key" type="key" class="form-control" name="key" value="{{ old('key') }}" required autocomplete="off" autofocus>
                                    @if (Session::get('error'))
                                        <div class="alert mt-3 alert-warning" role="alert">
                                            {{Session::get('error')}}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        
                            <div class="form-group row">
                                <div class="col align-self-center">
                                    <button type="submit" class="btn btn-orange mt-2">
                                        {{ __('Lanjutkan') }}
                                    </button>
                                    <a href="/logout" class="btn btn-success mt-2">
                                        {{ __('Kembali') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                            
                    </div>
                @else
                    <div class="card-header">{{ __('Cek OTP Dan Reset Password') }}</div>
                    <div class="card-body">
                        <p align='center'>Untuk pertama kali melakukan <b>LogIn</b>. Anda diwaajibkan untuk reset <b>Password</b></p>
                        
                        <form method="POST" action="/resetpassword">
                            @csrf
                            <div class="form-group row">
                                <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password baru') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password" value="{{ old('password') }}" required autocomplete="off" autofocus>
                                    
                                </div>
                            </div>
                            <div class="form-group row">
                                <label for="key" class="col-md-4 col-form-label text-md-right">{{ __('Key Authenticator') }}</label>
                                    @if (Session::get('error'))
                                        <div class="alert mt-3 alert-warning" role="alert">
                                            {{Session::get('error')}}
                                        </div>
                                    @endif
                                <div class="col-md-6">
                                    <input id="key" type="number" class="form-control" name="key" value="{{ old('key') }}" required autocomplete="off" autofocus>
                                </div>
                            </div>

                            <div class="form-group row">
                                <div class="col align-self-center">
                                    <button type="submit" class="btn btn-orange mt-2">
                                        {{ __('Lanjutkan') }}
                                    </button>
                                    <a href="/logout" class="btn btn-success mt-2">
                                        {{ __('Kembali') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

</html>