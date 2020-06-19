<?php

namespace App\Http\Controllers\transaksi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Repositories\HelperInterface as Helper;
use DB;
use App\Http\Models\transaksi_m;
use Auth;


class AntrianTransaksiController{

    function __construct(Helper $helper){
        $this->helper = $helper;
        config(['app.timezone' => 'Asia/Makassar']);
    }


    public function index(){

        return view('page.transaksi.antrian_transaksi');
    }

    // Conver integer to rupiah
    function convertCurrency($n) {
        return 'Rp ' . number_format($n,0,',','.');
    }

    function getData(){
        $posts = DB::table('antrean_transaksi')
                    ->where('transaksi_status','=','Waiting')
                    ->orWhere('transaksi_status', '=', 'Onproses')
                    ->orderBy('tgl_trx')
                    ->get();

        $supplier = DB::table('supliyer')
                    ->select(DB::Raw('supliyer_id, supliyer_name'))
                    ->where('supliyer_status', 'Active')
                    ->get();

        return view('page.transaksi.antrian_transaksi', compact('posts', 'supplier'));
    }

    //
    function updateStatusTransaksi(Request $request){

        // Request
        $idtrx      = $request->id_trx;
        $ket        = $request->alasan;
        $typetrx    = $request->status;

        // Tambahan
        $sys_date   = date("Y-m-d H:i:s");
        $kkopr      = Auth::user()->nama;

        $member = transaksi_m::where('transaksi_id', $idtrx)->first();
        $meber_code     = $member->mbr_code;

        $hrgbeli        = $member->harga_jual;
        $transaksi_code = $member->transaksi_code;
        $transaksi_jalur= $member->transaksi_jalur;
        $product_kode   = $member->product_kode;
        $supplier_id    = $member->supliyer_id;

        //Cek Record
        $report = DB::table('transaksi')
                    ->whereNotIn('transaksi_status', ['Active', 'Gagal'])
                    ->where('transaksi_id', $idtrx)
                    ->where('product_kode', $product_kode)
                    ->count();
        if ($report > 0){

                $jalur   = 0;
                $idsup   = 0;

            // Jika Transaksi GAGAL
            if($typetrx == "GAGAL"){

                // PROSES
                DB::beginTransaction();
                try{

                    $result = DB::select("SELECT * FROM f_proses_transaksi('$sys_date','$meber_code','$typetrx','$idtrx','$ket','$kkopr','$hrgbeli', '$idsup', '$jalur')");

                DB::commit();
                $this->helper->logAction(15, $meber_code, 'Gagal');
                return json_encode([
                    "respStatus"    => TRUE,
                    "statusTRX"     => "Gagal",
                    "respMessage"   => "Transaksi Berhasil dibatalkan"
                ]);

                }catch(\exception $e){
                    DB::rollBack();

                    return json_encode([
                        "respStatus"   => FALSE,
                        "respMessage" => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
                    ]);

                }

                // Jika Transaksi RESEND
            } else if($typetrx == "RESEND"){

                $ket = "KIRIM ULANG";
                $supplier_id = $request->supplier;
                // Cek Jalur Transaksi
                $dbjalur = DB::table('jalur_transaksi')
                    ->where('supliyer_id', $supplier_id)
                    ->where('product_kode', $product_kode)
                    ->get();

                if(count($dbjalur) > 0){

                    $idsup = $dbjalur[0]->supliyer_id;
                    $jalur = $dbjalur[0]->jalur;

                    // PROSES
                    DB::beginTransaction();
                    try{

                        $result = DB::select("SELECT * FROM f_proses_transaksi('$sys_date', '$meber_code', '$typetrx', '$idtrx', '$ket', '$kkopr', '$hrgbeli', '$idsup','$jalur')");

                    DB::commit();
                    $this->helper->logAction(15, $meber_code, 'Resend');
                    return json_encode([
                        "respStatus"    => TRUE,
                        "statusTRX"     => "Resend",
                        "respMessage"   => "Transaksi berhasil dikirim kembali"
                    ]);

                    }catch(\exception $e){
                        DB::rollBack();

                        return json_encode([
                            "respStatus"   => FALSE,
                            "respMessage" => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
                        ]);

                    }

                } else {
                    // Supplier Tidak didukung
                    return json_encode([
                        "respStatus"   => FALSE,
                        "respMessage" => "Transaksi tidak didukung supplier ini"
                    ]);
                }

            } else if($typetrx == "SUKSES"){

                $ket = $request->alasan[0];
                $harga = preg_replace("/[^0-9]/", "", $request->alasan[1]);

                // Cek Emboh
                if($supplier_id == 6 || $supplier_id == 8 || $supplier_id == 12 || $supplier_id == 14){

                    $hpp_dasar = (int)$harga;

                } else {

                    $data = DB::table('vw_hpp_supliyer')->where('supliyer_id', $supplier_id)->where('product_kode', $product_kode)->get();

                    $hpp_dasar = $data[0]->hpp_dasar;

                }

                // Cek Terus
                if ($hpp_dasar <= 0){

                    return json_encode(['respStatus' => FALSE, 'respMessage' => 'HPP tidak boleh kurang atau sama dengan 0 (nol)!']);

                } else {

                    if($hpp_dasar <= $hrgbeli){

                        if($supplier_id == 6 || $supplier_id == 8 || $supplier_id == 12 || $supplier_id == 14){

                        // if($harga > 0){

                            $hrgbeli = (int)$harga;

                            // PROSES
                            DB::beginTransaction();
                            try{

                                DB::select("SELECT * FROM f_proses_transaksi('$sys_date','$meber_code','$typetrx','$idtrx','$ket','$kkopr','$hrgbeli', '$idsup', '$jalur')");

                            DB::commit();
                            $this->helper->logAction(15, $meber_code, 'Sukses');
                            return json_encode([
                                "respStatus"    => TRUE,
                                "statusTRX"     => "Sukses",
                                "respMessage"   => "Transaksi Berhasil "
                            ]);

                            }catch(\exception $e){
                                DB::rollBack();

                                return json_encode([
                                    "respStatus"   => FALSE,
                                    "respMessage" => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
                                ]);

                            }

                        } else {

                            if($supplier_id > 0 && $supplier_id != 4 && $supplier_id != 6 && $supplier_id != 8 && $supplier_id != 12 && $supplier_id != 14){

                                // PROSES
                                DB::beginTransaction();
                                try{

                                    DB::select("SELECT * FROM f_proses_transaksi('$sys_date','$meber_code','$typetrx','$idtrx','$ket','$kkopr','$hrgbeli', '$idsup', '$jalur')");

                                DB::commit();

                                return json_encode([
                                    "respStatus"    => TRUE,
                                    "statusTRX"     => "Sukses",
                                    "respMessage"   => "Transaksi Berhasil "
                                ]);

                                }catch(\exception $e){
                                    DB::rollBack();

                                    return json_encode([
                                        "respStatus"   => FALSE,
                                        "respMessage" => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
                                    ]);

                                }

                            } else {
                                return json_encode(['respStatus' => FALSE, 'respMessage' => 'Tidak ada data HPP!']);
                            }

                        }

                    } else {
                        return json_encode(['respStatus' => FALSE, 'respMessage' => 'HPP tidak boleh melebihi harga jual!']);
                    }

                }

            }

        } else {
                // Invoice Sudah Pernah divalidasi
            return json_encode([
                "respStatus"   => FALSE,
                "respMessage" => "Invoice Sudah Pernah di Validasi!"
            ]);
        }

    }

}
