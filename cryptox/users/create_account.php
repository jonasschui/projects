<?php

	session_start();

	// For debugging:
	error_reporting(-1);
	ini_set("display_errors", 1);

	require_once('../config.inc.php');

	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);
	
	$user_name = htmlspecialchars($_POST['user_name']);
	$password_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
	$first_name = htmlspecialchars($_POST['first_name']);
	$last_name = htmlspecialchars($_POST['last_name']);
	$email = htmlspecialchars($_POST['email']);

	if (isset($user_name) && isset($_POST['password']) && isset($email)){

		$qh = $dbh->prepare('INSERT INTO Users (user_name, password_hash, first_name, last_name, email) VALUES (?,?,?,?,?)');
		$qh->execute(array($user_name, $password_hash, $first_name, $last_name, $email));
		header('Location: ../index.php');
	}

	// Terminate current script
  	exit();
?>