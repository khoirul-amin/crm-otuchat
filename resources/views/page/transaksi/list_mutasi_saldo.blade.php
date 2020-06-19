@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">List Mutasi Saldo</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Transaksi</a></li>
            <li class="breadcrumb-item active">List Mutasi Saldo</li>
        </ol>
    </div>
</div>

<div class="p-30 bg-white">
    <form id="cariMutasiSaldo">
        <div class="row">
            <div class="col-3">
                <label for="date_range" class="control-label">Pencarian Berdasarkan Tanggal :</label>
            </div>
            <div class="col-3">

            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="mdi mdi-calendar-range"></i></span>
                    </div>
                    <input id="date_range" type="text" class="form-control" name="date_range" autocomplete="off" placeholder="masukkan tanggal pencarian"/>
                </div>
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-primary"><i class="mdi mdi-magnify mr-2"></i>Search</button>
                <button type="button" class="btn btn-primary" onclick="resetDataTable()"><i class="mdi mdi-undo mr-2"></i>Reset</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table id="list_mutasi_saldo" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Tanggal Mutasi</th>
                    <th>ID EKL</th>
                    <th>Saldo Masuk</th>
                    <th>Saldo Keluar</th>
                    <th>Total Saldo</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody class="small"></tbody>
        </table>
    </div>

</div>

@endsection

@push('scripts')
    <script>
        var start_date = "{{date('Y-m-d')}}";
        var end_date = "{{date('Y-m-d')}}";
        var ajax_load = 0;

        $(document).ready(function(){
            dataTable();

            $("#cariMutasiSaldo").submit(function(event){
                event.preventDefault();

                if(ajax_load == 0){
                    ajax_load = 1;
                    $('#list_mutasi_saldo').DataTable().destroy();
                    dataTableSearch();
                }
            });
        });
        
        function resetDataTable(){
            $('#date_range').val('');
            $('#list_mutasi_saldo').DataTable().destroy();

            dataTable();
        }

        function dataTable(){
            $('#list_mutasi_saldo').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/transaksi/get_list_mutasi_saldo",
                    "dataType": "json",
                    "type": "POST"
                },
                "columns": [
                    {"data": "uang_date"},
                    {"data": "mbr_code"},
                    {"data": "uang_masuk", "className": "text-right"},
                    {"data": "uang_keluar", "className": "text-right"},
                    {"data": "uang_amount", "className": "text-right"},
                    {"data": "uang_desc"}
                ],
                "initComplete" : function() {
                    var input = $('.dataTables_filter input').unbind(),
                        self = this.api(),
                        $searchButton = $('<button class="btn ml-2 btn-primary">')
                                .text('search')
                                .click(function() {
                                    self.search(input.val()).draw();
                                }),
                        $clearButton = $('<button class="btn ml-2 btn-primary">')
                                .text('clear')
                                .click(function() {
                                    input.val('');
                                    $searchButton.click(); 
                                })
                    $('.dataTables_filter input')
                        .off('.DT')
                        .on('keyup.DT', function (e) {
                            if (e.keyCode == 13) {
                                self.search(this.value).draw();
                            }
                        });
                    $('.dataTables_filter').append($searchButton, $clearButton);
                },
                "language": {
                    searchPlaceholder: "Search Something"
                },
                "order":[[0, "desc"]]
            });
        }

        function dataTableSearch(){
            $('#list_mutasi_saldo').DataTable({
                "lengthMenu": [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/transaksi/get_list_mutasi_saldo",
                    "dataType": "json",
                    "type": "POST",
                    "data": {"start_date": start_date, "end_date": end_date }
                },
                "columns": [
                    {"data": "uang_date"},
                    {"data": "mbr_code"},
                    {"data": "uang_masuk", "className": "text-right"},
                    {"data": "uang_keluar", "className": "text-right"},
                    {"data": "uang_amount", "className": "text-right"},
                    {"data": "uang_desc"}
                ],
                "initComplete" : function() {
                    var input = $('.dataTables_filter input').unbind(),
                        self = this.api(),
                        $searchButton = $('<button class="btn ml-2 btn-primary">')
                                .text('search')
                                .click(function() {
                                    self.search(input.val()).draw();
                                }),
                        $clearButton = $('<button class="btn ml-2 btn-primary">')
                                .text('clear')
                                .click(function() {
                                    input.val('');
                                    $searchButton.click(); 
                                })
                    $('.dataTables_filter input')
                        .off('.DT')
                        .on('keyup.DT', function (e) {
                            if (e.keyCode == 13) {
                                self.search(this.value).draw();
                            }
                        });
                    $('.dataTables_filter').append($searchButton, $clearButton);
                    ajax_load = 0;
                },
                "language": {
                    searchPlaceholder: "Search Something"
                },
                "order":[[0, "desc"]]
            });
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
