<?php

namespace App\Http\Controllers\member;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Repositories\HelperInterface as Helper;
use DB;

use GuzzleHttp\Client;

// Model
use App\Http\Models\mbr_list_m;
use App\Http\Models\mbr_upgrade_verification_m;
use App\Http\Models\kota_indonesia_m;
use App\Http\Models\mbr_saldo_m;
use App\Http\Models\bank_indonesia_m;

class UpgradeMemberController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function GetCity(){

        $url    = "https://api.eklanku.com/developapi/MemberWeb/kota_provinsi";
        $client = new Client();
        $data   = $client->request('GET', $url, [
                'headers' => [
                'X-API-KEY' => '222'
                ]
            ]);

        $res = json_decode($data->getBody());

        return response()->json($res);
    }

    public function index(){

        $bank = bank_indonesia_m::all();
        $kota = kota_indonesia_m::all();
        // if(!$res){
        //     return redirect('/home');
        // }
        return view('page.member.upgrade_member', compact('bank','kota'));
    }

    // Get Product
    public function getAllUpgradeMember(Request $request){
        ini_set('memory_limit', '512M');
        $columns = array(
            0 => 'B.status',
            1 => 'B.date_request',
            2 => 'A.mbr_code',
            3 => 'A.mbr_kota',
            4 => 'A.rekening',
            5 => 'B.selfie'
        );


        // Total Data
        $data = DB::table('mbr_list')->rightJoin('mbr_upgrade_verification','mbr_list.mbr_code','=','mbr_upgrade_verification.mbr_code')->where('mbr_upgrade_verification.status','=','Waiting')->get();
        $totaldata = count($data);
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->column)){
            $posts = DB::table('mbr_list as a')
                    ->rightJoin('mbr_upgrade_verification as b','a.mbr_code','=','b.mbr_code')
                    ->where('b.status','=','Waiting')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('b.date_request', $dir)
                    ->get();

            $totalFiltered = $totaldata;
        } else {
            $kolom_search = $request->column;
            $kolom_value = $request->value;

                $posts = DB::table('mbr_list as a')
                        ->rightJoin('mbr_upgrade_verification as b','a.mbr_code','=','b.mbr_code')
                        ->where('b.status','=','Waiting')
                        ->where('a.'.$kolom_search, 'ilike', "%{$kolom_value}%")
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('b.date_request', $dir)
                        ->get();
                $totaldata = count($posts);
                $totalFiltered = $totaldata;

        }

          $data = array();

          if($posts){
              foreach($posts as $row){

                if($row->status == 'Waiting'){
                    $sts = '<span class="label label-warning">'.$row->status.'</span>';
                } else {
                    $sts = '<span class="label label-danger">'.$row->status.'</span>';
                }

                $kota = kota_indonesia_m::where('id_kota', $row->kota)->first();

                $nestedData['action'] = '<button class="btn btn-success btn-sm" onClick="approveMember(\''.$row->mbr_code.'\')"><i class="mdi mr-2 mdi-check"></i>Approve</button>
                                        <button class="btn btn-danger btn-sm" onClick="rejectMember(\''.$row->mbr_code.'\')"><i class="mdi mr-2 mdi-close"></i>Reject</button>';

                $nestedData['tanggal'] = '<span style="font-weight:bold" class="mr-2">Request :</span>'.$row->date_request.'<br>
                                        <span style="font-weight:bold" class="mr-2">Action :</span>'.$row->date_approve.'<br>
                                        <span style="font-weight:bold" class="mr-2">Status :</span>'.$sts;

                $nestedData['datamember'] =   '<span style="font-weight:bold" class="mr-2">Nama :</span>'.$row->mbr_name.'['.$row->mbr_code.']<br>
                                        <span style="font-weight:bold" class="mr-2">Tipe :</span>REGULER<br>
                                        <span style="font-weight:bold" class="mr-2">Telepon :</span>'.$row->mbr_mobile.'<br>
                                        <span style="font-weight:bold" class="mr-2">'.$row->tipe_identitas.' :</span>'.$row->no_ktp;

                $nestedData['detail'] =    '<span style="font-weight:bold" class="mr-2">Info Lahir :</span>'.$row->tempat_lahir.','.date_format(date_create($row->tgl_lahir),"d M Y").'<br>
                                            <span style="font-weight:bold" class="mr-2">Alamat :</span>'.$row->alamat.'<br>
                                            <span style="font-weight:bold" class="mr-2">Kota :</span>'.$kota->kota.'<br>';

                $nestedData['rekening'] =   '<span style="font-weight:bold" class="mr-2">Bank :</span>'.$row->rekening.'<br>
                                        <span style="font-weight:bold" class="mr-2">Nomor :</span>'.$row->norec.'<br>
                                        <span style="font-weight:bold" class="mr-2">Pemilik :</span>'.$row->nm_pemilik.'<br>';

                $nestedData['image'] = '<button class="btn btn-info btn-sm"  data-toggle="modal" onClick="showBanner(\'' . $row->selfie . '\')" data-target="#UpdateYayasan"><i class="mdi mr-2 mdi-eye"></i>Selfie</button>
                                        <button class="btn btn-info btn-sm" onClick="showBanner(\'' . $row->identity_id . '\')" id="hapus"><i class="mdi mr-2 mdi-eye"></i>Identitas</button>';

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

    // Add Upgrade Member
    function addUpgradeMember(Request $request){

        $mbr_code   = $request->mbr_code;
        $tempat     = $request->tempat_lahir;
        $tgl        = $request->tgl_lahir;
        $alamat     = $request->alamat;
        $kota       = $request->kota;
        $tipe_id    = $request->tipe_identitas;
        $nomer_ktp  = $request->nomer_identitas;
        $nama_bank  = $request->nama_bank;
        $norek      = $request->nomer_rekening;
        $nama_an    = $request->nama_rekening;

        $mbr = mbr_list_m::where('mbr_code', $mbr_code)->first();
        $mbrup = mbr_upgrade_verification_m::where('mbr_code', $mbr_code)->first();
        $ktp = mbr_list_m::where('mbr_id_number', $nomer_ktp)->first();

        if(!empty($nama_bank)){
            $bank = bank_indonesia_m::where('id', $nama_bank)->first()->name;
        }
        else{
            $bank = '';
        }

        // Cek mbr_list
        if(!empty($mbr)){
            // Cek KTP
            if(empty($ktp)){
                //Cek mbr_upgrade
                if(empty($mbrup)){

                    // Create
                    DB::beginTransaction();
                    try{

                        $upgrade = new mbr_upgrade_verification_m;

                        $upgrade->mbr_code      = $mbr_code;
                        $upgrade->identity_id   = "Test";
                        $upgrade->selfie        = "Test";
                        $upgrade->tempat_lahir  = $tempat;
                        $upgrade->tgl_lahir     = $tgl;
                        $upgrade->alamat        = $alamat;
                        $upgrade->kota          = $kota;
                        $upgrade->tipe_identitas= $tipe_id;
                        $upgrade->no_ktp        = $nomer_ktp;
                        $upgrade->rekening      = $bank;
                        $upgrade->norec         = $norek;
                        $upgrade->nm_pemilik    = $nama_an;
                        $upgrade->status        = 'Waiting';
                        $upgrade->date_request  = date("Y-m-d H:i:s");
                        $upgrade->save();

                        DB::commit();

                        return response()->json([
                            "respStatus" => TRUE,
                            "respMessage" => "Data Berhasil Disimpan"
                        ]);

                    }catch(\exception $e){
                        DB::rollback();

                        return response()->json([
                            "respStatus" => FALSE,
                            "respMessage" => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode :".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
                        ]);
                    }

                } else {

                    // Update
                    DB::beginTransaction();
                    try{

                        mbr_upgrade_verification_m::where('mbr_code', $mbr_code)
                            ->update([
                                // 'opr_approve'   =>  '',
                                // 'date_approve'  =>  '',
                                'identity_id'   =>  'Test',
                                'selfie'        =>  'Test',
                                'date_request'  =>  date("Y-m-d H:i:s"),
                                'tempat_lahir'  =>  $tempat,
                                'tgl_lahir'     =>  $tgl,
                                'alamat'        =>  $alamat,
                                'kota'          =>  $kota,
                                'tipe_identitas'=>  $tipe_id,
                                'no_ktp'        =>  $nomer_ktp,
                                'rekening'      =>  $bank->name,
                                'norec'         =>  $norek,
                                'nm_pemilik'    =>  $nama_an,
                                'status'        => 'Waiting',
                            ]);

                        mbr_list_m::where('mbr_code', $mbr_code)
                            ->update([
                                'type_keanggotan'   => 0,
                            ]);

                        DB::commit();

                        return json_encode([
                            "respStatus"   => TRUE,
                            "respMessage" => "Data Berhasil disimpan"
                        ]);

                    }catch(\exception $e){
                        DB::rollBack();

                        return json_encode([
                            "respStatus"   => FALSE,
                            "respMessage" => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
                        ]);

                    }
                }
            } else {

                return json_encode([
                    "respStatus" => FALSE,
                    "respMessage" => "Nomer Identitas sudah terdaftar"
                ]);
            }
        } else {

            return json_encode([
                "respStatus" => FALSE,
                "respMessage" => "EKL Tidak terdaftar"
            ]);
        }

    }

    // Approve Member
    function approveMember(Request $request){

        // Request
        $mbr_code = $request->mbr_code;

        // Check
        $dbupgrade = mbr_upgrade_verification_m::where('mbr_code', '=', $mbr_code)->first();
        $dbkota    = kota_indonesia_m::where('id_kota', '=', $dbupgrade->kota)->first();

        if (strlen($dbupgrade->no_ktp) == 12)
            $tipe_id = 'SIM';
        else $tipe_id = 'KTP';


        DB::beginTransaction();
        try{

            // Update mbr_list
            mbr_list_m::where('mbr_code', '=', $mbr_code)
                ->update([
                    'mbr_dob'           => $dbupgrade->tgl_lahir,
                    'mbr_address'       => strtoupper($dbupgrade->alamat),
                    'mbr_kota'          => $dbkota->kota,
                    'mbr_province'      => $dbkota->provinsi,
                    'mbr_id_type'       => $tipe_id,
                    'mbr_id_number'     => $dbupgrade->no_ktp,
                    'mbr_bank_num'      => $dbupgrade->norec,
                    'mbr_bank_acc'      => strtoupper($dbupgrade->nm_pemilik),
                    'mbr_bank_name'     => $dbupgrade->rekening,
                    'type_keanggotan'   => 1,
                ]);

            // mbr_saldo
            mbr_saldo_m::where('mbr_code', $mbr_code)
                ->update([
                    'max_balance'       =>  10000000,
                    'max_transaction'   =>  5000000,
                    'max_transfer'      =>  1000000,
                ]);

            $delete = mbr_upgrade_verification_m::where('mbr_code', '=', $mbr_code)->first();
            $delete->delete();

            DB::commit();

            return response()->json([
                "respStatus"   => TRUE,
                "respMessage" => "Data Berhasil disimpan"
            ]);

        }catch(\exception $e){
            DB::rollBack();

            return response()->json([
                "respStatus"   => FALSE,
                "respMessage" => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
            ]);
        }

    }

    function rejectMember(Request $request){

        $mbr_code = $request->mbr_code;
        $alasan   = $request->alasan;


        DB::beginTransaction();
        try{

            mbr_upgrade_verification_m::where('mbr_code', $mbr_code)
                ->update([
                    'opr_approve'   => '',
                    'date_approve'  => date("Y-m-d H:i:s"),
                    'status'        => 'Reject',
                    'keterangan'    => $alasan
                ]);

            mbr_list_m::where('mbr_code', $mbr_code)
                ->update([
                    'type_keanggotan'   => 0,
                ]);

            DB::commit();

            return json_encode([
                "respStatus"   => TRUE,
                "respMessage" => "Data Berhasil disimpan"
            ]);

        }catch(\exception $e){
            DB::rollBack();

            return json_encode([
                "respStatus"   => FALSE,
                "respMessage" => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
            ]);

        }

    }

}
