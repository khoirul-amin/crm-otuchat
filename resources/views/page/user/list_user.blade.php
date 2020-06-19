@extends('layouts.app')

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Manajemen User CRM</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">User CRM</a></li>
                <li class="breadcrumb-item active">Manajemen User CRM</li>
            </ol>
        </div>
    </div>


    <!-- ============================================================== -->
    <!-- Content -->
    <!-- ============================================================== -->


    <div class="p-30" style="background-color:white">

        <div class="table-responsive">
            <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addUser"><i class="mdi mdi-plus mr-2"></i> Tambah User </button>
            <table id="list_user" class="table table-striped table-bordered no-wrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody style="font-size:10pt">

                </tbody>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>

        

        <!-- ============================================================== -->
        <!-- Modal Add -->
        <!-- ============================================================== -->
        <div class="modal fade bd-example-modal-lg" id="addUser" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title" id="addUserLabel">Tambah Data User</h3>
                <button type="button"  onclick="clearForm('formUser')" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                    <form id="formUser" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Nama</label>
                                        <input class="form-control" type="text"  name="nama" placeholder="Nama" value="" autocomplete="off" required/>
                                    </div>
                                </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Email</label>
                                    <input class="form-control" type="text" name="email" value="" placeholder="Email" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Password</label>
                                    <input class="form-control" type="text"  name="password" placeholder="Password" value="" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Role</label>
                                        <select class="form-control custom-select"  name="role"
                                            data-placeholder="Choose a Category" tabindex="1">
                                            <option value="" disabled selected>-- pilih role --</option>
                                            @foreach ($role as $role)
                                                <option value="{{$role->id}}">{{$role->nama_role}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                <button type="button"  onclick="clearForm('formUser')" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" onclick="addUser()" class="btn btn-success">Simpan</button>
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
        <div class="modal fade bd-example-modal-lg" id="updateUser" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="updateUserLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title" id="updateUserLabel">Ubah Data User</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                    <form id="formUpdateUser" enctype="multipart/form-data">
                        <input name="id" type="hidden" value="" id="id"/>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Nama</label>
                                        <input class="form-control" type="text" id="nama" name="nama" placeholder="Nama" value="" autocomplete="off" required/>
                                    </div>
                                </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Email</label>
                                    <input class="form-control" type="text" id="email" name="email" value="" placeholder="Email" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Role</label>
                                        <select class="form-control custom-select" id="role" name="role"
                                            data-placeholder="Choose a Category" tabindex="1">
                                            <option value="" disabled selected>-- pilih role --</option>
                                            @foreach ($role1 as $role)
                                                <option value="{{$role->id}}">{{$role->nama_role}}</option>
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
                <button type="button" onclick="updateUser()" class="btn btn-success">Simpan</button>
                </div>
            </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Modal Update -->
        <!-- ============================================================== -->


    </div>


@endsection


@push('scripts')
    <script>
        allDataUser();

        function getAllUser(){
            $('#list_user').DataTable().destroy();
            allDataUser();
        };
        function allDataUser(column, value){
            // console.log(search,value);
            $('#list_user').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                // "searching": false,
                "ajax": {
                    "url" : "/user/get_all_user",
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
                    {"data": "email"},
                    {"data": "role"},
                    {"data": "action"}
                    // {"data": "alamat"},
                ]
            });
        }

        function resetAuth(id){
            Swal.fire({
                title: 'Update Auth!',
                icon: 'question',
                text: "Anda akan mereset akun ini ?",
                confirmButtonColor: "#47A447",
                confirmButtonText: "Ok",
                cancelButtonText: "Batal",
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        type: "GET",
                        url: `/user/reset_auth/${id}`,
                        cache: false,
                        success: function(data){
                            // console.log(data)
                            if(data.status){
                                $('#list_user').DataTable().ajax.reload();
                                Swal.fire("PROSES BERHASIL", data.message, "success");
                            }else{
                                Swal.fire("PROSES GAGAL", data.message, "error");
                            }
                        }
                    });
                }
            });
                
        }
        function addUser(){
            $.ajax({
                url: "/user/add_user",
                method: 'POST',
                dataType: 'json',
                data: $('#formUser').serialize(),
                success: function (data) {
                    // console.log(data)
                    if (data.status) {
                        $('#list_user').DataTable().ajax.reload();
                        $('#addUser').modal('hide');
                        clearForm('formUser')
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                    }
                }
            });
        }

        function clearForm(data){
            document.getElementById(data).reset();
        }
        // Delete Data
        function deleteUser(data) {
            Swal.fire({
                    title: 'Hapus data!',
                    icon: 'question',
                    text: "Anda akan menghapus data ini",
                    confirmButtonColor: "#47A447",
                    confirmButtonText: "Ok",
                    cancelButtonText: "Batal",
                    input: 'text',
                    inputAttributes: {
                        autocapitalize: 'off',
                        placeholder: 'Masukan Key Authenticator',
                        required: true
                    },
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        $('html, body').css("cursor", "wait");
                        $.ajax({
                            url: "/user/delete_user",
                            method: 'POST',
                            processing: true,
                            dataType: 'json',
                            data: ({
                                "_token": "<?= csrf_token()?>",
                                id:data,
                                key: result.value,
                            }),
                            success: function (data) {
                                $('html, body').css("cursor", "auto");
                                if (data.status) {
                                    $('#list_user').DataTable().ajax.reload();
                                    Swal.fire("PROSES BERHASIL", data.message, "success");
                                }
                                else {
                                    Swal.fire("PROSES GAGAL", data.message, "error");
                                }
                            }
                        });
                    }
                });
        };

        function getUserById(id){
            $.ajax({
                type: "GET",
                url: `/user/get_user_by_id/${id}`,
                cache: false,
                success: function(data){
                    // console.log(data)
                    $("#nama").val(data.nama);  $("#email").val(data.email); $("#role").val(data.id_role); $("#id").val(data.id);
                }
            });
        }
        function updateUser(){
            $.ajax({
                url: "/user/update_user",
                method: 'POST',
                dataType: 'json',
                data: $('#formUpdateUser').serialize(),
                success: function (data) {
                    // console.log(data)
                    if (data.status) {
                        $('#list_user').DataTable().ajax.reload();
                        $('#updateUser').modal('hide');
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                    }
                }
            });
        }

        function resetPassword(id){
            Swal.fire({
                    title: 'Update Password!',
                    icon: 'warning',
                    text: "Silahkan masukkan password baru",
                    input: 'text',
                    confirmButtonColor: "#47A447",
                    confirmButtonText: "Ok",
                    cancelButtonText: "Batal",
                    showCancelButton: true,
                }).then((result) => {
                    if(result.value){
                        $.ajax({
                            url:"/user/update_password",
                            method: 'POST',
                            dataType: 'json',
                            data: {
                                "_token": "<?= csrf_token()?>",
                                id: id,
                                password: result.value
                            },
                            success: function (data) {
                                if (data.status) {
                                    $('#all_request').DataTable().ajax.reload();
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

    </script>
@endpush