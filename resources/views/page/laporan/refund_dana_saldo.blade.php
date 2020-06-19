@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">Rerfund Dana Saldo</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Laporan</a></li>
            <li class="breadcrumb-item active">Rerfund Dana Saldo</li>
        </ol>
    </div>
</div>


<div class="p-30 bg-white">
    <form id="formSearchRefund">
        <div class="row">
            <div class="col-3">
                <label for="date_range" class="control-label">Pencarian Berdasarkan Tanggal :</label>
                <input id="toggle_tanggal" type="checkbox" data-toggle="toggle" data-size="mini" data-onstyle="success" data-offstyle="danger">
            </div>
            <div class="col-2">
                <label for="target_search" class="control-label">Target Pencarian :</label>
            </div>
            <div class="col-2">
                <label for="keyword" class="control-label">Kata Kunci :</label>
            </div>
        </div>
        <div class="row">
            <div class="col-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="mdi mdi-calendar-range"></i></span>
                    </div>
                    <input type="text" class="form-control datepicker" disabled name="tgl_search" id="tgl_search" autocomplete="off" placeholder="<?php echo date('d-m-Y'); ?>" value="">
                </div>
            </div>
            <div class="col-2">
                <select class="form-control" id="target_search" name="target_search" required>
                    <option value="" selected="selected" hidden="hidden">Pilih Target Pencarian</option>
                    <option value="mbr_code">ID EKL</option>
                    <option value="penarikan_code">Nomor Invoice</option>
                </select>
            </div>
            <div class="col-2">
                <input id="keyword" type="text" class="form-control" name="keyword" placeholder="Masukkan Kata Kunci" required/>
            </div>
            <div class="col-2">
                <button type="button" onclick="search()" class="btn btn-success"><i class="mdi mdi-magnify mr-2"></i>Cari</button>
                <button type="button" class="btn btn-warning" onclick="resetData()"><i class="mdi mdi-undo mr-2"></i>Reset</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table id="list_refund" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Tanggal Refund</th>
                    <th>ID EKL</th>
                    <th>Nama</th>
                    <th>Jumlah Dana</th>
                    <th>Operator</th>
                    <th>Keterangan</th>
                    <th>Kode Refferensi</th>
                </tr>
            </thead>
            <tbody class="small">

            </tbody>
            <tfoot>
                <tr>
                    <th>Tanggal Refund</th>
                    <th>ID EKL</th>
                    <th>Nama</th>
                    <th>Jumlah Dana</th>
                    <th>Operator</th>
                    <th>Keterangan</th>
                    <th>Kode Refferensi</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


@endsection

@push('scripts')
    <script>
        dataTable();
        function resetData(){
            $('#list_refund').DataTable().destroy();
            dataTable();
        }
        function intVal(i) {
            return typeof i === 'string' ?
            i.replace(/[\$,]/g, '') * 1 :
            typeof i === 'number' ?
            i : 0;
        }
        function search(){
            $('#list_refund').DataTable().destroy();
            var tgl_search = $('#tgl_search').val()
            var target_search = $('#target_search').val()
            var keyword = $('#keyword').val()
            dataTable(tgl_search,target_search,keyword)
        }
        function dataTable(tgl_search,target_search,keyword){
            $('#list_refund').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    "url" : "/laporan/get_refund_dana",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "_token": "<?= csrf_token()?>",
                        tgl_search :tgl_search,
                        target_search :target_search,
                        keyword :keyword
                    }
                },
                "buttons": [
                    "csv","excel","pdf","print"
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api(), data;

                    var colNum = [3];
                    var useRp = ['Rp '];
                    var i = 0;

                    colNum.forEach(function (element) {
                        page = api
                        .column(element, { page: 'current' })
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                        $(api.column(element).footer()).html(
                        useRp[i] + page.toLocaleString(undefined, { minimumFractionDigits: 0 })
                        );
                        i++;
                    });
                }
            })
        }
        $(function() {
            $('input[name="tgl_search"]').daterangepicker({
                singleDatePicker: true,
                showDropdowns: true,
                autoUpdateInput: false,
                "locale": {
                    "format": "YYYY-MM-DD"
                },
            }, function(start, end, label) {
                $('#tgl_search').val(start.format('YYYY-MM-DD'));
            });
        });
        //toggle daterange
        $(function() {
            $('#toggle_tanggal').change(function() {
                if($(this).prop('checked')){
                    $('#tgl_search').prop('disabled', false);
                    var date_now = moment().format('YYYY-MM-DD');
                    $('#tgl_search').val(date_now);
                }else{
                    $('#tgl_search').prop('disabled', true);
                    $('#tgl_search').val('');
                }
            })
        })



    </script>
@endpush
