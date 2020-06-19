<?php

namespace App\Http\Controllers\transaksi;

use Illuminate\Http\Request;
use App\Repositories\HelperInterface as Helper;
use DB;
use DateTime;
use DateTimeZone;

use App\Http\Models\mbr_list_m;
use App\Http\Models\blok_destination_m;
use App\Http\Models\product_all_mv;
use App\Http\Models\globalsetting_m;
use App\Http\Models\data_ep_mv;
use App\Http\Models\mbr_saldo_m;
use App\Http\Models\transaksi_m;
use App\Http\Models\deposit_driver_operasional_m;
use App\Http\Models\mbr_driver_saldo_operasional_m;
use App\Http\Models\log_uang_m;
use App\Http\Models\partner_eklanku_mv;
use App\Http\Models\product_m;
use App\Http\Models\tabel_transaksi_mv;
use App\Http\Models\provider_m;
use App\Http\Models\data_vendor_active_mv;

use GuzzleHttp\Client;

class InputManualTransaksiController {

    private $base_url;
    function __construct(Helper $helper){
        $this->helper = $helper;
        $this->base_url = env("BASE_URL_EKLANKU", "https://payment.eklanku.com/");
    }

    public function index(){
        return view('page.transaksi.input_manual_transaksi');
    }

    public function getProdukPrabayar(Request $request){
        $mbr_code = $request->input('mbr_code');
        $type_produk = $request->input('type_produk');

        switch($type_produk){
            case 'KUOTA':
                $result_function = DB::select("SELECT * FROM f_get_ep_by_type('$mbr_code','$type_produk') WHERE provider NOT LIKE '%SMS%' ORDER BY 7");
            break;
            case 'SMS':
                $result_function = DB::select("SELECT * FROM f_get_ep_by_type('$mbr_code','KUOTA') WHERE provider LIKE '%SMS%' ORDER BY 7");
            break;
            case 'ETOLL':
                $result_function = DB::select("SELECT * FROM f_get_ep_by_type('$mbr_code','ETOOL MANDIRI') UNION SELECT * FROM f_get_ep_by_type('$mbr_code','ETOOL BNI') UNION SELECT * FROM f_get_ep_by_type('$mbr_code','ETOOL BRI') ORDER BY 7");
            break;
            default:
                $result_function = DB::select("SELECT * FROM f_get_ep_by_type('$mbr_code','$type_produk') ORDER BY 7");
            break;
        }

        return $result_function;
    }

    public function getProdukPascabayar(Request $request){
        $mbr_code = $request->input('mbr_code');
        $type_produk = $request->input('type_produk');

        //GET PROVIDER
        $provider = provider_m::select('provider_name')
            ->where('provaider_type', '=', $type_produk)
            ->where('provaider_status', '=', 'Active')
            ->where('provaider_group', '=', 2)
            ->first()->provider_name;
        $provider = strtoupper($provider);
        $product = product_all_mv::select('product_kode as code', 'product_name as name', 'product_status as isActive', 'provider_name as group', DB::raw("coalesce(product_image, '-') as image"))
            ->where('provider_name', '=', $provider)
            ->get();

        return $product;
    }

    public function buyProdukPrabayar(Request $request){
        //CHECK STATUS SERVER
        $status_server = globalsetting_m::where('setting_id', '=', 1)->first()->sts_sistme;
        if($status_server != 0){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'SEVER SEDANG MAINTENANCE, SILAHKAN COBA LAGI NANTI'
            ));
        }

        //INISIALISASI GLOBAL
        date_default_timezone_set('Asia/Makassar');

        //AMBIL REQUEST
        $mbr_code = $request->input('mbr_code');
        $jenis_transaksi = $request->input('jenis_transaksi');
        $jenis_produk = $request->input('jenis_produk');
        $nama_produk = $request->input('nama_produk');
        $msisdn = $request->input('msisdn');
        $buyer_phone = $request->input('buyer_phone');
        $ref_id_customer = $request->input('ref_id_customer');
        $product_code = $request->input('product_code');
        $apl_use = $request->input('apl_use');

        //QUERY
        $mbr_list = mbr_list_m::where('mbr_code', '=', $mbr_code)->first();
        $produk = product_all_mv::where('product_kode', '=', $product_code)->first();

        //CHECK MBR CODE
        if(!$mbr_list){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'AKUN TIDAK DITEMUKAN'
            ));
        }

        //CHECK IF MSISDN IS BLOCKED
        if(blok_destination_m::select('destination')->where('destination', '=', $msisdn)->first()){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'NOMOR TUJUAN TIDAK DAPAT DIGUNAKAN UNTUK BERTRANSAKSI'
            ));    
        }

        //CHECK TRANSACTION TIME
        $start = '02';
        $end   = '22';
        $now   = Date('H');
        if ($start < $now && $now > $end) {
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'MAAF, TRANSAKSI HANYA TERSEDIA PADA 02:00:00 - 22:00:00'
            ));
        }

        //CHECK KEANGGOTAAN DAN PRODUK OJEK ONLINE
        if ($jenis_produk == 'E-Saldo' && $mbr_list->type_keanggotan != 1) {
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'TRANSAKSI PRODUK TERPILIH HANYA BISA DILAKUKAN OLEH MEMBER TERVERIFIKASI'
            ));
        }

        //CHECK ACTIVE ACCOUNT
        if($mbr_list->mbr_status != 'Active'){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'AKUN ANDA TIDAK AKTIF DAN TIDAK DAPAT DIGUNAKAN BERTRANSAKSI'
            ));
        }

        //CHECK NOMOR TUJUAN/MSISDN
        if($mbr_list->mbr_type == 'H2H' AND $ref_id_customer == ''){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'NOMOR REFERENSI TIDAK BOLEH KOSONG'
            ));
        }

        //CHECK BUYER PHONE
        if($jenis_produk == 'Token Listrik' AND $buyer_phone == ''){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'PARAMETER TIDAK LENGKAP, NOMOR TELEPON PEMBELI TIDAK BOLEH KOSONG'
            ));
        }

        //! PENGECEKAN SENDIRI - PENGECEKAN PRODUK
        $cek_produk = product_all_mv::select('product_kode')
            ->where('product_kode', '=', $product_code)
            ->where('product_status', '=', 'Active')
            ->first();
        if(!$cek_produk){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'PRODUK TIDAK TERSEDIA SEKARANG, SILAHKAN COBA LAGI NANTI'
            ));
        }

        //CHECK OWAI PRODUK AND BUY PRODUK
        if($jenis_produk == 'E-Saldo' && $produk->provider_name == 'OWAI'){
            if(substr($product_code,0,14) != "OWAI_OP_DRIVER"){
                return response()->json(array(
                    'status' => 'gagal',
                    'reason' => 'PRODUK TIDAK TERSEDIA SEKARANG, SILAHKAN COBA LAGI NANTI'
                ));
            }
            else{
                $status = $this->depositDriverOwai($product_code, $mbr_code, $msisdn, request()->ip());
                if($status != "SUCCESS"){
                    return response()->json(array(
                        'status' => 'gagal',
                        'reason' => $status
                    ));
                }
                else{
                    return response()->json(array(
                        'status' => 'success',
                        'reason' => 'Pemesanan produk OWAI DRIVER telah berhasil'
                    ));
                }
            }
        }

        //PERHITUNGAN HARGA JUAL
        if($mbr_list->mbr_type == 'H2H'){
            $harga_jual = (int) product_all_mv::select('harga_h2h')
                ->where('product_kode', '=', $product_code)
                ->where('product_status', '=', 'Active')
                ->first()->harga_h2h;
            $data_level = (int) partner_eklanku_mv::select('konter_level')
                ->where('mbr_code', '=', $mbr_code)
                ->first()->konter_level;
            if ($data_level == 1) {
                $harga_jual = $harga_jual -375;
            } else if ($data_level == 2) {
                $harga_jual = $harga_jual -225;
            } else if ($data_level == 3) {
                $harga_jual = $harga_jual -125;
            }
        }
        else{
            $harga_jual = (int) product_all_mv::select('harga_jual')
                ->where('product_kode', '=', $product_code)
                ->where('product_status', '=', 'Active')
                ->first()->harga_jual;
        }
        if($harga_jual < 1){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'KODE PRODUK SALAH, PRODUK BUKAN DARI GRUP TOP UP'
            ));
        }

        //PENGECEKAN SALDO MEMBER DAN MAX TRANSAKSI
        $db_saldo = mbr_saldo_m::where('mbr_code', '=', $mbr_code)
            ->first();
        $mbr_saldo = $db_saldo->mbr_amount;
        $max_transaction = $db_saldo->max_transaction;
        $total_trx_harian = (int) DB::table('report_transaksi_'.date('Y').'_'.date('m'))
            ->selectRaw("COALESCE(sum(harga_jual),0) as tot_trx_harian")
            ->where('tgl_sukses', '>=', date('Y-m-d'))
            ->where('transaksi_status', '=', 'Active')
            ->where('mbr_code', '=', $mbr_code)
            ->first()->tot_trx_harian;
        if($mbr_saldo < $harga_jual){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'SALDO PELANGGAN TIDAK CUKUP UNTUK MELAKUKAN TRANSAKSI'
            ));
        }
        if(($total_trx_harian + $harga_jual) > $max_transaction){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'TOTAL TRANSAKSI HARIAN ANDA AKAN MELEBIHI KUOTA YANG TERSEDIA, SILAHKAN HUBUNGI KAMI UNTUK MENAMBAH KUOTA TRANSAKSI ANDA'
            ));
        }

        //GET TYPE PRODUK
        $type_produk = product_all_mv::select('type_product')
            ->where('product_kode', '=', $product_code)
            ->first()->type_product;

        //GET DIFFERENT IN SECONDS BETWEEN DATE NOW AND LAST TRANSACTION
        $db_last_trx = tabel_transaksi_mv::select('product_kode')
            ->where('mbr_code', '=', $mbr_code)
            ->where('product_kode', '=', $product_code)
            ->where('tujuan', '=', $msisdn)
            ->where('transaksi_status', '!=', $msisdn)
            ->orderBy('transaksi_id', 'DESC')
            ->limit(1)
            ->first();
        if($db_last_trx){
            $last_trx = $db_last_trx->tgl_trx;
            $diff_seconds = abs(strtotime(date("Y-m-d H:i:s")) - $last_trx);
            $last_type_produk = product_all_mv::select('type_product')
                ->where('product_kode', '=', $db_last_trx->product_kode)
                ->first()->type_product;
        }
        else $diff_seconds = 43201;

        //GET TOTAL TRANSAKSI
        $total_trx_mbr = tabel_transaksi_mv::selectRaw("count(transaksi_id) as total")
            ->where('tujuan', '=', $msisdn)
            ->whereRaw('DATE(tgl_trx) = DATE(now())')
            ->where('mbr_code', '=', $mbr_code)
            ->where('transaksi_status', '!=', 'Gagal')
            ->where('product_kode', '=', $product_code)
            ->first();
        if($total_trx_mbr){
            $total_trx_mbr = $total_trx_mbr->total;
        }
        else $total_trx_mbr = 1;

        //CHECK IF TRX HIGHER THAN 10
        if($total_trx_mbr > 10){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'DALAM SATU HARI, ANDA HANYA BISA MELAKUKAN 10 X TRANSAKSI DENGAN TUJUAN DAN NOMINAL YANG SAMA'
            ));
        }

        //CHECK PRODUK PROMO
        if(($product_code == 'SZ5' || $product_code == 'SZ10') && $total_trx_mbr > 1){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'TRANSAKSI PRODUK PROMO HANYA BISA DI ULANG 1x24 JAM UNTUK DENOM SAMA'
            ));
        }

        //CHECK PRODUK TRX 12 JAM
        if($diff_seconds <= 43200 && (($last_type_produk == 'ETOOL BNI' && $type_produk == 'ETOOL BNI') || ($last_type_produk == 'OJEK ONLINE' && $type_produk == 'OJEK ONLINE'))){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'TRANSAKSI DENGAN TUJUAN DAN NOMINAL YANG SAMA HANYA DILAKUKAN DALAM 12 JAM SEKALI'
            ));            
        }

        //CHECK PRODUK TRX 5 MENIT
        if($diff_seconds <= 300 && $last_type_produk != 'ETOOL BNI' && $type_produk != 'ETOOL BNI' && $last_type_produk != 'OJEK ONLINE' && $type_produk != 'OJEK ONLINE'){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'TRANSAKSI DENGAN TUJUAN DAN NOMINAL YANG SAMA HANYA DILAKUKAN DALAM 5 MENIT SEKALI'
            ));            
        }

        //GET DISC. PSST I DONT KNOW WHAT DISC IS 
        $disc = (int) product_m::select("disc")
            ->where('product_kode', '=', $product_code)
            ->first()->disc;


        //TRANSACTION IS BEGIN, HAHAHAHAHA YOU SHALL NOT PASS!!!
        $res = '';
        $mbr_mobile = $mbr_list->mbr_mobile;
        $order = $total_trx_mbr+1;
        $saldo = $mbr_saldo - $harga_jual;
        $sys_date = date("Y-m-d H:i:s");

        //accounting format. Hasil = 402.500
        $amount = number_format($harga_jual, 0, ",", ".");
        if ($amount < 0) {
            $amount = '(' . str_replace("-","", $amount) . ')';
        }
        else $amount;
        $keterangan = "TRX via " . $apl_use . " Sebesar Rp. " . $amount . " ke " . $msisdn . " order ke " . $order;

        $result = DB::select("SELECT * FROM f_cal_save_purchase('$sys_date', '$mbr_code', '$product_code', '$msisdn', '$apl_use', $harga_jual, '$res', '$mbr_mobile', $disc, '$buyer_phone', $order, $saldo, '$keterangan', '$ref_id_customer')");
        if($result){
            $this->helper->logAction(13, $mbr_code, 'buy prabayar ' . '(' . $product_code . ')' . ' (' . $amount . ')');
            return response()->json(array(
                'status' => 'success',
                'reason' => "Pemesanan transaksi ke nomor tujuan " . $msisdn . " telah berhasil tercatat pada sistem kami, silahkan menunggu notifikasi berikutnya untuk status transaksi anda"
            ));
        }
        else{
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'TERJADI KESALAHAN, SILAHKAN COBA BEBERAPA SAAT LAGI'
            ));
        }

    }

    public function cekProdukPascabayar(Request $request){
        //CHECK STATUS SERVER
        $status_server = globalsetting_m::where('setting_id', '=', 1)->first()->sts_sistme;
        if($status_server != 0){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'SEVER SEDANG MAINTENANCE, SILAHKAN COBA LAGI NANTI'
            ));
        }

        //INISIALISASI GLOBAL
        date_default_timezone_set('Asia/Makassar');
        $sys_date = date("Y-m-d H:i:s");

        //AMBIL REQUEST
        $mbr_code = $request->input('mbr_code');
        $jenis_transaksi = $request->input('jenis_transaksi');
        $jenis_produk = $request->input('jenis_produk');
        $nama_produk = $request->input('nama_produk');
        $msisdn = $request->input('customer_id');
        $buyer_phone = $request->input('customer_msisdn');
        $product_code = strtoupper($request->input('product_code'));
        $apl_use = $request->input('apl_use');
        $periode = '01';
        $nominal = 0;

        //QUERY
        $mbr_list = mbr_list_m::where('mbr_code', '=', $mbr_code)->first();

        //CHECK MBR CODE
        if(!$mbr_list){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'AKUN TIDAK DITEMUKAN'
            ));
        }

        //CHECK IF MSISDN IS BLOCKED
        if(blok_destination_m::select('destination')->where('destination', '=', $msisdn)->first()){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'NOMOR TUJUAN TIDAK DAPAT DIGUNAKAN UNTUK BERTRANSAKSI'
            ));    
        }

        //CHECK TRANSACTION TIME
        $start = '02';
        $end   = '22';
        $now   = Date('H');
        if ($start < $now && $now > $end) {
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'MAAF, TRANSAKSI HANYA TERSEDIA PADA 02:00:00 - 22:00:00'
            ));
        }

        //CHECK ACTIVE ACCOUNT
        if($mbr_list->mbr_status != 'Active'){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'AKUN ANDA TIDAK AKTIF DAN TIDAK DAPAT DIGUNAKAN BERTRANSAKSI'
            ));
        }

        //CEK STATUS TRANSAKSI 24 JAM TERAKHIR
        $last_trx = tabel_transaksi_mv::select('resp')
            ->whereRaw("transaksi_status not in ('Gagal','Active','Onpay')")
            ->where('tujuan', '=', $msisdn)
            ->where('product_kode', '=', $product_code)
            ->whereRaw("tgl_trx  + interval '24 hours' > NOW()")
            ->orderby("tgl_trx", "DESC")
            ->first();
        // if($last_trx){
        //     return response()->json(array(
        //         'status' => 'gagal',
        //         'reason' => 'TRANSAKSI ANDA TELAH TERJADI, TRANSAKSI DENGAN NOMINAL DAN TUJUAN YANG SAMA HANYA DAPAT DIPROSES SETIAP 1X24 JAM'
        //     ));
        // }

        //GET DISC. PSST I DONT KNOW WHAT DISC IS 
        $disc = (int) product_m::select("disc")
            ->where('product_kode', '=', $product_code)
            ->first()->disc;

        //GET DATA SUPPLIER
        $db_supplier = data_vendor_active_mv::select('supliyer_id', 'user_url', 'paswd_url', 'url_topup', 'h2h_code')
            ->where('product_kode', '=', $product_code)
            ->orderBy('supliyer_id')
            ->first();
        if(!$db_supplier){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'PRODUK TIDAK TERSEDIA, SILAHKAN HUBUNGI CUSTOMER SERVICE KAMI'
            ));
        }
        $user_url = $db_supplier->user_url;
        $paswd_url = $db_supplier->paswd_url;
        $url_topup = $db_supplier->url_topup;
        $h2h_code = $db_supplier->h2h_code;
        $supliyer_id = $db_supplier->supliyer_id;

        //SEND INQUIRY TO EACH SUPPLIER
        switch($supliyer_id){
            case 6:
                $hasil = $this->inquiryRajaBiller($msisdn, $h2h_code, $user_url, $paswd_url, $url_topup, $disc, date("YmdHis", strtotime($sys_date)), $periode);
            break;
            case 8:
                $hasil = $this->inquiryDarmawisata($msisdn, $h2h_code, $user_url, $paswd_url, $url_topup, $disc, $buyer_phone);
            break;
            case 12:
                $hasil = $this->inquiryFastPay($msisdn, $h2h_code, $user_url, $paswd_url, $url_topup, $disc, date("YmdHis", strtotime($sys_date)), $periode, $nominal);
            break;
            case 14:
                $hasil = $this->inquiryPTPOS($msisdn, $h2h_code, $user_url, $paswd_url, $url_topup, $disc, date("YmdHis", strtotime($sys_date)), $buyer_phone);
            break;
            default:
                return response()->json(array(
                    'status' => 'gagal',
                    'reason' => 'SUPPLIER TIDAK DITEMUKAN'
                ));
            break;
        }

        //FIX NO RESPONSE FROM VENDOR
        $dataHasil = explode("|", $hasil);
        if($dataHasil[0] = 'FAILED'){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => $dataHasil[1]
            ));
        }

        //PERBAIKI RESPON
        $dataHasil = explode("|", $hasil);
        $tag = (int) $dataHasil[10];
        $respp = $dataHasil[1];
        
        if ($tag < 1000 && $dataHasil[0] == "SUKSES"){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'FAILED PRODUCT NOT OPEN'
            ));
        }
        if ($tag > 1000 && $dataHasil[0] == "SUKSES"){
            $outputKey = array();
            $outputVal = array();

            array_push($outputKey, 'respTime');
            array_push($outputKey, 'productCode');
            array_push($outputKey, 'billingReferenceID');
            array_push($outputKey, 'customerID');
            array_push($outputKey, 'customerMSISDN');
            array_push($outputKey, 'customerName');
            array_push($outputKey, 'period');
            array_push($outputKey, 'policeNumber');
            array_push($outputKey, 'lastPaidPeriod');
            array_push($outputKey, 'tenor');
            array_push($outputKey, 'lastPaidDueDate');
            array_push($outputKey, 'usageUnit');
            array_push($outputKey, 'penalty');
            array_push($outputKey, 'payment');
            array_push($outputKey, 'minPayment');
            array_push($outputKey, 'maxPayment');
            array_push($outputKey, 'additionalMessage');
            array_push($outputKey, 'billing');
            array_push($outputKey, 'sellPrice');
            array_push($outputKey, 'adminBank');
            array_push($outputKey, 'profit');
            array_push($outputKey, 'ep');
            array_push($outputKey, 'ref1');

            array_push($outputVal, $token);
            array_push($outputVal, $product_kode);
            array_push($outputVal, $dataHasil[1]);
            array_push($outputVal, $tujuan);
            array_push($outputVal, $hp_pelanggan); # customerMSISDN
            array_push($outputVal, $dataHasil[2]); # customerName
            array_push($outputVal, $dataHasil[3]); # period
            array_push($outputVal, $dataHasil[4]); # policeNumber
            array_push($outputVal, $dataHasil[5]); # lastPaidPeriod
            array_push($outputVal, $dataHasil[6]); # tenor
            array_push($outputVal, $dataHasil[7]); # lastPaidDueDate
            array_push($outputVal, $dataHasil[8]); # usageUnit
            array_push($outputVal, $dataHasil[9]); # penalty
            array_push($outputVal, $dataHasil[10]);
            array_push($outputVal, $dataHasil[11]);
            array_push($outputVal, $dataHasil[12]);
            array_push($outputVal, $dataHasil[13]);
            array_push($outputVal, $dataHasil[14]);
            array_push($outputVal, $dataHasil[15]);
            array_push($outputVal, $dataHasil[16]);
            array_push($outputVal, $dataHasil[17]);

            if ($mbr_list->$mbr_type == 'H2H'){
                $data_level = (int) partner_eklanku_mv::select('konter_level')
                ->where('mbr_code', '=', $mbr_code)
                ->first()->konter_level;
                if ($data_level == 1){
                    array_push($outputVal, 50);
                }
                elseif ($data_level == 2){
                    array_push($outputVal, 150);
                }
                elseif ($data_level == 3){
                    array_push($outputVal, 250);
                }
                else{
                    array_push($outputVal, 300);
                }
            }
            else{
                array_push($outputVal, $dataHasil[18]); # ep
            }
            array_push($outputVal, $dataHasil[19]);
            $output = array_combine($outputKey, $outputVal);
            
            $dataTampil = json_encode($output);
            $mbr_mobile = $mbr_list->mbr_mobile;
            $fix_error_aphosthrope = str_replace("'","''",$dataTampil);
            $fix_error_aphosthrope2 = str_replace(".{","{",str_replace("'","''",$dataHasil[20]));

            //SAVE INQUIRY
            $dt = new DateTime("now", new DateTimeZone('Asia/Makassar')); 
            $dt->setTimestamp(time());
            $date_trx = $dt->format('Y-m-d H:i:s');
            $harga = (int) $dataHasil[14];
            $admin = (int) $disc;
            $prefix = substr((string)$buyer_phone, 0, 2); 
            if(strpos($prefix, '62') !== false){
                $buyer_phone = str_replace("62", "0", $prefix) . "" . substr($buyer_phone, 2);
            }
            
            $result = DB::select("SELECT * FROM f_cal_inquiry_payment('$date_trx', '$mbr_code', '$product_code', '$msisdn', '$dataHasil[1]', '$apl_use', '$harga', '$fix_error_aphosthrope', '4','$mbr_mobile', '$supliyer_id', '$admin', '$buyer_phone', '$fix_error_aphosthrope2')");

            if($result){
                $this->helper->logAction(13, $mbr_code, 'cek pascabayar ' . '(' . $msisdn . ')' . ' (' . $harga . ')');
                return response()->json(array(
                    'status' => 'success',
                    'reason' => $output
                ));
            }
            else{
                return response()->json(array(
                    'status' => 'gagal',
                    'reason' => 'TERJADI KESALAHAN, SILAHKAN COBA BEBERAPA SAAT LAGI'
                ));
            }
        }
        else {
            return response()->json(array(
                'status' => 'gagal',
                'reason' => $output
            ));
        }

    }

    public function cekProdukPascabayarAPI(Request $request){

        //AMBIL REQUEST
        $mbr_code = $request->input('mbr_code');
        $jenis_transaksi = $request->input('jenis_transaksi');
        $jenis_produk = $request->input('jenis_produk');
        $nama_produk = $request->input('nama_produk');
        $msisdn = $request->input('customer_id');
        $buyer_phone = $request->input('customer_msisdn');
        $product_code = strtoupper($request->input('product_code'));
        $apl_use = $request->input('apl_use');

        $client = new Client();
        $url = $this->base_url . "Pascabayar/inquiry";

        $token = mbr_list_m::select('mbr_token')
            ->where('mbr_code', '=', $mbr_code)
            ->first()->mbr_token;

        $data = $client->request('POST', $url, [
            'headers' => [
                'X-API-KEY' => '222',
                'User-Agent' => 'okhttp/3.9.0'
            ],
            'form_params' => [
                'userID' => $mbr_code,
                'accessToken' => $token,
                'productCode' => $product_code,
                'customerID' => $msisdn,
                'customerMSISDN' => $buyer_phone,
                'aplUse' => $apl_use
            ]
        ]);

        $res = json_decode($data->getBody());

        if($res->errNumber != 0){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => $res->respMessage
            ));
        }

        $this->helper->logAction(13, $mbr_code, 'cek pascabayar ' . '(' . $msisdn . ')' . ' (' . $res->billing . ')');
        return response()->json(array(
            'status' => 'success',
            'reason' => $res
        ));
    }

    public function buyProdukPascabayar(Request $request){
        //AMBIL REQUEST
        $mbr_code = $request->input('mbr_code');
        $billing_reference_id = $request->input('billing_reference_id');
        $apl_use = $request->input('apl_use');

        //CHECK STATUS SERVER
        $status_server = globalsetting_m::where('setting_id', '=', 1)->first()->sts_sistme;
        if($status_server != 0){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'SEVER SEDANG MAINTENANCE, SILAHKAN COBA LAGI NANTI'
            ));
        }

        //QUERY
        $mbr_list = mbr_list_m::where('mbr_code', '=', $mbr_code)->first();

        //CHECK MBR CODE
        if(!$mbr_list){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'AKUN TIDAK DITEMUKAN'
            ));
        }

        //CHECK ACTIVE ACCOUNT
        if($mbr_list->mbr_status != 'Active'){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'AKUN ANDA TIDAK AKTIF DAN TIDAK DAPAT DIGUNAKAN BERTRANSAKSI'
            ));
        }

        //GET INFO TRX
        $trx = transaksi_m::select('product_kode', 'harga_jual', 'biaya_admin', 'tujuan')
            ->where('transaksi_status', '=', 'Onpay')
            ->where('transaksi_type', '=', 'INQUIRY')
            ->where('id_inquiry', '=', $billing_reference_id)
            ->where('mbr_code', '=', $mbr_code)
            ->first();
        if(!$trx){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'REFERENSI BILLING TIDAK DITEMUKAN'
            ));
        }
        $kode_barang = $trx->product_kode;
        $tagihan = $trx->harga_jual;
        $tujuan = $trx->tujuan;
        $biaya_admin = $trx->biaya_admin;

        //INISIALISASI GLOBAL
        date_default_timezone_set('Asia/Makassar');

        //PENGECEKAN SALDO MEMBER DAN MAX TRANSAKSI
        $db_saldo = mbr_saldo_m::where('mbr_code', '=', $mbr_code)
            ->first();
        $mbr_saldo = $db_saldo->mbr_amount;
        $max_transaction = $db_saldo->max_transaction;
        $total_trx_harian = (int) DB::table('report_transaksi_'.date('Y').'_'.date('m'))
            ->selectRaw("COALESCE(sum(harga_jual),0) as tot_trx_harian")
            ->where('tgl_sukses', '>=', date('Y-m-d'))
            ->where('transaksi_status', '=', 'Active')
            ->where('mbr_code', '=', $mbr_code)
            ->first()->tot_trx_harian;
        if($mbr_saldo < $tagihan){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'SALDO PELANGGAN TIDAK CUKUP UNTUK MELAKUKAN TRANSAKSI'
            ));
        }
        if(($total_trx_harian + $tagihan) > $max_transaction){
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'TOTAL TRANSAKSI HARIAN ANDA AKAN MELEBIHI KUOTA YANG TERSEDIA, SILAHKAN HUBUNGI KAMI UNTUK MENAMBAH KUOTA TRANSAKSI ANDA'
            ));
        }

        //CEK TRX PPOB
        $trx_ppob = DB::table('report_transaksi_'.date('Y').'_'.date('m'))
            ->selectRaw('count(transaksi_id) as total')
            ->whereRaw("tgl_trx + interval '24 hours' > NOW()")
            ->where('product_kode', '=', $kode_barang)
            ->where('tujuan', '=', $tujuan)
            ->where('mbr_code', '=', $mbr_code)
            ->where('transaksi_type', '=', 'TRANSAKSI')
            ->where('transaksi_status', '=', 'Active')
            ->first();
        // if($trx_ppob){
        //     return response()->json(array(
        //         'status' => 'gagal',
        //         'reason' => 'TRANSAKSI ANDA TELAH TERJADI, TRANSAKSI DENGAN NOMINAL DAN TUJUAN YANG SAMA HANYA DAPAT DIPROSES SETIAP 1X24 JAM'
        //     ));
        // }

        //PROSES TRX PPOB
        $refid = '';
        $sisa = $mbr_saldo - $tagihan;
        $sys_date = date('Y-m-d H:i:s');
        $tagihan = (int) $tagihan;

        //accounting format. Hasil = 402.500
        $tagihan_f = number_format($tagihan, 0, ",", ".");
        if ($tagihan_f < 0) {
            $tagihan_f = '(' . str_replace("-","", $tagihan_f) . ')';
        }
        else $tagihan_f;
        $keterangan = "TRX via " . $apl_use . " Sebesar Rp. " . $tagihan_f. " ke " . $tujuan;

        $result = DB::select("SELECT * FROM f_cal_ppob('$sys_date', '$mbr_code', $sisa, $tagihan, '$keterangan', '$billing_reference_id', '$refid', '$apl_use')");

        if($result){
            $this->helper->logAction(13, $mbr_code, 'buy pascabayar ' . '(' . $billing_reference_id . ')' . ' (' . $tagihan . ')');
            return response()->json(array(
                'status' => 'success',
                'reason' => "prosess berhasil"
            ));
        }
        else{
            return response()->json(array(
                'status' => 'gagal',
                'reason' => 'TERJADI KESALAHAN, SILAHKAN COBA BEBERAPA SAAT LAGI'
            ));
        }

    }

    public function mbrCodeCheck(Request $request){
        $data = mbr_list_m::select('mbr_mobile')
        ->where('mbr_code', '=', $request->input('mbr_code'))
        ->first();
        if($data){
            return response()->json(array(
                "mbr_code" => $data->mbr_mobile
            ));
        }
        return 0;
    }

    public function inquiryRajaBiller($tujuan, $h2h_code, $user_url, $paswd_url, $url, $agen_fee, $trx_time, $periode){
        if($h2h_code == 'ASRBPJSKS'){
            $p_tujuan=strlen($tujuan);
            if($p_tujuan < 16){
                $tujuan = substr("88888888", 0, 16 - strlen($tujuan)) . "" . $tujuan;
            }
			$data = array(
				'method'    	=> 'rajabiller.bpjsinq',
				'uid'       	=> $user_url,
				'pin'       	=> $paswd_url,
				'kode_produk' 	=> $h2h_code,
				'periode' 		=> $periode,
				'ref1' 			=> $trx_time,
				'idpel'			=> $tujuan
			);
        }
        else if($h2h_code == 'SPEEDY'){
            $prefix = substr($tujuan, 0, 3);
            $no_telepon = '';
            $kode_area = $tujuan;

            $data_prefix = provider_m::select("provider_id")
                ->where("provider_pref", "like", "%{$prefix}%")
                ->where("provaider_type", "=", "TELEPON RUMAH")
                ->first();
            if($data_prefix){
                $h2h_code = 'TELEPON';
                $kode_area = substr($tujuan, 0, 4);
                $no_telepon = substr($tujuan, 4, strlen($tujuan));
                if($prefix == '021' || $prefix == '024' || $prefix == '031' || $prefix == '061'){
                    $kode_area = $prefix;
                    $no_telepon = substr($tujuan, 3, strlen($tujuan));
                }
            }
            $data = array(
                'method'    	=> 'rajabiller.inq',
                'uid'       	=> $user_url,
                'pin'       	=> $paswd_url,
                'idpel1' 		=> $kode_area,
                'idpel2' 		=> $no_telepon,
                'idpel3' 		=> '',
                'kode_produk' 	=> $h2h_code,
                'ref1'			=> $trx_time
            );
        }
        else if($h2h_code == 'WATAPIN' || $h2h_code == 'WAMJK' || $h2h_code == 'WABGK'){
			$data = array(
				'method'    	=> 'rajabiller.inq',
				'uid'       	=> $user_url,
				'pin'       	=> $paswd_url,
				'idpel1' 		=> '',
				'idpel2' 		=> $tujuan,
				'idpel3' 		=> '',
				'kode_produk' 	=> $h2h_code,
				'ref1'			=> $trx_time
			);
        }
        else{
			$data = array(
				'method'    	=> 'rajabiller.inq',
				'uid'       	=> $user_url,
				'pin'       	=> $paswd_url,
				'idpel1' 		=> $tujuan,
				'idpel2' 		=>'',
				'idpel3' 		=> '',
				'kode_produk' 	=> $h2h_code,
				'ref1'			=> $trx_time
			);
        }

        //SEND INQUIRY TO RAJABILLER
		$url = 'https://202.43.173.234/transaksi/json.php';
        $respon = json_decode($this->sendJson($data, $url, 500));
        
        //FIX NO RESPONSE FROM VENDOR
        if(!$respon){
            return "FAILED" . "|" . 'TIDAK ADA RESPON DARI VENDOR';
        }

        //PROCESSING RESPONSE
        if($respon->STATUS == '00'){
            $tagihan = (int) $respon->NOMINAL + (int) $respon->ADMIN + $agen_fee;
            $billing = (int) $respon->NOMINAL + (int) $respon->ADMIN + $agen_fee;
            $biaya_admin = (int) $respon->ADMIN + $agen_fee;
            $idpel = $respon->IDPEL1;
            $ep = ((int) $respon->NOMINAL + (int) $respon->ADMIN - (int) $respon->SALDO_TERPOTONG) * 0.55;
            if ($agen_fee > 0){
                $ep = $ep + $agen_fee;
            }
			if($h2h_code == 'TELEPON'){ 
                $idpel = $respon->IDPEL1 . "" . $respon->IDPEL2;
            }
			$dan = (int) $respon->NOMINAL;
			if($dan > 0){
                $message = "SUKSES|".$respon->REF2."|"
                    .$respon->NAMA_PELANGGAN."|"
                    .$respon->PERIODE."|-|-|-|-|-|-"."|"
                    .$respon->NOMINAL."|0|0|"
                    .$respon->KET."|"
                    .$billing."|"
                    .$tagihan."|"
                    .$biaya_admin."|"
                    .$agen_fee."|"
                    .$ep."|"
                    .$respon->REF1."|."
                    .$send_data;
            }else{
                $message = "FAILED|Tagihan belum tersedia!";
            }
        }
        else{
			$message = "FAILED" . "|" . $respon->KET;
		}
        return $message;
    }

    public function inquiryDarmawisata($tujuan, $h2h_code, $user_url, $paswd_url, $url, $agen_fee, $buyer_phone){
        $login = $this->loginDarmawisata($user_url, $paswd_url, $url);

        if($login){
            if($buyer_phone == ''){
                $buyer_phone = '087762720080';
            }
            $fields = array(
                'userID' => $user_url,
                'accessToken'=> $login,
                'productCode'=> $h2h_code,
                'customerID' => $tujuan,
                'customerMSISDN'=>$buyer_phone
            );

            //CURL CEK TAGIHAN
            $dturl = $url . "/PPOB/Inquiry";
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL, $dturl);
            curl_setopt($ch,CURLOPT_POST, count($fields));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));  
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close($ch);

            //KELOLA RESPON
            $hasil = json_decode($result, true);
            $kode = $hasil['status'];
            $idinq = "XX";
            if($kode == 'SUCCESS'){
                $idinq = $hasil['billingReferenceID'];
                $nama = $hasil['customerName'];
                $tagihan = $hasil['payment'] + $agen_fee;
                $billing = $hasil['payment'];

                $message = "SUKSES|".$idinq."|"
                    .$nama."|"
                    .$hasil['period']."|"
                    .$hasil['policeNumber']."|"
                    .$hasil['lastPaidPeriod']."|"
                    .$hasil['tenor']."|"
                    .$hasil['lastPaidDueDate']."|"
                    .$hasil['usageUnit']."|"
                    .$hasil['penalty']."|"
                    .$billing."|"
                    .$hasil['minPayment']."|"
                    .$hasil['maxPayment']."|"
                    .$hasil['additionalMessage']."|"
                    .$billing."|"
                    .$tagihan."|"
                    .$hasil['adminBank']."|"
                    .$agen_fee;  
            }else{
                $kode = 05;
                $pesanEror = $hasil['additionalMessage'];
                $msgres = $hasil['respMessage'];
                if(!empty($pesanEror)){
                    $msgres= $pesanEror;
                }
                $message = "FAILED|" . $msgres;
            }
            $this->doOut($user_url, $paswd_url, $url, $login);
        }else{
            $msg = 'Kesalahan jaringan';
            $message = "FAILED|".$msg;
        }
        return $message;
    }

    public function loginDarmawisata($user, $pass, $url){
        $dt = new DateTime("now", new DateTimeZone('Asia/Makassar'));
        $dt->setTimestamp(time());
        $token = $dt->format('Y-m-d') . "T" . $dt->format('H:i:s');
        $fields = array(
            'userID' => $user,
            'token'=> $token,
            'securityCode'=> md5($token . md5($pass))
        );

        //CURL LOGIN
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url . "/Session/Login");
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));  
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $hasil = json_decode($result, true);

        $kode = $hasil['status'];
        $acToken = 0;
        if($kode == "SUCCESS"){
            $acToken = $hasil['accessToken'];
        }
        return $acToken;
    }

    public function logoutDarmawisata($user, $pass, $url, $access_token){
        $dt = new DateTime("now", new DateTimeZone('Asia/Makassar'));
        $dt->setTimestamp(time());
        $token = $dt->format('Y-m-d') . "T" . $dt->format('H:i:s');
        $fields = array(
            'userID' => $user,
            'accessToken' => $access_token,
            'token'=> $token
        );

        //CURL LOGOUT
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL, $url . "/Session/Logout");
        curl_setopt($ch,CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));    
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close($ch);

        $hasil = json_decode($result, true);

        $kode = $hasil['status'];
        $acToken = 0;
        if($kode == "SUCCESS"){
            $acToken = $hasil['accessToken'];
        }
        return $acToken;
    }

    public function inquiryFastPay($tujuan, $h2h_code, $user_url, $paswd_url, $url, $agen_fee, $trx_time, $periode, $nominal){
        if ($h2h_code == 'ASRBPJSKS')
        {
            $p_tujuan=strlen($tujuan);
            if($p_tujuan < 16){
                $tujuan = substr("88888888", 0, 16 - strlen($tujuan)) . "" . $tujuan;
            }
			$data = array(
                'method'        => 'fastpay.bpjsinq',
                'uid'           => $user_url,
                'pin'           => $paswd_url,
                'ref1'          => $trx_time,
                'kode_produk'   => $h2h_code,
                'idpel1'        => $tujuan,
                'periode'       => $periode
            );
        }
        else if($h2h_code == 'SPEEDY'){
            $prefix = substr($tujuan, 0, 3);
            $no_telepon = '';
            $kode_area = $tujuan;

            $data_prefix = provider_m::select("provider_id")
                ->where("provider_pref", "like", "%{$prefix}%")
                ->where("provaider_type", "=", "TELEPON RUMAH")
                ->first();
            if($data_prefix){
                $h2h_code = 'TELEPON';
                $kode_area = substr($tujuan, 0, 4);
                $no_telepon = substr($tujuan, 4, strlen($tujuan));
                if($prefix == '021' || $prefix == '024' || $prefix == '031' || $prefix == '061'){
                    $kode_area = $prefix;
                    $no_telepon = substr($tujuan, 3, strlen($tujuan));
                }
            }
            $data = array(
				'method'    	=> 'fastpay.inq',
				'uid'       	=> $user_url,
				'pin'       	=> $paswd_url,
				'ref1'			=> $trx_time,
				'kode_produk' 	=> $h2h_code,
				'idpel1' 		=> $kode_area,
				'idpel2' 		=> $no_telepon,
				'idpel3' 		=> ''
            );
        }
        else if($h2h_code == 'WATAPIN' || $h2h_code == 'WAMJK' || $h2h_code == 'WABGK'){
            $data = array(
                'method'    	=> 'fastpay.inq',
                'uid'       	=> $user_url,
                'pin'       	=> $paswd_url,
                'ref1'			=> $trx_time,
                'kode_produk' 	=> $h2h_code,
                'idpel1' 		=> '',
                'idpel2' 		=> $tujuan,
                'idpel3' 		=> ''
            );
        }
        else if(strpos(strtoupper($h2h_code), 'KK') !== false){
            $data = array(
                'method'    	=> 'fastpay.inq',
                'uid'       	=> $user_url,
                'pin'       	=> $paswd_url,
                'ref1'			=> $trx_time,
                'kode_produk' 	=> $h2h_code,
                'idpel1' 		=> $tujuan,
                'idpel2' 		=> '',
                'idpel3' 		=> $nominal
            );
        }
        else{
            $data = array(
                'method'    	=> 'fastpay.inq',
                'uid'       	=> $user_url,
                'pin'       	=> $paswd_url,
                'ref1'			=> $trx_time,
                'kode_produk' 	=> $h2h_code,
                'idpel1' 		=> $tujuan,
                'idpel2' 		=> '',
                'idpel3' 		=> ''
            );
        }

        //CURL PENGECEKAN TAGIHAN
        $send_data 	= $this->sendJson($data, $url, 500);
        $respon = json_decode($send_data);

        //FIX NO RESPONSE FROM VENDOR
        if(!$respon){
            return "FAILED" . "|" . 'TIDAK ADA RESPON DARI VENDOR';
        }

        if($respon->status == '00'){
			$tagihan = (int) $respon->nominal + (int) $respon->biayaadmin + $agen_fee;
            $billing = (int) $respon->nominal + (int) $respon->biayaadmin + $agen_fee;
            $biaya_admin = (int) $respon->biayaadmin + $agen_fee;
            $idpel = $respon->idpelanggan1;
            $ep = ((int) $respon->nominal + (int) $respon->biayaadmin - (int) $respon->saldoterpotong) * 0.55;
            if ($agen_fee > 0){
                $ep = $ep + $agen_fee;
            }
			if($h2h_code == 'TELEPON'){ 
                $idpel = $respon->idpelanggan1 . "" . $respon->idpelanggan2;
            }
			$dan = (int) $respon->nominal;
			if($dan > 0){
                $nama_pel = '';
                if(!empty($respon->subscribername)){
                    $nama_pel = $respon->subscribername;
                }
                else if(!empty($respon->customername)){
                    $nama_pel = $respon->customername;
                }
                else if(!empty($respon->namapelanggan)){
                    $nama_pel = $respon->namapelanggan;
                }
                $periode = '';
                if(!empty($respon->jumlahbill)){
                    $periode = $respon->jumlahbill;
                }
                else if(!empty($respon->billstatus)){
                    $periode = $respon->billstatus;
                }
                else if(!empty($respon->lastpaidperiode)){
                    $periode = $respon->lastpaidperiode;
                }
                else if(!empty($respon->billquantity)){
                    $periode = $respon->billquantity;
                }
                else if(!empty($respon->periode)){
                    $periode = $respon->periode;
                }
                $message = "SUKSES|".$respon->ref2."|"
                    .$nama_pel."|"
                    .$periode."|-|-|-|-|-|-"."|"
                    .$respon->nominal."|0|0|"
                    .$respon->keterangan."|"
                    .$billing."|"
                    .$tagihan."|"
                    .$biaya_admin."|"
                    .$agen_fee."|"
                    .$ep."|"
                    .$respon->ref1."|."
                    .$send_data;
            }else{
                $message = "FAILED|Tagihan belum tersedia!";
            }
        }
        else{
            $ket = str_replace('EXT: ', '', $respon->keterangan);
            if($respon->status == '55'){
                $ket = 'ID YANG ANDA GUNAKAN SALAH';
            }
			$message = "FAILED"."|".$ket;
		}
    }

    public function inquiryPTPOS($tujuan, $h2h_code, $user_url, $paswd_url, $url, $agen_fee, $trx_time, $buyer_phone){
        //GENERATE TRX ID
        $trx_id = transaksi_m::selectRaw("COUNT(trx_id) AS trx_id")
            ->whereRaw("DATE(tgl_trx) >= DATE(NOW())")
            ->first()->trx_id;
        $trx_id++;
        $trxid = sprintf('%08d', $trx_id);
        
        $password = '34f75ab8a3c5020cf6063eb9e744de03'; # key1
        $key = 'b41a4f3909657f660fa313a44aa607c4'; # key2
        $sign = md5('inquiry' . $user_url . $paswd_url . $h2h_code . $tujuan . $trxid . $password . date("Y-m-d") . $key);
        $fields = array(
            'type'              => 'inquiry',
            'accountType'       => 'B2B',
            'account'           => $user_url,
            'institutionCode'   => $paswd_url, 
            'product'           => $h2h_code,
            'billNumber'        => $tujuan,
            'trxId'             => $trxid,
            'retrieval'         => date('YmdHisz'),
            'sign'              => $sign
        );

        $payload = json_encode($fields);

        $ch = curl_init();
        $headr = array();
        $headr[] = 'Content-type: application/json';
        $headr[] = 'Authorization: Basic Base64(finsbu:finsbu321!@#)';
        $headr[] = 'Origin: eklanku.com';

        curl_setopt($ch, CURLOPT_VERBOSE, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);  
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headr);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_USERAGENT,'FinSBUGiroMPospay-B2B');
        $result = curl_exec($ch);

        if ($result === false) {
            $message = "FAILED" . "|" . "Something Went Wrong! Please Contact Us Immediately. ERRCODE: CE01";
        }
        else {
            $hasil = json_decode($result, true);
            if ($hasil['resultCode'] == '000') {
                # Declare Variable Response
                $resp_nominal = (int)$hasil['nominal'];
                $resp_admin = (int)$hasil['admin'];

                if ($agen_fee == 0) {
                    $admin_supplier = product_m::select('admin_supplier')
                        ->where('h2h_code', '=', $h2h_code)
                        ->first()->admin_supplier;

                    # PRODUK INVOICING
                    $biaya_admin = $resp_admin;
                    $tagihan = $resp_nominal + $biaya_admin;
                    $billing = $resp_nominal + $biaya_admin;
                    $ep = ($admin_supplier) * 0.55;
                }
                else {
                    $biaya_admin = $agen_fee * (int)$hasil['jmlLembar'];
                    $tagihan = $resp_nominal + $biaya_admin;
                    $billing = $resp_nominal + $biaya_admin;
                    $ep = ($biaya_admin - $resp_admin) * 0.55;
                }
                
                $customerName = '-';
                $period = '-';
                $lastPaidDueDate = '-';
                $usageUnit = '-';
                $policeNumber = '-';
                $minPayment = 0;
                $penalty = 0;
                
                $info1 = explode("|",$hasil['info1']);
                foreach ($info1 as $info_key) {
                    if (strpos(strtoupper($info_key), 'NAMA') !== false) $customerName = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    if (strpos(strtoupper($info_key), 'BL/TH') !== false) $period = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'BULAN REK') !== false) $period = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'REK BULAN') !== false) $period = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'PERIODE') !== false) $period = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'NO ANGSURAN') !== false) $period = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'ANGSURAN KE') !== false) $period = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'ANGS.KE') !== false) $period = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'PER TGL') !== false) $period = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    if (strpos(strtoupper($info_key), 'PEMAKAIAN') !== false) $usageUnit = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'VOL.PAKAI') !== false) $usageUnit = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'JUMLAH BULAN') !== false) $usageUnit = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'JML TAGIHAN') !== false) $usageUnit = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    if (strpos(strtoupper($info_key), 'POLIS') !== false) $policeNumber = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'NOMOR VA') !== false) $policeNumber = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'NO REFF') !== false) $policeNumber = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'KODE BIGTV') !== false) $policeNumber = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'NO.KONTRAK') !== false) $policeNumber = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    if (strpos(strtoupper($info_key), 'MIN.BAYAR') !== false) $minPayment = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'BAYAR MIN') !== false) $minPayment = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    if (strpos(strtoupper($info_key), 'JATUH TEMPO') !== false) $lastPaidDueDate = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    else if (strpos(strtoupper($info_key), 'TGL J.TEMPO') !== false) $lastPaidDueDate = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    if (strpos(strtoupper($info_key), 'DENDA') !== false) $penalty = trim(substr(trim($info_key), strpos(trim($info_key), ':')+1));
                    
                    array_push($arr,strtoupper($info_key));
                }
                
                $resp = $hasil;
                $resp['info1'] = str_replace("|",";",$hasil['info1']);
                $resp['info2'] = str_replace("|",";",$hasil['info2']);
                $resp['info3'] = str_replace("|",";",$hasil['info3']);
                
                $additionalMessage = $hasil['resultDesc'].';'.$resp['info1'].';'.$resp['info2'].';'.$resp['info3'];
                
                $message = "SUKSES|".
                    $hasil['paymentCode']."|"
                    .$customerName."|"
                    .$period."|"
                    .$policeNumber."|-|-|"
                    .$lastPaidDueDate."|"
                    .$usageUnit."|"
                    .$penalty."|"
                    .$hasil['nominal']."|"
                    .$minPayment."|0|"
                    .$additionalMessage."|"
                    .$billing."|"
                    .$tagihan."|"
                    .$biaya_admin."|"
                    .$agen_fee."|"
                    .$ep."|"
                    .str_replace("|",";",$hasil['info1'])."|"
                    .json_encode($resp);
            }
            else $message = "FAILED" . "|" . $hasil['resultDesc'];
        }
        curl_close($ch);
        return $message;
    }

    public function depositDriverOwai($product_code, $mbr_code, $phone_driver, $ip_address){
        try{
            DB::beginTransaction();
            date_default_timezone_set('Asia/Makassar');
            $sys_date = date("Y-m-d H:i:s");

            //SELECT EP_DATA BY PRODUCT CODE
            $data_ep = data_ep_mv::select('product_kode')->where("product_kode", '=', $product_code)->first();
            if(!$data_ep){
                throw new Exception("PRODUK TIDAK DITEMUKAN");
            }

            //GET CURRENT AMOUNT MEMBER SALDO
            $mbr_saldo = mbr_saldo_m::select('mbr_amount')->where("mbr_code", '=', $mbr_code)->first();
            if(!$mbr_saldo){
                throw new Exception("MBR SALDO TIDAK DITEMUKAN");
            }
            if($mbr_saldo->mbr_amount < $data_ep->harga_jual){
                throw new Exception("SALDO TIDAK CUKUP UNTUK PEMBELIAN PRODUK INI");
            }

            //GET CURRENT AMOUNT DRIVER OPERASIONAL
            $current_saldo_driver = mbr_list_m::select('mbr_list.mbr_code', 'B.mbr_amount')
                ->join('mbr_driver_saldo_operasional AS B', 'B.mbr_code', '=', 'mbr_list.mbr_code')
                ->where('mbr_list.mbr_mobile', '=', $phone_driver)
                ->first();
            if(!$current_saldo_driver){
                throw new Exception("SALDO DRIVER OPERASIONAL TIDAK DITEMUKAN");
            }

            //PERHITUNGAN BONUS
            $harga_jual = $data_ep->harga_jual;
            $bonus = $data_ep->epoint;
            $harga_beli = $data_ep->harga_jual - $bonus;
            $profit = $bonus * 0.6;
            $reward = $profit * 0.1;
            $profit_perusahaan = $profit - $reward;
            $profit_share = $bonus * 0.4;

            //GENERATE INVOICE CODE
            $invoice = "TRX/".date("Y/m/d")."/".strtoupper(uniqid());

            //INSERT TABEL TRANSACTION
            $transaksi = new transaksi_m;
            $transaksi->trx_id              = "0";
            $transaksi->tgl_trx             = $sys_date;
            $transaksi->tgl_sukses          = $sys_date;
            $transaksi->sender              = "OTU";
            $transaksi->mbr_code            = $mbr_code;
            $transaksi->product_kode        = $product_code;
            $transaksi->transaksi_status    = "Active";
            $transaksi->transaksi_jalur     = 4;
            $transaksi->harga_jual          = $harga_jual;
            $transaksi->harga_beli          = $harga_beli;
            $transaksi->supliyer_id         = $data_ep->supliyer_id;
            $transaksi->tujuan              = $current_saldo_driver->mbr_code;
            $transaksi->vsn                 = "";
            $transaksi->opr                 = "";
            $transaksi->partner_trxid       = "";
            $transaksi->transaksi_profit    = $profit_share;
            $transaksi->resp                = "";
            $transaksi->transaksi_desc      = "TRX via OTU senilai " . $data_ep->harga_jual;
            $transaksi->transaksi_code      = $invoice;
            $transaksi->h2h_partner_trxid   = "";
            $transaksi->inbox_code          = $invoice;
            $transaksi->biaya_admin         = 0;
            $transaksi->bonus_send          = "Waiting";
            $transaksi->profit_perusahaan   = $profit_perusahaan;
            $transaksi->reward              = $reward;
            $transaksi->transaksi_order     = 0;
            $transaksi->id_inquiry          = "";
            $transaksi->transaksi_type      = "TRANSAKSI";
            $transaksi->nama_pelanggan      = "";
            $transaksi->resp_payment        = NULL;
            $transaksi->save();

            //INSERT TABEL DEPOSIT_DRIVER_OPERASIONAL
            $deposit_driver_op = new deposit_driver_operasional_m;
            $deposit_driver_op->deposit_code    = "DEP/".date("Y/m/d")."/".strtoupper(uniqid());
            $deposit_driver_op->mbr_code        = $current_saldo_driver->mbr_code;
            $deposit_driver_op->deposit_amount  = $harga_beli;
            $deposit_driver_op->deposit_status  = "Active";
            $deposit_driver_op->deposit_date    = $sys_date;
            $deposit_driver_op->approve_date    = $sys_date;
            $deposit_driver_op->opr             = "System";
            $deposit_driver_op->ip_addrs        = $ip_address;
            $deposit_driver_op->codeunix        = 0;
            $deposit_driver_op->deposit_bank    = $mbr_code;
            $deposit_driver_op->desc_deposit    = "DEPOSIT via OTU TRANSAKSI senilai ". $harga_beli ." invoice = ".$invoice;
            $deposit_driver_op->refferency_code = $invoice;
            $deposit_driver_op->deposit_type    = "";
            $deposit_driver_op->save();

            //UPDATE TABEL MBR SALDO
            $update_mbr_saldo = mbr_saldo_m::where('mbr_code', '=', $mbr_code)
                ->update(array(
                    "mbr_amount"        => $mbr_saldo->mbr_amount - $harga_jual,
                    "update_identitas"  => "TRANSAKSI OTU SENILAI " . $harga_jual . " invoice " . $invoice,
                    "sts_triger"        => "Active",
                    "refid"             => $invoice
                ));

            //UPDATE TABEL SALDO OPERASIONAL
            $update_mbr_saldo_operasional = mbr_driver_saldo_operasional_m::where('mbr_code', '=', $mbr_code)
                ->update(array(
                    "mbr_amount"        => $current_saldo_driver->mbr_amount - $harga_beli,
                    "update_identitas"  => "DEPOSIT SALDO MELALUI OTU SENILAI " . $harga_beli . " invoice " . $invoice,
                    "sts_triger"        => "1",
                    "refid"             => $invoice
                ));

            //INSERT TO LOG UANG
            $log_uang = new log_uang_m;
            $log_uang->mbr_code         = $mbr_code;
            $log_uang->uang_code        = "DEB/".date("Y/m/d")."/".strtoupper(uniqid());
            $log_uang->uang_amount      = $mbr_saldo->mbr_amount - $harga_jual;
            $log_uang->uang_masuk       = 0;
            $log_uang->uang_keluar      = $harga_jual;
            $log_uang->uang_desc        = "DEPOSIT DRIVER OPERASIONAL sebesar " . $harga_jual . " Ke DRIVER dengan id = ".$current_saldo_driver->mbr_code;
            $log_uang->uang_date        = $sys_date;
            $log_uang->uang_status      = "Active";
            $log_uang->reverenci_code   = $invoice;
            $log_uang->save();

            DB::commit();
            return "SUCCESS";
        }catch(Exception $e){
            DB::rollback();
            return $e->getMessage();
            // return 'KESALAHAN SISTEM. SILAHKAN COBA LAGI NANTI';
        }
    }

    public function sendJson($data, $url, $timeout){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        return curl_exec($ch);
    }

}
