<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Time-Based Authentication like Google Authenticator</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <meta name="description" content="Implement Google like Time-Based Authentication into your existing PHP application. And learn How to Build it? How it Works? and Why is it Necessary these days."/>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
    <link rel='shortcut icon' href='/favicon.ico'  />
    <style>
        body,html {
            height: 100%;
        }       


        .bg { 
            /* The image used */
            background-image: url("images/bg.jpg");
            /* Full height */
            height: 100%; 
            /* Center and scale the image nicely */
            background-position: center;
            background-repeat: no-repeat;
           
            background-size: cover;
        }
    </style>
</head>
<body  class="bg">
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
                            
                            <form method="POST" action="/example/coba_key">
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
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>