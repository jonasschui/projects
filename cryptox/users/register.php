<?php

session_start();
error_reporting(-1);
ini_set("display_errors", 1);

?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width"/>
		<link rel="stylesheet" href="../css/reset.css" />
		<link rel="stylesheet" href="../css/main.css">
		<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<script type='text/javascript' src='../js/jquery.particleground.js'></script>
		<script type='text/javascript' src='../js/background.js'></script>
		<title>Sign Up</title>
	</head>
	<body id="body">
		<div class="container-register">
			<form action="create_account.php" method="POST">
				<h2 class="form-header">Let's get you started</h2>
				<input class="input" id="user_name" type="text" placeholder="Username" name="user_name"></br>
				<input class="input" id="password" type="password" placeholder="Password" name="password"></br>
				<input class="input" id="conf_password" type="password" placeholder="Confirm password" name="conf_password"></br>
				<input class="input" id="first_name" type="text" placeholder="First name" name="first_name"></br>
				<input class="input" id="last_name" type="text" placeholder="Last name" name="last_name"></br>
				<input class="input" id="email" type="email" placeholder="Email" name="email"></br>
				<input class="button wide-button green-button" type="submit" name="signin" value="sign up!">
			</form>
		</div>	
	</body>
</html>