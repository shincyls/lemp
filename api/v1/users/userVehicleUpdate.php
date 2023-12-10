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
    $exist = str_replace(' ','',strtoupper($post['exist']));

    $limit = 5;
    $error = [];

    // IF CHARGING CANNOT EDIT

    if($plate=='' || $plate=='EMPTY'){array_push($error,"Vehicle Number Cannot Be Empty"); }

    $sql = "SELECT id FROM user_info WHERE username='${user}' AND email='${email}' LIMIT 1;";
    $getusr = mysqli_fetch_array(mysqli_query($con, $sql));
    if($getusr==null){ array_push($error,"Missing User Info, Please Login Again"); }

    $sql = "SELECT count(id) FROM `vehicle_info` WHERE `userid`='$getusr[0]' AND `status`='active' GROUP BY `userid`;";
    $max = mysqli_fetch_array(mysqli_query($con, $sql));
    if($max[0]>$limit){ array_push($error,"Cannot Add More Than ${limit} Vehicle(s)"); }

    $sql = "SELECT a.vehicle_plate as car,a.id as carid, b.id as userid, a.status FROM vehicle_info a LEFT JOIN user_info b ON a.userid=b.id WHERE b.username='$user' AND a.vehicle_plate='$plate' ORDER BY a.create_on asc LIMIT 1;";
    $cars = mysqli_fetch_array(mysqli_query($con, $sql));
    if($cars!=null && $cars[3]=='active'){ array_push($error,"${plate} Is Still Active"); }
    // array_push($error,$sql);

    if(empty($error)){

        $response['statusCode'] = "200";
        $response['status'] = "OK";

        if($cars!=null){
            // Reinvoke Deactivated Car To Preserve Records
            $sql = "UPDATE vehicle_info SET `status`='active' WHERE `id`='$cars[1]' AND `userid`='$cars[2]' AND `vehicle_plate`='$plate' AND `status`='inactive' LIMIT 1;";
            if($con->query($sql)){
                if($exist!='' || $exist!='EMPTY'){
                    $sql = "SELECT a.vehicle_plate as car,a.id as carid, b.id as userid FROM vehicle_info a LEFT JOIN user_info b ON a.userid=b.id WHERE b.username='$user' AND a.vehicle_plate='$old' AND a.status='active' ORDER BY a.create_on asc LIMIT 1;";
                    $prevcar = mysqli_fetch_array(mysqli_query($con, $sql));
                    if($prevcar!=null){
                        $sql = "UPDATE vehicle_info SET `status`='inactive' WHERE `id`='$cars[1]' AND `userid`='$cars[2]' AND `vehicle_plate`='$old' AND `status`='active' LIMIT 1;";
                        $con->query($sql);
                    }
                }
                $response['statusMessage'] = "${plate} Has Successfully Re-Activated";
            } else {
                $response['statusMessage'] = "Unable To Update, Please Check Your Input";
            }
        } else {
            // Add as New Vehicle
            $sql = "INSERT INTO `vehicle_info` (`userid`,`vehicle_plate`) VALUES ($getusr[0],'$plate');";
            if($con->query($sql)){
                $response['statusMessage'] = "${plate} Has Successfully Activated";
            } else {
                $response['statusMessage'] = "Unable To Update, Please Check Your Input";
            }
        }

    } else{
        $response['statusMessage'] = join(",",$error);
    }

    $sql = "INSERT INTO api_logs (`object`,`request`,`response`) VALUES ('/api/carupdate','".json_encode($post)."','".json_encode($response)."');";
    $log = mysqli_query($con,$sql);

    header('Content-Type: application/json');
    echo json_encode($response);
}