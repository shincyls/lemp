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
    $input['fullname'] = strtolower($post['full_name']);
    $input['email'] = strtolower($post['new_mail']);
    $input['phone_number'] = strtolower($post['phone']);

    $where = [];
    foreach($input as $k=>$v){
        if(!empty($v)){
            array_push($where, $k."='".$v."'");
        }
    }
    if($where==[]){array_push($error,"No Info To Be Updated");}

    $limit = 5;
    $error = [];

    $sql = "SELECT id FROM user_info WHERE username='${user}' AND email='${email}' LIMIT 1;";
    $getusr = mysqli_fetch_array(mysqli_query($con, $sql));
    if($getusr==null){ array_push($error,"Missing User Info, Please Login Again"); }
    // array_push($error,$sql);

    if(empty($error)){
        $response['statusCode'] = "200";
        $response['status'] = "OK";
        $sql = "UPDATE user_info SET ".join(',',$where)." WHERE id=".$getusr[0]." LIMIT 1;";
        if($con->query($sql)){
            $response['statusMessage'] = "User Profile Has Successfully Updated";
        } else {
            $response['statusMessage'] = "Unable To Update, Please Check Your Input";
        }
    } else{
        $response['statusMessage'] = join("\n",$error);
    }

    header('Content-Type: application/json');
    echo json_encode($response);
}