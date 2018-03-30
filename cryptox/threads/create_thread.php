<?php

	session_start();
	error_reporting(-1);
	ini_set("display_errors", 1);
if( isset($_POST['submit'] )){
	require_once('../config.inc.php');
	try {
	$dbh = new PDO("mysql:dbname={$config['db_name']};host={$config['db_host']}", $config['db_user'], $config['db_pass']);
	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);	
	$thread_title = htmlspecialchars($_POST['thread_title']);
	$thread_content = htmlspecialchars($_POST['thread_content']);
	$currency_name = htmlspecialchars($_POST['currency_name']);
	$user_id = $_SESSION['user_id'];
	$thread_date = date("Y-m-d H:i:s");

	$qid = $dbh->prepare('SELECT currency_id FROM currencies WHERE currency_name = ?');
	$qid->execute(array($currency_name));
	$current_currency = $qid->fetch();
	$currency_id = $current_currency['currency_id'];
			
	$qh = $dbh->prepare('INSERT INTO threads (thread_title, thread_content, currency_id, user_id, thread_date) VALUES (?, ?, ?, ?, ?)');
	$qh->execute(array($thread_title, $thread_content, $currency_id, $user_id, $thread_date));
	} catch(PDOException $e) {
		echo 'Connection failed: ' . $e->getMessage();
	}
}
else {
	echo "nothing submitted, please head back";
}
header('Location: index.php');
$conn = null;
?>
