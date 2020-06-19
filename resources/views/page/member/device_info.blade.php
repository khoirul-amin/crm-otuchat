@extends('layouts.app')

@section('content')
<style>
    .style{
        border: 1px solid grey;
        margin-bottom: 10px;
        border-radius: 5px;
        padding-top: 10px;
        padding-bottom: 10px;
        padding-left: 10px;
        padding-right: 10px;
    }
    .my-custom-scrollbar {
        position: relative;
        height: 200px;
        overflow: auto;
    }
    .table-wrapper-scroll-y {
        display: block;
    }
</style>

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Device Info</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Member</a></li>
                <li class="breadcrumb-item active">Device Info</li>
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
                            <option value="mbr_mobile">Nomer HP</option>
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
            <table id="device_info" class="table table-basic table-bordered no-wrap">
                <thead>
                    <tr>
                        <th class="text-center">EKL ID</th>
                        <th class="text-center">Detail Member</th>
                        <th class="text-center">Device</th>
                    </tr>
                </thead>
                <tbody style="font-size:10pt">

                </tbody>
                <thead>
                    <tr>
                        <th class="text-center">EKL ID</th>
                        <th class="text-center">Detail Member</th>
                        <th class="text-center">Device</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

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
        allDataUpgradeMember();

        function resetDatatable(){
            $('#device_info').DataTable().destroy();
            allDataUpgradeMember();
        };

        function search(){
            $('#device_info').DataTable().destroy();
            var column_search = document.getElementById('column_search');
            var value_search = document.getElementById('value_search');

            var start_date = document.getElementById('start_date');
            var end_date = document.getElementById('end_date');

            allDataUpgradeMember(column_search.value, value_search.value);
        };

        function allDataUpgradeMember(column, value){
            // console.log(search,value);
            $('#device_info').DataTable({
                lengthMenu: [[10, 50, 200, 1000], [10, 50, 200, 1000]],
                "processing": true,
                "serverSide": true,
                "searching": false,
                "ajax": {
                    "url" : "/member/get_all_device_info",
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
                    {"data": "mbr_code"},
                    {"data": "detail"},
                    {"data": "device"},
                ]
            });
        }

    </script>
@endpush
