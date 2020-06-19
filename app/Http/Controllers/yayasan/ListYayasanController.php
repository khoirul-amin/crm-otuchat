<?php

namespace App\Http\Controllers\yayasan;

use Illuminate\Http\Request;
use App\Http\Models\yayasan_m;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Repositories\HelperInterface as Helper;
use Illuminate\Support\Facades\URL;
use DB;

class ListYayasanController{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }

    public function index(){
        return view('page.yayasan.list_yayasan');
    }

    // Get yayasan
    public function getDataYayasan(Request $request){

        // dd($request->column);
        $columns = array(
            0 =>'id',
            1 =>'nama',
            2 =>'email',
            3 =>'nama_penanggung_jawab',
            4 =>'alamat',
        );


        // Total Data
        $totaldata = count(DB::table('yayasan')->get());
        // Limit
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir   = $request->input('order.0.dir');

        if (empty($request->column)){
            // $posts = list_product_m::select('*')
            $posts = DB::table('yayasan')
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy($order, $dir)
                    ->get();
            $totalFiltered = $totaldata;
        } else {
            // dd($request->column, $request->value);
            $kolom_search = $request->column;
            $kolom_value = $request->value;
            $status = $request->status;
            $posts = DB::table('yayasan')
                    ->where($kolom_search, 'like', "%{$kolom_value}%")
                    ->offset($start)
                    ->limit($limit)
                    ->orderBy('id', 'ASC')
                    ->get();
            $totalFiltered = $totaldata;
        }

          // dd($posts);
          $data = array();
        
          if($posts){
              foreach($posts as $row){
                // $saldo = DB::table('yayasan_saldo')->where('id_yayasan', $row->id)->first();
                if ($row->image != NULL && $row->image != "-") $image = "<button type=button class='btn btn-xs btn-success' onclick=\"showBanner('$row->image')\"><i class='mdi mr-2 mdi-eye'></i> Lihat Logo</button>";
                else $image = "<font class='text-danger'>-</font>";

                $nestedData['action'] = '<button class="btn btn-warning btn-sm" onClick="getYayasanById(\'' . $row->id . '\')" data-toggle="modal" data-target="#UpdateYayasan"><i class="mdi mr-2 mdi-settings"></i>Ubah</button>
                <button class="btn ml-2 btn-success btn-sm" onClick="getSaldoById(\'' . $row->id . '\')" data-toggle="modal" data-target="#CekSaldo"><i class="mdi mr-2 mdi-settings"></i>Cek Saldo</button>
                                        <button class="btn ml-2 btn-danger btn-sm" onClick="deleteYayasan(\'' . $row->id . '\')" id="hapus"><i class="mdi mr-2 mdi-close"></i>Hapus</button>
                                        ';
                $nestedData['profil'] = '<span style="font-weight:bold" class="mr-2">Nama Yayasan :</span>'.$row->nama.'<br>
                                        <span style="font-weight:bold" class="mr-2">Nomor Akta :</span>'.$row->nomor_akta.'<br>
                                        <span style="font-weight:bold" class="mr-2">NPWP :</span>'.$row->npwp_yayasan.'<br>';

                $nestedData['akun'] =   '<span style="font-weight:bold" class="mr-2">Phone :</span>'.$row->phone.'<br>
                                        <span style="font-weight:bold" class="mr-2">Email :</span>'.$row->email.'<br>
                                        <span style="font-weight:bold" class="mr-2">Logo :</span>'.$image;

                $nestedData['penanggung_jawab'] =   '<span style="font-weight:bold" class="mr-2">Penanggung Jawab :</span>'.$row->nama_penanggung_jawab.'<br>
                                                    <span style="font-weight:bold" class="mr-2">KTP :</span>'.$row->ktp_penanggung_jawab.'<br>
                                                    <span style="font-weight:bold" class="mr-2">NPWP :</span>'.$row->npwp_penanggung_jawab.'<br>';

                $nestedData['alamat'] =   '<span style="font-weight:bold" class="mr-2">Kelurahan :</span> '.$row->Kelurahan.' Rt:'.$row->RT.' / Rw:'.$row->RW.'<br>
                                        <span style="font-weight:bold" class="mr-2">Kecamatan :</span>'.$row->kecamatan.'<br>
                                        <span style="font-weight:bold" class="mr-2">kabupaten / Kota :</span>'.$row->kota.'<br>';

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

    function getSaldoById($id){
        $res = DB::table('yayasan_saldo')->where('id_yayasan', $id)->first();
        if($res){
            return response()->json($res);
        }
    }

    public function addYayasan(Request $request){
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'email' => 'required',
            'nama' => 'required',
			'phone' => 'required',
			'alamat' => 'required',
			'nama_penanggung_jawab' => 'required',
			'ktp_penanggung_jawab' => 'required',
			'npwp_penanggung_jawab' => 'required',
			'npwp_yayasan' => 'required',
			'RT' => 'required',
			'RW' => 'required',
			'kelurahan' => 'required',
			'kecamatan' => 'required',
			'kota' => 'required',
			'kode_pos' => 'required',
			'nomor_akta' => 'required',
			'tgl_akte' => 'required',
            'nama_singkat' => 'required',
            // 'key' => 'required',
			// 'image' => $request->image,
			'deskripsi' => 'required'
        ]);
        
        // Upload Image
        $file = $request->file('image');
        $nama_file = $file->getClientOriginalName();

        $image_yayasan = uniqid('yayasan_' . rand(100,999)) . '.' . $file->getClientOriginalExtension();
        $file->move(\base_path() .'/public/assets/imageupload/yayasan', $image_yayasan);

        // dd(URL::current().'/'.$image_yayasan);
        $nama_image = "/assets/imageupload/yayasan/".$image_yayasan;
        
        // Generate Password
        $passwordDefault = uniqid();
        $password = Hash::make($passwordDefault);

        $date = $request->tgl_akte.' 00:00:00';

        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            DB::table('yayasan')->insert([
                'nama' => $request->nama,
                'email' => $request->email,
                'phone' => $request->phone,
                'alamat' => $request->alamat,
                'password' => $passwordDefault,
                'nama_penanggung_jawab' => $request->nama_penanggung_jawab,
                'ktp_penanggung_jawab' => $request->ktp_penanggung_jawab,
                'npwp_penanggung_jawab' => $request->npwp_penanggung_jawab,
                'npwp_yayasan' => $request->npwp_yayasan,
                'RT' => $request->RT,
                'RW' => $request->RW,
                'Kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'kode_pos' => $request->kode_pos,
                'nomor_akta' => $request->nomor_akta,
                'tanggal_akta' => $date,
                'nama_singkatan' => $request->nama_singkat,
                'image' => $nama_image,
                'deskripsi' => $request->deskripsi
            ]);
            
            $yayasan_new = DB::table('yayasan')->max('id');
            // dd($yayasan_new);
            // Update Yayasan Saldo
            DB::table('yayasan_saldo')->insert(['id_yayasan' => $yayasan_new]);

            $to = $request->email;
            $subject = 'SELAMAT DATANG DI OTUCHAT';
            $view = 'mails.mail_yayasan';
            $param = array(
                "email" => $request->email,
                "password" => $passwordDefault,
                "subject" => $subject
            );

            $this->helper->sendEmail($request->email, $subject, $view, $param);

            $this->helper->logAction(1, $request->nama, 'insert');
            return json_encode(array(
                'status' => TRUE, 'message' => 'Proses penambahan data berhasil!'
            ));
        }
    }

    public function deleteYayasan(Request $request){
        $data = DB::table('yayasan')->where('id',$request->id)->first();
        $res = DB::table('yayasan')->where('id',$request->id)->delete();
        if($res){
            DB::table('yayasan_saldo')->where('id_yayasan',$request->id)->delete();
            $this->helper->logAction(1, $data->nama, 'delete');
            return json_encode(array(
                'status' => TRUE, 'message' => 'Data Telah Terhapus!'
            ));
        }else{
            return json_encode(array(
                'status' => FALSE, 'message' => 'Data Gagal Terhapus!'
            ));
        }
        
    }

    public function updateYayasan(Request $request){
        $validatedData = Validator::make($request->all(),[
            'nama' => 'required',
            'email' => 'required',
            'nama' => 'required',
			'phone' => 'required',
			'alamat' => 'required',
			'nama_penanggung_jawab' => 'required',
			'ktp_penanggung_jawab' => 'required',
			'npwp_penanggung_jawab' => 'required',
			'npwp_yayasan' => 'required',
			'RT' => 'required',
			'RW' => 'required',
			'kelurahan' => 'required',
			'kecamatan' => 'required',
			'kota' => 'required',
			'kode_pos' => 'required',
			'nomor_akta' => 'required',
			'tgl_akte' => 'required',
            'nama_singkat' => 'required',
			'deskripsi' => 'required'
        ]);

        $date = $request->tgl_akte.' 00:00:00';
        

        if ($validatedData->fails()) {
            return json_encode(array(
                'status' => FALSE, 'message' => 'Lengkapi data terlebih dahulu!'
            ));
        }else{
            DB::table('yayasan')->where('id', $request->id)
                ->update([
                'nama' => $request->nama,
                'email' => $request->email,
                'phone' => $request->phone,
                'alamat' => $request->alamat,
                'nama_penanggung_jawab' => $request->nama_penanggung_jawab,
                'ktp_penanggung_jawab' => $request->ktp_penanggung_jawab,
                'npwp_penanggung_jawab' => $request->npwp_penanggung_jawab,
                'npwp_yayasan' => $request->npwp_yayasan,
                'RT' => $request->RT,
                'RW' => $request->RW,
                'Kelurahan' => $request->kelurahan,
                'kecamatan' => $request->kecamatan,
                'kota' => $request->kota,
                'kode_pos' => $request->kode_pos,
                'nomor_akta' => $request->nomor_akta,
                'tanggal_akta' => $date,
                'nama_singkatan' => $request->nama_singkat,
                // 'image' => $request->image,
                'deskripsi' => $request->deskripsi
            ]);
            
            $this->helper->logAction(1, $request->nama, 'update');
            return json_encode(array(
                'status' => TRUE, 'message' => 'Data Telah Terupdate!'
            ));
        }
    }

    function getYayasanById($id){
        $yayasan = DB::table('yayasan')->where('id', $id)->first();

        if($yayasan){
            return response()->json($yayasan);
        }
        
        
    }
}