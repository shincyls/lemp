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
        
        case 'task_info_search':
            $sql = "SELECT task_name,task_desc,task_file,loc_id,com_id,chronic,exe_month,exe_day,exe_hour,confirm,logfile FROM task_info;";
            $data = mysqli_fetch_all(mysqli_query($con,$sql));
            $response['status'] = "success";
            $response['data'] = $data;
        break;

        case 'task_log_search':
            // task_id int(16) not null,
            // task_name VARCHAR(64) not null,
            // status VARCHAR(18) default "failed",
            // message VARCHAR(255) default "",
            // remarks VARCHAR(255) default "",
            $sql = "SELECT * FROM task_log LIMIT 1000;";
            $data = mysqli_fetch_all(mysqli_query($con,$sql));
            if(empty($data)){
                $data = ["No Data",null,null,null,null];
            }
            $response['status'] = "success";
            $response['data'] = $data;
        break;

        case 'task_info_create':
            // Collect
            // task_name VARCHAR(64) not null,
            // task_desc TEXT null,
            // task_file VARCHAR(255) null,
            // loc_id int(16) null,
            // com_id int(16) null,
            // chronic VARCHAR(64) not null default "yearly",
            // exe_month int(2) null default 1,
            // exe_day int(2) null default 1,
            // exe_hour int(2) null default 0,
            // confirm boolean default false,
            // logfile boolean default false,
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
            // Execute
            if(empty($error)){
                $sql = "INSERT INTO `task_info` (`username`,`email`,`phone`,`fullname`,`password`,`company`) VALUES ('username','$email','$phone','$fullname','$password','$company');";
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

        case 'task_info_update':
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
                $sql = "UPDATE task_info SET `fullname`='".$fullname."',`email`='".$email."',`phone`='".$phone."',`company`='".$company."',`suspended`=".$suspend." WHERE id='".$target."';";
                if ($con->query($sql)) {
                    $response['id'] = $target;
                    $response['data'] = [$fullname,$email,$phone,$company,null]; // update data
                    $response['status'] = "success";
                    $response['message'] = 'Task Successfully Updated';
                } else {
                    $response['status'] = "danger";
                    $response['message'] = 'Task Update Failed';
                }
            } else {
                $response['status'] = "danger";
                $response['message'] = join(", ", $error);
            }
        break;

        case 'task_info_delete':
            $target = mysqli_real_escape_string($con, $_POST['target']);
            $sql = "DELETE task_info WHERE id='$target';";
            if ($con->query($sql)) {
                $response['status'] = "success";
                $response['message'] = 'Task Successfully Removed';
            } else {
                $response['status'] = "danger";
                $response['message'] = 'Task Remove Failed';
            }
        break;

    }

    // Capture Action Logs
    if(true){
        $con->query("INSERT INTO `task_log` (`admin_id`,`username`,`action`,`status`,`message`) VALUES 
        ('".$_SESSION["uuid"]."','".$_SESSION["user"]."','".$response["action"]."','".$response["status"]."','".$response["message"]."');");
    }

    // Return Response Data
    echo json_encode($response);
}

?>


