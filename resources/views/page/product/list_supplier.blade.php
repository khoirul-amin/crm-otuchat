@extends('layouts.app')

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">List Supplier</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Product</a></li>
                <li class="breadcrumb-item active">List Supplier</li>
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
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Target Pencarian</label>
                        <select class="form-control custom-select"  name="column_search" id="column_search" data-placeholder="Choose a Category" tabindex="1">
                            <option value="supliyer_name">Nama Supplier</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Kata Kunci</label>
                        <input type="text" class="form-control" placeholder="Masukan kata pencarian" name="value_search" id="value_search">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Cari berdasarkan status</label>
                        <select class="form-control custom-select" name="status_search" id="status_search" data-placeholder="Choose a Category" tabindex="1">
                            <option value="">Semua</option>
                            <option value="Active">Active</option>
                            <option value="Not Active">Non Active</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
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
            <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addSupplier"><i class="mdi mdi-plus mr-2"></i> Tambah Data </button>
            <table id="list_supplier" class="table table-striped table-bordered no-wrap">
                <thead>
                    <tr>
                        <th rowspan="2">Action</th>
                        <th rowspan="2">Detail</th>
                        <th colspan="2">URL/Link</th>

                        {{-- <th colspan="2">Data Login</th>

                        <th rowspan="2">Alamat IP</th> --}}
                    </tr>
                    <tr>
                        <th>Top Up</th>
                        <th>Report</th>
                        {{-- <th>Top Up</th>
                        <th>Report</th> --}}
                    </tr>
                </thead>
                <tbody style="font-size:10pt">
                </tbody>
            </table>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Content -->
    <!-- ============================================================== -->

    <!-- modal tambah -->
    <div class="modal fade bs-example-modal-lg" id="addSupplier" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Penambahan Data Provider Max</h4>
                    <button type="button" onclick="clearForm('form-insert-supplier')" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form id="form-insert-supplier" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Nama Supplier</label>
                                    <input type="text" name="nama" class="form-control" placeholder="Nama Supplier">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Alamat IP</label>
                                    <input type="text" name="alamatip" class="form-control" placeholder="Alamat IP">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">URL Halaman Top-Up</label>
                                    <input type="text" class="form-control" name="urltopup" placeholder="URL Top-Up">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Username Halaman Top-Up</label>
                                    <input type="text" class="form-control" name="usernametopup" placeholder="Username Top-Up">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Password Halaman Top-Up</label>
                                    <input type="text" class="form-control" name="passwordtopup" placeholder="Password Top-Up">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">URL Halaman Report</label>
                                    <input type="text" class="form-control" name="urlreport" placeholder="URL Top-Up">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Username Halaman Report</label>
                                    <input type="text" class="form-control" name="usernamereport" placeholder="Username Report">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Password Halaman Report</label>
                                    <input type="text" class="form-control" name="passwordreport" placeholder="Password Report">
                                </div>
                            </div>
                        </div>
                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="clearForm('form-insert-supplier')" data-dismiss="modal">Batal</button>
                    <button type="button" onclick="submitSupplier()" class="btn btn-success">Simpan</button>
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
        allDataSupplier();

       function search(){
            $('#list_supplier').DataTable().destroy();
            var column_search = document.getElementById('column_search');
            var value_search = document.getElementById('value_search');
            var status_search = document.getElementById('status_search');
            allDataSupplier(column_search.value, value_search.value, status_search.value);
        };

        function resetDatatable(){
            $('#list_supplier').DataTable().destroy();
            allDataSupplier();
        };

        function allDataSupplier(column, value, status){
            // console.log(status_product)
            $('#list_supplier').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    "url" : "/product/get_all_supplier",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "_token": "<?= csrf_token()?>",
                        column : column,
                        value : value,
                        status : status
                    }
                },
                "columns" : [
                    {"data": "action"},
                    {"data": "detail"},
                    {"data": "urltopup"},
                    {"data": "urlreport"}
                ]
            });
        }

        // Insert data
        function submitSupplier() {
            $.ajax({
                url: "/product/insert_supplier",
                method: 'POST',
                dataType: 'json',
                data: $('#form-insert-supplier').serialize(),
                success: function (data) {
                    // console.log(data)
                    if (data.respStatus) {
                        $('#list_supplier').DataTable().ajax.reload();
                        $('#addSupplier').modal('hide');
                        clearForm('form-insert-supplier')
                        Swal.fire("PROSES BERHASIL", data.respMessage, "success");
                    } else {
                        Swal.fire("PROSES GAGAL", data.respMessage, "error");
                    }
                }
            });
        };

        function deleteSupplier(id) {
            Swal.fire({
                title: 'Hapus data!',
                icon: 'warning',
                text:  "Apa Anda yakin melakukannya?",
                confirmButtonColor: "#47A447",
                confirmButtonText: "Iya, saya yakin!",
                cancelButtonText: "Batal",
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "/product/delete_supplier",
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "<?= csrf_token()?>",
                            id: id,
                        },
                        success: function (data) {
                            if (data.respStatus) {
                                $('#list_supplier').DataTable().ajax.reload();
                                Swal.fire("PROSES BERHASIL", "Data telah terhapus", "success");
                            } else {
                                Swal.fire("PROSES GAGAL", data.respMessage, "error");
                            }
                        }
                    });
                }
            });
        }

        function ubahSupplier(id){
            var dialog = bootbox.dialog({
                title: 'Perubahan Data List Supplier',
                size: "large",
                message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
            }).find(".modal-dialog").addClass("modal-dialog-centered");
            $.get("/product/get_detail_supplier/" + id, function(data, status){
                var message = '<form id="form-ubah-supplier">';

                message += '<input class="form-control" type="hidden" name="id" value="' + data.supliyer_id + '"/>';
                message += '<div class="row"><div class="col-md-6"><div class="form-group"><label class="control-label">Nama Supplier</label><input type="text" name="nama" value="'+ data.supliyer_name +'" class="form-control" placeholder="Nama Supplier"></div></div><div class="col-md-6"><div class="form-group"><label class="control-label">Alamat IP</label><input type="text" name="alamatip" class="form-control" value="' + data.ip_in + '" placeholder="Alamat IP"></div></div></div>';
                message += '<div class="row"><div class="col-md-12"><div class="form-group"><label class="control-label">URL Halaman Top-Up</label><input type="text" class="form-control" value="' + data.url_topup + '" name="urltopup" placeholder="URL Top-Up"></div></div></div>';

                message += '<div class="row"><div class="col-md-6"><div class="form-group"><label class="control-label">Username Halaman Top-Up</label><input type="text" class="form-control" name="usernametopup" value="' + data.user_url + '" placeholder="Username Top-Up"></div></div><div class="col-md-6"><div class="form-group"><label class="control-label">Password Halaman Top-Up</label><input type="text" class="form-control" name="passwordtopup" value="' + data.paswd_url + '" placeholder="Password Top-Up"></div></div></div>';
                message += '<div class="row"><div class="col-md-12"><div class="form-group"><label class="control-label">URL Halaman Report</label><input type="text" class="form-control" name="urlreport" value="' + data.url_report + '" placeholder="URL Top-Up"></div></div></div>';

                message += '<div class="row"><div class="col-md-6"><div class="form-group"><label class="control-label">Username Halaman Report</label><input type="text" class="form-control" name="usernamereport" value="' + data.usr_report + '" placeholder="Username Report"></div></div><div class="col-md-6"><div class="form-group"><label class="control-label">Password Halaman Report</label><input type="text" class="form-control" name="passwordreport" value="' + data.pswd_report + '" placeholder="Password Report"></div></div></div>';

                message += '<div class="text-right"><button id="ubahSupplierButtonLoading" class="btn btn-success disabled" style="display: none;">Loading...</button><button id="ubahSupplierButtonSubmit" type="submit" class="btn btn-success"><i class="mdi mr-2 mdi-content-save"></i>Simpan</button></div></form>'

                dialog.init(function(){
                    dialog.find('.bootbox-body').html(message);
                });

                $(document).ready(function(){
                    $("#form-ubah-supplier").submit(function(event){
                        event.preventDefault();

                        $('#ubahSupplierButtonLoading').show();
                        $('#ubahSupplierButtonSubmit').hide();
                        $('.bootbox-close-button').hide();

                        var formData = new FormData(this);

                        $.ajax({
                            type:'POST',
                            dataType: 'json',
                            url: '/product/update_supplier',
                            data:formData,
                            contentType: false,
                            cache: false,
                            processData: false,
                            success:function(data){
                                $('#ubahSupplierButtonLoading').hide();
                                $('#ubahSupplierButtonSubmit').show();
                                $('.bootbox-close-button').show();
                                if(data.respStatus){
                                    Swal.fire(
                                        'Success!',
                                        'Provider successfully updated.',
                                        'success'
                                    ).then(() => {
                                        $('#list_supplier').DataTable().ajax.reload();
                                        bootbox.hideAll();
                                    });
                                } else {
                                    Swal.fire(
                                        'Oops...',
                                        'Terjadi Kesalahan sistem, silahkan ulangi lagi',
                                        'error'
                                    )
                                }
                            }
                        });

                    });
                });
            });
        }

        function OpenLink(url) {
            var win = window.open(url, '_blank');
            win.focus();
        }

        function clearForm(data){
            document.getElementById(data).reset();
        }
    </script>
@endpush
