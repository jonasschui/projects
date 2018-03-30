<?php

	session_start();
	error_reporting(-1);
	ini_set("display_errors", 1);
	include 'password.inc.php';
	require_once('../config.inc.php');
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);

	$user_name = htmlspecialchars($_POST['user_name']);
	$password = htmlspecialchars($_POST['password']);
	
	if (isset($user_name) && isset($password)){
		
		$query = $dbh->prepare('SELECT * FROM Users WHERE user_name=?');
		$query->execute(array($user_name));
		$row = $query->fetch();
		
		if ($row && password_verify($password, $row['password_hash'])){
			$_SESSION['user_id'] = $row['user_id'];
			header('Location: timeline.php');
		} else {
			header('Location: ../index.php');
		}
	}

  	exit();
?>
