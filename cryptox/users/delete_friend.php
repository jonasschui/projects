<?php

	session_start();

	// For debugging:
	error_reporting(-1);
	ini_set("display_errors", 1);

	require_once('../config.inc.php');
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);

	if (isset($_SESSION['user_id'])) {
		if ($_GET['user_id'] < $_SESSION['user_id']) {
			$u1_id = $_GET['user_id'];
			$u2_id = $_SESSION['user_id'];
		} else {
			$u2_id = $_GET['user_id'];
			$u1_id = $_SESSION['user_id'];
		}
		$qh = $dbh->prepare('DELETE FROM relationships WHERE user_one_id = ? AND user_two_id = ?');
		$qh->execute(array($u1_id, $u2_id));  

		header('Location: ../users/profile.php?user_id='.$_GET['user_id']);

	}

	// Terminate current script
  	exit();
?>