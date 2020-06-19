@extends('layouts.app')

@section('content')

    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Manajemen Log Activity</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">User CRM</a></li>
                <li class="breadcrumb-item active">Manajemen Log Activity</li>
            </ol>
        </div>
    </div>

    <div class="p-30" style="background-color:white;">
        <h2>CRM Log Activity</h2><hr>
        <div class="row p-0 m-0">
            <div class="col">
                <div class="table-responsive">
                    <table id="dt_log_activity" class="table table-hover table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Ip Address</th>
                                <th>Waktu</th>
                                <th>Detail Activity</th>
                                <th>Mbr Code</th>
                                <th>Description</th>
                                <th>User Agent</th>
                            </tr>
                        </thead>
                        <tbody class="small"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <h2 class="mt-5">CRM Detail Activity</h2><hr>
        <div class="row p-0 m-0">
            <div class="col-sm-6">
                <div class="table-responsive">
                    <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addActivityDetail"><i class="mdi mdi-plus mr-2"></i>Tambah Activity Detail</button>
                    <table id="dt_activity_detail" class="table table-hover table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th class="w-25">Id</th>
                                <th class="w-50">Keterangan</th>
                                <th class="w-25">Action</th>
                            </tr>
                        </thead>
                        <tbody class="small"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal fade" id="addActivityDetail">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
        
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Tambah Activity Detail</h4>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="col">
                        <div class="form-group">
                            <label for="tambah_keterangan" class="control-label">Keterangan</label>
                            <input class="form-control" type="text" id="tambah_keterangan" name="keterangan" placeholder="Keterangan"/>
                        </div>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button id="tambahActivityDetail" class="btn btn-success" onclick="tambah()"><i class="mdi mdi-content-save"></i>Simpan</button>
                </div>
        
            </div>
        </div>
    </div>
    
@endsection

@push('scripts')
    <script>
        var table_log_activity, table_activity_detail;
        dataTableLogActivity();
        dataTableActivityDetail();

        function dataTableLogActivity(){
            table_log_activity = $('#dt_log_activity').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/user/get_log_activity",
                    "dataType": "json",
                    "type": "POST"
                },
                "columns" : [
                    {"data": "nama"},
                    {"data": "ip_address"},
                    {"data": "waktu"},
                    {"data": "keterangan"},
                    {"data": "mbr_code"},
                    {"data": "description"},
                    {"data": "user_agent"}
                ],
                initComplete: function(){
                    var api = this.api();
                    $('#dt_log_activity_filter input')
                        .off('.DT')
                        .on('keyup.DT', function (e) {
                            if (e.keyCode == 13) {
                                api.search(this.value).draw();
                            }
                        });
                },
                "order": [[ 2, "DESC" ]],
            });
        }

        function dataTableActivityDetail(){
            table_activity_detail = $('#dt_activity_detail').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/user/get_activity_detail",
                    "dataType": "json",
                    "type": "POST"
                },
                "columns" : [
                    {"data": "id", "className": "w-25"},
                    {"data": "keterangan", "className": "w-50"},
                    {"data": "action", "className": "w-25"}
                ],
                initComplete: function(){
                    var api = this.api();
                    $('#dt_activity_detail_filter input')
                        .off('.DT')
                        .on('keyup.DT', function (e) {
                            if (e.keyCode == 13) {
                                api.search(this.value).draw();
                            }
                        });
                },
            });
        }

        function tambah(){
            $.ajax({
                url: "/user/tambah_activity_detail",
                method: 'POST',
                type: 'POST',
                dataType: 'json',
                data: {keterangan: $('#tambah_keterangan').val()}
            }).done(function (response, textStatus, jqXHR){
                if(response.status == 'success'){
                    $("#addActivityDetail").modal("hide");
                    $("#tambah_keterangan").val("");
                    table_activity_detail.ajax.reload( null, false);
                    Swal.fire(
                        'Success',
                        response.reason,
                        'success'
                    )
                }
                else{
                    Swal.fire(
                        'Oops...',
                        response.reason,
                        'error'
                    )
                }
            }).fail(function (jqXHR, textStatus, errorThrown){
                console.error("The following error occurred: " + textStatus, errorThrown);
            }).always(function (){});
        }

        function ubah(id, keterangan){
            var message = '<form id="ubahActivityDetail">';
                message += '<div class="row"><div class="col-4"><div class="form-group"><label for="id_log_activity" class="control-label">Id Log Activity</label><input class="form-control" type="text" id="id_log_activity" name="id_log_activity" placeholder="ID Log Activity Detail" value="' + id + '" autocomplete="off" readonly required/></div></div>';
                message += '<div class="col-8"><div class="form-group"><label for="keterangan" class="control-label">Keterangan</label><input class="form-control" type="text" id="keterangan" name="keterangan" value="'+ keterangan +'" placeholder="Keterangan" autocomplete="off" required/></div></div></div>';

                message += '<div class="text-right"><button id="ubahButtonLoading" class="btn btn-success disabled" style="display: none;">Loading...</button><button id="ubahButtonSubmit" type="submit" class="btn btn-success"><i class="mdi mr-2 mdi-content-save"></i>Simpan</button></div></form>'

            bootbox.dialog({
                title: 'Perubahan Log Activity Detail',
                size: "large",
                message: message
            }).find(".modal-dialog").addClass("modal-dialog-centered");

            $(document).ready(function(){
                $("#ubahActivityDetail").submit(function(event){
                    event.preventDefault();

                    $('#ubahButtonLoading').show();
                    $('#ubahButtonSubmit').hide();
                    $('.bootbox-close-button').hide();

                    var formData = new FormData(this);

                    $.ajax({
                        type:'POST',
                        dataType: 'json',
                        url: '/user/ubah_activity_detail',
                        data:{id:id, keterangan:$('#keterangan').val()},
                        success:function(data){
                            $('#ubahMemberButtonLoading').hide();
                            $('#ubahMemberButtonSubmit').show();
                            $('.bootbox-close-button').show();
                            if(data.status === 'success'){
                                Swal.fire(
                                    'Success!',
                                    'Data Sukses Diupdate',
                                    'success'
                                ).then(() => {
                                    table_activity_detail.ajax.reload( null, false);
                                    bootbox.hideAll();
                                });
                            } else {
                                Swal.fire(
                                    'Oops...',
                                    data.reason,
                                    'error'
                                )
                            }
                        }
                    });

                });
            });
        }

        function hapus(id){
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data yang dihapus tidak bisa dikembalikan",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, data sudah benar',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: "/user/hapus_activity_detail",
                        method: 'POST',
                        type: 'POST',
                        dataType: 'json',
                        data: {id:id},
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
        }

    </script>
@endpush