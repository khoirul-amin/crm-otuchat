<?php

namespace App\Http\Controllers\product;

use Illuminate\Http\Request;
use App\Http\Models\list_product_m;
use App\Repositories\HelperInterface as Helper;
use Illuminate\Support\Facades\Validator;
use DB;

class ListProductController{
    
    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        $data_provider = DB::table('provider')->get();
        $data_provider1= DB::table('provider')->get();
        $data_type_product = DB::table('product')->distinct('type_product')->get();
        $data_type_product1 = DB::table('product')->distinct('type_product')->get();
        return view('page.product.list_product', compact('data_provider','data_type_product','data_provider1','data_type_product1'));
    }


    // Get Product
    public function getAllProduct(Request $request){
        // dd($request->all());
        // dd($request->status_product);
        $columns = array(
            0 =>'product_date',
            1 => 'product_name',
            2 => 'type_product',
            3 => 'harga_jual',
            4 => 'h2h_code',
            5 => 'product_status',
        );

        $totaldata = list_product_m::count();
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->kolom)){
            $posts = DB::table('vw_produck_all')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            // Total Data
            $totalFiltered = $totaldata;
        } else {
            $kolom = $request->kolom;
            $kata_kunci = $request->kata_kunci;
            $status = $request->status;
            if($status == ""){
                $data_search = DB::table('vw_produck_all')
                    ->where($kolom, 'ilike', "%{$kata_kunci}%")
                    ->orderBy($order, $dir);
                    $totalFiltered = count($data_search->get());
                    $posts = $data_search
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }else{
                $data_search = DB::table('vw_produck_all')
                    ->where($kolom, '=', $kata_kunci)
                    ->where('product_status', '=', $status)
                    ->orderBy($order, $dir);
                    $totalFiltered = count($data_search->get());
                    $posts = $data_search
                    ->offset($start)
                    ->limit($limit)
                    ->get();
            }
            
        }
        $data = array();
        if($posts){
            foreach($posts as $row){
                // $dbprovider = DB::table('provider')->where('provider_id', $row->provider_id)->first();
                $vw_epoint = DB::table('vw_data_ep')->where('product_kode', $row->product_kode)->first();
                if(!$vw_epoint){
                    $epoint = '-';
                }else{
                    $epoint =  $vw_epoint->epoint;
                }

                if ($row->harga_h2h) $harga_h2h = number_format($row->harga_h2h).' <br><b>Admin Supplier: </b>'.number_format($row->admin_supplier);
                else $harga_h2h = '-'; 
                    

                $status='';
                if($row->product_status == 'Active'){
                    $status='<span class="badge badge-success">Active</span>';
                }elseif($row->product_status == 'Block'){
                    $status='<span class="badge badge-danger">Block</span>';
                }else{
                    $status='<span class="badge badge-warning">Inactive</span>';
                };
                if ($row->product_image != NULL && $row->product_image != "-") $image = "<button type=button class='btn btn-xs btn-success' onclick=\"showBanner('$row->product_image')\"><i class='mdi mr-2 mdi-eye'></i> Lihat Logo</button>";
                else $image = "<font class='text-danger'>-</font>";

                $nestedData['action'] = '  <button class="btn btn-warning btn-sm"data-toggle="modal" data-target="#updateDataProduct" onClick="getProductById(\'' . $row->product_kode . '\')"><i class="mdi mr-2 mdi-settings" ></i>Ubah</button>
                                        <button class="btn ml-3 btn-danger btn-sm" onClick="deleteProduct(\'' . $row->product_kode . '\')" id="hapus"><i class="mdi mr-2 mdi-close"></i>Hapus</button>
                                    ';
                $nestedData['info_produk'] = '<span style="font-weight:bold" class="mr-2">Nama :</span>'.$row->product_name.
                                            '<br/><span class="mr-2" style="font-weight:bold">Kode Reguler :</span>'.$row->product_kode.
                                            '<br/><span class="mr-2" style="font-weight:bold">Kode H2H :</span>'.$row->h2h_code;

                $nestedData['provider'] = '<span style="font-weight:bold" class="mr-2">Provider :</span>'.$row->provider_name.
                                            '<br/><span class="mr-2" style="font-weight:bold">Tipe Produk :</span>'.$row->type_product.
                                            '<br/><span class="mr-2" style="font-weight:bold">Gambar Produk :</span>'.$image;
                $nestedData['harga'] = '<span style="font-weight:bold" class="mr-2">Harga Jual :</span>'.$this->convertCurency($row->harga_jual).
                                            '<br/><span class="mr-2" style="font-weight:bold">EP :</span>'.$epoint;
                $nestedData['harga_h2h'] = '<span style="font-weight:bold" class="mr-2">Harga Jual :</span>'.$harga_h2h;
                $nestedData['product_status'] = '<span style="font-weight:bold" class="mr-2">Status :</span>'.$status.
                                            '<br/><span class="mr-2" style="font-weight:bold">Tanggal Penambahan :</span>'.$row->product_date;
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

    function addProduct(Request $request){
        $validatedData = Validator::make($request->all(),[
            'product_kode' => 'required',
            'product_name' => 'required',
            'h2h_code' => 'required',
            'harga_jual' => 'required',
            'product_status' => 'required',
            'type_product' => 'required',
            'provider_id' => 'required',
            'nominal_id' => 'required',
            'harga_h2h' => 'required',
            'admin_supplier' => 'required',
            'disc' => 'required',
            'product_image' => 'required'
        ]);
        

        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $product_kode = strtoupper($request->product_kode);
            $h2h_code = strtoupper($request->h2h_code);
            $res = DB::table('product')->insert([
                'product_kode' => $product_kode,
                'product_name' => $request->product_name,
                'h2h_code' => $h2h_code,
                'harga_jual' => $request->harga_jual,
                'product_status' => $request->product_status,
                'product_date' => date("Y-m-d H:i:s"),
                'type_product' => $request->type_product,
                'provider_id' => $request->provider_id,
                'nominal_id' => $request->nominal_id,
                'product_image' => $request->product_image,
                'disc' => $request->disc,
                'harga_h2h' => $request->harga_h2h,
                'admin_supplier' => $request->admin_supplier
            ]);
            if($res){
                $this->helper->logAction(3, $product_kode, 'insert');
                return json_encode(array(
                    'status' => TRUE, 'message' => 'Data Telah Ditambah!'
                ));
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data Gagal Ditambah!'
                ));
            }
        }
    }

    function getProductById($id){
        $product = DB::table('product')->where('product_kode', $id)->first();
        if($product){
            return response()->json($product);
        }
    }

    function updateProduct(Request $request){
        $validatedData = Validator::make($request->all(),[
            'product_name' => 'required',
            'product_kode' => 'required',
            'h2h_code' => 'required',
            'harga_jual' => 'required',
            'product_status' => 'required',
            'type_product' => 'required',
            'provider_id' => 'required',
            'nominal_id' => 'required',
            'harga_h2h' => 'required',
            'admin_supplier' => 'required',
            'disc' => 'required',
            'product_image' => 'required'
        ]);
        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            $product_kode = strtoupper($request->product_kode);
            $h2h_code = strtoupper($request->h2h_code);
            $res = DB::table('product')->where('product_kode', $request->product_kode)->update([
                'product_kode' => $product_kode,
                'product_name' => $request->product_name,
                'h2h_code' => $h2h_code,
                'harga_jual' => $request->harga_jual,
                'product_status' => $request->product_status,
                'type_product' => $request->type_product,
                'provider_id' => $request->provider_id,
                'nominal_id' => $request->nominal_id,
                'product_image' => $request->product_image,
                'disc' => $request->disc,
                'harga_h2h' => $request->harga_h2h,
                'admin_supplier' => $request->admin_supplier
            ]);
            if($res){
                $this->helper->logAction(3, $request->product_kode, 'update');
                return json_encode(array(
                    'status' => TRUE, 'message' => 'Data Telah Diubah!'
                ));
            }else{
                return json_encode(array(
                    'status' => FALSE, 'message' => 'Data Gagal Diubah!'
                ));
            }
        }
    }

    function deleteProduct(Request $request){
        $data = DB::table('product')->where('product_kode',$request->id)->first();
        $res = DB::table('product')->where('product_kode',$request->id)->delete();
        
        if($res){
            $this->helper->logAction(3, $data->product_kode, 'delete');
            return json_encode(array(
                'status' => TRUE, 'message' => 'Data Telah Terhapus!'
            ));
        }else{
            return json_encode(array(
                'status' => FALSE, 'message' => 'Data Gagal Dihapus!'
            ));
        }
    }
    // Conver integer to rupiah
    function convertCurency($n) {
        $hasil_rupiah = number_format($n,0,',','.');
        return $hasil_rupiah;
    }
}
