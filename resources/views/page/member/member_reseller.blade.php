@extends('layouts.app')

@push('scripts')
    <link href="{{ asset('assets') }}/css/select2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
@endpush

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">Member Reseller</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Member</a></li>
            <li class="breadcrumb-item active">Member Reseller</li>
        </ol>
    </div>
</div>

<div class="p-30 bg-white">
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#tambahResellerModal">Tambah Member Reseller</button>
    <div class="table-responsive">
        <table id="member_reseller" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Action</th>
                    <th>Data Outlet</th>
                    <th>Data Pemilik</th>
                    <th>Keterangan</th>
                    <th>Alamat Outlet</th>
                    <th>Info Evaluasi</th>
                </tr>
            </thead>
            <tbody class="small"></tbody>
            <tfoot>
                <tr>
                    <th>Action</th>
                    <th>Data Outlet</th>
                    <th>Data Pemilik</th>
                    <th>Keterangan</th>
                    <th>Alamat Outlet</th>
                    <th>Info Evaluasi</th>
                </tr>
            </tfoot>
        </table>
    </div>

</div>

<!-- The Modal -->
<div class="modal fade" id="tambahResellerModal" data-backdrop="static">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
        
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Tambah Member Reseller</h4>
            </div>
            
            <form id="tambahResellerForm">
            <!-- Modal body -->
            <div class="modal-body">
                <h3 class="text-center">Info Pemilik Outlet</h3><hr>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="mbr_code" class="control-label">ID EKL</label>
                            <input class="form-control" type="text" id="mbr_code" name="mbr_code" placeholder="ID EKL" value="" autocomplete="off" required/>
                        </div>
                    </div>
                    {{-- <div class="col-6">
                        <div class="form-group">
                            <label for="mbr_name" class="control-label">Nama Member</label>
                            <input class="form-control" type="text" id="mbr_name" name="mbr_name" value="" placeholder="Nama Member" autocomplete="off" readonly/>
                        </div>
                    </div> --}}
                </div>

                <h3 class="text-center">Info Outlet</h3><hr>
                <div class="row">
                    <div class="col-6">
                        <div class="form-group">
                            <label for="konter_name" class="control-label">Nama Outlet</label>
                            <input class="form-control" type="text" id="konter_name" name="konter_name" placeholder="Nama Outlet" value="" autocomplete="off" required/>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group">
                            <label for="konter_kota" class="control-label">Kota Outlet</label>
                            <select class="form-control js-example-basic-single" id="konter_kota" name="konter_kota" required>
                                <option value="" selected="selected" hidden="hidden">Pilih Kota Outlet</option>
                                @foreach ($kota as $key)
                                    <option value="{{ $key->kota }}">{{ $key->kota }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="konter_addres" class="control-label">Alamat Outlet</label>
                            <input class="form-control" type="text" id="konter_addres" name="konter_address" value="" placeholder="Alamat Outlet" autocomplete="off" required/>
                        </div>
                    </div>
                </div>
                {{-- <div class="row">
                    <div class="col">
                        <div class="form-group">
                            <label for="key_approval" class="control-label">Key Approval</label>
                            <input class="form-control" type="text" id="key_approval" name="key_approval" placeholder="Key Approval" value="" autocomplete="off" required/>
                        </div>
                    </div>
                </div> --}}
            </div>
            
            <!-- Modal footer -->
            <div class="modal-footer">
                <button id="tambahResellerButtonLoading" class="btn btn-success disabled" style="display: none;">Loading...</button>
                <button id="tambahResellerButtonSubmit" type="submit" class="btn btn-success"><i class="mdi mr-2 mdi-content-save"></i>Simpan</button>
                <button id="tambahResellerButtonClose" type="reset" class="btn btn-secondary" onclick="clearFormReseller()" data-dismiss="modal">Close</button>
            </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        var table;
        dataTable();

        function dataTable(){
            table = $('#member_reseller').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/member/get_member_reseller",
                    "dataType": "json",
                    "type": "POST"
                },
                "columns" : [
                    {"data": "action", "className": "text-center"},
                    {"data": "data_outlet"},
                    {"data": "data_pemilik"},
                    {"data": "keterangan"},
                    {"data": "alamat_outlet", "className": "w-25"},
                    {"data": "info_evaluasi"},
                ],
                "initComplete" : function() {
                    var input = $('.dataTables_filter input').unbind(),
                        self = this.api(),
                        $searchButton = $('<button class="btn ml-2 btn-primary">')
                                .text('search')
                                .click(function() {
                                    self.search(input.val()).draw();
                                }),
                        $clearButton = $('<button class="btn ml-2 btn-primary">')
                                .text('clear')
                                .click(function() {
                                    input.val('');
                                    $searchButton.click(); 
                                })
                    $('.dataTables_filter input')
                        .off('.DT')
                        .on('keyup.DT', function (e) {
                            if (e.keyCode == 13) {
                                self.search(this.value).draw();
                            }
                        });
                    $('.dataTables_filter').append($searchButton, $clearButton);
                },
                "language": {
                    searchPlaceholder: "Search Something"
                },
                "order": [[ 3, "DESC" ]],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ]
            });
        }

        function blockReseller(mbr_code){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, block it!',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`/member/block_member_reseller/${mbr_code}`)
                    .then(response => {
                        return response.json();
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value.status === 'success') {
                    Swal.fire(
                        'Blocked!',
                        'Member reseller has been succcesfully blocked.',
                        'success'
                    )
                    table.ajax.reload( null, false);
                }
                else{
                    Swal.fire(
                        'Oops...',
                        'Something went wrong!',
                        'error'
                    )
                }
            })

        }

        function activeReseller(mbr_code){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, active it!',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`/member/active_member_reseller/${mbr_code}`)
                    .then(response => {
                        return response.json();
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value.status === 'success') {
                    Swal.fire(
                        'Activated!',
                        'Member reseller has been succesfully activated.',
                        'success'
                    )
                    table.ajax.reload( null, false);
                }
                else{
                    Swal.fire(
                        'Oops...',
                        'Something went wrong!',
                        'error'
                    )
                }
            })
        }

        function ubahReseller(mbr_code){
            var dialog = bootbox.dialog({
                title: 'Perubahan Data Member Reseller',
                size: "large",
                message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
            }).find(".modal-dialog").addClass("modal-dialog-centered");
            $.get("/member/get_detail_member_reseller/" + mbr_code, function(data, status){
                var message = '<form id="ubahResellerForm">';

                message += '<h3 class="text-center">Info Pemilik Outlet</h3><hr>';
                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="mbr_code" class="control-label">ID EKL</label><input class="form-control" type="text" id="mbr_code" name="mbr_code" placeholder="ID EKL" value="' + data.mbr_code + '" autocomplete="off" readonly/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="mbr_name" class="control-label">Nama Member</label><input class="form-control" type="text" id="mbr_name" name="mbr_name" value="'+ data.mbr_name +'" placeholder="Nama Member" autocomplete="off" readonly/></div></div></div>';

                message += '<h3 class="text-center">Info Outlet</h3><hr>';
                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="konter_name" class="control-label">Nama Outlet</label><input class="form-control" type="text" id="konter_name" name="konter_name" placeholder="Nama Outlet" value="' + data.konter_name + '" autocomplete="off" required/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="konter_addres" class="control-label">Alamat Outlet</label><input class="form-control" type="text" id="konter_addres" name="konter_address" value="'+ data.konter_addres +'" placeholder="Alamat Outlet" autocomplete="off" required/></div></div></div>';
                if(data.konter_kota === 'Data Kota Kosong!'){
                    message += '<div class="row"><div class="col"><div class="form-group"><label for="konter_kota" class="control-label">Kota Outlet</label><input class="form-control" type="text" id="konter_kota" name="konter_kota" placeholder="Kota Outlet" value="" autocomplete="off"/></div></div></div>';
                }
                else{
                    message += '<div class="row"><div class="col"><div class="form-group"><label for="konter_kota" class="control-label">Kota Outlet</label><input class="form-control" type="text" id="konter_kota" name="konter_kota" placeholder="Kota Outlet" value="' + data.konter_kota + '" autocomplete="off"/></div></div></div>';
                }

                // message += '<div class="row"><div class="col"><div class="form-group"><label for="key_approval" class="control-label">Key Approval</label><input class="form-control" type="text" id="key_approval" name="key_approval" placeholder="Key Approval" value="" autocomplete="off" required/></div></div></div>';

                message += '<div class="text-right"><button id="ubahResellerButtonLoading" class="btn btn-success disabled" style="display: none;">Loading...</button><button id="ubahResellerButtonSubmit" type="submit" class="btn btn-success"><i class="mdi mr-2 mdi-content-save"></i>Simpan</button></div></form>'

                dialog.init(function(){
                    dialog.find('.bootbox-body').html(message);
                });

                $(document).ready(function(){
                    $("#ubahResellerForm").submit(function(event){
                        event.preventDefault();

                        $('#ubahResellerButtonLoading').show();
                        $('#ubahResellerButtonSubmit').hide();
                        $('.bootbox-close-button').hide();

                        var formData = new FormData(this);

                        $.ajax({
                            type:'POST',
                            dataType: 'json',
                            url: '/member/ubah_member_reseller',
                            data:formData,
                            contentType: false,
                            cache: false,
                            processData: false,
                            success:function(data){
                                $('#ubahResellerButtonLoading').hide();
                                $('#ubahResellerButtonSubmit').show();
                                $('.bootbox-close-button').show();
                                if(data.status === 'success'){
                                    Swal.fire(
                                        'Success!',
                                        'Member reseller successfully updated.',
                                        'success'
                                    ).then(() => {
                                        table.ajax.reload( null, false);
                                        bootbox.hideAll();
                                    });
                                } else {
                                    Swal.fire(
                                        'Oops...',
                                        'Something went wrong!',
                                        'error'
                                    )
                                }
                            }
                        });

                    });
                });
            });
        }

        function surrenderReseller(mbr_code){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, surrender!',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`/member/surrender_member_reseller/${mbr_code}`)
                    .then(response => {
                        return response.json();
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value.status === 'success') {
                    Swal.fire(
                        'Surrendered!',
                        'Member reseller has been succesfully surrendered.',
                        // 'Pihak lawan mengakui kekalahan.',
                        'success'
                    )
                    table.ajax.reload( null, false);
                }
                else{
                    Swal.fire(
                        'Oops...',
                        'Something went wrong!',
                        'error'
                    )
                }
            })
        }

        function clearFormReseller(){
            $('#mbr_code').val('');
            $('#konter_name').val('');
            $('#konter_kota').val('');
            $('#konter_address').val('');
        }

        $(document).ready(function(){

            $('.js-example-basic-single').select2();
            $('.select2-container--default').css('width', '');
            $('.select2-selection--single').css('border', '1px solid #ced4da');
            $('.select2-selection--single').css('min-height', '38px');
            $('.select2-selection__arrow').css('margin-top', '5px');

            $("#tambahResellerForm").submit(function(event){
                event.preventDefault();

                $('#tambahResellerButtonLoading').show();
                $('#tambahResellerButtonSubmit').hide();
                $('#tambahResellerButtonClose').hide();

                var formData = new FormData(this);

                $.ajax({
                    type:'POST',
                    dataType: 'json',
                    url: '/member/tambah_member_reseller',
                    data:formData,
                    contentType: false,
                    cache: false,
                    processData: false,
                    success:function(data){
                        $('#tambahResellerButtonLoading').hide();
                        $('#tambahResellerButtonSubmit').show();
                        $('#tambahResellerButtonClose').show();
                        if(data.status === 'success'){
                            Swal.fire(
                                'Success!',
                                'Member reseller successfully added.',
                                'success'
                            ).then(() => {
                                $('#member_reseller').DataTable().destroy();
                                dataTable();
                                $('#tambahResellerModal').modal('hide');
                            });
                        } else {
                            Swal.fire(
                                'Something went wrong!',
                                data.reason,
                                'error'
                            )
                        }
                    }
                });

            });
        });

    </script>
@endpush
