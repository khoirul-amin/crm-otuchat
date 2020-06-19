@extends('layouts.app')

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Request Upgrade Limit</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Member</a></li>
                <li class="breadcrumb-item active">Request Upgrade Limit</li>
            </ol>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Content -->
    <!-- ============================================================== -->

    <div class="p-30 bg-white">

        <!-- ============================================================== -->
        <!-- Searcing -->
        <!-- ============================================================== -->


        <div class="form-body">
                <div class="row">
                <div class="col-sm-3">
                    <div class="form-group">
                        <label class="control-label">Pencarian Berdasarkan Tanggal : </label>
                        <input id="toggle_tanggal" type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="success" data-offstyle="danger">
                        <div class="input-daterange input-group">
                            <input type="text" class="form-control datepicker" disabled name="search_start" id="search_start" autocomplete="off" placeholder="<?php echo date('d-m-Y'); ?>" value="">
                            <span class="input-group-text border-left-0 border-right-0 rounded-0">
                                sampai
                            </span>
                            <input type="text" class="form-control datepicker" disabled name="search_end" id="search_end" autocomplete="off" placeholder="<?php echo date('d-m-Y'); ?>" value="">
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">  
                        <label for="example-text-input" class="control-label">Cari ID EKL</label>
                        <input class="form-control" type="text" name="kata_kunci" id="kata_kunci" value="" autocomplete="off">
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group">  
                        <label for="example-text-input" class="control-label">Status</label>
                        <select class="form-control custom-select"  name="target" id="target"
                            data-placeholder="Choose a Category" tabindex="1">
                            <option value="" selected>Semua</option>
                            <option value="0">Waiting</option>
                            <option value="1">Approve</option>
                            <option value="2">Reject</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group mt-4">
                        <button type="button" onclick="search()" class="btn btn-success mt-2"> Cari </button>
                        <button type="button" onclick="getAllRequest()" class="btn btn-primary mt-2 ml-2"> Reset </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- End Searcing -->
        <!-- ============================================================== -->
        <div class="table-responsive">
            <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addUpgrade"><i class="mdi mdi-plus mr-2"></i> Tambah Data </button>
            <table id="all_request" class="table table-hover table-bordered no-wrap">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Tanggal</th>
                        <th>Data Member</th>
                        <th>Pengajuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody style="font-size:10pt">

                </tbody>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Tanggal</th>
                        <th>Data Member</th>
                        <th>Pengajuan</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
            </table>
        </div>


        <!-- ============================================================== -->
        <!-- Add Manual Request Upgrade -->
        <!-- ============================================================== -->
        
        <div class="modal fade bd-example-modal-lg" id="addUpgrade" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addUpgradeLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                    <h3 class="modal-title" id="addUpgradeLabel">Tambah Request Upgrade</h3>
                    <button type="button" onclick="clearForm('formUpgradeLimit')" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    </div>
                    <form id="formUpgradeLimit">
                        @csrf
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">ID EKL</label>
                                        <input class="form-control" type="text" name="mbr_code" value="" placeholder="ID EKL" autocomplete="off" />
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Max Balance</label>
                                        <input class="form-control" type="text" name="max_balance" placeholder="Max Balance" value="0" autocomplete="off" >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Max Transaction</label>
                                        <input class="form-control" type="text" name="max_transaction" placeholder="Max Transaction" value="0" autocomplete="off" >
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Max Transfer</label>
                                        <input class="form-control" type="text" name="max_transfer" placeholder="Max Transfer" value="0" autocomplete="off" >
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Alasan</label>
                                        <input class="form-control" type="text" name="alasan" placeholder="Alasan" required value="" autocomplete="off" >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" onclick="clearForm('formUpgradeLimit')" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                            <button type="button" onclick="submitUpgrade()" class="btn btn-success">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Add Manual Request Upgrade -->
        <!-- ============================================================== -->
    
    </div>

    
@endsection
@push('scripts')
    <script>

        allDataRequest();

        function getAllRequest(){
            $('#all_request').DataTable().destroy();
            allDataRequest();
        };

        function addRequest(){

        }
        function search(){
            $('#all_request').DataTable().destroy();
            var target = document.getElementById('target');
            var kata_kunci = document.getElementById('kata_kunci');
            var search_start = document.getElementById('search_start');
            var search_end = document.getElementById('search_end');
            allDataRequest(search_start.value,search_end.value,target.value,kata_kunci.value);
        }
        function allDataRequest(search_start,search_end, target, kata_kunci ){
            $('#all_request').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    "url" : "/member/get_request_transaksi",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "_token": "<?= csrf_token()?>", 
                        target : target,
                        kata : kata_kunci,
                        search_end:search_end,
                        search_start:search_start
                        }
                },
                dom: 'Blfrtip',
                buttons: [
                    {
                        extend: 'excel'
                    },
                    {
                        extend: 'csv'
                    },
                    {
                        extend: 'print'
                    }
                ],
                "columns" : [
                    {"data": "action"},
                    {"data": "tanggal"},
                    {"data": "data"},
                    {"data": "pengajuan"},
                    {"data": "keterangan"},
                ]
            });
        }

        function Approve(mbr_code,mbr_name,pengajuan,id){
            Swal.fire({
                    title: 'Approve data!',
                    icon: 'warning',
                    text: "Proses ini akan menerima pengajuan Limit " + pengajuan + " dari member [" + mbr_code + "]. Apa Anda yakin melakukannya?",
                    // inputPlaceholder: 'contoh: xxxxxx',
                    confirmButtonColor: "#47A447",
                    confirmButtonText: "Oke",
                    cancelButtonText: "Batal",
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                    $.ajax({
                        url:"/member/approve_request_transaksi",
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "<?= csrf_token()?>",
                            id: id,
                            mbr_code: mbr_code,
                            pengajuan: pengajuan
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

        function Reject(mbr_code,mbr_name,pengajuan,id){
            Swal.fire({
                    title: 'Reject data!',
                    icon: 'warning',
                    text: "Proses ini akan menerima pengajuan Limit " + pengajuan + " dari member [" + mbr_code + "].",
                    // input: 'text',
                    confirmButtonColor: "#47A447",
                    confirmButtonText: "Oke",
                    cancelButtonText: "Batal",
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                    $.ajax({
                        url:"/member/reject_request_transaksi",
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "<?= csrf_token()?>",
                            id: id,
                            mbr_code: mbr_code,
                            pengajuan: pengajuan,
                            alasan: result.value
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

        function clearForm(data){
            document.getElementById(data).reset();
        }

        function submitUpgrade(){
            $.ajax({
                url: "/member/add_upgrade_limit",
                method: 'POST',
                dataType: 'json',
                data: $('#formUpgradeLimit').serialize(),
                success: function (data) {
                    // console.log(data)
                    if (data.status) {
                        $('#all_request').DataTable().ajax.reload();
                        $('#addUpgrade').modal('hide'); 
                        clearForm('formUpgradeLimit')
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                    }
                }
            });
        }

        $(function() {
            $('input[name="search_start"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                "locale": {
                    "format": "DD-MM-YYYY"
                },
            }, function(start, end, label) {
                $("#search_start").val(start.format('DD-MM-YYYY'));
            });
        });
        $(function() {
            $('input[name="search_end"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                "locale": {
                    "format": "DD-MM-YYYY"
                },
            }, function(start, end, label) {
                $("#search_end").val(start.format('DD-MM-YYYY'));
            });
        });
        //toggle daterange
        $(function() {
            $('#toggle_tanggal').change(function() {
                if($(this).prop('checked')){
                    $('#search_start').prop('disabled', false);
                    $('#search_end').prop('disabled', false);
                    $('#kata_kunci').prop('disabled', true);
                    $('#kata_kunci').val('');
                    var date_now = moment().format('DD-MM-YYYY');
                    $('#search_start').val(date_now);
                    $('#search_end').val(date_now);
                }else{
                    $('#search_start').prop('disabled', true);
                    $('#search_end').prop('disabled', true);
                    $('#kata_kunci').prop('disabled', false);
                    $('#search_start').val('');
                    $('#search_end').val('');
                }
            })
        })
        </script>
@endpush
