@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">Input Manual Transaksi</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Transaksi</a></li>
            <li class="breadcrumb-item active">Input Manual Transaksi</li>
        </ol>
    </div>
</div>

<div class="p-30 bg-white">
    <form id="InputManualTransaksi">
        <h3 class="text-center">Jenis Transaksi</h3><hr>
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label for="jenis_transaksi" class="control-label">Jenis Transaksi</label>
                    <select class="form-control" id="jenis_transaksi" name="jenis_transaksi" required>
                        <option value="" selected="selected" hidden="hidden">Pilih Jenis Transaksi</option>
                        <option value="prabayar">Prabayar</option>
                        <option value="pascabayar">Pascabayar</option>
                    </select>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label for="mbr_code" class="control-label">Kode EKL</label>
                    <input class="form-control" type="text" id="mbr_code" name="mbr_code" placeholder="Kode EKL" required/>
                    <div class="invalid-feedback">
                        Kode EKL Tidak Ditemukan.
                    </div>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label for="jenis_produk" class="control-label">Jenis Produk</label>
                    <select class="form-control" id="jenis_produk" name="jenis_produk" required>
                        <option value="" selected="selected" hidden="hidden">Pilih Jenis Produk</option>
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label for="nama_produk" class="control-label">Nama Produk</label>
                    <select class="form-control" id="nama_produk" name="nama_produk" required>
                        <option value="" selected="selected" hidden="hidden">Pilih Nama Produk</option>
                    </select>
                </div>
            </div>
        </div>

        <h3 class="text-center">Data Transaksi</h3><hr>
        {{-- Form Data Prabayar --}}
        <div id="data_trx_prabayar">
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label for="msisdn" class="control-label">MSISDN</label>
                        <input class="form-control" type="text" id="msisdn" name="msisdn" placeholder="MSISDN" required/>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="buyer_phone" class="control-label">Buyer Phone</label>
                        <input class="form-control" type="text" id="buyer_phone" name="buyer_phone" placeholder="Buyer Phone" required/>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label for="ref_id_customer" class="control-label">Ref ID Customer</label>
                        <input class="form-control" type="text" id="ref_id_customer" name="ref_id_customer" placeholder="Ref ID Customer"/>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label for="product_code" class="control-label">Product Code</label>
                        <input class="form-control" type="text" id="product_code" name="product_code" placeholder="Product Code" readonly required/>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <label for="apl_use" class="control-label">Apl Use</label>
                        <input class="form-control" type="text" id="apl_use" name="apl_use" placeholder="Apl Use" required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <button type="submit" class="btn btn-success"><i class="mdi mdi-content-save mr-2"></i>Transaksi</button>
                </div>
            </div>
        </div>
        {{-- Form Data Prabayar --}}
        <div id="data_trx_pascabayar" style="display:none">
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label for="customer_id" class="control-label">Customer ID</label>
                        <input class="form-control" type="text" id="customer_id" name="customer_id" placeholder="Customer ID" disabled required/>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="customer_msisdn" class="control-label">Customer MSISDN</label>
                        <input class="form-control" type="text" id="customer_msisdn" name="customer_msisdn" placeholder="Customer MSISDN" readonly disabled required/>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="product_code_pasca" class="control-label">Product Code</label>
                        <input class="form-control" type="text" id="product_code_pasca" name="product_code" placeholder="Product Code" readonly disabled required/>
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <label for="apl_use" class="control-label">Apl Use</label>
                        <input class="form-control" type="text" id="apl_use_pasca" name="apl_use" placeholder="Apl Use" disabled required/>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    <button type="submit" class="btn btn-success"><i class="mdi mdi-content-save mr-2"></i>Inquiry</button>
                </div>
            </div>
        </div>
    </form>
    <form id="bayar_pascabayar">
        <h3 class="text-center">Bayar Tagihan Pascabayar</h3><hr>
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label for="mbr_code" class="control-label">Kode EKL</label>
                    <input class="form-control" type="text" id="mbr_code_bayar_pasca" name="mbr_code" placeholder="Kode EKL" required/>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label for="billing_reference_id" class="control-label">Billing Reference ID</label>
                    <input class="form-control" type="text" id="billing_reference_id" name="billing_reference_id" placeholder="Billing Reference ID" required/>
                </div>
            </div>
            <div class="col-2">
                <div class="form-group">
                    <label for="apl_use" class="control-label">Apl Use</label>
                    <input class="form-control" type="text" id="apl_use_bayar_pasca" name="apl_use" placeholder="Apl Use" required/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-2">
                <button type="submit" class="btn btn-success"><i class="mdi mdi-content-save mr-2"></i>Bayar</button>
            </div>
        </div>
    </form>
</div>

@endsection

@push('scripts')
    <script>
        //AJAX BEFORE AND AFTER
        $(document).ajaxStart(function () {
            $('body').css({ 'cursor': 'wait' });
            $('input').css({ 'cursor': 'wait' });
        });
        $(document).ajaxComplete(function () {
            $('body').css({ 'cursor': 'default' });
            $('input').css({ 'cursor': 'default' });
        });

        //INISIALISASI
        var jenis_produk = {};
        jenis_produk['prabayar'] = ['Pulsa', 'Paket Data', 'SMS', 'Telepon', 'Token Listrik', 'E-Toll', 'E-Saldo', 'Wifi Id', 'Voucher Game'];
        jenis_produk['pascabayar'] = ['Tagihan Listrik', 'BPJS', 'Indihome', 'TV Kabel', 'PDAM', 'Pasca Bayar', 'Finance', 'Kartu Kredit', 'PGN', 'PBB', 'Asuransi'];

        $(document).ready(function() {
            //VALIDASI MBR CODE
            $("#mbr_code").blur(function(){
                $.ajax({
                    url: "/transaksi/mbr_code_check",
                    method: 'POST',
                    dataType: 'json',
                    data: {mbr_code: $(this).val()}
                }).done(function (response, textStatus, jqXHR){
                    if(response){
                        $('#mbr_code').removeClass('is-invalid');
                        $('#buyer_phone').val(response.mbr_code);
                        $('#customer_msisdn').val(response.mbr_code);
                    }
                    else{
                        $('#mbr_code').addClass('is-invalid');
                    }
                }).fail(function (jqXHR, textStatus, errorThrown){
                    console.error("The following error occurred: " + textStatus, errorThrown);
                }).always(function (){});
            });

            //SELECT EVENT JENIS TRANSAKSI
            $("#jenis_transaksi").change(function(){
                $('#jenis_produk').find('option').remove();
                $('#nama_produk').find('option').remove();
                var jenis_produk_s = jenis_produk[$(this).val()];
                if(jenis_produk_s){
                    $('#jenis_produk').append($('<option>', {
                        text: 'Pilih Jenis Produk',
                        value: '',
                        selected: 'selected',
                        hidden: 'hidden'
                    }));
                    $('#nama_produk').append($('<option>', {
                        text: 'Pilih Nama Produk',
                        value: '',
                        selected: 'selected',
                        hidden: 'hidden'
                    }));
                    for (var i = 0; i < jenis_produk_s.length; i++) {
                        $('#jenis_produk').append($('<option>', {
                            text: jenis_produk_s[i],
                            value: jenis_produk_s[i]
                        }));
                    }
                }

                //SHOW DATA TRX
                if($(this).val() == 'prabayar'){

                    $('#msisdn').prop('disabled', false);
                    $('#buyer_phone').prop('disabled', false);
                    $('#ref_id_customer').prop('disabled', false);
                    $('#product_code').prop('disabled', false);
                    $('#apl_use').prop('disabled', false);

                    $('#customer_id').prop('disabled', true);
                    $('#customer_msisdn').prop('disabled', true);
                    $('#product_code_pasca').prop('disabled', true);
                    $('#apl_use_pasca').prop('disabled', true);

                    $('#data_trx_prabayar').show();
                    $('#data_trx_pascabayar').hide();
                }
                if($(this).val() == 'pascabayar'){
                    
                    $('#msisdn').prop('disabled', true);
                    $('#buyer_phone').prop('disabled', true);
                    $('#ref_id_customer').prop('disabled', true);
                    $('#product_code').prop('disabled', true);
                    $('#apl_use').prop('disabled', true);

                    $('#customer_id').prop('disabled', false);
                    $('#customer_msisdn').prop('disabled', false);
                    $('#product_code_pasca').prop('disabled', false);
                    $('#apl_use_pasca').prop('disabled', false);

                    $('#data_trx_prabayar').hide();
                    $('#data_trx_pascabayar').show();
                }
            });

            //SELECT EVENT JENIS PRODUK
            $("#jenis_produk").change(function(){
                $('#nama_produk').find('option').remove();
                $('#nama_produk').append($('<option>', {
                    text: 'Pilih Nama Produk',
                    value: '',
                    selected: 'selected',
                    hidden: 'hidden'
                }));
                if($('#jenis_transaksi').val() == 'prabayar'){
                    switch($(this).val()){
                        case 'Pulsa':
                            getProdukPrabayar("PULSA");
                            break;
                        case 'Paket Data':
                            getProdukPrabayar("KUOTA");
                            break;
                        case 'SMS':
                            getProdukPrabayar("SMS");
                            break;
                        case 'Telepon':
                            getProdukPrabayar("TELPONS");
                            break;
                        case 'Token Listrik':
                            getProdukPrabayar("PLNTOKEN");
                            break;
                        case 'E-Toll':
                            getProdukPrabayar("ETOLL");
                            break;
                        case 'E-Saldo':
                            getProdukPrabayar("OJEK ONLINE");
                            break;
                        case 'Wifi Id':
                            getProdukPrabayar("WIFI ID");
                            break;
                        case 'Voucher Game':
                            getProdukPrabayar("GAME");
                            break;
                    }
                }
                else if($('#jenis_transaksi').val() == 'pascabayar'){
                    switch($(this).val()){
                        case 'Tagihan Listrik':
                            getProdukPascabayar("PLN");
                            break;
                        case 'BPJS':
                            getProdukPascabayar("BPJS");
                            break;
                        case 'Indihome':
                            getProdukPascabayar("TELKOM");
                            break;
                        case 'TV Kabel':
                            getProdukPascabayar("TV KABEL");
                            break;
                        case 'PDAM':
                            getProdukPascabayar("PDAM");
                            break;
                        case 'Pasca Bayar':
                            getProdukPascabayar("PASCA BAYAR");
                            break;
                        case 'Finance':
                            getProdukPascabayar("FINANCE");
                            break;
                        case 'Kartu Kredit':
                            getProdukPascabayar("KARTU KREDIT");
                            break;
                        case 'PGN':
                            getProdukPascabayar("PGN");
                            break;
                        case 'PBB':
                            getProdukPascabayar("PAJAK DAERAH");
                            break;
                        case 'Asuransi':
                            getProdukPascabayar("ASURANSI");
                            break;
                    }
                }
            });

            //SELECT EVENT NAMA PRODUK
            $("#nama_produk").change(function(){
                if($('#jenis_transaksi').val() == 'prabayar'){
                    $('#product_code').val($(this).val());
                }
                else if($('#jenis_transaksi').val() == 'pascabayar'){
                    $('#product_code_pasca').val($(this).val());
                }
            });

            //TRX
            $("#InputManualTransaksi").submit(function(event) {
                event.preventDefault();

                if($('#jenis_transaksi').val() == 'prabayar'){
                    
                    Swal.fire({
                        title: 'Apakah Anda Yakin?',
                        text: "Pastikan data sudah benar dan lengkap",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, data sudah benar',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                url: "/transaksi/buy_produk_prabayar",
                                method: 'POST',
                                type: 'POST',
                                dataType: 'json',
                                data: new FormData(this),
                                contentType: false,
                                cache: false,
                                processData: false
                            }).done(function (response, textStatus, jqXHR){
                                console.log(response);
                                return response;
                            }).fail(function (jqXHR, textStatus, errorThrown){
                                console.error("The following error occurred: " + textStatus, errorThrown);
                                return response;
                            }).always(function (){});
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.value.status === 'success') {
                            Swal.fire(
                                'Success!',
                                result.value.reason,
                                'success'
                            )
                        }
                        else{
                            Swal.fire(
                                'Oops..., Something went wrong!',
                                result.value.reason,
                                'error'
                            )
                        }
                    })

                }
                else if($('#jenis_transaksi').val() == 'pascabayar'){

                    Swal.fire({
                        title: 'Apakah Anda Yakin?',
                        text: "Pastikan data sudah benar dan lengkap",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, data sudah benar',
                        showLoaderOnConfirm: true,
                        preConfirm: () => {
                            return $.ajax({
                                url: "/transaksi/cek_produk_pascabayar",
                                method: 'POST',
                                type: 'POST',
                                dataType: 'json',
                                data: new FormData(this),
                                contentType: false,
                                cache: false,
                                processData: false
                            }).done(function (response, textStatus, jqXHR){
                                console.log(response);
                                return response;
                            }).fail(function (jqXHR, textStatus, errorThrown){
                                console.error("The following error occurred: " + textStatus, errorThrown);
                                return response;
                            }).always(function (){});
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.value.status === 'success') {
                            var reason = 'Billing Reference ID : ' + result.value.reason.billingReferenceID + '<br>' +
                                'Customer ID : ' + result.value.reason.customerID + '<br>' +
                                'Customer MSISDN : ' + result.value.reason.customerMSISDN + '<br>' +
                                'Customer Name : ' + result.value.reason.customerName + '<br>' +
                                'Payment : ' + result.value.reason.payment + '<br>' +
                                'Admin Bank : ' + result.value.reason.adminBank + '<br>' +
                                'Billing : ' + result.value.reason.billing + '<br>';
                            Swal.fire(
                                'Success!',
                                reason,
                                'success'
                            );
                            $('#mbr_code_bayar_pasca').val($('#mbr_code').val());
                            $('#billing_reference_id').val(result.value.reason.billingReferenceID);
                            $('#apl_use_bayar_pasca').val($('#apl_use_pasca').val());
                        }
                        else{
                            Swal.fire(
                                'Oops..., Something went wrong!',
                                result.value.reason,
                                'error'
                            );
                        }
                    });

                }

            });

            //BAYAR PASCABAYAR
            $("#bayar_pascabayar").submit(function(event) {
                event.preventDefault();

                Swal.fire({
                    title: 'Apakah Anda Yakin?',
                    text: "Pastikan data sudah benar dan lengkap",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, data sudah benar',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        return $.ajax({
                            url: "/transaksi/buy_produk_pascabayar",
                            method: 'POST',
                            type: 'POST',
                            dataType: 'json',
                            data: new FormData(this),
                            contentType: false,
                            cache: false,
                            processData: false
                        }).done(function (response, textStatus, jqXHR){
                            console.log(response);
                            return response;
                        }).fail(function (jqXHR, textStatus, errorThrown){
                            console.error("The following error occurred: " + textStatus, errorThrown);
                            return response;
                        }).always(function (){});
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.value.status === 'success') {
                        Swal.fire(
                            'Success!',
                            result.value.reason,
                            'success'
                        )
                    }
                    else{
                        Swal.fire(
                            'Oops..., Something went wrong!',
                            result.value.reason,
                            'error'
                        )
                    }
                });

            });
        });

        function getProdukPrabayar(type_produk){
            $.ajax({
                url: "/transaksi/get_produk_prabayar",
                method: 'POST',
                dataType: 'json',
                data: {mbr_code: $('#mbr_code').val(), type_produk: type_produk}
            }).done(function (response, textStatus, jqXHR){
                for (var i = 0; i < response.length; i++) {
                    $('#nama_produk').append($('<option>', {
                        text: '(' + response[i]['code'] + ')' + ' (' + response[i]['name'] + ') (' + response[i]['price'] + ') (' + response[i]['provider'] + ')',
                        value: response[i]['code'],
                    }));
                }
            }).fail(function (jqXHR, textStatus, errorThrown){
                console.error("The following error occurred: " + textStatus, errorThrown);
            }).always(function (){});
        }

        function getProdukPascabayar(type_produk){
            $.ajax({
                url: "/transaksi/get_produk_pascabayar",
                method: 'POST',
                dataType: 'json',
                data: {mbr_code: $('#mbr_code').val(), type_produk: type_produk}
            }).done(function (response, textStatus, jqXHR){
                for (var i = 0; i < response.length; i++) {
                    $('#nama_produk').append($('<option>', {
                        text: '(' + response[i]['code'] + ')' + ' (' + response[i]['name'] + ')',
                        value: response[i]['code'],
                    }));
                }
            }).fail(function (jqXHR, textStatus, errorThrown){
                console.error("The following error occurred: " + textStatus, errorThrown);
            }).always(function (){});
        }
    </script>
@endpush
