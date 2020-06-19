@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">All Member</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Member</a></li>
            <li class="breadcrumb-item active">All Member</li>
        </ol>
    </div>
</div>

<div class="p-30 bg-white">
    <form id="customSearch">
        <div class="row">
            <div class="col-2">
                <label for="custom_search" class="control-label">Target Pencarian:</label>
            </div>
            <div class="col-2">
                <label for="keyword" class="control-label">Kata Kunci:</label>
            </div>
            <div class="col-2">
                <label for="keyword" class="control-label">Status Akun:</label>
            </div>
        </div>
        <div class="row">
            <div class="col-2">
                <select class="form-control" id="custom_search" name="custom_search" required>
                    <option value="" selected="selected" hidden="hidden">Pilih Target Pencarian</option>
                    <option value="Semua">Semua</option>
                    <option value="mbr_code">ID EKL</option>
                    <option value="mbr_name">Nama Member</option>
                    <option value="mbr_email">Email Member</option>
                    <option value="mbr_mobile">Telepon Member</option>
                    <option value="mbr_id_number">Nomor KTP Member</option>
                    <option value="mbr_type">Jabatan Member</option>
                    <option value="mbr_username">Username Member</option>
                    <option value="mbr_sponsor">ID EKL Upline</option>
                </select>
            </div>
            <div class="col-2">
                <input id="keyword" type="text" class="form-control" name="keyword" placeholder="Masukkan Kata Kunci" required/>
            </div>
            <div class="col-2">
                <select class="form-control" id="status_member" name="status_member" required>
                    <option value="" selected="selected" hidden="hidden">Pilih Status Member</option>
                    <option value="Semua">Semua</option>
                    <option value="Block">Block</option>
                    <option value="Active">Active</option>
                    <option value="Waiting">Waiting</option>
                    <option value="Inactive">Inactive</option>
                </select>
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-success"><i class="mdi mdi-magnify mr-2"></i>Search</button>
                <button type="button" class="btn btn-warning" onclick="resetDataTable()"><i class="mdi mdi-undo mr-2"></i>Reset</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table id="all_member" class="table table-hover table-bordered no-wrap">
            <thead>
                <tr>
                    <th class="notexport">Action</th>
                    <th>Data Member</th>
                    <th>Status</th>
                    <th>Data Sponsor</th>
                    <th>Kontak</th>
                    <th>Info Saldo</th>
                    <th>Info Member</th>
                </tr>
            </thead>
            <tbody class="small"></tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        var table;
        $(document).ready(function(){
            dataTable(0);

            $("#customSearch").submit(function(event){
                event.preventDefault();

                $('#all_member').DataTable().destroy();

                dataTable({
                    custom_search: $('#custom_search').val(),
                    keyword: $('#keyword').val(),
                    status: $('#status_member').val()
                });
            });
        });

        $(document).ready(function(){
            $('#custom_search').on('change', function (e) {
                if(this.value == 'Semua'){
                    $('#keyword').prop('readonly', true);
                    $('#keyword').val('');
                }
                else{
                    $('#keyword').prop('readonly', false);
                }
            });
        });

        function resetDataTable(){
            $('#custom_search').val('');
            $('#keyword').prop('readonly', false);
            $('#keyword').val('');
            $('#status_member').val('');
            $('#all_member').DataTable().destroy();

            dataTable(0);
        }

        function dataTable(data_search){
            var data;
            if(data_search == 0){
                data = "default";
            }
            else{
                data = data_search;           
            }

            table = $('#all_member').on('preXhr.dt', function () {
                // $('<label class="ml-4 mr-2" style="font-weight: normal;">Export: </label>').prependTo(".dt-buttons");
                $(".dt-buttons").addClass("ml-2");
            }).DataTable({
                "dom": 'lBfrtip',
                "buttons": [
                    {
                        "extend": 'excel',
                        "exportOptions": {
                            "columns": ':not(.notexport)'
                        }
                    }
                ],
                "lengthMenu": [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/member/get_all_member",
                    "dataType": "json",
                    "type": "POST",
                    "data": {"data_search": data}
                },
                "columns" : [
                    {"data": "action", "className": "text-center"},
                    {"data": "data_member"},
                    {"data": "status"},
                    {"data": "data_sponsor"},
                    {"data": "kontak"},
                    {"data": "info_saldo"},
                    {"data": "info_member"}
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
                "order": [[ 1, "DESC" ]],
                "columnDefs": [
                    { "orderable": false, "targets": 0 }
                ],
                "searching": false
            });
        }

        function blockMember(mbr_code){
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
                    return fetch(`/member/block_member/${mbr_code}`)
                    .then(response => {
                        return response.json();
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value.status === 'success') {
                    Swal.fire(
                        'Blocked!',
                        'Member has been succcesfully blocked.',
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

        function activeMember(mbr_code){
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
                    return fetch(`/member/active_member/${mbr_code}`)
                    .then(response => {
                        return response.json();
                    })
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.value.status === 'success') {
                    Swal.fire(
                        'Activated!',
                        'Member has been succesfully activated.',
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

        function ubahMember(mbr_code){
            var dialog = bootbox.dialog({
                title: 'Perubahan Data Member',
                size: "large",
                message: '<p><i class="fa fa-spin fa-spinner"></i> Loading...</p>'
            }).find(".modal-dialog").addClass("modal-dialog-centered");
            $.get("/member/get_detail_member/" + mbr_code, function(data, status){
                var message = '<form id="ubahMemberForm">';
                if(data.mbr_dob){
                    data.mbr_dob = data.mbr_dob.slice(8,10) + '-' + data.mbr_dob.slice(5,7) + '-' + data.mbr_dob.slice(0,4);
                }
                else{
                    data.mbr_dob = '';
                }
                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="mbr_code" class="control-label">ID EKL</label><input class="form-control" type="text" id="mbr_code" name="mbr_code" placeholder="ID EKL" value="' + data.mbr_code + '" autocomplete="off" readonly required/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="mbr_name" class="control-label">Nama Member</label><input class="form-control" type="text" id="mbr_name" name="mbr_name" value="'+ data.mbr_name +'" placeholder="Nama Member" autocomplete="off" required/></div></div></div>';

                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="mbr_id_number" class="control-label">Nomor Identitas</label><input class="form-control" type="text" id="mbr_id_number" name="mbr_id_number" placeholder="Nomor Identitas" value="' + data.mbr_id_number + '" autocomplete="off" required/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="mbr_mobile" class="control-label">Nomor Telepon</label><input class="form-control" type="text" id="mbr_mobile" name="mbr_mobile" value="'+ data.mbr_mobile +'" placeholder="Nomor Telepon" autocomplete="off" readonly required/></div></div></div>';

                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="mbr_email" class="control-label">Email</label><input class="form-control" type="text" id="mbr_email" name="mbr_email" placeholder="Email" value="' + data.mbr_email + '" autocomplete="off" required/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="mbr_address" class="control-label">Alamat</label><input class="form-control" type="text" id="mbr_address" name="mbr_address" value="'+ data.mbr_address +'" placeholder="Alamat" autocomplete="off" required/></div></div></div>';

                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="mbr_kota" class="control-label">Kota</label><input class="form-control" type="text" id="mbr_kota" name="mbr_kota" placeholder="Kota" value="' + data.mbr_kota + '" autocomplete="off" required/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="mbr_dob" class="control-label">Tanggal Lahir</label><input class="form-control datepicker" type="text" id="mbr_dob" name="mbr_dob" value="'+ data.mbr_dob +'" placeholder="Tanggal Lahir" autocomplete="off" required/></div></div></div>';

                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="mbr_bank_name" class="control-label">Rekening Bank</label><input class="form-control" type="text" id="mbr_bank_name" name="mbr_bank_name" placeholder="Rekening Bank" value="' + data.mbr_bank_name + '" autocomplete="off" required/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="mbr_bank_num" class="control-label">Nomor Rekening</label><input class="form-control" type="text" id="mbr_bank_num" name="mbr_bank_num" value="'+ data.mbr_bank_num +'" placeholder="Nomor Rekening" autocomplete="off" required/></div></div></div>';

                message += '<div class="row"><div class="col-6"><div class="form-group"><label for="mbr_bank_acc" class="control-label">Pemilik Rekening</label><input class="form-control" type="text" id="mbr_bank_acc" name="mbr_bank_acc" placeholder="mbr_bank_acc" value="' + data.mbr_bank_acc + '" autocomplete="off" required/></div></div>';
                message += '<div class="col-6"><div class="form-group"><label for="mbr_status" class="control-label">Status</label><input class="form-control" type="text" id="mbr_status" name="mbr_status" value="'+ data.mbr_status +'" placeholder="Status" autocomplete="off" readonly/></div></div></div>';

                // message += '<div class="row"><div class="col"><div class="form-group"><label for="key_approval" class="control-label">Key Approval</label><input class="form-control" type="text" id="key_approval" name="key_approval" placeholder="Key Approval" value="" autocomplete="off" required/></div></div></div>';
                
                message += '<div class="text-right"><button id="ubahMemberButtonLoading" class="btn btn-success disabled" style="display: none;">Loading...</button><button id="ubahMemberButtonSubmit" type="submit" class="btn btn-success"><i class="mdi mr-2 mdi-content-save"></i>Simpan</button></div></form>'

                dialog.init(function(){
                    dialog.find('.bootbox-body').html(message);
                });

                $(function() {
                    $('input[name="mbr_dob"]').daterangepicker({
                        autoUpdateInput: false,
                        singleDatePicker: true,
                        showDropdowns: true,
                        "locale": {
                            "format": "DD-MM-YYYY"
                        },
                    }, function(start, end, label) {
                        $("#mbr_dob").val(start.format('DD-MM-YYYY'));
                    });
                });

                $(document).ready(function(){
                    $("#ubahMemberForm").submit(function(event){
                        event.preventDefault();

                        $('#ubahMemberButtonLoading').show();
                        $('#ubahMemberButtonSubmit').hide();
                        $('.bootbox-close-button').hide();

                        var formData = new FormData(this);

                        $.ajax({
                            type:'POST',
                            dataType: 'json',
                            url: '/member/ubah_member',
                            data:formData,
                            contentType: false,
                            cache: false,
                            processData: false,
                            success:function(data){
                                $('#ubahMemberButtonLoading').hide();
                                $('#ubahMemberButtonSubmit').show();
                                $('.bootbox-close-button').show();
                                if(data.status === 'success'){
                                    Swal.fire(
                                        'Success!',
                                        'Member successfully updated.',
                                        'success'
                                    ).then(() => {
                                        table.ajax.reload( null, false);
                                        bootbox.hideAll();
                                    });
                                } else {
                                    Swal.fire(
                                        'Oops...',
                                        data.reason,
                                        'error'
                                    )
                                }
                            }
                        });

                    });
                });
            });
        }

    </script>
@endpush
