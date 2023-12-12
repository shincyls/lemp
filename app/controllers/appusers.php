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

function exist($cxn,$useruniqid){
    $output = true;
    $sql = "SELECT id FROM appuser_info WHERE uuid='".$useruniqid."' LIMIT 1";
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

        case 'appuser_info_search':
            $sql = "SELECT row_number() over (order by id) AS num,username,fullname,email,phone,balcredit,uuid FROM appuser_info WHERE usr_lock<99 ORDER BY created_on DESC;";
            $data = mysqli_fetch_all(mysqli_query($con,$sql));
            $response['status'] = "success";
            $response['data'] = $data;
        break;

        case 'appuser_info_create':
            // Collect
            $username = mysqli_real_escape_string($con, strtolower($_POST['username']));
            $email = mysqli_real_escape_string($con, strtolower($_POST['email']));
            $phone = mysqli_real_escape_string($con, $_POST['phone']);
            $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
            $usrgrp = mysqli_real_escape_string($con, $_POST['group']);
            $password = password_hash($_POST['pwdset'],PASSWORD_BCRYPT);
            // Validate
            if($_POST['pwdset']!=$_POST['pwdcon']){array_push($error,'Password Not Matched');}
            if(empty($username)){array_push($error,'Username cannot empty');}
            if(empty($email)){array_push($error,'Email cannot empty');}
            if(empty($phone)){array_push($error,'Phone cannot empty');}
            if(empty($fullname)){array_push($error,'Name cannot empty');}
            if(empty($password)){array_push($error,'Password cannot empty');}
            // if($_POST['pwdset']!=$_POST['pwdcon']){array_push($error,'Email or Phone already taken');}
            // Execute
            if(empty($error)){
                $sql = "INSERT INTO `appuser_info` (`username`,`email`,`phone`,`fullname`,`password`,`usr_group`) VALUES 
                ('$username','$email','$phone','$fullname','$password','$usrgrp');";
                if ($con->query($sql)) {
                    $response['status'] = "success";
                    $response['message'] = "Appuser Successfully Registered";
                    $response['status'] = "success";
                    $response['data'] = [null,$username,$fullname,$email,$phone,"0.00"];
                    $response['message'] = 'Appuser Successfully Added';
                } else {
                    $response['status'] = "danger";
                    $response['message'] = "Please Check Your Database Connection";
                }
            }
            else{
                $response['status'] = "danger";
                $response['message'] = "<ul class=\"list-unstyled\"><li>".join("</li><li>", $error)."</li></ul>";
            }
        break;

        case 'appuser_info_update':
            // Collect
            $target = mysqli_real_escape_string($con, $_POST['target']);
            $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $phone = mysqli_real_escape_string($con, $_POST['phone']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $group = mysqli_real_escape_string($con, $_POST['group']);
            // Validate
            if(empty($fullname)){array_push($error,'Name cannot empty');}
            if(empty($username)){array_push($error,'Username cannot empty');}
            if(empty($email)){array_push($error,'Email cannot empty');}
            if(empty($phone)){array_push($error,'Phone cannot empty');}
            // if(exist($con,$target)){array_push($error,'User not exist');}
            // Execute
            if(empty($error)){
                $sql = "UPDATE appuser_info SET `username`='".$username."',`fullname`='".$fullname."',`email`='".$email."',`phone`='".$phone."',`usr_group`=".$group." WHERE uuid='".$target."';";
                if ($con->query($sql)) {
                    $response['id'] = $target;
                    $response['data'] = [null,$fullname,$email,$phone,$company,null];
                    $response['status'] = "success";
                    $response['message'] = 'Appuser Successfully Updated';
                } else {
                    $response['status'] = "danger";
                    $response['message'] = $sql;
                }
            }
        break;

        case 'appuser_info_delete':
            // if($permission['admin']['user']){
                // Collect
                $target = mysqli_real_escape_string($con, $_POST['target']);
                // Validate
                // if(exist($target)){array_push($error,'User not exist');}
                // Execute
                $sql = "UPDATE appuser_info SET usr_lock=99 WHERE uuid='".$target."';";
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

        case 'appuser_info_topup':
            // if($permission['admin']['user']){
                $type = mysqli_real_escape_string($con, $_POST['type']);
                $amount = mysqli_real_escape_string($con, $_POST['amount']);
                $method = mysqli_real_escape_string($con, $_POST['method']);
                $target = mysqli_real_escape_string($con, $_POST['target']);
                // Validation
                $sql = "SELECT balcredit,id,usernmae FROM appuser_info WHERE uuid='".$target."' LIMIT 1;";
                $get = mysqli_fetch_all(mysqli_query($con, $sql))[0];
                // "1" User Purchase Credits
                // "2" Received Transfer Credits
                // "3" Redeemed PromoCode Credits
                // "9" Admin Magical Credits
                if(empty($get)){array_push($error,$sql);}
                // if($type==2 && $get[1]<$amount){array_push($error,'User have not enough amount to transfer to');}
                // if($type==3){array_push($error,'Promo Code Invalid');}
                // if($type==9){array_push($error,'Company credits pool insufficient');}
                if(empty($error)){
                    $balnew = $get[0] + $amount;
                    $sql = "INSERT INTO appuser_topup (`username`,`user_id`,`bal_before`,`bal_now`,`pay_amount`,`pay_type`,`pay_gateway`,`pay_gateway_cost`,`pay_status`) VALUES
                        ('$get[2]','$get[1]','$get[0]','$balnew','$amount','$type','$method',0.50,'COMPLETE');";
                    if ($con->query($sql)) {
                        $sql = "UPDATE appuser_info SET balcredit=$balnew WHERE uuid='$target';";
                        $con->query($sql);
                        $response['id'] = $target;
                        $response['data'] = [null,null,null,null,null,$balnew];
                        $response['status'] = "success";
                        $response['message'] = 'Appuser has Topup MYR'.$amount;
                    } else {
                        $response['status'] = "danger";
                        $response['message'] = 'Action Failed';
                    }
                } else{
                    $response['status'] = "danger";
                    $response['message'] = "<ul class=\"list-unstyled\"><li>".join("</li><li>", $error)."</li></ul>";
                }
            // }
        break;

        case 'appuser_info_spend':
            // if($permission['admin']['user']){
                $target = mysqli_real_escape_string($con, $_POST['target']);
                $product = $_POST['product'];
                $unit = $_POST['unit'];
                $price = $_POST['price'];
                $amount = $_POST['amount'];
                $total = 0;
                $collect = [];
                $stsname = 'COMPLETE';
                $stscode = 10;
                // $code = "MY".date('ymdHis', time());
                // Validation
                $sql = "SELECT balcredit,id,username FROM appuser_info WHERE uuid='$target' LIMIT 1;";
                $get = mysqli_fetch_all(mysqli_query($con, $sql))[0];
                if(empty($get)){array_push($error,$sql);}
                if($get[0]<$total){array_push($error,'User has insufficient credits to continue purchase');}
                // if($type==3){array_push($error,'Promo Code Invalid');}
                // if($type==9){array_push($error,'Company credits pool insufficient');}
                if(empty($error)){
                    // Calculate Total
                    for($a=0;$a<sizeof($product);$a++){
                        if($unit[$a]>0 && !empty($product[$a])){
                            $total += $unit[$a]*$price[$a];
                            array_push($collect,"('$get[2]','$get[1]',1,'$product[$a]',$unit[$a],$price[$a],$amount[$a],$stscode,'$stsname')");
                        }
                    }
                    if($stscode==10){
                        $balnew = $get[0] - $total;
                    } else {
                        $balnew = $get[0];
                    }
                    // Execute
                    $sql = "INSERT INTO appuser_spend (`username`,`user_id`,`product_id`,`product_desc`,`product_unit`,`product_price`,`product_total`,`status_id`,`status_name`) VALUES ".join(',',$collect).";";
                    if ($con->query($sql)) {
                        $sql = "UPDATE appuser_info SET balcredit=$balnew WHERE uuid='".$target."';";
                        $con->query($sql);
                        $response['id'] = $target;
                        $response['data'] = [null,null,null,null,null,$balnew];
                        $response['status'] = "success";
                        $response['message'] = 'Appuser has Spent MYR '.$total;
                    } else {
                        $response['status'] = "danger";
                        $response['message'] = "Action Failed";
                    }
                } else{
                    $response['status'] = "danger";
                    $response['message'] = "<ul class=\"list-unstyled\"><li>".join("</li><li>", $error)."</li></ul>";
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