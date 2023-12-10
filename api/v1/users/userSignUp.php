<?php

include("./db_entry.php");
include("./helpers/functions.php");
// include("/backend/mailers.php");
// include("/backend/notifiers.php");
date_default_timezone_set("Asia/Singapore");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $error = [];
    $response['statusCode'] = "400";
    $response['status'] = "ERROR";
    $jsonReqUrl  = "php://input";
    $reqjson = file_get_contents($jsonReqUrl);
    $post = json_decode($reqjson, true);
    
    $email = strtolower($post['input_email']);
    $email = mysqli_real_escape_string($con, $email);
    $fullname = strtolower($post['input_username']);
    $username = generateRandomString(8);
    $last4ic = strtolower($post['input_last4ic']);
    $phone = strtolower($post['input_phone']);
    $password = $post['input_password'];
    $confirm = $post['input_confirm'];
    $hashed = password_hash($password,PASSWORD_BCRYPT);

    $sql = "SELECT email FROM user_info WHERE email='$email' OR username='$email' LIMIT 1;";
    $user = mysqli_fetch_all(mysqli_query($con,$sql));

    // Validator
    if(count($user)>0){
        array_push($error,'This Email has been Registered, Use other Email');
    }
    if($password!=$confirm){
        array_push($error,'Password Not Match');
    }

    // Main
    if(empty($error)){
        $sql = "INSERT INTO `user_info` (`username`,`fullname`,`email`,`phone_number`,`password`) VALUES ('$username','$fullname','$email','$phone','$hashed');";
        if($con->query($sql)){
            $response['statusCode'] = "200";
            $response['status'] = "SUCCESS";
            $response['statusMessage'] = "User Successfully Registered";
        }
        else{
            $response['statusMessage'] = "Unable Connect Server, Please Try Again";
        }
    }
    else{
        $response['statusMessage'] = join(',',$error);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    // Logging Activity
    $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'user_sign_up','".json_encode($post)."','".json_encode($response)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/users.log", $ip, FILE_APPEND);
}
