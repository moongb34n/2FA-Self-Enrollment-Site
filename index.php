<!--
Web front-end for 2FA self-enrollment.

Written by: jreyes@prysm.com
v1.0 2017-04

4/14/2017 - latest version
-->
<!DOCTYPE html>
<html>
<body>

<?php

error_reporting(E_ALL);
ini_set('display_errors', '0');

session_start();
session_unset();

include 'functions.php';

$user = $_POST['user'];
$password = $_POST['password'];
$type = $_POST['type'];
$domain = "@prysm.com";
$username = ($user . $domain);
$time = date('F j Y, g:i a');

if ($_POST['submit']) {
	$ldap = ldap_connect("10.1.0.75");
	if ($bind = ldap_bind($ldap, $username, $password)) {
		$_SESSION['valid'] = 1;
	} else {
		echo "<h3 style=color:red>Login Failed</h3>";
	}
}
?>

<form action="" method='post'>
<?php 
	if ($_SESSION['valid'] == 0) {
		echo "
		<h3 style=color:blue>Prysm VPN 2 Factor Authentication Enrollment Page</h3>
		<h4><u>What is 2FA?</u></h4>
		<p>
		Two factor authentication or 2FA provides an extra layer of security by requiring clients to provide two authentication <br>
		parameters to verify access. Prysm IT has implemented this system using Google Authenticator to our VPN service to improve security. <br>
		User's connecting to Prysm's VPN service will now be required to input a one-time generated password (generated every 30 seconds) <br>
		after successfully authenticating with their credentials to gain access. 
		</p>
		
		<b>Instructions for installing the free Google Authenticator app can be viewed from the links below:</b>
		<ul>
			<li><a href='http://pen.prysm.corp/vpn/files/Google_Authenticator_Android.pdf' target='_blank'>Android</a></li>
			<li><a href='http://pen.prysm.corp/vpn/files/Google_Authenticator_iPhone.pdf' target='_blank'>iPhone</a></li>
		</ul>
		
		<b>Please login with your credentials to generate a key and enroll your phone to our VPN 2FA system.</b>
		<table>
			<tr><td>Username:</td><td><input size='15' type='text' name='user'></td><td>@prysm.com</td></tr>
			<tr><td>Password:</td><td><input size='15' type='password' name='password'></td></tr>
			<tr>
				<td>Enrollment type:</td>
				<td>
				<input type='radio' name='type' value='new' checked>New<br>
				<input type='radio' name='type' value='update'>Update<br>
				</td>
			</tr>
		</table>
		<input type='submit' name='submit' value='Submit'>
		";
	} else {
		// Generate GA secret key and put into email message
		$key = exec('python26 /var/www/html/vpn/generateKey.py');
		if ($type == 'new') {
			//echo "New";
			newEnrollment($key, $user, $username, $time, $type);
		} else {
			//echo "Updated";
			updateEnrollment($key, $user, $username, $time, $type);
		}
	}
?>
</form>
</body>
</html>
