<?php

include("/usr/share/nginx/html/eclimo.lits.com.my/db_entry.php");
include("functions.php");
// include("chargePayCredits.php");
// include("/backend/mailers.php");
// include("/backend/notifiers.php");
date_default_timezone_set("Asia/Singapore");

if ($_SERVER["REQUEST_METHOD"] == "POST" ) {

    $error = [];
    $response['statusCode'] = "400";
    $response['status'] = "ERROR";
    $response['statusMessage'] = "Error";

    $id = mysqli_real_escape_string($con, $_POST['id']);
    $secret = mysqli_real_escape_string($con, $_POST['secret']);

    $station_id = mysqli_real_escape_string($con, $_POST['station_id']);
    $car_number = mysqli_real_escape_string($con, $_POST['car_number']);
    $lang = mysqli_real_escape_string($con, $_POST['lang']);
    $scan_type = mysqli_real_escape_string($con, $_POST['scan_type']);

    $stop_timestamp = mysqli_real_escape_string($con, $_POST['stop_timestamp']);
    $cost = mysqli_real_escape_string($con, $_POST['cost']);

    $sql = "SELECT id,userid,vehicle_plate,pay_status,paid_id,location_name FROM charge_session WHERE station_name='${station_id}' AND vehicle_plate='${car_number}' AND pay_status='START' LIMIT 1;";
    $charge = mysqli_fetch_array(mysqli_query($con, $sql));
    // $sql = "SELECT id,username,credit_balance FROM user_info WHERE id='".$charge[1]."' LIMIT 1;";
    // $user = mysqli_fetch_array(mysqli_query($con, $sql));
    $timestamp = date('Y-m-d H:i:s');

    // Validation
    // if(empty($user)) array_push($error,'Username Not Found');
    if(empty($charge)) array_push($error, 'Charge Session Not Found');
    // if(empty($carplate)) array_push($error,'Vehicle Number Not Found');

    // // CURL to LITS Charger Server
    // $send = [
    //     "id" => "czero",
    //     "secret" => "czero@2023",
    //     "timestamp" => time(),
    //     "code" => $charge[3],
    //     "license" => "NDMzMzIwOTc5OTMwNDgyOTAzNjc3",
    //     "car_number" => $carplate,
    //     "customer_id" => $user[0],
    //     "lang" => 0,
    //     "scan_type" => "EV_INFO",
    // ];

    // $ch = curl_init('https://flexicharging.lits.com.my/charger_on_off.php');
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($send));
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_USERAGENT, 'FlexiCZero');
    // $return = curl_exec($ch);
    // $return = json_decode($return,true);
    // curl_close($ch);

    // // Logging Activity
    // $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'lits_charge_stop_api','".json_encode($send)."','".json_encode($return)."'\r\n";
    // file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/charge.log", $ip, FILE_APPEND);

    // $stop_timestamp = $return['stop_time'];
    // $cost = $return['cost'];

    // Proceed
    if(empty($error)){
        // $cost = $return['cost'];
        $sql = "UPDATE charge_session SET end_dt=NOW(), need_credits='${cost}', pay_status='STOP' WHERE paid_id='".$charge[4]."';";
        if ($con->query($sql)) {
            // $payment = payChargeCredits($con,$user[0],$cost);
            $cost = number_format((float)$cost, 2, '.', '');
            $response['status'] = "OK";
            $response['statusCode'] = "200";
            $response['statusMessage'] = $charge[2]." Has Stopped Charging (-${cost} Credits)";
            $response['data']['credits'] = $cost;
        } else {
            $response['statusMessage'] = "Connection ".$charge[5]." has failed, please try again";
        }
    } else {
        $response['statusMessage'] = join(",", $error);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    // Logging Activity
    $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'lits_charge_stop','".json_encode($_POST)."','".json_encode($response)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/charge.log", $ip, FILE_APPEND);

}