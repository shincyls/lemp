<?php

include("/usr/share/nginx/html/eclimo.lits.com.my/db_entry.php");
include("functions.php");
// include("/backend/mailers.php");
// include("/backend/notifiers.php");
date_default_timezone_set("Asia/Singapore");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $error = [];
    $fakeTopup = true;
    $response['statusCode'] = "400";
    $response['status'] = "ERROR";
    $response['data'] = [];
    $jsonReqUrl  = "php://input";
    $reqjson = file_get_contents($jsonReqUrl);
    $post = json_decode($reqjson, true);

    $email = mysqli_real_escape_string($con, $post['email']);
    $username = mysqli_real_escape_string($con, $post['username']);
    $topup = mysqli_real_escape_string($con, $post['value']);
    $method = mysqli_real_escape_string($con, $post['method']);
    $sql = "SELECT id,username,credit_balance FROM user_info WHERE username='${username}' AND email='${email}' LIMIT 1;";
    $get = mysqli_fetch_array(mysqli_query($con, $sql));
    $after = $get[2] + $topup;

    $bill = mysqli_fetch_array(mysqli_query($con, "SELECT count(id) as cnt FROM topup_stat WHERE YEAR(created_on)=YEAR(CURDATE()) AND MONTH(created_on)=MONTH(CURDATE());"));
    $transid = "EC".((new DateTime())->format("ymdHi")).sprintf("%04d", $bill[0]+1);

    // Validation
    if($topup<5) array_push($error,'Cannot Topup Less Than RM5');
    else if(empty($get)) array_push($error,'Username Not Found');

    $pay = [
        'merchant'=>'M14502',
        'remarks'=> $transid,
        'bank_info'=>'ECLIMO',
        'payment_id'=> $transid,
        'ecurrency'=>'MYR',
    ];

    $payment = [];
    $data = [];
    $tcost = 0;
    $bank = '';
    $lits = 0;
    $operator = 0;
    if($method=='Online Banking'){
        $payment = [
            'MerchantCode' => $pay['merchant'],
            'RefNo' => $transid,
            'Amount' => $topup,
            'Currency' => 'MYR',
            'PaymentId' => 2,
            'ProdDesc' => 'Flexi Eclimo TopUp',
            'UserName' => $username,
            'UserEmail' => $email,
            'UserContact' => '0123456789',
            'Remark' => 'Flexi Eclimo '.$username,
            'Lang' => 'UTF-8',
            'SignatureType' => 'SHA256',
            'Signature' => '',
            'ResponseURL' => 'https://eclimo.lits.com.my/ipay/success',
            'BackendURL' => 'https://eclimo.lits.com.my/ipay/update'
        ];
        $Amount = str_replace(",","",$payment['Amount'])*100;
        $strHash = 'mCCSUh0MnB'.$payment['MerchantCode'].$payment['RefNo'].$Amount."MYR";
        $payment['Signature'] = hash('sha256', $strHash);
        $bank = 'IPAY88';
        $tcost = round($payment['Amount']*0.0145,2);
        $data = [
            'MerchantCode'=> $payment['MerchantCode'],
            'RefNo'=> $payment['RefNo'],
            'Amount'=> $payment['Amount'],
            'Currency'=> $payment['Currency'],
            'PaymentId'=> $payment['PaymentId'],
            'ProdDesc'=> $payment['ProdDesc'],
            'UserName'=> $payment['UserName'],
            'UserEmail'=> $payment['UserEmail'],
            'UserContact'=> $payment['UserContact'],
            'Remark'=> $payment['Remark'],
            'Lang'=> $payment['Lang'],
            'SignatureType'=> $payment['SignatureType'],
            'Signature'=> $payment['Signature'],
            'ResponseURL'=> $payment['ResponseURL'],
            'BackendURL'=> $payment['BackendURL']
        ];
        $lits = round(($topup - $tcost)*0.1,2);
        $operator = round(($topup - $tcost)*0.9,2);
    }
    else if($method=='Credit/Debit Card'){
        $payment = [
            'MerchantCode' => $pay['merchant'],
            'RefNo' => $transid,
            'Amount' => $topup,
            'Currency' => 'MYR',
            'ProdDesc' => 'Flexi Eclimo TopUp',
            'UserId' => $username,
            'Remark' => 'Flexi Eclimo '.$username,
            'Lang' => 0,
            'SignatureType' => 'SHA256',
            'Signature' => '',
            'ResponseURL' => 'https://eclimo.lits.com.my/pay/fpx',
            'BackendURL' => 'https://eclimo.lits.com.my/fpx_receive'
        ];
        $Amount = str_replace(",","",$payment['Amount'])*100;
        $strHash = 'mCCSUh0MnB'.$payment['MerchantCode'].$payment['RefNo'].$Amount."MYR";
        $payment['Signature'] = hash('sha256', $strHash);
        $bank = 'LITS FPX';
        $tcost = 0.50;
        $data = [
            'RefNo' => $payment['RefNo'],
            'Amount' => $payment['Amount'],
            'Currency' => $payment['Currency'],
            'ProdDesc' => $payment['ProdDesc'],
            'UserId' => $payment['UserId'],
            'Remark' => $payment['Remark'],
            'Lang' => $payment['Lang']
        ];
        $lits = round(($topup - $tcost)*0.1,2);
        $operator = round(($topup - $tcost)*0.9,2);
    }

    // Proceed
    // Migrate To IPAY/FPX Receive
    if(empty($error) && $fakeTopup){

        $sql = "INSERT INTO topup_stat (userid,username,balance_before,balance_after,topup_value,purchase,realtime,transid,
        status,transaction_fee,location,area,bankinfo,merchantcode,refno,ecurrency,paymentid,remark,errdesc,
        signature,portal,lits,operator,gst,discount,authcode,paid_time,exported,export_time,purchase_type,transaction_type,app_web,params) 
        VALUES ($get[0],'$get[1]',$get[2],$after,$topup,$topup,NOW(),'$transid','COMPLETE','$tcost','','',
        '$bank','$pay[merchant]','$transid','$pay[ecurrency]','$pay[payment_id]','','','".$payment['Signature']."','$tcost','$lits','$operator',
        '0.00','0.00','AUTHCODE',NOW(),UNIX_TIMESTAMP(),NOW(),'mobile_topup','$method','app_web','".json_encode($data)."');";
        // array_push($error,$sql);
        
        if ($con->query($sql)) {
            $sql = "UPDATE user_info SET `credit_balance`='${after}' WHERE email='${email}';";
            if ($con->query($sql)) {
                $response['statusCode'] = "200";
                $response['status'] = "OK";
                $response['statusMessage'] = "Successfully Top-Up MYR ".number_format($topup, 2, '.', '');
                $response['data']['transid'] = $transid;
                $response['data']['real'] = "NO";
            }
            else {
                array_push($error,"Transaction Info Is Not Updated");
                $response['statusMessage'] = join(",",$error);
            }
        } else {
            $response['statusMessage'] = "Failure Top-Up MYR ".number_format($topup, 2, '.', '');
        }

    } else if(empty($error) && !$fakeTopup) {

        $sql = "INSERT INTO topup_stat (userid,username,balance_before,balance_after,topup_value,purchase,realtime,transid,
        status,transaction_fee,location,area,bankinfo,merchantcode,refno,ecurrency,paymentid,remark,errdesc,
        signature,portal,lits,operator,gst,discount,authcode,paid_time,exported,export_time,purchase_type,transaction_type,app_web,params) 
        VALUES ($get[0],'$get[1]',$get[2],$after,$topup,$topup,NOW(),'$transid','PENDING','$tcost','','',
        '$bank','$pay[merchant]','$transid','$pay[ecurrency]','$pay[payment_id]','','','".$payment['Signature']."','$tcost','$lits','$operator',
        '0.00','0.00','AUTHCODE',NOW(),UNIX_TIMESTAMP(),NOW(),'mobile_topup','$method','app_web','".json_encode($data)."');";
        // array_push($error,$sql);

        if ($con->query($sql)) {
            $response['statusCode'] = "200";
            $response['status'] = "OK";
            $response['statusMessage'] = "Successfully Top-Up MYR ".number_format($topup, 2, '.', '');
            $response['data']['transid'] = $transid;
            $response['data']['real'] = "NO";
        } else {
            $response['statusMessage'] = "Failure Top-Up MYR ".number_format($topup, 2, '.', '');
        }

    } else {
        $response['statusMessage'] = join(",",$error);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    // Logging Activity
    $ip .= ((new DateTime())->format("Y-m-d H:i:s")).",'mobile_topup_request','".json_encode($post)."','".json_encode($response)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/payment.log", $ip, FILE_APPEND);

}




            