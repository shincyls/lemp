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
    $username = $post['input_username'];
    $start = $post['dt_start'];
    $end = $post['dt_end'];

    $sql = "SELECT email,username,phone_number,user_type,date_of_birth,credit_balance FROM user_info WHERE email='$email' AND username='$username' LIMIT 1;";
    $user = mysqli_fetch_assoc(mysqli_query($con,$sql));

    if($user==null){       
        $response['statusMessage'] = $sql;
    }
    else{
        $response['statusMessage'] = "Success";
        $response['statusCode'] = "200";
        $response['status'] = "OK";
        $response['data']['user'] = [];
        $response['data']['vehicles'] = [];
        $response['data']['credits_history'] = getTopUpsList($con,$email,$start,$end);
        $response['data']['charges_history'] = getChargesList($con,$email,$start,$end);
    }

    // $sql = "INSERT INTO api_logs (`object`,`request`,`response`) VALUES ('user_signin','".json_encode($post)."','".json_encode($response)."');";
    // $log = mysqli_query($con,$sql);

    header('Content-Type: application/json');
    echo json_encode($response);
}