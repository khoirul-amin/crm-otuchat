<?php

namespace App\Http\Controllers\laporan;

use Illuminate\Http\Request;

use App\Repositories\HelperInterface as Helper;

use DB;

class RefundDanaSaldoController{

    function index(){
        return view('page.laporan.refund_dana_saldo');
    }

    function getDataRefund(Request $request){
        $columns = array(
            0 => 'penarikan_date',
            1 => 'mbr_code',
            2 => 'mbr_name',
            3 => 'penarikan_amount',
            4 => 'opr',
            5 => 'atas_nama',
            6 => 'penarikan_code'
        );
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (!$request->target_search && !$request->tgl_search){
            $posts = DB::table('vw_penarikan_all')
                    ->where('bank', 'like', '%REFUND%')
                    ->offset($start) 
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            $totaldata = DB::table('vw_penarikan_all')->where('bank', 'like', '%REFUND%')->count();
            $totalFiltered = $totaldata;
        } else {
            if(!$request->tgl_search){
                $data_search = DB::table('vw_penarikan_all')
                    ->whereRaw("".$request->target_search."='".$request->keyword."' and bank like '%REFUND%'")
                    ->orderBy($order, $dir);

                $totaldata = count($data_search->get());

                $posts = $data_search
                            ->offset($start)
                            ->limit($limit)
                            ->get();
                $totalFiltered = $totaldata;
            }else{
                $date_start = DATE("Y-m-d H:i:s", strtotime($request->tgl_search.' 00:00:00'));
                $date_end = DATE("Y-m-d H:i:s", strtotime($request->tgl_search.' 23:59:59'));

                $data_search = DB::table('vw_penarikan_all')
                    ->whereRaw("penarikan_date between'".$date_start."' and '".$date_end."' and bank like '%REFUND%'")
                    ->orderBy($order, $dir);

                $totaldata = count($data_search->get());

                $posts = $data_search
                            ->offset($start)
                            ->limit($limit)
                            ->get();
                $totalFiltered = $totaldata;
            }
        }


        $data = array();

        if($posts){
            foreach($posts as $res){
                $row = array();
                    $row[] = date_format(date_create($res->penarikan_date),"d M Y H:i");
                    $row[] =  $res->mbr_code;
                    $row[] = $res->mbr_name;
                    $row[] = number_format($res->penarikan_amount);
                    $row[] = $res->opr;
                    $row[] = $res->atas_nama;
                    $row[] = $res->penarikan_code;
              
              $data[] = $row;
            }
        }


         $output = array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => intval($totaldata),
            "recordsFiltered"   => intval($totalFiltered),
            "data"              => $data,
        );
        echo json_encode($output);
    }
}

