@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">Log Login</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Member</a></li>
            <li class="breadcrumb-item active">Log Login</li>
        </ol>
    </div>
</div>

<div class="p-30 bg-white">
    <form id="customSearch">
        <div class="row">
            <div class="col-3">
                <label for="custom_search" class="control-label">Target Pencarian:</label>
            </div>
            <div class="col-3">
                <label for="keyword" class="control-label">Kata Kunci:</label>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <select class="form-control" id="custom_search" name="custom_search" required>
                    <option value="" selected="selected" hidden="hidden">Pilih Target Pencarian</option>
                    <option value="1">ID EKL</option>
                    <option value="2">Nama Member</option>
                    <option value="3">No HP</option>
                </select>
            </div>
            <div class="col-3">
                <input id="keyword" type="text" class="form-control" name="keyword" placeholder="Masukkan Kata Kunci" required/>
            </div>
            <div class="col-3">
                <button type="submit" class="btn btn-success"><i class="mdi mdi-magnify mr-2"></i>Search</button>
                <button type="button" class="btn btn-warning" onclick="resetDataTable()"><i class="mdi mdi-undo mr-2"></i>Reset</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table id="log_login_member" class="table table-hover table-bordered no-wrap">
            <thead>
                <tr>
                    <th>ID EKL</th>
                    <th>Nama Member</th>
                    <th>No HP</th>
                    <th>Last Login</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody class="small"></tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        var table;
        $(document).ready(function(){
            dataTable(0);

            $("#customSearch").submit(function(event){
                event.preventDefault();

                $('#log_login_member').DataTable().destroy();

                dataTable({
                    custom_search: $('#custom_search').val(),
                    keyword: $('#keyword').val()
                });
            });
        });

        function resetDataTable(){
            $('#custom_search').val('');
            $('#keyword').val('');
            $('#log_login_member').DataTable().destroy();

            dataTable(0);
        }

        function dataTable(data_search){
            var data;
            if(data_search == 0){
                data = "default";
            }
            else{
                data = data_search;           
            }

            table = $('#log_login_member').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/member/get_all_log_login_member",
                    "dataType": "json",
                    "type": "POST",
                    "data": {"data_search": data}
                },
                "columns" : [
                    {"data": "mbr_code"},
                    {"data": "mbr_name"},
                    {"data": "mbr_mobile"},
                    {"data": "last_login"},
                    {"data": "action"}
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
                "order": [[ 3, "DESC" ]],
                "columnDefs": [
                    { "orderable": false, "targets": 4 }
                ],
                "searching": false
            });
        }

        function detailLogLoginMember(mbr_code){
            var message = '<div class="table-responsive"><table id="detail_log_login_member" class="table table-hover table-bordered no-wrap"><thead><tr><th>Tanggal Login</th><th>Alamat IP</th><th>Deskripsi</th><th>Device</th></tr></thead><tbody class="small"></tbody></table></div>';
            var dialog = bootbox.dialog({
                title: 'Detail Log Login ID EKL ' + mbr_code,
                size: "extra-large",
                message: message
            }).find(".modal-dialog").addClass("modal-dialog-centered");

            $('#detail_log_login_member').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/member/get_detail_log_login_member",
                    "dataType": "json",
                    "type": "POST",
                    "data": {mbr_code: mbr_code}
                },
                "columns" : [
                    {"data": "log_date"},
                    {"data": "ip"},
                    {"data": "log_desc"},
                    {"data": "device"}
                ],
                "initComplete" : function() {
                    var input = $('#detail_log_login_member_filter input').unbind(),
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
                    $('#detail_log_login_member_filter input')
                        .off('.DT')
                        .on('keyup.DT', function (e) {
                            if (e.keyCode == 13) {
                                self.search(this.value).draw();
                            }
                        });
                    $('#detail_log_login_member_filter').append($searchButton, $clearButton);
                },
                "language": {
                    searchPlaceholder: "Search Something"
                },
                "order": [[ 0, "DESC" ]]
            });
        }

    </script>
@endpush