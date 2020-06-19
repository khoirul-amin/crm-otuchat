@extends('layouts.app')

@section('content')
<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">Dashboard</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </div>
</div>
<div class="row">
    <!-- Column -->
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="round round-lg text-white d-inline-block text-center rounded-circle bg-info">
                        <i class="mdi mdi-cart-outline"></i>
                    </div>
                    <div class="ml-2 align-self-center">
                        <h3 class="mb-0 font-weight-light"> {{number_format($dataDashboard->today_trx)}} </h3>
                        <h5 class="text-muted mb-0">Transaksi Hari Ini</h5>
                    </div>
                </div>
                <hr class="mt-4 mb-2">
                <div>Rp  {{number_format($dataDashboard->pemakaian_sukses)}} Nominal Transaksi Sukses</div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="round round-lg text-white d-inline-block text-center rounded-circle bg-warning">
                        <i class="mdi mdi-cart-outline"></i></div>
                    <div class="ml-2 align-self-center">
                        <h3 class="mb-0 font-weight-light"> {{number_format($dataDashboard->jumlah_trx_refund)}}</h3>
                        <h5 class="text-muted mb-0">Transaksi Refund Hari Ini</h5>
                    </div>
                </div>
                <hr class="mt-4 mb-2">
                <div>Rp  {{number_format($dataDashboard->nilai_trx_refund)}} Nominal Transaksi Refund</div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="round round-lg text-white d-inline-block text-center rounded-circle bg-danger">
                        <i class="mdi mdi-cart-outline"></i></div>
                    <div class="ml-2 align-self-center">
                        <h3 class="mb-0 font-weight-light"> {{number_format($dataDashboard->jumlah_trx_gagal)}}</h3>
                        <h5 class="text-muted mb-0">Transaksi Gagal Hari Ini</h5>
                    </div>
                </div>
                <hr class="mt-4 mb-2">
                <div>Rp  {{number_format($dataDashboard->nilai_trx_gagal)}} Nominal Transaksi Gagal</div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-lg-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="d-flex flex-row">
                    <div class="round round-lg text-white d-inline-block text-center rounded-circle bg-success">
                        <i class="mdi mdi-bullseye"></i></div>
                    <div class="ml-2 align-self-center">
                        <h3 class="mb-0 font-weight-light"> {{number_format($dataDashboard->antrean_trx)}}</h3>
                        <h5 class="text-muted mb-0">Antrian Transaksi</h5>
                    </div>
                </div>
                <hr class="mt-4 mb-2">
                <div>Rp  {{number_format($dataDashboard->pemakaian_proses)}} Nominal Antrean Transaksi</div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>
<div class="row">
    <div class="col-md-6 col-lg-3">
        <!-- Column -->
        <div class="card"> <img src="{{ asset('assets') }}/images/background/profile-bg.jpg" alt="Card image cap"
                class="profile-bg-height  w-100 rounded-top">
            <div class="card-body little-profile text-center">
                <h3 class="mb-0">Verified Member Hari Ini</h3>
                <div class="row text-center mt-3 pt-1">
                    <div class="col-lg-6 col-md-6 mt-4">
                        <h3 class="mb-2 text-success"> Approved </h3>
                        <h2 class="mb-0 text-secondary"> {{number_format($approved->count)}} </h2>
                    </div>
                    <div class="col-lg-6 col-md-6 mt-4">
                        <h3 class="mb-2 text-danger"> Rejected </h3>
                        <h2 class="mb-0 text-secondary"> {{number_format($rejected->count)}} </h2>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!-- Column -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-body">
            <!-- Row -->
            <div class="row">
                <!-- Column -->
                <div class="col-sm-8 pr-0 align-self-center">
                    <h2 class="font-weight-light mb-0"> {{number_format($dataDashboard->otu_active)}} </h2>
                    <h6 class="text-muted">Jumlah Pengguna OTU</h6>
                </div>
                <!-- Column -->
                <div class="col-sm-4 text-right align-self-center">
                    <div class="round round-lg text-white d-inline-block text-center rounded-circle bg-warning">
                        <i class="mdi mdi-account-location"></i></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-body">
            <!-- Row -->
            <div class="row">
                <!-- Column -->
                <div class="col-sm-8 pr-0 align-self-center">
                    <h2 class="font-weight-light mb-0"> {{number_format($dataDashboard->total_membermax)}} </h2>
                    <h6 class="text-muted">Jumlah Member EklankuMax</h6>
                </div>
                <!-- Column -->
                <div class="col-sm-4 text-right align-self-center">
                    <div class="round round-lg text-white d-inline-block text-center rounded-circle bg-warning">
                        <i class="mdi mdi-account-multiple"></i></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
    <!-- Column -->
    <div class="col-md-6 col-lg-3">
        <div class="card card-body">
            <!-- Row -->
            <div class="row">
                <!-- Column -->
                <div class="col-sm-8 pr-0 align-self-center">
                    <h2 class="font-weight-light mb-0"> {{number_format($dataDashboard->total_saldo)}} </h2>
                    <h6 class="text-muted">Jumlah Saldo Member</h6>
                </div>
                <!-- Column -->
                <div class="col-sm-4 text-right align-self-center">
                    <div class="round round-lg text-white d-inline-block text-center rounded-circle bg-warning">
                        <i class="mdi mdi-currency-usd"></i></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Column -->
</div>
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 col-lg-6">
                        <h3 class="card-title">Grafik Transaksi Dan Pertumbuhan Member</h3>
                    </div>
                    <div class="col-md-12 col-lg-6">
                        <div class="row" id="searchGrafik">
                            <div class="col">
                                <select class="form-control custom-select" onchange="disableInput()" name="key" id="key" data-placeholder="Choose a Category" tabindex="2">
                                    <option value="bulanan">Bulanan</option>
                                    <option value="harian" disabled>Harian</option>
                                    <option value="tahunan" disabled>Tahunan</option>
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-control custom-select" disabled name="first_date" id="first_date" data-placeholder="Choose a Category" tabindex="1">
                                    @for ($i = 0; $i <= 11; $i++)
                                        <option value="{{$i+1}}"> {{$i+1}} </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col">
                                <select class="form-control custom-select" name="second_date" id="second_date" data-placeholder="Choose a Category" tabindex="1">
                                    @for ($j = 2010; $j <= date('Y'); $j++)
                                        <option value="{{$j}}"> {{$j}} </option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col">
                                <button onclick="getMorries()" class="btn btn-success">Lihat</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center mt-2">
                    <div>
                        <ul class="list-inline mb-0">
                            <li class="list-inline-item px-2">
                                <h6 class="text-info"><i class="fa fa-circle font-10 mr-2 "></i>Transaksi</h6>
                            </li>
                            <li class="list-inline-item px-2">
                                <h6 class="text-success"><i class="fa fa-circle font-10 mr-2"></i>Member </h6>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="amp-pxl" style="height: 360px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        function disableInput(){
            if($('#key').val() === 'tahunan'){
                $('#first_date').prop('disabled', true);
            }else if($('#key').val() === 'bulanan'){
                $('#first_date').prop('disabled', true);
            }else{
                $('#first_date').prop('disabled', false);
            }
        }
        function getMorries(){
            const key = $('#key').val()
            const month = $('#first_date').val()
            const year = $('#second_date').val()
            var x = document.getElementsByClassName("amp-pxl");
            x[0].innerHTML = "Loading.....";
            $.ajax({
                url:"/get_mories",
                method: 'POST',
                dataType: 'json',
                data: {
                    "_token": "<?= csrf_token()?>",
                    periode : key,
                    bulan : month,
                    tahun : year
                },
                success: function (data) {
                    x[0].innerHTML = "";
                    var transaksiMoris = data[1]
                    var memberMoris = data[0]
                    var highMoris = data[2]
                    var label = data[3]
                    var labelMoris = label
                    var chart = new Chartist.Bar('.amp-pxl', {
                        labels: labelMoris,
                        series: [ transaksiMoris , memberMoris ]
                    }, {
                        low: 0,
                        high: highMoris,
                        showArea: true,
                        fullWidth: false,
                        plugins: [
                            Chartist.plugins.tooltip()
                        ],
                        axisY: {
                            onlyInteger: true, 
                            scaleMinSpace: 20, 
                            seriesBarDistance: 10,
                            labelInterpolationFnc: function (value) {
                                return (value / 1);
                            }
                        },
                    });
                }
            })
        }
    </script>
    @endpush
