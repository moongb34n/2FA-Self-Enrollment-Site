<?php
/*
Written by: jreyes@prysm.com
v1.0 2017-04

4/7/2017 - latest version
*/

function newEnrollment($key, $user, $username, $time, $type) {
	// Open and write to enrollment log file
	$enrolledList = fopen("/var/www/html/vpn/enrollment-list.txt", "a");
	fwrite($enrolledList, "$type - $username - $key - $time\n");
	fclose($enrolledList);
	
	$cmdResult = exec("expect /var/www/html/vpn/VPNenrollment-new.exp $user $key", $result);
	
	// Set mail variables
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'Bcc: jreyes@prysm.com';
	
	if ($cmdResult == "User already exists") {
		$subjectNew = 'Your enrollment failed';
		$messageNew = "
			<html><body>
			<h4>User: <span style='color:blue;'>$username</span> already exists in the system</h4>
			<p>Please use <b>Update</b> enrollment type to overwrite your existing account or contact IT support.</p>
			</body></html>
		";
		echo $messageNew;
	} else {
		$subjectNew = 'You have been successfully enrolled';
		$messageNew = "
			<html><body>
			<h4>Enable Google Authenticator on your mobile device:</h4>
			<ol>
				<li>Install <b>Google Authenticator</b> from Play Store (Android) or App Store (Apple)</li>
				<li>Open the Google Authenticator app</li>
				<li>Click <b>Begin</b>, create your account using one of the following methods:</li>
					<ul>
						<li>Scanning the QR code on this page</li>
						<li>Manual key entry using your account and the generated <b>Secret Key</b> below</li>
					</ul>
			</ol>
		
			<div style=margin-left:50px>
			<img src=https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/$username%3Fsecret%3D$key>
			<br><br>
			<p><b>Account: </b>$username</p>
			<p><b>Secret Key: </b>$key</p>
			</div>
			</div>
			</body></html>
		";
		echo $messageNew;
		
		mail($username, $subjectNew, $messageNew, $headers);
	}	
}

function updateEnrollment($key, $user, $username, $time, $type) {
	// Open and write to enrollment log file
	$enrolledList = fopen("/var/www/html/vpn/enrollment-list.txt", "a");
	fwrite($enrolledList, "$type - $username - $key - $time\n");
	fclose($enrolledList);
	
	$cmdResult = exec("expect /var/www/html/vpn/VPNenrollment-update.exp $user $key", $result);
	// $cmdResult doesn't have any other output, no forking necessary
	
	// Set mail variables
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'Bcc: jreyes@prysm.com';
	$subjectUpdate = 'Your enrollment has been updated';
	$messageUpdate = "
		<html><body>
		<h4>Update your Google Authenticator account on your mobile device:</h4>
		<ol>
			<li>Open the Google Authenticator app</li>
			<li>Remove the old account</li>
			<li>Click <b>+</b> button to re-create your account, using one of the following methods:</li>
				<ul>
					<li>Scanning the QR code on this page</li>
					<li>Manual key entry using your account and the generated <b>Secret Key</b> below</li>
				</ul>
		</ol>
		
		<div style=margin-left:50px>
		<img src=https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=otpauth://totp/$username%3Fsecret%3D$key>
		<br><br>
		<p><b>Account: </b>$username</p>
		<p><b>Secret Key: </b>$key</p>
		</div>
		</div>
		</body></html>
	";		
	echo $messageUpdate;
		
	mail($username, $subjectUpdate, $messageUpdate, $headers);
}
?>
