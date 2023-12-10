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
    $password = $post['input_password'];
    $sql = "SELECT email,username,phone_number,user_type,date_of_birth,password,status,credit_balance,fullname,id FROM user_info WHERE email='$email' OR username='$email' LIMIT 1;";
    $user = mysqli_fetch_assoc(mysqli_query($con,$sql));
    // $now = (new DateTime())->format("Y-m-d H:i");
    // $end = (new DateTime($login_info['expired_at']))->format("Y-m-d H:i");

    if($user==null){       
        $response['statusMessage'] = "Wrong Email Or Password";
    }
    else if($user['status']=='SUSPENDED'){
        $response['statusMessage'] = "Account Have Been Suspended";
    }
    else if(password_verify($password, $user['password']) === TRUE){
        unset($user['password']);
        $response['statusCode'] = "200";
        $response['status'] = "OK";
        $response['statusMessage'] = "Login Success";
        $response['data']['user'] = $user;
        $response['data']['vehicles'] = getVehiclesList($con,$user['id']);
        if(empty($response['data']['vehicles'])){
            $response['data']['vehicles'] = [];
        }
        $response['data']['charging'] = getChargingList($con,$user['id']);
        $response['data']['credits_history'] = getTopUpsList($con,$user['username']);
        $response['data']['charges_history'] = getChargesList($con,$user['username']);
        // $response['data']['url_domain'] = getDomainUrl($con);
        // $response['data']['url_payment'] = getPaymentUrl($con);
        // Load More Data Like Vehicle / Transaction History / TopUp History into APK Shared_Preferences
    }
    else{
        $response['statusMessage'] = "Wrong Username Or Password";
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    // Logging Activity
    $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'user_sign_in','".json_encode($post)."','".json_encode($response)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/users.log", $ip, FILE_APPEND);

}