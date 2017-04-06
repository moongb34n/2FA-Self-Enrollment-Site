<!--
Web front-end for 2FA self-enrollment.

Written by: jreyes@prysm.com
v1.0 2017-04

4/6/2017 - latest version
-->
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
		<h4>Please login with your credentials to generate a key</h4>
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
