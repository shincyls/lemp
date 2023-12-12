<?php

function encrypt($data, $secret) {
    $parts = explode('-',$secret);
    $key = $parts[4].$parts[0].$parts[3].$parts[2].$parts[1];
    $iv = $parts[3].$parts[0].$parts[2];
    return base64_encode(openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv));
}

function decrypt($data, $secret) {
    $parts = explode('-',$secret);
    $key = $parts[4].$parts[0].$parts[3].$parts[2].$parts[1];
    $iv = $parts[3].$parts[0].$parts[2];
    return openssl_decrypt(base64_decode($data), 'aes-256-cbc', $key, 0, $iv);
}

function extractBetweenKeywords($string, $startKeyword, $endKeyword) {
    $startPos = strpos($string, $startKeyword);
    if ($startPos === false) {
        return '';
    }
    $startPos += strlen($startKeyword);
    $endPos = strpos($string, $endKeyword, $startPos);
    if ($endPos === false) {
        return '';
    }
    return substr($string, $startPos, $endPos - $startPos);
}

function extractControllers(){
    $directory = 'app/controllers/';
    $data = array();
    if ($handle = opendir($directory)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != 'auth.php' && $file != '..' && is_file($directory . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
            $fname = str_replace('.php','',$file);
            $data[$fname ] = array();
            $fileHandle = fopen($directory . $file, 'r');
            while (($line = fgets($fileHandle)) !== false) {
                if (stripos($line, "case ") !== false && stripos($line, ":") !== false) {
                $action = extractBetweenKeywords($line,"case '", "':");
                // $actions = array_merge($actions, array($action=>0));
                array_push($data[$fname],$action);
                }
            }
            // $data[$fname] = $actions;
            fclose($fileHandle);
            }
        }
        closedir($handle);
    }
    return $data;
} 

function complexMapper($action){
    $maps = array();
    $directory = './app/controllers/';
    if ($handle = opendir($directory)) {
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && is_file($directory . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $fileHandle = fopen($directory . $file, 'r');
                while (($line = fgets($fileHandle)) !== false) {
                    if (stripos($line, "case ") !== false && stripos($line, ":") !== false) {
                        $name = extractBetweenKeywords($line,"case '", "':");
                        $maps = array_merge($maps,array($name=>$file));
                    }
                }
                fclose($fileHandle);
            }
        }
        closedir($handle);
    }
    return $maps[$action];
}

function simpleMapper($action){
    $maps = [
        "appuser_spend_search"=>"orders.php",
        "appuser_spend_update"=>"orders.php",
        "appuser_spend_delete"=>"orders.php",
        "appuser_info_search"=>"appusers.php",
        "appuser_info_create"=>"appusers.php",
        "appuser_info_update"=>"appusers.php",
        "appuser_info_delete"=>"appusers.php",
        "appuser_info_topup"=>"appusers.php",
        "appuser_info_spend"=>"appusers.php",
        "admin_info_search"=>"admin.php",
        "admin_permission_search"=>"admin.php",
        "admin_permission_update"=>"admin.php",
        "admin_log_search"=>"admin.php",
        "admin_info_create"=>"admin.php",
        "admin_info_update"=>"admin.php",
        "admin_info_delete"=>"admin.php",
        "web_signin"=>"auth.php",
        "web_signup"=>"auth.php",
        "web_forgetpwd"=>"auth.php",
        "admin_change_profile"=>"auth.php",
        "admin_change_password"=>"auth.php"
    ];
    return empty($maps[$action]) ? "" : $maps[$action];
}