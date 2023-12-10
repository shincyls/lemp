<?php


function payChargeCredits($con,$userid,$payid){

    $result = [
        'update_receipt'=>0,
        'update_user'=>0,
        'error'=>[]
    ];
    $error = [];

    // Validate User
    $sql1 = "SELECT credit_balance FROM user_info WHERE id=$userid LIMIT 1;";
    $user = mysqli_fetch_array(mysqli_query($con, $sql1));
    if(empty($user)) array_push($error,'User Not Found');

    // Update charge_payment
    $sql2 = "SELECT id,paid_id,start_dt,end_dt,vehicle_plate,need_credits FROM charge_session WHERE userid='$userid' AND paid_id='$payid' AND pay_status='STOP' LIMIT 1;";
    $charge = mysqli_fetch_array(mysqli_query($con, $sql2));
    if(empty($charge)) array_push($error,'Session Not Found');

    if(empty($error)){

        $new = $user[0] - $charge[5];
        // Update charge_session payment status
        $createReceipt = "UPDATE charge_session SET paid_credits='$charge[5]',pay_status='PAID' WHERE userid=$userid AND paid_id='$payid' AND pay_status='STOP' LIMIT 1;";
        $result['update_receipt'] = $con->query($createReceipt);

        // Update user_info credit_balance
        $deductCredits = "UPDATE user_info SET credit_balance=$new WHERE id=$userid LIMIT 1;";
        $result['update_user'] = $con->query($deductCredits);

    } else {

        $result['error'] = $error;
        
    }

    return $result;
}