<?php

include("/usr/share/nginx/html/eclimo.lits.com.my/db_entry.php");
// include("/backend/mailers.php");
// include("/backend/notifiers.php");
date_default_timezone_set("Asia/Singapore");

if ($_SERVER["REQUEST_METHOD"] == "POST" ) {

    $error = [];
    $response['statusCode'] = "400";
    $response['status'] = "ERROR";
    $response['statusMessage'] = "Error";
    $response['data'] = [];
    $jsonReqUrl  = "php://input";
    $reqjson = file_get_contents($jsonReqUrl);
    $post = json_decode($reqjson, true);

    $email = mysqli_real_escape_string($con, $post['email']);
    $username = mysqli_real_escape_string($con, $post['username']);
    $carplate = mysqli_real_escape_string($con, $post['carNumber']);
    $location = mysqli_real_escape_string($con, $post['location']);
    $station = mysqli_real_escape_string($con, $post['station']);
    $qrcode = mysqli_real_escape_string($con, $post['qr']); //"https://c.lits.my/d.php?qr=TGlUU0ZDfEZDREVWMDA3fDE2NjkwMTE3NDN8MC4yMHxFVk98U2hhaCBBbGFt|a146b5";

    $paidid = "EC".((new DateTime())->format("ymdHis"));
    $timestamp = date('Y-m-d H:i:s');

    // Validation
    $sql = "SELECT id,username,credit_balance FROM user_info WHERE username='${username}' AND email='${email}' LIMIT 1;";
    $user = mysqli_fetch_array(mysqli_query($con, $sql));
    $sql = "SELECT id,vehicle_plate,status FROM vehicle_info WHERE userid='$user[0]' AND vehicle_plate='${carplate}' AND status='active' LIMIT 1;";
    $car = mysqli_fetch_array(mysqli_query($con, $sql));
    $sql = "SELECT id,vehicle_plate,pay_status FROM charge_session WHERE userid='$user[0]' AND vehicle_plate='${carplate}' AND pay_status='START' LIMIT 1;";
    $charge = mysqli_fetch_array(mysqli_query($con, $sql));
    if(empty($user)) array_push($error,'Username Not Found');
    if($user[2]<10) array_push($error,'Must have minimum 10 Credits');
    if(empty($car)) array_push($error,'User Vehicle Number Not Found');
    if(!empty($charge)) array_push($error, $carplate.' having active charging session');

    // CURL to LITS Charger Server
    $send = [
        "id" => "czero", //czero
        // "platform" => "eclimo_app",
        "secret" => "czero@2023", //czero@2023
        "timestamp" => time(),
        "code" => $qrcode,
        "license" => "NDMzMzIwOTc5OTMwNDgyOTAzNjc3",
        "car_number" => $carplate,
        "customer_id" => $user[0],
        "lang" => 0,
        "scan_type" => "EV_ON",
        "session_ref" => ""
    ];

    $ch = curl_init('https://flexicharging.lits.com.my/charger_on_off.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($send));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, 'FlexiCZero');
    $return = curl_exec($ch);
    $return = json_decode($return,true);
    curl_close($ch);

    // Logging Activity
    $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'user_charge_start_api','".json_encode($send)."','".json_encode($return)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/charge.log", $ip, FILE_APPEND);

    if(isset($return['msg']) && !empty($return['msg'])) {
        if($return['msg'] != 'EV charging has successfully activated'){
            array_push($error,$return['msg']);
        } 
        // else if ($return['charger_status'] == 'In use') {
        //     array_push($error, $return['msg']);
        // }
    };

    // $return = [
    //     'msg' => "",
    //     'price' => "",
    //     'cost' => "",
    //     'receipt' => "",
    //     'power' => "",
    //     'temperature' => "",
    //     'total_time' => "",
    //     'location' => "",
    //     'scan_type' => "",
    //     'charger_type' => "",
    //     'charger_status' => "",
    //     'charger_name' => "",
    //     'status' => "",
    //     'car_number' => "",
    //     'start_time' => "",
    // ];

    // Proceed
    if(empty($error)){
        $sql = "INSERT INTO charge_session (vehicle_plate,userid,station_name,location_name,start_dt,paid_id,qrcode,pay_status)
        VALUES ('$carplate','$user[0]','$station','$location','$timestamp','$paidid','$qrcode','START');";
        if ($con->query($sql)) {
            $response['status'] = "OK";
            $response['statusCode'] = "200";
            $response['statusMessage'] = "${carplate} Has Started Charging";
        } else {
            $response['statusMessage'] = "Connection ${location} Has Failed, Please Try Again";
        }

    } else {
        $response['statusMessage'] = join(",",$error);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    // Logging Activity
    $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'user_charge_start','".json_encode($post)."','".json_encode($response)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/charge.log", $ip, FILE_APPEND);

}