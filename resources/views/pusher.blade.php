@extends('layouts.app')

@section('content')
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

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Transaksi</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Antrian Transaksi</a></li>
                <li class="breadcrumb-item active">Antrian Transaksi</li>
            </ol>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Content -->
    <!-- ============================================================== -->
    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Antrian Transaksi</h4>
            <div class="p-30" style="background-color:white">
                <div class="table-responsive m-t-40">
                    <table id="antrian_transaksi" class="table color-table warning-table">
                            <tr>
                                <th>Tanggal</th>
                                <th>Member</th>
                                <th>Info Pembelian</th>
                                <th>Tujuan</th>
                                <th>Harga</th>
                                <th>Lama Antrian</th>
                                <th>Status</th>
                            </tr>
                        <tbody id="tableBody" style="font-size:10pt">
                            @foreach ($posts as $item)
                            <tr>
                               <td>{{$item->tgl_trx}}</td>
                               <td>{{$item->mbr_code}}</td>
                               <td>{{$item->product_kode}}</td>
                               <td>{{$item->tujuan}}</td>
                               <td>{{$item->harga_jual}}</td>
                               <td class="second">{{$item->tgl_trx}}</td>
                               <td>{{$item->transaksi_status}}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h4 class="card-title">Transaksi</h4>
            <div class="p-30" style="background-color:white">
                <div class="table-responsive m-t-40 table-wrapper-scroll-y my-custom-scrollbar">
                    <table id="transaksi_gagal" class="table color-table success-table">
                            <tr>
                                <th>Tanggal</th>
                                <th>Member</th>
                                <th>Info Pembelian</th>
                                <th>Tujuan</th>
                                <th>Harga</th>
                                <th>Lama Antrian</th>
                                <th>Status</th>
                            </tr>
                        <tbody id="tableBody1" style="font-size:10pt">
                            <tr>

                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- End Content -->
    <!-- ============================================================== -->
@endsection


@push('scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" />
    {{-- <script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.js"></script> --}}
    <script src="https://js.pusher.com/5.1/pusher.min.js"></script>
    <script>
        var dataarray = <?php echo json_encode($posts);?>

        // Enable pusher logging - don't include this in production
         // Pusher.logToConsole = true;
         var pusher = new Pusher('57e46b1f4aecf962d647', {
            cluster: 'ap1',
            forceTLS: true
            });
            // Connect Pusher
            var channel = pusher.subscribe('my_channel');
            channel.bind('my_event', function(data) {


                var table = document.getElementById('tableBody');
                var table1 = document.getElementById('tableBody1');

                if (data.transaksi_status == "Waiting"){

                    var row = table.insertRow(-1);
                    var tanggal = row.insertCell(0);
                    var member = row.insertCell(1);
                    var pembelian = row.insertCell(2);
                    var tujuan = row.insertCell(3);
                    var harga = row.insertCell(4);
                    var antrian = row.insertCell(5);
                    var status = row.insertCell(6);

                    dataarray.push(data);
                    // inner HTML
                    tanggal.innerHTML = '<span style="font-weight:bold" class="mr-2">Request :</span>'+data.tgl_trx;
                    member.innerHTML = data.mbr_code;
                    pembelian.innerHTML = data.product_kode;
                    tujuan.innerHTML = data.tujuan;
                    harga.innerHTML = '<span style="font-weight:bold" class="mr-2">'+convertToRupiah(data.harga_jual)+'</span>';
                    antrian.innerHTML = data.tgl_trx;
                    status.innerHTML = '<span class="label label-warning">'+data.transaksi_status+'</span>';

                } else if(data.transaksi_status == "Active" || data.transaksi_status == "Gagal") {

                    var index = dataarray.findIndex(x => x.transaksi_id === data.transaksi_id);
                    console.log(data.transaksi_id, index)

                    //console.log(dataarray.indexOf(data.transaksi_id))

                    if (data.transaksi_type == 'TRANSAKSI'){
                        var row = table1.insertRow(-1);
                        var tanggal = row.insertCell(0);
                        var member = row.insertCell(1);
                        var pembelian = row.insertCell(2);
                        var tujuan = row.insertCell(3);
                        var harga = row.insertCell(4);
                        var antrian = row.insertCell(5);
                        var status = row.insertCell(6);

                        if(data.transaksi_status == 'Active'){
                            sts = '<span class="label label-success">'+data.transaksi_status+'</span>';
                        } else {
                            sts = '<span class="label label-danger">'+data.transaksi_status+'</span>';
                        }

                        // inner HTML
                        tanggal.innerHTML = '<span style="font-weight:bold" class="mr-2">Request :</span>'+data.tgl_trx+'<br><span style="font-weight:bold" class="mr-2">Sukses :</span>'+ data.sukses;
                        member.innerHTML = data.mbr_code;
                        pembelian.innerHTML = data.product_kode;
                        tujuan.innerHTML = data.tujuan;
                        harga.innerHTML = '<span style="font-weight:bold" class="mr-2">'+convertToRupiah(data.harga_jual)+'</span>';
                        antrian.innerHTML = data.tgl_trx;
                        status.innerHTML = sts;
                    }

                    // if (dataarray.length > index){
                    //     // Hapus

                    // }

                    //console.log(data.transaksi_id, index)

                    //console.log(dataarray.length);

                    // delete row by index
                    table.deleteRow(index);

                    // delete data in array
                    dataarray.splice(data, 1);

                }
            });


            // Fungsi formatRupiah
            function convertToRupiah(angka){
                var rupiah = '';
                var angkarev = angka.toString().split('').reverse().join('');
                for(var i = 0; i < angkarev.length; i++) if(i%3 == 0) rupiah += angkarev.substr(i,3)+'.';
                return 'Rp. '+rupiah.split('',rupiah.length-1).reverse().join('');
            }

    </script>
@endpush
