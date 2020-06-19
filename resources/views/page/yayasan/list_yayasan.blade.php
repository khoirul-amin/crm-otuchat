@extends('layouts.app')

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">List Yayasan</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Data Yayasan</a></li>
                <li class="breadcrumb-item active">List Yayasan</li>
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
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Pilih data yang akan dicari</label>
                        <select class="form-control custom-select"   name="status" id="column_search"
                            data-placeholder="Choose a Category" tabindex="1">
                            <option value="nama">Nama Yayasan</option>
                            <option value="phone">No. Telpon</option>
                            <option value="nama_penanggung_jawab">Nama Penanggung Jawab</option>
                            <option value="npwp_yayasan">NPWP Yayasan</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">  
                        <label for="example-text-input" class="control-label">Kata Kunci</label>
                        <input class="form-control" type="text" value="" autocomplete="off" id="value_search">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group mt-4">
                        <button type="" onclick="search()" class="btn btn-success mt-2"> Cari </button>
                        <button type="" onclick="getAllYayasan()" class="btn btn-warning mt-2"> Reset </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================== -->
        <!-- End Searcing -->
        <!-- ============================================================== -->

        <div class="table-responsive">
            <button type="" class="btn btn-primary mt-2" data-toggle="modal" data-target="#addYayasan"><i class="mdi mdi-plus mr-2"></i> Tambah Data </button>
            <table id="list_yayasan" class="table table-striped table-bordered no-wrap">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Profil</th>
                        <th>Account</th>
                        <th>Penanggung Jawab</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody style="font-size:10pt">

                </tbody>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Profil</th>
                        <th>Account</th>
                        <th>Penanggung Jawab</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>


        <!-- ============================================================== -->
        <!-- Modal Add -->
        <!-- ============================================================== -->
        <div class="modal fade bd-example-modal-lg" id="addYayasan" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addYayasanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title" id="addYayasanLabel">Tambah Data Yayasan</h3>
                <button type="button" onclick="clearForm('formYayasan')" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form id="formYayasan">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Nama Yayasan</label>
                                    <input class="form-control" type="text"  name="nama" placeholder="Nama Yayasan" value="" autocomplete="off" required/>
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
                                <label for="example-text-input" class="control-label">No. Telp</label>
                                <input class="form-control" type="text" name="phone" placeholder="No. Telp" value="" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Alamat</label>
                                <input class="form-control" type="text" name="alamat" value="" placeholder="Alamat" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Nama Penanggung Jawab</label>
                                <input class="form-control" type="text" name="nama_penanggung_jawab" value="" placeholder="Nama Penanggung Jawab" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">KTP Penanggung Jawab</label>
                                <input class="form-control" type="text" name="ktp_penanggung_jawab" placeholder="KTP Penanggung Jawab" value="" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">NPWP Penanggung Jawab</label>
                                <input class="form-control" type="text" name="npwp_penanggung_jawab" value="" placeholder="NPWP Penanggung Jawab" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">NPWP Yayasan</label>
                                <input class="form-control" type="text" name="npwp_yayasan" placeholder="NPWP Yayasan" value="" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group"><label for="example-text-input" class="control-label">Alamat Lengkap</label><br/>
                                <input class="form-control" style="width:48%" type="number" name="RT" value="" placeholder="RT" autocomplete="off" />
                                <input style="width:48%" class="form-control" type="number" name="RW" value="" placeholder="RW" autocomplete="off" /><br/>
                                <input  style="width:100%" class="form-control mt-2" type="text" name="kelurahan" value="" placeholder="Kelurahan" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Kecamatan</label>
                                <input class="form-control" type="text" name="kecamatan" placeholder="Kecamatan" value="" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Kota</label>
                                <input class="form-control" type="text" name="kota" value="" placeholder="Kota" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Kode Pos</label>
                                <input class="form-control" type="number" name="kode_pos" placeholder="Kode Pos" value="" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Nomor Akte</label>
                                <input class="form-control" type="text" name="nomor_akta" value="" placeholder="Nomor Akte" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Tanggal Akte</label>
                                <div class="input-group">
                                    <input type="text" class="form-control datepicker" name="tgl_akte" id="tgl_akte" value="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Nama Singkat</label>
                                <input class="form-control" type="text" name="nama_singkat" value="" placeholder="Nama Singkat" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <label for="example-text-input" class="control-label">Image</label>
                            {{-- <input name="image"/> --}}
                            <div class="custom-file mb-3">
                                <input type="file" class="custom-file-input" name="image" id="customFile">
                                <label class="custom-file-label form-control" for="customFile">Choose file</label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Deskripsi</label>
                                <input class="form-control" type="text" name="deskripsi" value="" placeholder="Deskripsi" autocomplete="off" />
                            </div>
                        </div>
                        <div class="col-sm-6">
                            {{-- <div class="form-group">
                                <label for="example-text-input" class="control-label">Key Authenticator</label>
                                <input type="text" class="form-control" name="key" placeholder="Key Authenticator">
                            </div> --}}
                        </div>
                    </div>

                    </form>
                </div>
                <div class="modal-footer">
                <button type="button" onclick="clearForm('formYayasan')" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" onclick="submitYayasan()" class="btn btn-success">Simpan</button>
                </div>
            </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Modal Add -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- Modal Cek Saldo yayasan -->
        <!-- ============================================================== -->
        <div class="modal fade bd-example-modal-lg" id="CekSaldo" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="CekSaldoLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title" id="CekSaldoLabel">Saldo Yayasan</h3>
                <button type="button" onclick="clearForm('formSaldo')" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                    <form id="formSaldo" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <label for="example-text-input" class="control-label">Saldo</label>
                                        <input class="form-control" type="text" id="saldo" name="saldo" placeholder="Saldo" value="" autocomplete="off" disabled/>
                                    </div>
                                </div>
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Riwayat Donasi Terahir</label>
                                    <input class="form-control" type="text" id="riwayat" name="riwayat" value="" placeholder="Riwayat" autocomplete="off"  disabled>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Keterangan</label>
                                    <input class="form-control" type="text" id="ket" name="ket" placeholder="Keterangan" value="" autocomplete="off"  disabled/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="control-label">Status</label>
                                        <select class="form-control custom-select" id="status" name="status"
                                            data-placeholder="Choose a Category" tabindex="1">
                                            <option value="" disabled selected>-- pilih status --</option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            {{-- <option value="Block">Block</option> --}}
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" onclick="clearForm('formSaldo')" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                    {{-- <button type="button" class="btn btn-success">Simpan Perubahan</button> --}}
                </div>
            </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Modal Cek Saldo yayasan -->
        <!-- ============================================================== -->

        <!-- ============================================================== -->
        <!-- Modal Update -->
        <!-- ============================================================== -->
        <div class="modal fade bd-example-modal-lg" id="UpdateYayasan" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addYayasanLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                <h3 class="modal-title" id="UpdateYayasanLabel">Update Data Yayasan</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                </div>
                <form id="formUpdateYayasan" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="id_yayasan" name="id" value="" />
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Nama Yayasan</label>
                                    <input class="form-control" type="text" id="nama1" name="nama" placeholder="Nama Yayasan" value="" autocomplete="off" required/>
                                </div>
                            </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="example-text-input" class="control-label">Email</label>
                                <input class="form-control" type="text" id="email1" name="email" value="" placeholder="Email" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">No. Telp</label>
                                    <input class="form-control" type="text" id="phone1" name="phone" placeholder="No. Telp" value="" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Alamat</label>
                                    <input class="form-control" type="text"id="alamat1"  name="alamat" value="" placeholder="Alamat" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Nama Penanggung Jawab</label>
                                    <input class="form-control" type="text" id="nama_penanggung_jawab1" name="nama_penanggung_jawab" value="" placeholder="Nama Penanggung Jawab" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">KTP Penanggung Jawab</label>
                                    <input class="form-control" type="text" id="ktp_penanggung_jawab1" name="ktp_penanggung_jawab" placeholder="KTP Penanggung Jawab" value="" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">NPWP Penanggung Jawab</label>
                                    <input class="form-control" type="text" id="npwp_penanggung_jawab1" name="npwp_penanggung_jawab" value="" placeholder="NPWP Penanggung Jawab" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">NPWP Yayasan</label>
                                    <input class="form-control" type="text" id="npwp_yayasan1" name="npwp_yayasan" placeholder="NPWP Yayasan" value="" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group"><label for="example-text-input" class="control-label">Alamat Lengkap</label><br/>
                                    <input class="form-control" style="width:48%" type="number" id="RT1" name="RT" value="" placeholder="RT" autocomplete="off" />
                                    <input style="width:48%" class="form-control" type="number" id="RW1" name="RW" value="" placeholder="RW" autocomplete="off" /><br/>
                                    <input  style="width:100%" class="form-control mt-2" type="text" id="kelurahan1" name="kelurahan" value="" placeholder="Kelurahan" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Kecamatan</label>
                                    <input class="form-control" type="text" id="kecamatan1" name="kecamatan" placeholder="Kecamatan" value="" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Kota</label>
                                    <input class="form-control" type="text" id="kota1" name="kota" value="" placeholder="Kota" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Kode Pos</label>
                                    <input class="form-control" type="number" id="kode_pos1" name="kode_pos" placeholder="Kode Pos" value="" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Nomor Akte</label>
                                    <input class="form-control" type="text" id="nomor_akta1" name="nomor_akta" value="" placeholder="Nomor Akte" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Tanggal Akte</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control datepicker" name="tgl_akte" id="tgl_akte1" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Nama Singkat</label>
                                    <input class="form-control" type="text" id="nama_singkat1" name="nama_singkat" value="" placeholder="Nama Singkat" autocomplete="off" />
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Deskripsi</label>
                                    <input class="form-control" type="text" id="deskripsi1" name="deskripsi" value="" placeholder="Deskripsi" autocomplete="off" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                            </div>
                            <div class="col-sm-6">
                                {{-- <div class="form-group">
                                    <label for="example-text-input" class="control-label">Key Authenticator</label>
                                    <input type="text" class="form-control" name="key" placeholder="Key Authenticator">
                                </div> --}}
                            </div>
                        </div>

                </form>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                <button type="button" onclick="updateYayasan()" class="btn btn-success">Simpan</button>
                </div>
            </div>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Modal Update -->
        <!-- ============================================================== -->
   
        {{-- @if ($errors->any())
            <div class="alert alert-danger" role="alert">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif --}}

    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
@endsection


@push('scripts')
    <script>
        allDataYayasan();

        function getAllYayasan(){
            $('#list_yayasan').DataTable().destroy();
            allDataYayasan();
        };
        
        function search(){
            $('#list_yayasan').DataTable().destroy();
            var column_search = document.getElementById('column_search');
            var value_search = document.getElementById('value_search');
            allDataYayasan(column_search.value,value_search.value);
        };

        function allDataYayasan(column, value){
            // console.log(search,value);
            $('#list_yayasan').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    "url" : "/yayasan/get_all_yayasan",
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
                    {"data": "action"},
                    {"data": "profil"},
                    {"data": "akun"},
                    {"data": "penanggung_jawab"},
                    {"data": "alamat"},
                ]
            });
        }
            
        // Insert data
        function submitYayasan() {
            var formData = new FormData($('#formYayasan')[0]);
            $.ajax({
                url: "/yayasan/add_yayasan",
                method: 'POST',
                dataType: 'json',
                data: formData,
                contentType: false,
                processData: false,
                cache: false,
                async: false,
                success: function (data) {
                    // console.log(data)
                    if (data.status) {
                        $('#list_yayasan').DataTable().ajax.reload();
                        $('#addYayasan').modal('hide'); 
                        clearForm('formYayasan')
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                    }
                }
            });
        };
        
        function getYayasanById(id){

            $.ajax({
                type: "GET",
                url: `/yayasan/get_yayasan_by_id/${id}`,
                cache: false,
                success: function(data){
                    // console.log(data.tanggal_akta)
                    $("#nama1").val(data.nama);  $("#email1").val(data.email); $("#phone1").val(data.phone); $("#alamat1").val(data.alamat);
                    $("#nama_penanggung_jawab1").val(data.nama_penanggung_jawab);  $("#ktp_penanggung_jawab1").val(data.ktp_penanggung_jawab);
                    $("#npwp_penanggung_jawab1").val(data.npwp_penanggung_jawab); $("#id_yayasan").val(data.id);
                    $("#npwp_yayasan1").val(data.npwp_yayasan);  $("#RT1").val(data.RT);  $("#RW1").val(data.RW);
                    $("#kelurahan1").val(data.Kelurahan); $("#kecamatan1").val(data.kecamatan);
                    $("#kota1").val(data.kota); $("#kode_pos1").val(data.kode_pos); $("#deskripsi1").val(data.deskripsi);
                    $("#nomor_akta1").val(data.nomor_akta); $("#tgl_akte1").val(data.tanggal_akta); $("#nama_singkat1").val(data.nama_singkatan);
                }
            });
        }

        function getSaldoById(id){

            $.ajax({
                type: "GET",
                url: `/yayasan/get_saldo_by_id/${id}`,
                cache: false,
                success: function(data){
                    // console.log(data)
                    $("#saldo").val(data.amount); $("#riwayat").val(data.reff); $("#ket").val(data.update_identitas); $("#status").val(data.sts_triger);
                }
            });
        }

        // Update data
        function updateYayasan() {
            $.ajax({
                url: "/yayasan/update_yayasan",
                method: 'POST',
                dataType: 'json',
                data: $('#formUpdateYayasan').serialize(),
                success: function (data) {
                    // console.log(data)
                    if (data.status) {
                        $('#list_yayasan').DataTable().ajax.reload();
                        $('#UpdateYayasan').modal('hide');
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                    }
                }
            });
        };
           
        // Delete Data
        function deleteYayasan(data) {
            Swal.fire({
                    title: 'Hapus data!',
                    icon: 'warning',
                    text: "Anda akan menghapus data ini",
                    confirmButtonColor: "#47A447",
                    confirmButtonText: "Ok",
                    cancelButtonText: "Batal",
                    showCancelButton: true,
                }).then((result) => {
                    if (result.value) {
                        $.ajax({
                            url: "/yayasan/delete_yayasan",
                            method: 'POST',
                            processing: true,
                            dataType: 'json',
                            data: ({
                                "_token": "<?= csrf_token()?>",
                                id:data
                            }),
                            success: function (data) {
                                if (data.status) {
                                    $('#list_yayasan').DataTable().ajax.reload();
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

        function clearForm(data){
            document.getElementById(data).reset();
        }

        $(function() {
            $('input[name="tgl_akte"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                "locale": {
                    "format": "YYYY-MM-DD"
                },
            }, function(start, end, label) {
                $("#tgl_akte").val(start.format('YYYY-MM-DD'));
            });
        });
        $(function() {
            $('input[name="tgl_akte1"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                "locale": {
                    "format": "YYYY-MM-DD"
                },
            }, function(start, end, label) {
                $("#tgl_akte1").val(start.format('YYYY-MM-DD'));
            });
        });
    </script>
@endpush