@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">Penjualan Berdasarkan Supplier</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Laporan</a></li>
            <li class="breadcrumb-item active">Penjualan Berdasarkan Supplier</li>
        </ol>
    </div>
</div>

<div class="p-30 bg-white">
    <form id="penjualanSupplier">
        <div class="row">
            <div class="col-4">
                <label for="date_range" class="control-label">Pencarian Berdasarkan Tanggal :</label>
                <input id="toggle_tanggal" type="checkbox" checked data-toggle="toggle" data-size="mini" data-onstyle="success" data-offstyle="danger">
            </div>
        </div>
        <div class="row">
            <div class="col-4">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="mdi mdi-calendar-range"></i></span>
                    </div>
                    <input id="date_range" type="text" class="form-control" name="date_range" autocomplete="off" placeholder="masukkan tanggal pencarian" required/>
                </div>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-success"><i class="mdi mdi-magnify mr-2"></i>Cari</button>
                <button type="button" class="btn btn-warning" onclick="resetDataTable()"><i class="mdi mdi-undo mr-2"></i>Reset</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table id="penjualan_supplier" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Nama Supplier</th>
                    <th>Jumlah Penjualan</th>
                    <th>Total Penjualan</th>
                    <th>Total Pembelian</th>
                    <th>Laba Kotor</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>Total</th>
                    <th>Total</th>
                    <th>Total</th>
                    <th>Total</th>
                    <th>Total</th>
                </tr>
            </tfoot>
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
            dataTable();

            $("#penjualanSupplier").submit(function(event) {
                event.preventDefault();
                $('#penjualan_supplier').DataTable().destroy();
                custom_search = true;
                dataTable();
            });
        });

        //toggle daterange
        $(function() {
            $('#toggle_tanggal').change(function() {
                if($(this).prop('checked')){
                    $('#date_range').prop('disabled', false);
                    start_date = '';
                    end_date = '';
                }
                else{
                    $('#date_range').prop('disabled', true);
                    $('#date_range').val('');
                    start_date = '';
                    end_date = '';
                }
            })
        })

        function resetDataTable(){
            custom_search = true;
            $('#toggle_tanggal').bootstrapToggle('off');
            $('#date_range').val('');
            $('#target_search').val('');
            $('#keyword').val('');
            $('#keyword').prop('readonly', false);
            $('#select_status').val('');
            $('#penjualan_supplier').DataTable().destroy();
            custom_search = false;
            dataTable();
        }

        function dataTable(){
            $('#penjualan_supplier').DataTable({
                "processing": true,
                "serverSide": true,
                "paging": false,
                "info": false,
                "ajax": {
                    "url" : "/laporan/get_penjualan_berdasarkan_supplier",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "start_date": start_date,
                        "end_date": end_date,
                        "custom_search": custom_search,
                        "target_search": $('#target_search').val(),
                        "keyword": $('#keyword').val()
                    }
                },
                "columns": [
                    {"data": "supplier"},
                    {"data": "jumlah_penjualan"},
                    {"data": "total_penjualan", "className": "text-right"},
                    {"data": "total_pembelian", "className": "text-right"},
                    {"data": "laba_kotor", "className": "text-right"},
                ],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api(), data;

                    var colNum = [1, 2, 3, 4];
                    var useRp = ['', 'Rp. ', 'Rp. ', 'Rp. '];
                    var i = 0;

                    colNum.forEach(function (element) {
                        page = api
                        .column(element)
                        .data()
                        .reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);
                        console.log(data)
                        $(api.column(element).footer()).html(useRp[i] + page.toLocaleString(undefined, { minimumFractionDigits: 0 }));
                        i++;
                    });
                },
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
                    zeroRecords: "Data tidak ditemukan"
                },
                "order":[[1, "asc"]],
                "searching": false
            });
        }

        function intVal(i) {
            return typeof i === 'string' ?
                i.replace(/[\$,]/g, '') * 1 :
                typeof i === 'number' ?
                i : 0;
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
