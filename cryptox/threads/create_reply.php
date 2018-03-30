<?php

	session_start();
	error_reporting(-1);
	ini_set("display_errors", 1);
	
	require_once('../config.inc.php');
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);
	
	$reply_content = htmlspecialchars($_POST['reply_content']);
	$user_id = $_SESSION['user_id'];
	$reply_date = date("Y-m-d H:i:s");
	$thread_id = htmlspecialchars($_POST['thread_id']);

	if(isset($_SESSION['user_id'])){
		$qh = $dbh->prepare('INSERT INTO replies (reply, reply_date, user_id, thread_id) VALUES (?, ?, ?, ?)');
		$qh->execute(array($reply_content, $reply_date, (int)$user_id, (int)$thread_id));
	}

	header('Location: thread.php?thread_id=' . $thread_id);
	exit();

?>
