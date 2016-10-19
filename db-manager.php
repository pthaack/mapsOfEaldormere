<?php
/// This file must be included and the function 'accessAuthenticate()' querried before any HTML is used, even a line feed.
// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for ESDB */
define('DB_NAME', 'db_example');
/** MySQL database username */
define('DB_USER', 'db_master');
/** MySQL database password */
define('DB_PASSWORD', 'db_master_pwd');
/** MySQL hostname */
define('DB_HOST', 'localhost');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');
/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/// Digest Access Authentication
//******** VERY IMPORTANT -- call to this function must be performed before all other functions, especially ones with TRACErs *********
function accessAuthenticate() {	 
	
$realm = 'Authorized users of Event Site Database';
$nonce = uniqid();
$opaque = md5($realm);
$strDeathKnell = '';
/// Done: connect to database
if( $objDB = dbAccessOpen() ){
	$strDeathKnell = '<html><head><title>Log in failed</title><style type="text/css">h1 {font-family:Georgia; color:#000080}h2 {font-family:Georgia; color:#000080}body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }.wall {background-color:#ffffd8; background-size: 100px 50px; background-repeat: repeat-x; background-image:url("images/bkgrnd.gif"); }</style></head><body><h1 class="wall">Log in failed</h1>You must enter a valid login ID and password to access this resource.<br />I appologize.<br />The authentication does not work in all browsers. Try Chrome, Edge, or Safari.<br />If you have a menu button, &nbsp; (Android)<img src="images/Screenshot_Menu-in-Chrome.png" width="25" height="32"> &nbsp; (iOS)<img src="images/Screenshot_Menu-in-Safari.png" width="25" height="32"> &nbsp; you can access your browser from there.<br /><img src="images/Screenshot_Open-in-Chrome.png" width="349" height="286"> &nbsp; <img src="images/Screenshot_Open-in-Safari.png" width="338" height="286">';
}
else
{
	$strDeathKnell = '<html><head><title>Log in failed</title><style type="text/css">h1 {font-family:Georgia; color:#000080}h2 {font-family:Georgia; color:#000080}body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }.wall {background-color:#ffffd8; background-size: 100px 50px; background-repeat: repeat-x; background-image:url("images/bkgrnd.gif"); }</style></head><body><h1 class="wall">Log in failed</h1>A database error occured while trying to access this resource.<br />I appologize.<br />If this issue continues, please, let the database administrator know.</body></html>';
	die( $strDeathKnell );
}

if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

		die( $strDeathKnell );
//    die('Text to send if user hits Cancel button');
}


// analyze the PHP_AUTH_DIGEST variable
if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

//		die( $strDeathKnell );
    die('Wrong Credentials!');
	 }
$strQuery = "SELECT `strUserName`, `strPassword`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` FROM `es_users_list` " 
	."WHERE `strUserName`='".$objDB->real_escape_string($data['username'])."'";
$objResult = $objDB->query($strQuery);
if( $objResult->num_rows == 1 ) 
{
	$blnFound = true;
}
else
{
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

    die('Wrong Credentials!');
		die( $strDeathKnell );
}
$arrUser = $objResult->fetch_array(MYSQLI_ASSOC);
dbAccessClose($objDB);

// generate the valid response
$A1 = md5($data['username'] . ':' . $realm . ':' . $arrUser['strPassword']);
$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

if ($data['response'] != $valid_response) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

    die($strDeathKnell); }

// ok, valid username & password
//echo 'You are logged in as: ' . $data['username'];
		return true;
}


/// ToDo: Digest Access Authentication
//******** VERY IMPORTANT -- call to this function must be performed before all other functions, especially ones with TRACErs *********
// http://stackoverflow.com/questions/2384230/what-is-digest-authentication
// https://www.sitepoint.com/understanding-http-digest-access-authentication/
function digestAccessAuthenticate() {	 
	
$realm = 'Authorized users of Event Site Database';
$nonce = uniqid();
$opaque = md5($realm);
$strDeathKnell = '';
/// Done: connect to database
if( $objDB = dbAccessOpen() ){
	$strDeathKnell = '<html><head><title>Log in failed</title><style type="text/css">h1 {font-family:Georgia; color:#000080}h2 {font-family:Georgia; color:#000080}body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }.wall {background-color:#ffffd8; background-size: 100px 50px; background-repeat: repeat-x; background-image:url("images/bkgrnd.gif"); }</style></head><body><h1 class="wall">Log in failed</h1>You must enter a valid login ID and password to access this resource.<br />I appologize.<br />The authentication does not work in all browsers. Try Chrome, Edge, or Safari.</body></html>';
	$strWrongCredentials = '<html><head><title>Log in failed</title><style type="text/css">h1 {font-family:Georgia; color:#000080}h2 {font-family:Georgia; color:#000080}body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }.wall {background-color:#ffffd8; background-size: 100px 50px; background-repeat: repeat-x; background-image:url("images/bkgrnd.gif"); }</style></head><body><h1 class="wall">Log in failed</h1>The username and password you entered does not match our records. Please, try again.</body></html>';
}
else
{
	$strDeathKnell = '<html><head><title>Log in failed</title><style type="text/css">h1 {font-family:Georgia; color:#000080}h2 {font-family:Georgia; color:#000080}body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }.wall {background-color:#ffffd8; background-size: 100px 50px; background-repeat: repeat-x; background-image:url("images/bkgrnd.gif"); }</style></head><body><h1 class="wall">Log in failed</h1>A database error occured while trying to access this resource.<br />I appologize.<br />If this issue continues, please, let the database administrator know.</body></html>';
	die( $strDeathKnell );
}

/*
if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Digest realm="'.$realm.
           '",qop="auth",nonce="'.uniqid().'",opaque="'.md5($realm).'"');

		die( $strDeathKnell );
//    die('Text to send if user hits Cancel button');
*/

if (empty($_SERVER['PHP_AUTH_DIGEST']) ) {
    header('HTTP/1.1 401 Unauthorized');
//    header(sprintf('WWW-Authenticate: Digest realm="%s", nonce="%s", opaque="%s"', $realm, $nonce, $opaque));
//    header('WWW-Authenticate: Digest realm="'.$realm.'",qop="auth",nonce="'.$nonce.'",opaque="'.$opaque.'"');
    header(sprintf('WWW-Authenticate: Digest realm="%s", qop="auth", nonce="%s", opaque="%s"', $realm, $nonce, $opaque));
    header('Content-Type: text/html');
		die( $strDeathKnell );
//    die('Text to send if user hits Cancel button');
}

// analyze the PHP_AUTH_DIGEST variable
if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ) {
    header('HTTP/1.1 401 Unauthorized');
    header(sprintf('WWW-Authenticate: Digest realm="%s", qop="auth", nonce="%s", opaque="%s"', $realm, $nonce, $opaque));
    header('Content-Type: text/html');
		die( $strWrongCredentials ); 
//    die('Wrong Credentials!');
		}
$strQuery = "SELECT `strUserName`, `strPassword`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` FROM `es_users_list` " 
	."WHERE `strUserName`='".$objDB->real_escape_string($data['username'])."'";
$objResult = $objDB->query($strQuery);
if( $objResult->num_rows == 1 )
{
	$blnFound = true;
}
else
{
    header('HTTP/1.1 401 Unauthorized');
    header(sprintf('WWW-Authenticate: Digest realm="%s", qop="auth", nonce="%s", opaque="%s"', $realm, $nonce, $opaque));
    header('Content-Type: text/html');
	die( $strWrongCredentials );
}
$arrUser = $objResult->fetch_array(MYSQLI_ASSOC);
dbAccessClose($objDB);

// generate the valid response
$A1 = md5($data['username'] . ':' . $realm . ':' . $arrUser['strPassword']);
$A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
$valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

if ($data['response'] != $valid_response) {
    header('HTTP/1.1 401 Unauthorized');
    header(sprintf('WWW-Authenticate: Digest realm="%s", qop="auth", nonce="%s", opaque="%s"', $realm, $nonce, $opaque));
    header('Content-Type: text/html');
    die('Wrong Credentials!');
    die( $strWrongCredentials ); }

// ok, valid username & password
//echo 'You are logged in as: ' . $data['username'];
		return true;
/*
*/

}
// function to parse the http auth header; part of Digest Access Authentication 
function http_digest_parse($txt)
{
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}

/// Basic access authentication
//******** VERY IMPORTANT -- call to this function must be performed before all other functions, especially ones with TRACErs *********
function basicAccessAuthenticate() {	 
	
	$validated = false;
	
	if( isset( $_SERVER['PHP_AUTH_USER'] ) )
	{
		$user = $_SERVER['PHP_AUTH_USER'];
		$pass = $_SERVER['PHP_AUTH_PW'];		
		$validated = dbUserFound($user, $pass);
	}
	
	if (!$validated) {
		header('WWW-Authenticate: Basic realm="Venue DB"');
		header('HTTP/1.0 401 Unauthorized');
		$strDeathKnell = '<html><head><title>Log in failed</title><style type="text/css">h1 {font-family:Georgia; color:#000080}h2 {font-family:Georgia; color:#000080}body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }.wall {background-color:#ffffd8; background-size: 100px 50px; background-repeat: repeat-x; background-image:url("images/bkgrnd.gif"); }</style></head><body><h1 class="wall">Log in failed</h1>You must enter a valid login ID and password to access this resource.<br />I appologize.<br />The authentication does not work in all browsers. Try Chrome, Edge, or Safari.</body></html>';
		die( $strDeathKnell );
		return false;
	} else {
		return true;
	}
}


/*  Open Database */
function dbAccessOpen()
{
	$objConn = new mysqli(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);
	if ($objConn->connect_error) 
	{
		echo "Unable to select database: " . $objConn->connect_error; 
		return false;
	}
	else
	{
		return $objConn;
	}
}
/* /Open Database */

/*  Close Database */
function dbAccessClose($objConn)
{
	$objConn->close();
}
/* /Close Database */

// Done: Safely validate login information
function dbUserFound( $uname, $upass ) {
	$blnFound = false;
	$objDB = dbAccessOpen();
	if( $objDB ) {
		$strQuery = "SELECT `strUserName`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` FROM `es_users_list` " 
			."WHERE `strUserName`='".$objDB->real_escape_string($uname)."' AND `strPassword`='".$objDB->real_escape_string($upass)."'";
		$objResult = $objDB->query($strQuery);
		$blnFound = ( $objResult->num_rows == 1 ? true : false );
		dbAccessClose( $objDB );
	}
	return $blnFound;
}

// Done: Safely validate login information
function dbFindUser( $objDB, $uname, $upass ) {
	$arrFound = array();
	if( $objDB ) {
		$strQuery = "SELECT `idUser`, `strUserName`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` FROM `es_users_list` " 
			."WHERE `strUserName`='".$objDB->real_escape_string($uname)."' AND `strPassword`='".$objDB->real_escape_string($upass)."'";
		$objResult = $objDB->query($strQuery);
		$arrFound = $objResult->fetch_array(MYSQLI_ASSOC);
	}
	return $arrFound;
}

// Done: Safely retrieve user id without revealing password
function dbUser( $objDB ) {
	$arrUser = array();
	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];		
	if (!empty($_SERVER['PHP_AUTH_DIGEST'])) {
		if( $data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']) ) {
			$strQuery = "SELECT `strUserName`, `strPassword`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` " 
				."FROM `es_users_list` " 
				."WHERE `strUserName`='".$objDB->real_escape_string($data['username'])."'";
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 1 ) 
			{
				$arrFound = $objResult->fetch_array(MYSQLI_ASSOC);
				$user = $arrFound['strUserName'];
				$pass = $arrFound['strPassword']; 	
			}
		}
	}
	if( $objDB ) {
		$arrUser=dbFindUser( $objDB, $user, $pass );
	}
	return $arrUser;
}

// Done: Safely retrieve user id without revealing password
function accessUser() {
	$arrUser = array();
	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];		
	$objDB = dbAccessOpen();
	if( $objDB ) {
		if (!empty($_SERVER['PHP_AUTH_DIGEST'])) {
			if( $data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']) ) {
				$strQuery = "SELECT `strUserName`, `strPassword`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` " 
					."FROM `es_users_list` " 
					."WHERE `strUserName`='".$objDB->real_escape_string($data['username'])."'";
				$objResult = $objDB->query($strQuery);
				if( $objResult->num_rows == 1 ) 
				{
					$arrFound = $objResult->fetch_array(MYSQLI_ASSOC);
					$user = $arrFound['strUserName'];
					$pass = $arrFound['strPassword']; 
				}
			}
		}	
		$arrUser=dbFindUser( $objDB, $user, $pass );
		dbAccessClose( $objDB );
	}
	return $arrUser;
}
	
// Done: Safely retrieve user id without revealing password
function accessDigestUser() {
	$arrUser = array();
	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];		
	$objDB = dbAccessOpen();
	if( $objDB ) {
		if (!empty($_SERVER['PHP_AUTH_DIGEST'])) {
			if( $data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']) ) {
				$strQuery = "SELECT `strUserName`, `strPassword`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` " 
					."FROM `es_users_list` " 
					."WHERE `strUserName`='".$objDB->real_escape_string($data['username'])."'";
				$objResult = $objDB->query($strQuery);
				if( $objResult->num_rows == 1 ) 
				{
					$arrFound = $objResult->fetch_array(MYSQLI_ASSOC);
					$user = $arrFound['strUserName'];
					$pass = $arrFound['strPassword']; 
				}
			}
		}	
		$arrUser=dbFindUser( $objDB, $user, $pass );
		dbAccessClose( $objDB );
	}
	return $arrUser;
}
	
// Validate and post a venue.  Minimum information -- location name OR extra notes.  If no name given, name will be generated.
// Done: Post validated new venue
function validVenuePost() {
	$blnValid = true;
	$arrFields = array( 'locationName',  
		'address',  
		'city',  
		'province',  
		'postalCode',  
		'icon',  
		'geolatitude',  
		'geolongitude',  
		'phoneNumber',  
		'contact',  
		'email',  
		'webSite',  
		'extraNotes' );
	foreach( $arrFields as $strField ) {
		if( !isset( $_POST[$strField] ) ) 
		{ 
			$blnValid = false;
		}
		else
		{
			if( trim( $_POST['locationName'] ) == '' 
				&& trim( $_POST['extraNotes'] ) == '' 
				|| trim( $_POST['geolatitude'] ) == '' 
				|| trim( $_POST['geolongitude'] ) == '' 
			) {
				$blnValid = false;
			} 
//			if( strpos( '.point.path.area.', $_POST['geolocationStyle'] ) === false ) {
//				$blnValid = false;
//			}
		}
	}
//		echo '<!-- ' . chr(13) . chr( 10 ); print_r($_POST); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	return $blnValid;
}

// Done: Post new venue
function dbVenuePostNew( $objDB ) {
	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];
	if (!empty($_SERVER['PHP_AUTH_DIGEST'])) {
		if( $data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']) ) {
			$strQuery = "SELECT `strUserName`, `strPassword`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` " 
				."FROM `es_users_list` " 
				."WHERE `strUserName`='".$objDB->real_escape_string($data['username'])."'";
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 1 ) 
			{
				$arrFound = $objResult->fetch_array(MYSQLI_ASSOC);
				$user = $arrFound['strUserName'];
				$pass = $arrFound['strPassword']; 
			}
		}
	}	
	$posted = false;		
//		echo '<!-- ' . chr(13) . chr( 10 ); echo $user .' '. $pass; echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	if( validVenuePost() ) {
		$arrUser = dbFindUser( $objDB, $user, $pass );
		$intUser = $arrUser['idUser'];
		$intAdmin = $arrUser['blnAdmin'];
		$strLocationName = ( trim( $_POST['locationName'] ) != '' ? $objDB->real_escape_string( trim( $_POST['locationName'] ) ) : 'New Venue' );
		$strAddress = ( trim( $_POST['address'] ) != '' ? $objDB->real_escape_string( trim( $_POST['address'] ) ) : '' );
		$strCity = ( trim( $_POST['city'] ) != '' ? $objDB->real_escape_string( trim( $_POST['city'] ) ) : '' );
		$strProvince = ( trim( $_POST['province'] ) != '' ? $objDB->real_escape_string( substr( trim( $_POST['province'] ), 0, 2 ) ) : '' );
		$strPostalCode = ( trim( $_POST['postalCode'] ) != '' ? $objDB->real_escape_string( trim( $_POST['postalCode'] ) ) : '' );
		$strGeoLatitude = $objDB->real_escape_string( trim( $_POST['geolatitude'] ) );
		$strGeoLongitude = 	$objDB->real_escape_string( trim( $_POST['geolongitude'] ) );
		$strGeolocationStyle = ( trim( $_POST['geolocationStyle'] ) != '' ? $objDB->real_escape_string( substr( trim( $_POST['geolocationStyle'] ), 0, 1 ) ) : 'P' );
		$strGeolocationPoints = '{"lat":'. $objDB->real_escape_string( trim( $_POST['geolatitude'] ) ) 
			.',"lng":'. $objDB->real_escape_string( trim( $_POST['geolongitude'] ) )
			.',"icon":"'. $objDB->real_escape_string( trim( $_POST['icon'] ) ) .'"}';
		$strPhoneNumber = ( trim( $_POST['phoneNumber'] ) != '' ? $objDB->real_escape_string( trim( $_POST['phoneNumber'] ) ) : '' );
		$strContact = ( trim( $_POST['contact'] ) != '' ? $objDB->real_escape_string( trim( $_POST['contact'] ) ) : '' );
		$strEmail = ( trim( $_POST['email'] ) != '' ? $objDB->real_escape_string( trim( $_POST['email'] ) ) : '' );
		$strWebSite = ( trim( $_POST['webSite'] ) != '' ? $objDB->real_escape_string( trim( $_POST['webSite'] ) ) : '' );
		$txtExtraNotes = ( trim( $_POST['extraNotes'] ) != '' ? $objDB->real_escape_string( trim( $_POST['extraNotes'] ) ) : '' );
		$strQuery = "INSERT INTO `es_venues_list`  " 
			."(`idUser`, " 
			."`strLocationName`, " 
			."`strAddress`, " 
			."`strCity`, " 
			."`strProvince`, " 
			."`strPostalCode`, " 
			."`strGeoLatitude`, " 
			."`strGeoLongitude`, " 
			."`strGeolocationStyle`, " 
			."`strGeolocationPoints`, " 
			."`strPhoneNumber`, " 
			."`strContact`, " 
			."`strEmail`, " 
			."`strWebSite`, " 
			."`txtExtraNotes`) " 
			."VALUES " 
			."( " 
			."'". $intUser ."', " 
			."'". $strLocationName ."', " 
			."'". $strAddress ."', " 
			."'". $strCity ."', " 
			."'". $strProvince ."', " 
			."'". $strPostalCode ."', " 
			."'". $strGeoLatitude ."', " 
			."'". $strGeoLongitude ."', " 
			."'". $strGeolocationStyle ."', " 
			."'". $strGeolocationPoints ."', " 
			."'". $strPhoneNumber ."', " 
			."'". $strContact ."', " 
			."'". $strEmail ."', " 
			."'". $strWebSite ."', " 
			."'". $txtExtraNotes ."' " 
			.") ";
		// echo '<!-- Insert Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$objResult = $objDB->query($strQuery);
		//echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		if( $objResult == 1 ) { $posted = true; }
	}
	return $posted;
}

// ToDo: Post abandon to existing venue
function dbVenuePostUpdate( $objDB, $intVenue ) {
	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];
	$posted = false;		
	if (!empty($_SERVER['PHP_AUTH_DIGEST'])) {
		if( $data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']) ) {
			$strQuery = "SELECT `strUserName`, `strPassword`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` " 
				."FROM `es_users_list` " 
				."WHERE `strUserName`='".$objDB->real_escape_string($data['username'])."'";
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 1 ) 
			{
				$arrFound = $objResult->fetch_array(MYSQLI_ASSOC);
				$user = $arrFound['strUserName'];
				$pass = $arrFound['strPassword']; 
			}
		}
	}	
	if( validVenuePost() ) {
		$arrUser = dbFindUser( $objDB, $user, $pass );
		$intUser = $arrUser['idUser'];
		$intAdmin = $arrUser['blnAdmin'];
		$posted = false;
		$blnGoForUpdate = false;
		if( $intAdmin == 0 )
		{
			$strQuery = "SELECT * "
				. "FROM `es_venues_list` " 
				. "WHERE `idUser` = '". $intUser ."' " 
				. "AND `idVenue`='".$objDB->real_escape_string($intVenue)."' ";  
			//echo '<!-- Update Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			$objResult = $objDB->query($strQuery);
			//echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			if( $objResult->num_rows == 1 ) { $blnGoForUpdate = true; }
		}
		else
		{
			$blnGoForUpdate = true;
		}
		//echo '<!-- Go 4 Update:' . $blnGoForUpdate .'-->' . chr(13) . chr( 10 );
		if( $blnGoForUpdate )
		{
			$strLocationName = ( trim( $_POST['locationName'] ) != '' ? $objDB->real_escape_string( trim( $_POST['locationName'] ) ) : 'New Venue' );
			$strAddress = ( trim( $_POST['address'] ) != '' ? $objDB->real_escape_string( trim( $_POST['address'] ) ) : '' );
			$strCity = ( trim( $_POST['city'] ) != '' ? $objDB->real_escape_string( trim( $_POST['city'] ) ) : '' );
			$strProvince = ( trim( $_POST['province'] ) != '' ? $objDB->real_escape_string( substr( trim( $_POST['province'] ), 0, 2 ) ) : '' );
			$strPostalCode = ( trim( $_POST['postalCode'] ) != '' ? $objDB->real_escape_string( trim( $_POST['postalCode'] ) ) : '' );
			$strGeoLatitude = $objDB->real_escape_string( trim( $_POST['geolatitude'] ) );
			$strGeoLongitude = 	$objDB->real_escape_string( trim( $_POST['geolongitude'] ) );
			$strGeolocationStyle = ( trim( $_POST['geolocationStyle'] ) != '' ? $objDB->real_escape_string( substr( trim( $_POST['geolocationStyle'] ), 0, 1 ) ) : 'P' );
			$strGeolocationPoints = '{"lat":'. $objDB->real_escape_string( trim( $_POST['geolatitude'] ) ) 
				.',"lng":'. $objDB->real_escape_string( trim( $_POST['geolongitude'] ) )
				.',"icon":"'. $objDB->real_escape_string( trim( $_POST['icon'] ) ) .'"}';
			$strPhoneNumber = ( trim( $_POST['phoneNumber'] ) != '' ? $objDB->real_escape_string( trim( $_POST['phoneNumber'] ) ) : '' );
			$strContact = ( trim( $_POST['contact'] ) != '' ? $objDB->real_escape_string( trim( $_POST['contact'] ) ) : '' );
			$strEmail = ( trim( $_POST['email'] ) != '' ? $objDB->real_escape_string( trim( $_POST['email'] ) ) : '' );
			$strWebSite = ( trim( $_POST['webSite'] ) != '' ? $objDB->real_escape_string( trim( $_POST['webSite'] ) ) : '' );
			$txtExtraNotes = ( trim( $_POST['extraNotes'] ) != '' ? $objDB->real_escape_string( trim( $_POST['extraNotes'] ) ) : '' );
			$strQuery = "UPDATE `es_venues_list` " 
				."SET `idUser` = '". $intUser ."', " 
				."`strLocationName` = '". $strLocationName ."', " 
				."`strAddress` = '". $strAddress ."', " 
				."`strCity` = '". $strCity ."', " 
				."`strProvince` = '". $strProvince ."', " 
				."`strPostalCode` = '". $strPostalCode ."', " 
				."`strGeoLatitude` = '". $strGeoLatitude ."', " 
				."`strGeoLongitude` = '". $strGeoLongitude ."', " 
				."`strGeolocationStyle` = '". $strGeolocationStyle ."', " 
				."`strGeolocationPoints` = '". $strGeolocationPoints ."', " 
				."`strPhoneNumber` = '". $strPhoneNumber ."', " 
				."`strContact` = '". $strContact ."', " 
				."`strEmail` = '". $strEmail ."', " 
				."`strWebSite` = '". $strWebSite ."', " 
				."`txtExtraNotes` = '". $txtExtraNotes ."' " 
				."WHERE `idVenue`='".$objDB->real_escape_string($intVenue)."'";
			//echo '<!-- Update Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			$objResult = $objDB->query($strQuery);
			//echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			if( $objResult == 1 ) { $posted = true; }
		}
		
	}
	return $posted;
}

// ToDo: Post archive to existing venue
function dbVenuePostDelete( $objDB, $intVenue ) {
	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];
	$posted = false;		
	if (!empty($_SERVER['PHP_AUTH_DIGEST'])) {
		if( $data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']) ) {
			$strQuery = "SELECT `strUserName`, `strPassword`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` " 
				."FROM `es_users_list` " 
				."WHERE `strUserName`='".$objDB->real_escape_string($data['username'])."'";
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 1 ) 
			{
				$arrFound = $objResult->fetch_array(MYSQLI_ASSOC);
				$user = $arrFound['strUserName'];
				$pass = $arrFound['strPassword']; 
			}
		}
	}	
	$arrUser = dbFindUser( $objDB, $user, $pass );
	$intUser = $arrUser['idUser'];
	$intAdmin = $arrUser['blnAdmin'];
	if( $intAdmin == 0 )
	{
		$strQuery = "SELECT * "
			. "FROM `es_venues_list` " 
			. "WHERE `idUser` = '". $intUser ."' " 
			. "AND `idVenue`='".$objDB->real_escape_string($intVenue)."' ";  
		//echo '<!-- Update Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$objResult = $objDB->query($strQuery);
		//echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		if( $objResult->num_rows == 1 ) { $blnGoForUpdate = true; }
	}
	else
	{
		$blnGoForUpdate = true;
	}
	if( $blnGoForUpdate )
	{
		$strQuery = "UPDATE `es_venues_list` " 
			."SET `idUser` = '". $intUser ."', " 
			."`blnArchive` = '1' " 
			."WHERE `idVenue`='".$objDB->real_escape_string($intVenue)."'";
		//echo '<!-- Update Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$objResult = $objDB->query($strQuery);
		//echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		if( $objResult == 1 ) { $posted = true; }
	}
		
	return $posted;
}

// Fetch nearby locations, put geolocation arrays in an array 
function accessNearby( $strLatitude, $strLongitude ) {
	$arrNearby = array();
	if( is_numeric( $strLatitude ) && is_numeric( $strLongitude ) ) 
	{
		$objDB = dbAccessOpen();
		if( $objDB ) {
			$strQuery = "SELECT * FROM `es_venues_list` "
				. "WHERE `blnArchive` IS NULL "
				. "AND CAST(`strGeoLatitude` AS DECIMAL(10,6)) > ". $strLatitude ." - 0.05 "
				. "AND CAST(`strGeoLatitude` AS DECIMAL(10,6)) < ". $strLatitude ." + 0.05 "
				. "AND CAST(`strGeoLongitude` AS DECIMAL(10,6)) > ". $strLongitude ." - 0.05 "
				. "AND CAST(`strGeoLongitude` AS DECIMAL(10,6)) < ". $strLongitude ." + 0.05 ;";
			$objResult = $objDB->query($strQuery);
//			$arrNearby = $objResult->fetch_array(MYSQLI_ASSOC);
			while( $arrRow = $objResult->fetch_array(MYSQLI_ASSOC) ) // fetch rows while there are rows to fetch. (=) is intentional, not (==)
			{
				$arrNearby[] = $arrRow;
			}
			dbAccessClose( $objDB );
		}
	}
	return $arrNearby;
}
// Fetch mapped locations, put geolocation arrays in an array 
function accessOnMap( $strLatitude, $strLongitude, $strZoomLevel ) {
	$arrOnMap = array();
	if( is_numeric( $strLatitude ) && is_numeric( $strLongitude ) && is_numeric( $strZoomLevel ) ) 
	{
		$objDB = dbAccessOpen();
		if( $objDB ) {
			$strQuery = "SELECT * FROM `es_venues_list` "
				. "WHERE `blnArchive` IS NULL "
				. "AND CAST(`strGeoLatitude` AS DECIMAL(10,6)) > ". $strLatitude ." - ". 360/pow(2,$strZoomLevel) ." "
				. "AND CAST(`strGeoLatitude` AS DECIMAL(10,6)) < ". $strLatitude ." + ". 360/pow(2,$strZoomLevel) ." "
				. "AND CAST(`strGeoLongitude` AS DECIMAL(10,6)) > ". $strLongitude ." - ". 360/pow(2,$strZoomLevel) ." "
				. "AND CAST(`strGeoLongitude` AS DECIMAL(10,6)) < ". $strLongitude ." + ". 360/pow(2,$strZoomLevel) .";";
//	echo '<!-- Locations Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			$objResult = $objDB->query($strQuery);
//			$arrNearby = /*-fetch_array(MYSQLI_ASSOC);
			while( $arrRow = $objResult->fetch_array(MYSQLI_ASSOC) ) // fetch rows while there are rows to fetch. (=) is intentional, not (==)
			{
				$arrOnMap[] = $arrRow;
			}
			dbAccessClose( $objDB );
		}
	}
	return $arrOnMap;
}
// Fetch locations based on distance, put geolocation arrays in an array 
function accessByDistance( $strLatitude, $strLongitude, $strDistanceLatitude, $strDistanceLongitude, $strJSONFilters ) {
	$arrByDist = array();
	if( is_numeric( $strLatitude ) && is_numeric( $strLongitude ) 
			&& is_numeric( $strDistanceLatitude ) && is_numeric( $strDistanceLongitude )
			&& isJson( $strJSONFilters ) ) 
	{
		$objDB = dbAccessOpen();
		$arrFilter = json_decode( $strJSONFilters, true );
		$strFilter = '';
		$strAmenityFilter = '';
		foreach( $arrFilter as $field => $value )
		{
			switch( $field ){
			case 'strLocationName':
				$strFilter .= " AND INSTR(`strLocationName`, '". $objDB->real_escape_string($value) ."')>0 ";
				break;
			case 'strCity':
				$strFilter .= " AND INSTR(`strCity`, '". $objDB->real_escape_string($value) ."')>0 ";
				break;
			case 'strPostalCode':
				$strFilter .= " AND INSTR(`strPostalCode`, '". $objDB->real_escape_string($value) ."')>0 ";
				break;
			case 'strIcon':
				$strIconFilters = '';
				if( is_array( $value ) )
				{
					foreach( $value as $strIcon )
					{
						if( $strIcon != '' )
						{
							$strIconFilters .= ($strIconFilters != ''?' OR ':'') . "INSTR(`strGeolocationPoints`, '". $objDB->real_escape_string($strIcon) ."')>0";
						}
					}
					if($strIconFilters != '')
					{
						$strFilter .= " AND INSTR(`strGeolocationPoints`, 'icon')>0 ";
						$strFilter .= " AND (". $strIconFilters .") ";
					}
				}
//	echo '<!-- Locations filter: ' . chr(13) . chr( 10 ). $strFilter . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
				break;
			case 'strContact':
				$strFilter .= " AND (";
				$strFilter .= "`strContact`!='' ";
				$strFilter .= " OR ";
				$strFilter .= "`idVenue` IN(SELECT `idVenue` "
				. "FROM `es_venues_details` "
				. "WHERE `blnArchive` IS NULL "
				. "AND INSTR(`txtDetailNotes`, '\"strContactPerson\"')>0 ) ";
				$strFilter .= ") ";
				break;
			case 'strContactPerson':
				$strFilter .= " AND (";
				$strFilter .= "INSTR(`strContact`, '". $objDB->real_escape_string($value) ."')>0 ";
				$strFilter .= " OR ";
				$strFilter .= "`idVenue` IN(SELECT `idVenue` "
				. "FROM `es_venues_details` "
				. "WHERE `blnArchive` IS NULL "
				. "AND `strDetailType` = 'contact' "
				. "AND INSTR(`txtDetailNotes`, '". $objDB->real_escape_string($value) ."')>0 ) ";
				$strFilter .= ") ";
				break;
			case 'strAmenities':
				$strFilter .= " AND `idVenue` IN(SELECT `idVenue` "
				. "FROM `es_venues_details` "
				. "WHERE `blnArchive` IS NULL "
				. "AND `strDetailType` = 'amenity' )";
				break;
			case 'strAmenity':
				$strFilter .= " AND `idVenue` IN(SELECT `idVenue` "
				. "FROM `es_venues_details` "
				. "WHERE `blnArchive` IS NULL "
				. "AND `strDetailType` = 'amenity' "
				. "AND INSTR(`txtDetailNotes`, '\"". $objDB->real_escape_string($value) ."\"')>0 ) ";
				break;
			case 'strDetailType':
				$strFilter .= " AND `idVenue` IN(SELECT `idVenue` "
				. "FROM `es_venues_details` "
				. "WHERE `blnArchive` IS NULL "
				. "AND `strDetailType` = '". $objDB->real_escape_string($value) ."') ";
				break;
			case 'strPhotos':
				$strFilter .= " AND `idVenue` IN(SELECT `idVenue` "
				. "FROM `es_venues_details` "
				. "WHERE `blnArchive` IS NULL "
				. "AND `strDetailType` = 'photo' )";
				break;
			default:
				if( strpos( $field, 'strAmenitiesInfo' ) !== false )
				{
				$strAmenityFilter .= ""
				. "AND INSTR(`txtDetailNotes`, '\"". $objDB->real_escape_string($field) ."\"')>0 "
				. "AND INSTR(`txtDetailNotes`, '\"". $objDB->real_escape_string($value) ."\"')>0 ";
				}
				break;
			}
		}
		if( $objDB ) {
			$strQuery = "SELECT "
				. "SQRT( POWER( `strGeoLatitude`- ". $strLatitude .", 2 ) + POWER( `strGeoLongitude` - ". $strLongitude .", 2) ) AS 'Distance', "
				. "`idVenue`, `idUser`, `dteEntry`, `blnArchive`, `strLocationName`, `strAddress`, `strCity`, `strProvince`, "
				. "`strPostalCode`, `strGeoLatitude`, `strGeoLongitude`, `strGeolocationStyle`, `strGeolocationPoints`, "
				. "`strPhoneNumber`, `strContact`, `strEmail`, `strWebSite`, `txtExtraNotes` "
				. "FROM `es_venues_list` ";
			$strQuery .= "WHERE `blnArchive` IS NULL "
				. "AND CAST(`strGeoLatitude` AS DECIMAL(10,6)) > ". $strLatitude ." - ". $strDistanceLatitude ." "
				. "AND CAST(`strGeoLatitude` AS DECIMAL(10,6)) < ". $strLatitude ." + ". $strDistanceLatitude ." "
				. "AND CAST(`strGeoLongitude` AS DECIMAL(10,6)) > ". $strLongitude ." - ". $strDistanceLongitude ." "
				. "AND CAST(`strGeoLongitude` AS DECIMAL(10,6)) < ". $strLongitude ." + ". $strDistanceLongitude ." ";
			$strQuery .= $strFilter;
			if( $strAmenityFilter != '' )
			{
				$strQuery .= " AND `idVenue` IN(SELECT `idVenue` "
				. "FROM `es_venues_details` "
				. "WHERE `blnArchive` IS NULL "
				. $strAmenityFilter . " ) ";
			}
			$strQuery .= "ORDER BY `Distance`";
			$strQuery .= ";";
//	echo '<!-- Locations Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			$objResult = $objDB->query($strQuery);
//			$arrNearby = $objResult->fetch_array(MYSQLI_ASSOC);
			while( $arrRow = $objResult->fetch_array(MYSQLI_ASSOC) ) // fetch rows while there are rows to fetch. (=) is intentional, not (==)
			{
				$arrByDist[] = $arrRow;
			}
			dbAccessClose( $objDB );
		}
	}
	return $arrByDist;
}

// ToDo: Post archive to existing venue
function dbVenueDetailPostDelete( $objDB, $intDetail ) {
	$user = $_SERVER['PHP_AUTH_USER'];
	$pass = $_SERVER['PHP_AUTH_PW'];
	$posted = false;		
	if (!empty($_SERVER['PHP_AUTH_DIGEST'])) {
		if( $data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST']) ) {
			$strQuery = "SELECT `strUserName`, `strPassword`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` " 
				."FROM `es_users_list` " 
				."WHERE `strUserName`='".$objDB->real_escape_string($data['username'])."'";
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 1 ) 
			{
				$arrFound = $objResult->fetch_array(MYSQLI_ASSOC);
				$user = $arrFound['strUserName'];
				$pass = $arrFound['strPassword']; 
			}
		}
	}	
	$arrUser = dbFindUser( $objDB, $user, $pass );
	$intUser = $arrUser['idUser'];
	$intAdmin = $arrUser['blnAdmin'];
	$posted = false;
	$blnGoForUpdate = false;
	if( $intAdmin == 0 )
	{
		$strQuery = "SELECT * "
			. "FROM `es_venues_details` " 
			. "WHERE `idUser` = '". $intUser ."' " 
			. "AND `idDetail`='".$objDB->real_escape_string($intDetail)."' ";  
		//echo '<!-- Update Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$objResult = $objDB->query($strQuery);
		//echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		if( $objResult->num_rows == 1 ) { $blnGoForUpdate = true; }
	}
	else
	{
		$blnGoForUpdate = true;
	}
	if( $blnGoForUpdate )
	{
		$strQuery = "UPDATE `es_venues_details` " 
			."SET `idUser` = '". $intUser ."', " 
			."`blnArchive` = '". true ."' " 
			."WHERE `idDetail`='".$objDB->real_escape_string($intDetail)."'";
		//echo '<!-- Update Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$objResult = $objDB->query($strQuery);
		//echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		if( $objResult == 1 ) { $posted = true; }
	}
		
	return $posted;
}

// Done: Fetch current, previous, and next venue.
function accessVenue( $intVenueID )
{
	$arrVenue = array();
	$arrPrev = array();
	$arrCurr = array('idVenue' => '-1',
		'idUser' => '',
		'dteEntry' => '',
		'strLocationName' => '',
		'blnEditable' => '0',
		'strLocationName' => '',
		'strAddress' => '',
		'strCity' => '',
		'strProvince' => '',
		'strPostalCode' => '',
		'strGeoLatitude' => '43.5934163', 
		'strGeoLongitude' => '-79.6455198', 
		'strGeolocationStyle' => '',
		'strGeolocationPoints' => '',
		'strPhoneNumber' => '',
		'strContact' => '',
		'strEmail' => '',
		'strWebSite' => '',
		'txtExtraNotes' => ''
	);
	$arrNext = array();
	$objDB = dbAccessOpen();
	if( is_numeric( $intVenueID ) ) 
	{
		if( $objDB ) {
			$arrUser = dbUser( $objDB );
			$intUser = $arrUser['idUser'];
			$intAdmin = $arrUser['blnAdmin'];
			$strQuery = "SELECT * FROM `es_venues_list` "
				. "WHERE `blnArchive` IS NULL "
				. "AND `idVenue` < ". $intVenueID ." "
				. "ORDER BY `idVenue` DESC "
				. "LIMIT 0,1 ;";
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 1 )
			{
				$arrPrev = $objResult->fetch_array(MYSQLI_ASSOC);
			}
			else
			{
				$strQuery = "SELECT * FROM `es_venues_list` "
					. "WHERE `blnArchive` IS NULL "
					. "ORDER BY `idVenue` DESC "
					. "LIMIT 0,1 ;";
				$objResult = $objDB->query($strQuery);
				$arrPrev = $objResult->fetch_array(MYSQLI_ASSOC);
			}
			
			//  Get current venue, but include permissions for this user.
			//  $arrUser = $objResult->fetch_array(MYSQLI_ASSOC);
			if( $intAdmin == 0 )
			{
				$strQuery = "SELECT (`idUser`='". $intUser ."') AS 'blnEditable', `es_venues_list`.* "
					. "FROM `es_venues_list` "
					. "WHERE `blnArchive` IS NULL "
					. "AND `idVenue` = ". $intVenueID ." "
					. "ORDER BY `idVenue` "
					. "LIMIT 0,1 ;";
			}
			else
			{
				$strQuery = "SELECT '1' AS 'blnEditable', `es_venues_list`.* "
					. "FROM `es_venues_list` "
					. "WHERE `blnArchive` IS NULL "
					. "AND `idVenue` = ". $intVenueID ." "
					. "ORDER BY `idVenue` "
					. "LIMIT 0,1 ;";
			}
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 1 )
			{
				$arrCurr = $objResult->fetch_array(MYSQLI_ASSOC); 
			}
			$strQuery = "SELECT * FROM `es_venues_list` "
				. "WHERE `blnArchive` IS NULL "
				. "AND `idVenue` > ". $intVenueID ." "
				. "ORDER BY `idVenue` "
				. "LIMIT 0,1 ;";
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 0 )
			{
				$strQuery = "SELECT * FROM `es_venues_list` "
					. "WHERE `blnArchive` IS NULL "
					. "ORDER BY `idVenue` "
					. "LIMIT 0,1 ;";
				$objResult = $objDB->query($strQuery);
				$arrNext = $objResult->fetch_array(MYSQLI_ASSOC);
			}
			else
			{
				$arrNext = $objResult->fetch_array(MYSQLI_ASSOC);				
			}
			
			$arrVenue[] = $arrPrev;
			$arrVenue[] = $arrCurr;
			$arrVenue[] = $arrNext;
			dbAccessClose( $objDB );
		}
	}
	else
	{
		if( $objDB ) {
			$arrUser = dbUser( $objDB );
			$intUser = $arrUser['idUser'];
			$intAdmin = $arrUser['blnAdmin'];
			$strQuery = "SELECT * FROM `es_venues_list` "
				. "WHERE `blnArchive` IS NULL "
				. "ORDER BY `idVenue` DESC "
				. "LIMIT 0,1 ;";
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 1 )
			{
				$arrPrev = $objResult->fetch_array(MYSQLI_ASSOC);
			}
			
			if( $intAdmin == 0 )
			{
				$strQuery = "SELECT (`idUser`='". $intUser ."') AS 'blnEditable', `es_venues_list`.* FROM `es_venues_list` "
					. "WHERE `blnArchive` IS NULL "
					. "ORDER BY `idVenue` "
					. "LIMIT 0,2 ;";
			}
			else
			{
				$strQuery = "SELECT '1' AS 'blnEditable', `es_venues_list`.* FROM `es_venues_list` "
					. "WHERE `blnArchive` IS NULL "
					. "ORDER BY `idVenue` "
					. "LIMIT 0,2 ;";
			}
			$objResult = $objDB->query($strQuery);
			if( $objResult->num_rows == 2 )
			{
 				$arrCurr = $objResult->fetch_array(MYSQLI_ASSOC); 
				$arrNext = $objResult->fetch_array(MYSQLI_ASSOC);				
			}

			$arrVenue[] = $arrPrev;
			$arrVenue[] = $arrCurr;
			$arrVenue[] = $arrNext;
			
			dbAccessClose( $objDB );
		}
	}
	return $arrVenue;
}

// Done: Fetch current venue details.
function accessDetail( $intVenueID )
{
	$arrDetail = array();
	$objDB = dbAccessOpen();
	if( is_numeric( $intVenueID ) ) 
	{
		if( $objDB ) {
			$arrUser = dbUser( $objDB );
			$intUser = $arrUser['idUser'];
			$intAdmin = $arrUser['blnAdmin'];
			if( $intAdmin == 0 )
			{
				$strQuery = "SELECT (`idUser`='". $intUser ."') AS 'blnEditable', `idVenue`, `idDetail`, `idUser`, `dteEntry`, `strLocationName`, `strDetailType`, `txtDetailNotes` "
					. "FROM `es_venues_details` "
					. "WHERE `blnArchive` IS NULL "
					. "AND `idVenue` IN (". $intVenueID .") "
					. "ORDER BY `idDetail` DESC ;";
			}
			else
			{
				$strQuery = "SELECT '1' AS 'blnEditable', `idVenue`, `idDetail`, `idUser`, `dteEntry`, `strLocationName`, `strDetailType`, `txtDetailNotes` "
					. "FROM `es_venues_details` "
					. "WHERE `blnArchive` IS NULL "
					. "AND `idVenue` IN (". $intVenueID .") "
					. "ORDER BY `idDetail` DESC ;";
			}
			$objResult = $objDB->query($strQuery);
			while( $arrRow = $objResult->fetch_array(MYSQLI_ASSOC) ) // fetch rows while there are rows to fetch. (=) is intentional, not (==)
			{
		//echo '<!-- Detail Row: ' . chr(13) . chr( 10 ); print_r( $arrRow ); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
				$arrDetail[] = $arrRow;
			}
		}
	}
	return $arrDetail;
}

// Validate and post a venue.  Minimum information -- location name OR extra notes.  If no name given, name will be generated.
// ToDo: Post validated new venue detail (contact, amenity, photo)
function validVenueDetailPost( $strDetailType ) {
	$blnValid = true;
	$arrFields = array();
	switch( $strDetailType ) 
	{
	  case 'venue':
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $strDetailType . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$arrFields = array( 'venueid',  
			'locationName',  
			'address',  
			'city',  
			'province',  
			'postalCode',  
			'geolatitude',  
			'geolongitude',  
			'phoneNumber',  
			'contact',  
			'email',  
			'webSite',  
			'extraNotes' );
		break;
	  case 'deletevenue':
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $strDetailType . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$arrFields = array( 'venueid',  
			'locationName',  
			'delete' );
		break;
	  case 'contact':
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $strDetailType . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	  	if( isset($_POST['ContactComboBox']) )
		{
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $_POST['ContactComboBox'] . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		  switch($_POST['ContactComboBox'])
		  {
		  	case 'conversation':
				$arrFields = array( 'venueid',  
					'locationName',  
					'ContactComboBox',  
					'ContactPerson',  
					'ContactDate',  
					'ContactEncrypttext' );
				break;
		  	case 'event':
				$arrFields = array( 'venueid',  
					'locationName',  
					'ContactComboBox',  
					'ContactEvent',  
					'ContactDate',  
					'ContactEncrypttext' );
				break;
		  	case 'meeting':
				$arrFields = array( 'venueid',  
					'locationName',  
					'ContactComboBox',  
					'ContactPerson',  
					'ContactDate',  
					'ContactEncrypttext' );
				break;
		  	case 'financial':
				$arrFields = array( 'venueid',  
					'locationName',  
					'ContactComboBox',  
					'ContactDate',  
					'ContactEncryptKey',  
					'ContactEncrypttext' );
				if( isset( $_POST['ContactEncryptKey'] ) ) { if( trim( $_POST['ContactEncryptKey'] ) == '' ) { $blnValid = false; } }	
				break;
			default:
				$blnValid = false;
				$arrFields = array();
				break;
		  }
		}
		else
		{
		// otherwise, give no data fields to check and fail the test.
			$blnValid = false;
			$arrFields = array();
		}
		break;
	  case 'deletecontact':
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $strDetailType . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$arrFields = array( 'venueid',  
			'detailid',  
			'ContactPerson',  
			'delete' );
		break;
/* // Sample JSON data for a contact detail record
{"idUser" : "1", "venueid" : "25", "strLocationName" : "Friends of the Aviary", "strDetailType" : "contact", "arrContactNotes" : 
	{"strContactType" : "conversation",
	"strContactPerson" : "",
	"strContactEvent" : "",
	"strContactDate" : "",
	"strContactText" : ""} 
} 
*/
	  case 'amenity':
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $strDetailType . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $_POST['AmenitiesComboBox'] . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	  	if( isset($_POST['AmenitiesComboBox']) )
		{
		  switch($_POST['AmenitiesComboBox'])
		  {
		  	case 'summary':
			  $arrFields = array( 'venueid',  
				'locationName',  
				'AmenitiesComboBox',  
				'AmenitiesRoom',
				'AmenitiesDescription' );
				break;
		  	case 'room':
			  $arrFields = array( 'venueid',  
				'locationName',  
				'AmenitiesComboBox',  
				'AmenitiesRoom',
				'AmenitiesLength',			
				'AmenitiesWidth',
				'AmenitiesHeight',
				'AmenitiesDescription' );
				break;
		  	case 'gym':
			  $arrFields = array( 'venueid',  
				'locationName',  
				'AmenitiesComboBox',  
				'AmenitiesRoom',
				'AmenitiesLength',			
				'AmenitiesWidth',
				'AmenitiesDescription' );
				break;
		  	case 'field':
			  $arrFields = array( 'venueid',  
				'locationName',  
				'AmenitiesComboBox',  
				'AmenitiesRoom',
				'AmenitiesLength',			
				'AmenitiesWidth',
				'AmenitiesDescription' );
				break;
		  	case 'dais':
			  $arrFields = array( 'venueid',  
				'locationName',  
				'AmenitiesComboBox',  
				'AmenitiesRoom',
				'AmenitiesLength',			
				'AmenitiesWidth',
				'AmenitiesHeight',
				'AmenitiesElevation',
				'AmenitiesDescription' );
				break;
		  	case 'kitchen':
			  $arrFields = array( 'venueid',  
				'locationName',  
				'AmenitiesComboBox',  
				'AmenitiesRoom',
				'AmenitiesLength',			
				'AmenitiesWidth',
				'AmenitiesDescription' );
				break;
		  	case 'bathroom':
			  $arrFields = array( 'venueid',  
				'locationName',  
				'AmenitiesComboBox',  
				'AmenitiesRoom',
				'AmenitiesLength',			
				'AmenitiesWidth',
				'AmenitiesDescription' );
				break;
		  	case 'elevator':
			  $arrFields = array( 'venueid',  
				'locationName',  
				'AmenitiesComboBox',  
				'AmenitiesRoom',
				'AmenitiesLength',			
				'AmenitiesWidth',
				'AmenitiesHeight',
				'AmenitiesDescription' );
				break;
		  	case 'parking':
			  $arrFields = array( 'venueid',  
				'locationName',  
				'AmenitiesComboBox',  
				'AmenitiesRoom',
				'AmenitiesDescription' );
				break;
			default:
				$blnValid = false;
				$arrFields = array();
				break;
			}	
		}	
		else
		{
		// otherwise, give no data fields to check and fail the test.
			$blnValid = false;
			$arrFields = array();
		}
		break;
	  case 'deleteamenity':
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $strDetailType . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$arrFields = array( 'venueid',  
			'detailid',  
			'AmenitiesRoom',  
			'delete' );
		break;
/*  // JSON data for a sample amenities record
{"idUser" : "1", "venueid" : "25", "strLocationName" : "Friends of the Aviary", "strDetailType" : "amenity", "arrAmenitiesNotes" : 
	{"strAmenitiesType" : "summary",
	"strAmenitiesRoom" : "",
	"strAmenitiesLength" : "",
	"strAmenitiesWidth" : "",
	"strAmenitiesHeight" : "",
	"strAmenitiesElevation" : "",
	"strAmenitiesDescription" : "",
	"strAmenitiesInfobigEvent" : "big event",
	"strAmenitiesInfosmallEvent" : "small event",
	"strAmenitiesInfocamping" : "camping",
	"strAmenitiesInfomeetings" : "meetings",
	"strAmenitiesInfofightingIndoor" : "fighting indoor",
	"strAmenitiesInfofightingOutdoor" : "fighting outdoor",
	"strAmenitiesInfofencingIndoor" : "fencing indoor",
	"strAmenitiesInfofencingOutdoor" : "fencing outdoor",
	"strAmenitiesInfoarcheryIndoor" : "archery indoor",
	"strAmenitiesInfoarcheryOutdoor" : "archery outdoor",
	"strAmenitiesInfoclassesIndoor" : "classes indoor",
	"strAmenitiesInfoclassesOutdoor" : "classes outdoor",
	"strAmenitiesInfospecialty" : "specialty",
	"strAmenitiesInfokitchenstove" : "kitchenstove",
	"strAmenitiesInfokitchenoven" : "kitchen oven",
	"strAmenitiesInfokitchenwarmer" : "kitchen warmer",
	"strAmenitiesInfokitchenfridge" : "kitchen fridge",
	"strAmenitiesInfokitchenfreezer" : "kitchen freezer",
	"strAmenitiesInfokitchendishwasher" : "kitchen dish washer",
	"strAmenitiesInfoelectricity" : "electricity",
	"strAmenitiesInfowater" : "water",
	"strAmenitiesInfobathroom" : "bathroom",
	"strAmenitiesInfochangeroom" : "change room",
	"strAmenitiesInfoshower" : "shower",
	"strAmenitiesInfoparkingonsite" : "on-site parking",
	"strAmenitiesInfoparkingpaylot" : "pay lot parking",
	"strAmenitiesInfoparkingstreetfree" : "street parking (free)",
	"strAmenitiesInfoparkingstreetmetered" : "street parking (metered)",
	"strAmenitiesInfotransitbus" : "transit bus",
	"strAmenitiesInfotransitsubway" : "transit subway",
	"strAmenitiesInfotransittrain" : "transit train",
	"strAmenitiesInfoloadingzone" : "loading zone",
	"strAmenitiesInfoloadinginfront" : "loading in front",
	"strAmenitiesInfoloadingoutback" : "loading out back",
	"strAmenitiesInfowheelchairaccessible" : "wheel-chair accessible",
	"strAmenitiesInfoaccessibleelevator" : "elevator",
	"strAmenitiesInfoaccessibleramp" : "ramp",
	"strAmenitiesInfoaccessiblenoStairs" : "no stairs"} 
} 
*/
	  case 'photo':
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $strDetailType . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$arrFields = array( 'venueid',  
			'locationName',  
			'PhotoType',  
//			'PhotoSourceFile',  
			'PhotoServerFile',  
			'PhotoDescription' );
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $_POST['PhotoType'] . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		break;
	  case 'deletephoto':
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $strDetailType . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$arrFields = array( 'venueid',  
			'detailid',  
			'PhotoServerFile',  
			'delete' );
		break;
/* // JSON data for a sample photo record
{"idUser" : "1", "venueid" : "25", "strLocationName" : "Friends of the Aviary", "strDetailType" : "photo", "arrPhotoNotes" : 
	{"strPhotoType" : "JPG",
	"strPhotoSourceFile" : "",
	"strPhotoServerFile" : "",
	"strPhotoDescription" : ""}
}
*/
	  default:
		//echo '<!-- Detail Type: ' . chr(13) . chr( 10 ). $strDetailType . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		// otherwise, give no data fields to check and fail the test.
		$arrFields = array();
		$blnValid = false;
		break;
	}
	foreach( $arrFields as $strField ) {
		if( !isset( $_POST[$strField] ) ) 
		{ 
			$blnValid = false;
		}
		else
		{
			// locationName and idVenue must be declared and have value, as well as one of the name-description pairs.
		  if( isset( $_POST['locationName'] ) ) {
			if( trim( $_POST['locationName'] ) == '' 
				|| trim( $_POST['venueid'] ) == '' 
			) {
				$blnValid = false;
			} 
		  }
		  if( isset( $_POST['ContactComboBox'] ) ) {
			if( trim( $_POST['ContactComboBox'] ) != '' 
				&& trim( $_POST['ContactEncrypttext'] ) == '' 
			) {
				$blnValid = false;
//		echo '<!-- ' . chr(13) . chr( 10 ); print_r($_POST); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			} 
		  }
		  if( isset( $_POST['AmenitiesComboBox'] ) ) {
			if( trim( $_POST['AmenitiesComboBox'] ) != '' 
				&& trim( $_POST['AmenitiesDescription'] ) == '' 
			) {
				$blnValid = false;
//		echo '<!-- ' . chr(13) . chr( 10 ); print_r($_POST); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			} 
		  }
		}
	}
//		echo '<!-- ' . chr(13) . chr( 10 ); print_r($_POST); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	return $blnValid;
}

/*
// ToDo: Post detail info for an existing venue.
function dbVenueDetailPostNew( $objDB, $intVenueID )
{
	$posted = false;		
//		echo '<!-- ' . chr(13) . chr( 10 ); echo $user .' '. $pass; echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	if( validVenueDetailPost() ) {
	}
}
*/

/*
//  function not used.  Okay to delete
function uploadVenue( $objDB, $intVenueID )
{
	$blnResult = 'trace1';
	//	echo '<!-- ' . $blnResult .'-->' . chr(13) . chr( 10 );
	$arrUser = dbUser( $objDB );
	$intUser = $arrUser['idUser'];
	$intAdmin = $arrUser['blnAdmin'];
	
	$intVenueID = $_POST['venueid'];
	$strLocation = $_POST['locationName'];
	$strAddress = ( trim( $_POST['address'] ) != '' ? $objDB->real_escape_string( trim( $_POST['address'] ) ) : '' );
	$strCity = ( trim( $_POST['city'] ) != '' ? $objDB->real_escape_string( trim( $_POST['city'] ) ) : '' );
	$strProvince = ( trim( $_POST['province'] ) != '' ? $objDB->real_escape_string( substr( trim( $_POST['province'] ), 0, 2 ) ) : '' );
	$strPostalCode = ( trim( $_POST['postalCode'] ) != '' ? $objDB->real_escape_string( trim( $_POST['postalCode'] ) ) : '' );
	$strGeoLatitude = $objDB->real_escape_string( trim( $_POST['geolatitude'] ) );
	$strGeoLongitude = 	$objDB->real_escape_string( trim( $_POST['geolongitude'] ) );
	$strGeolocationStyle = ( trim( $_POST['geolocationStyle'] ) != '' ? $objDB->real_escape_string( substr( trim( $_POST['geolocationStyle'] ), 0, 1 ) ) : 'P' );
		$strGeolocationPoints = '{"lat":'. $objDB->real_escape_string( trim( $_POST['geolatitude'] ) ) 
			.',"lng":'. $objDB->real_escape_string( trim( $_POST['geolongitude'] ) )
			.',"icon":"'. $objDB->real_escape_string( trim( $_POST['icon'] ) ) .'"}';
	$strPhoneNumber = ( trim( $_POST['phoneNumber'] ) != '' ? $objDB->real_escape_string( trim( $_POST['phoneNumber'] ) ) : '' );
	$strContact = ( trim( $_POST['contact'] ) != '' ? $objDB->real_escape_string( trim( $_POST['contact'] ) ) : '' );
	$strEmail = ( trim( $_POST['email'] ) != '' ? $objDB->real_escape_string( trim( $_POST['email'] ) ) : '' );
	$strWebSite = ( trim( $_POST['webSite'] ) != '' ? $objDB->real_escape_string( trim( $_POST['webSite'] ) ) : '' );
	$txtExtraNotes = ( trim( $_POST['extraNotes'] ) != '' ? $objDB->real_escape_string( trim( $_POST['extraNotes'] ) ) : '' );
}
*/


function uploadContact( $objDB )
{
	$blnResult = 'trace1';
	//	echo '<!-- ' . $blnResult .'-->' . chr(13) . chr( 10 );
	if( isset($_POST['ContactEncrypttext']) )
	{
		$arrUser = dbUser( $objDB );
		$intUser = $arrUser['idUser'];
		$intAdmin = $arrUser['blnAdmin'];  // not changing saved data.
		
		$intVenueID = $_POST['venueid'];
		$strLocation = $_POST['locationName'];
		$strDesc = $_POST['ContactEncrypttext'];
		$blnResult = 'trace2';
		// Note: if JSON data contains special characters, it will not decode properly.
		$arrDetailNotes = array("strContactType" => ($_POST['ContactComboBox']), 
			"strContactPerson" => ($_POST['ContactPerson']), 
			"strContactEvent" => ($_POST['ContactEvent']), 
			"strContactDate" => ($_POST['ContactDate']), 
			"strContactText" => ($strDesc) );
		$txtDetailNotes = json_encode( $arrDetailNotes );
	
		$strQuery = "INSERT INTO `es_venues_details`  " 
			."(`idUser`, " 
			."`idVenue`, " 
			."`strLocationName`, " 
			."`strDetailType`, " 
			."`txtDetailNotes`) " 
			."VALUES " 
			."( " 
			."'". $intUser ."', " 
			."'". $intVenueID ."', " 
			."'". $objDB->real_escape_string($strLocation) ."', " 
			."'contact', " 
			."'". $objDB->real_escape_string($txtDetailNotes) ."' " 
			.") ";
		//  Cannot trace here. XML Output. 
		//echo '<!-- Insert Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$objResult = $objDB->query($strQuery);
		//  Cannot trace here. XML Output. echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		if( mysqli_error($objDB) == 0 ) { $blnResult = 'true'; }
/*
		*/
	}
	//  Cannot trace here. XML Output. 
	// echo '<!-- Tracer: ' . chr(13) . chr( 10 ). $blnResult . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	return $blnResult;
}

function uploadAmenity( $objDB )
{
	$blnResult = 'trace1';
	//	echo '<!-- Insert Tracer: ' . chr(13) . chr( 10 ). $blnResult . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	if( isset($_POST['AmenitiesDescription']) )
	{
		// Fields from the form page
		// {"idUser" : "1", "venueid" : "25", "strLocationName" : "Friends of the Aviary", "strDetailType" : "amenity", "arrAmenitiesNotes" : 
/*	
		{"strAmenitiesType" : "summary",
	"strAmenitiesRoom" : "",
	"strAmenitiesLength" : "",
	"strAmenitiesWidth" : "",
	"strAmenitiesHeight" : "",
	"strAmenitiesElevation" : "",
	"strAmenitiesDescription" : "",
	"strAmenitiesInfobigEvent" : "big event",
*/
		$arrUser = dbUser( $objDB );
		$intUser = $arrUser['idUser'];
		$intAdmin = $arrUser['blnAdmin'];  // not changing saved data.
		
		$intVenueID = $_POST['venueid'];
		$strLocation = $_POST['locationName'];
		$strDesc = $_POST['AmenitiesDescription'];
		$blnResult = 'trace2';
		// Note: if JSON data contains special characters, it will not decode properly.
		$arrDetailNotes = array("strAmenitiesType" => ($_POST['AmenitiesComboBox']), 
		//$txtDetailNotes = '{"strAmenitiesType" : "'. $objDB->real_escape_string($_POST['AmenitiesComboBox']) .'", '
			"strAmenitiesRoom" => ($_POST['AmenitiesRoom']), 
		//	. '"strAmenitiesRoom" : "'. $objDB->real_escape_string($_POST['AmenitiesRoom']) .'", '
			"strAmenitiesLength" => ($_POST['AmenitiesLength']), 
		//	. '"strAmenitiesLength" : "'. $objDB->real_escape_string($_POST['AmenitiesLength']) .'", '
			"strAmenitiesWidth" => ($_POST['AmenitiesWidth']),
		//	. '"strAmenitiesWidth" : "'. $objDB->real_escape_string($_POST['AmenitiesWidth']) .'", '
			"strAmenitiesHeight" => ($_POST['AmenitiesHeight']), 
		//	. '"strAmenitiesHeight" : "'. $objDB->real_escape_string($_POST['AmenitiesHeight']) .'", '
			"strAmenitiesElevation" => ($_POST['AmenitiesElevation']), 
		//	. '"strAmenitiesElevation" : "'. $objDB->real_escape_string($_POST['AmenitiesElevation']) .'", '
			"strAmenitiesDescription" => ($strDesc) );
		//	. '"strAmenitiesDescription" : "'. $objDB->real_escape_string($strDesc) .'"';
		if( isset($_POST['AmenitiesInfobigEvent']) ) {
			$arrDetailNotes[ 'strAmenitiesInfobigEvent' ] = ($_POST['AmenitiesInfobigEvent']); }
		if( isset($_POST['AmenitiesInfosmallEvent']) ) {
			$arrDetailNotes[ 'strAmenitiesInfosmallEvent' ] = ($_POST['AmenitiesInfosmallEvent']); }
		if( isset($_POST['AmenitiesInfocamping']) ) {
			$arrDetailNotes[ 'strAmenitiesInfocamping' ] = ($_POST['AmenitiesInfocamping']); }
		if( isset($_POST['AmenitiesInfomeetings']) ) {
			$arrDetailNotes[ 'strAmenitiesInfomeetings' ] = ($_POST['AmenitiesInfomeetings']); }
		if( isset($_POST['AmenitiesInfofightingIndoor']) ) {
			$arrDetailNotes[ 'strAmenitiesInfofightingIndoor' ] = ($_POST['AmenitiesInfofightingIndoor']); }
		if( isset($_POST['AmenitiesInfofightingOutdoor']) ) {
			$arrDetailNotes[ 'strAmenitiesInfofightingOutdoor' ] = ($_POST['AmenitiesInfofightingOutdoor']); }
		if( isset($_POST['AmenitiesInfofencingIndoor']) ) {
			$arrDetailNotes[ 'strAmenitiesInfofencingIndoor' ] = ($_POST['AmenitiesInfofencingIndoor']); }
		if( isset($_POST['AmenitiesInfofencingOutdoor']) ) {
			$arrDetailNotes[ 'strAmenitiesInfofencingOutdoor' ] = ($_POST['AmenitiesInfofencingOutdoor']); }
		if( isset($_POST['AmenitiesInfoarcheryIndoor']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoarcheryIndoor' ] = ($_POST['AmenitiesInfoarcheryIndoor']); }
		if( isset($_POST['AmenitiesInfoarcheryOutdoor']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoarcheryOutdoor' ] = ($_POST['AmenitiesInfoarcheryOutdoor']); }
		if( isset($_POST['AmenitiesInfoclassesIndoor']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoclassesIndoor' ] = ($_POST['AmenitiesInfoclassesIndoor']); }
		if( isset($_POST['AmenitiesInfoclassesOutdoor']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoclassesOutdoor' ] = ($_POST['AmenitiesInfoclassesOutdoor']); }
		if( isset($_POST['AmenitiesInfospecialty']) ) {
			$arrDetailNotes[ 'strAmenitiesInfospecialty' ] = ($_POST['AmenitiesInfospecialty']); }
		if( isset($_POST['AmenitiesInfokitchenstove']) ) {
			$arrDetailNotes[ 'strAmenitiesInfokitchenstove' ] = ($_POST['AmenitiesInfokitchenstove']); }
		if( isset($_POST['AmenitiesInfokitchenoven']) ) {
			$arrDetailNotes[ 'strAmenitiesInfokitchenoven' ] = ($_POST['AmenitiesInfokitchenoven']); }
		if( isset($_POST['AmenitiesInfokitchenwarmer']) ) {
			$arrDetailNotes[ 'strAmenitiesInfokitchenwarmer' ] = ($_POST['AmenitiesInfokitchenwarmer']); }
		if( isset($_POST['AmenitiesInfokitchenfridge']) ) {
			$arrDetailNotes[ 'strAmenitiesInfokitchenfridge' ] = ($_POST['AmenitiesInfokitchenfridge']); }
		if( isset($_POST['AmenitiesInfokitchenfreezer']) ) {
			$arrDetailNotes[ 'strAmenitiesInfokitchenfreezer' ] = ($_POST['AmenitiesInfokitchenfreezer']); }
		if( isset($_POST['AmenitiesInfokitchendishwasher']) ) {
			$arrDetailNotes[ 'strAmenitiesInfokitchendishwasher' ] = ($_POST['AmenitiesInfokitchendishwasher']); }
		if( isset($_POST['AmenitiesInfoelectricity']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoelectricity' ] = ($_POST['AmenitiesInfoelectricity']); }
		if( isset($_POST['AmenitiesInfowater']) ) {
			$arrDetailNotes[ 'strAmenitiesInfowater' ] = ($_POST['AmenitiesInfowater']); }
		if( isset($_POST['AmenitiesInfobathroom']) ) {
			$arrDetailNotes[ 'strAmenitiesInfobathroom' ] = ($_POST['AmenitiesInfobathroom']); }
		if( isset($_POST['AmenitiesInfochangeroom']) ) {
			$arrDetailNotes[ 'strAmenitiesInfochangeroom' ] = ($_POST['AmenitiesInfochangeroom']); }
		if( isset($_POST['AmenitiesInfoshower']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoshower' ] = ($_POST['AmenitiesInfoshower']); }
		if( isset($_POST['AmenitiesInfoparkingonsite']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoparkingonsite' ] = ($_POST['AmenitiesInfoparkingonsite']); }
		if( isset($_POST['AmenitiesInfoparkingpaylot']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoparkingpaylot' ] = ($_POST['AmenitiesInfoparkingpaylot']); }
		if( isset($_POST['AmenitiesInfoparkingstreetfree']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoparkingstreetfree' ] = ($_POST['AmenitiesInfoparkingstreetfree']); }
		if( isset($_POST['AmenitiesInfoparkingstreetmetered']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoparkingstreetmetered' ] = ($_POST['AmenitiesInfoparkingstreetmetered']); }
		if( isset($_POST['AmenitiesInfotransitbus']) ) {
			$arrDetailNotes[ 'strAmenitiesInfotransitbus' ] = ($_POST['AmenitiesInfotransitbus']); }
		if( isset($_POST['AmenitiesInfotransitsubway']) ) {
			$arrDetailNotes[ 'strAmenitiesInfotransitsubway' ] = ($_POST['AmenitiesInfotransitsubway']); }
		if( isset($_POST['AmenitiesInfotransittrain']) ) {
			$arrDetailNotes[ 'strAmenitiesInfotransittrain' ] = ($_POST['AmenitiesInfotransittrain']); }
		if( isset($_POST['AmenitiesInfoloadingzone']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoloadingzone' ] = ($_POST['AmenitiesInfoloadingzone']); }
		if( isset($_POST['AmenitiesInfoloadinginfront']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoloadinginfront' ] = ($_POST['AmenitiesInfoloadinginfront']); }
		if( isset($_POST['AmenitiesInfoloadingoutback']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoloadingoutback' ] = ($_POST['AmenitiesInfoloadingoutback']); }
		if( isset($_POST['AmenitiesInfowheelchairaccessible']) ) {
			$arrDetailNotes[ 'strAmenitiesInfowheelchairaccessible' ] = ($_POST['AmenitiesInfowheelchairaccessible']); }
		if( isset($_POST['AmenitiesInfoaccessibleelevator']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoaccessibleelevator' ] = ($_POST['AmenitiesInfoaccessibleelevator']); }
		if( isset($_POST['AmenitiesInfoaccessibleramp']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoaccessibleramp' ] = ($_POST['AmenitiesInfoaccessibleramp']); }
		if( isset($_POST['AmenitiesInfoaccessiblenoStairs']) ) {
			$arrDetailNotes[ 'strAmenitiesInfoaccessiblenoStairs' ] = ($_POST['AmenitiesInfoaccessiblenoStairs']); }
		//$txtDetailNotes .= '}';
		$txtDetailNotes = json_encode( $arrDetailNotes );
	
		$strQuery = "INSERT INTO `es_venues_details`  " 
			."(`idUser`, " 
			."`idVenue`, " 
			."`strLocationName`, " 
			."`strDetailType`, " 
			."`txtDetailNotes`) " 
			."VALUES " 
			."( " 
			."'". $intUser ."', " 
			."'". $intVenueID ."', " 
			."'". $objDB->real_escape_string($strLocation) ."', " 
			."'amenity', " 
			."'". $objDB->real_escape_string($txtDetailNotes) ."' " 
			.") ";
		//  Cannot trace here. XML Output. 
		//echo '<!-- Insert Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$objResult = $objDB->query($strQuery);
		//  Cannot trace here. XML Output. echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		if( mysqli_error($objDB) == 0 ) { $blnResult = 'true'; }
/*
		*/
	}
	//  Cannot trace here. XML Output. 
	// echo '<!-- Tracer: ' . chr(13) . chr( 10 ). $blnResult . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	return $blnResult;
}

function uploadPhoto( $objDB )
{
/*
http://www.w3schools.com/php/php_file_upload.asp
http://php.net/manual/en/function.imagescale.php

	  array("strPhotoType" => "JPG",
		"strPhotoSourceFile" => "badDecisions.jpeg",
		"strPhotoServerFile" => "adfd8765309.jpg",
		"strPhotoWidth" => "1600",
		"strPhotoHeight" => "1200",
		"strPhotoDescription" => "<!-- // Don't try to make bad decisions & do your \"best\". -->")
  
Array
(
    [PhotoSourceFile] =&gt; Array
        (
            [name] =&gt; Stadium - Boys Watching Field.JPG
            [type] =&gt; image/jpeg
            [tmp_name] =&gt; /tmp/php7aOapp
            [error] =&gt; 0
            [size] =&gt; 182717
        )

)
*/

	$blnResult = 'trace1';
	if( isset($_POST['PhotoDescription']) )
	{
	  $blnResult = 'trace1b';
	  if( isset($_FILES['PhotoSourceFile']) )
	  {
		// Fields from the form page
		$intVenueID = $_POST['venueid'];
		$strLocation = $_POST['locationName'];
		$strDesc = $_POST['PhotoDescription'];
		$strPhotoName = $_FILES['PhotoSourceFile']['name'];
		$strPhotoType = $_FILES['PhotoSourceFile']['type'];
		$strPhotoPath = $_FILES['PhotoSourceFile']['tmp_name'];
		$strPhotoErr = $_FILES['PhotoSourceFile']['error'];
		$strPhotoSize = $_FILES['PhotoSourceFile']['size'];
		$blnResult = 'trace2';
		
		$strPhotoMime = mime_content_type($strPhotoPath);
		// Only process JPEG images. Thou shalt not process PNG, nor shalt GIF images be processed. TXT is right out.
		if( $strPhotoMime == 'image/jpg' || $strPhotoMime == 'image/jpeg' )
		{
						
			$blnResult = 'trace3';
			list($intPhotoWidth, $intPhotoHeight, $intPhotoType, $intPhotoAttr) = getimagesize($strPhotoPath);
			// reduce the photo size to maximunm 800 wide OR 600 tall ( 4:3 landscape or 3:4 portrait)
			if( !filter_var($intPhotoWidth, FILTER_VALIDATE_INT, array('options' => array('min_range'=>1))) === false
				&& !filter_var($intPhotoHeight, FILTER_VALIDATE_INT, array('options' => array('min_range'=>1))) === false
			) 
			{
				$blnResult = 'trace4';
				if( $intPhotoWidth > 800 )
				{
					$intPhotoHeight = ceil( $intPhotoHeight * 800 / $intPhotoWidth );
					$intPhotoWidth = 800;
				}
				if( $intPhotoHeight > 600 )
				{
					$intPhotoWidth = ceil( $intPhotoWidth * 600 / $intPhotoHeight );
					$intPhotoHeight = 600;
				}
				
				// Internally generated fields
				//  Cannot trace here. XML Output. 
				//echo '<!-- strDesc: ' . $strDesc  . chr(13) . chr( 10 ). 'Escape: ' . $objDB->real_escape_string($strDesc)  .'-->' . chr(13) . chr( 10 );
				$arrUser = dbUser( $objDB );
				$intUser = $arrUser['idUser'];  // not an update.  Need just the user id
				// $intAdmin = $arrUser['blnAdmin'];  // not changing saved data.
				//echo '<!-- strDesc: ' . $strDesc  . chr(13) . chr( 10 ). 'Escape: ' . $objDB->real_escape_string($strDesc)  .'-->' . chr(13) . chr( 10 );
				$strTargetDir = "data/";
				do { 
					$strTargetFile = uniqid() .'.jpeg';
				} while (file_exists($strTargetDir . $strTargetFile));
		

				// Resize and save
				$rsrOrg = imagecreatefromjpeg($strPhotoPath);
				$rsrScl = imagescale($rsrOrg, $intPhotoWidth, $intPhotoHeight,  IMG_BICUBIC_FIXED);
				imagejpeg($rsrScl, $strTargetDir . $strTargetFile);
				imagedestroy($rsrOrg);
				imagedestroy($rsrScl);
				
				// Note: if JSON data contains special characters, it will not decode properly.
				$arrDetailNotes = array("strPhotoType" => "JPG", 
					"strPhotoSourceFile" => $strPhotoName, 
					"strPhotoServerFile" => $strTargetFile, 
					"strPhotoDescription" => $strDesc, 
					"strPhotoWidth" => $intPhotoWidth, 
					"strPhotoHeight" => $intPhotoHeight );
//				$txtDetailNotes = '{"strPhotoType" : "JPG", '
//					.'"strPhotoSourceFile" : "'. $objDB->real_escape_string($strPhotoName) .'", '
//					.'"strPhotoServerFile" : "'. $strTargetFile .'", '
//					.'"strPhotoDescription" : "'. $objDB->real_escape_string($strDesc) .'", '
//					.'"strPhotoWidth" : "'. $intPhotoWidth .'", '
//					.'"strPhotoHeight" : "'. $intPhotoHeight .'"}';
				$txtDetailNotes = json_encode( $arrDetailNotes );

/*				
INSERT INTO `es_venues_details`
( `idVenue`, `idDetail`, `idUser`, `strLocationName`, `strDetailType`, `txtDetailNotes`)
VALUES
('1', NULL, '1', 'venue_1', 'test', '{"strPhotoType" : "JPG", "strPhotoSourceFile" : "", "strPhotoServerFile" : "", "strPhotoDescription" : ""}');
*//**/

				$strQuery = "INSERT INTO `es_venues_details`  " 
					."(`idUser`, " 
					."`idVenue`, " 
					."`strLocationName`, " 
					."`strDetailType`, " 
					."`txtDetailNotes`) " 
					."VALUES " 
					."( " 
					."'". $intUser ."', " 
					."'". $intVenueID ."', " 
					."'". $objDB->real_escape_string($strLocation) ."', " 
					."'photo', " 
					."'". $objDB->real_escape_string( $txtDetailNotes ) ."' " 
					.") ";
				//  Cannot trace here. XML Output. 
				// echo '<!-- Insert Query: ' . chr(13) . chr( 10 ). $strQuery . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
				$objResult = $objDB->query($strQuery);
				//  Cannot trace here. XML Output. echo '<!-- ' . chr(13) . chr( 10 ); print_r($objResult); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
				if( mysqli_error($objDB) == 0 ) { $blnResult = 'true'; }
				

			}
		} 
	  }
	}
	//  Cannot trace here. XML Output. 
	// echo '<!-- Tracer: ' . chr(13) . chr( 10 ). $blnResult . chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	return $blnResult;
}

function isJson($json_string) {
	json_decode($json_string);
 	return (json_last_error() == JSON_ERROR_NONE);
}

/*
SELECT `strUserName`, `strFirstName`, `strLastName`, `blnAdmin`, `blnPermission` FROM `es_users_list`
WHERE `strUserName`='cinaed' AND `strPassword`='password1'


INSERT INTO `es_venues_list` 
(`idVenue`, `idUser`, `dteEntry`, `strLocationName`, `strAddress`, `strCity`, `strProvince`, `strPostalCode`, `strGeolocationStyle`, `strGeolocationPoints`, `strPhoneNumber`, `strContact`, `strEmail`, `strWebSite`, `txtExtraNotes`) 
VALUES 
(NULL, '1', CURRENT_TIMESTAMP, 'venue_1', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', 'test', NULL);
UPDATE `es_venues_list` 
SET `txtExtraNotes` = 'strrpos'

INSERT INTO `es_venues_details`
( `idVenue`, `idDetail`, `idUser`, `strLocationName`, `strDetailType`, `txtDetailNotes`)
VALUES
('1', NULL, '1', 'venue_1', 'test', '{"strPhotoType" : "JPG", "strPhotoSourceFile" : "", "strPhotoServerFile" : "", "strPhotoDescription" : ""}');


SELECT * FROM `es_venues_list` WHERE CAST(`strGeoLatitude` AS DECIMAL(10,6)) > 43.61461520000001 AND CAST(`strGeoLatitude` AS DECIMAL(10,6)) < 43.69461520000001 AND CAST(`strGeoLongitude` AS DECIMAL(10,6)) > -79.61678979999993 AND CAST(`strGeoLongitude` AS DECIMAL(10,6)) < -79.55678979999993


http://php.net/manual/en/mysqli.real-escape-string.php
http://stackoverflow.com/questions/3683746/escaping-mysql-wild-cards

-- php function return array
php.net/manual/en/functions.returning-values.php
-- php mysql table to array
php.net/manual/en/function.mysql-fetch-array.php
http://php.net/manual/en/mysqli-result.fetch-array.php
http://php.net/manual/en/mysqli-result.fetch-row.php

SELECT `COLUMN_NAME` FROM `information_schema`.`COLUMNS`
WHERE `TABLE_NAME` = `es_users_list'


SELECT `TABLE_NAME`, `COLUMN_NAME`, `DATA_TYPE`, `CHARACTER_MAXIMUM_LENGTH`, `COLLATION_NAME`, `COLUMN_TYPE` FROM `COLUMNS`
WHERE `TABLE_NAME` LIKE 'es_%'
TABLE_NAME	COLUMN_NAME	DATA_TYPE	CHARACTER_MAXIMUM_LENGTH	COLLATION_NAME	COLUMN_TYPE	
 Current selection does not contain a unique column. Grid edit, checkbox, Edit, Copy and Delete features are not available.
 Showing rows 0 - 24 (25 total, Query took 0.0169 seconds.)
SELECT `TABLE_NAME`, `COLUMN_NAME`, `DATA_TYPE`, `CHARACTER_MAXIMUM_LENGTH`, `COLLATION_NAME`, `COLUMN_TYPE` FROM `information_schema`.`COLUMNS` WHERE `TABLE_NAME` LIKE 'es_%'

TABLE_NAME	COLUMN_NAME		DATA_TYPE	CHARACTER_MAXIMUM_LENGTH	COLLATION_NAME	COLUMN_TYPE
es_users_list	idUser	int	NULL	NULL	int(11)
es_users_list	strUserName	varchar	12	ascii_general_ci	varchar(12)
es_users_list	strFirstName	varchar	30	utf8_general_ci	varchar(30)
es_users_list	strLastName	varchar	30	utf8_general_ci	varchar(30)
es_users_list	strPassword	varchar	15	ascii_general_ci	varchar(15)
es_users_list	blnAdmin	tinyint	NULL	NULL	tinyint(1)
es_users_list	blnPermission	tinyint	NULL	NULL	tinyint(1)
es_users_list	blnCookie	tinyint	NULL	NULL	tinyint(1)
es_users_list	strEmail	varchar	45	ascii_general_ci	varchar(45)
es_users_list	strSMS	varchar	10	ascii_general_ci	varchar(10)

es_venues_list	idVenue	int	NULL	NULL	int(11)
es_venues_list	idUser	int	NULL	NULL	int(11)
es_venues_list	dteEntry	timestamp	NULL	NULL	timestamp
es_venues_list	blnArchive	tinyint	NULL	NULL	tinyint(1)
es_venues_list	strLocationName	varchar	50	ascii_general_ci	varchar(50)
es_venues_list	strAddress	mediumtext	16777215	ascii_general_ci	mediumtext
es_venues_list	strCity	varchar	50	ascii_general_ci	varchar(50)
es_venues_list	strProvince	varchar	2	ascii_general_ci	varchar(2)
es_venues_list	strPostalCode	varchar	10	ascii_general_ci	varchar(10)
es_venues_list	strGeolocationStyle	varchar	1	ascii_general_ci	varchar(1)
es_venues_list	strGeolocationPoints	varchar	250	ascii_general_ci	varchar(250)
es_venues_list	strPhoneNumber	varchar	20	ascii_general_ci	varchar(20)
es_venues_list	strContact	varchar	50	ascii_general_ci	varchar(50)
es_venues_list	strEmail	varchar	50	ascii_general_ci	varchar(50)
es_venues_list	strWebSite	varchar	50	ascii_general_ci	varchar(50)
es_venues_list	txtExtraNotes	longtext	4294967295	utf8_general_ci	longtext	

es_venues_details	idVenue	int	NULL	NULL	int(11)
es_venues_details	idDetail	int	NULL	NULL	int(11)
es_venues_details	idUser	int	NULL	NULL	int(11)
es_venues_details	dteEntry	timestamp	NULL	NULL	timestamp
es_venues_details	blnArchive	tinyint	NULL	NULL	tinyint(1)
es_venues_details	strLocationName	varchar	50	ascii_general_ci	varchar(50)	'Redundancy for widow/orphan matching
es_venues_details	strDetailType	varchar	10	ascii_general_ci	varchar(10)
es_venues_details	txtDetailNotes	longtext	4294967295	utf8_general_ci	longtext	'JSON text for all of the fields used

*/


// ToDo: Post venue information
/*
Array
(
    [locationName] => Camp Impessa
    [address] => 827559 Township Rd. 8,
    [city] => Ayr
    [province] => ON
    [postalCode] => N3L 3E2
    [geolocationStyle] => point
    [geolatitude] => 43.262218
    [geolongitude] => -80.500216
    [mapLock] => maplock
    [phoneNumber] => 519-432-2928
    [contact] => 
    [email] => swocamps@scouts.ca
    [webSite] => camp-impeesa.ca
    [extraNotes] => FOOL a Scouts Canada site near Ayr Ontario
Camp Location
#827559 Township Road 8, Drumbo, ON N0J 1G0
Township of Blandford-Blenheim, Oxford County

GPS Co-ordinates 43.262218, -80.500216
    [submit] => Submit
)

// ToDo: return geolocations in range
markers=color:0x1A3380%7Csize:tiny%7C43.5954395%2C-79.64688579999999&
markers=color:0x2952CC%7Csize:small%7C43.5934163%2C-79.6455198&
markers=color:0x3366FF%7Csize:mid%7C43.5904529%2C-79.645155&
markers=color:0x7094FF%7C43.5890452%2C-79.6441198&


SELECT `COLUMN_NAME` FROM `information_schema`.`COLUMNS`
WHERE `TABLE_NAME` = 'es_venues_list'
COLUMN_NAME
idVenue
idUser
dteEntry
blnArchive
strLocationName
strAddress
strCity
strProvince
strPostalCode
strGeolocationStyle
strGeolocationPoints
strPhoneNumber
strContact
strEmail
strWebSite
txtExtraNotes
*/

// Done: Retrieve nearby venue information

/*
// ToDo: Post new detail information

    [venueid] => 
    [ContactComboBox] => conversation
    [ContactPerson] => 
    [ContactEvent] => 
    [ContactDate] => 
    [ContactPlaintext] => 
    [ContactEncryptKey] => 
    [ContactEncrypttext] => 
    [AmenitiesComboBox] => summary
    [AmenitiesRoom] => 
    [AmenitiesLength] => 
    [AmenitiesWidth] => 
    [AmenitiesHeight] => 
    [AmenitiesElevation] => 
    [AmenitiesDescription] => 
    [AmenitiesInfobigEvent] => big event
    [AmenitiesInfosmallEvent] => small event
    [AmenitiesInfocamping] => camping
    [AmenitiesInfomeetings] => meetings
    [AmenitiesInfofightingIndoor] => fighting indoor
    [AmenitiesInfofightingOutdoor] => fighting outdoor
    [AmenitiesInfofencingIndoor] => fencing indoor
    [AmenitiesInfofencingOutdoor] => fencing outdoor
    [AmenitiesInfoarcheryIndoor] => archery indoor
    [AmenitiesInfoarcheryOutdoor] => archery outdoor
    [AmenitiesInfoclassesIndoor] => classes indoor
    [AmenitiesInfoclassesOutdoor] => classes outdoor
    [AmenitiesInfospecialty] => specialty
    [AmenitiesInfokitchenstove] => kitchenstove
    [AmenitiesInfokitchenoven] => kitchen oven
    [AmenitiesInfokitchenwarmer] => kitchen warmer
    [AmenitiesInfokitchenfridge] => kitchen fridge
    [AmenitiesInfokitchenfreezer] => kitchen freezer
    [AmenitiesInfokitchendishwasher] => kitchen dish washer
    [AmenitiesInfoelectricity] => electricity
    [AmenitiesInfowater] => water
    [AmenitiesInfobathroom] => bathroom
    [AmenitiesInfochangeroom] => change room
    [AmenitiesInfoshower] => shower
    [AmenitiesInfoparkingonsite] => on-site parking
    [AmenitiesInfoparkingpaylot] => pay lot parking
    [AmenitiesInfoparkingstreetfree] => street parking (free)
    [AmenitiesInfoparkingstreetmetered] => street parking (metered)
    [AmenitiesInfotransitbus] => transit bus
    [AmenitiesInfotransitsubway] => transit subway
    [AmenitiesInfotransittrain] => transit train
    [AmenitiesInfoloadingzone] => loading zone
    [AmenitiesInfoloadinginfront] => loading in front
    [AmenitiesInfoloadingoutback] => loading out back
    [AmenitiesInfowheelchairaccessible] => wheel-chair accessible
    [AmenitiesInfoaccessibleelevator] => elevator
    [AmenitiesInfoaccessibleramp] => ramp
    [AmenitiesInfoaccessiblenoStairs] => no stairs
)
TRUE
Array
(
    [idUser] => 1
    [strUserName] => cinaed
    [strFirstName] => Philip
    [strLastName] => Young
    [blnAdmin] => 1
    [blnPermission] => 0
)
*/

?>
