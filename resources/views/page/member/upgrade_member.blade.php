@extends('layouts.app')

@push('scripts')
    <link href="{{ asset('assets') }}/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
@endpush

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Request Upgrade Member</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Member</a></li>
                <li class="breadcrumb-item active">Request Upgrade Member</li>
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
                {{-- <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Date Range</label>
                        <div class="input-daterange input-group" id="date-range">
                            <input type="text" class="form-control" name="start" id="start_date" />
                            <span class="input-group-addon bg-info b-0 text-white">to</span>
                            <input type="text" class="form-control" name="end" id="end_date" />
                        </div>
                    </div>
                </div> --}}
                <div class="col-md-2">
                    <div class="form-group">
                        <label class="control-label">Pilih data</label>
                        <select class="form-control custom-select"   name="status" id="column_search"
                            data-placeholder="Choose a Category" tabindex="1">
                            <option value="mbr_code">ID EKL</option>
                            <option value="mbr_name">Nama</option>
                            <option value="mbr_mobile">Nomor HP</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="example-text-input" class="control-label">Kata Kunci</label>
                        <input class="form-control" type="text" value="" autocomplete="off" placeholder="Masukan kata kunci" id="value_search">
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
            {{-- <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addUpgradeMember"><i class="mdi mdi-plus mr-2"></i> Tambah Data </button> --}}
            <table id="list_upgrade_member" class="table table-striped table-bordered no-wrap">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Tanggal</th>
                        <th>Data Member</th>
                        <th>Detail</th>
                        <th>Rekening</th>
                        <th>Gambar Pendukung</th>
                    </tr>
                </thead>
                <tbody style="font-size:10pt">

                </tbody>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Tanggal</th>
                        <th>Data Member</th>
                        <th>Detail</th>
                        <th>Rekening</th>
                        <th>Gambar Pendukung</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


        <!-- ============================================================== -->
        <!-- Modal Add -->
        <!-- ============================================================== -->
        <div class="modal fade bd-example-modal-lg" id="addUpgradeMember" data-backdrop="static" role="dialog" aria-labelledby="addYayasanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title" id="addYayasanLabel">Tambah Data Upgrade Keanggotaan</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form id="form-upgrade-member" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">ID EKL</label>
                                    <input class="form-control" type="text"  name="mbr_code" placeholder="EKL000000" value="" autocomplete="off" required/>
                                </div>
                            </div>
                        {{-- <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Email</label>
                                <input class="form-control" type="text" name="email" value="" placeholder="Email" autocomplete="off" >
                            </div>
                        </div> --}}
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Tempat Lahir</label>
                                <input class="form-control" type="text" name="tempat_lahir" placeholder="Tempat Lahir" value="" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Tanggal Lahir</label>
                                <input class="form-control" type="date" name="tgl_lahir" value="" placeholder="Tanggal Lahir" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Alamat</label>
                                <textarea class="form-control" name="alamat" placeholder="Alamat" id="" cols="10" rows="10" style="height: 100px"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="locality" class="control-label">Kota</label>
                                <select class="form-control js-example-basic-single" id="locality" name="kota">
                                    <option value="" selected="selected" hidden="hidden">Pilih Kota Outlet</option>
                                    @foreach ($kota as $item)
                                      <option value="{{$item->id_kota}}">{{$item->kota}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Tipe Identitas</label>
                                <select class="form-control custom-select"  name="tipe_identitas" id="tipe_identity" data-placeholder="Choose a Category" name="" id="">
                                    <option value="KTP">KTP</option>
                                    <option value="SIM">SIM</option>
                                </select>

                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Nomor Identitas</label>
                                <input class="form-control" type="text" name="nomer_identitas" placeholder="Nomer Identitas" value="" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group" data-select2-id="1">
                                <label class="">Rekening Bank</label>
                                    <select class="form-control custom-select" name="nama_bank">
                                        <option>-- Please Select --</option>
                                          @foreach ($bank as $item)
                                            <option value="{{$item->id}}">{{$item->name}}</option>
                                          @endforeach
                                    </select>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Nomor Rekening</label>
                                <input class="form-control" type="text" name="nomer_rekening" placeholder="Nomer Rekening" value="" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Nama Pemilik / a/n</label>
                                <input class="form-control" type="text" name="nama_rekening" value="" placeholder="A/N Pemilik" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Key Approval</label>
                                <input class="form-control" type="text" name="key" placeholder="Key Approval" value="" autocomplete="off" >
                            </div>
                        </div>
                    </div>

                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" onclick="submitUpgradeMember()" class="btn btn-success">Simpan</button>
                </div>
            </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Modal Add -->
        <!-- ============================================================== -->


        @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
@endsection


@push('scripts')
    <script>

        $(document).ready(function(){
            $('.js-example-basic-single').select2();
            $('.select2-container--default').css('width', '');
            $('.select2-selection--single').css('border', '1px solid #ced4da');
            $('.select2-selection--single').css('min-height', '38px');
            $('.select2-selection__arrow').css('margin-top', '5px');
        });

        allDataUpgradeMember();

        function resetDatatable(){
            $('#list_upgrade_member').DataTable().destroy();
            allDataUpgradeMember();
        };

        function search(){
            $('#list_upgrade_member').DataTable().destroy();
            var column_search = document.getElementById('column_search');
            var value_search = document.getElementById('value_search');

            var start_date = document.getElementById('start_date');
            var end_date = document.getElementById('end_date');
            allDataUpgradeMember(column_search.value, value_search.value);
        };

        function allDataUpgradeMember(column, value){
            // console.log(search,value);
            $('#list_upgrade_member').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    "url" : "/member/get_all_upgrade_member",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "_token": "<?= csrf_token()?>",
                        column : column,
                        value : value,
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
                    {"data": "datamember"},
                    {"data": "detail"},
                    {"data": "rekening"},
                    {"data": "image"},
                ]
            });
        }

        // Insert data
        function submitUpgradeMember() {
            $.ajax({
                url: "/member/add_upgrade_member",
                method: 'POST',
                dataType: 'json',
                data: $('#form-upgrade-member').serialize(),
                success: function (data) {
                    // console.log(data)
                    if (data.respStatus) {
                        $('#list_upgrade_member').DataTable().ajax.reload();
                        $('#addUpgradeMember').modal('hide');
                        Swal.fire("PROSES BERHASIL", data.respMessage, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.respMessage, "error");
                    }
                }
            });
        };

        function approveMember(mbr_code) {
            Swal.fire({
                title: 'Approve data!',
                icon: 'warning',
                text: "Proses ini akan menerima request upgrade dari member [" + mbr_code + "]. Apa Anda yakin melakukannya?",
                inputPlaceholder: 'contoh: xxxxxx',
                confirmButtonColor: "#47A447",
                confirmButtonText: "Iya, saya yakin!",
                cancelButtonText: "Batal",
                showCancelButton: true,
            }).then((result) => {
                if (result.value) {
                    $.ajax({
                        url: "/member/approve_member",
                        method: 'POST',
                        dataType: 'json',
                        data: {
                            "_token": "<?= csrf_token()?>",
                            mbr_code: mbr_code
                        },
                        success: function (data) {
                            if (data.respStatus) {
                                $('#list_upgrade_member').DataTable().ajax.reload();
                                Swal.fire("PROSES BERHASIL", data.respMessage, "success");
                            } else {
                                Swal.fire("PROSES GAGAL", data.respMessage, "error");
                            }
                        }
                    });
                }
            })
        }

        // ==============================================================
        // Reject Member
        // ==============================================================
    function rejectMember(mbr_code) {
        Swal.fire({
            title: 'Reject data!',
            icon: 'warning',
            text: "Proses ini akan menolak proses upgrade dari member [" + mbr_code + "].",
            input: 'text',
            inputAttributes: {
                autocapitalize: 'off',
                placeholder: 'Masukan Alasan',
                required: true
            },
            confirmButtonColor: "#47A447",
            confirmButtonText: "Iya, saya yakin!",
            cancelButtonText: "Batal",
            showCancelButton: true,
        }).then((result) => {
            if (result.value) {
                // $('body').loading('stop');
                $.ajax({
                    url: "/member/reject_member",
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        "_token": "<?= csrf_token()?>",
                        mbr_code: mbr_code,
                        alasan: result.value
                    },
                    success: function (data) {
                        if (data.respStatus) {
                            $('#list_upgrade_member').DataTable().ajax.reload();
                            Swal.fire("PROSES BERHASIL", data.respStatus, "success");
                        } else {
                            Swal.fire("PROSES GAGAL", data.respStatus, "error");
                        }
                    }
                });
            }
        });
    }
    </script>
@endpush
