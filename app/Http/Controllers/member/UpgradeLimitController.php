<?php

namespace App\Http\Controllers\member;

use App\Http\Models\upgrade_limit_m;
use App\Repositories\HelperInterface as Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Session;
use DB;

class UpgradeLimitController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.member.upgrade_limit');
    }

    function getDataTable(Request $request){
        $columns = array(
            0 =>'mbr_code',
            1 => 'date_request',
            2 => 'mbr_code',
            3 => 'max_transaction',
            4 => 'status',
        );


        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (!$request->kata && !$request->search_start){
            if(!$request->kata){
                if($request->target == ""){
                    $data_search = DB::table('log_upgrade_limit')->select('log_upgrade_limit.*', 'mbr_list.mbr_name', 'mbr_list.mbr_mobile','mbr_list.mbr_id_number' )
                            ->join('mbr_list', 'log_upgrade_limit.mbr_code', '=',  'mbr_list.mbr_code' )
                            ->orderBy('log_upgrade_limit.'.$order, $dir);
                }else{
                    $data_search = DB::table('log_upgrade_limit')->select('log_upgrade_limit.*', 'mbr_list.mbr_name', 'mbr_list.mbr_mobile','mbr_list.mbr_id_number' )
                            ->where('log_upgrade_limit.status', '=',  "$request->target")
                            ->join('mbr_list', 'log_upgrade_limit.mbr_code', '=',  'mbr_list.mbr_code' )
                            ->orderBy('log_upgrade_limit.'.$order, $dir);
                }
                $totaldata = count($data_search->get());
                $posts = $data_search
                            ->offset($start)
                            ->limit($limit)
                            ->get();
                $totalFiltered = $totaldata;
            }else{
                $posts = DB::table('log_upgrade_limit')->select('log_upgrade_limit.*', 'mbr_list.mbr_name', 'mbr_list.mbr_mobile','mbr_list.mbr_id_number' )
                    ->join('mbr_list', 'log_upgrade_limit.mbr_code', '=', 'mbr_list.mbr_code' )
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy('log_upgrade_limit.'.$order, $dir)
                        ->get();
                $totaldata = upgrade_limit_m::count();
                $totalFiltered = $totaldata;
            }
        } else {
                $starttime = strtotime($request->search_start.' 00:00:00');
                $end = strtotime($request->search_end.' 23:59:59');
            if(!$request->kata){
                $date_start = DATE("Y-m-d H:i:s", $starttime);
                $date_end = DATE("Y-m-d H:i:s", $end);
                if($request->target == ""){
                    $data_search = DB::table('log_upgrade_limit')->select('log_upgrade_limit.*', 'mbr_list.mbr_name', 'mbr_list.mbr_mobile','mbr_list.mbr_id_number' )
                                ->whereBetween('log_upgrade_limit.date_request', [$date_start, $date_end])
                                ->join('mbr_list', 'log_upgrade_limit.mbr_code', '=', 'mbr_list.mbr_code' )
                                ->orderBy('log_upgrade_limit.'.$order, $dir);
                }else{
                    $data_search = DB::table('log_upgrade_limit')->select('log_upgrade_limit.*', 'mbr_list.mbr_name', 'mbr_list.mbr_mobile','mbr_list.mbr_id_number' )
                                ->where('log_upgrade_limit.status' ,'ilike', "$request->target")
                                ->whereBetween('log_upgrade_limit.date_request', [$date_start, $date_end])
                                ->join('mbr_list', 'log_upgrade_limit.mbr_code', '=', 'mbr_list.mbr_code' )
                                ->orderBy('log_upgrade_limit.'.$order, $dir);
                }
            }else{
                if($request->target == ""){
                    $data_search = DB::table('log_upgrade_limit')->select('log_upgrade_limit.*', 'mbr_list.mbr_name', 'mbr_list.mbr_mobile','mbr_list.mbr_id_number' )
                                ->whereRaw('log_upgrade_limit.mbr_code = '."'".strtoupper($request->kata)."'")
                                ->join('mbr_list', 'mbr_list.mbr_code', '=', 'log_upgrade_limit.mbr_code')
                                ->orderBy('log_upgrade_limit.'.$order, $dir);
                }else{
                    $data_search = DB::table('log_upgrade_limit')->select('log_upgrade_limit.*', 'mbr_list.mbr_name', 'mbr_list.mbr_mobile','mbr_list.mbr_id_number' )
                                ->whereRaw('log_upgrade_limit.mbr_code = '."'".strtoupper($request->kata)."'".' and log_upgrade_limit.status ='."'".$request->target."'")
                                ->join('mbr_list', 'mbr_list.mbr_code', '=', 'log_upgrade_limit.mbr_code')
                                ->orderBy('log_upgrade_limit.'.$order, $dir);
                }
            }
            $totaldata = count($data_search->get());
            $posts = $data_search
                        ->offset($start)
                        ->limit($limit)
                        ->get();
            $totalFiltered = $totaldata;
        }


        $data = array();
        if($posts){
            foreach($posts as $row){

                if ($row->max_balance != 0){ 
                    $pengajuan = '<span style="font-weight:bold" class="mr-2">Saldo :</span>'.number_format($row->max_balance);
                    $pengajuanColom = '<b>Saldo :</b>'.number_format($row->max_balance);
                }else if($row->max_transaction != 0) {
                    $pengajuan = '<span style="font-weight:bold" class="mr-2">Transaksi :</span>'.number_format($row->max_transaction);
                    $pengajuanColom = '<b>Transaksi :</b>'.number_format($row->max_transaction);
                }else if($row->max_transfer != 0)  {
                    $pengajuan = '<span style="font-weight:bold" class="mr-2">Transfer :</span>'.number_format($row->max_transfer);
                    $pengajuanColom = '<b>Transfer :</b>'.number_format($row->max_transfer);
                };

                $exclude = array("<b>" => "", "</b>" => "");

                if ($row->status == 0) {
                    $sts = '<font class=text-warning>Waiting</font>';
                    $date_execute = '-';
                    $opr_execute = "-";
                    $btn = "<button class='btn btn-warning btn-sm' onClick=\"Approve(' $row->mbr_code ',' $row->mbr_name ','".strtr($pengajuanColom, $exclude)."','$row->id')\"><i class='mdi mr-2 mdi-settings'></i>Approve</button>
                    <button class='btn ml-3 btn-danger btn-sm' onClick=\"Reject(' $row->mbr_code ',' $row->mbr_name ','".strtr($pengajuanColom, $exclude)."','$row->id')\" id='hapus'><i class='mdi mr-2 mdi-close'></i>Reject</button>";
                } else if ($row->status == 1) {
                    $sts = '<font class=text-success>Approve</font>';
                    $date_execute = date_format(date_create($row->date_execute),"d M Y H:i");
                    $opr_execute = $row->opr_execute;
                    $btn = "";
                } else if ($row->status == 2) {
                    $sts = '<font class=text-danger>Reject</font>';
                    $date_execute = date_format(date_create($row->date_execute),"d M Y H:i");
                    $opr_execute = $row->opr_execute;
                    $btn = "";
                } else {
                    $sts = '-';
                    $date_execute = '-';
                    $opr_execute = "-";
                    $btn = "";
                }

              $nestedData['action'] = $btn;
              $nestedData['tanggal'] = '<span style="font-weight:bold" class="mr-2">Request :</span>'.date_format(date_create($row->date_request),"d M Y H:i").'<br>
                                      <span style="font-weight:bold" class="mr-2">Approve :</span>'.$date_execute.'<br>';

              $nestedData['data'] =   '<span style="font-weight:bold" class="mr-2">Nama :</span>'.$row->mbr_name.' ['.$row->mbr_code.']<br>
                                      <span style="font-weight:bold" class="mr-2">Telepon :</span>'.$row->mbr_mobile.'<br>
                                      <span style="font-weight:bold" class="mr-2">KTP :</span>'.$row->mbr_id_number.'<br>';

              $nestedData['pengajuan'] =   $pengajuan.'<br/>
                                            <span style="font-weight:bold" class="mr-2">Alasan :</span>'.$row->reason.'<br>';

              $nestedData['keterangan'] =  '<span style="font-weight:bold" class="mr-2">Status :</span>'.$sts.'<br>
                                      <span style="font-weight:bold" class="mr-2">Operator :</span>'.$opr_execute.'<br>';


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

    function Approve(Request $request){

        $date = DATE("Y/m/d H:i:s");
        $user = Session::get('dataUser')->nama;
        $desc = 'Di approve oleh :'. Session::get('dataUser')->nama;
        $pengajuan = $request->pengajuan;

                        
        if (strpos($pengajuan, 'Saldo') !== false) $kolom = 'max_balance';
        else if (strpos($pengajuan, 'Transaksi') !== false) $kolom = 'max_transaction';
        else if (strpos($pengajuan, 'Transfer') !== false) $kolom = 'max_transfer';

        $saldo = DB::table('mbr_saldo')->where('mbr_code',$request->mbr_code)->first();
        $dblog = DB::table('log_upgrade_limit')->where('id',$request->id)->first();
        



        if ($saldo->$kolom >= $dblog->$kolom){
            echo json_encode(array('status' => FALSE, 'message' => 'Jumlah limit saat ini sudah melebihi/sama dibandingkan dengan nilai pengajuan'));
        } else{

            // Ubah Status Upgrade
            DB::table('log_upgrade_limit')->where('id', $request->id)->update(['status' => '1', 'date_execute' => $date, 'opr_execute' =>$user, 'desc_execute'=> $desc]);

            // Pindah Saldo dari pengajuan ke tabel saldo
            DB::table('mbr_saldo')->where('mbr_code', $request->mbr_code)->update([$kolom => $dblog->$kolom]);
            $this->helper->logAction(2, $request->mbr_code, 'approve');

            return json_encode(array(
                'status' => TRUE, 'message' => 'Proses approve berhasil!'
            ));
        }
    }
    function addUpgrade(Request $request){
        date_default_timezone_set("Asia/Jakarta");
        $validatedData = Validator::make($request->all(),[
            'mbr_code' => 'required',
            'alasan' => 'required'
        ]);
        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $cek_ekl = DB::table('mbr_list')->where('mbr_code', $request->mbr_code)->first();
            if($cek_ekl){
                if($cek_ekl->type_keanggotan == 1){
                    $res = DB::table('log_upgrade_limit')->insert([
                        'mbr_code' => $request->mbr_code,
                        'max_balance' => $request->max_balance,
                        'max_transaction' => $request->max_transaction,
                        'max_transfer' => $request->max_transfer,
                        'reason' => $request->alasan,
                        'status' => 0,
                        'date_request' =>  DATE("Y/m/d H:i:s")
                    ]);
                    if($res){
                        $this->helper->logAction(2,  $request->mbr_code, 'insert');
                        return json_encode(array(
                            'status' => TRUE, 'message' => 'Data Telah Ditambah!'
                        ));
                    }else{
                        return json_encode(array(
                            'status' => FALSE, 'message' => 'Data Gagal Ditambah!'
                        ));
                    }
                }else{
                    return json_encode(array(
                        'status' => FALSE, 'message' => 'ID EKL yang belum melakukan upgrade member!'
                    ));
                }
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'ID EKL tidak terdaftar di dalam database!'
                ));
            }
            
        } 
    }

    function Reject(Request $request){

        $date = DATE("Y/m/d H:i:s");
        $user = Session::get('dataUser')->nama;
        $desc = 'Di reject oleh :'. Session::get('dataUser')->nama;
        

        DB::table('log_upgrade_limit')->where('id', $request->id)->update(['status' => '2', 'date_execute' => $date, 'opr_execute' =>$user, 'desc_execute'=> $desc]);

        $this->helper->logAction(2, $request->mbr_code, 'reject');

        return json_encode(array(
            'status' => TRUE, 'message' => 'Proses perubahan data berhasil!'
        ));

    }
}