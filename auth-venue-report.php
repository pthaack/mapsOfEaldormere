<?php
/*
================================================================
----------------------------------------------------------------
// auth-venue-report.php
Build a report based on parameters.  
If none are given, report is blank.
Keeping it simple.  No active frames.  Minimal scripts.
Mimic the auth-venue-search-list.php
 
Wanted:
	Table of Contents
	Page breaks
	Summary only
	OR
	Massive details
	Decoding encrypted data.
----------------------------------------------------------------
================================================================
*/
	define('ROOTPATH', 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']));
	
	include 'db-manager.php';
	$blnSignedIn=accessAuthenticate();
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><?php

// http://stackoverflow.com/questions/24908686/jquery-open-page-in-a-new-tab-while-passing-post-data
// http://stackoverflow.com/questions/13657362/open-a-new-popup-window-and-post-data-to-it
// http://stackoverflow.com/questions/3200357/post-data-to-a-new-popup-window-without-using-hidden-input-fields
// http://stackoverflow.com/questions/1740783/how-to-open-popup-and-populate-it-with-data-from-parent-window

/*
  two step pop-up.
  check for POST and SESSION data. 
  if POST not set, check for SESSION.
  if neither set, fail.
*/

	/*
	Start: From Stack Overflow -- jQuery open page in a new tab while passing POST data
	* /
    session_start();

    // DO NOT just copy from _POST to _SESSION,
    // as it could allow a malicious user to override security.
    // Use a disposable variable key, such as "data" here.
    if (array_key_exists('listdata', $_POST)) {
        if (array_key_exists('ts', $_POST)) {
            // TODO: verify ts(), but beware of time zones!
            $_SESSION['listdata'] = $_POST['listdata'];
            Header("Content-Type: application/json;charset=UTF-8");
            die(json_encode(array('status' => 'OK')));
        }
		$strDeathKnell = '<html><head><title>Log in failed</title><style type="text/css">h1 {font-family:Georgia; color:#000080}h2 {font-family:Georgia; color:#000080}body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }.wall {background-color:#ffffd8; background-size: 100px 50px; background-repeat: repeat-x; background-image:url("images/bkgrnd.gif"); }</style></head><body><h1 class="wall">Fields empty</h1>Nothing to report.<br>Do not refresh screen as fields are also refreshed.</body></html>';
		die( $strDeathKnell );
    }
    // This is safe (we move unsecurity-ward):
    $_POST = $_SESSION['listdata'];
    unset($_SESSION['listdata']); // keep things clean.
	/ *
	End: From Stack Overflow -- jQuery open page in a new tab while passing POST data
	*/

// http://stackoverflow.com/questions/24337317/encrypt-with-php-decrypt-with-javascript-cryptojs
// require( ROOTPATH . 'scripts/cryptojs/cryptojs-aes.php');
/**
* Decrypt data from a CryptoJS json encoding string
*
* @param mixed $passphrase
* @param mixed $jsonString
* @return mixed
*/
/*
function cryptoJsAesDecrypt($passphrase, $jsonString){
    $jsondata = json_decode($jsonString, true);
    $salt = hex2bin($jsondata["s"]);
    $ct = base64_decode($jsondata["ct"]);
    $iv  = hex2bin($jsondata["iv"]);
    $concatedPassphrase = $passphrase.$salt;
    $md5 = array();
    $md5[0] = md5($concatedPassphrase, true);
    $result = $md5[0];
    for ($i = 1; $i < 3; $i++) {
        $md5[$i] = md5($md5[$i - 1].$concatedPassphrase, true);
        $result .= $md5[$i];
    }
    $key = substr($result, 0, 32);
    $data = openssl_decrypt($ct, 'aes-256-cbc', $key, true, $iv);
    return json_decode($data, true);
}
*/



	$strLat = '';
	$strLng = '';
	$strZoom = '';
	$strDistLat = '';
	$strDistLon = '';
	$strVenueName = '';
	$strCity = '';
	$strPostal = '';
	$strContact = '';
	$strContactType = '';
	$strContactName = '';
	$arrIcon = array();
	$strAmenity = '';
	$strAmenityType = '';
	$strAmenityLength = '';
	$strAmenityWidth = '';
	$strAmenityHeight = '';
	$strPhoto = '';
	$arrFilters = array();

	if( isset($_POST['latitude']) ) { 
		if( is_numeric($_POST['latitude']) ) {
			$strLat = $_POST['latitude'];
		}
	}
	if( isset($_POST['longitude']) ) { 
		if( is_numeric($_POST['longitude']) ) {
			$strLng = $_POST['longitude'];
		}
	}
	if( isset($_POST['zoomlevel']) ) { 
		if( is_numeric($_POST['zoomlevel']) ) {
			$strZoom = $_POST['zoomlevel'];
		}
	}
	if( isset($_POST['latitudedistance']) ) { 
		if( is_numeric($_POST['latitudedistance']) ) {
			$strDistLat = $_POST['latitudedistance'];
		}
	}
	if( $strDistLat == '' && $strZoom != '' ) {
		$strDistLat = 360 / pow( 2, $strZoom ); }
	if( isset($_POST['longitudedistance']) ) { 
		if( is_numeric($_POST['longitudedistance']) ) {
			$strDistLng = $_POST['longitudedistance'];
		}
	}
	if( $strDistLng == '' && $strLat != '' && $strDistLat != '' ) {
		$strDistLng = $strDistLat / cos( M_PI * $strLat / 180 ); }
		
	if( isset($_POST['searchVenueName']) ) { 
		$strVenueName = $_POST['searchVenueName'];
		$arrFilters['strLocationName'] = $strVenueName; 
	}
	if( isset($_POST['searchHasContacts']) ) { 
		$arrFilters['strContact'] = true; 
	}
	if( isset($_POST['searchThisContact']) ) { 
		$strContactPerson = $_POST['searchThisContact'];
		$arrFilters['strContactPerson'] = $strContactPerson; 
	}
	if( isset($_POST['icon']) ) { 
		$arrIcon = split(';',$_POST['icon']);
		$arrFilters['strIcon'] = $arrIcon; 
	}
	if( isset($_POST['searchHasAmenities']) ) { 
		$arrFilters['strAmenities'] = true; 
	}
	if( isset($_POST['searchThisAmenity']) ) { 
		$strAmenity = $_POST['searchThisAmenity'];
		$arrFilters['strAmenity'] = $strAmenity; 
	}
	if( isset($_POST['searchHasPhotos']) ) { 
		$arrFilters['strPhotos'] = true; 
	}
	foreach( $_POST as $PostKey => $PostValue )
	{
		if( strpos( $PostKey, 'searchAmenity' ) !== false )
		{
			$arrFilters[ 'strAmenitiesInfo'. substr( $PostKey, strlen( 'searchAmenity' ) ) ] = $PostValue;
		}
	}
	$strJSONfilters = json_encode( $arrFilters );

echo '<!-- Parameter list:' . chr(13) . chr( 10 );
echo '-- $strLat:' . 	$strLat . chr(13) . chr( 10 );
echo '-- $strLng:' . 		$strLng . chr(13) . chr( 10 );
echo '-- $strZoom:' . 		$strZoom . chr(13) . chr( 10 );
echo '-- $strDistLat:' . 		$strDistLat . chr(13) . chr( 10 );
echo '-- $strDistLon:' . 		$strDistLon . chr(13) . chr( 10 );
echo '-- $strVenueName:' . 		$strVenueName . chr(13) . chr( 10 );
echo '-- $strCity:' . 		$strCity . chr(13) . chr( 10 );
echo '-- $strPostal:' . 		$strPostal . chr(13) . chr( 10 );
echo '-->' . chr(13) . chr( 10 );
// echo '<!-- POST list:' . chr(13) . chr( 10 ); print_r($_POST); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
// echo '<!-- FILTERS list:' . chr(13) . chr( 10 ); print_r($arrFilters); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );

	$arrCloseVenues = array();
	if( $strLat != ''
		&& $strLng != ''
		&& $strDistLat != ''
		&& $strDistLng != ''
		 )
	{
		$arrCloseVenues = accessByDistance( $strLat, $strLng, $strDistLat, $strDistLng, $strJSONfilters );
// echo '<!-- VENUE list:' . chr(13) . chr( 10 ); print_r($arrCloseVenues); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		$strVenueList = ';';
		foreach( $arrCloseVenues as $idKey => $arrVenue )
		{
			$arrGeolocationPoints = ( isJson( ) ? json_decode($arrVenue['strGeolocationPoints'], true) : array('icon' => 'archery' ) );
// echo '<!-- VENUE points '.$arrVenue['strGeolocationPoints'].':' . chr(13) . chr( 10 ); print_r($arrGeolocationPoints); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			$strIconType = $arrGeolocationPoints['icon'];
// echo '<!-- VENUE type '.$idKey.':' . $arrGeolocationPoints['icon'] .'-->' . chr(13) . chr( 10 );
			switch( $strIconType )
			{
			  case 'grocery':
			  case 'fuel':
			  case 'mall':
				unset( $arrCloseVenues[ $idKey ] );
				break;
			  default:
				$arrVenueDetails[ $arrVenue['idVenue'] ] = accessDetail( $arrVenue['idVenue'] );
				break;
			}
		}
//echo '<!-- VENUE details:' . chr(13) . chr( 10 ); print_r($arrVenueDetails); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
		
	}

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Venue Report</title>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>   
<!--  Use AES encryption to protect private conversations.  -->
<script src="scripts/cryptojs/components/core-min.js"></script>
<script src="scripts/cryptojs/components/enc-utf16-min.js"></script>
<script src="scripts/cryptojs/components/enc-base64-min.js"></script>
<script src="scripts/cryptojs/rollups/aes.js"></script>
<script>

function decryptText( txtSecretKey, txtCoded )
{
	var txtPlaintext = '';
	if( txtSecretKey != '' )
	{
		try {
			txtPlaintext = escapeLineBreak( escapeHtml( '' + CryptoJS.AES.decrypt(txtCoded, txtSecretKey).toString(CryptoJS.enc.Utf8) ) );
		}
		catch( e ) { 
			console.log( 'Wrong secret key:' + e ); 
			txtPlaintext = ''; //escapeLineBreak( escapeHtml( CryptoJS.AES.decrypt(txtCoded, txtSecretKey) ) );
		}
	}
	else
	{
		txtPlaintext = ''; // txtCoded;
	} 
	return( txtPlaintext );
}

function getCookieForSecretKey( strID )
{
	// http://www.w3schools.com/js/js_cookies.asp
    var name = strID + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length,c.length);
        }
    }
    return "";
}

function escapeHtml(text) {
  var map = {
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#039;'
  };

  try {
	return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }
  catch(e) { 
  	console.log( e.message ); 
	return ''; 
  }
}

function escapeLineBreak(text) {
  var map = {
    '\r\n': '<br />',
    '\r': '<br />',
    '\n': '<br />'
  };

  return text.replace(/(\r\n|[\r\n])/g, function(m) { return map[m]; });
}

</script>
<style type="text/css"><!--
div { vertical-align: top }
div#navButtons {position: fixed; left: 425px; top: 1em; height: 2em; overflow-y: auto; }
div#detailEntry {position: fixed; left: 425px; top: 3em; height: 90%; overflow-y: auto; }
label {width: 140px; display: inline-block;}
label.privacy {font-size:x-small; text-align:right}
img.photogallery {display:inline}
/* div table styles */
.rTable { display: table; } .rTableRow { display: table-row; } .rTableHeading { display: table-header-group; } .rTableBody { display: table-row-group; } .rTableFoot { display: table-footer-group; } .rTableCell, .rTableHead { display: table-cell; }
/* https://css-tricks.com/snippets/css/prevent-long-urls-from-breaking-out-of-container/ */
.dont-break-out {

  /* These are technically the same, but use both */
  overflow-wrap: break-word;
  word-wrap: break-word;

  -ms-word-break: break-all;
  /* This is the dangerous one in WebKit, as it breaks things wherever */
  word-break: break-all;
  /* Instead use this non-standard one: */
  word-break: break-word;

  /* Adds a hyphen where the word breaks, if supported (No Blink) */
  -ms-hyphens: auto;
  -moz-hyphens: auto;
  -webkit-hyphens: auto;
  hyphens: auto;

}
/* grabbed from VestYorvik website */
.wall, .wall TD, .wall TH  {background-color:#ffffd8; background-size: 100px 50px; background-repeat: repeat-x; background-image:url("images/bkgrnd.gif"); }
body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }
map {color:#993333}
h1 {font-family:Georgia; color:#000080}
h2 {font-family:Georgia; color:#000080}
p.footer {font-size:x-small; text-align:center}
-->
</style>

</head>
<body>
<?php
if( count( $arrCloseVenues ) == 0 )
{
	echo '<h1 class="wall">Venue Report</h1>';
	echo '<p>Nothing to report</p>';
}
else
{
	echo '<h1 class="wall">Venue Report</h1>' . chr(13) . chr( 10 );
	foreach( $arrCloseVenues as $arrVenue )
	{
		// http://stackoverflow.com/questions/12704613/php-str-replace-replace-spaces-with-underscores
		// http://php.net/manual/en/function.htmlspecialchars.php
		echo '<h3><a href="#anchor' . $arrVenue['idVenue'] . '">'. 
			htmlspecialchars( $arrVenue['strLocationName'] ) . '</a> &nbsp; '. 
			'</h3>' . chr(13) . chr( 10 );
	}
	echo '<p style="page-break-after:always;"></p>'; //page break
	foreach( $arrCloseVenues as $arrVenue )
	{
		// http://stackoverflow.com/questions/12704613/php-str-replace-replace-spaces-with-underscores
		// http://php.net/manual/en/function.htmlspecialchars.php
		echo '<h3><a name="anchor' . $arrVenue['idVenue'] . '">'. 
			htmlspecialchars( $arrVenue['strLocationName'] ) . '</a> &nbsp; '. 
			'</h3>' . chr(13) . chr( 10 );
		$strMapLG = '<img src="http://maps.google.com/maps/api/staticmap?center='. $arrVenue['strGeoLatitude'] .','. $arrVenue['strGeoLongitude'] .'&zoom=14&size=400x400'
		.'&markers=color:0x7094FF%7C'. $arrVenue['strGeoLatitude'] .'%2C'. $arrVenue['strGeoLongitude'] .'&sensor=false" alt="detail map" id="detailMap" width="400" height="400" border="0" >';
		$strMapTN = '<img src="http://maps.google.com/maps/api/staticmap?center='. $arrVenue['strGeoLatitude'] .','. $arrVenue['strGeoLongitude'] .'&zoom=7&size=100x100'
		.'&markers=color:0x7094FF%7C'. $arrVenue['strGeoLatitude'] .'%2C'. $arrVenue['strGeoLongitude'] .'&sensor=false" alt="thumbnail map" id="thumbnailMap" width="100" height="100" border="0" >';
			
         //   [strAddress] => 1468 Krick Rd
         //   [strCity] => Saint Anns
         //   [strProvince] => ON
         //   [strPostalCode] => L0R 1Y0
         //   [strGeoLatitude] => 43.034811
         //   [strGeoLongitude] => -79.5420498
         //   [strGeolocationStyle] => P
         //   [strGeolocationPoints] => {lat:43.034811,lng:-79.5420498,icon:park}
         //   [strPhoneNumber] => (905) 386-6683
         //   [strContact] => John and Morna Ahlstedt
         //   [strEmail] => mornaahlstedt@ymail.com
         //   [strWebSite] => https://finallyoaks.com/about/
         //   [txtExtraNotes] => FINALLY OAKS
		echo '<p>';
		echo (trim( $arrVenue['strAddress'] ) != '' ? htmlspecialchars( $arrVenue['strAddress'] ) . '<br />' : '')
			. (trim( $arrVenue['strCity'] ) != '' ? htmlspecialchars( $arrVenue['strCity'] ) . ' ' : '')
			. (trim( $arrVenue['strProvince'] ) != '' ? htmlspecialchars( $arrVenue['strProvince'] ) . '&nbsp; ' : '')
			. (trim( $arrVenue['strPostalCode'] ) != '' ? htmlspecialchars( $arrVenue['strPostalCode'] ) : '')
			. (trim( $arrVenue['strPhoneNumber'] ) != '' ? '<br />' . htmlspecialchars( $arrVenue['strPhoneNumber'] ) . '<br />' : '');
		echo (trim( $arrVenue['strContact'] ) != '' ? '<br />'. 'Contact: '. htmlspecialchars( $arrVenue['strContact'] ) . ' ' : '');
		echo (trim( $arrVenue['strEmail'] ) != '' ? '<br />'. 'Email: '. htmlspecialchars( $arrVenue['strEmail'] ) . ' ' : '');
		echo (trim( $arrVenue['strWebSite'] ) != '' ? '<br />'. 'WebSite: '. htmlspecialchars( $arrVenue['strWebSite'] ) . ' ' : '');
		echo (trim( $arrVenue['txtExtraNotes'] ) != '' ? '<br />Notes:<br />'. 
			str_replace( chr(10), '<br />', 	str_replace( chr(13), '<br />', 	str_replace( chr(13) . chr(10), '<br />', htmlspecialchars( $arrVenue['txtExtraNotes'] ) ) ) ) . ' ' 
			: '');
		echo '</p>';

		$strContactHTML = ''; 
		$strAmenityHTML = ''; 
		$strPhotoHTML = ''; 
		foreach( $arrVenueDetails[ $arrVenue['idVenue'] ] as $arrDetail )
		{
			$arrDetailNotes = json_decode($arrDetail['txtDetailNotes'], true);
//	 echo '<!-- Detail Notes:' . chr(13) . chr( 10 ); print_r($arrDetailNotes); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
			switch( $arrDetail['strDetailType'] )
			{
			  case 'contact':
				$strContactHTML .= '<p class="dont-break-out">';
				switch( $arrDetailNotes['strContactType'] )
				{
				  case 'conversation':
					$strContactHTML .= '<em>Phone conversation</em>';
					break;
				  case 'event':
					$strContactHTML .= '<em>Event</em>';
					break;
				  case 'meeting':
					$strContactHTML .= '<em>On-site meeting</em>';
					break;
				  case 'financial':
					$strContactHTML .= '<em>Financial transaction</em>';
					break;
				}
				$strContactHTML .= '<br />';
				$strContactHTML .= ( trim($arrDetailNotes['strContactPerson'])!='' ? '&nbsp; Contact:&nbsp; '. htmlspecialchars($arrDetailNotes['strContactPerson']) . '<br />' : '' );
				$strContactHTML .= ( trim($arrDetailNotes['strContactEvent'])!='' ? '&nbsp; Event:&nbsp; '. htmlspecialchars($arrDetailNotes['strContactEvent']) . '<br />' : '' );
				$strContactHTML .= ( trim($arrDetailNotes['strContactDate'])!='' ? '&nbsp; Date:&nbsp; '. htmlspecialchars($arrDetailNotes['strContactDate']) . '<br />' : '' );
				
				$strSecretCookie = 'key'. $arrDetail['idDetail'];
				$strMsgID = 'msg'. $arrDetail['idDetail'];
				if( isset( $_COOKIE[ $strSecretCookie ] ) )
				{
					$strContactHTML .= ( trim($arrDetailNotes['strContactText'])!='' ? '&nbsp; &nbsp; <span id="'. $strMsgID . '" class="dont-break-out">'. str_replace( chr(10), '<br />', str_replace( chr(13), '<br />', 	str_replace( chr(13) . chr(10), '<br />', htmlspecialchars($arrDetailNotes['strContactText']) ) ) ) . '</span><br />' : '' );
					$strContactHTML .= '<script type="text/javascript">jQuery(\'#'. $strMsgID .'\').html(decryptText(getCookieForSecretKey(\''. $strSecretCookie .'\'),jQuery(\'#'. $strMsgID .'\').text()));</script>';
				}
				else
				{
					$strContactHTML .= ( trim($arrDetailNotes['strContactText'])!='' ? '&nbsp; &nbsp; <span id="msg'. $arrDetail['idDetail'] . '" class="dont-break-out">'. str_replace( chr(10), '<br />', str_replace( chr(13), '<br />', 	str_replace( chr(13) . chr(10), '<br />', htmlspecialchars($arrDetailNotes['strContactText']) ) ) ) . '</span><br />' : '' );
				}
				$strContactHTML .= '</p>' . chr(13) . chr( 10 );
			  	break;
			  case 'amenity':
				$strAmenityHTML .= '<p class="dont-break-out">';
				switch( $arrDetailNotes['strAmenitiesType'] )
				{
				  case 'summary':
					$strAmenityHTML .= '<em>Amenity summary</em>';
					break;
				  case 'room':
					$strAmenityHTML .= '<em>Room</em>';
					break;
				  case 'gym':
					$strAmenityHTML .= '<em>Gymnasium</em>';
					break;
				  case 'field':
					$strAmenityHTML .= '<em>Field</em>';
					break;
				  case 'dais':
					$strAmenityHTML .= '<em>Dais or Stage</em>';
					break;
				  case 'kitchen':
					$strAmenityHTML .= '<em>Kitchen or Bar</em>';
					break;
				  case 'bathroom':
					$strAmenityHTML .= '<em>Bathroom or Changeroom</em>';
					break;
				  case 'elevator':
					$strAmenityHTML .= '<em>Elevator</em>';
					break;
				  case 'parking':
					$strAmenityHTML .= '<em>Parking and access</em>';
					break;
				}
				$strAmenityHTML .= '<br />';
				$strAmenityHTML .= ( $arrDetailNotes['strAmenitiesRoom']!='' ? '&nbsp; Room:&nbsp; '. htmlspecialchars( $arrDetailNotes['strAmenitiesRoom']) . '<br />' : '' );
				$strAmenityHTML .= ( $arrDetailNotes['strAmenitiesLength']!='' ? '&nbsp; Length:&nbsp; '. htmlspecialchars($arrDetailNotes['strAmenitiesLength']) . '<br />' : '' );
				$strAmenityHTML .= ( $arrDetailNotes['strAmenitiesWidth']!='' ? '&nbsp; Width:&nbsp; '. htmlspecialchars($arrDetailNotes['strAmenitiesWidth']) . '<br />' : '' );
				$strAmenityHTML .= ( $arrDetailNotes['strAmenitiesHeight']!='' ? '&nbsp; Height:&nbsp; '. htmlspecialchars($arrDetailNotes['strAmenitiesHeight']) . '<br />' : '' );
				$strAmenityHTML .= ( $arrDetailNotes['strAmenitiesElevation']!='' ? '&nbsp; Elevation:&nbsp; '. htmlspecialchars($arrDetailNotes['strAmenitiesElevation']) . '<br />' : '' );
				$strAmenityHTML .= ( $arrDetailNotes['strAmenitiesDescription']!='' ? '&nbsp; &nbsp; '. str_replace( chr(10), '<br />', str_replace( chr(13), '<br />', 	str_replace( chr(13) . chr(10), '<br />', htmlspecialchars($arrDetailNotes['strAmenitiesDescription']) ) ) ) . '<br />' : '' );
				$strAmenityHTML .= '<br />';
				$strAmenityHTML .= '<em>&nbsp; &nbsp; Amenities</em>';
				$strAmenityHTML .= '<br />';
				foreach( $arrDetailNotes as $strAmenityKey => $strAmenityInfo )
				{
					if( strpos( $strAmenityKey, 'strAmenitiesInfo' ) === 0 ){
						$strAmenityHTML .= ( $strAmenityInfo != '' ? '&nbsp; &nbsp; '. str_replace( chr(10), '<br />', str_replace( chr(13), '<br />', 	str_replace( chr(13) . chr(10), '<br />', htmlspecialchars( $strAmenityInfo ) ) ) ) . '<br />' : '' );
					}
				}
				$strAmenityHTML .= '</p>' . chr(13) . chr( 10 );
			  	break;
			  case 'photo':
				$strPhotoHTML .= '<p class="dont-break-out">';
				$strPhotoSrc = 'data/'. urlencode( $arrDetailNotes['strPhotoServerFile'] );
				$strPhotoDesc = htmlspecialchars( $arrDetailNotes['strPhotoDescription'] );
				$strPhotoHTML .= '<img src="'. $strPhotoSrc .'" /><br />'. $strPhotoDesc;
				$strPhotoHTML .= '</p>' . chr(13) . chr( 10 );
			  	break;
			  default:
			  	break;
			}
		}

		echo $strContactHTML; 
		echo $strAmenityHTML; 
		echo $strPhotoHTML; 
		echo $strMapLG . $strMapTN ;
		echo '<p style="page-break-after:always;"></p>'; //page break
	}
}
?></body>
</html>
<?php
?>