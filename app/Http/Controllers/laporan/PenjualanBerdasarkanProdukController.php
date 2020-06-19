<?php

namespace App\Http\Controllers\laporan;

use Illuminate\Http\Request;
use App\Repositories\HelperInterface as Helper;
use DB;

use App\Http\Models\report_transaksi_m;

class PenjualanBerdasarkanProdukController {

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.laporan.penjualan_berdasarkan_produk');
    }

    public function getPenjualanBerdasarkanProduk(Request $request){
        ini_set('max_execution_time', 300);
        // print_r($request->all());
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $custom_search = $request->input('custom_search');
        $target_search = $request->input('target_search');
        $keyword = $request->input('keyword');
        $select_status = $request->input('select_status');

        // Columns
        $columns = array(
            0 => 'B.product_name',
            1 => 'A.product_kode',
            2 => 'C.supliyer_name',
            3 => 'qty',
            4 => 'A.harga_jual',
            5 => 'A.harga_beli',
            6 => 'laba_kotor'
        );

        $order_columns = array(
            0 => 'B.product_name',
            1 => 'A.product_kode',
            2 => 'C.supliyer_name',
            3 => 'qty',
            4 => 'harga_jual',
            5 => 'harga_beli',
            6 => 'laba_kotor',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $order_columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        // if filtered search
        if($custom_search == 'true'){
            if(empty($request->input('search.value'))){
                $data_search = report_transaksi_m::from('report_transaksi AS A')
                    ->select($columns[0], $columns[1], $columns[2],
                        DB::raw('count(\'A.product_kode\') AS qty'),
                        DB::raw('sum(COALESCE("A"."harga_jual", 0)) AS harga_jual'),
                        DB::raw('sum(COALESCE("A"."harga_beli", 0)) AS harga_beli'),
                        DB::raw('(sum(COALESCE("A"."harga_jual", 0))-sum(COALESCE("A"."harga_beli", 0))) AS laba_kotor')
                    )
                    ->join('product AS B', 'A.product_kode', '=', 'B.product_kode')
                    ->join('supliyer AS C', 'A.supliyer_id', '=', 'C.supliyer_id');
                if($start_date && $end_date){
                    $where_start = $start_date . ' 00:00:00';
                    $where_end = $end_date . ' 23:59:59';
                    $data_search = $data_search->whereRaw("\"A\".\"tgl_sukses\" >= '" . $where_start . "' AND \"A\".\"tgl_sukses\" <= '" . $where_end . "'");
                    // ->whereRaw("'[" . $start_date . ", " . $end_date . "]'::tsrange @> deposit_date")
                }
                if($target_search != 'Semua'){
                    switch ($target_search) {
                        case "product_name":
                            $data_search = $data_search->where($columns[0], 'ilike', "%{$keyword}%");
                        break;
                        case "product_kode":
                            $data_search = $data_search->where($columns[1], 'ilike', "%{$keyword}%");
                        break;
                        case "supliyer_name":
                            $data_search = $data_search->where($columns[2], 'ilike', "%{$keyword}%");
                        break;
                    }
                }
                $data_search = $data_search->groupBy($columns[0], $columns[1], $columns[2]);
                $total_filtered = count($data_search->get());
                $total_data = $total_filtered;
                $posts = $data_search
                        ->orderBy($order, $dir)
                        ->offset($start)
                        ->limit($limit)
                        ->get();
            }
            else{
                $search = $request->input('search.value');
                $data_search = report_transaksi_m::from('report_transaksi AS A')->select(
                            $columns[0],
                            $columns[1],
                            $columns[2],
                            $columns[3],
                            $columns[4],
                            $columns[5],
                            $columns[6],
                            $columns[7],
                            $columns[8],
                            $columns[9],
                            $columns[10],
                            $columns[11]
                        )
                        ->leftJoin('supliyer AS B', 'A.supliyer_id', '=', 'B.supliyer_id')
                        ->leftJoin('product AS C', 'A.product_kode', '=', 'C.product_kode')
                        ->leftJoin('mbr_list AS D', 'A.mbr_code', '=', 'D.mbr_code');
                if($start_date && $end_date){
                    $where_start = $start_date . ' 00:00:00';
                    $where_end = $end_date . ' 23:59:59';
                    $data_search = $data_search->whereRaw("tgl_sukses >= '" . $where_start . "' AND tgl_sukses <= '" . $where_end . "'");
                    // ->whereRaw("'[" . $start_date . ", " . $end_date . "]'::tsrange @> deposit_date")
                }
                if($target_search != 'Semua'){
                    switch ($target_search) {
                        case "mbr_code":
                            $data_search = $data_search->where($columns[3], '=', $keyword);
                        break;
                        case "tujuan":
                            $data_search = $data_search->where($columns[7], '=', $keyword);
                        break;
                        case "transaksi_code":
                            $data_search = $data_search->where($columns[9], '=', $keyword);
                        break;
                        case "supliyer_name":
                            $data_search = $data_search->where($columns[6], '=', $keyword);
                        break;
                    }
                }
                if($target_search != 'Semua'){
                    $data_search = $data_search->where($columns[11], '=', $target_search);
                }
                $data_search = $data_search->where(function($query) use ($columns, $search)
                        {
                            $query->orWhere($columns[0], 'ilike', "%{$search}%")
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
                            ->orWhere($columns[11], 'ilike', "%{$search}%");
                        })
                        ->orderBy($order, $dir);
                $total_filtered = count($data_search->get());
                $posts = $data_search
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }
        }
        else if (empty($request->input('search.value'))){
            $data = report_transaksi_m::from('report_transaksi AS A')
                ->select($columns[0], $columns[1], $columns[2],
                    DB::raw('count(\'A.product_kode\') AS qty'),
                    DB::raw('sum(COALESCE("A"."harga_jual", 0)) AS harga_jual'),
                    DB::raw('sum(COALESCE("A"."harga_beli", 0)) AS harga_beli'),
                    DB::raw('(sum(COALESCE("A"."harga_jual", 0))-sum(COALESCE("A"."harga_beli", 0))) AS laba_kotor')
                )
                ->whereRaw("tgl_sukses >= '" . date('Y-m-d') . " 00:00:00' AND tgl_sukses <= '" . date('Y-m-d') . " 23:59:59'")
                ->join('product AS B', 'A.product_kode', '=', 'B.product_kode')
                ->join('supliyer AS C', 'A.supliyer_id', '=', 'C.supliyer_id')
                ->groupBy($columns[0], $columns[1], $columns[2]);

            $total_data = count($data->get());
            $total_filtered = $total_data;

            $posts = $data->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        } else {
            $search = $request->input('search.value');
            $data_search = report_transaksi_m::from('report_transaksi AS A')->select(
                    $columns[0],
                    $columns[1],
                    $columns[2],
                    $columns[3],
                    $columns[4],
                    $columns[5],
                    $columns[6],
                    $columns[7],
                    $columns[8],
                    $columns[9],
                    $columns[10],
                    $columns[11]
                )
                ->leftJoin('supliyer AS B', 'A.supliyer_id', '=', 'B.supliyer_id')
                ->leftJoin('product AS C', 'A.product_kode', '=', 'C.product_kode')
                ->leftJoin('mbr_list AS D', 'A.mbr_code', '=', 'D.mbr_code')
                ->where(function($query) use ($columns, $search)
                {
                    $query->orWhere($columns[0], 'ilike', "%{$search}%")
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
                    ->orWhere($columns[11], 'ilike', "%{$search}%");
                })
                ->orderBy($order, $dir);
            $total_filtered = count($data_search->get());
            $posts = $data_search
                ->offset($start)
                ->limit($limit)
                ->get();
        }

        //Get Total Transaksi
        $data_total = report_transaksi_m::selectRaw('COUNT(product_kode) AS sum_qty, SUM(harga_jual) AS sum_jual, SUM(harga_beli) AS sum_beli, (SUM(harga_jual)-SUM(harga_beli)) AS sum_kotor');
        if($start_date && $end_date){
            $data_total = $data_total->whereRaw("tgl_sukses >= '" . $start_date . " 00:00:00' AND tgl_sukses <= '" . $end_date . " 23:59:59'");
        }
        else{
            $data_total = $data_total->whereRaw("tgl_sukses >= '" . date('Y-m-d') . " 00:00:00' AND tgl_sukses <= '" . date('Y-m-d') . " 23:59:59'");
        }
        $data_total = $data_total->first();
        $data_total->sum_jual = $this->convertCurrency($data_total->sum_jual);
        $data_total->sum_beli = $this->convertCurrency($data_total->sum_beli);
        $data_total->sum_kotor = $this->convertCurrency($data_total->sum_kotor);
        $data = array();
        if($posts){
            foreach($posts as $row){
                $nestedData['product_name'] = $row->product_name;
                $nestedData['product_kode'] = $row->product_kode;
                $nestedData['supliyer_name'] = $row->supliyer_name;
                $nestedData['qty'] = $row->qty;
                $nestedData['harga_jual'] = $this->convertCurrency($row->harga_jual);
                $nestedData['harga_beli'] = $this->convertCurrency($row->harga_beli);
                $nestedData['laba_kotor'] = $this->convertCurrency($row->laba_kotor);

                $data[] = $nestedData;
            }
        }

        return response()->json(array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => intval($total_data),
            "recordsFiltered"   => intval($total_filtered),
            "data"              => $data,
            "data_total"        => $data_total
        ));
    }

    // Conver integer to rupiah
    function convertCurrency($n) {
        return 'Rp ' . number_format($n,0,',','.');
    }

}
