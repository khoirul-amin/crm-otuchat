@extends('layouts.app')

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Create Menu CRM</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">User CRM</a></li>
                <li class="breadcrumb-item active">Create Menu CRM</li>
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
                    <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addMenu"><i class="mdi mdi-plus mr-2"></i> Tambah Menu </button>
                    <table id="menu" class="table table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Menu</th>
                                <th>Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody style="font-size:10pt">
        
                        </tbody>
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Menu</th>
                                <th>Sub Menu</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

            <div class="col-sm-6">
                <div class="table-responsive">
                    <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addSubMenu"><i class="mdi mdi-plus mr-2"></i> Tambah Sub Menu </button>
                    <table id="sub_menu" class="table table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Menu</th>
                                <th>Sub Menu</th>
                            </tr>
                        </thead>
                        <tbody style="font-size:10pt">
        
                        </tbody>
                        <thead>
                            <tr>
                                <th>Action</th>
                                <th>Menu</th>
                                <th>Sub Menu</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

        <!-- modal tambah menu -->
        <div class="modal fade bs-example-modal-lg" data-backdrop="static" id="addMenu" tabindex="-1" role="dialog" aria-labelledby="addMenuLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="addMenuLabel">Penambahan Data Menu CRM</h4>
                        <button type="button" onclick="clearForm('form-insert-menu')" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-insert-menu" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Name Menu</label>
                                        <input type="text" name="nama" class="form-control" placeholder="Name Menu">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Icon</label>
                                        <input type="text" class="form-control" name="deskripsi" placeholder="Ion" value="">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Key Approval</label>
                                        <input type="text" class="form-control" name="key" placeholder="Masukan Key">
                                    </div>
                                </div>
                            </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="clearForm('form-insert-menu')" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" onclick="addMenu()" class="btn btn-success">Simpan</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <!-- modal ubah menu -->
        <div class="modal fade bs-example-modal-lg" data-backdrop="static" id="updateMenu" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myLargeModalLabel">Penambahan Data Menu CRM</h4>
                        <button type="button" onclick="clearForm('form-update-menu')" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-update-menu" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" id='id_menu' name='id_menu' value=""/>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Name Menu</label>
                                        <input type="text" id="nama_menu" name="nama" class="form-control" placeholder="Name Menu">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Icon</label>
                                        <input type="text" class="form-control" id="deskripsi" name="deskripsi" placeholder="Ion" value="">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Key Approval</label>
                                        <input type="text" class="form-control" name="key" placeholder="Masukan Key">
                                    </div>
                                </div>
                            </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="clearForm('form-update-menu')" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" onclick="updateMenu()" class="btn btn-success">Simpan</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

        <!-- modal tambah submenu-->
        <div class="modal fade bs-example-modal-lg" data-backdrop="static" id="addSubMenu" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myLargeModalLabel">Penambahan Data  SubMenu CRM</h4>
                        <button type="button" onclick="clearForm('form-insert-provider')" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-insert-provider" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Nama Sub Menu</label>
                                        <input type="text" name="nama" class="form-control" placeholder="Nama Sub Menu">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Menu</label>
                                        <select class="form-control custom-select"  name="menu" data-placeholder="Choose a Category" tabindex="1">
                                            <option value="">-- Pilih Menu --</option>
                                            @foreach ($menu as $menu)
                                                <option value="{{$menu->id}}">{{$menu->nama_menu}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Root/Endpoint</label>
                                        <input type="text" class="form-control" name="endpoint" placeholder="Root/Endpoint">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Key Approval</label>
                                        <input type="text" class="form-control" name="key" placeholder="Masukan Key">
                                    </div>
                                </div>
                            </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="clearForm('form-insert-provider')" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" onclick="addSubMenu()" class="btn btn-success">Simpan</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->


        <!-- modal ubah submenu -->
        <div class="modal fade bs-example-modal-lg" data-backdrop="static" id="updateSubMenu" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myLargeModalLabel">Penambahan Data  SubMenu CRM</h4>
                        <button type="button" onclick="clearForm('form-insert-provider')" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    </div>
                    <div class="modal-body">
                        <form id="form-update-provider" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <input type="hidden" id="id_submenu" name="id_submenu" value="" />
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Nama Sub Menu</label>
                                        <input type="text" id="nama_submenu" name="nama" class="form-control" placeholder="Nama Sub Menu">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Menu</label>
                                        <select class="form-control custom-select"  name="menu" id="id_menu_submenu" data-placeholder="Choose a Category" tabindex="1">
                                            {{-- <option value="">-- Pilih Menu --</option> --}}
                                            @foreach ($menu1 as $menu)
                                                <option value="{{$menu->id}}">{{$menu->nama_menu}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Root/Endpoint</label>
                                        <input type="text" id="endpoint" class="form-control" name="endpoint" placeholder="Root/Endpoint">
                                    </div>
                                </div>
                            </div>
                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label class="control-label">Key Approval</label>
                                        <input type="text" class="form-control" name="key" placeholder="Masukan Key">
                                    </div>
                                </div>
                            </div> --}}
                    </div>
                    <div class="modal-footer">
                        <button type="button" onclick="clearForm('form-insert-provider')" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" onclick="updateSubMenu()" class="btn btn-success">Simpan</button>
                    </div>
                    </form>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->

@endsection

@push('scripts')
<script>
    listSubMenu()
    function listSubMenu(){
        $('#sub_menu').DataTable({
            lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
            "processing": true,
            "serverSide": true,
            // "searching": false,
            "ajax": {
                "url" : "/user/get_data_menu",
                "dataType": "json",
                "type": "POST",
                "data": {
                    "_token": "<?= csrf_token()?>"
                }
            },
            "buttons": [
                "csv","excel","pdf","print"
            ],
            "columns" : [
                {"data": "action"},
                {"data": "menu"},
                {"data": "sub_menu"},
            ]
        });
    }  
    listMenu()
    function listMenu(){
        $('#menu').DataTable({
            lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
            "processing": true,
            "serverSide": true,
            // "searching": false,
            "ajax": {
                "url" : "/user/data_menu",
                "dataType": "json",
                "type": "POST",
                "data": {
                    "_token": "<?= csrf_token()?>"
                }
            },
            "buttons": [
                "csv","excel","pdf","print"
            ],
            "columns" : [
                {"data": "action"},
                {"data": "menu"},
                {"data": "deskripsi"},
            ]
        });
    }  

    function setMenu(id,nama,deskripsi){
        $("#id_menu").val(id);
        $("#nama_menu").val(nama);
        $("#deskripsi").val(deskripsi);
    }
    function setSubMenu(id,nama_submenu,id_menu,endpoint){
        $("#id_submenu").val(id);
        $("#nama_submenu").val(nama_submenu);
        $("#id_menu_submenu").val(id_menu);
        $("#endpoint").val(endpoint);
    }

    function updateMenu(){
        $.ajax({
            url: "/user/update_menu",
            method: 'POST',
            dataType: 'json',
            data: $('#form-update-menu').serialize(),
            success: function (data) {
                // console.log(data)
                if (data.status) {
                    $('#menu').DataTable().ajax.reload();
                    $('#updateMenu').modal('hide');
                    Swal.fire("PROSES BERHASIL", data.message, "success");
                }
                else {
                    Swal.fire("PROSES GAGAL", data.message, "error");
                }
            }
        });
    }
    function updateSubMenu(){
        $.ajax({
            url: "/user/update_submenu",
            method: 'POST',
            dataType: 'json',
            data: $('#form-update-provider').serialize(),
            success: function (data) {
                // console.log(data)
                if (data.status) {
                    $('#sub_menu').DataTable().ajax.reload();
                    $('#updateSubMenu').modal('hide');
                    Swal.fire("PROSES BERHASIL", data.message, "success");
                }
                else {
                    Swal.fire("PROSES GAGAL", data.message, "error");
                }
            }
        });
    }

    function addMenu(){
        $.ajax({
            url: "/user/add_menu",
            method: 'POST',
            dataType: 'json',
            data: $('#form-insert-menu').serialize(),
            success: function (data) {
                // console.log(data)
                if (data.status) {
                    $('#menu').DataTable().ajax.reload();
                    $('#addMenu').modal('hide');
                    Swal.fire("PROSES BERHASIL", data.message, "success");
                }
                else {
                    Swal.fire("PROSES GAGAL", data.message, "error");
                }
            }
        });
    }
    function addSubMenu(){
        $.ajax({
            url: "/user/add_submenu",
            method: 'POST',
            dataType: 'json',
            data: $('#form-insert-provider').serialize(),
            success: function (data) {
                // console.log(data)
                if (data.status) {
                    $('#sub_menu').DataTable().ajax.reload();
                    $('#addSubMenu').modal('hide');
                    Swal.fire("PROSES BERHASIL", data.message, "success");
                }
                else {
                    Swal.fire("PROSES GAGAL", data.message, "error");
                }
            }
        });
    }
    function deleteMenu(id){
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
                    url: `/user/delete_menu/${id}`,
                    cache: false,
                    success: function(data){
                        if (data.status) {
                            $('#menu').DataTable().ajax.reload();
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
    function deleteSubMenu(id){
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
                    url: `/user/delete_submenu/${id}`,
                    cache: false,
                    success: function(data){
                        if (data.status) {
                            $('#sub_menu').DataTable().ajax.reload();
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

    function clearForm(data){
        document.getElementById(data).reset();
    }
 
</script>

    
@endpush