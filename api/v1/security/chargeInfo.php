<?php

include("/usr/share/nginx/html/eclimo.lits.com.my/db_entry.php");
include("functions.php");
include("chargePayCredits.php");
// include("/backend/mailers.php");
// include("/backend/notifiers.php");
date_default_timezone_set("Asia/Singapore");

if ($_SERVER["REQUEST_METHOD"] == "POST" ) {

    $error = [];
    $response['statusCode'] = "400";
    $response['status'] = "ERROR";
    $response['statusMessage'] = "Error";
    $jsonReqUrl  = "php://input";
    $reqjson = file_get_contents($jsonReqUrl);
    $post = json_decode($reqjson, true);

    $email = mysqli_real_escape_string($con, $post['email']);
    $username = mysqli_real_escape_string($con, $post['username']);
    $carplate = mysqli_real_escape_string($con, $post['vehicle']);
    $payid = mysqli_real_escape_string($con, $post['chargeId']);
    // $qr = "https://c.lits.my/d.php?qr=TGlUU0ZDfEZDREVWMDA3fDE2NjkwMTE3NDN8MC4yMHxFVk98U2hhaCBBbGFt|a146b5";

    $sql = "SELECT id,username,credit_balance FROM user_info WHERE username='${username}' AND email='${email}' LIMIT 1;";
    $user = mysqli_fetch_array(mysqli_query($con, $sql));
    $sql = "SELECT id,vehicle_plate,pay_status,qrcode FROM charge_session WHERE userid='$user[0]' AND vehicle_plate='${carplate}' AND paid_id='${payid}' AND pay_status='START' LIMIT 1;";
    $charge = mysqli_fetch_array(mysqli_query($con, $sql));
    $timestamp = date('Y-m-d H:i:s');

    // Validation
    if(empty($user)) array_push($error,'Username Not Found');
    if(empty($carplate)) array_push($error,'Vehicle Number Not Found');
    if(empty($charge)) array_push($error, 'Charge Session Not Found');

    // CURL to LITS Charger Server
    $send = [
        "id" => "eclimo",
        "secret" => "eclimo@2023",
        "timestamp" => time(),
        "code" => $charge[3],
        "license" => "NDMzMzIwOTc5OTMwNDgyOTAzNjc3",
        "car_number" => $carplate,
        "customer_id" => $user[0],
        "lang" => 0,
        "scan_type" => "EV_OFF",
    ];

    $ch = curl_init('https://flexicharging.lits.com.my/charger_on_off.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($send));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'FlexiEclimo');
    $return = curl_exec($ch);
    $return = json_decode($return,true);
    curl_close($ch);

    // Logging Activity
    $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'user_charge_stop_api','".json_encode($send)."','".json_encode($return)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/charge.log", $ip, FILE_APPEND);

    $return = [
        'msg' => "",
        'price' => "",
        'cost' => "0.50",
        'receipt' => "",
        'power' => "",
        'temperature' => "",
        'total_time' => "",
        'location' => "",
        'scan_type' => "",
        'charger_type' => "",
        'charger_status' => "",
        'charger_name' => "",
        'status' => "",
        'car_number' => "",
        'start_time' => "",
    ];

    // Proceed
    if(empty($error)){
        $cost = $return['cost'];
        $sql = "UPDATE charge_session SET end_dt='${timestamp}', need_credits=${cost}, pay_status='STOP' WHERE paid_id='${payid}';";
        if ($con->query($sql)) {
            $payment = payChargeCredits($con,$user[0],$payid);
            $cost = number_format((float)$cost, 2, '.', '');
            $response['status'] = "OK";
            $response['statusCode'] = "200";
            $response['statusMessage'] = "${carplate} Has Stopped Charging (-${cost} Credits)";
            $response['data']['credits'] = $cost;
            
        } else {
            $response['statusMessage'] = "Connection ${location} has failed, please try again";
        }
    } else {
        $response['statusMessage'] = join(",",$error);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    // Logging Activity
    $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'user_charge_stop','".json_encode($post)."','".json_encode($response)."','".json_encode($payment)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/charge.log", $ip, FILE_APPEND);

}