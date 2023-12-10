<?php

	$system_hostname = gethostname();
	$hostname='localhost';
	$dbpwd='LiVe$$2023';
	$dbusr='app2dev';
	$server_name = gethostname();
	$dbname='app_main';
	$con = mysqli_connect ($hostname,$dbusr,$dbpwd,$dbname);

	if (mysqli_connect_errno()) {
		echo "Connection Failed => " . mysqli_connect_error();
	} else {
        // Do Nothing
    }

