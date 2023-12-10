<?php

class MyWeb {
    public $logo = "";
    public $favicon = "";
    public $company = "";
    public $logadmin = false;
    public $logappusers = false;
    public $logauth = false;
    public $logsales = false;
    public function __construct($value1, $value2) {
        $this->logo_path = $value1;
        $this->favicon_path = $value2;
    }
}

// Create an instance of the class
$web = new MyWeb();
$web->logo = "./app/assets/fav.png"; 
$web->favicon = "./app/assets/fav.png"; 
$web->company = "Homepage";

// Logging
$web->log->auth = false;
$web->log->admin = false;
$web->log->appusers = false;
$web->log->sales = false;