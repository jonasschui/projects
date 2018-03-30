<?php

	session_start();
	error_reporting(-1);
	ini_set("display_errors", 1);
	
	require_once('../config.inc.php');
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);
	
	$content_title = htmlspecialchars($_POST['content_title']);
	$content = htmlspecialchars($_POST['content']);
	$content_date = date("Y-m-d H:i:s");

	if (isset($content) && isset($_SESSION['user_id'])){
		$qh = $dbh->prepare('INSERT INTO announcements (announcement_date, announcer_id, announcement_title, announcement_content) VALUES (?,?,?,?)');
		$qh->execute(array($announcement_date, $_SESSION['active_id'], $announcement_title, $announcement));
	}

	header('Location: profile.php?currency_id=' . $_SESSION['active_id'] . ')';
	exit();
?>