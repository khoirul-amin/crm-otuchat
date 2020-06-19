<?php

namespace App\Http\Controllers\transaksi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Repositories\HelperInterface as Helper;
use DB;

// Model
use App\Http\Models\deposit_m;
use App\Http\Models\bank_m;

class ListDepositController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){

        $bank = bank_m::all();
        return view('page.transaksi.list_deposit', compact('bank'));
    }

    // Get Product
    public function getListDeposit(Request $request){
        ini_set('memory_limit', '4096M');
        $columns = array(
            0 =>'a.deposit_date',
            1 =>'b.mbr_name',
            2 =>'a.deposit_amount',
            3 =>'a.deposit_code',
            4 =>'a.deposit_status',
        );

        $totaldata = deposit_m::count();

        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        // dd($request->all());

        if (empty($request->column)){
            // semua
            $kolom_search = $request->column;
            $kolom_value = $request->value;

            $posts = DB::table('deposit as a')
                    ->leftJoin('mbr_list as b','a.mbr_code','b.mbr_code')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            $totalFiltered = $totaldata;

            $totaldeposit = DB::table('deposit as a')
                    ->select(DB::raw('sum(a.deposit_amount) as total'))
                    ->leftJoin('mbr_list as b','a.mbr_code','b.mbr_code')
                    ->get();
        } else {
            $kolom_search = $request->column;
            $kolom_value = $request->value;
            $status = $request->status;

            if($request->start_date != '' && $request->end_date != ''){
                $where_start = $request->start_date . ' 00:00:00';
                $where_end = $request->end_date . ' 23:59:59';
            }

            if ($kolom_search == "deposit_bank"){

                if ($status == ""){

                    $posts = DB::table('deposit as a')
                    ->leftJoin('mbr_list as b','a.mbr_code','=','b.mbr_code');
                    if($request->start_date != '' && $request->end_date != ''){
                        $posts = $posts->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'");
                    }
                    $posts = $posts->where('a.'.$kolom_search, '=', $request->bank)
                    ->orderBy($order, $dir);
                    $totaldata = count($posts->get());
                    $totalFiltered = $totaldata;

                    $posts = $posts->offset($start)
                    ->limit($limit)
                    ->get();

                    $totaldeposit = DB::table('deposit as a')
                    ->select(DB::raw('sum(a.deposit_amount) as total'))
                    ->leftJoin('mbr_list as b','a.mbr_code','b.mbr_code');
                    if($request->start_date != '' && $request->end_date != ''){
                        $totaldeposit = $totaldeposit->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'");
                    }
                    $totaldeposit = $totaldeposit->where('a.'.$kolom_search, '=', $request->bank)
                    ->get();

                }else{
                    $posts = DB::table('deposit as a')
                    ->leftJoin('mbr_list as b','a.mbr_code','=','b.mbr_code');
                    if($request->start_date != '' && $request->end_date != ''){
                        $posts = $posts->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'");
                    }
                    $posts = $posts->where('a.'.$kolom_search, '=', $request->bank)
                    ->where('a.deposit_status', '=', $status)
                    ->orderBy($order, $dir);
                    $totaldata = count($posts->get());
                    $totalFiltered = $totaldata;

                    $posts = $posts->offset($start)
                    ->limit($limit)
                    ->get();

                    $totaldeposit = DB::table('deposit as a')
                    ->select(DB::raw('sum(a.deposit_amount) as total'))
                    ->leftJoin('mbr_list as b','a.mbr_code','b.mbr_code');
                    if($request->start_date != '' && $request->end_date != ''){
                        $totaldeposit = $totaldeposit->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'");
                    }
                    $totaldeposit = $totaldeposit->where('a.'.$kolom_search, '=', $request->bank)
                    ->where('a.deposit_status', '=', $status)
                    ->get();
                }

            } else {

                if ($status == ""){

                    $posts = DB::table('deposit as a')
                    ->leftJoin('mbr_list as b','a.mbr_code','=','b.mbr_code');
                    if($request->start_date != '' && $request->end_date != ''){
                        $posts = $posts->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'");
                    }
                    $posts = $posts->where('b.'.$kolom_search, 'ilike', "%{$kolom_value}%")
                    ->orderBy($order, $dir);
                    $totaldata = count($posts->get());
                    $totalFiltered = $totaldata;

                    $posts = $posts->offset($start)
                    ->limit($limit)
                    ->get();

                    $totaldeposit = DB::table('deposit as a')
                    ->select(DB::raw('sum(a.deposit_amount) as total'))
                    ->leftJoin('mbr_list as b','a.mbr_code','b.mbr_code');
                    if($request->start_date != '' && $request->end_date != ''){
                        $totaldeposit = $totaldeposit->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'");
                    }
                    $totaldeposit = $totaldeposit->where('b.'.$kolom_search, 'ilike', "%{$kolom_value}%")
                    ->get();
                }
                else{
                    $posts = DB::table('deposit as a')
                    ->leftJoin('mbr_list as b','a.mbr_code','=','b.mbr_code');
                    if($request->start_date != '' && $request->end_date != ''){
                        $posts = $posts->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'");
                    }
                    $posts = $posts->where('b.'.$kolom_search, 'ilike', "%{$kolom_value}%")
                    ->where('a.deposit_status', '=', $status)
                    ->orderBy($order, $dir);
                    $totaldata = count($posts->get());
                    $totalFiltered = $totaldata;

                    $posts = $posts->offset($start)
                    ->limit($limit)
                    ->get();

                    $totaldeposit = DB::table('deposit as a')
                    ->select(DB::raw('sum(a.deposit_amount) as total'))
                    ->leftJoin('mbr_list as b','a.mbr_code','b.mbr_code');
                    if($request->start_date != '' && $request->end_date != ''){
                        $totaldeposit = $totaldeposit->whereRaw("deposit_date >= '" . $where_start . "' AND deposit_date <= '" . $where_end . "'");
                    }
                    $totaldeposit = $totaldeposit->where('b.'.$kolom_search, 'ilike', "%{$kolom_value}%")
                    ->where('a.deposit_status', '=', $status)
                    ->get();
                }

            }

        }

          $data = array();

          if($posts){
              foreach($posts as $row){
                if($row->deposit_status == 'Active'){
                    $sts = '<span class="label label-success">'.$row->deposit_status.'</span>';
                } else {
                    $sts = '<span class="label label-danger">'.$row->deposit_status.'</span>';
                }

                $nestedData['tanggal'] ='<span style="font-weight:bold" class="mr-2">Request :</span>'.$row->deposit_date.'<br>
                                        <span style="font-weight:bold" class="mr-2">Approve :</span>'.$row->approve_date;

                $nestedData['member'] ='<span style="font-weight:bold" class="mr-2">Nama :</span>'.$row->mbr_name.'<br>
                                        <span style="font-weight:bold" class="mr-2">No EKL :</span>'.$row->mbr_code;

                $nestedData['deposit'] ='<span style="font-weight:bold" class="mr-2">Deposit : </span>'.$this->convertCurrency($row->deposit_amount);

                $nestedData['info'] ='<span style="font-weight:bold" class="mr-2">Invoice :</span>'.$row->deposit_code.'<br>
                                                    <span style="font-weight:bold" class="mr-2">Sumber :</span>'.$row->deposit_bank.'<br>';

                $nestedData['status'] ='<span style="font-weight:bold" class="mr-2">Status :</span>'.$sts.'<br>
                                        <span style="font-weight:bold" class="mr-2">Operator :</span>'.$row->opr;

                $data[] = $nestedData;
              }
          }

            echo json_encode(array(
                "draw"              => intval($request->input('draw')),
                "recordsTotal"      => intval($totaldata),
                "recordsFiltered"   => intval($totalFiltered),
                "data"              => $data,
                "totaldeposit"      => $totaldeposit
            ));

    }

    function convertCurrency($n) {
        return 'Rp ' . number_format($n,0,',','.');
    }
}
