@extends('layouts.app')

@section('content')


    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">List Product</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Product</a></li>
                <li class="breadcrumb-item active">List Product</li>
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
                            <label class="control-label">Pilih data yang akan dicari</label>
                            <select class="form-control custom-select" id="kolom" name="kolom"
                                data-placeholder="Choose a Category" tabindex="1">
                                <option value="product_kode">Kode Produk</option>
                                <option value="product_name">Nama Produk</option>
                                <option value="type_product">Tipe Produk</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">  
                            <label for="example-text-input" class="control-label">Kata Kunci</label>
                            <input class="form-control" type="text" id="kata_kunci" value="" autocomplete="off" id="example-text-input">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">  
                            <label for="example-text-input" class="control-label">Status</label>
                            <select class="form-control custom-select"  name="status" id="status"
                                data-placeholder="Choose a Category" tabindex="1">
                                <option value="">Semua</option>
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Block">Block</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group mt-4">
                            <button type="" onclick="search()" class="btn btn-success mt-2"> Cari </button>
                            <button type="" onclick="getAllProduct()" class="btn btn-warning mt-2 ml-2"> Reset </button>
                        </div>
                    </div>
                </div>
            </div>

        <!-- ============================================================== -->
        <!-- End Searcing -->
        <!-- ============================================================== -->

        <div class="table-responsive">
            <button type="" data-toggle="modal" data-target="#addProduct" class="btn btn-primary mt-2"><i class="mdi mdi-plus mr-2"></i> Tambah Data </button>
            <table id="list_product" class="table table-striped table-bordered no-wrap">
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Info Produk</th>
                        <th>Provider</th>
                        <th>Harga Reguler</th>
                        <th>Harga H2H</th>
                        <th>Status Produk</th>
                    </tr>
                </thead>
                <tbody style="font-size:10pt">

                </tbody>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Info Produk</th>
                        <th>Provider</th>
                        <th>Harga Reguler</th>
                        <th>Harga H2H</th>
                        <th>Status Produk</th>
                    </tr>
                </thead>
            </table>
        </div>
    
    </div>
    
    <!-- ============================================================== -->
    <!-- End Content -->
    <!-- ============================================================== -->

    <!-- ============================================================== -->
    <!-- Add Product -->
    <!-- ============================================================== -->
    <div class="modal fade bd-example-modal-lg" id="addProduct" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="addProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="addProductLabel">Tambah Data Produk</h3>
                    <button type="button" onclick="clearForm('formProduct')" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formProduct" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Nama Produk</label>
                                    <input class="form-control" type="text"  name="product_name" placeholder="Nama Produk" value="" autocomplete="off" required/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Kode Produk</label>
                                    <input class="form-control" type="text"  name="product_kode" placeholder="Kode Produk" value="" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Kode Produk Supplier</label>
                                    <input class="form-control" type="text" name="h2h_code" value="" placeholder="Kode Produk Supplier" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Tipe Produk</label>
                                    <select class="form-control custom-select"  name="type_product"
                                        data-placeholder="Choose a Category" tabindex="1">
                                        <option value="" disabled selected>-- pilih produk --</option>
                                        @foreach ($data_type_product as $data_type_product)
                                            <option value="{{$data_type_product->type_product}}">{{$data_type_product->type_product}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Provider</label>
                                    <select class="form-control custom-select"  name="provider_id"
                                        data-placeholder="Choose a Category" tabindex="1">
                                        <option value="" disabled selected>-- pilih provider --</option>
                                        @foreach ($data_provider as $data_provider)
                                            @if ($data_provider->provaider_status == 'Active')
                                                <option value="{{$data_provider->provider_id}}">{{$data_provider->provider_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Harga Jual Reguler</label>
                                    <input class="form-control" type="number"  name="harga_jual" placeholder="Harga Jual Reguler" value="" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Harga Jual Supplier</label>
                                    <input class="form-control" type="number" name="harga_h2h" value="" placeholder="Harga Jual Supplier" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Admin Eklanku</label>
                                    <input class="form-control" type="number"  name="disc" placeholder="Admin Eklanku" value="" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Admin Supplier</label>
                                    <input class="form-control" type="text" name="admin_supplier" value="" placeholder="Admin Supplier" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Nominal ID</label>
                                    <input class="form-control" type="number"  name="nominal_id" placeholder="Nominal ID" value="" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="control-label">Status</label>
                                        <select class="form-control custom-select" name="product_status"
                                            data-placeholder="Choose a Category" tabindex="1">
                                            <option value="" disabled selected>-- pilih status --</option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Block">Block</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="example-text-input" class="control-label">Logo Produk</label>
                                <input class="form-control" type="text" name="product_image" value="" placeholder="Logo Produk" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" onclick="clearForm('formProduct')" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" onclick="submitProduct()" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- End Add Product -->
    <!-- ============================================================== -->


    <!-- ============================================================== -->
    <!-- Update Product -->
    <!-- ============================================================== -->
    <div class="modal fade bd-example-modal-lg" id="updateDataProduct" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="updateDataProductLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered  modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="updateDataProductLabel">Ubah Data Produk</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="formUpdateProduct" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Nama Produk</label>
                                    <input class="form-control" type="text" id="product_name" name="product_name" placeholder="Nama Produk" value="" autocomplete="off" required/>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Kode Produk</label>
                                    <input class="form-control" type="text" id="product_kode" name="product_kode" placeholder="Kode Produk" value="" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Kode Produk Supplier</label>
                                    <input class="form-control" type="text" id="h2h_code" name="h2h_code" value="" placeholder="Kode Produk Supplier" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Tipe Produk</label>
                                    <select class="form-control custom-select"  name="type_product" id="type_product"
                                        data-placeholder="Choose a Category" tabindex="1">
                                        <option value="" disabled selected>-- pilih produk --</option>
                                        @foreach ($data_type_product1 as $data_type_product)
                                            <option value="{{$data_type_product->type_product}}">{{$data_type_product->type_product}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Provider</label>
                                    <select class="form-control custom-select"  name="provider_id" id="provider_id"
                                        data-placeholder="Choose a Category" tabindex="1">
                                        <option value="" disabled selected>-- pilih provider --</option>
                                        @foreach ($data_provider1 as $data_provider)
                                            @if ($data_provider->provaider_status == 'Active')
                                                <option value="{{$data_provider->provider_id}}">{{$data_provider->provider_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Harga Jual Reguler</label>
                                    <input class="form-control" type="number" id="harga_jual" name="harga_jual" placeholder="Harga Jual Reguler" value="" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Harga Jual Supplier</label>
                                    <input class="form-control" type="number" id="harga_h2h" name="harga_h2h" value="" placeholder="Harga Jual Supplier" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Admin Eklanku</label>
                                    <input class="form-control" type="number" id="disc" name="disc" placeholder="Admin Eklanku" value="" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Admin Supplier</label>
                                    <input class="form-control" type="text" id="admin_supplier" name="admin_supplier" value="" placeholder="Admin Supplier" autocomplete="off" >
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label for="example-text-input" class="control-label">Nominal ID</label>
                                    <input class="form-control" type="number" id="nominal_id" name="nominal_id" placeholder="Nominal ID" value="" autocomplete="off" required/>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label class="control-label">Status</label>
                                        <select class="form-control custom-select" id="product_status" name="product_status"
                                            data-placeholder="Choose a Category" tabindex="1">
                                            <option value="" disabled selected>-- pilih status --</option>
                                            <option value="Active">Active</option>
                                            <option value="Inactive">Inactive</option>
                                            <option value="Block">Block</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label for="example-text-input" class="control-label">Logo Produk</label>
                                <input class="form-control" type="text" id="product_image" name="product_image" value="" placeholder="Logo Produk" autocomplete="off" >
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" onclick="updateProduct()" class="btn btn-success">Simpan</button>
                </div>
            </div>
        </div>
    </div>
    <!-- ============================================================== -->
    <!-- Update Product -->
    <!-- ============================================================== -->
    
@endsection


@push('scripts')
    <script>
        allDataProduct();

        function getAllProduct(){
            $('#list_product').DataTable().destroy();
            allDataProduct();
        };

        function search(){
            $('#list_product').DataTable().destroy();
            var kolom = document.getElementById('kolom');
            var kata_kunci = document.getElementById('kata_kunci');
            var status = document.getElementById('status');
            allDataProduct(kolom.value, kata_kunci.value, status.value);
        };
        function allDataProduct(kolom, kata_kunci, status){
            // console.log(column_search, value_search)
            $('#list_product').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    "url" : "/product/get_all_product",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "_token": "<?= csrf_token()?>",
                        kolom : kolom,
                        kata_kunci : kata_kunci,
                        status : status
                    }
                },
                "columns" : [
                    {"data": "action"},
                    {"data": "info_produk"},
                    {"data": "provider"},
                    {"data": "harga"},
                    {"data": "harga_h2h"},
                    {"data": "product_status"}
                ]
            });
        }

        function ubahData(data){
            console.log(data);
        }


        function submitProduct(){
            $.ajax({
                url: "/product/add_product",
                method: 'POST',
                dataType: 'json',
                data: $('#formProduct').serialize(),
                success: function (data) {
                    if (data.status) {
                        $('#list_product').DataTable().ajax.reload();
                        clearForm('formProduct');
                        $('#addProduct').modal('hide');
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                    }
                }
            });
        }

                
        function getProductById(id){
            $.ajax({
                type: "GET",
                url: `/product/get_product_by_id/${id}`,
                cache: false,
                success: function(data){
                    // console.log(data)
                    $("#product_name").val(data.product_name);  $("#product_kode").val(data.product_kode); $("#harga_h2h").val(data.harga_h2h); $("#type_product").val(data.type_product);
                    $("#provider_id").val(data.provider_id);  $("#harga_jual").val(data.harga_jual); $("#disc").val(data.disc); $("#admin_supplier").val(data.admin_supplier);
                    $("#nominal_id").val(data.nominal_id);  $("#product_status").val(data.product_status); $("#product_image").val(data.product_image); $("#h2h_code").val(data.h2h_code);
                }
            });
        }

        // Update data
        function updateProduct() {
            $.ajax({
                url: "/product/update_product",
                method: 'POST',
                dataType: 'json',
                data: $('#formUpdateProduct').serialize(),
                success: function (data) {
                    // console.log(data)
                    if (data.status) {
                        $('#list_product').DataTable().ajax.reload();
                        $('#updateDataProduct').modal('hide');
                        Swal.fire("PROSES BERHASIL", data.message, "success");
                    }
                    else {
                        Swal.fire("PROSES GAGAL", data.message, "error");
                    }
                }
            });
        };

        // Delete Data
        function deleteProduct(data) {
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
                            url: "/product/delete_product",
                            method: 'POST',
                            processing: true,
                            dataType: 'json',
                            data: ({
                                "_token": "<?= csrf_token()?>",
                                id:data
                            }),
                            success: function (data) {
                                if (data.status) {
                                    $('#list_product').DataTable().ajax.reload();
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
    </script>
@endpush