<?php
	include 'db-manager.php';
	$blnSignedIn=accessAuthenticate();
	/* 
	This is a very complex search.  Part of the calculation is in Javascript/jQuery.  Part by Google's server.  Part will be done in PHP.
	
	Distance or zoom level is calculated in Javascript and sent here, but not both.  Zoom is ignored if both received.
	
	All other filters will be performed in PHP.
	What is the name of the venue?
	Is there a contact?
	What kind of contact?
	What is the name of the contact?
	Are there amenities?
	Which kind of amenities?
	Which amenities?
	Only venues with photos?
	*/
	$strLat = '43.5890452';
	$strLng = '-79.6441198';
	$strZoom = '14';
	$strDistLat = '';
	$strDistLon = '';
	$strVenueName = '';
	$strCity = '';
	$strPostal = '';
	$strContact = '';
	$strContactType = '';
	$strContactName = '';
	$strAmenity = '';
	$strAmenityType = '';
	$strAmenityLength = '';
	$strAmenityWidth = '';
	$strAmenityHeight = '';
	$strPhoto = '';
	$arrFilters = array();
	if( isset($_GET['lat']) ) { 
		if( is_numeric($_GET['lat']) ) {
			$strLat = $_GET['lat'];
		}
	}
	if( isset($_POST['latitude']) ) { 
		if( is_numeric($_POST['latitude']) ) {
			$strLat = $_POST['latitude'];
		}
	}
	if( isset($_GET['lng']) ) { 
		if( is_numeric($_GET['lng']) ) {
			$strLng = $_GET['lng'];
		}
	}
	if( isset($_POST['longitude']) ) { 
		if( is_numeric($_POST['longitude']) ) {
			$strLng = $_POST['longitude'];
		}
	}
	if( isset($_GET['zoom']) ) { 
		if( is_numeric($_GET['zoom']) ) {
			$strZoom = $_GET['zoom'];
		}
	}
	if( isset($_POST['zoomlevel']) ) { 
		if( is_numeric($_POST['zoomlevel']) ) {
			$strZoom = $_POST['zoomlevel'];
		}
	}
	if( isset($_GET['dlat']) ) { 
		if( is_numeric($_GET['dlat']) ) {
			$strDistLat = $_GET['dlat'];
		}
	}
	if( isset($_POST['latitudedistance']) ) { 
		if( is_numeric($_POST['latitudedistance']) ) {
			$strDistLat = $_POST['latitudedistance'];
		}
	}
	if( $strDistLat == '' ) {
		$strDistLat = 360 / pow( 2, $strZoom ); }
	if( isset($_GET['dlng']) ) { 
		if( is_numeric($_GET['dlng']) ) {
			$strDistLng = $_GET['dlng'];
		}
	}
	if( isset($_POST['longitudedistance']) ) { 
		if( is_numeric($_POST['longitudedistance']) ) {
			$strDistLng = $_POST['longitudedistance'];
		}
	}
	if( $strDistLng == '' ) {
		$strDistLng = $strDistLat / cos( M_PI * $strLat / 180 ); }
		
	if( isset($_GET['vn']) ) { 
		$strVenueName = $_GET['vn'];
		$arrFilters['strLocationName'] = $strVenueName; 
	}
	if( isset($_POST['searchVenueName']) ) { 
		$strVenueName = $_POST['searchVenueName'];
		$arrFilters['strLocationName'] = $strVenueName; 
	}
	if( isset($_GET['vc']) ) { 
		$strCity = $_GET['vc'];
		$arrFilters['strCity'] = $strCity;
	}
	if( isset($_GET['vpc']) ) { 
		$strPostal = $_GET['vpc'];
		$arrFilters['strPostalCode'] = $strPostal;
	}
	if( isset($_POST['searchHasContacts']) ) { 
		$arrFilters['strContact'] = true; 
	}
	if( isset($_GET['ctc']) ) { 
		$strContactPerson = $_GET['ctc'];
		$arrFilters['strContactPerson'] = $strContactPerson; 
	}
	if( isset($_POST['searchThisContact']) ) { 
		$strContactPerson = $_POST['searchThisContact'];
		$arrFilters['strContactPerson'] = $strContactPerson; 
	}
	if( isset($_GET['ctct']) ) { 
		$strContactType = $_GET['ctct'];
	}
	if( isset($_GET['ctcn']) ) { 
		$strContactName = $_GET['ctcn'];
	}
	if( isset($_POST['searchHasAmenities']) ) { 
		$arrFilters['strAmenities'] = true; 
	}
	if( isset($_GET['amty']) ) { 
		$strAmenity = $_GET['amty'];
		$arrFilters['strAmenity'] = $strAmenity; 
	}
	if( isset($_POST['searchThisAmenity']) ) { 
		$strAmenity = $_POST['searchThisAmenity'];
		$arrFilters['strAmenity'] = $strAmenity; 
	}
	if( isset($_GET['amtt']) ) { 
		$strAmenityType = $_GET['amtt'];
	}
	if( isset($_GET['amtl']) ) { 
		$strAmenityLength = $_GET['amtl'];
	}
	if( isset($_GET['amtw']) ) { 
		$strAmenityWidth = $_GET['amtw'];
	}
	if( isset($_GET['amth']) ) { 
		$strAmenityHeight = $_GET['amth'];
	}
	if( isset($_GET['photo']) ) { 
		$strPhoto = $_GET['photo'];
		$arrFilters['strPhotos'] = true; 
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
	
	$arrCloseVenues = accessByDistance( $strLat, $strLng, $strDistLat, $strDistLng, $strJSONfilters );

echo '<?xml version="1.0" encoding="iso-8859-1"?>' . chr(13) . chr(10);

echo '<searchFiles>' . chr(13) . chr(10);
echo '<param>' . $strLat . '</param>' . chr(13) . chr(10);
echo '<param>' . $strLng . '</param>' . chr(13) . chr(10);
echo '<param>' . $strZoom . '</param>' . chr(13) . chr(10);
echo '<param>' . $strDistLat . '</param>' . chr(13) . chr(10);
echo '<param>' . $strDistLng . '</param>' . chr(13) . chr(10);
echo '<param>' . $strJSONfilters . '</param>' . chr(13) . chr(10);
echo '<posts>' . chr(13) . chr(10);
foreach( $_POST as $PostKey => $PostValue )
{
	$strSafeItem = str_replace( '<', '&lt;', 
		str_replace( '>', '&gt;', 
			str_replace( '"', '&quot;', 
				str_replace( "'", '&apos;', 
					str_replace( '&', '&amp;', $PostValue ) ) ) ) );
	echo '  <'.$PostKey.'>'. $strSafeItem .'</'.$PostKey.'>' . chr(13) . chr(10);
}
echo '</posts>' . chr(13) . chr(10);
foreach( $arrCloseVenues as $arrVenue )
{
	echo '  <searchList>' . chr(13) . chr(10);
	foreach( $arrVenue as $key => $item )
	{
		// http://www.w3schools.com/xml/xml_syntax.asp
		$strSafeItem = str_replace( '<', '&lt;', 
			str_replace( '>', '&gt;', 
				str_replace( '"', '&quot;', 
					str_replace( "'", '&apos;', 
						str_replace( '&', '&amp;', $item ) ) ) ) );
		echo '    <'.$key.'>'. $strSafeItem .'</'.$key.'>' . chr(13) . chr(10);
	}
	echo '  </searchList>' . chr(13) . chr(10);
}
echo '</searchFiles>' . chr(13) . chr(10);
?>