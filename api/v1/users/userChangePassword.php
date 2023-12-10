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

    $user = $post['username'];
    $email = $post['email'];
    $old = $post['old'];
    $new = $post['new'];
    $confirm = $post['confirm'];

    $limit = 5;
    $error = [];

    // VALIDATION
    // if($new!=$confirm){ 
    //     array_push($error,"New Password and Confirm Password Not Match"); // validated in frontend
    // }
    $sql = "SELECT id,password FROM user_info WHERE username='${user}' AND email='${email}' LIMIT 1;";
    $getusr = mysqli_fetch_all(mysqli_query($con, $sql))[0];
    if(empty($getusr)){ 
        array_push($error, "Missing User Info, Please Login Again");
    }
    else if(password_verify($old, $getusr[1]) === FALSE){
        array_push($error, "Current Password Is Not Correct"); 
    }
    // array_push($error,"Test");

    if(empty($error)){
        $hashed = password_hash($new,PASSWORD_BCRYPT);
        $sql = "UPDATE user_info SET `password`='${hashed}' WHERE `id`=".$getusr[0]." LIMIT 1;";
        if($con->query($sql)){
            $response['statusCode'] = "200";
            $response['status'] = "OK";
            $response['statusMessage'] = "Password for ${email} Has Successfully Updated";
        } else {
            $response['statusMessage'] = "Unable To Update, Please Check Your Input";
        }
    } else {
        $response['statusMessage'] = join("\n",$error);
    }

    // $sql = "INSERT INTO api_logs (`object`,`request`,`response`) VALUES ('/api/modpass','".json_encode($post)."','".json_encode($response)."');";
    // $log = mysqli_query($con,$sql);

    header('Content-Type: application/json');
    echo json_encode($response);
}