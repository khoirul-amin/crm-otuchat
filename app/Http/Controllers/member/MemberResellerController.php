<?php

namespace App\Http\Controllers\member;

use Illuminate\Http\Request;
use App\Http\Models\partner_eklanku_mv;
use App\Http\Models\mbr_partner_m;
use App\Http\Models\mbr_list_m;
use App\Http\Models\kota_indonesia_m;
use App\Http\Models\pembelian_m;

use App\Repositories\HelperInterface as Helper;

use DB;

class MemberResellerController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        $kota = kota_indonesia_m::all();
        return view('page.member.member_reseller', compact('kota'));
    }

    public function getMemberReseller(Request $request){
        // print_r($request->all());
        // Columns
        $columns = array(
            0 => 'konter_name',
            1 => 'konter_level',
            2 => 'mbr_code',
            3 => 'mbr_name',
            4 => 'mbr_mobile',
            5 => 'konter_status',
            6 => 'konter_date',
            7 => 'konter_addres',
            8 => 'konter_kota',
            9 => 'konter_provinsi',
            10 => 'evaluasi_terakhir',
            11 => 'evaluasi_selanjutnya'
        );

        $order_columns = array(
            0 => 'konter_status',
            1 => 'konter_name',
            2 => 'mbr_code',
            3 => 'konter_date',
            4 => 'konter_addres',
            5 => 'evaluasi_terakhir'
        );

        // Total Data
        $totaldata = DB::table('vw_patner_eklanku')->count();
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $order_columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))){
            $posts = DB::table('vw_patner_eklanku')->select($columns[0], DB::raw("
                        case when konter_level = 1 then 'RESELLER SIGNATURE'
                            when konter_level = 2 then 'RESELLER PLATINUM'
                            when konter_level = 3 then 'RESELLER GOLD'
                            when konter_level = 4 then 'RESELLER SILVER'
                        else
                            'konter level tidak diketahui'
                        end as konter_level
                    "),
                        $columns[2], $columns[3], $columns[4], $columns[5],
                        DB::raw("to_char(konter_date, 'YYYY-MM-DD') as konter_date"),
                        $columns[7], $columns[8], $columns[9],
                        DB::raw("to_char(evaluasi_terakhir, 'YYYY-MM-DD') as evaluasi_terakhir"),
                        DB::raw("to_char(evaluasi_terakhir + interval '31' day, 'YYYY-MM-DD') as evaluasi_selanjutnya")
                    )
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            $totalFiltered = $totaldata;
        } else {
            $search = $request->input('search.value');
            $data_search = DB::table('vw_patner_eklanku')->select($columns[0], DB::raw("
                        case when konter_level = 1 then 'RESELLER SIGNATURE'
                            when konter_level = 2 then 'RESELLER PLATINUM'
                            when konter_level = 3 then 'RESELLER GOLD'
                            when konter_level = 4 then 'RESELLER SILVER'
                        else
                            'konter level tidak diketahui'
                        end as konter_level
                    "),
                        $columns[2], $columns[3], $columns[4], $columns[5],
                        DB::raw("to_char(konter_date, 'YYYY-MM-DD') as konter_date"),
                        $columns[7], $columns[8], $columns[9],
                        DB::raw("to_char(evaluasi_terakhir, 'YYYY-MM-DD') as evaluasi_terakhir"),
                        DB::raw("to_char(evaluasi_terakhir + interval '31' day, 'YYYY-MM-DD') as evaluasi_selanjutnya")
                    )
                    ->where($columns[0], 'ilike', "%{$search}%")
                    ->orWhere($columns[1], 'ilike', "%{$search}%")
                    ->orWhere($columns[2], 'ilike', "%{$search}%")
                    ->orWhere($columns[3], 'ilike', "%{$search}%")
                    ->orWhere($columns[4], 'ilike', "%{$search}%")
                    ->orWhere($columns[5], 'ilike', "%{$search}%")
                    ->orWhere($columns[6], 'ilike', "%{$search}%")
                    ->orWhere($columns[7], 'ilike', "%{$search}%")
                    ->orWhere($columns[8], 'ilike', "%{$search}%")
                    ->orWhere($columns[9], 'ilike', "%{$search}%")
                    ->orWhere($columns[10], 'ilike', "%{$search}%")
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
                if($row->konter_status === 'Block'){
                    $nestedData['action'] =
                    '<button class="btn btn-success btn-sm" onClick="activeReseller(\'' . $row->mbr_code . '\', \'' . $row->konter_status . '\')"><i class="mdi mr-2 mdi-lock-open"></i>Active</button><br>
                    <button class="btn disabled mt-2 btn-info btn-sm"><i class="mdi mr-2 mdi-settings"></i>Ubah</button><br>
                    <button class="btn mt-2 btn-warning btn-sm" onClick="surrenderReseller(\'' . $row->mbr_code . '\')"><i class="mdi mr-2 mdi-undo"></i>Surrender</button>
                    ';
                }
                else{
                    $nestedData['action'] =
                    '<button class="btn btn-danger btn-sm" onClick="blockReseller(\'' . $row->mbr_code . '\', \'' . $row->konter_status . '\')"><i class="mdi mr-2 mdi-lock"></i>Block</button><br>
                    <button class="btn mt-2 btn-info btn-sm" onClick="ubahReseller(\'' . $row->mbr_code . '\')"><i class="mdi mr-2 mdi-settings"></i>Ubah</button><br>
                    <button class="btn mt-2 btn-warning btn-sm" onClick="surrenderReseller(\'' . $row->mbr_code . '\')"><i class="mdi mr-2 mdi-undo"></i>Surrender</button>
                    ';
                }
                $nestedData['data_outlet'] = '
                        <strong>Nama Outlet:</strong> ' . $row->konter_name . '<br>
                        <strong>Kategori:</strong> ' . $row->konter_level;
                $nestedData['data_pemilik'] = '
                        <strong>Id EKL:</strong> ' . $row->mbr_code . '<br>
                        <strong>Nama:</strong> ' . $row->mbr_name;
                if($row->konter_status == 'Active'){
                    $status = '<strong>Status: </strong><span class="label label-success ml-1"> ' . $row->konter_status . '</span>';
                }
                else{
                    $status = '<strong>Status: </strong><span class="label label-danger ml-1"> ' . $row->konter_status . '</span>';
                }
                $nestedData['keterangan'] = $status . '
                        <br><strong>Tanggal Terdaftar:</strong> ' . $row->konter_date;
                $nestedData['alamat_outlet'] = '
                        <strong>Alamat:</strong> ' . $row->konter_addres . '<br>
                        <strong>Kota:</strong> ' . $row->konter_kota . '<br>
                        <strong>Provinsi:</strong> ' . $row->konter_provinsi;
                $nestedData['info_evaluasi'] = '
                        <strong>Evaluasi Terakhir:</strong> ' . $row->evaluasi_terakhir . '<br>
                        <strong>Evaluasi Selanjutnya:</strong> ' . $row->evaluasi_selanjutnya;
                $data[] = $nestedData;
            }
        }

        echo json_encode(array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => intval($totaldata),
            "recordsFiltered"   => intval($totalFiltered),
            "data"              => $data
        ));
    }

    public function blockReseller($mbr_code){
        $update = mbr_partner_m::where('mbr_code', '=', $mbr_code)
            ->update(['konter_status' => 'Block']);
        if($update){
            $this->helper->logAction(4, $mbr_code, 'block');
            return response()->json(array("status" => "success"));
        }
        else{
            return response()->json(array("status" => "failed"));
        }
    }

    public function activeReseller($mbr_code){
        $update = mbr_partner_m::where('mbr_code', '=', $mbr_code)
            ->update(['konter_status' => 'Active']);
        if($update){
            $this->helper->logAction(4, $mbr_code, 'active');
            return response()->json(array("status" => "success"));
        }
        else{
            return response()->json(array("status" => "failed"));
        }
    }

    public function surrenderReseller($mbr_code){
        $update = mbr_partner_m::where('mbr_code', '=', $mbr_code)
            ->update(['konter_status' => 'Surrender']);
        if($update){
            $this->helper->logAction(4, $mbr_code, 'surrender');
            return response()->json(array("status" => "success"));
        }
        else{
            return response()->json(array("status" => "failed"));
        }
    }

    public function getDetailMemberReseller($mbr_code){
        $data = DB::table('vw_patner_eklanku')->where('mbr_code', '=', $mbr_code)->first();
        return response()->json($data);
    }

    public function ubahMemberReseller(Request $request){

        $mbr_code = $request->input('mbr_code');
        $konter_name = $request->input('konter_name');
        $konter_address = $request->input('konter_address');
        $konter_kota = $request->input('konter_kota');
        $key_approval = $request->input('key_approval');

        try{
            $update = mbr_partner_m::where('mbr_code', '=', $mbr_code)
                ->update([
                    'konter_name' => $konter_name,
                    'konter_addres' => $konter_address,
                    'konter_kota' => $konter_kota,
                ]);

            if($update){
                $this->helper->logAction(4, $mbr_code, 'update');
                return response()->json(array('status' => 'success'));
            }
            return response()->json(array('status' => 'gagal'));
        }catch(Exception $e){
            return response()->json(array('status' => 'gagal'));
        }
    }

    public function tambahMemberReseller(Request $request){
        $mbr_code = $request->input('mbr_code');
        $konter_name = strtoupper($request->input('konter_name'));
        $konter_address = strtoupper($request->input('konter_address'));
        $konter_kota = strtoupper($request->input('konter_kota'));
        $key_approval = $request->input('key_approval');

        try{
            //check mbr_code
            $check = mbr_list_m::find($mbr_code);
            if(!$check){
                return response()->json(array('status' => 'gagal', 'reason' => 'ID EKL Tidak Ditemukan'));
            }

            //check existing member reseller
            $check = mbr_partner_m::find($mbr_code);
            if($check){
                return response()->json(array('status' => 'gagal', 'reason' => 'ID EKL Sudah Terdaftar Sebagai Member Reseller'));
            }

            //check is member premium
            $check = pembelian_m::select(DB::raw('count(member_code)'))
                ->where('member_code', '=', $mbr_code)
                ->where('pembelian_status', '=', 'Active')
                ->first();
            if($check->count >= 1){
                return response()->json(array('status' => 'gagal', 'reason' => 'ID EKL Sudah Terdaftar Sebagai Member Premium'));
            }

            //check downline
            $check = mbr_list_m::select(DB::raw('count(mbr_sponsor)'))
                ->where('mbr_sponsor', '=', $mbr_code)
                ->where('mbr_pswd', '!=', '-')
                ->first();
            if($check->count >= 1){
                return response()->json(array('status' => 'gagal', 'reason' => 'ID EKL Sudah Memiliki Downline'));
            }

            DB::beginTransaction();

            $insert = new mbr_partner_m;
            $insert->mbr_code = $mbr_code;
            $insert->konter_name = $konter_name;
            $insert->konter_addres = $konter_address;
            $insert->konter_kota = $konter_kota;
            $insert->konter_date = date("Y-m-d H:i:s");
            $insert->konter_cek = date("Y-m-d H:i:s");
            $insert->konter_status = 'Active';
            $insert->save();

            $update = mbr_list_m::where('mbr_code', '=', $mbr_code)
                ->update(['mbr_type' => 'H2H']);

            if($insert && $update){
                DB::commit();
                $this->helper->logAction(4, $mbr_code, 'insert');
                return response()->json(array('status' => 'success'));
            }
            else{
                DB::rollback();
                return response()->json(array('status' => 'gagal', 'reason' => 'Gagal Tambah Data'));
            }
        }catch(Exception $e){
            DB::rollback();
            return response()->json(array('status' => 'gagal', 'reason' => 'Sistem Error'));
        }
    }

}
