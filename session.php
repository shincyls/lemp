<?php
session_start();
$uri = explode("?",$_SERVER['REQUEST_URI'])[0];
if($uri=="/signout"){
  session_destroy();
}
// For API Users
if(substr($uri, 0, 7)=="/api/v1"){
  // Check JWT Token First
  // If No Token Return Message
  $response['status'] = "success";
  $response['message'] = "not available";
  echo json_encode($response);
} 
// For WEB Users
else {
  // Routes allowed to access without login (redirect to index) OR not allowed to access with login (redirect to home)
  $allowed = ['','/','/signin','/forgetpwd','/ctrl/auth','/signup','/404'];
  if(isset($_SESSION['uuid']) && isset($_SESSION['login']) && strlen($_SESSION['uuid'])==36 && $_SESSION['login']){
    // If logged in, redirect to home is 
    if(in_array($uri,$allowed)) {
      header('Location: /home');
    }
  } else {
    // Not Logged only allow to external pages
    if(in_array($uri,$allowed)) {
      // Do nothing, allow proceed
    } else {
      if(substr($uri, 0, 5)=="/ctrl" || substr($uri, 0, 6)=="/modals"){
        $response['status'] = "warning";
        $response['message'] = "session expired, please login again";
        echo json_encode($response);
        exit;
      } else {
        $to = "Location: /?url=".substr($uri,1);
        header($to);
        // header('Location: /');
      }
    }
  }
}