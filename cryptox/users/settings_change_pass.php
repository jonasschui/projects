<?php

	session_start();

	// For debugging:
	error_reporting(-1);
	ini_set("display_errors", 1);

	require_once('../config.inc.php');

	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);

	$password = $_POST['old_password'];	
	if (isset($password)){
		
		$query = $dbh->prepare('SELECT * FROM Users WHERE user_id=?');
		$query->execute(array($_SESSION['user_id']));
		$row = $query->fetch();
		
		if ($row && password_verify($password, $row['password_hash'])){
			// check if new password == confirm new password
			if ($_POST['new_password'] == $_POST['confirm_new_password']) {
				// query to change password	
				$hash = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
				$qh = $dbh->prepare('UPDATE Users SET password_hash = ? WHERE user_id = ?');
				$qh->execute(array($hash, $_SESSION['user_id']));
				header('Location: ../users/profile.php?user_id='.$_SESSION['user_id']);
				exit();

			} else {
			}  
		} else {
		}
	}
	header('Location: ../users/settings_change_password.php');


	// Terminate current script
  	exit();
?>