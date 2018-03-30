<?php

	session_start();
	error_reporting(-1);
	ini_set('display_errors', 1);	
	require_once('../config.inc.php');
 	
	$user_id = $_SESSION['user_id'];

 	if (isset($user_id) && isset($_POST['user_name']) && isset($_POST['email'])){
		$user_name = htmlspecialchars($_POST['user_name']);
		$first_name = htmlspecialchars($_POST['first_name']);
		$last_name = htmlspecialchars($_POST['last_name']);
		$email = htmlspecialchars($_POST['email']);
		$bio = htmlspecialchars($_POST['bio']);

		$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);
		$qh = $dbh->prepare('UPDATE Users SET user_name=?, first_name=?, last_name=?, email=?, bio=? WHERE user_id=?');
		$qh->execute(array($user_name, $first_name, $last_name, $email, $bio, $user_id));
	}

	header("Location:settings.php");
  	exit();
?>