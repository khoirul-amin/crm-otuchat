<?php

namespace App\Http\Controllers\transaksi;

use Illuminate\Http\Request;
use DB;
use Session;

use App\Http\Models\deposit_mv;
use App\Http\Models\deposit_m;
use App\Http\Models\notif_transaksi_m;
use App\Http\Models\mbr_list_m;

use App\Repositories\HelperInterface as Helper;

class ListDepositApprovalController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.transaksi.list_deposit_approval');
    }

    public function getListDepositApproval(Request $request){
        // print_r($request->all());
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $custom_search = $request->input('custom_search');
        // Columns
        $columns = array(
            0 => 'deposit_date',
            1 => 'mbr_code',
            2 => 'mbr_name',
            3 => 'jumlah_topup',
            4 => 'deposit_bank',
            5 => 'deposit_code',
            //Only in controller
            6 => 'deposit_id'
        );

        $order_columns = array(
            0 => 'deposit_id',
            1 => 'deposit_date',
            2 => 'mbr_code',
            3 => 'mbr_name',
            4 => 'deposit_amount',
            5 => 'deposit_bank',
            6 => 'deposit_code'
        );

        // Total Data
        $totaldata = deposit_mv::where('deposit_status', '=', 'Waiting')->count();

        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $order_columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');
        
        // if filtered search
        if($custom_search == 'true'){
            $where_start = $start_date . ' 00:00:00';
            $where_end = $end_date . ' 23:59:59';
            if(empty($request->input('search.value'))){
                $data_search = deposit_mv::select(DB::raw("to_char(".$columns[0].", 'YYYY-MM-DD hh:mm:ss') as deposit_date"), $columns[1], $columns[2], DB::raw("deposit_amount + codeunix AS jumlah_topup"), $columns[4], $columns[5], $columns[6])
                        // ->whereRaw("'[" . $start_date . ", " . $end_date . "]'::tsrange @> deposit_date")
                        ->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'")
                        ->where('deposit_status', '=', 'Waiting')
                        ->orderBy($order, $dir);
                $totalFiltered = count($data_search->get());
                $posts = $data_search
                        ->offset($start)
                        ->limit($limit)
                        ->get();
            }
            else{
                $search = $request->input('search.value');
                $data_search = deposit_mv::select(DB::raw("to_char(".$columns[0].", 'YYYY-MM-DD hh:mm:ss') as deposit_date"), $columns[1], $columns[2], DB::raw("deposit_amount + codeunix AS jumlah_topup"), $columns[4], $columns[5], $columns[6])
                        // ->whereRaw("'[" . $start_date . ", " . $end_date . "]'::tsrange @> deposit_date")
                        ->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'")
                        ->where('deposit_status', '=', 'Waiting')
                        ->where(function($query) use ($columns, $search)
                        {
                            $query->orWhere($columns[0], 'ilike', "%{$search}%")
                            ->orWhere($columns[1], 'ilike', "%{$search}%")
                            ->orWhere($columns[2], 'ilike', "%{$search}%")
                            ->orWhere(DB::raw('CAST(deposit_amount + codeunix AS TEXT)'), 'ilike', "%{$search}%")
                            ->orWhere($columns[4], 'ilike', "%{$search}%")
                            ->orWhere($columns[5], 'ilike', "%{$search}%");
                        })
                        ->orderBy($order, $dir);
                $totalFiltered = count($data_search->get());
                $posts = $data_search
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }
        }
        else if (empty($request->input('search.value'))){
            $data = deposit_mv::select(DB::raw("to_char(".$columns[0].", 'YYYY-MM-DD hh:mm:ss') as deposit_date"), $columns[1], $columns[2], DB::raw("deposit_amount + codeunix AS jumlah_topup"), $columns[4], $columns[5], $columns[6])
                    ->where('deposit_status', '=', 'Waiting')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir);
            $posts = $data->get();
            $totalFiltered = $totaldata;
        } else {
            $search = $request->input('search.value');
            $data_search = deposit_mv::select(DB::raw("to_char(".$columns[0].", 'YYYY-MM-DD hh:mm:ss') as deposit_date"), $columns[1], $columns[2], DB::raw("deposit_amount + codeunix AS jumlah_topup"), $columns[4], $columns[5], $columns[6])
                ->where('deposit_status', '=', 'Waiting')
                ->where(function($query) use ($columns, $search)
                {
                    $query->orWhere($columns[0], 'ilike', "%{$search}%")
                    ->orWhere($columns[1], 'ilike', "%{$search}%")
                    ->orWhere($columns[2], 'ilike', "%{$search}%")
                    ->orWhere(DB::raw('CAST(deposit_amount + codeunix AS TEXT)'), 'ilike', "%{$search}%")
                    ->orWhere($columns[4], 'ilike', "%{$search}%")
                    ->orWhere($columns[5], 'ilike', "%{$search}%");
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
                $nestedData['action'] = '<button class="btn mt-2 btn-success btn-sm" onClick="approveDeposit(\'' . $row->deposit_id . '\')"><i class="mdi mr-2 mdi-check"></i>Approve</button>
                <button class="btn mt-2 btn-danger btn-sm" onClick="rejectDeposit(\'' . $row->deposit_id . '\')"><i class="mdi mr-2 mdi-close"></i>Reject</button>';
                $nestedData['deposit_date'] = $row->deposit_date;
                $nestedData['mbr_code'] = $row->mbr_code;
                $nestedData['mbr_name'] = $row->mbr_name;
                $nestedData['jumlah_topup'] = $this->convertCurrency($row->jumlah_topup);
                $nestedData['deposit_bank'] = $row->deposit_bank;
                $nestedData['deposit_code'] = $row->deposit_code;
                $nestedData['deposit_status'] = '<span class="text-success">' . $row->deposit_status . '</span>';
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

    // Conver integer to rupiah
    function convertCurrency($n) {
        return 'Rp ' . number_format($n,0,',','.');
    }

    function approveDeposit($deposit_id){
        try{
            DB::beginTransaction();

            $select = deposit_m::where('deposit_id', '=', $deposit_id)->first();

            // CEK STATUS MEMBER
            $cekStatus = mbr_list_m::where('mbr_code', '=', $select->mbr_code)->first();
            if($cekStatus->mbr_status != 'Active'){
                return response()->json(array("status" => "failed", "reason" => "Akun member dapat status Block/Inactive, Mohon periksa kembali!"));
            }

            $result_function = DB::statement('SELECT f_update_deposit(?, ?, ?, ?, ?, ?, ?)', [
                    date("Y-m-d H:i:s"),
                    $select->mbr_code,
                    $select->deposit_amount,
                    $select->codeunix,
                    Session::get('dataUser')->nama . ' (' . Session::get('dataUser')->nama_role . ')',
                    $deposit_id,
                    $select->deposit_bank
                ]);

            if(!$result_function){
                return response()->json(array("status" => "failed", "reason" => "Kesalahan sistem!"));
            }

            DB::commit();
            $this->helper->logAction(5, $select->mbr_code, 'approve');
            return response()->json(array("status" => "success"));
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(array("status" => "failed", "reason" => "Kesalahan sistem!"));
        }
    }

    function rejectDeposit($deposit_id){
        try {
            DB::beginTransaction();

            $select = deposit_m::where('deposit_id', '=', $deposit_id)->first();

            $update = deposit_m::where('deposit_id', '=', $deposit_id)
                    ->where('deposit_status', '=', 'Waiting')
                    ->update([
                        'deposit_status' => 'Gagal',
                        'approve_date' => date("Y-m-d H:i:s"),
                        'opr' => Session::get('dataUser')->nama . ' (' . Session::get('dataUser')->nama_role . ')'
                    ]);
            
            $insert = new notif_transaksi_m;
            $insert->mbr_code = $select->mbr_code;
            $insert->tpl_code = 'RDEP';
            $insert->notif_date = date("Y-m-d H:i:s");
            $insert->notif_amount = (int) $select->deposit_amount;
            $insert->notif_status = 'Waiting';
            $insert->tujuan = '';
            $insert->invoice = 'DEPOSIT EXPIRED';
            $insert->device = 'EKLANKU MAX';
            $insert->sts_hp = 0;
            
            DB::commit();
            $this->helper->logAction(5, $select->mbr_code, 'reject');
            return response()->json(array("status" => "success"));
        } catch(Exception $e) {
            DB::rollback();
            return response()->json(array("status" => "failed", "reason" => "Kesalahan sistem!"));
        }
    }

}
