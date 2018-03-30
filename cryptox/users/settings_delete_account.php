<?php

	session_start();

	// For debugging:
	error_reporting(-1);
	ini_set('display_errors', 1);	

	require_once('../config.inc.php');
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);

	if (isset($_SESSION['user_id'])) { 

		$qh = $dbh->prepare('DELETE FROM posts WHERE poster_id = ?');
		$qh->execute(array($_SESSION['user_id']));  
		
		$qh = $dbh->prepare('DELETE FROM relationships WHERE user_one_id = ? OR user_two_id = ?');
		$qh->execute(array($_SESSION['user_id'], $_SESSION['user_id']));  

		$qh = $dbh->prepare('DELETE FROM threads WHERE user_id = ?');
		$qh->execute(array($_SESSION['user_id']));  
		
		$qh = $dbh->prepare('DELETE FROM Users WHERE user_id = ?');
		$qh->execute(array($_SESSION['user_id'])); 
		session_destroy();

		header('Location: ../threads');
	}

	// Terminate current script
  	exit();
?>