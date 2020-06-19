<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Session;
use App\Repositories\HelperInterface as Helper;
use App\Http\Libraries\AuthLibrary;
use Illuminate\Http\Request;

class ExampleController extends Controller
{

    function __construct(Helper $helper){
        $this->helper = $helper;
    }
    function index(){
        return view('example_key', ['QR_Image' => '', 'secret' => '']);
    }

    function sendEmail(){

        $to = 'khoirulamin605@gmail.com';
        $subject = 'WELCOME TO CRM';
        $view = 'mails.mail_users';
        $param = array(
            "email" => "khoirulamin605@gmail.com",
            "password" => "Khoirul03"
        );

        $res = $this->helper->sendEmail($to, $subject, $view, $param);
        // dd($res);
        if($res){
            echo 'SUKSES';
        }
        else{
            echo 'GAGAL';
        }
    }
    function cobaLib(){
        $secret = (new AuthLibrary())->generateRandomSecret();
        $QR_Image = (new AuthLibrary())->getQR('coba@gmail.com', $secret, 'CRM Otu');
        
        Session::put('abcdef', $secret);
        return view('example_key', compact('secret', 'QR_Image'));
    }
    function cekKey(Request $request){
        $checkResult =  (new AuthLibrary())->verifyCode(Session::get('abcdef'), $request->key, 2);    // 2 = 2*30sec clock tolerance
        dd($checkResult);
    }

    function cekRespon(){
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

        array_push($outputVal, 'respTime');
        array_push($outputVal, 'productCode');
        array_push($outputVal, 'billingReferenceID');
        array_push($outputVal, 'customerID');
        array_push($outputVal, 'customerMSISDN'); # customerMSISDN
        array_push($outputVal, 'customerName'); # customerName
        array_push($outputVal, 'period'); # period
        array_push($outputVal, 'policeNumber'); # policeNumber
        array_push($outputVal, 'lastPaidPeriod'); # lastPaidPeriod
        array_push($outputVal, 'tenor'); # tenor
        array_push($outputVal, 'lastPaidDueDate'); # lastPaidDueDate
        array_push($outputVal, 'usageUnit'); # usageUnit
        array_push($outputVal, 'penalty'); # penalty
        array_push($outputVal, 'payment');
        array_push($outputVal, 'minPayment');
        array_push($outputVal, 'maxPayment');
        array_push($outputVal, 'additionalMessage');
        array_push($outputVal, 'billing');
        array_push($outputVal, 'sellPrice');
        array_push($outputVal, 'adminBank');
        array_push($outputVal, 'profit');
$output = array_combine($outputKey, $outputVal);
            
            return response()->json(array(
                'status' => 'success',
                'reason' => $output
            ));

    }

}
