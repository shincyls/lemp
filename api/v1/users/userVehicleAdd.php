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

    $user = strtolower($post['username']);
    $email = strtolower($post['email']);
    $plate = str_replace(' ','',strtoupper($post['plate']));

    $limit = 5;
    $error = [];

    // VALIDATIONS
    $sql = "SELECT id FROM user_info WHERE username='${user}' AND email='${email}' LIMIT 1;";
    $getusr = mysqli_fetch_array(mysqli_query($con, $sql));
    $sql = "SELECT * from vehicle_info WHERE status='active' AND userid=$getusr[0] AND vehicle_plate='$plate' LIMIT 1;";
    $getcar = mysqli_fetch_array(mysqli_query($con, $sql));

    if($plate=='' || $plate=='EMPTY') array_push($error,"Vehicle Number Cannot Be Empty");
    if(empty($getusr)) array_push($error,"Missing User Info, Please Login Again");
    if(!empty($getcar)) array_push($error,"User has already added this car");
    
    if(empty($error)){
        $sql = "INSERT INTO vehicle_info (`userid`,`vehicle_plate`,`status`) VALUES ($getusr[0],'$plate','active');";
        if($con->query($sql)){
            $response['statusCode'] = "200";
            $response['status'] = "OK";
            $response['statusMessage'] = "${plate} Has Successfully Added";
        } else {
            $response['statusMessage'] = "Unable To Add, Please Check Your Internet";
        }
    } else{
        $response['statusMessage'] = join("\n",$error);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    // Logging Activity
    $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'user_vehicle_add','".json_encode($post)."','".json_encode($response)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/charge.log", $ip, FILE_APPEND);

}