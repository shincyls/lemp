<?php

include("./connect.php");
// include '/helpers/functions.php';
// include '/helpers/notifiers.php';
// include '/helpers/mailers.php';
$error = [];
$prohibited = "user not found prohibited";
$admin = ['shincy'];
if (isset($_SESSION['uuid']) && strlen($_SESSION['uuid'])==36){
    // $sql = "SELECT id,username FROM admin_info WHERE uuid='".$_SESSION['uuid']."' LIMIT 1;";
    // $admin = mysqli_fetch_all(mysqli_query($con,$sql))[0];
}
if(empty($admin)){
    $response['action'] = 'no_action';
    $response['message'] = 'session expired, <a href="/singin">please login again</a>';
    $response['status'] = "danger";
    echo json_encode($response);
    exit;
}

function exist($cxn,$salesid){
    $output = true;
    $sql = "SELECT id FROM appuser_topup WHERE unix='".$salesid."' LIMIT 1";
    $apu = mysqli_fetch_all(mysqli_query($cxn,$sql));
    if(empty($apu)) {
        $output = false;
    };
    return $output;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $response = [];
    $response['action'] = $_POST['action'];
    $response['status'] = "danger";
    $action = $_POST['action'];
    
    switch ($action) {

        case 'appuser_spend_search':
            $sql = "SELECT unix,row_number() over (order by id) AS num,unix,username,product_desc,product_unit,product_price,product_total,status_name,created_on FROM appuser_spend ORDER BY created_on DESC;";
            $data = mysqli_fetch_all(mysqli_query($con,$sql));
            if(empty($data)){
                $data = ["No Data",null,null,null,null];
            }
            $response['status'] = "success";
            $response['data'] = $data;
        break;

        case 'appuser_spend_update':
            //if($permission['admin']['user']){
                $target = mysqli_real_escape_string($con, $_POST['target']);
                $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
                $phone = mysqli_real_escape_string($con, $_POST['phone']);
                $email = mysqli_real_escape_string($con, $_POST['email']);
                $company = mysqli_real_escape_string($con, $_POST['company']);
                $suspend = mysqli_real_escape_string($con, $_POST['status']);
                $sql = "UPDATE appuser_info SET `fullname`='".$fullname."',`email`='".$email."',`phone`='".$phone."',`company`='".$company."',`suspended`=".$suspend." WHERE uuid='".$target."';";
                if ($con->query($sql)) {
                    $response['id'] = $target;
                    $response['data'] = [null,$fullname,$email,$phone,$company,null];
                    $response['status'] = "success";
                    $response['message'] = 'Appuser Successfully Updated';
                } else {
                    $response['status'] = "danger";
                    $response['message'] = 'Appuser Update Failed';
                }
            // }
        break;

        case 'appuser_spend_delete':
            // if($permission['admin']['user']){
                $target = mysqli_real_escape_string($con, $_POST['target']);
                $sql = "UPDATE appuser_info SET suspended=99 WHERE uuid='".$target."';";
                if ($con->query($sql)) {
                    $response['id'] = $target;
                    $response['data'] = [null,null,null,null,null];
                    $response['status'] = "success";
                    $response['message'] = 'Appuser Successfully Removed';
                } else {
                    $response['status'] = "danger";
                    $response['message'] = 'Appuser Remove Failed';
                }
            // }
        break;

    }

    // Capture Action Logs
    if(true){
        $con->query("INSERT INTO `admin_log` (`admin_id`,`username`,`action`,`status`,`message`) VALUES 
        ('".$_SESSION["uuid"]."','".$_SESSION["user"]."','".$response["action"]."','".$response["status"]."','".$response["message"]."');");
    }
    // Return Response Data
    echo json_encode($response);
}

?>