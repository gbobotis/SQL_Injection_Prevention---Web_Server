###################################
###	File: config.php	3130144	###
###################################

<?php 

$db_user = "root";
$db_pass = "xGdKPpPw#TO";
$db_name = "GDPR";

$db = new PDO('mysql:host=localhost;dbname=' . $db_name . ';charset=utf8', $db_user, $db_pass);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);