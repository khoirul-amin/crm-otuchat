@extends('layouts.app')

@section('content')
<?php
    $url1=$_SERVER['REQUEST_URI'];
    header("Refresh: 4000; URL=$url1");
?>
<style>
    .style{
        border: 1px solid grey;
        margin-bottom: 10px;
        border-radius: 5px;
        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 10px;
        padding-right: 10px;
    }
    .text-center{
        text-align: center;
    }
    .my-custom-scrollbar {
        position: relative;
        height: 200px;
        overflow: auto;
    }
    .table-wrapper-scroll-y {
        display: block;
    }
</style>
<script src="{{ asset('assets') }}/plugins/jquery/jquery.min.js"></script>
<script src="{{ asset('assets') }}/jquery-countdown/js/jquery.plugin.min.js"></script>
<script src="{{ asset('assets') }}/jquery-countdown/js/jquery.countdown.min.js"></script>
<script src="{{ asset('assets') }}/jquery-countdown/js/jquery.countdown-id.js"></script>
<script>
    var id = 0;
    function highlightLast3(periods) {
        if (periods[5] >= 3 || ((periods[4] != 0) || (periods[3] != 0) || (periods[2] != 0) || (periods[1] != 0) || (periods[0] != 0) )) {
            $(this).addClass('text-danger');
        }
    }
</script>

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Antrian Transaksi</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Transaksi</a></li>
                <li class="breadcrumb-item active">Antrian Transaksi</li>
            </ol>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Content -->
    <!-- ============================================================== -->
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Transaksi Sukses</h4>
            <div class="p-0" style="background-color:white">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control" onkeyup="cariSukses()" placeholder="Cari Nomer Tujuan" id="myInput">
                    </div>
                </div>
                <div class="table-responsive mt-3 table-wrapper-scroll-y my-custom-scrollbar">
                    <table id="transaksi_sukses" class="table color-table success-table">
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Member</th>
                                <th>Info Pembelian</th>
                                <th>Tujuan</th>
                                <th>Harga</th>
                                <th width="15%">Lama Antrian</th>
                                <th>Status</th>
                            </tr>
                        <tbody id="tableBody1" style="font-size:10pt">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Gagal --}}
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Transaksi Gagal</h4>
            <div class="p-0" style="background-color:white">
                <div class="col-md-3">
                    <div class="form-group">
                        <input type="text" class="form-control" onkeyup="cariGagal()" placeholder="Cari Nomer Tujuan" id="myInputGagal">
                    </div>
                </div>
                <div class="table-responsive mt-3 table-wrapper-scroll-y my-custom-scrollbar">
                    <table id="transaksi_gagal" class="table color-table success-table">
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Member</th>
                                <th>Info Pembelian</th>
                                <th>Tujuan</th>
                                <th>Harga</th>
                                <th>Lama Antrian</th>
                                <th>Status</th>
                            </tr>
                        <tbody id="tableBody2" style="font-size:10pt">
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Antrian Transaksi</h4>
            <div class="p-0" style="background-color:white">
                <div class="table-responsive mt-3">
                    <table id="antrian_transaksi" class="table color-table warning-table">
                            <tr>
                                <th>ID </th>
                                <th>Tanggal</th>
                                <th>Member</th>
                                <th>Info Pembelian</th>
                                <th>Tujuan</th>
                                <th>Harga</th>
                                <th>Lama Antrian</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        <tbody id="tableBody" style="font-size:10pt">
                            @foreach ($posts as $row => $key)
                            <tr>
                                <td>{{$key->transaksi_id}}</td>
                                <td><span style="font-weight:bold" class="mr-2">Request :</span>{{$key->tgl_trx}}</td>
                                <td><span style="font-weight:bold" class="mr-2">Nama : </span>{{$key->mbr_name}}<br>
                                    <span style="font-weight:bold" class="mr-2">No EKL : </span>{{$key->mbr_code}}</td>
                                <td><span style="font-weight:bold" class="mr-2">Product : </span>{{$key->product_name}}<br>
                                    <span style="font-weight:bold" class="mr-2">Kode Product : {{$key->product_kode}}<br>
                                    <span style="font-weight:bold" class="mr-2">Supplier : {{$key->supliyer_name}}</td>
                                <td><span style="font-weight:bold" class="mr-2">Tujuan : {{$key->tujuan}}</span></td>
                                <td><span style="font-weight:bold" class="mr-2">Rp. {{number_format($key->harga_jual,0,',','.')}}</span></td>
                                <td width="15%" id="{{$row}}"></td>
                                <script>
                                    var fDate = new Date('{{$key->tgl_trx}}');
                                        $('#' + {{$row}}).countdown({
                                            since: fDate,
                                            timezone: +8,
                                            padZeroes: true,
                                            onTick: highlightLast3
                                        });
                                    id = {{$row}};
                                </script>
                                <td>
                                    @if ($key->transaksi_status == "Waiting")
                                        <span class="label label-warning">{{$key->transaksi_status}}
                                    @else
                                        <span class="label label-info">{{$key->transaksi_status}}
                                    @endif</span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            Action
                                        </button>
                                        <div class="dropdown-menu animated flipInX">
                                            @if ($key->transaksi_status == "Waiting")
                                                <button class="dropdown-item" onClick="updateStatusSukses({{$key->transaksi_id}})">Sukses</button>
                                                <button class="dropdown-item" onClick="updateStatusGagal({{$key->transaksi_id}})">Gagal</button>
                                            @else
                                                <button class="dropdown-item" onClick="updateStatusSukses({{$key->transaksi_id}})">Sukses</button>
                                                <button class="dropdown-item" onClick="updateStatusGagal({{$key->transaksi_id}})">Gagal</button>
                                                <div class="dropdown-divider"></div>
                                                <button class="dropdown-item" onClick="updateStatusWaiting({{$key->transaksi_id}})">Resend</button>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                            <script>
                                id++;
                            </script>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- End Content -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
@endsection

@push('scripts')
    <script src="{{ asset('assets') }}/jquery-countdown/js/jquery.plugin.min.js"></script>
    <script src="{{ asset('assets') }}/jquery-countdown/js/jquery.countdown.min.js"></script>
    <script src="{{ asset('assets') }}/jquery-countdown/js/jquery.countdown-id.js"></script>

    <script src="https://js.pusher.com/5.1/pusher.min.js"></script>
    <script>
        id = id++;
        var dataarray = {!!json_encode($posts)!!}

        var total = 0;

        //console.log("Total Array Pertama", dataarray.length)
        var table = document.getElementById('tableBody');
        var table1 = document.getElementById('tableBody1');
        var table2 = document.getElementById('tableBody2');

        // Enable pusher logging - don't include this in production
         // Pusher.logToConsole = true;
        var pusher = new Pusher('57e46b1f4aecf962d647', {
            cluster: 'ap1',
            forceTLS: true
            });
            // Connect Pusher
            var channelInsert = pusher.subscribe('my_channel');
            var channelUpdate = pusher.subscribe('my_channel_update');

            channelInsert.bind('my_event', function(data) {

                    var row = table.insertRow(-1);
                    var id_transaksi      = row.insertCell(0);
                    var tanggal = row.insertCell(1);
                    var member  = row.insertCell(2);
                    var pembelian = row.insertCell(3);
                    var tujuan  = row.insertCell(4);
                    var harga   = row.insertCell(5);
                    var antrian = row.insertCell(6);
                    var status  = row.insertCell(7);

                    dataarray.push(data);
                    //console.log("Total Array", dataarray.length)
                    //console.log(dataarray)
                    // inner HTML
                    id_transaksi.innerHTML        = data.transaksi_id;
                    tanggal.innerHTML   = '<span style="font-weight:bold" class="mr-2">Request :</span>'+data.tgl_trx;
                    member.innerHTML    = '<span style="font-weight:bold" class="mr-2">Nama   : </span>'+data.mbr_name+'<br><span style="font-weight:bold" class="mr-2">No EKL : </span>'+data.mbr_code;
                    pembelian.innerHTML = '<span style="font-weight:bold" class="mr-2">Product      : </span>'+data.product_name+'<br><span style="font-weight:bold" class="mr-2">Kode Product : '+data.product_kode+'<br><span style="font-weight:bold" class="mr-2">Supplier     : '+data.supliyer_name;
                    tujuan.innerHTML    = '<span style="font-weight:bold" class="mr-2">Tujuan : '+data.tujuan+'</span>';
                    harga.innerHTML     = '<span style="font-weight:bold" class="mr-2">'+convertToRupiah(data.harga_jual)+'</span>';

                    antrian.id = id;
                    var fDate = new Date(data.tgl_trx);
                        $('#' + id).countdown({
                            since: fDate,
                            timezone: +8,
                            padZeroes: true,
                            onTick: highlightLast3
                        });
                    id++;

                    if (data.transaksi_status == "Waiting"){
                        sts = '<span class="label label-warning">'+data.transaksi_status+'</span>';
                    } else {
                        sts = '<span class="label label-info">'+data.transaksi_status+'</span>';
                    }

                    status.innerHTML = sts;
            });

            channelUpdate.bind('my_event_update', function(data) {

                if(data.transaksi_status == "Active") {

                    var index = dataarray.findIndex(x => x.transaksi_id === data.transaksi_id);

                    var row     = table1.insertRow(0);
                    var id_transaksi = row.insertCell(0);
                    var tanggal = row.insertCell(1);
                    var member  = row.insertCell(2);
                    var pembelian = row.insertCell(3);
                    var tujuan  = row.insertCell(4);
                    var harga   = row.insertCell(5);
                    var antrian = row.insertCell(6);
                    var status  = row.insertCell(7);

                    // inner HTML
                    id_transaksi.innerHTML = data.transaksi_id;
                    tanggal.innerHTML   = '<span style="font-weight:bold" class="mr-2">Request :</span>'+data.tgl_trx+'<br><span style="font-weight:bold" class="mr-2">Sukses :</span>'+data.tgl_sukses;
                    member.innerHTML    = '<span style="font-weight:bold" class="mr-2">Nama   : </span>'+data.mbr_name+'<br><span style="font-weight:bold" class="mr-2">No EKL : </span>'+data.mbr_code;
                    pembelian.innerHTML = '<span style="font-weight:bold" class="mr-2">Product      : </span>'+data.product_name+'<br><span style="font-weight:bold" class="mr-2">Kode Product : '+data.product_kode+'<br><span style="font-weight:bold" class="mr-2">Supplier     : '+data.supliyer_name;
                    tujuan.innerHTML    = '<span style="font-weight:bold" class="mr-2">Tujuan : '+data.tujuan+'</span>';
                    harga.innerHTML     = '<span style="font-weight:bold" class="mr-2">'+convertToRupiah(data.harga_jual)+'</span>';
                    antrian.innerHTML   = diffBetweenDate(new Date(data.tgl_trx), new Date(data.tgl_sukses));
                    status.innerHTML    = '<span class="label label-success">'+data.transaksi_status+'</span>';

                    //console.log("Index", index)

                    if (index < 0){
                        //console.log("NOTFOUND")
                    } else {
                        dataarray.splice(dataarray.findIndex(e => e.transaksi_id === data.transaksi_id),1);

                        table.deleteRow(index);
                    }



                } else if (data.transaksi_status == "Gagal") {

                    var index = dataarray.findIndex(x => x.transaksi_id === data.transaksi_id);

                    var row     = table2.insertRow(0);
                    var id_transaksi  = row.insertCell(0);
                    var tanggal = row.insertCell(1);
                    var member  = row.insertCell(2);
                    var pembelian = row.insertCell(3);
                    var tujuan  = row.insertCell(4);
                    var harga   = row.insertCell(5);
                    var antrian = row.insertCell(6);
                    var status  = row.insertCell(7);


                    // inner HTML
                    id_transaksi.innerHTML        = data.transaksi_id;
                    tanggal.innerHTML   = '<span style="font-weight:bold" class="mr-2">Request :</span>'+data.tgl_trx+'<br><span style="font-weight:bold" class="mr-2">Sukses :</span>'+data.tgl_sukses;
                    member.innerHTML    = '<span style="font-weight:bold" class="mr-2">Nama   : </span>'+data.mbr_name+'<br><span style="font-weight:bold" class="mr-2">No EKL : </span>'+data.mbr_code;
                    pembelian.innerHTML = '<span style="font-weight:bold" class="mr-2">Product      : </span>'+data.product_name+'<br><span style="font-weight:bold" class="mr-2">Kode Product : '+data.product_kode+'<br><span style="font-weight:bold" class="mr-2">Supplier     : '+data.supliyer_name;
                    tujuan.innerHTML    = '<span style="font-weight:bold" class="mr-2">Tujuan : '+data.tujuan+'</span>';
                    harga.innerHTML     = '<span style="font-weight:bold" class="mr-2">'+convertToRupiah(data.harga_jual)+'</span>';
                    antrian.innerHTML   = diffBetweenDate(new Date(data.tgl_trx), new Date(data.tgl_sukses));
                    status.innerHTML    = '<span class="label label-danger">'+data.transaksi_status+'</span>';

                    //console.log(data.transaksi_id, index)

                    // delete row by index
                    if (index < 0){
                        //console.log("NOTFOUND")
                    } else {
                        dataarray.splice(dataarray.findIndex(e => e.transaksi_id === data.transaksi_id),1);
                        table.deleteRow(index);
                        // dataarray.splice( dataarray.indexOf(data.transaksi_id), 1 );
                        // //dataarray.splice(data, 1);
                    }

                } else if (data.transaksi_status == "Onproses"){
                    var index = dataarray.findIndex(x => x.transaksi_id === data.transaksi_id);
                    if (index < 0){
                        //console.log(index, "NOTFOUND")
                    } else {
                        // Delete Dulu
                        dataarray.splice(dataarray.findIndex(e => e.transaksi_id === data.transaksi_id),1);
                        table.deleteRow(index);

                        // Insert
                        var row           = table.insertRow(-1);
                        var id_transaksi  = row.insertCell(0);
                        var tanggal       = row.insertCell(1);
                        var member        = row.insertCell(2);
                        var pembelian     = row.insertCell(3);
                        var tujuan        = row.insertCell(4);
                        var harga   = row.insertCell(5);
                        var antrian = row.insertCell(6);
                        var status  = row.insertCell(7);
                        var action  = row.insertCell(8);



                        dataarray.push(data);

                        // inner HTML
                        id_transaksi.innerHTML        = data.transaksi_id;
                        tanggal.innerHTML   = '<span style="font-weight:bold" class="mr-2">Request :</span>'+data.tgl_trx;
                        member.innerHTML    = '<span style="font-weight:bold" class="mr-2">Nama   : </span>'+data.mbr_name+'<br><span style="font-weight:bold" class="mr-2">No EKL : </span>'+data.mbr_code;
                        pembelian.innerHTML = '<span style="font-weight:bold" class="mr-2">Product      : </span>'+data.product_name+'<br><span style="font-weight:bold" class="mr-2">Kode Product : '+data.product_kode+'<br><span style="font-weight:bold" class="mr-2">Supplier     : '+data.supliyer_name;
                        tujuan.innerHTML    = '<span style="font-weight:bold" class="mr-2">Tujuan : '+data.tujuan+'</span>';
                        harga.innerHTML     = '<span style="font-weight:bold" class="mr-2">'+convertToRupiah(data.harga_jual)+'</span>';
                        action.innerHTML    = '<div class="btn-group"><button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Action</button><div class="dropdown-menu animated flipInX"><button class="dropdown-item" onClick="updateStatusSukses('+data.transaksi_id+')">Sukses</button><button class="dropdown-item" onClick="updateStatusGagal('+data.transaksi_id+')">Gagal</button><div class="dropdown-divider"></div><button class="dropdown-item" onClick="updateStatusWaiting('+data.transaksi_id+')">Resend</button></div></div>';

                        antrian.id = id;
                        var fDate = new Date(data.tgl_trx);
                            $('#' + id).countdown({
                                since: fDate,
                                timezone: +8,
                                padZeroes: true,
                                onTick: highlightLast3
                            });
                        id++;

                        if (data.transaksi_status == "Waiting"){
                            sts = '<span class="label label-warning">'+data.transaksi_status+'</span>';
                        } else {
                            sts = '<span class="label label-info">'+data.transaksi_status+'</span>';
                        }

                        status.innerHTML = sts;
                    }
                }
            });


            // Fungsi formatRupiah
            function convertToRupiah(angka){
                var rupiah = '';
                var angka = parseInt(angka)
                var angkarev = angka.toString().split('').reverse().join('');
                for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
                return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
            }

            function diffBetweenDate(date_awal, date_akhir) {
                var miliseconds = Math.abs(date_akhir - date_awal);

                total_seconds = parseInt(Math.floor(miliseconds / 1000));
                total_minutes = parseInt(Math.floor(total_seconds / 60));
                total_hours = parseInt(Math.floor(total_minutes / 60));
                days = parseInt(Math.floor(total_hours / 24));

                seconds = parseInt(total_seconds % 60);
                minutes = parseInt(total_minutes % 60);
                hours = parseInt(total_hours % 24);

                return days + ' hari ' + hours + ' jam ' + minutes + ' menit ' + seconds + ' detik';
            }

            function cariSukses() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("myInput");
                filter = input.value.toUpperCase();
                table = document.getElementById("transaksi_sukses");
                tr = table.getElementsByTagName("tr");
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[4];
                    if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                    }
                }
            }

            function cariGagal() {
                var input, filter, table, tr, td, i, txtValue;
                input = document.getElementById("myInputGagal");
                filter = input.value.toUpperCase();
                table = document.getElementById("transaksi_gagal");
                tr = table.getElementsByTagName("tr");
                for (i = 0; i < tr.length; i++) {
                    td = tr[i].getElementsByTagName("td")[4];
                    if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                    }
                }
            }

            var supplier = {!!json_encode($supplier) !!}
            var supplierdata = "{";
            var index;
            for (index = 0; index < supplier.length; index++) {
                supplierdata += "\""+supplier[index].supliyer_id+"\":\""+supplier[index].supliyer_name+"\"";
                if (index !== supplier.length-1){
                    supplierdata += ",";
                }
            }
            supplierdata += "}";

            function updateStatusWaiting(id_trx) {
                Swal.fire({
                    title: 'Update Status Transaksi!',
                    icon: 'warning',
                    text: "Proses ini akan mengirim transaksi lagi ke supplier. Silahkan pilih Supplier dulu.",
                    input: 'select',
                    inputPlaceholder: ' -- Pilih Supplier --',
                    inputOptions: JSON.parse(supplierdata),
                    confirmButtonColor: "#47A447",
                    confirmButtonText: "Iya, saya yakin!",
                    cancelButtonText: "Batal",
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        Swal.fire('Proses sedang berjalan, tunggu sampai selesai!')
                        Swal.showLoading()
                        $.ajax({
                            url: "/transaksi/update_status_transaksi",
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                "_token": "<?= csrf_token()?>",
                                id_trx: id_trx,
                                status: "RESEND",
                                supplier: result.value
                            },
                            success: function (data) {
                                //console.log(id_trx)
                                if (data.respStatus) {

                                    var index = dataarray.findIndex(x => x.transaksi_id === id_trx);
                                        if (index < 0){
                                            //console.log(index, "NOTFOUND")
                                        } else {
                                            var dataarraysecond = []
                                            dataarraysecond.push(dataarray[index])
                                            // Delete Dulu
                                            dataarray.splice(dataarray.findIndex(e => e.transaksi_id === id_trx),1);
                                            table.deleteRow(index);

                                            // Insert
                                            var row           = table.insertRow(-1);
                                            var id_transaksi  = row.insertCell(0);
                                            var tanggal       = row.insertCell(1);
                                            var member        = row.insertCell(2);
                                            var pembelian     = row.insertCell(3);
                                            var tujuan        = row.insertCell(4);
                                            var harga       = row.insertCell(5);
                                            var antrian     = row.insertCell(6);
                                            var status      = row.insertCell(7);
                                            var action      = row.insertCell(8);

                                            // console.log(dataarraysecond[index])
                                            // console.log(index)
                                            dataarray.push(dataarraysecond[0]);

                                            // inner HTML
                                            id_transaksi.innerHTML        = dataarraysecond[0].transaksi_id;
                                            tanggal.innerHTML   = '<span style="font-weight:bold" class="mr-2">Request :</span>'+dataarraysecond[0].tgl_trx;
                                            member.innerHTML    = '<span style="font-weight:bold" class="mr-2">Nama   : </span>'+dataarraysecond[0].mbr_name+'<br><span style="font-weight:bold" class="mr-2">No EKL : </span>'+dataarraysecond[0].mbr_code;
                                            pembelian.innerHTML = '<span style="font-weight:bold" class="mr-2">Product      : </span>'+dataarraysecond[0].product_name+'<br><span style="font-weight:bold" class="mr-2">Kode Product : '+dataarraysecond[0].product_kode+'<br><span style="font-weight:bold" class="mr-2">Supplier     : '+dataarraysecond[0].supliyer_name;
                                            tujuan.innerHTML    = '<span style="font-weight:bold" class="mr-2">Tujuan : '+dataarraysecond[0].tujuan+'</span>';
                                            harga.innerHTML     = '<span style="font-weight:bold" class="mr-2">'+convertToRupiah(dataarraysecond[0].harga_jual)+'</span>';
                                            action.innerHTML    = '';

                                            antrian.id = id;
                                            var fDate = new Date(dataarraysecond[0].tgl_trx);
                                                $('#' + id).countdown({
                                                    since: fDate,
                                                    timezone: +8,
                                                    padZeroes: true,
                                                    onTick: highlightLast3
                                                });
                                            id++;

                                            status.innerHTML = '<span class="label label-warning">Waiting</span>';
                                            var dataarraysecond = []
                                        }
                                    Swal.fire("PROSES BERHASIL", data.respMessage, "success");
                                } else {
                                    Swal.fire("PROSES GAGAL", data.respMessage, "error");
                                }
                            }
                        });
                    }
                })
            }

            function updateStatusGagal(id_trx) {
                Swal.fire({
                    title: 'Gagalkan Transaksi!',
                    icon: 'warning',
                    text: "Proses ini akan membatalkan proses transaksi.",
                    input: 'text',
                    inputValue: 'TRX TIDAK DAPAT DIPROSES',
                    inputAttributes: {
                        autocapitalize: 'off',
                        placeholder: 'Masukan Alasan',
                        required: true
                    },
                    confirmButtonColor: "#47A447",
                    confirmButtonText: "Iya, saya yakin!",
                    cancelButtonText: "Batal",
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        Swal.fire('Proses sedang berjalan, tunggu sampai selesai!')
                        Swal.showLoading()
                        // $('body').loading('stop');
                        $.ajax({
                            url: "/transaksi/update_status_transaksi",
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                "_token": "<?= csrf_token()?>",
                                id_trx: id_trx,
                                alasan: result.value,
                                status: "GAGAL"
                            },
                            success: function (data) {
                                if (data.respStatus) {
                                    var index = dataarray.findIndex(x => x.transaksi_id === id_trx);
                                    if (index < 0){
                                        //console.log(index, "NOTFOUND")
                                    } else {
                                        // Delete Dulu
                                        dataarray.splice(dataarray.findIndex(e => e.transaksi_id === id_trx),1);
                                        table.deleteRow(index);
                                    }
                                    Swal.fire("PROSES BERHASIL", data.respStatus, "success");
                                } else {
                                    Swal.fire("PROSES GAGAL", data.respMessage, "error");
                                }
                            }
                        });
                    }
                });
            }

            function updateStatusSukses(id_trx) {
                Swal.mixin({
                    title: 'Transaksi Sukses!',
                    // icon: 'warning',
                    text: "Proses ini akan merubah status transaksi menjadi sukses, yakin ingin melanjutkan?",
                    input: 'text',
                    confirmButtonText: 'Next &rarr;',
                    showCancelButton: true,
                    // inputAttributes: {
                    //     autocapitalize: 'off',
                    //     placeholder: 'Masukan Harga Supplier',
                    //     required: true
                    // },
                    progressSteps: ['1', '2']
                    }).queue([
                    {
                        title: 'Masukan VSN Transaksi',
                        inputPlaceholder: 'Nomer VSN',
                        text: 'VSN Terdapat pada Vendor'
                    },{
                        title:'Masukan Harga Supplier',
                        text:'Harga tidak boleh lebih dari harga jual',
                        inputPlaceholder: 'Masukan Harga Sesuai harga jual',
                    }
                    // confirmButtonColor: "#47A447",
                    // confirmButtonText: "Iya, saya yakin!",
                    // cancelButtonText: "Batal",
                    // showCancelButton: true,
                    ]).then((result) => {
                    if (result.value) {
                        Swal.fire('Proses sedang berjalan, tunggu sampai selesai!')
                        Swal.showLoading()
                        // $('body').loading('stop');
                        $.ajax({
                            url: "/transaksi/update_status_transaksi",
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                "_token": "<?= csrf_token()?>",
                                id_trx: id_trx,
                                alasan: result.value,
                                status: "SUKSES"
                            },
                            success: function (data) {
                                if (data.respStatus) {
                                    var index = dataarray.findIndex(x => x.transaksi_id === id_trx);
                                    if (index < 0){
                                        //console.log(index, "NOTFOUND")
                                    } else {
                                        // Delete Dulu
                                        dataarray.splice(dataarray.findIndex(e => e.transaksi_id === id_trx),1);
                                        table.deleteRow(index);
                                    }
                                    Swal.fire("PROSES BERHASIL", data.respStatus, "success");
                                } else {
                                    Swal.fire("PROSES GAGAL", data.respMessage, "error");
                                }
                            }
                        });
                    }
                });
            }
    </script>
@endpush
