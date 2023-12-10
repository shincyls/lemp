<?php 


function getDomainUrl($con){
  $data = array();
  $limit = 5;
  $sql = "SELECT `value` WHERE `name`='url_domain' LIMIT 1;";
  $url = mysqli_fetch_array(mysqli_query($con, $sql));
  return $url[0];
}

function getPaymentUrl($con){
  $data = array();
  $limit = 5;
  $sql = "SELECT `value` WHERE `name`='pay_domain' LIMIT 1;";
  $url = mysqli_fetch_array(mysqli_query($con, $sql));
  return $url[0];
}

function getVehiclesList2($con,$user){
    $data = array();
    $limit = 5;
    $sql = "SELECT a.vehicle_plate FROM vehicle_info a LEFT JOIN user_info b ON a.userid=b.id WHERE b.username='$user' AND a.status='active' ORDER BY created_on asc LIMIT $limit;";
    $cars = mysqli_fetch_all(mysqli_query($con, $sql));
    for($c=0; $c<$limit; $c++){
      $result = [
        'plate'=>(empty($cars[$c]) ? "EMPTY" : $cars[$c][0]),
        'timestamp'=>'2023-02-21 20:00:00',
        'chargeType'=>'22kw',
        'chargeCost'=>'RM 0.20',
        'location'=>'Shah Alam',
        'selectState'=>($c==0 ? true : false)
      ];
      array_push($data,(object)$result);
    }
    return $data;
}

function getVehiclesList($con,$user){
  $data = array();
  $limit = 5;
  $sql = "SELECT vehicle_plate FROM vehicle_info WHERE userid='$user' AND status='active' LIMIT $limit;";
  $cars = mysqli_fetch_all(mysqli_query($con, $sql));
  for($c=0; $c<$limit; $c++){
    $result = [
      'plate'=>(empty($cars[$c]) ? "EMPTY" : $cars[$c][0]),
      'selectState'=>($c==0 ? true : false)
    ];
    array_push($data,(object)$result);
  }
  return $data;
}

function getChargingList($con,$user){
  $data = array();
  $sql = "SELECT vehicle_plate, start_dt, charge_type, cost_per_min, location_name, paid_id FROM charge_session WHERE userid='$user' AND pay_status='START';";
  $cars = mysqli_fetch_all(mysqli_query($con, $sql));
  foreach($cars as $c){
    $result = [
      'plate'=> $c[0],
      'timestamp'=> $c[1],
      'chargeType'=> $c[2],
      'chargeCost'=> $c[3],
      'location'=> $c[4],
      'payId'=> $c[5]
    ];
    array_push($data,(object)$result);
  }
  return $data;
}

function getTopUpsList($con,$user,$start="",$end=""){
    $where = "";
    if(!empty($start)) $where .= "a.paid_time>='".$start."' AND ";
    if(!empty($end)) $where .= "a.paid_time<DATE_ADD('".$end."', INTERVAL 1 DAY) AND ";
    $data = array();
    $sql = "SELECT * FROM topup_stat a LEFT JOIN user_info b ON a.userid=b.id WHERE ".$where."b.username='$user' ORDER BY a.paid_time desc LIMIT 50;";
    $items = mysqli_query($con, $sql);
    foreach($items as $i){
      $result = [
        'tid'=>$i['paymentid'],
        'amount'=>$i['topup_value'],
        'method'=>'Online Banking',//$i['bank_info'],
        'datetime'=>(new DateTime($i['paid_time']))->format('ymd h:iA'),
        'before'=>$i['balance_before'],
        'after'=>$i['balance_after'],
      ];
      array_push($data,(object)$result);
    }
    return $data;
}

function getChargesList($con,$user,$start="",$end=""){
  $where = "";
    if(!empty($start)) $where .= "a.created_on>='".$start."' AND ";
    if(!empty($end)) $where .= "a.created_on<DATE_ADD('".$end."', INTERVAL 1 DAY) AND ";
    $data = array();
    $sql = "SELECT * FROM charge_session a LEFT JOIN user_info b ON a.userid=b.id WHERE ".$where."b.username='$user' ORDER BY a.created_on desc LIMIT 50;";
    $items = mysqli_query($con, $sql);
    foreach($items as $i){
      $result = [
        'tid'=>$i['paid_id'],
        'amount'=>$i['paid_credits'],
        'loc'=>$i['location_name'],
        'from'=>(new DateTime($i['start_dt']))->format('ymd h:iA'),
        'to'=>(new DateTime($i['end_dt']))->format('ymd h:iA'),
        'status'=>$i['pay_status']
      ];
      array_push($data,(object)$result);
    }
    return $data;
}


function generateTemporaryPassword($length = 12) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}

if(isset($_GET['a']) && $_GET['a']=='1'){
  include("/usr/share/nginx/html/eclimo.lits.com.my/db_entry.php");
  echo "<p>Functions Test</p>";
  $user = '14';
  echo "<p>Cars</p>";
  echo "<PRE>".json_encode(getVehiclesList($con,$user),JSON_PRETTY_PRINT)."</PRE>";
  // echo "<p>TopUps</p>";
  // echo "<PRE>".json_encode(getTopUpsList($con,$user),JSON_PRETTY_PRINT)."</PRE>";
  echo "<p>Charges</p>";
  echo "<PRE>".json_encode(getChargingList($con,$user),JSON_PRETTY_PRINT)."</PRE>";
}