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
                <div class="card-header">{{ __('Verifikasi Key') }}</div>

                <div class="card-body">
                    <div class="panel-heading">Google Authenticator</div>
    ​
                    <div class="panel-body" style="text-align: center;">
                        @if ($QR_Image || $secret)
                        <p>Anda dapat menggunakan two factor authentication dengan melakukan scan pada barcode dibawah.<br/> Alternatif lainnya, anda dapat menggunakan code berikut: <b>{{ $secret ?? '' }}</b> </p>
                        <div>
                            <img src="{{ $QR_Image }}">
                        </div>
                        <p>Anda harus mengatur Google Authenticator app sebelum melanjutkan.</p>

                        <div class="form-group row">
                            <div class="col align-self-center">
                                <a class="btn btn-orange mt-2" href="/authenticate">
                                    {{ __('Lanjutkan Memasukkan Key') }}
                                </a>
                            </div>
                        </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.7.1/dist/sweetalert2.all.min.js"></script> --}}

</html>