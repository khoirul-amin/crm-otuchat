@extends('layouts.app')

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">List Deposit</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Transaksi</a></li>
                <li class="breadcrumb-item active">List Deposit</li>
            </ol>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Content -->
    <!-- ============================================================== -->

    <div class="p-30" style="background-color:white">

        <!-- ============================================================== -->
        <!-- Searcing -->
        <!-- ============================================================== -->

        <div class="form-body">
            <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <label class="control-label">Tanggal Pencarian</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="mdi mdi-calendar-range"></i></span>
                            </div>
                            <input id="date_range" type="text" class="form-control" name="date_range" autocomplete="off" placeholder="masukkan tanggal pencarian" required/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Target Pencarian</label>
                        <select class="form-control custom-select"  name="column_search" id="column_search" data-placeholder="Choose a Category" tabindex="1">
                            {{-- <option value="">Semua</option> --}}
                            <option value="mbr_name">Nama</option>
                            <option value="mbr_code">ID EKL</option>
                            <option value="deposit_bank">Bank</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Kata Kunci</label>
                        <input type="text" class="form-control" placeholder="Masukan kata pencarian" name="value_search" id="value_search">
                    </div>
                </div>
                <div class="col-md-2">
                    <div>
                        <label for="">Nama Bank</label>
                        <select class="form-control custom-select" name="nama_bank" id="nama_bank" data-placeholder="Choose a Category" tabindex="1">
                            <option value="">-- Pilih --</option>
                            @foreach ($bank as $item)
                                <option value="{{$item->nama_bank}}">{{$item->nama_bank}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Cari status</label>
                        <select class="form-control custom-select" name="status_search" id="status_search" data-placeholder="Choose a Category" tabindex="1">
                            <option value="">Semua</option>
                            <option value="Active">Active</option>
                            <option value="Gagal">Gagal</option>
                            <option value="Waiting">Waiting</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group mt-4">
                        <button type="" onclick="search()" class="btn btn-success mt-2"> Cari </button>
                        <button type="" onclick="resetDatatable()" class="btn btn-warning mt-2"> Reset </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- End Searcing -->
        <!-- ============================================================== -->

        <div class="table-responsive">
            <table id="list_deposit" class="table table-striped table-bordered no-wrap">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Data Member</th>
                        <th>Jumlah Deposit</th>
                        <th>Info Deposit</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody style="font-size:10pt">

                </tbody>
                <tfoot>
                    <tr>
                        <th>Tanggal</th>
                        <th>Data Member</th>
                        <th id="total_deposit" style="background-color: #FAEBD7; color:red;"></th>
                        <th>Info Deposit</th>
                        <th>Status</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Content -->
    <!-- ============================================================== -->

@endsection
@push('scripts')

<script>
    var start_date = "";
    var end_date = "";
    allListDeposit();

    $(document).ready(function () {

        $("#cariListTransaksi").submit(function (event) {
            event.preventDefault();
            $('#list_transaksi').DataTable().destroy();
            custom_search = true;
            dataTable();
        });

        $('#column_search').on('change', function (e) {
            if (this.value == 'deposit_bank') {
                $('#value_search').prop('readonly', true);
                $('#value_search').val('');
            }else{
                $('#value_search').prop('readonly', false);
            }
        });
        $('#column_search').on('change', function (e) {
            if (this.value == 'mbr_code' || this.value == 'mbr_name') {
                $('#nama_bank').prop('disabled', true);
                $('#nama_bank').val('');
            }else{
                $('#nama_bank').prop('disabled', false);
            }
        });
    });

function resetDatatable() {
    $('#list_deposit').DataTable().destroy();
    allListDeposit();
};

function search() {
    $('#list_deposit').DataTable().destroy();
    var column_search = document.getElementById('column_search');
    var value_search = document.getElementById('value_search');
    var status_search = document.getElementById('status_search');
    var nama_bank = document.getElementById('nama_bank');

    allListDeposit(column_search.value, value_search.value, status_search.value, nama_bank.value);
};

function allListDeposit(column, value, status, bank) {
    // console.log(search,value);
    $('#list_deposit').DataTable({
        lengthMenu: [
            [10, 50, 200, 1000],
            [10, 50, 200, 1000]
        ],
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ajax": {
            "url": "/transaksi/get_list_deposit",
            "dataType": "json",
            "type": "POST",
            "data": {
                "_token": "<?= csrf_token()?>",
                column: column,
                value: value,
                status: status,
                bank: bank,
                start_date: start_date,
                end_date: end_date
            },
            "dataSrc": function (json) {
                $('#total_deposit').html(convertToRupiah(json.totaldeposit[0].total));
                return json.data;
            }
        },
        "buttons": [
            "csv", "excel", "pdf", "print"
        ],
        "columns": [{
                "data": "tanggal"
            },
            {
                "data": "member"
            },
            {
                "data": "deposit"
            },
            {
                "data": "info"
            },
            {
                "data": "status"
            },
        ],
        "order": [[0, 'DESC']]
    });
}

// Fungsi formatRupiah
function convertToRupiah(angka) {
    var rupiah = '';
    var angka = parseInt(angka)
    var angkarev = angka.toString().split('').reverse().join('');
    for (var i = 0; i < angkarev.length; i++)
        if (i % 3 == 0) rupiah += angkarev.substr(i, 3) + '.';
    return 'Rp. ' + rupiah.split('', rupiah.length - 1).reverse().join('');
}

$(function() {
    $('input[name="date_range"]').daterangepicker({
        "autoUpdateInput": false,
        "locale":{
            "format": "DD-MM-YYYY",
            "separator": " to ",
        },
        "opens": "right",
        "maxDate": "{{date('d-m-Y')}}"
    }, function(start, end, label) {
        start_date = start.format('YYYY-MM-DD');
        end_date = end.format('YYYY-MM-DD');
    });

    $('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
    });
});

</script>
@endpush
