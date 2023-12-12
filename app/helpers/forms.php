<?php
include "./connect.php";

function selected($arg,$val){
    return ($arg==$val) ? "selected" : "";
};

function checked($arg,$val){
    return ($arg==$val) ? "checked" : "";
};

$jsonReqUrl  = "php://input";
$reqjson = file_get_contents($jsonReqUrl);
$post = json_decode($reqjson, true);

$id = $post['id'];
$action = $post['action'];
$response['action'] = $action;

switch ($action) {
    case 'admin_info_create':
    case 'admin_info_update':
    case 'admin_info_delete':
        $title = "";
        $disabled = "";
        $hidden = "";
        $agree = "";
        $get = ["","","",""];
        $pwd = '<tr><td>Password</td>
                    <td><input type="password" name="pwdset" class="form-control" value="" aria-label="Password"></td></tr>
                    <tr><td>Password Confirmation</td>
                    <td><input type="password" name="pwdcon" class="form-control" value="" aria-label="Password"></td></tr>';
        if($action=='admin_info_update') {
            $title = '<h3 class="font-weight-bolder text-success text-gradient">Update Admin</h3>';
            $agree = '<button type="submit" class="btn bg-gradient-success" onclick="submitAction()">Update</button>';
        } else if($action=='admin_info_delete') {
            $title = '<h3 class="font-weight-bolder text-danger text-gradient">Delete Admin</h3>';
            $disabled = "disabled";
            $agree = '<button type="submit" class="btn bg-gradient-danger" onclick="submitAction()">Delete</button>';
        } else if($action=='admin_info_create') {
            $title = '<h3 class="font-weight-bolder text-primary text-gradient">Create Admin</h3>';
            $disabled = "";
            $agree = '<button type="submit" class="btn bg-gradient-primary" onclick="submitAction()">Create</button>';
        }
        if($action!='admin_info_create'){
            $sql = "SELECT fullname,phone,email,company,suspended FROM admin_info WHERE uuid='".$id."' LIMIT 1;";
            $get = mysqli_fetch_all(mysqli_query($con, $sql))[0];
            $pwd = "";
        }
        $response['title'] = $title;
        $response['body'] = '<form class="form text-left app-controller">
                                <table style="width:100%;"><tbody>
                                <tr hidden><td>Target</td>
                                <td><input type="text" name="target" class="form-control" value="'.$id.'" ></td></tr>
                                <tr hidden><td>Action</td>
                                <td><input type="text" name="action" class="form-control" value="'.$action.'"></td></tr>
                                <tr><td>Full Name</td>
                                <td> <input type="text" name="fullname" class="form-control" value="'.$get[0].'" '.$disabled.'></td></tr>
                                <tr><td>Phone Number</td>
                                <td><input type="text" name="phone" class="form-control" value="'.$get[1].'" '.$disabled.'></td></tr>
                                <tr><td>Email</td>
                                <td><input type="email" name="email" class="form-control" value="'.$get[2].'" '.$disabled.'></td></tr>
                                <tr><td>Company Name</td>
                                <td><input type="text" name="company" class="form-control" value="'.$get[3].'" '.$disabled.'></td></tr>
                                <tr><td>Status</td>
                                <td><select class="form-control form-control-sm" name="status" '.$disabled.'>
                                    <option value="0" selected>ACTIVE</option>
                                    <option value="10">SUSPENDED</option>
                                </select></td></tr>
                                '.$pwd.'</tbody></table>
                                <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-link ml-auto" data-bs-dismiss="modal">Close</button>'.$agree.'</div></form>';
    break;

    case 'appuser_info_create':
    case 'appuser_info_update':
    case 'appuser_info_delete':
        $title = "";
        $disabled = "";
        $hidden = "";
        $agree = "";
        $get = ["","","",""];
        $pwd = '<tr><td>Password</td>
                    <td><input type="password" name="pwdset" class="form-control" value="" aria-label="Password"></td></tr>
                    <tr><td>Password Confirmation</td>
                    <td><input type="password" name="pwdcon" class="form-control" value="" aria-label="Password"></td></tr>';
        if($action=='appuser_info_update') {
            $title = '<h3 class="font-weight-bolder text-info text-gradient">Update Appuser</h3>';
            $agree = '<button type="submit" class="btn bg-gradient-info" onclick="submitAction()">Update</button>';
        } else if($action=='appuser_info_delete') {
            $title = '<h3 class="font-weight-bolder text-danger text-gradient">Delete Appuser</h3>';
            $disabled = "disabled";
            $agree = '<button type="submit" class="btn bg-gradient-danger" onclick="submitAction()">Delete</button>';
        } else if($action=='appuser_info_create') {
            $title = '<h3 class="font-weight-bolder text-primary text-gradient">Create Appuser</h3>';
            $disabled = "";
            $agree = '<button type="submit" class="btn bg-gradient-primary" onclick="submitAction()">Create</button>';
        }
        if($action!='appuser_info_create'){
            $sql = "SELECT username,fullname,phone,email,usr_group FROM appuser_info WHERE uuid='".$id."' LIMIT 1;";
            $get = mysqli_fetch_all(mysqli_query($con, $sql))[0];
            $pwd = "";
        }
        $response['title'] = $title;
        $response['body'] = '<form class="form text-left app-controller">
                                <table style="width:100%;"><tbody>
                                <tr hidden><td>Target</td>
                                <td><input type="text" name="target" class="form-control" value="'.$id.'" ></td></tr>
                                <tr hidden><td>Action</td>
                                <td><input type="text" name="action" class="form-control" value="'.$action.'"></td></tr>
                                <tr><td>Username</td>
                                <td><input type="text" name="username" class="form-control" value="'.$get[0].'" '.$disabled.'></td></tr>
                                <tr><td>Full Name</td>
                                <td> <input type="text" name="fullname" class="form-control" value="'.$get[1].'" '.$disabled.'></td></tr>
                                <tr><td>Phone Number</td>
                                <td><input type="text" name="phone" class="form-control" value="'.$get[2].'" '.$disabled.'></td></tr>
                                <tr><td>Email</td>
                                <td><input type="email" name="email" class="form-control" value="'.$get[3].'" '.$disabled.'></td></tr>
                                <tr><td>Group</td>
                                <td><select class="form-control form-control-sm" name="group" '.$disabled.'>
                                    <option value="0" selected>Normal</option>
                                    <option value="10">VIP</option>
                                    <option value="20">Developer</option>
                                    <option value="30">Tester</option>
                                </select></td></tr>'.$pwd.'</tbody></table>
                                <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-link ml-auto" data-bs-dismiss="modal">Close</button>'.$agree.'</div></form>';
    break;

    case 'appuser_info_topup':
        $disabled = "";
        $hidden = "";
        $title = '<h3 class="font-weight-bolder text-success text-gradient">AppUser TopUp</h3>';
        $disabled = "";
        $agree = '<button type="submit" class="btn bg-gradient-success" onclick="submitAction()">Topup</button>';
        $sql = "SELECT username,id FROM appuser_info WHERE uuid='".$id."' LIMIT 1;";
        $get = mysqli_fetch_all(mysqli_query($con, $sql))[0];
        $response['title'] = $title;
        $response['body'] = '<form class="form text-left app-controller">
                                <table style="width:100%;"><tbody>
                                <tr hidden><input type="hidden" name="target" class="form-control" value="'.$id.'" ></tr>
                                <tr hidden><input type="hidden" name="action" class="form-control" value="'.$action.'"></tr>
                                <tr><td>Username</td>
                                <td><input type="text" class="form-control" value="'.$get[0].'" readonly></td></tr>
                                <tr><td>Transfer Type</td><td>
                                <select class="form-control form-control-sm" name="type">
                                    <option value="1" selected>User Purchase Credits</option>
                                    <option value="2">User Transfer Credits</option>
                                    <option value="3">PromoCode Free Credits</option>
                                    <option value="9">Admin Create Credits</option>
                                </select></td></tr>
                                <tr><td>Payment Method</td><td>
                                <select class="form-control form-control-sm" name="method">
                                    <option value="fpx" selected>FPX</option>
                                    <option value="ipay">IPAY</option>
                                    <option value="billplz">Billplz</option>
                                </select></td></tr>
                                <tr><td>Amount</td><td>
                                <select class="form-control form-control-sm" name="amount">
                                    <option value="10" selected>RM 10</option>
                                    <option value="20">RM 20</option>
                                    <option value="50">RM 50</option>
                                    <option value="100">RM 100</option>
                                </select></td></tr></tbody></table>
                                <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-link ml-auto" data-bs-dismiss="modal">Close</button>'.$agree.'</div></form>';
    break;

    case 'appuser_info_spend':
        $disabled = "";
        $hidden = "";
        $title = '<h3 class="font-weight-bolder text-warning text-gradient">AppUser Purchase</h3>';
        $disabled = "";
        $agree = '<button type="submit" class="btn bg-gradient-warning" onclick="submitAction()">Purchase</button>';
        $sql = "SELECT username,id FROM appuser_info WHERE uuid='".$id."' LIMIT 1;";
        $get = mysqli_fetch_all(mysqli_query($con, $sql))[0];
        $response['title'] = $title;
        $response['body'] = '<form class="form text-left app-controller">
                                <table id="appuser-purchase" class="text-center" style="width:100%;">
                                <thead class="font-weight-bold"><tr>
                                    <td style="width:40%;">Product</td>
                                    <td style="width:20%;">Unit</td>
                                    <td style="width:20%;">Price/Unit</td>
                                    <td style="width:20%;">Amount</td></tr></thead>
                                <tbody>
                                <tr hidden><td><input type="hidden" name="target" class="form-control" value="'.$id.'" ></tr>
                                <tr hidden><input type="hidden" name="action" class="form-control" value="'.$action.'"></tr>
                                <tr>
                                    <td><input type="text" name="product[]" class="form-control" value=""></td>
                                    <td><input type="number" name="unit[]" class="form-control unit" onchange="calcTotal(this)" min="1" max="1000" value="1"></td>
                                    <td><input type="number" name="price[]" class="form-control price" onchange="calcTotal(this)" min="0.00" max="99999.99" value="0.00"></td>
                                    <td><input type="number" name="amount[]" class="form-control total" value="0.00" readonly></td>
                                </tr>
                                <tr class="font-weight-bold text-right">
                                    <td colspan="2"><button class="btn btn-light w-100" onclick="addmore()"><i class="fas fa-plus" aria-hidden="true"></i> Add More Item(s)</button></td>
                                    <td class="text-right">Total</td>
                                    <td><input type="number" id="total" name="total" class="form-control table-warning" value="0.00" readonly></td>
                                </tr>
                                </tbody></table>
                                <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-link ml-auto" data-bs-dismiss="modal">Close</button>'.$agree.'</div></form>';
    break;


    case 'appuser_spend_update':
    case 'appuser_spend_delete':
        $title = "";
        $disabled = "";
        $hidden = "";
        $agree = "";
        if($action=='admin_info_update') {
            $title = '<h3 class="font-weight-bolder text-success text-gradient">Update Purchase</h3>';
            $agree = '<button type="submit" class="btn bg-gradient-success" onclick="submitAction()">Update</button>';
        } else if($action=='admin_info_delete') {
            $title = '<h3 class="font-weight-bolder text-danger text-gradient">Delete Purchase</h3>';
            $disabled = "disabled";
            $agree = '<button type="submit" class="btn bg-gradient-danger" onclick="submitAction()">Delete</button>';
        }
        $sql = "SELECT spend_id,product_desc,unit_purchase,unit_price,payment_total FROM appuser_spend WHERE id=".$id." LIMIT 1;";
        $get = mysqli_fetch_all(mysqli_query($con, $sql))[0];
        $pwd = "";
        $response['title'] = $title;
        $response['body'] = '<form class="form text-left app-controller">
                                <table style="width:100%;"><tbody>
                                <tr hidden><td>Target</td>
                                <td><input type="text" name="target" class="form-control" value="'.$id.'" ></td></tr>
                                <tr hidden><td>Action</td>
                                <td><input type="text" name="action" class="form-control" value="'.$action.'"></td></tr>
                                <tr><td>Full Name</td>
                                <td> <input type="text" name="fullname" class="form-control" value="'.$get[0].'" '.$disabled.'></td></tr>
                                <tr><td>Phone Number</td>
                                <td><input type="text" name="phone" class="form-control" value="'.$get[1].'" '.$disabled.'></td></tr>
                                <tr><td>Email</td>
                                <td><input type="email" name="email" class="form-control" value="'.$get[2].'" '.$disabled.'></td></tr>
                                <tr><td>Company Name</td>
                                <td><input type="text" name="company" class="form-control" value="'.$get[3].'" '.$disabled.'></td></tr>
                                <tr><td>Status</td>
                                <td><select class="form-control form-control-sm" name="status" '.$disabled.'>
                                    <option value="0" selected>ACTIVE</option>
                                    <option value="10">SUSPENDED</option>
                                </select></td></tr>
                                '.$pwd.'</tbody></table>
                                <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-link ml-auto" data-bs-dismiss="modal">Close</button>'.$agree.'</div></form>';
    break;

    case 'task_info_create':
    case 'task_info_update':
    case 'task_info_delete':
        $title = "";
        $disabled = "";
        $hidden = "";
        $agree = "";
        $get = ["","","",""];
        if($action=='task_info_update') {
            $title = '<h3 class="font-weight-bolder text-info text-gradient">Update Cronjob</h3>';
            $agree = '<button type="submit" class="btn bg-gradient-info" onclick="submitAction()">Update</button>';
        } else if($action=='task_info_delete') {
            $title = '<h3 class="font-weight-bolder text-danger text-gradient">Delete Cronjob</h3>';
            $disabled = "disabled";
            $agree = '<button type="submit" class="btn bg-gradient-danger" onclick="submitAction()">Delete</button>';
        } else if($action=='task_info_create') {
            $title = '<h3 class="font-weight-bolder text-primary text-gradient">Create Cronjob</h3>';
            $disabled = "";
            $agree = '<button type="submit" class="btn bg-gradient-primary" onclick="submitAction()">Create</button>';
        }
        if($action!='task_info_create'){
            $sql = "SELECT task_name,task_desc,task_file,email,usr_group FROM task_info WHERE id='".$id."' LIMIT 1;";
            $get = mysqli_fetch_all(mysqli_query($con, $sql))[0];
            $pwd = "";
        }
        $response['title'] = $title;
        $response['body'] = '<form class="form text-left app-controller">
                                <table style="width:100%;"><tbody>
                                <tr hidden><td>Target</td>
                                <td><input type="text" name="target" class="form-control" value="'.$id.'" ></td></tr>
                                <tr hidden><td>Action</td>
                                <td><input type="text" name="action" class="form-control" value="'.$action.'"></td></tr>
                                <tr><td>Property</td>
                                <td><input type="text" name="loc" class="form-control" value="" readonly></td></tr>
                                <tr><td>Task Name</td>
                                <td><input name="name" class="form-control" value="'.$get[0].'" '.$disabled.'></td></tr>
                                <tr><td>Task Description</td>
                                <td><textarea name="desc" class="form-control" value="'.$get[1].'" '.$disabled.'></textarea></td></tr>
                                <tr><td>Script</td>
                                <td><select class="form-control form-control-sm" name="file" '.$disabled.'>
                                    <option value="taskA.php" selected>Generate Maintainence Fees</option>
                                    <option value="taskB.php">Generate Sinking Funds</option>
                                    <option value="taskC.php">Generate Penalties</option>
                                    <option value="taskD.php">Generate </option>
                                </select></td></tr>
                                <tr><td>Periodical</td>
                                <td><select class="form-control form-control-sm" name="month" '.$disabled.'>
                                    <option value="yearly" selected>Annually (Selected Month/Day/Hour Only)</option>
                                    <option value="halfly">Bianually (Selected Month/Day/Hour & Every Next 6 Months)</option>
                                    <option value="quarterly">Quarterly (Selected Month/Day/Hour & Every Next 3 Months)</option>
                                    <option value="bimonthly">Bimonthly (Selected Month/Day/Hour & Every Next 2 Months)</option>
                                    <option value="monthly">Monthly (Selected Month/Day/Hour & Every Month)</option>
                                    <option value="weekly">Weekly (Choose Monday 1 to Sunday 7)</option>
                                    <option value="daily">Daily</option>
                                    <option value="hourly">Hourly</option>
                                </select></td></tr>
                                <tr><td>Start Month?</td>
                                <td><select class="form-control form-control-sm" name="month" '.$disabled.'>
                                    <option value="1" selected>January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select></td></tr>
                                <tr><td>On Day?</td>
                                <td><select class="form-control form-control-sm" name="day" '.$disabled.'>
                                    <option value="1" selected>1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                </select></td></tr>
                                <tr><td>On Hour?</td>
                                <td><select class="form-control form-control-sm" name="hour" '.$disabled.'>
                                    <option value="0" selected>0 AM</option>
                                    <option value="1">1 AM</option>
                                    <option value="2">2 AM</option>
                                    <option value="3">3 AM</option>
                                    <option value="4">4 AM</option>
                                    <option value="5">5 AM</option>
                                    <option value="6">6 AM</option>
                                    <option value="7">7 AM</option>
                                    <option value="8">8 AM</option>
                                    <option value="9">9 AM</option>
                                    <option value="10">10 AM</option>
                                    <option value="11">11 AM</option>
                                    <option value="12">12 PM</option>
                                    <option value="13">1 PM</option>
                                    <option value="14">2 PM</option>
                                    <option value="15">3 PM</option>
                                    <option value="16">4 PM</option>
                                    <option value="17">5 PM</option>
                                    <option value="18">6 PM</option>
                                    <option value="19">7 PM</option>
                                    <option value="20">8 PM</option>
                                    <option value="21">9 PM</option>
                                    <option value="22">10 PM</option>
                                    <option value="23">11 PM</option>
                                </select></td></tr>
                                </tbody></table>
                                <div class="mt-4 d-flex justify-content-between">
                                <button type="button" class="btn btn-link ml-auto" data-bs-dismiss="modal">Close</button>'.$agree.'</div></form>';
    break;

    default:
        $response['message'] = 'invalid action';
}

echo json_encode($response);