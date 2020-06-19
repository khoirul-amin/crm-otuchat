@extends('layouts.app')
<section id="wrapper">
<div class="login-register" style="background-image:url({{ asset('assets') }}/images/background/login-register.jpg);">
    <div class="login-box card">
        <div class="card-body">
            <div class="form-horizontal form-material">
                {{-- @csrf --}}
                <h3 class="box-title m-b-20">Sign In</h3>
                <div class="form-group ">
                    <div class="col-xs-12">
                        <input id="email" type="email" placeholder="Email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="off" autofocus>

                    </div>
                </div>
                <div class="form-group">
                    <div class="col-xs-12">
                        <input id="password" placeholder="Password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-12">
                        <div class="checkbox checkbox-primary pull-left p-t-0">
                            <input id="checkbox-signup" type="checkbox">
                            <label for="checkbox-signup"> Remember me </label>
                        </div> 
                            <a href="javascript:void(0)" id="to-recover" class="text-dark pull-right">
                                    {{-- <i class="fa fa-lock m-r-5"></i> Forgot pwd? --}}
                            </a> 
                    </div>
                </div>
                <div class="form-group text-center m-t-20">
                    <div class="col-xs-12">
                        <button id="buttonLogin" class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" onclick="login()">Log In</button>
                        {{-- <button class="btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light" onclick="passwordBaru()">fdherh</button> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="loading" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="loadingLabel" aria-hidden="true">
    <div class="modal-dialog  modal-dialog-centered" role="document">
        <div class="col-sm-12" align="center">
            <div class="spinner-border text-danger" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
</div>

</section>

@push('scripts')
<script>
    $("#password").keyup(function(event){
        if(event.keyCode == 13){
            $("#buttonLogin").click();
        }
    });
</script>
<script>
    // function hideModal(){
    //     $('#loading').modal('hide')
    // }
    function login(){
        email = $('#email').val();
        password = $('#password').val();
        // data = $('#loginform').serialize()
        
        if(email && password){
            // $('#loading').modal('show')
            $.ajax({
                url: "/loginuser",
                method: 'POST',
                dataType: 'json',
                data: {
                    "_token": "<?= csrf_token()?>",
                    email : email,
                    password : password
                },
                success: function (data) {
                    if (data.status) {
                        if(data.status === 'olduser'){
                            // hideModal()
                            scanKey()
                        }else{
                            showKey(data.QR_Image, data.secret)
                        }
                    }
                    else {
                        Swal.fire({
                            title : "LOGIN GAGAL", 
                            text : data.message, 
                            icon: "error"
                        }).then((result) => {
                            if(result.value){
                                $('#loading').modal('hide')
                            }
                        });
                    }
                }
            });
        }else{
            Swal.fire("LOGIN GAGAL", "Lengkapi data terlegih dahulu", "error")
        }
        
    }

    function scanKey(){
        Swal.fire({
            title: 'Masukkan Key!',
            icon: 'warning',
            text: "Silahkan masukkan key authenticator anda",
            input: 'text',
            confirmButtonColor: "#47A447",
            confirmButtonText: "Oke",
            cancelButtonText: "Batal",
            allowOutsideClick: false,
            showCancelButton: true,
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url:"/otpvalidate",
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        "_token": "<?= csrf_token()?>",
                        key: result.value
                    },
                    success: function (data) {
                        // $('#loading').modal('show')
                        if (data.status) {
                            $('#loading').modal('hide')
                            Swal.fire({
                                title: 'Login Berhasil',
                                text:  data.message,
                                icon: 'success',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'Ok',
                                allowOutsideClick: false,
                            }).then((result) => {
                                // $('#loading').modal('show')
                                if (result.value) {
                                    window.location = '/home'
                                }
                            })
                        }
                        else {
                            Swal.fire({
                                title: 'Login Gagal',
                                text:  data.message,
                                icon: 'warning',
                                showCancelButton: false,
                                confirmButtonColor: '#3085d6',
                                allowOutsideClick: false,
                                confirmButtonText: 'Ok'
                            }).then((result) => {
                                $('#loading').modal('hide')
                                if (result.value) {
                                    scanKey() 
                                }
                            })
                        }
                    }
                });
            }else{
                $.ajax({
                    type:"GET",
                    "url" : "logout_v2",
                    cache: false,
                    success: function(data){
                        window.location = "/"
                    }
                })
            }
        });
    }

    function showKey(QR_Image,secret){
        Swal.fire({
            title: 'Authenticator',
            text: 'Alternatif lain anda bisa menggunakan '+secret,
            imageUrl: QR_Image,
            allowOutsideClick: false,
            confirmButtonColor: "#47A447",
            showCancelButton: false,
        }).then((result) => {
            passwordBaru()
        });
    }

    function passwordBaru(){
        Swal.mixin({
            input: 'text',
            confirmButtonText: 'Next &rarr;',
            showCancelButton: true,
            progressSteps: ['1', '2'],
            allowOutsideClick: false,
        }).queue([
            {
                title: 'Masukkan password baru',
                text: 'Untuk pertama login mohon masukkan password baru'
            },
            {
                title : 'Masukkan key authenticator',
                text : 'Masukkan key authenticator anda'
            }
        ]).then((result) => {
            if (result.value) {
                const data = result.value
                nextOTP(data)
            }else{
                $.ajax({
                    type:"GET",
                    "url" : "logout_v2",
                    cache: false,
                    success: function(data){
                        window.location = "/"
                    }
                })
            }
        })
    }

    function nextOTP(otp){
        $('#loading').modal('show')
        $.ajax({
            url:"/resetpassword",
            method: 'POST',
            dataType: 'json',
            data: {
                "_token": "<?= csrf_token()?>",
                password: otp[0],
                key: otp[1]
            },
            success: function (data) {
                if (data.status) {
                    // $('#loading').modal('hide')
                    Swal.fire({
                        title: 'Login Berhasil',
                        text:  data.message,
                        icon: 'success',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'Ok',
                        allowOutsideClick: false,
                    }).then((result) => {
                        $('#loading').modal('show')
                        if (result.value) {
                            window.location = '/home'
                        }
                    })
                }
                else {
                    Swal.fire({
                        title: 'Login Gagal',
                        text:  data.message,
                        icon: 'warning',
                        showCancelButton: false,
                        confirmButtonColor: '#3085d6',
                        allowOutsideClick: false,
                        confirmButtonText: 'Ok'
                    }).then((result) => {
                        // $('#loading').modal('hide')
                        if (result.value) {
                            passwordBaru() 
                        }
                    })
                }
            }
        });
    }
</script>
@endpush
