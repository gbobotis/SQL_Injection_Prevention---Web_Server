#######################################
###	File: authenticate.php	3130144	###
#######################################

<?php
//function that returns IP address
function getIpAddr(){
if (!empty($_SERVER['HTTP_CLIENT_IP'])){
	$ipAddr=$_SERVER['HTTP_CLIENT_IP'];
}elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
	$ipAddr=$_SERVER['HTTP_X_FORWARDED_FOR'];
}else{
	$ipAddr=$_SERVER['REMOTE_ADDR'];
}
return $ipAddr;
}

session_start();
if(isset($_SESSION['attempt_again'])){
		$now = time();
		if($now >= $_SESSION['attempt_again']){
			unset($_SESSION['attempt']);
			unset($_SESSION['attempt_again']);
		}
	}

// Change this to your connection info.
$DATABASE_HOST = 'localhost';
$DATABASE_USER = 'root';
$DATABASE_PASS = 'xGdKPpPw#TO';
$DATABASE_NAME = 'GDPR';
// Try and connect using the info above.
$con = mysqli_connect($DATABASE_HOST, $DATABASE_USER, $DATABASE_PASS, $DATABASE_NAME);
if ( mysqli_connect_errno() ) {
	// If there is an error with the connection, stop the script and display the error.
	exit('Failed to connect to MySQL: ' . mysqli_connect_error());
}
//set login attempt if not set
if(!isset($_SESSION['attempt'])){
			$_SESSION['attempt'] = 0;
		}
//check if there are 3 attempts already
if($_SESSION['attempt'] == 3){
	$_SESSION['error'] = 'Attempt limit reach';
	echo '<script language="javascript">';
	echo 'alert("Attempt limit reach")';
	echo '</script>';
}
else{
	// Now we check if the data from the login form was submitted, isset() will check if the data exists.
	if ( !isset($_POST['username'], $_POST['password']) ) {
		// Could not get the data that should have been sent.
		exit('Please fill both the username and password fields!');
	}
	// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
	if ($stmt = $con->prepare('SELECT id, password FROM users WHERE username = ?')) {
		// Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
		$usr = $_POST['username'];
		$stmt->bind_param('s', $_POST['username']);
		$stmt->execute();
		// Store the result so we can check if the account exists in the database.
		$stmt->store_result();
		
		if ($stmt->num_rows > 0) {
			$stmt->bind_result($id, $password);
			$stmt->fetch();
			// Account exists, now we verify the password.
			if (password_verify($_POST['password'], $password)) {
				// Verification success! User has logged-in!
				echo 'Verification success! User has logged-in!';
				$status = "Connected";
				//First we log the incident
				$ip_address=getIpAddr();
				$current_time=date("Y-m-d H:i:s");
				
				// prepare and bind
				$stmt = $con->prepare("INSERT INTO logging (username, IpAddress, TryTime, status) VALUES (?, ?, ?, ?)");
				$stmt->bind_param("ssss",$usr, $ip_address, $current_time, $status);
				$stmt->execute();
				
				// Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
				session_regenerate_id();
				$_SESSION['loggedin'] = TRUE;
				$_SESSION['name'] = $_POST['username'];
				$_SESSION['id'] = $id;
				$_SESSION['success'] = 'Login successful';
				//unset our attempt
				unset($_SESSION['attempt']);
				header('Location: welcome.php');
			} else {
			
				$status = "Fail Login";
				//First we log the incident
				$ip_address=getIpAddr();
				$current_time=date("Y-m-d H:i:s");

				// prepare and bind
				$stmt = $con->prepare("INSERT INTO logging (username, IpAddress, TryTime, status) VALUES (?, ?, ?, ?)");
				$stmt->bind_param("ssss",$usr, $ip_address, $current_time, $status);
				$stmt->execute();
				
				
				$_SESSION['error'] = 'Password incorrect';
				//this is where we put our 3 attempt limit
				$_SESSION['attempt'] += 1;
				//set the time to allow login if third attempt is reach
				if($_SESSION['attempt'] == 3){
					$_SESSION['attempt_again'] = time() + (5*60);
				}
				echo '<script language="javascript">';
				echo 'alert("Incorrect username and/or password!")';
				echo '</script>';
			}
		} else {
			$_SESSION['error'] = 'No account with that username';
			$status = "Fail Login";
			//We log the incident
			$ip_address=getIpAddr();
			$current_time=date("Y-m-d H:i:s");

			// prepare and bind
			$stmt = $con->prepare("INSERT INTO logging (username, IpAddress, TryTime, status) VALUES (?, ?, ?, ?)");
			$stmt->bind_param("ssss",$usr, $ip_address, $current_time, $status);
			$stmt->execute();
			echo '<script language="javascript">';
			echo 'alert("Account does not exist")';
			echo '</script>';
		}

		$stmt->close();
	}
}

?>