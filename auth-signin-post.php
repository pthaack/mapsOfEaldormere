<?php
/// Basic access authentication
/// ToDo: update server user name and password
$blnStatus = 'false';
if( isset( $_POST['user'] ) && isset( $_POST['pass'] ) ) {
	$_SERVER['PHP_AUTH_USER'] = $_POST['user'];
	$_SERVER['PHP_AUTH_PW'] = $_POST['pass'];
	$blnStatus = 'true';		
}
echo '<?xml version="1.0" encoding="UTF-8"?>' . chr(13) . chr(10);
echo '<signinPost>' . chr(13) . chr(10);
echo ' <posted>'. $blnStatus .'</posted>' . chr(13) . chr(10);
echo ' <uname>'. $_SERVER['PHP_AUTH_USER'] .'</uname>' . chr(13) . chr(10);
echo ' <upass>'. $_SERVER['PHP_AUTH_PW'] .'</upass>' . chr(13) . chr(10);
echo '</signinPost>' . chr(13) . chr(10);
?>
