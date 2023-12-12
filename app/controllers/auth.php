<?php

include("./connect.php");
$error = [];

function genRandomPass($length= 12) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $password = '';
    $charLength = strlen($characters);
    for ($i = 0; $i < $length; $i++) {
        $randomChar = $characters[mt_rand(0, $charLength - 1)];
        $password .= $randomChar;
    }
    return $password;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $response = [];
    $response['action'] = $_POST['action'];
    $response['status'] = "danger";
    
    // Routing
    switch ($_POST['action']) {

        case 'web_signin':
            $email = strtolower($_POST['email']);
            $sql = "SELECT `email`,`uuid`,`password`,`suspended`,`username`,`adminsion` FROM admin_info WHERE lower(email)='".strtolower($email)."' OR phone='$email' LIMIT 1;";
            $login = mysqli_fetch_all(mysqli_query($con,$sql))[0];
            // $now = (new DateTime())->format("Y-m-d H:i");
            // $end = (new DateTime($login_info['expired_at']))->format("Y-m-d H:i");
            if($login==null){       
                $response['message'] = "Wrong Email";
            }
            else if($login[3]>0){
                $response['message'] = "Account Is Suspended, Please Check With Admin";
            }
            else if(password_verify($_POST['password'], $login[2]) === TRUE){
                session_destroy(); 
                ini_set('session.gc_maxlifetime', 432000);
                session_set_cookie_params(432000);
                session_start(); 
                $_SESSION["uuid"] = $login[1];
                $_SESSION["user"] = $login[4];
                $_SESSION["secret"] = $login[5];
                $_SESSION["login"] = 1;
                $response['status'] = "success";
                $response['message'] = "Successfully Login";
                $con->query("UPDATE admin_info SET signed_on=NOW() WHERE uuid='".$login[1]."';");
            }
            else{
                $response['message'] = "Wrong Password";
            }
        break;

        case 'web_signup':
            // Collect
            // $username = mysqli_real_escape_string($con, $_POST['username']);
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
            $phone = mysqli_real_escape_string($con, $_POST['phone']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $company = mysqli_real_escape_string($con, $_POST['company']);
            $password = password_hash($_POST['pwdset'],PASSWORD_BCRYPT);
            $sql = "SELECT `uuid` FROM admin_info WHERE email='$email' LIMIT 1;";
            $admin = mysqli_fetch_all(mysqli_query($con,$sql))[0];
            // Validate
            if(empty($fullname)){array_push($error,'Name cannot empty');}
            if(empty($phone)){array_push($error,'Phone cannot empty');}
            if(empty($email)){array_push($error,'Email cannot empty');}
            if(empty($company)){array_push($error,'Company cannot empty');}
            if($_POST['pwdset']!=$_POST['pwdcon']){array_push($error,'Password Not Matched');}
            if(!empty($admin)){array_push($error,'Email has already registered');}
            // Execute
            if(empty($error)){
                $sql = "INSERT INTO `admin_info` (`username`,`email`,`phone`,`fullname`,`password`,`company`) VALUES ('$username','$email','$phone','$fullname','$password','$company');";
                if($con->query($sql)){
                    $response['status'] = "success";
                    $response['message'] = "User Successfully Registered";
                }
                else{
                    $response['status'] = "danger";
                    $response['message'] = "Please Check Your Database Connection";
                }
            } else {
                $response['status'] = "danger";
                $response['message'] = join(", ", $error);
            }
        break;

        case 'web_forgetpwd':
            // Collect
            $email = strtolower($_POST['email']);
            $sql = "SELECT `email`,`suspended` FROM admin_info WHERE lower(email)='".strtolower($email)."' LIMIT 1;";
            $login = mysqli_fetch_all(mysqli_query($con,$sql))[0];
            // Validate
            if(empty($login)){array_push($error,"Email Not Found");}
            else if($login[1]>10){array_push($error, "Account Is Suspended, Please Check With Admin");}
            // Execute
            if(empty($error)){
                $pwdnew = genRandomPass(4);
                $pwdhash = password_hash($pwdnew,PASSWORD_BCRYPT);
                $sql = "UPDATE admin_info SET password='$pwdhash' WHERE email='$email';";
                if ($con->query($sql)) {
                    // include("./mailers/mailers.php");
                    // email_change_password($con,$email);
                    $response['status'] = "success";
                    $response['message'] = 'Your Temporary Password Is: '.$pwdnew;
                } else {
                    $response['status'] = "danger";
                    $response['message'] = "Please Check Your Database Connection";
                }
            } else {
                $response['status'] = "danger";
                $response['message'] = join(", ", $error);
            }
        break;

        case 'admin_change_profile':
            // Collect
            $target = mysqli_real_escape_string($con, $_SESSION['uuid']);
            $username = mysqli_real_escape_string($con, $_POST['username']);
            $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
            $phone = mysqli_real_escape_string($con, $_POST['phone']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $company = mysqli_real_escape_string($con, $_POST['company']);
            $sql = "SELECT `password`,`suspended` FROM admin_info WHERE uuid='$target' LIMIT 1;";
            $admin = mysqli_fetch_all(mysqli_query($con,$sql))[0];
            // Validate
            if(empty($admin)){array_push($error,'Admin Account Not Found');}
            if($admin[1]>=10){array_push($error,'Admin Account Suspended Unable To Change');}
            if(empty($username)){array_push($error,'Username cannot empty');}
            if(empty($fullname)){array_push($error,'Name cannot empty');}
            if(empty($phone)){array_push($error,'Phone cannot empty');}
            if(empty($email)){array_push($error,'Email cannot empty');}
            if(empty($company)){array_push($error,'Company cannot empty');}
            if($_POST['pwdset']!=$_POST['pwdcon']){array_push($error,'Password Not Matched');}
            // Execute
            if(empty($error)){
                $sql = "UPDATE admin_info SET `fullname`='$fullname',`email`='$email',`phone`='$phone',`company`='$company',`username`='$username' WHERE uuid='$target';";
                if ($con->query($sql)) {
                    $response['status'] = "success";
                    $response['message'] = 'Admin Profile Successfully Updated';
                } else {
                    $response['status'] = "danger";
                    $response['message'] = 'Admin Profile Updated Failed';
                }
            } else {
                $response['status'] = "danger";
                $response['message'] = join(", ", $error);
            }
        break;

        case 'admin_change_password':
            // Collect
            $target = mysqli_real_escape_string($con, $_SESSION['uuid']);
            $pwdnow = mysqli_real_escape_string($con, $_POST['pwdnow']);
            $pwdnowcon = mysqli_real_escape_string($con, $_POST['pwdnowcon']);
            $pwdnew = mysqli_real_escape_string($con, $_POST['pwdnew']);
            $pwdnewcon = mysqli_real_escape_string($con, $_POST['pwdnewcon']);
            $sql = "SELECT `password`,`suspended` FROM admin_info WHERE uuid='$target' LIMIT 1;";
            $admin = mysqli_fetch_all(mysqli_query($con,$sql))[0];
            // Validate
            if(empty($admin)){array_push($error,'Admin Account Not Found');}
            if($admin[1]>=10){array_push($error,'Admin Account Suspended Unable To Change');}
            if(!password_verify($pwdnow, $admin[0]) === TRUE){array_push($error,'Current Password Is Wrong');}
            if($pwdnow!=$pwdnowcon){array_push($error,'Current Password Not Matched');}
            if($pwdnew!=$pwdnewcon){array_push($error,'New Password Not Matched');}
            if(strlen($pwdnew)<8 || strlen($pwdnew)>12){array_push($error,'New Password Must Between 8-12 Characters');}
            // Execute
            if(empty($error)){
                $pwdhash = password_hash($pwdnew,PASSWORD_BCRYPT);
                $sql = "UPDATE admin_info SET password='".$pwdhash."' WHERE uuid='$target';";
                if ($con->query($sql)) {
                    $response['status'] = "success";
                    $response['message'] = 'Admin Password Successfully Updated';
                } else {
                    $response['status'] = "danger";
                    $response['message'] = 'Admin Password Update Failed';
                }
            } else {
                $response['status'] = "danger";
                $response['message'] = join(", ", $error);
            }
        break;
        
    }

    // Capture Action Logs
    if(true){
        $con->query("INSERT INTO `admin_log` (`username`,`action`,`status`,`message`) VALUES ('".$email."','".$response["action"]."','".$response["status"]."','".$response["message"]."');");
    }

    // Response
    echo json_encode($response);
}


