@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">Mutasi Bonus Transaksi</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Laporan</a></li>
            <li class="breadcrumb-item active">Mutasi Bonus Transaksi</li>
        </ol>
    </div>
</div>

<div class="p-30 bg-white">
    <form id="cariMutasiBonus">
        <div class="row">
            <div class="col-3">
                <label for="date_range" class="control-label">Pencarian Berdasarkan Tanggal :</label>
                <input id="toggle_tanggal" type="checkbox" checked data-toggle="toggle" data-size="mini" data-onstyle="success" data-offstyle="danger">
            </div>
            <div class="col-2">
                <label for="jenis_bonus" class="control-label">Jenis Bonus :</label>
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
                    <input id="date_range" type="text" class="form-control" name="date_range" autocomplete="off" placeholder="masukkan tanggal pencarian" required/>
                </div>
            </div>
            <div class="col-2">
                <select class="form-control" id="jenis_bonus" name="jenis_bonus" required>
                    <option value="mutasi_bonus" selected="selected">Mutasi Bonus</option>
                    <option value="bonus_active">Bonus Active</option>
                    <option value="bonus_lock">Bonus Lock</option>
                    <option value="bonus_pending">Bonus Pending</option>
                </select>
            </div>
            <div class="col-2">
                <select class="form-control" id="target_search" name="target_search" required>
                    <option value="Semua" selected="selected">Semua</option>
                    <option value="kode_ekl">Kode EKL</option>
                </select>
            </div>
            <div class="col-2">
                <input id="keyword" type="text" class="form-control" name="keyword" placeholder="Masukkan Kata Kunci" readonly required/>
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-success"><i class="mdi mdi-magnify mr-2"></i>Cari</button>
                <button type="button" class="btn btn-warning" onclick="resetDataTable()"><i class="mdi mdi-undo mr-2"></i>Reset</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table id="mutasi_bonus_trx" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Data Member</th>
                    <th>Info Bonus</th>
                    <th>Keterangan Bonus</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody class="small"></tbody>
        </table>
    </div>

</div>

@endsection

@push('scripts')
    <script>
        var start_date = "{{date('Y-m-d')}}";
        var end_date = "{{date('Y-m-d')}}";
        var custom_search = false;

        $(document).ready(function(){
            $('#toggle_tanggal').bootstrapToggle('off');
            $('#date_range').prop('readonly', true);
            dataTable();

            $("#cariMutasiBonus").submit(function(event) {
                event.preventDefault();
                $('#mutasi_bonus_trx').DataTable().destroy();
                custom_search = true;
                dataTable();
            });

            $('#target_search').on('change', function (e) {
                if(this.value == 'Semua'){
                    $('#keyword').prop('readonly', true);
                    $('#keyword').val('');
                }
                else{
                    $('#keyword').prop('readonly', false);
                }
            });
        });

        //toggle daterange
        $(function() {
            $('#toggle_tanggal').change(function() {
                if($(this).prop('checked')){
                    $('#date_range').prop('readonly', false);
                    start_date = "";
                    end_date = "";
                }
                else{
                    $('#date_range').prop('readonly', true);
                    $('#date_range').val('');
                    start_date = '';
                    end_date = '';
                }
            })
        })

        function resetDataTable(){
            custom_search = true;
            $('#toggle_tanggal').bootstrapToggle('off');
            $('#date_range').prop('readonly', true);
            $('#date_range').val('');
            $('#jenis_bonus').val('mutasi_bonus');
            $('#target_search').val('Semua');
            $('#keyword').val('');
            $('#keyword').prop('readonly', true);
            $('#select_status').val('');
            $('#mutasi_bonus_trx').DataTable().destroy();
            custom_search = false;
            dataTable();
        }

        function dataTable(){
            $('#mutasi_bonus_trx').DataTable({
                "lengthMenu": [
                    [10, 50, 200, 1000],
                    [10, 50, 200, 1000]
                ],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/laporan/get_mutasi_bonus_transaksi",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "start_date": start_date,
                        "end_date": end_date,
                        "custom_search": custom_search,
                        "jenis_bonus": $('#jenis_bonus').val(),
                        "target_search": $('#target_search').val(),
                        "keyword": $('#keyword').val()
                    }
                },
                "columns": [
                    {"data": "tanggal"},
                    {"data": "data_member"},
                    {"data": "info_bonus"},
                    {"data": "keterangan_bonus"},
                    {"data": "keterangan"}
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
                "ordering": false,
                "searching": false
            });
        }

        $(function() {
            $('input[name="date_range"]').daterangepicker({
                "autoUpdateInput": false,
                "locale":{
                    "format": "DD-MM-YYYY",
                    "separator": " to ",
                },
                "opens": "right",
                "maxDate": "{{date('d-m-Y')}}"
            }, function(start, end, label) {
                start_date = start.format('YYYY-MM-DD');
                end_date = end.format('YYYY-MM-DD');
            });

            $('input[name="date_range"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('DD-MM-YYYY') + ' to ' + picker.endDate.format('DD-MM-YYYY'));
            });
        });

    </script>
@endpush
