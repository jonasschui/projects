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
		$qh = $dbh->prepare('INSERT INTO posts (post_date, poster_id, post_title, post_content) VALUES (?,?,?,?)');
		$qh->execute(array($content_date, $_SESSION['user_id'], $content_title, $content));
	}

	header('Location: timeline.php');
	exit();
?>