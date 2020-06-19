<?php

namespace App\Http\Controllers\laporan;

use Illuminate\Http\Request;
use App\Repositories\HelperInterface as Helper;
use DB;

use App\Http\Models\report_transaksi_m;
use App\Http\Models\mutasi_list_m;
use App\Http\Models\bonus_member_active_mv;
use App\Http\Models\bonus_member_lock_mv;
use App\Http\Models\bonus_pending_m;

class MutasiBonusTransaksiController {

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.laporan.mutasi_bonus_transaksi');
    }

    public function getMutasiBonusTransaksi(Request $request){
        ini_set('memory_limit', '4096M');
        ini_set('max_execution_time', 300);

        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');
        $custom_search = $request->input('custom_search');
        $jenis_bonus = $request->input('jenis_bonus');
        $target_search = $request->input('target_search');
        $keyword = $request->input('keyword');
        $select_status = $request->input('select_status');

        $limit = $request->input('length');
        $start = $request->input('start');
        if($jenis_bonus != 'mutasi_bonus' && $jenis_bonus != ''){
            $order = 'bonus_date';
        }
        else{
            $order = 'uang_date';
        }
        $dir   = 'DESC';

        // if filtered search
        if($custom_search == 'true'){
            if($jenis_bonus == 'bonus_active'){
                $data_search = bonus_member_active_mv::from('vw_bonus_member_active AS A')
                    ->select('bonus_date', 'mbr_name', 'mbr_code', 'mbr_type',
                        'bonus_type', 'bonus_status', 'bonus_amount', 'bonus_code', 'reffren_id', 'bonus_desc');
            }
            else if($jenis_bonus == 'bonus_lock'){
                $data_search = bonus_member_lock_mv::from('vw_bonus_member_lock AS A')
                    ->select('bonus_date', 'mbr_name', 'mbr_code', 'mbr_type',
                        'bonus_type', 'bonus_status', 'bonus_amount', 'bonus_code', 'reffren_id', 'bonus_desc');
            }
            else if($jenis_bonus == 'bonus_pending'){
                $data_search = bonus_pending_m::from('bonus_pending AS A')
                    ->select('A.bonus_date', 'B.mbr_name', 'A.mbr_code', 'B.mbr_type', 'A.bonus_status',
                        'A.mesin_number', 'A.reward', 'A.transaksi_profit', 'A.profit_perusahaan', 'A.transaksi_code')
                    ->join('mbr_list AS B', 'A.mbr_code', '=', 'B.mbr_code');
            }
            else{
                $data_search = mutasi_list_m::from('mutasi_list AS A')
                    ->select('A.uang_date', 'B.mbr_name', 'B.mbr_code', 'B.mbr_type', 'A.uang_masuk', 'A.uang_keluar', 'A.uang_amount', 'A.uang_desc')
                    ->join('mbr_list AS B', 'A.mbr_code', '=', 'B.mbr_code');
            }

            if($start_date && $end_date){
                $where_start = $start_date . ' 00:00:00';
                $where_end = $end_date . ' 23:59:59';

                if($jenis_bonus != 'mutasi_bonus'){
                    $data_search = $data_search->whereRaw("bonus_date >= '" . $where_start . "' AND bonus_date <= '" . $where_end . "'");
                }
                else{
                    $data_search = $data_search->whereRaw("uang_date >= '" . $where_start . "' AND uang_date <= '" . $where_end . "'");
                }
            }
            else{

                if($jenis_bonus != 'mutasi_bonus'){
                    $data_search = $data_search->whereRaw("bonus_date >= '" . date('Y-m-d') . " 00:00:00' AND bonus_date <= '" . date('Y-m-d') . " 23:59:59'");
                }
                else{
                    $data_search = $data_search->whereRaw("uang_date >= '" . date('Y-m-d') . " 00:00:00' AND uang_date <= '" . date('Y-m-d') . " 23:59:59'");

                }
            }

            if($target_search != 'Semua'){
                switch ($target_search) {
                    case "kode_ekl":
                        $data_search = $data_search->where('A.mbr_code', 'ilike', "%{$keyword}%");
                    break;
                }
            }
            $total_filtered = count($data_search->get());
            $total_data = $total_filtered;
            $posts = $data_search
                ->orderBy($order, $dir)
                ->offset($start)
                ->limit($limit)
                ->get();
        }
        else {
            $data = mutasi_list_m::from('mutasi_list AS A')
                ->select('A.uang_date', 'B.mbr_name', 'B.mbr_code', 'B.mbr_type', 'A.uang_masuk', 'A.uang_keluar', 'A.uang_amount', 'A.uang_desc')
                ->join('mbr_list AS B', 'A.mbr_code', '=', 'B.mbr_code')
                ->whereRaw("uang_date >= '" . date('Y-m-d') . " 00:00:00' AND uang_date <= '" . date('Y-m-d') . " 23:59:59'");
            $total_data = count($data->get());
            $total_filtered = $total_data;

            $posts = $data->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();
        }

        $data = array();
        if($posts){
            if($jenis_bonus == 'bonus_active'){
                foreach($posts as $row){
                    $nestedData['tanggal'] = $row->bonus_date;
                    $nestedData['data_member'] = '<strong>Nama:</strong> ' . $row->mbr_name . '<br><strong>Kode Ekl:</strong> ' . $row->mbr_code . '<br><strong>Jabatan:</strong> ' . $row->mbr_type;
                    $nestedData['info_bonus'] = '<strong>Tipe:</strong> ' . $row->bonus_type . '<br><strong>Status:</strong> <span class=" label label-success">' . $row->bonus_status . '</span>';
                    $nestedData['keterangan_bonus'] = '<strong>Jumlah Bonus:</strong> ' . $row->bonus_amount;
                    $nestedData['keterangan'] = '<strong>Invoice:</strong> ' . $row->bonus_code . '<br><strong>Referensi:</strong> ' . $row->reffren_id . '<br><strong>Deskripsi:</strong> ' . $row->bonus_desc;
                    $data[] = $nestedData;
                }
            }
            else if($jenis_bonus == 'bonus_lock'){
                foreach($posts as $row){
                    $nestedData['tanggal'] = $row->bonus_date;
                    $nestedData['data_member'] = '<strong>Nama:</strong> ' . $row->mbr_name . '<br><strong>Kode Ekl:</strong> ' . $row->mbr_code . '<br><strong>Jabatan:</strong> ' . $row->mbr_type;
                    $nestedData['info_bonus'] = '<strong>Tipe:</strong> ' . $row->bonus_type . '<br><strong>Status:</strong> <span class=" label label-danger">' . $row->bonus_status . '</span>';
                    $nestedData['keterangan_bonus'] = '<strong>Jumlah Bonus:</strong> ' . $row->bonus_amount;
                    $nestedData['keterangan'] = '<strong>Invoice:</strong> ' . $row->bonus_code . '<br><strong>Referensi:</strong> ' . $row->reffren_id . '<br><strong>Deskripsi:</strong> ' . $row->bonus_desc;
                    $data[] = $nestedData;
                }
            }
            else if($jenis_bonus == 'bonus_pending'){
                foreach($posts as $row){
                    $nestedData['tanggal'] = $row->bonus_date;
                    $nestedData['data_member'] = '<strong>Nama:</strong> ' . $row->mbr_name . '<br><strong>Kode Ekl:</strong> ' . $row->mbr_code . '<br><strong>Jabatan:</strong> ' . $row->mbr_type;
                    $nestedData['info_bonus'] = '<strong>Status:</strong> <span class=" label label-warning">' . $row->bonus_status . '</span><br><strong>Mesin:</strong> ' . $row->mesin_number . '<br><strong>Jumlah Bonus:</strong> ' . $row->reward;
                    $nestedData['keterangan_bonus'] = '<strong>Profit Transaksi:</strong> ' . $row->transaksi_profit . '<br><strong>Profit Perusahaan:</strong> ' . $row->profit_perusahaan;
                    $nestedData['keterangan'] = $row->transaksi_code;
                    $data[] = $nestedData;
                }
            }
            else{
                foreach($posts as $row){
                    $nestedData['tanggal'] = $row->uang_date;
                    $nestedData['data_member'] = '<strong>Nama:</strong> ' . $row->mbr_name . '<br><strong>Kode Ekl:</strong> ' . $row->mbr_code . '<br><strong>Jabatan:</strong> ' . $row->mbr_type;
                    $nestedData['info_bonus'] = '<strong>Bonus Masuk:</strong> ' . $row->uang_masuk . '<br><strong>Bonus Keluar:</strong> ' . $row->uang_keluar;
                    $nestedData['keterangan_bonus'] = '<strong>Total Bonus:</strong> ' . $row->uang_amount;
                    $nestedData['keterangan'] = $row->uang_desc;
                    $data[] = $nestedData;
                }
            }
        }

        return response()->json(array(
            "draw"              => intval($request->input('draw')),
            "recordsTotal"      => intval($total_data),
            "recordsFiltered"   => intval($total_filtered),
            "data"              => $data
        ));
    }

    // Convert integer to rupiah
    function convertCurrency($n) {
        return 'Rp ' . number_format($n,0,',','.');
    }

}
