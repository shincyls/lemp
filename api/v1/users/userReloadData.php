<?php

include("/usr/share/nginx/html/eclimo.lits.com.my/db_entry.php");
include("functions.php");
// include("/backend/mailers.php");
// include("/backend/notifiers.php");
date_default_timezone_set("Asia/Singapore");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $response['statusCode'] = "400";
    $response['status'] = "ERROR";
    $response['data'] = [];
    $jsonReqUrl  = "php://input";
    $reqjson = file_get_contents($jsonReqUrl);
    $post = json_decode($reqjson, true);

    $email = strtolower($post['input_email']);
    // $password = $post['input_password'];
    $sql = "SELECT email,username,phone_number,user_type,date_of_birth,password,status,credit_balance,fullname,id FROM user_info WHERE email='$email' OR username='$email' LIMIT 1;";
    $user = mysqli_fetch_array(mysqli_query($con,$sql));

    if($user!=null){       
        $response['statusCode'] = "200";
        $response['status'] = "OK";
        $response['statusMessage'] = "Refresh Data Success";
        $response['data']['user'] = $user;
        $response['data']['vehicles'] = getVehiclesList($con,$user[9]);
        $response['data']['charging'] = getChargingList($con,$user[9]);
        $response['data']['credits_history'] = getTopUpsList($con,$user[1]);
        $response['data']['charges_history'] = getChargesList($con,$user[1]);
        // $response['data']['url_domain'] = getDomainUrl($con);
        // $response['data']['url_payment'] = getPaymentUrl($con);
    }
    else{
        $response['statusMessage'] = "Username Not Found";
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}