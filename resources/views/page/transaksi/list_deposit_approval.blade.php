@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">List Deposit Approval</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Transaksi</a></li>
            <li class="breadcrumb-item active">List Deposit Approval</li>
        </ol>
    </div>
</div>

<div class="p-30 bg-white">
    <form id="cariDepositApproval">
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
        <table id="list_deposit_approval" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Tanggal Permintaan</th>
                    <th>ID EKL</th>
                    <th>Nama Member</th>
                    <th>Jumlah Top-Up</th>
                    <th>Bank</th>
                    <th>Invoice</th>
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
        var custom_search = false;
        var table;

        $(document).ready(function(){
            dataTable();

            $("#cariDepositApproval").submit(function(event){
                event.preventDefault();
                $('#list_deposit_approval').DataTable().destroy();
                custom_search = true;
                dataTable();
            });
        });

        function resetDataTable(){
            $('#date_range').val('');
            custom_search = false;
            $('#list_deposit_approval').DataTable().destroy();

            dataTable();
        }
        
        function dataTable(){
            table = $('#list_deposit_approval').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/transaksi/get_list_deposit_approval",
                    "dataType": "json",
                    "type": "POST",
                    "data": {"start_date": start_date, "end_date": end_date, "custom_search": custom_search }
                },
                "columns": [
                    {"data": "action", "className": "text-center"},
                    {"data": "deposit_date"},
                    {"data": "mbr_code"},
                    {"data": "mbr_name"},
                    {"data": "jumlah_topup", "className": "text-right"},
                    {"data": "deposit_bank"},
                    {"data": "deposit_code"}
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
            $('#list_deposit_approval').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/transaksi/get_list_deposit_approval",
                    "dataType": "json",
                    "type": "POST",
                    "data": {"start_date": start_date, "end_date": end_date }
                },
                "columns": [
                    {"data": "action", "className": "text-center"},
                    {"data": "deposit_date"},
                    {"data": "mbr_code"},
                    {"data": "mbr_name"},
                    {"data": "jumlah_topup", "className": "text-right"},
                    {"data": "deposit_bank"},
                    {"data": "deposit_code"}
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

        function approveDeposit(deposit_id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, approve it!',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`/transaksi/approve_deposit_approval/${deposit_id}`)
                    .then(response => {
                        return response.json();
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value.status === 'success') {
                    Swal.fire(
                        'Approved!',
                        'Deposit has been succcesfully approved.',
                        'success'
                    )
                    table.ajax.reload( null, false);
                }
                else{
                    Swal.fire(
                        'Something went wrong!',
                        result.value.reason,
                        'error'
                    )
                }
            })
        }

        function rejectDeposit(deposit_id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reject it!',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`/transaksi/reject_deposit_approval/${deposit_id}`)
                    .then(response => {
                        return response.json();
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value.status === 'success') {
                    Swal.fire(
                        'Rejected!',
                        'Deposit has been succcesfully rejected.',
                        'success'
                    )
                    table.ajax.reload(null, false);
                }
                else{
                    Swal.fire(
                        'Something went wrong!',
                        result.value.reason,
                        'error'
                    )
                }
            })
        }

    </script>
@endpush
