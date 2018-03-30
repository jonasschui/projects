<?php

	session_start();

	$target_dir = "../profile-pictures/";
	$path_info = pathinfo(basename($_FILES["fileToUpload"]["name"]));
	$target_file = $target_dir . $_SESSION['user_id'] . '.' . $path_info['extension'];
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
	    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	    if($check !== false) {
	        header('Location: settings_profile_picture_error.php');
	        $uploadOk = 1;
	    } else {
	        header('Location: settings_profile_picture_error.php');
	        $uploadOk = 0;
	    }
	}
	// Check if the user currently has another image in use
	$jpgimg = '../profile-pictures/' . $_SESSION['user_id'] . '.' . 'jpg';
	$jpegimg = '../profile-pictures/' . $_SESSION['user_id'] . '.' . 'jpeg';
	$pngimg = '../profile-pictures/' . $_SESSION['user_id'] . '.' . 'png';
	if (file_exists ($jpgimg)){
		unlink($jpgimg);
	} elseif (file_exists ($jpegimg)){
		unlink($jpegimg);
	} elseif (file_exists ($pngimg)){
		unlink($pngimg);
	}
	// Check file size
	if ($_FILES["fileToUpload"]["size"] > 3000000) {
	    header('Location: settings_profile_picture_error.php');
	    $uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
	    header('Location: settings_profile_picture_error.php');
	    $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	    header('Location: settings_profile_picture_error.php');
	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        header('Location: settings_profile_picture_succes.php');
	    } else {
	        header('Location: settings_profile_picture_error.php');
	    }
	}
?>