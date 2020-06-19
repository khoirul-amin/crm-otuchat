<?php

namespace App\Http\Controllers\product;

use Illuminate\Http\Request;
use App\Http\Models\list_provider_m;
use App\Repositories\HelperInterface as Helper;
use DB;
use Illuminate\Support\Facades\Validator;


class ListProviderController{


    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.product.list_provider');
    }


    // Get Product
    public function getAllProvider(Request $request){

        // dd($request->status_product);
        $columns = array(
            0 => 'provider_id',
            1 => 'provider_name',
            2 => 'provaider_type',
            3 => 'provaider_status',
            // 4 => 'provaider_group',
        );

        // Total Data
        $totaldata = list_provider_m::count();
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->column)){
            // $posts = list_product_m::select('*')
            $posts = DB::table('provider')
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

                $posts = DB::table('provider')
                    ->where($column, 'ilike', "%{$value}%")
                    ->get();

                $totaldata = count($posts);
                $totalFiltered = $totaldata;

            } else {

                $posts = DB::table('provider')
                        ->where($column, 'ilike', "%{$value}%")
                        ->Where('provaider_status', '=', $status)->get();
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

                $nestedData['action'] ='
                        <button class="btn ml-3 btn-warning btn-sm" onClick="ubahProvider(\'' . $row->provider_id . '\')"><i class="mdi mr-2 mdi-settings"></i>Ubah</button>
                        <button class="btn ml-3 btn-danger btn-sm" onClick="deleteProvider(\'' . $row->provider_id . '\')" id="hapus"><i class="mdi mr-2 mdi-close"></i>Hapus</button>
                ';
                $nestedData[$columns[1]] = $row->provider_name;

                $nestedData[$columns[2]] = $row->provaider_type;

                $nestedData[$columns[3]] = $row->provaider_status;

                // $nestedData[$columns[4]] = $row->provaider_group;

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


   function insertProvider(Request $request){

        $validator = Validator::make($request->all(), [

            'nama' => 'required',
            'tipe' => 'required',
            'status' => 'required',
        ]);

        if($validator->fails()){

            return json_encode(array(
                'respStatus' => FALSE,
                "respMessage" => "Masukan semua data"
            ));
        }

        $max = list_provider_m::max('provider_id');
        $nomer_id = $max+1;

        DB::beginTransaction();
        try{

            $provider = new list_provider_m;
            $provider->provider_id      = $nomer_id;
            $provider->provider_name    = $request->nama;
            $provider->provider_pref    = '0';
            $provider->provaider_type   = $request->tipe;
            $provider->provaider_group  = '0';
            $provider->provaider_status = $request->status;
            $provider->save();

            $this->helper->logAction(10, $request->nama, 'insert');

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

   function updateProvider(Request $request){

    //dd($request->all());

    $validator = Validator::make($request->all(), [

        'nama' => 'required',
        'tipe' => 'required',
        'status' => 'required',
    ]);

    if($validator->fails()){


        return json_encode(array(
            'respStatus' => FALSE,
            "respMessage" => "Masukan semua data"
        ));
    }


    DB::beginTransaction();
    try{

        list_provider_m::where('provider_id', $request->id)
            ->update([
                'provider_name'       =>  $request->nama,
                'provider_pref'       =>  0,
                'provaider_type'      =>  $request->tipe,
                'provaider_group'     =>  0,
                'provaider_status'    =>  $request->status
                ]);


        $this->helper->logAction(10, $request->nama, 'update');

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

   function deleteProvider(Request $request){

        $nama = DB::table('provider')->where('provider_id',$request->id)->first();
        DB::beginTransaction();
        try{

            DB::table('provider')->where('provider_id',$request->id)->delete();

            DB::commit();
            $this->helper->logAction(10, $nama->provider_name, 'delete');

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

    public function getDetailProvider($id){
        $data = DB::table('provider')->where('provider_id', '=', $id)->first();
        return response()->json($data);
    }
}
