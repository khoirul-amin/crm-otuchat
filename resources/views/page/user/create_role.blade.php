@extends('layouts.app')

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Create Role User</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">User CRM</a></li>
                <li class="breadcrumb-item active">Create Role User</li>
            </ol>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Content -->
    <!-- ============================================================== -->


    <div class="p-30" style="background-color:white;">
        <div class="row p-0 m-0">
            <div class="col-sm-6">
                <div class="table-responsive">
                    <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addRole"><i class="mdi mdi-plus mr-2"></i> Tambah Role </button>
                    <table id="list_role" class="table table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>Nama Role</th>
                                <th>Divisi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody style="font-size:10pt">
        
                        </tbody>
                        <thead>
                            <tr>
                                <th>Nama Role</th>
                                <th>Divisi</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
            
    {{-- <input type="checkbox" id="md_checkbox_21" class="filled-in chk-col-red" checked />
    <label for="md_checkbox_21">Red</label> --}}
        <!-- ============================================================== -->
        <!-- Modal Add -->
        <!-- ============================================================== -->
        <div class="modal" id="addRole" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addRoleLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title" id="addRoleLabel">Tambah Data Role</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                    <form id="formRole" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Nama Role</label>
                                        <input class="form-control" type="text"  name="nama" placeholder="Nama" value="" autocomplete="off" required/>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="example-text-input" class="control-label">Divisi</label>
                                            <select class="form-control custom-select"  name="divisi"
                                                data-placeholder="Choose a Category" tabindex="1">
                                                <option value="" disabled selected>-- pilih divisi --</option>
                                                @foreach ($divisi as $divisi)
                                                    <option value="{{$divisi->id}}">{{$divisi->nama_divisi}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" onclick="addRole()" class="btn btn-success">Simpan</button>
                </div>
            </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Modal Add -->
        <!-- ============================================================== -->


        <!-- ============================================================== -->
        <!-- Modal Update -->
        <!-- ============================================================== -->
        <div class="modal" id="updateRole" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="updateRoleLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title" id="updateRoleLabel">Update Data Role</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                    <form id="formUpdateRole" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="id" />
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Nama Role</label>
                                        <input class="form-control" type="text" id="nama" name="nama" placeholder="Nama" value="" autocomplete="off" required/>
                                    </div>
                                </div>
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <div class="form-group">
                                            <label for="example-text-input" class="control-label">Divisi</label>
                                            <select class="form-control custom-select" id="divisi" name="divisi"
                                                data-placeholder="Choose a Category" tabindex="1">
                                                <option value="" disabled selected>-- pilih divisi --</option>
                                                @foreach ($divisi1 as $divisi1)
                                                    <option value="{{$divisi1->id}}">{{$divisi1->nama_divisi}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" onclick="updateRole()" class="btn btn-success">Simpan</button>
                </div>
            </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Modal Update -->
        <!-- ============================================================== -->



        <!-- ============================================================== -->
        <!-- Modal Permisission -->
        <!-- ============================================================== -->
        <div class="modal fade bd-example-modal-lg" id="kelolaMenu" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="kelolaMenuLabel" aria-hidden="true">
            <div class="modal-dialog  modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title" id="kelolaMenuLabel">Menu Akses Permission</h3>
                <button type="button" onclick="closeModal()" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                    <form id="menuPermission" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="id_permission" />
                        <div class="modal-body">
                            <div class="row">
                                <table class="table table-striped">
                                    <thead>
                                      <tr>
                                        <th scope="col">Menu</th>
                                        <th scope="col">Status Akses</th>
                                      </tr>
                                    </thead>
                                    <tbody> 
                                        @foreach ($menu as $menu)
                                            <tr>
                                                <td colspan="2"><b>{{$menu->nama_menu}}</b></td>
                                            </tr>
                                            @foreach ($sub_menu as $sub)
                                                @if($sub->id_menu == $menu->id)
                                                    <tr>
                                                        <td class="pl-4">{{$sub->nama_submenu}}</td>
                                                        <td><input type="checkbox" name="{{$sub->id}}" value="{{$sub->id}}" id="{{$sub->id}}" class="filled-in chk-col-teal" /><label for="{{$sub->id}}"></label></td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"  onclick="closeModal()" data-dismiss="modal">Batal</button>
                    <button type="button" onclick="addPermission()" class="btn btn-success">Simpan</button>
                </div>
            </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Modal Permisission -->
        <!-- ============================================================== -->

@endsection


@push('scripts')
    <script>
        listRole();

        function getAllUser(){
            $('#list_role').DataTable().destroy();
            listRole();
        };
        function listRole(column, value){
            // console.log(search,value);
            $('#list_role').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                // "searching": false,
                "ajax": {
                    "url" : "/user/get_all_role",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "_token": "<?= csrf_token()?>",
                        column : column,
                        value : value
                    }
                },
                "buttons": [
                    "csv","excel","pdf","print"
                ],
                "columns" : [
                    {"data": "nama"},
                    {"data": "divisi"},
                    {"data": "action"},
                    // {"data": "action"}
                    // {"data": "alamat"},
                ]
            });
        }

        function addRole(){
            $.ajax({
                url: "/user/add_role",
                method: 'POST',
                dataType: 'json',
                data: $('#formRole').serialize(),
                success: function (data) {
                    if (data.status) {
                        $('#list_role').DataTable().ajax.reload();
                        $('#addRole').modal('hide');
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                    }
                }
            });
        }

        function deleteRole(id){
            Swal.fire({
                    title: 'Hapus data!',
                    icon: 'question',
                    text: "Anda akan menghapus data ini",
                    confirmButtonColor: "#47A447",
                    confirmButtonText: "Ok",
                    cancelButtonText: "Batal",
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            type: "GET",
                            url: `/user/delete_role/${id}`,
                            cache: false,
                            success: function(data){
                                if (data.status) {
                                    $('#list_role').DataTable().ajax.reload();
                                    Swal.fire("PROSES BERHASIL", data.message, "success");
                                }
                                else {
                                    Swal.fire("PROSES GAGAL", data.message, "error");
                                }
                            }
                        });
                    }
                });
        }

        function getRoleID(id){
            $.ajax({
                type: "GET",
                url: `/user/get_role_id/${id}`,
                cache: false,
                success: function(data){
                    $("#id").val(data.id);  $("#nama").val(data.nama_role); $("#divisi").val(data.id_divisi);
                }
            });
        }

        function updateRole(){
            $.ajax({
                url: "/user/update_role",
                method: 'POST',
                dataType: 'json',
                data: $('#formUpdateRole').serialize(),
                success: function (data) {
                    if (data.status) {
                        $('#list_role').DataTable().ajax.reload();
                        $('#updateRole').modal('hide');
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                    }
                }
            });
        }

        function getSubMenu(id, param, submenu){
            // console.log(submenu)
            $('html, body').css("cursor", "wait");
            $('#id_permission').val(param);
            if(id){
                $.ajax({
                    type:"GET",
                    "url" : `/user/get_permission_by_id/${id}`,
                    cache: false,
                    success: function(data){
                        $('html, body').css("cursor", "auto");
                        for( i=0; i <= submenu-1; i++ ){
                            if(data[i]){
                                $("#"+data[i].id).prop("checked", true);
                            }
                        }
                    }
                })
            }else{

                $('html, body').css("cursor", "auto");
            }
        }

        function closeModal(){
            document.getElementById("menuPermission").reset();
        }


        function addPermission(){
            $.ajax({
                url: "/user/add_permission",
                method: 'POST',
                dataType: 'json',
                data: $('#menuPermission').serialize(),
                success: function (data) {
                    if (data.status) {
                        $('#kelolaMenu').modal('hide');
                        document.getElementById("menuPermission").reset();
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                        getAllUser();
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                        getAllUser();
                    }
                }
            });
        }
    </script>
@endpush