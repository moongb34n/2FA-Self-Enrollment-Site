<?php
/*
Written by: jreyes@prysm.com
v1.1 2017-05

5/23/2017 - latest version
*/

function newEnrollment($key, $user, $username, $time, $type) {
	// Open enrollment log file
	$enrolledList = fopen("/var/www/html/vpn/enrollment-list.txt", "a");

	$cmdResult = exec("expect /var/www/html/vpn/VPNenrollment-new.exp $user $key", $result);
	
	// Set mail variables
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'Bcc: jreyes@prysm.com';
	
	if ($cmdResult == "User already exists") {
		$messageNew = "
			<html><body>
			<h4>User: <span style='color:blue;'>$username</span> already exists in the system</h4>
			<p>Please use <b>Update</b> enrollment type to overwrite your existing account or contact IT support.</p>
			</body></html>
		";
		echo $messageNew;
	} else {
		// Write to enrollment log file
		fwrite($enrolledList, "$type - $username - $key - $time\n");
		$subjectNew = 'You have been successfully enrolled';
		$messageNew = "
			<html><body>
			<h4>Enable Google Authenticator on your mobile device:</h4>
			<ol>
				<li>Download and install <b>Google Authenticator</b> from Play Store (Android) or App Store (Apple), instructions below:</li>
					<ul>
						<li><a href='http://pen.prysm.corp/vpn/files/Google_Authenticator_Android.pdf' target='_blank'>Android</a></li>
						<li><a href='http://pen.prysm.corp/vpn/files/Google_Authenticator_iPhone.pdf' target='_blank'>iPhone</a></li>						
					</ul>
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
	fclose($enrolledList);
}

function updateEnrollment($key, $user, $username, $time, $type) {
	// Open enrollment log file
	$enrolledList = fopen("/var/www/html/vpn/enrollment-list.txt", "a");
	
	$cmdResult = exec("expect /var/www/html/vpn/VPNenrollment-update.exp $user $key", $result);
	
	// Set mail variables
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	$headers .= 'Bcc: jreyes@prysm.com';
	
	if ($cmdResult == "User doesn't exist") {
		$messageUpdate = "
			<html><body>
			<h4>User: <span style='color:blue;'>$username</span> doesn't exist in the system</h4>
			<p>Please use <b>New</b> enrollment type to create your account.</p>
			</body></html>
		";
		echo $messageUpdate;
	} else {
		// Write to enrollment log file
		fwrite($enrolledList, "$type - $username - $key - $time\n");
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
	fclose($enrolledList);
}
?>
