<?php
	session_start();
	//including dB conn
	include('../dBConfig.php');
	//Enter the timeout in register
	//Get current timestamp
	date_default_timezone_set('Asia/Kolkata');
	$timeout = date('Y-m-d H:i:s', time());
	$values = [
		'timeout' => $timeout,
		'token' => $_SESSION['token']
	];
	$sql = "UPDATE register SET timeout = :timeout WHERE token = :token";
	$stmt = $pdo -> prepare($sql); 
	$stmt-> execute($values);
	$res = $stmt -> rowCount();
	if ($res > 0) {
		session_destroy();
		header('Location: http://localhost/');
	}
?>