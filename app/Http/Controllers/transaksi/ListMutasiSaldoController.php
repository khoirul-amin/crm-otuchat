<?php

namespace App\Http\Controllers\transaksi;

use Illuminate\Http\Request;
use DB;

use App\Http\Models\log_uang_m;

class ListMutasiSaldoController{

    public function index(){
        return view('page.transaksi.list_mutasi_saldo');
    }

    public function getListMutasiSaldo(Request $request){
        ini_set('memory_limit', '4096M');
        // print_r($request->all());
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        // Columns
        $columns = array(
            0 => 'uang_date',
            1 => 'mbr_code',
            2 => 'uang_masuk',
            3 => 'uang_keluar',
            4 => 'uang_amount',
            5 => 'uang_desc'
        );

        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');
        
        // if filtered search
        if($start_date && $end_date){
            $where_start = $start_date . ' 00:00:00';
            $where_end = $end_date . ' 23:59:59';
            if(empty($request->input('search.value'))){
                $data_search = log_uang_m::select($columns[0], $columns[1], $columns[2], $columns[3], $columns[4], $columns[5])
                        // ->whereRaw("'[" . $start_date . ", " . $end_date . "]'::tsrange @> uang_date")
                        ->whereRaw("uang_date >= '" . $where_start . "' AND uang_date <= '" . $where_end . "'")
                        ->orderBy($order, $dir);
                        $totaldata = count($data_search->limit(1000)->get());
                        $totalFiltered = $totaldata;
                $posts = $data_search
                        ->offset($start)
                        ->limit($limit)
                        ->get();
            }
            else{
                $search = $request->input('search.value');
                $data_search = log_uang_m::select($columns[0], $columns[1], $columns[2], $columns[3], $columns[4], $columns[5])
                        // ->whereRaw("'[" . $start_date . ", " . $end_date . "]'::tsrange @> uang_date")
                        ->whereRaw("uang_date >= '" . $where_start . "' AND uang_date <= '" . $where_end . "'")
                        ->where(function($query) use ($columns, $search)
                        {
                            $query->orWhere($columns[0], 'ilike', "%{$search}%")
                            ->orWhere($columns[1], 'ilike', "%{$search}%")
                            ->orWhere($columns[2], 'ilike', "%{$search}%")
                            ->orWhere($columns[3], 'ilike', "%{$search}%")
                            ->orWhere($columns[4], 'ilike', "%{$search}%")
                            ->orWhere($columns[5], 'ilike', "%{$search}%");
                        })
                        ->orderBy($order, $dir);
                        $totaldata = count($data_search->limit(1000)->get());
                        $totalFiltered = $totaldata;
                $posts = $data_search
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }
        }
        else if (empty($request->input('search.value'))){
            $data = log_uang_m::select($columns[0], $columns[1], $columns[2], $columns[3], $columns[4], $columns[5])
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir);
            $posts = $data->get();
            // Total Data
            $totaldata = count(log_uang_m::select('mbr_code')->limit(1000)->get());
            $totalFiltered = $totaldata;
        } else {
            $search = $request->input('search.value');
            $data_search = log_uang_m::select($columns[0], $columns[1], $columns[2], $columns[3], $columns[4], $columns[5])
                ->where($columns[0], 'ilike', "%{$search}%")
                ->orWhere($columns[1], 'ilike', "%{$search}%")
                ->orWhere($columns[2], 'ilike', "%{$search}%")
                ->orWhere($columns[3], 'ilike', "%{$search}%")
                ->orWhere($columns[4], 'ilike', "%{$search}%")
                ->orWhere($columns[5], 'ilike', "%{$search}%")
                ->orderBy($order, $dir);
            $totaldata = count($data_search->limit(1000)->get());
            $totalFiltered = $totaldata;
            $posts = $data_search
                ->offset($start)
                ->limit($limit)
                ->get();
        }

        $data = array();
        if($posts){
            foreach($posts as $row){
                $nestedData['uang_date'] = $row->uang_date;
                $nestedData['mbr_code'] = $row->mbr_code;
                $nestedData['uang_masuk'] = $this->convertCurrency($row->uang_masuk);
                $nestedData['uang_keluar'] = $this->convertCurrency($row->uang_keluar);
                $nestedData['uang_amount'] = $this->convertCurrency($row->uang_amount);
                $nestedData['uang_desc'] = $row->uang_desc;
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

    // Conver integer to rupiah
    function convertCurrency($n) {
        return 'Rp ' . number_format($n,0,',','.');
    }
}
