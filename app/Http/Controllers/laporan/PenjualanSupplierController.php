<?php

namespace App\Http\Controllers\laporan;

use Illuminate\Http\Request;
use App\Repositories\HelperInterface as Helper;
use DB;

use App\Http\Models\report_transaksi_m;

class PenjualanSupplierController {

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.laporan.penjualan_supplier');
    }

    public function getPenjualanSupplier(Request $request){

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $custom_search = $request->input('custom_search');
        $target_search = $request->input('target_search');



        // Columns
        $columns = array(
            0 => 'A.supliyer_name',
            1 => 'jumlah_product',
            2 => 'A.harga_jual',
            3 => 'B.harga_beli',
            4 => 'laba_kotor'
        );

        $order_columns = array(
            0 => 'A.supliyer_name',
            1 => 'count(\'A.product_kode\')',
            2 => 'sum("A"."harga_jual")',
            3 => 'sum("A"."harga_beli")',
            4 => '(sum("A"."harga_jual")-sum("B"."harga_beli"))',
        );

        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $order_columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        // if filtered search
        if($custom_search == 'true'){
                // Search Tanggal
                $data_search = DB::table('vw_report_transaksi AS A')
                    ->select($columns[0],
                        DB::raw('count(\'A.product_kode\') AS jumlah_product'),
                        DB::raw('sum("A"."harga_jual") AS harga_jual'),
                        DB::raw('sum("B"."harga_beli") AS harga_beli'),
                        DB::raw('(sum("A"."harga_jual")-sum("B"."harga_beli")) AS laba_kotor')
                    )
                    ->join('report_transaksi AS B', 'A.transaksi_id', '=', 'B.transaksi_id')
                    ->where('A.transaksi_status', 'Active');
                if($start_date && $end_date){
                    $where_start = $start_date . ' 00:00:00';
                    $where_end = $end_date . ' 23:59:59';
                    $data_search = $data_search->whereRaw("\"A\".\"tgl_sukses\" >= '" . $where_start . "' AND \"A\".\"tgl_sukses\" <= '" . $where_end . "'");
                }
                $data_search = $data_search->groupBy($columns[0]);
                $total_filtered = count($data_search->get());
                $total_data = $total_filtered;
                $posts = $data_search
                        //->orderBy($order, $dir)
                        ->get();
        }
        else if (empty($request->input('search.value'))){

            $data = DB::table('vw_report_transaksi AS A')
                ->select($columns[0],
                    DB::raw('count(\'A.product_kode\') AS jumlah_product'),
                    DB::raw('sum("A"."harga_jual") AS harga_jual'),
                    DB::raw('sum("B"."harga_beli") AS harga_beli'),
                    DB::raw('(sum("A"."harga_jual")-sum("B"."harga_beli")) AS laba_kotor')
                )
                ->join('report_transaksi AS B', 'A.transaksi_id', '=', 'B.transaksi_id')
                ->where('A.transaksi_status', 'Active')
                ->groupBy($columns[0])
                //->orderBy($order, $dir)
                ->get();
            $posts = $data;
        }

        $data = array();
        if($posts){
            foreach($posts as $row){
                $nestedData['supplier']         = '<strong>'.$row->supliyer_name.'<strong>';
                $nestedData['jumlah_penjualan'] = number_format($row->jumlah_product);
                $nestedData['total_penjualan']  = number_format($row->harga_jual);
                $nestedData['total_pembelian']  = number_format($row->harga_beli);
                $nestedData['laba_kotor']       = number_format($row->laba_kotor);

                $data[] = $nestedData;
            }
        }

        return response()->json(array(
            // "draw"              => intval($request->input('draw')),
            // "recordsTotal"      => intval($total_data),
            // "recordsFiltered"   => intval($total_filtered),
            "data"              => $data
        ));
    }

}
