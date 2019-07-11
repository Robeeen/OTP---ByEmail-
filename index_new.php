<?php

date_default_timezone_set('Asia/Dhaka');

$success = "";
$error = "";
$conn = mysqli_connect('localhost', 'root', '', 'otp');

if(!empty($_POST['submit_email'])){
	//$email = $_POST['email'];
	$result = mysqli_query($conn, "SELECT * FROM registered_users WHERE email = '" . $_POST["email"] . "'");
	$count = mysqli_num_rows($result);

	if(empty($count)){
		//generate OTP
		$otp =  rand(100000, 999999);

		//To save Email Address at DB 
		$insert = mysqli_query($conn, "INSERT INTO registered_users(email) VALUES ('" . $_POST["email"] . "') ");
		//To save OTP,expired, create date at DB.
		$result = mysqli_query($conn, "INSERT INTO otp_expiry(otp, is_expired, created_at) VALUES ('" . $otp . "', 0, '" . date("Y-m-d H:i:s") . "')");
		
		//send OTP
		if($result){
			$to = $_POST["email"];
			$subject = "OTP For Verification";
			$message = "One time password for PHP login authetication is: <br /><br />" . $otp;
			$headers = "From: robeeen@gmail.com";
			$headers .= "MIME-Version: 1.0 Shams" . "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
			mail($to, $subject, $message, $headers);		
		}else{
			echo $mysqli->error;
		}
		$current_id = mysqli_insert_id($conn);

			if(!empty($current_id)){
				$success = 1;
			} else {
		     $error = "Email Does not Exists!!";
		 }
	}
}	
	if(!empty($_POST["submit_otp"])){
		//Checking of OTP is expired
		$result = mysqli_query($conn, "SELECT * FROM otp_expiry WHERE otp = '" . $_POST["otp"] ."' AND is_expired != 1 AND NOW() <= DATE_ADD(created_at, INTERVAL 24 HOUR)");
		$count = mysqli_num_rows($result);

			if(!empty($count)){

				$result = mysqli_query($conn, "UPDATE otp_expiry SET is_expired = 1 WHERE otp
					 = '" . $_POST["otp"] ."'");
				$success = 2;
			} else {
				$success = 1;
				$error = "Invalid OTP!";
			}
	}

?>

<!DOCTYPE html>
<html>
<head>
	<title>Login | OTP by Email</title>
	 <link rel="stylesheet" type="text/css"
	  href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	 <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>
	<div class="container">
		<div class="box">
		<form name="" method="POST" action="">
			
				<?php 
					if(!empty($success == 1)){
				?>
			<div><h2>Enter OTP</h2></div>
			<p style="color:#31ab00;">Check your email for the OTP</p>
			<div class="">
				<input type="text" name="otp" placeholder="one time password" class="form-control" required="">
			</div>	
			<br>
			<div class="">
				<input type="submit" name="submit_otp" value="submit" class="btn btn-primary mb-5">
			</div>
			<?php 
				} else if ($success == 2){
			?>	
			<p style="color:#31ab00;">Welcome! You are succsufully Logged in!</p>			
			<?php 
			}	else {
			?>
			<div><h2>Enter your login email</h2></div>
			<div class="">
				<input type="text" name="email" placeholder="Email address" class="form-control" required>
			</div>
				<br>
			<div class="">
				<input type="submit" name="submit_email" value="Submit" class="btn btn-primary mb-5">
			</div>
			<?php 
				}	
			?>			
			
		</form>	
		</div>
</div>
</body>
</html>
