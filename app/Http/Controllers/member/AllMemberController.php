<?php

namespace App\Http\Controllers\member;

use Illuminate\Http\Request;
use App\Http\Models\mbr_list_m;

use App\Repositories\HelperInterface as Helper;

use DB;

class AllMemberController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.member.all_member');
    }

    public function getAllMember(Request $request){
        // print_r($request->all());
        // Columns
        $columns = array(
            0 => 'mbr_name',
            1 => 'mbr_code',
            2 => 'mbr_type',
            3 => 'mbr_status',
            4 => 'mbr_token',
            5 => 'token_gowis',
            6 => 'sponsor_name',
            7 => 'mbr_sponsor',
            8 => 'sponsor_type',
            9 => 'mbr_mobile',
            10 => 'mbr_id_number',
            11 => 'mbr_email',
            12 => 'mbr_kota',
            13 => 'mbr_saldo',
            14 => 'mbr_bonus',
            15 => 'mbr_type_app',
            16 => 'mbr_date',
            17 => 'mbr_username',
            18 => 'type_keanggotan'
        );

        $order_columns = array(
            0 => 'mbr_status',
            1 => 'mbr_code',
            2 => 'mbr_status',
            3 => 'mbr_sponsor',
            4 => 'mbr_mobile',
            5 => 'mbr_saldo',
            6 => 'mbr_date'
        );

        $custom_search = $request->input('data_search');
        // Total Data
        $totaldata = mbr_list_m::count();
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $order_columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))){
            $posts = mbr_list_m::from('mbr_list AS A')->select(
                'A.'.$columns[0],
                'A.'.$columns[1],
                'A.'.$columns[2],
                'A.'.$columns[3],
                'A.'.$columns[4],
                'A.'.$columns[5],
                'B.mbr_name AS sponsor_name',
                'A.'.$columns[7],
                'B.mbr_type AS sponsor_type',
                'A.'.$columns[9],
                'A.'.$columns[10],
                'A.'.$columns[11],
                'A.'.$columns[12],
                'C.mbr_amount AS mbr_saldo',
                'D.mbr_amount AS mbr_bonus',
                'A.'.$columns[15],
                'A.'.$columns[16],
                'A.'.$columns[17],
                'A.'.$columns[18]
            )
            ->leftJoin('mbr_list AS B', 'A.mbr_sponsor', '=', 'B.mbr_code')
            ->leftJoin('mbr_saldo AS C', 'C.mbr_code', '=', 'A.mbr_code')
            ->leftJoin('mbr_bonus_transaksi AS D', 'D.mbr_code', '=', 'A.mbr_code');
            if($custom_search === 'default'){
                $totalFiltered = $totaldata;
            }
            else{
                if($custom_search['custom_search'] != 'Semua' ){
                    $posts = $posts->where('A.' . $custom_search['custom_search'], 'ilike', "%{$custom_search['keyword']}%");
                }
                if($custom_search['status'] != 'Semua'){
                    $posts = $posts->where('A.' . $columns[3], 'ilike', "%{$custom_search['status']}%");
                }

                $totalFiltered = count($posts->get());
            }
            $posts = $posts->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
        } else {
            $search = $request->input('search.value');
            $data_search = mbr_list_m::from('mbr_list AS A')->select(
                'A.'.$columns[0],
                'A.'.$columns[1],
                'A.'.$columns[2],
                'A.'.$columns[3],
                'A.'.$columns[4],
                'A.'.$columns[5],
                'B.mbr_name AS sponsor_name',
                'A.'.$columns[7],
                'B.mbr_type AS sponsor_type',
                'A.'.$columns[9],
                'A.'.$columns[10],
                'A.'.$columns[11],
                'A.'.$columns[12],
                'C.mbr_amount AS mbr_saldo',
                'D.mbr_amount AS mbr_bonus',
                'A.'.$columns[15],
                'A.'.$columns[16],
                'A.'.$columns[17],
                'A.'.$columns[18]
            )
            ->leftJoin('mbr_list AS B', 'A.mbr_sponsor', '=', 'B.mbr_code')
            ->leftJoin('mbr_saldo AS C', 'C.mbr_code', '=', 'A.mbr_code')
            ->leftJoin('mbr_bonus_transaksi AS D', 'D.mbr_code', '=', 'A.mbr_code');

            if($custom_search != 'default'){
                $data_search = $posts->where('A.' . $custom_search['custom_search'], 'ilike', "%{$custom_search['keyword']}%");
            }

            $data_search = $data_search->where(function($query) use ($columns, $search)
            {
                $query->orWhere('A.'.$columns[0], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[1], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[2], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[3], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[4], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[5], 'ilike', "%{$search}%")
                ->orWhere('B.mbr_name', 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[7], 'ilike', "%{$search}%")
                ->orWhere('B.mbr_type', 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[9], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[10], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[11], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[12], 'ilike', "%{$search}%")
                ->orWhere('C.mbr_amount', 'ilike', "%{$search}%")
                ->orWhere('D.mbr_amount', 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[15], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[16], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[17], 'ilike', "%{$search}%")
                ->orWhere('A.'.$columns[18], 'ilike', "%{$search}%");
            })
            ->orderBy($order, $dir);

            $totalFiltered = count($data_search->get());
            $posts = $data_search
                ->offset($start)
                ->limit($limit)
                ->get();
        }

        $data = array();
        if($posts){
            foreach($posts as $row){
                if($row->mbr_status == 'Active'){
                    $nestedData['action'] = 
                        '<button class="btn btn-danger btn-sm" onClick="blockMember(\'' . $row->mbr_code . '\')"><i class="mdi mr-2 mdi-lock"></i>Block</button><br>
                        <button class="btn mt-2 btn-warning btn-sm" onClick="ubahMember(\'' . $row->mbr_code . '\')"><i class="mdi mr-2 mdi-settings"></i>Ubah</button>
                    ';
                }
                else{
                    if($row->mbr_name == '-'){
                        $nestedData['action'] = 
                            '<button class="btn btn-success btn-sm" disabled><i class="mdi mr-2 mdi-lock-open"></i>Active</button><br>
                            <button class="btn mt-2 btn-warning btn-sm" disabled><i class="mdi mr-2 mdi-settings"></i>Ubah</button>
                        ';
                    }
                    else{
                        $nestedData['action'] = 
                            '<button class="btn btn-success btn-sm" onClick="activeMember(\'' . $row->mbr_code . '\')"><i class="mdi mr-2 mdi-lock-open"></i>Active</button><br>
                            <button class="btn mt-2 btn-warning btn-sm" disabled><i class="mdi mr-2 mdi-settings"></i>Ubah</button>
                        ';
                    }
                }
                $nestedData['data_member'] = 
                        $row->mbr_name . ' <br>'.
                        $row->mbr_code . ' <br>'.
                        $row->mbr_type;

                if($row->mbr_status == 'Active'){
                    $mbr_status = '<span class="text-success">' . $row->mbr_status . ' </span>';
                }
                else if($row->mbr_status == 'Waiting'){
                    $mbr_status = '<span class="text-warning">' . $row->mbr_status . ' </span>';
                }
                else if($row->mbr_status == 'Block' && $row->mbr_name == '-'){
                    $mbr_status = '<span class="text-danger">Block</span>';
                }
                else{
                    $mbr_status = '<span class="text-danger">' . $row->mbr_status . ' </span>';
                }
                if($row->mbr_token == 'NOT USE'){
                    $mbr_token = '<span class="text-danger">No</span>';
                }
                else{
                    $mbr_token = '<span class="text-success">Yes</span>';
                }
                if($row->token_gowis == 'NOT USE'){
                    $token_gowist = '<span class="text-danger">No</span>';
                }
                else{
                    $token_gowist = '<span class="text-success">Yes</span>';
                }
                $nestedData['status'] = 
                        '<strong>Status Akun:</strong> ' . $mbr_status . ' <br>'.
                        '<strong>Login OTU:</strong> ' . $mbr_token . ' <br>'.
                        '<strong>Login Gowist:</strong> ' . $token_gowist;

                $nestedData['data_sponsor'] = 
                        $row->sponsor_name . ' <br>'.
                        $row->mbr_sponsor . ' <br>'.
                        $row->sponsor_type;

                $nestedData['kontak'] = 
                        '<strong>Telepon:</strong> ' . $row->mbr_mobile . ' <br>'.
                        '<strong>KTP:</strong> ' . $row->mbr_id_number . ' <br>'.
                        '<strong>Email:</strong> ' . $row->mbr_email . ' <br>'.
                        '<strong>Kota:</strong> ' . $row->mbr_kota;

                $nestedData['info_saldo'] = 
                        '<strong>Saldo:</strong> ' . $this->convertCurrency($row->mbr_saldo) . ' <br>'.
                        '<strong>Bonus:</strong> ' . $this->convertCurrency($row->mbr_bonus);

                if($row->type_keanggotan == 0){
                    $type_keanggotan = '<span class="text-danger">Reject/Belum Pengajuan</span>';
                }
                else if($row->type_keanggotan == 1){
                    $type_keanggotan = '<span class="text-success">Active</span>';
                }
                else if($row->type_keanggotan == 2){
                    $type_keanggotan = '<span class="text-warning">Waiting</span>';
                }
                else{
                    $type_keanggotan = '-';
                }
                $nestedData['info_member'] = 
                        '<strong>Tipe Member:</strong> ' . $row->mbr_type_app . ' <br>'.
                        '<strong>Tanggal Daftar:</strong> ' . $row->mbr_date . ' <br>'.
                        '<strong>Username:</strong> ' . $row->mbr_username . ' <br>'.
                        '<strong>Verifikasi:</strong> ' . $type_keanggotan;

                $data[] = $nestedData;
            }
        }

        return response()->json(array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => intval($totaldata),
            "recordsFiltered"   => intval($totalFiltered),
            "data"              => $data
        ));

    }

    public function blockMember($mbr_code){
        $update = mbr_list_m::where('mbr_code', '=', $mbr_code)
            ->update(['mbr_status' => 'Block', 'mbr_token' => 'NOT USE']);
        if($update){
            $this->helper->logAction(8, $mbr_code, 'block');
            return response()->json(array("status" => "success"));
        }
        else{
            return response()->json(array("status" => "failed"));
        }
    }

    public function activeMember($mbr_code){
        $update = mbr_list_m::where('mbr_code', '=', $mbr_code)
            ->update(['mbr_status' => 'Active', 'mbr_token' => '']);
        if($update){
            $this->helper->logAction(8, $mbr_code, 'active');
            return response()->json(array("status" => "success"));
        }
        else{
            return response()->json(array("status" => "failed"));
        }
    }

    public function ubahMember(Request $request){
        $mbr_code = $request->input('mbr_code');
        $mbr_name = strtoupper($request->input('mbr_name'));
        $mbr_id_number = $request->input('mbr_id_number');
        $mbr_email = $request->input('mbr_email');
        $mbr_address = $request->input('mbr_address');
        $mbr_kota = $request->input('mbr_kota');
        $mbr_dob = date('Y-m-d', strtotime($request->input('mbr_dob')));
        $mbr_bank_name = $request->input('mbr_bank_name');
        $mbr_bank_num = $request->input('mbr_bank_num');
        $mbr_bank_acc = $request->input('mbr_bank_acc');
        try{
            $check_mbr_id_number = mbr_list_m::where('mbr_id_number', '=', $mbr_id_number)->where('mbr_code', '!=', $mbr_code)->first();
            $check_mbr_email = mbr_list_m::where('mbr_email', '=', $mbr_email)->where('mbr_code', '!=', $mbr_code)->first();
            $check_mbr_username = mbr_list_m::where('mbr_username', '=', $mbr_email)->where('mbr_code', '!=', $mbr_code)->first();

            if($check_mbr_id_number){
                return response()->json(array('status' => 'gagal', 'reason' => 'Nomor identitas sudah terdaftar, silahkan gunakan nomor lain!'));

            }
            if($check_mbr_email){
                return response()->json(array('status' => 'gagal', 'reason' => 'Alamat email sudah terdaftar, silahkan gunakan email lain!'));

            }
            if($check_mbr_username){
                return response()->json(array('status' => 'gagal', 'reason' => 'Username sudah terdaftar, silahkan gunakan email lain!'));
            }

            $update = mbr_list_m::where('mbr_code', '=', $mbr_code)
                ->update([
                    'mbr_name' => $mbr_name,
                    'mbr_id_number' => $mbr_id_number,
                    'mbr_email' => $mbr_email,
                    'mbr_address' => $mbr_address,
                    'mbr_kota' => $mbr_kota,
                    'mbr_dob' => $mbr_dob,
                    'mbr_bank_name' => $mbr_bank_name,
                    'mbr_bank_num' => $mbr_bank_num,
                    'mbr_bank_acc' => $mbr_bank_acc
                ]);

            if($update){
                $this->helper->logAction(8, $mbr_code, 'update');
                return response()->json(array('status' => 'success'));
            }
            return response()->json(array('status' => 'gagal', 'reason' => 'kesalahan sistem!'));
        }catch(Exception $e){
            return response()->json(array('status' => 'gagal', 'reason' => 'kesalahan sistem!'));
        }
    }

    public function getDetailMember($mbr_code){
        return response()->json(mbr_list_m::where('mbr_code', '=', $mbr_code)->first());
    }

    // Conver integer to rupiah
    function convertCurrency($n) {
        return 'Rp ' . number_format($n,0,',','.');
    }

}
