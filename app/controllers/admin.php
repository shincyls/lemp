<?php

include("./connect.php");
// include '/helpers/functions.php';
// include '/helpers/notifiers.php';
// include '/helpers/mailers.php';
$error = [];
$prohibited = "Prohibited From Performing This Action";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $response = [];
    $response['action'] = $_POST['action'];
    $response['status'] = "danger";
    
    switch ($_POST['action']) {

        case 'admin_info_search':
            $sql = "SELECT uuid,fullname,email,phone,company,created_on FROM admin_info WHERE suspended<99 LIMIT 100;";
            $data = mysqli_fetch_all(mysqli_query($con,$sql));
            if(empty($data)){
                $data = ["No Data",null,null,null,null];
            }
            $response['status'] = "success";
            $response['data'] = $data;
        break;

        case 'admin_permission_search':
            // Create Permission Forms
            $rules = extractControllers();
            $sql = "SELECT uuid,username,concat(fullname,'<hr>',email,'<hr>',phone),adminsion,BIN(adminrole)
            FROM admin_info WHERE suspended<99 LIMIT 100;";
            $data = mysqli_fetch_all(mysqli_query($con,$sql));
            if(empty($data)){
                $data = ["No Data",null,null,null,null];
            } else {
                for($i=0;$i<sizeof($data);$i++){
                    if($data[$i][3]!="") {
                        $data[$i][3] = decrypt($data[$i][3],$data[$i][0]);
                    };
                }
            }
            $response['status'] = "success";
            $response['data'] = $data;
            $response['list'] = $rules;
        break;

        case 'admin_permission_update':
            // Collect
            $admin = array_sum($_POST['admin']);
            $selected = join(',',$_POST['items']);
            $target =  mysqli_real_escape_string($con, $_POST['target']);
            // Validate
            if(empty($target)){array_push($error,'Admin not found');}
            // Execute
            if(empty($error)){
                $new = encrypt($selected,$target);
                $new = mysqli_real_escape_string($con, $new);
                $sql = "UPDATE admin_info SET adminsion='$new',adminrole='$admin' WHERE uuid='$target' LIMIT 1;";
                if($con->query($sql)){
                    $response['status'] = "success";
                    $response['message'] = "Admin permission successfully updated, effetively after Relogin ";
                }
            } else {
                $response['status'] = "danger";
                $response['message'] = join(", ", $error);
            }
        break;

        case 'admin_log_search':
            $sql = "SELECT admin_id,created_on,username,action,message,status FROM admin_log LIMIT 1000;";
            $data = mysqli_fetch_all(mysqli_query($con,$sql));
            if(empty($data)){
                $data = ["No Data",null,null,null,null];
            }
            $response['status'] = "success";
            $response['data'] = $data;
        break;

        case 'admin_info_create':
            // Collect
            // $username = mysqli_real_escape_string($con, $_POST['username']);
            $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
            $phone = mysqli_real_escape_string($con, $_POST['phone']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $company = mysqli_real_escape_string($con, $_POST['company']);
            $password = password_hash($_POST['pwdset'],PASSWORD_BCRYPT);
            // Validate
            if(empty($fullname)){array_push($error,'Name cannot empty');}
            if(empty($phone)){array_push($error,'Phone cannot empty');}
            if(empty($email)){array_push($error,'Email cannot empty');}
            if(empty($company)){array_push($error,'Company cannot empty');}
            if($_POST['pwdset']!=$_POST['pwdcon']){array_push($error,'Password Not Matched');}
            // if($_POST['pwdset']!=$_POST['pwdcon']){array_push($error,'Email or Phone already taken');}
            // Execute
            if(empty($error)){
                $sql = "INSERT INTO `admin_info` (`username`,`email`,`phone`,`fullname`,`password`,`company`) VALUES ('username','$email','$phone','$fullname','$password','$company');";
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

        case 'admin_info_update':
            // Collect
            $target = mysqli_real_escape_string($con, $_POST['target']);
            $fullname = mysqli_real_escape_string($con, $_POST['fullname']);
            $phone = mysqli_real_escape_string($con, $_POST['phone']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $company = mysqli_real_escape_string($con, $_POST['company']);
            $suspend = mysqli_real_escape_string($con, $_POST['status']);
            // Validate
            if(empty($fullname)){array_push($error,'Name cannot empty');}
            if(empty($phone)){array_push($error,'Phone cannot empty');}
            if(empty($email)){array_push($error,'Email cannot empty');}
            if(empty($company)){array_push($error,'Company cannot empty');}
            // Execute
            if(empty($error)){
                $sql = "UPDATE admin_info SET `fullname`='".$fullname."',`email`='".$email."',`phone`='".$phone."',`company`='".$company."',`suspended`=".$suspend." WHERE uuid='".$target."';";
                if ($con->query($sql)) {
                    $response['id'] = $target;
                    $response['data'] = [$fullname,$email,$phone,$company,null];
                    $response['status'] = "success";
                    $response['message'] = 'Admin Successfully Updated';
                } else {
                    $response['status'] = "danger";
                    $response['message'] = 'Admin Update Failed';
                }
            } else {
                $response['status'] = "danger";
                $response['message'] = join(", ", $error);
            }
        break;

        case 'admin_info_delete':
            $target = mysqli_real_escape_string($con, $_POST['target']);
            $sql = "UPDATE admin_info SET suspended=99 WHERE uuid='$target';";
            if ($con->query($sql)) {
                $response['id'] = $target;
                $response['data'] = [null,null,null,null,null];
                $response['status'] = "success";
                $response['message'] = 'Admin Successfully Removed';
            } else {
                $response['status'] = "danger";
                $response['message'] = 'Admin Remove Failed';
            }
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


