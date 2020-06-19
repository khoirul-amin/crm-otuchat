<?php

namespace App\Http\Controllers;
use Auth;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        if(!Session::get('otp')){
            return redirect('/logout');
		}
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $dataDashboard = DB::select('SELECT * FROM f_dasboard_crm()')[0];
        $approved = DB::select("SELECT COUNT(1) FROM mbr_upgrade_verification WHERE status='Approve' AND date_approve > CURRENT_DATE")[0];
        $rejected =  DB::select("SELECT COUNT(1) FROM mbr_upgrade_verification WHERE status='Reject' AND date_approve > CURRENT_DATE")[0];
        return view('home', compact('dataDashboard','approved','rejected'));
    }

    public function getMories(Request $request){
        ini_set('max_execution_time', 300);
        ini_set('memory_limit', '1024M');
        $maxTransaksi = null;
        $labelMoris = null;
        $dataMemberTahunIni = array();
        $dataTransaksiTahunIni = array();
        if($request->periode === 'harian'){
            $day = cal_days_in_month(CAL_GREGORIAN,$request->bulan,$request->tahun);
            $dataPeriodeHari = array();
            for($i = 0; $i <= $day-1; $i++){
                $dataMember = DB::select("SELECT * from vw_infomember where EXTRACT(DAY FROM member_date) = $i+1 and EXTRACT(MONTH FROM member_date) = $request->bulan and EXTRACT(YEAR FROM member_date) = $request->tahun");
                $dataTrx = DB::select("SELECT * from vw_report_transaksi where transaksi_status = 'Active' and  EXTRACT(DAY FROM tgl_trx) = $i+1 and EXTRACT(MONTH FROM tgl_trx) = $request->bulan and EXTRACT(YEAR FROM tgl_trx) = $request->tahun");

                array_push($dataPeriodeHari, $i+1);
                array_push($dataMemberTahunIni, count($dataMember));
                array_push($dataTransaksiTahunIni, count($dataTrx));
            }
        }else if($request->periode === 'bulanan'){
            $year = DATE("Y");
            for($i = 0; $i <= 11; $i++){
                $month = $i+1;
                $dataMember = DB::select("SELECT * from vw_infomember where EXTRACT(MONTH FROM member_date) = $month and EXTRACT(YEAR FROM member_date) = $request->tahun");
                $dataTrx = DB::select("SELECT * from vw_report_transaksi where transaksi_status = 'Active' and EXTRACT(MONTH FROM tgl_trx) = $month and EXTRACT(YEAR FROM tgl_trx) = $request->tahun");

                array_push($dataMemberTahunIni, count($dataMember));
                array_push($dataTransaksiTahunIni, count($dataTrx));
            }
            $dataPeriodeBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        }else if($request->periode === 'tahunan'){
            $dataPeriodeTahun = array();
            for ($i = $request->tahun-2; $i <= $request->tahun+2; $i++) {
                $dataMember = DB::select("SELECT * from vw_infomember where EXTRACT(YEAR FROM member_date) = $i");
                $dataTrx = DB::select("SELECT * from vw_report_transaksi where transaksi_status = 'Active' and EXTRACT(YEAR FROM tgl_trx) = $i");
                
                array_push($dataPeriodeTahun, $i);
                array_push($dataMemberTahunIni, count($dataMember));
                array_push($dataTransaksiTahunIni, count($dataTrx));
            }
        }

        if($request->periode === 'harian'){
            $labelMoris = $dataPeriodeHari;
        }else if($request->periode === 'bulanan'){
            $labelMoris = $dataPeriodeBulan;
        }else if($request->periode === 'tahunan'){
            $labelMoris = $dataPeriodeTahun;
        }
        if( max($dataMemberTahunIni) < max($dataTransaksiTahunIni)){
            $maxTransaksi = max($dataTransaksiTahunIni);
        }else{
            $maxTransaksi = max($dataMemberTahunIni);
        }
        if($maxTransaksi == 0 ){
            $maxTransaksi = 100;
        }
        $data = [$dataMemberTahunIni, $dataTransaksiTahunIni, $maxTransaksi, $labelMoris];
        return response($data);
    }
}
