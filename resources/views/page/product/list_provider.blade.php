@extends('layouts.app')

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">List Provider</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Product</a></li>
                <li class="breadcrumb-item active">List Provider</li>
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
                            <option value="provider_name">Nama Provider</option>
                            <option value="provaider_type">Tipe Provider</option>
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
            <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addProvider"><i class="mdi mdi-plus mr-2"></i> Tambah Data </button>
            <table id="list_provider" class="table table-striped table-bordered no-wrap">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Nama Provider</th>
                        <th>Tipe Provider</th>
                        <th>Status</th>
                        {{-- <th>Group Provider</th> --}}
                    </tr>
                </thead>
                <tbody style="font-size:10pt">

                </tbody>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Nama Provider</th>
                        <th>Tipe Provider</th>
                        <th>Status</th>
                        {{-- <th>Group Provider</th> --}}
                    </tr>
                </thead>
            </table>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Content -->
    <!-- ============================================================== -->

    <!-- modal tambah -->
    <div class="modal fade bs-example-modal-lg" id="addProvider" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Penambahan Data Provider Max</h4>
                    <button type="button" onclick="clearForm('form-insert-provider')" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <form id="form-insert-provider" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Nama Provider</label>
                                    <input type="text" name="nama" class="form-control" placeholder="Nama Provider">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Tipe Provider</label>
                                    <select class="form-control custom-select"  name="tipe" data-placeholder="Choose a Category" tabindex="1">
                                        <option value="">-- pilih tipe --</option>
                                        <option value="TV KABEL">TV KABEL</option>
                                        <option value="GAME">GAME</option>
                                        <option value="KUOTA">KUOTA</option>
                                        <option value="FINANCE">FINANCE</option>
                                        <option value="PAJAK DAERAH">PAJAK DAERAH</option>
                                        <option value="PULSA">PULSA</option>
                                        <option value="PDAM">PDAM</option>
                                        <option value="KARTU KREDIT">KARTU KREDIT</option>
                                        <option value="PASCA BAYAR">PASCA BAYAR</option>
                                        <option value="ASURANSI">ASURANSI</option>
                                        <option value="TELEPON RUMAH">TELEPON RUMAH</option>
                                        <option value="TELKOM">TELKOM</option>
                                        <option value="VOUCHER TV">VOUCHER TV</option>
                                        <option value="ETOOL">ETOOL</option>
                                        <option value="PLN TOKEN">PLN TOKEN</option>
                                        <option value="KREDIT">KREDIT</option>
                                        <option value="PLN">PLN</option>
                                        <option value="WIFI ID">WIFI ID</option>
                                        <option value="BPJS">BPJS</option>
                                        <option value="SMS">SMS</option>
                                        <option value="PGN">PGN</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Prefix Provider</label>
                                    <input type="text" class="form-control" name="prefix" placeholder="Prefix Provider" value="0" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">Status Provider</label>
                                    <select class="form-control custom-select"  name="status" id="target_provider" data-placeholder="Choose a Category" tabindex="1">
                                        <option value="">-- Pilih Status --</option>
                                        <option value="Active">Active</option>
                                        <option value="Non Active">Non Active</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Key Approval</label>
                                    <input type="text" class="form-control" name="key" placeholder="Masukan Key">
                                </div>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="clearForm('form-insert-provider')" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" onclick="submitProvider()" class="btn btn-success">Simpan</button>
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
        allDataMember();



       function search(){
            $('#list_provider').DataTable().destroy();
            var column_search = document.getElementById('column_search');
            var value_search = document.getElementById('value_search');
            var status_search = document.getElementById('status_search');
            allDataMember(column_search.value, value_search.value, status_search.value);
        };

        function resetDatatable(){
            $('#list_provider').DataTable().destroy();
            allDataMember();
        };

        function allDataMember(column, value, status){
            // console.log(status_product)
            $('#list_provider').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    "url" : "/product/get_all_provider",
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
                    {"data": "provider_name"},
                    {"data": "provaider_type"},
                    {"data": "provaider_status"}
                    // {"data": "provaider_group"}
                ]
            });
        }

        // Insert data
        function submitProvider() {
            $.ajax({
                url: "/product/insert_provider",
                method: 'POST',
                dataType: 'json',
                data: $('#form-insert-provider').serialize(),
                success: function (data) {
                    // console.log(data)
                    if (data.respStatus) {
                        $('#list_provider').DataTable().ajax.reload();
                        $('#addProvider').modal('hide');
                        clearForm('form-insert-provider')
                        Swal.fire("PROSES BERHASIL", data.respMessage, "success");
                    } else {
                        Swal.fire("PROSES GAGAL", data.respMessage, "error");
                    }
                }
            });
        };

        function deleteProvider(id) {
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
                        url: "/product/delete_provider",
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "<?= csrf_token()?>",
                            id: id,
                        },
                        success: function (data) {
                            if (data.respStatus) {
                                $('#list_provider').DataTable().ajax.reload();
                                Swal.fire("PROSES BERHASIL", data.respMessage, "success");
                            } else {
                                Swal.fire("PROSES GAGAL", data.respMessage, "error");
                            }
                        }
                    });
                }
            });
        }

    function ubahProvider(id){
            var dialog = bootbox.dialog({
                title: 'Perubahan Data List Provider',
                size: "large",
                message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
            }).find(".modal-dialog").addClass("modal-dialog-centered");
            $.get("/product/get_detail_provider/" + id, function(data, status){
                var message = '<form id="form-ubah-provider">';

                message += '<input class="form-control" type="hidden" name="id" value="' + data.provider_id + '"/>';
                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="mbr_code" class="control-label">Nama Provider</label><input class="form-control" type="text" id="mbr_code" name="nama" placeholder="Nama Provider" value="' + data.provider_name + '" autocomplete="off" required/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="mbr_name" class="control-label">Tipe Provider</label><select class="form-control custom-select"  name="tipe" data-placeholder="Choose a Category" tabindex="1"><option value="'+ data.provaider_type +'" >'+ data.provaider_type +'</option><option value="TV">TV</option><option value="Game">Game</option><option value="Kouta">Kouta</option></select></div></div></div>';

                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="konter_name" class="control-label">Prefix Provider</label><input class="form-control" type="text" id="konter_name" name="prefix" placeholder=" " value="' + data.provider_pref + '" autocomplete="off"/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="konter_addres" class="control-label">Status</label><select class="form-control custom-select"  name="status" data-placeholder="Choose a Category" tabindex="1"><option value="'+ data.provaider_status +'" >'+ data.provaider_status +'</option><option value="Active">Active</option><option value="Non Active">Non Active</option></select></div></div></div>';
                // message += '<div class="row"><div class="col"><div class="form-group"><label for="konter_kota" class="control-label">Key</label><input class="form-control" type="text" id="konter_kota" name="key" placeholder="Kota Outlet" value="" autocomplete="off"/></div></div></div>';


                message += '<div class="row"><div class="col"><div class="form-group"><label for="key_approval" class="control-label">Key Approval</label><input class="form-control" type="text" id="key_approval" name="key_approval" placeholder="Key Approval" value="" autocomplete="off"/></div></div></div>';

                message += '<div class="text-right"><button id="ubahProviderButtonLoading" class="btn btn-success disabled" style="display: none;">Loading...</button><button id="ubahProviderButtonSubmit" type="submit" class="btn btn-success"><i class="mdi mr-2 mdi-content-save"></i>Simpan</button></div></form>'

                dialog.init(function(){
                    dialog.find('.bootbox-body').html(message);
                });

                $(document).ready(function(){
                    $("#form-ubah-provider").submit(function(event){
                        event.preventDefault();

                        $('#ubahProviderButtonLoading').show();
                        $('#ubahProviderButtonSubmit').hide();
                        $('.bootbox-close-button').hide();

                        var formData = new FormData(this);

                        $.ajax({
                            type:'POST',
                            dataType: 'json',
                            url: '/product/update_provider',
                            data:formData,
                            contentType: false,
                            cache: false,
                            processData: false,
                            success:function(data){
                                $('#ubahProviderButtonLoading').hide();
                                $('#ubahProviderButtonSubmit').show();
                                $('.bootbox-close-button').show();
                                if(data.respStatus){
                                    Swal.fire(
                                        'Success!',
                                        'Provider successfully updated.',
                                        'success'
                                    ).then(() => {
                                        $('#list_provider').DataTable().ajax.reload(),
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
        function clearForm(data){
            document.getElementById(data).reset();
        }
    </script>
@endpush
