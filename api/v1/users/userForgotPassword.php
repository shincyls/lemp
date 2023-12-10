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

    $email = $post['email'];
    $error = [];

    $sql = "SELECT id,password FROM user_info WHERE email='${email}' LIMIT 1;";
    $getusr = mysqli_fetch_all(mysqli_query($con, $sql))[0];
    if(empty($getusr)){ 
        array_push($error, "Email Not Found In System");
    }
    // array_push($error,"Test");

    if(empty($error)){
        $new = generateTemporaryPassword(6);
        $hashed = password_hash($new,PASSWORD_BCRYPT);
        $sql = "UPDATE user_info SET `password`='${hashed}' WHERE `id`=".$getusr[0]." LIMIT 1;";
        if($con->query($sql)){
            $response['statusCode'] = "200";
            $response['status'] = "OK";
            $response['statusMessage'] = "New Temporary Password ${new} Has Been Sent To ${email}";
        } else {
            $response['statusMessage'] = "Unable To Update, Please Check Your Input";
        }
    } else {
        $response['statusMessage'] = join("\n",$error);
    }

    header('Content-Type: application/json');
    echo json_encode($response);

    // Logging Activity
    $ip = ((new DateTime())->format("Y-m-d H:i:s")).",'user_forgot_password','".json_encode($post)."','".json_encode($response)."'\r\n";
    file_put_contents("/usr/share/nginx/html/eclimo.lits.com.my/logs/users.log", $ip, FILE_APPEND);
}