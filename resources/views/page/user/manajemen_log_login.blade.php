@extends('layouts.app')

@section('content')

    <!-- ============================================================== -->
    <!-- Breadcrumb -->
    <!-- ============================================================== -->
    <div class="row page-titles">
        <div class="col-md-5 col-8 align-self-center">
            <h3 class="text-themecolor">Manajemen Log Login</h3>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">User CRM</a></li>
                <li class="breadcrumb-item active">Manajemen Log Login</li>
            </ol>
        </div>
    </div>

    <!-- ============================================================== -->
    <!-- Content -->
    <!-- ============================================================== -->    
    <div class="p-30" style="background-color:white;">
        <h2>CRM Log login</h2><hr>
        <div class="row p-0 m-0">
            <div class="col">
                <div class="table-responsive">
                    <table id="dt_log_login" class="table table-hover table-striped table-bordered no-wrap">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Ip Address</th>
                                <th>Waktu</th>
                                <th>User Agent</th>
                            </tr>
                        </thead>
                        <tbody class="small"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection
@push('scripts')
    <script>
        dataTableLogLogin();
        function dataTableLogLogin(){
            table_log_activity = $('#dt_log_login').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    "url" : "/user/get_log_login",
                    "dataType": "json",
                    "type": "POST"
                },
                "columns" : [
                    {"data": "nama"},
                    {"data": "ip_address"},
                    {"data": "waktu"},
                    {"data": "user_agent"}
                ],
                "order": [[ 2, "DESC" ]],
            });
        }
    </script>
@endpush