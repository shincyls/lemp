<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include './app/helpers/permissions.php';
    if(isset($_SESSION['secret']) && strlen($_SESSION['secret'])>=50 && strlen($_SESSION['uuid'])==36){
        $data = decrypt($_SESSION['secret'], $_SESSION['uuid']);
        $allows = explode(",", $data);
        if(isset($_POST['action']) && !empty($_POST['action']) && in_array($_POST['action'],$allows)){
            $controller = simpleMapper($_POST['action']);
            if(empty($controller)){
                $response['status'] = 'danger';
                $response['message'] = "No permission to perform this action";
                // $response['message'] = $_POST['action']."/".$controller."<ul><li>".join("</li><li>",$allows)."</li></ul>";
                echo json_encode($response);
            } else {
                include 'app/controllers/'.$controller;
            }
        } else {
            $_POST['action'] = "invalid_action";
            $response['status'] = 'danger';
            $response['message'] = 'invalid action no controller';
            echo json_encode($response);
        }
    } else if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
        $_POST['action'] = 'session_expired';
        $response['status'] = 'danger';
        $response['message'] = 'Session Expired, Redirect to Sign-In in 5 seconds <script>window.setTimeout(function(){window.location.href = "/signin";}, 5000);</script>';
        echo json_encode($response);
    } else if (!isset($_SESSION['secret']) || empty($_SESSION['secret'])) {
        $_POST['action'] = 'no_permission';
        $response['status'] = 'danger';
        $response['message'] = 'No permission and unable perform action, please check with system admin';
        echo json_encode($response);
    } else {
        $response['status'] = 'danger';
        $response['message'] = 'Other issues, please check with admin';
        echo json_encode($response);
    }
    
}



