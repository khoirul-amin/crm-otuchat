<?php

namespace App\Http\Controllers\laporan;

use Illuminate\Http\Request;
use App\Repositories\HelperInterface as Helper;
use DB;

use App\Http\Models\report_transaksi_m;

class RefundTransaksiController {

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.laporan.refund_transaksi');
    }

    public function getRefundTransaksi(Request $request){
        ini_set('memory_limit', '512M');

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $custom_search = $request->input('custom_search');
        $target_search = $request->input('target_search');
        $keyword = $request->input('keyword');
        $select_status = $request->input('select_status');

        // Columns
        $columns = array(
            0 => 'A.tgl_trx',
            1 => 'A.tgl_sukses',
            2 => 'D.mbr_name',
            3 => 'A.mbr_code',
            4 => 'C.product_name',
            5 => 'A.product_kode',
            6 => 'B.supliyer_name',
            7 => 'A.tujuan',
            8 => 'A.harga_jual',
            9 => 'A.transaksi_code',
            10 => 'A.transaksi_desc',
            11 => 'A.transaksi_status'
        );

        $order_columns = array(
            0 => 'A.tgl_trx',
            1 => 'A.mbr_code',
            2 => 'A.harga_jual',
            3 => 'A.tujuan',
            4 => 'A.supliyer_name',
            5 => 'A.transaksi_desc'
        );

        // Total Data
        $totaldata = report_transaksi_m::select('mbr_code')
        ->where('transaksi_status', 'Refund')
        ->where(function($query) use ($columns)
        {
            $query->orWhereRaw("tgl_trx > current_date - interval '2' day")
            ->orWhereRaw("tgl_sukses > current_date - interval '2' day");
        })
        ->count('mbr_code');

        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $order_columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        // if filtered search
        if($custom_search == 'true'){
            if(empty($request->input('search.value'))){
                // Pencarian Tanggal
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
                        ->where('A.transaksi_status', 'Refund');
                if($start_date && $end_date){
                    $where_start = $start_date . ' 00:00:00';
                    $where_end = $end_date . ' 23:59:59';
                    $data_search = $data_search->whereRaw("tgl_trx >= '" . $where_start . "' AND tgl_trx <= '" . $where_end . "'");
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
                $totalFiltered = count($data_search->get());
                $totaldata = $totalFiltered;
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
                        ->leftJoin('mbr_list AS D', 'A.mbr_code', '=', 'D.mbr_code')
                        ->where('A.transaksi_status', 'Refund');
                if($start_date && $end_date){
                    $where_start = $start_date . ' 00:00:00';
                    $where_end = $end_date . ' 23:59:59';
                    $data_search = $data_search->whereRaw("tgl_trx >= '" . $where_start . "' AND tgl_trx <= '" . $where_end . "'");
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
                $totalFiltered = count($data_search->get());
                $posts = $data_search
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }
        }
        else if (empty($request->input('search.value'))){
            // Awal
            $data = report_transaksi_m::from('report_transaksi AS A')->select(
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
                    ->where('A.transaksi_status', 'Refund')
                    ->where(function($query) use ($columns)
                    {
                        $query->orWhereRaw("\"A\".\"tgl_trx\" > current_date - interval '2' day")
                        ->orWhereRaw("\"A\".\"tgl_sukses\" > current_date - interval '2' day");
                    })
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir);
            $posts = $data->get();
            $totalFiltered = $totaldata;
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
                ->where('A.transaksi_status', 'Refund')
                ->where(function($query) use ($columns)
                {
                    $query->orWhereRaw("\"A\".\"tgl_trx\" > current_date - interval '2' day")
                    ->orWhereRaw("\"A\".\"tgl_sukses\" > current_date - interval '2' day");
                })
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
            $totalFiltered = count($data_search->get());
            $posts = $data_search
                ->offset($start)
                ->limit($limit)
                ->get();
        }

        $data = array();
        if($posts){
            foreach($posts as $row){
                // Menghitung Waktu
                $awal = date("Y-m-d H:i:s", strtotime($row->tgl_trx));
                $akhir = date("Y-m-d H:i:s", strtotime($row->tgl_sukses));

                $diff = date_diff(date_create($awal),date_create($akhir));

                //
                $nestedData['tanggal']          = '<strong>Request: </strong> ' .date_format(date_create($row->tgl_trx),"d M Y H:i"). '<br>
                                                    <strong>Refund: </strong> ' .date_format(date_create($row->tgl_sukses),"d M Y H:i");
                $nestedData['data_member']      = '<strong>Nama: </strong> '. $row->mbr_name . '<br><strong>ID EKL: </strong>'  . $row->mbr_code;
                $nestedData['nilai']            = $this->convertCurrency($row->harga_jual);
                $nestedData['info_pembelian']   = '<strong>Tujuan: </strong> ' . $row->tujuan . '<br>
                                                    <strong>Invoice: </strong> ' . $row->transaksi_code;
                $nestedData['supplier']         = '<strong>Produk: </strong> ' . $row->product_name . '<br>
                                                    <strong>Supplier: </strong> ' . $row->supliyer_name;
                $nestedData['keterangan']       = $row->transaksi_desc;
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

}
