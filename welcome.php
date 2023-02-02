###################################
###	File: welcome.php	3130144	###
###################################

<?php
// Initialize the session
session_start();
echo 'You are successfully connected / verified by database';
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: authenticate.php");
    exit;
}
?>