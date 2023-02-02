###################################
###	File: process.php	3130144	###
###################################
<?php
require_once('config.php');
?>
<?php

if(isset($_POST)){

	$username 		= $_POST['username'];
	$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

		$sql = "INSERT INTO users (username, password ) VALUES(?,?)";
		$stmtinsert = $db->prepare($sql);
		$result = $stmtinsert->execute([$username, $password]);
		if($result){
			echo 'Successfully saved.';
		}else{
			echo 'There were erros while saving the data.';
		}
}else{
	echo 'No data';
}