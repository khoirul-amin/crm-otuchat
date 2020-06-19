<?php

namespace App\Http\Controllers\product;

use Illuminate\Http\Request;
use App\Http\Models\list_supplier_m;
use App\Repositories\HelperInterface as Helper;
use DB;
use Illuminate\Support\Facades\Validator;


class ListSupplierController{


    function __construct(Helper $helper){
        $this->helper = $helper;
    }


    public function index(){
        return view('page.product.list_supplier');
    }


    // Get Product
    public function getAllSupplier(Request $request){

        // dd($request->status_product);
        $columns = array(
            0 => 'supliyer_id',
            1 => 'supliyer_name',
            2 => 'user_url',
            3 => 'paswd_url',
            // 4 => 'provaider_group',
        );

        // Total Data
        $totaldata = list_supplier_m::count();
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->column)){
            // $posts = list_product_m::select('*')
            $posts = DB::table('supliyer')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();

            $totalFiltered = $totaldata;
        } else {
            $column = $request->column;
            $value = strtolower($request->value);
            $status = $request->status;

            if ($status == ""){

                $posts = DB::table('supliyer')
                    ->where($column, 'ilike', "%{$value}%")
                    ->get();

                $totaldata = count($posts);
                $totalFiltered = $totaldata;

            } else {

                $posts = DB::table('supliyer')
                        ->where($column, 'ilike', "%{$value}%")
                        ->Where('supliyer_status', '=', $status)->get();
                        // ->offset($start)
                        // ->limit($limit)
                        // ->orderBy($order, $dir);

                $totaldata = count($posts);
                $totalFiltered = $totaldata;
            }
        }
        // dd($posts);
        $data = array();

        if($posts){
            foreach($posts as $row){

                if($row->supliyer_status == 'Active'){
                    $sts = '<span class="label label-success">'.$row->supliyer_status.'</span>';
                } else {
                    $sts = '<span class="label label-danger">'.$row->supliyer_status.'</span>';
                }
                if($row->url_topup){
                    $btn_topup = '<button class="btn ml-3 btn-info btn-sm" onClick="OpenLink(\'' . $row->url_topup . '\')"><i class="mdi mr-2 mdi-eye"></i>Lihat</button>';
                }else{
                    $btn_topup = '';
                }
                if($row->url_report){
                    $btn_report = '<button class="btn ml-3 btn-info btn-sm" onClick="OpenLink(\'' . $row->url_report. '\')"><i class="mdi mr-2 mdi-eye"></i>Lihat</button>';
                }else{
                    $btn_report = '';
                }

                $nestedData['action'] ='
                        <button class="btn ml-3 btn-warning btn-sm" onClick="ubahSupplier(\'' . $row->supliyer_id . '\')"><i class="mdi mr-2 mdi-settings"></i>Ubah</button>
                        <button class="btn ml-3 btn-danger btn-sm" onClick="deleteSupplier(\'' . $row->supliyer_id . '\')" id="hapus"><i class="mdi mr-2 mdi-close"></i>Hapus</button>';

                $nestedData['detail'] ='<span style="font-weight:bold" class="mr-2">Nama : '.$row->supliyer_name.'</span><br>
                        <span style="font-weight:bold" class="mr-2">Status :</span>'.$sts;

                $nestedData['urltopup'] =$btn_topup;

                $nestedData['urlreport'] = $btn_report;


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


   function insertSupplier(Request $request){

        $validator = Validator::make($request->all(), [

            'nama' => 'required',
            'alamatip' => 'required',
            'urltopup' => 'required',
            'usernametopup' => 'required',
            'passwordtopup' => 'required',
            'urlreport' => 'required',
            'usernamereport' => 'required',
            'passwordreport' => 'required',

        ]);

        if($validator->fails()){

            return json_encode(array(
                'respStatus' => FALSE,
                "respMessage" => "Masukan semua data"
            ));
        }

        $max = list_supplier_m::max('supliyer_id');
        $nomer_id = $max+1;

        DB::beginTransaction();
        try{

            $supplier = new list_supplier_m;
            $supplier->supliyer_id      = $nomer_id;
            $supplier->supliyer_name    = $request->nama;
            $supplier->user_url         = $request->usernametopup;
            $supplier->paswd_url        = $request->passwordtopup;
            $supplier->supliyer_status  = "Active";
            $supplier->supliyer_amount  = 0;
            $supplier->supliyer_date    = date("Y-m-d H:i:s");
            $supplier->url_topup        = $request->urltopup;
            $supplier->url_report       = $request->urlreport;
            $supplier->usr_report       = $request->usernamereport;
            $supplier->pswd_report      = $request->passwordreport;
            $supplier->ip_in            = $request->alamatip;
            $supplier->save();

            $this->helper->logAction(9, $request->nama, 'insert');

            DB::commit();

            return json_encode(array(
                'respStatus' => TRUE,
                'respMessage' => 'Berhasil disimpan'
            ));

        }catch(\exception $e){

            DB::rollBack();


            return json_encode(array(
                'respStatus' => FALSE,
                'respMessage' => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
            ));

        }

   }

   function updateSupplier(Request $request){

    //dd($request->all());

    $validator = Validator::make($request->all(), [

            'nama' => 'required',
            'alamatip' => 'required',
            'urltopup' => 'required',
            'usernametopup' => 'required',
            'passwordtopup' => 'required',
            'urlreport' => 'required',
            'usernamereport' => 'required',
            'passwordreport' => 'required',
    ]);

    if($validator->fails()){


        return json_encode(array(
            'respStatus' => FALSE,
            "respMessage" => "Masukan semua data"
        ));
    }


    DB::beginTransaction();
    try{

            $supplier = list_supplier_m::where('supliyer_id', $request->id)->first();
            $supplier->supliyer_name    = $request->nama;
            $supplier->user_url         = $request->usernametopup;
            $supplier->paswd_url        = $request->passwordtopup;
            $supplier->supliyer_status  = "Active";
            $supplier->supliyer_amount  = 0;
            $supplier->supliyer_date    = date("Y-m-d H:i:s");
            $supplier->url_topup        = $request->urltopup;
            $supplier->url_report       = $request->urlreport;
            $supplier->usr_report       = $request->usernamereport;
            $supplier->pswd_report      = $request->passwordreport;
            $supplier->ip_in            = $request->alamatip;
            $supplier->save();


            $this->helper->logAction(9, $request->id, 'update');

        DB::commit();

        return json_encode(array(
            'respStatus' => TRUE,
            'respMessage' => 'Success'
        ));

    }catch(\exception $e){

        DB::rollBack();


        return json_encode(array(
            'respStatus' => FALSE,
            'respMessage' => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
        ));

    }


}

   function deleteSupplier(Request $request){

        DB::beginTransaction();
        try{

            DB::table('supliyer')->where('supliyer_id',$request->id)->delete();

            DB::commit();
            
            $this->helper->logAction(9, $request->id, 'delete');

            return json_encode(array(
                'respStatus' => TRUE,
                'respMessage' => 'Data Telah Terhapus!'
            ));

        }catch(\exception $e){

            DB::rollBack();


            return json_encode(array(
                'respStatus' => FALSE,
                'respMessage' => env("APP_DEBUG", false) ? "File : ".$e->getFile()."\nLine : ".$e->getLine()."\nCode : ".$e->getCode()."\nMessage : ".$e->getMessage() : env("ERR_SYSTEM_MESSAGE", "SYSTEM ERROR")
            ));
        }
    }

    public function getDetailSupplier($id){
        $data = DB::table('supliyer')->where('supliyer_id', '=', $id)->first();
        return response()->json($data);
    }
}
