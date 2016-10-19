<?php
	include 'db-manager.php';
	$blnSignedIn=accessAuthenticate();
	$strLat = '43.5890452';
	$strLng = '-79.6441198';
	$strZoom = '14';
	if( isset($_GET['lat']) ) { 
		if( is_numeric($_GET['lat']) ) {
			$strLat = $_GET['lat'];
		}
	}
	if( isset($_GET['lng']) ) { 
		if( is_numeric($_GET['lng']) ) {
			$strLng = $_GET['lng'];
		}
	}
	if( isset($_GET['zoom']) ) { 
		if( is_numeric($_GET['zoom']) ) {
			$strZoom = $_GET['zoom'];
		}
	}
	$arrCloseVenues = accessOnMap( $strLat, $strLng, $strZoom );

echo '<?xml version="1.0" encoding="iso-8859-1"?>' . chr(13) . chr(10);

echo '<nearbyFiles>' . chr(13) . chr(10);
echo '<param>' . $strLat . '</param>' . chr(13) . chr(10);
echo '<param>' . $strLng . '</param>' . chr(13) . chr(10);
echo '<param>' . $strZoom . '</param>' . chr(13) . chr(10);
foreach( $arrCloseVenues as $arrVenue )
{
	echo '  <nearbyList>' . chr(13) . chr(10);
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
	echo '  </nearbyList>' . chr(13) . chr(10);
}
echo '</nearbyFiles>' . chr(13) . chr(10);
?>