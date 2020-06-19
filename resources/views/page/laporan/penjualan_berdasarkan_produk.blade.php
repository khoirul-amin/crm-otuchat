@extends('layouts.app')

@section('content')

<div class="row page-titles">
    <div class="col-md-5 col-8 align-self-center">
        <h3 class="text-themecolor">Penjualan Berdasarkan Produk</h3>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="javascript:void(0)">Laporan</a></li>
            <li class="breadcrumb-item active">Penjualan Berdasarkan Produk</li>
        </ol>
    </div>
</div>

<div class="p-30 bg-white">
    <form id="cariListTransaksi">
        <div class="row">
            <div class="col-3">
                <label for="date_range" class="control-label">Pencarian Berdasarkan Tanggal :</label>
                <input id="toggle_tanggal" type="checkbox" checked data-toggle="toggle" data-size="mini" data-onstyle="success" data-offstyle="danger">
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
                <select class="form-control" id="target_search" name="target_search" required>
                    <option value="" selected="selected" hidden="hidden">Pilih Target Pencarian</option>
                    <option value="Semua">Semua</option>
                    <option value="product_name">Nama Produk</option>
                    <option value="product_kode">Kode Produk</option>
                    <option value="supliyer_name">Nama Supplier</option>
                </select>
            </div>
            <div class="col-2">
                <input id="keyword" type="text" class="form-control" name="keyword" placeholder="Masukkan Kata Kunci" required/>
            </div>
            <div class="col-2">
                <button type="submit" class="btn btn-success"><i class="mdi mdi-magnify mr-2"></i>Cari</button>
                <button type="button" class="btn btn-warning" onclick="resetDataTable()"><i class="mdi mdi-undo mr-2"></i>Reset</button>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table id="list_transaksi" class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Kode Produk</th>
                    <th>Nama Supplier</th>
                    <th>Jumlah Penjualan</th>
                    <th>Total Penjualan</th>
                    <th>Total Pembelian</th>
                    <th>Laba Kotor</th>
                </tr>
            </thead>
            <tbody class="small"></tbody>
        </table>
    </div>
    <div id="jumlah_total" class="mt-3">
        <h6 id="sum_qty"></h6>
        <h6 id="sum_jual"></h6>
        <h6 id="sum_beli"></h6>
        <h6 id="sum_kotor"></h6>
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

            $("#cariListTransaksi").submit(function(event) {
                event.preventDefault();
                $('#list_transaksi').DataTable().destroy();
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
            $('#toggle_tanggal').bootstrapToggle('on');
            $('#date_range').val('');
            $('#target_search').val('');
            $('#keyword').val('');
            $('#keyword').prop('readonly', false);
            $('#select_status').val('');
            $('#list_transaksi').DataTable().destroy();
            custom_search = false;
            dataTable();
        }

        function dataTable(){
            $('#jumlah_total').hide();
            $('#list_transaksi').DataTable({
                lengthMenu: [
                    [10, 50, 200, 1000],
                    [10, 50, 200, 1000]
                ],
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/laporan/get_penjualan_berdasarkan_product",
                    "dataType": "json",
                    "type": "POST",
                    "data": {
                        "start_date": start_date,
                        "end_date": end_date,
                        "custom_search": custom_search,
                        "target_search": $('#target_search').val(),
                        "keyword": $('#keyword').val()
                    },
                    "dataSrc": function (json){
                        $('#sum_qty').html('Jumlah Penjualan: ' + json.data_total.sum_qty);
                        $('#sum_jual').html('Total Penjualan: ' + json.data_total.sum_jual);
                        $('#sum_beli').html('Total Pembelian: ' + json.data_total.sum_beli);
                        $('#sum_kotor').html('Laba Kotor: ' + json.data_total.sum_kotor);
                        $('#jumlah_total').show();
                        return json.data;
                    }
                },
                "columns": [
                    {"data": "product_name"},
                    {"data": "product_kode"},
                    {"data": "supliyer_name"},
                    {"data": "qty", "className": "text-right"},
                    {"data": "harga_jual", "className": "text-right"},
                    {"data": "harga_beli", "className": "text-right"},
                    {"data": "laba_kotor", "className": "text-right"},
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
                "order":[[1, "asc"]],
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
