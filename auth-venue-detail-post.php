<?php
/*
================================================================
----------------------------------------------------------------
// auth-venue-detail-post.php
AJAX version of posting venue information to the database.
If no new information is given, returns the data for the venue.
----------------------------------------------------------------
================================================================
*/
	include 'db-manager.php';
	$blnSignedIn=accessAuthenticate();

//  Todo: Initiallze defaults
$blnResult = 0; 	// Success - error of database save - INSERT … http://www.w3schools.com/php/php_mysql_insert.asp
$arrVenue = array("idUser" => "", 
	"idVenue" => "-1", 
	"dteEntry" => "", 
	"blnArchive" => "",
	"strLocationName" => "New Entry", 
	"strAddress" => "",
	"strCity" => "",
	"strProvince" => "",
	"strPostalCode" => "",
	"strGeoLatitude" => "43.5934163", 
	"strGeoLongitude" => "-79.6455198", 
	"strGeolocationStyle" => "",
	"strGeolocationPoints" => "",
	"strPhoneNumber" => "",
	"strContact" => "",
	"strEmail" => "",
	"strWebSite" => "",
	"txtExtraNotes" => ""
	);
if( isset( $_POST['venueid'] ) ) 
{
	$arrVenue = accessVenue($_POST['venueid'])[1]; 
}
elseif( isset( $_GET['id'] ) ) 
{
	if( $_GET['id'] >= 1 )
	{
		$arrVenue = accessVenue($_GET['id'])[1]; 
	}
}
$intThisVenue = $arrVenue['idVenue'];
$arrDetails = array();
if( isset($_POST['detailtype']) )
{
  switch( $_POST['detailtype'] )
  {
  case 'deletevenue':
	//  Done: Post details
	if( validVenueDetailPost( 'deletevenue') )
	{
	  //echo '<!-- Delete Venue -->' . chr(13) . chr(10);
	  if( isset( $_POST['venueid'] ) ) { $intVenue = trim( $_POST['venueid'] ); }
	  if( $objConnection = dbAccessOpen() )
	  {
		if( $intVenue != -1 )
		{
			dbVenuePostDelete( $objConnection, $intVenue );
	  //echo '<!-- Delete Venue: ' . $intVenue . ' -->' . chr(13) . chr(10);
		}
		dbAccessClose( $objConnection );
		$arrVenue = accessVenue($intVenue)[1];
	  }
	}
	break;
  case 'venue':
	if( validVenueDetailPost( 'venue') )
	{
	  //echo '<!-- Venue -->' . chr(13) . chr(10);
	  if( isset( $_POST['venueid'] ) ) { $intVenue = trim( $_POST['venueid'] ); }
	  if( $objConnection = dbAccessOpen() )
	  {
		if( $intVenue == -1 )
		{
			dbVenuePostNew( $objConnection );
			$intVenue = $objConnection->insert_id; 
			//echo '<!-- New Venue: ' . $intVenue . ' -->' . chr(13) . chr(10);
			$intThisVenue = $intVenue;
		}
		else
		{
			dbVenuePostUpdate( $objConnection, $intVenue );
		}
		dbAccessClose( $objConnection );
		$arrVenue = accessVenue($intVenue)[1];
	  }
	}
	break;
  case 'deletecontact':
	if( validVenueDetailPost( 'deletecontact') )
	{
	  //echo '<!-- Delete Contact -->' . chr(13) . chr(10);
	  if( isset( $_POST['venueid'] ) ) { $intVenue = trim( $_POST['venueid'] ); }
	  if( isset( $_POST['detailid'] ) ) { $intDetail = trim( $_POST['detailid'] ); }
	  //echo '<!-- Delete Detail: ' . $intDetail . ' -->' . chr(13) . chr(10);
	  if( $objConnection = dbAccessOpen() )
	  {
		if( $intVenue != -1 )
		{
			dbVenueDetailPostDelete( $objConnection, $intDetail );
		}
		dbAccessClose( $objConnection );
		$arrVenue = accessVenue($intVenue)[1];
	  }
	}
	break;
  case 'contact':
	if( validVenueDetailPost( 'contact') )
	{
	  //echo '<!-- Contact -->' . chr(13) . chr(10);
	  if( $objConnection = dbAccessOpen() )
	  {
		switch( uploadContact( $objConnection ) )
		{
		  case 'trace1':
			$arrDetails[] =
			  array("idUser" => "1", "venueid" => "25", "idDetail" => "189", "dteEntry" => "2016-07-30 20:45:17", "strLocationName" => "Friends of the Aviary", "strDetailType" => "contact", "arrContactNotes" => 
				  array("strContactType" => "conversation",
					"strContactPerson" => "Dave",
					"strContactEvent" => "",
					"strContactDate" => "2016-07-30 20:48:55",
					"strContactText" => "This is the default test data.  ".chr(13).chr(10).
						"The real stuff is coming.".chr(13).chr(10).			
						"<!-- // It's an example of resistance to SQL and HTML \"trace1\". -->")
			  );
			break;
		  case 'trace2':
			$arrDetails[] =
			  array("idUser" => "1", "venueid" => "25", "idDetail" => "189", "dteEntry" => "2016-07-30 20:45:17", "strLocationName" => "Friends of the Aviary", "strDetailType" => "contact", "arrContactNotes" => 
				  array("strContactType" => "conversation",
					"strContactPerson" => "Dave",
					"strContactEvent" => "",
					"strContactDate" => "2016-07-30 20:48:55",
					"strContactText" => "The real stuff is coming.".chr(13).chr(10).
						"<!-- // It's an example of resistance to SQL and HTML \"trace2\". -->".chr(13).chr(10).			
						"This is the default test data.  ")
			  );
			break;
			
		}
		dbAccessClose( $objConnection );
	  }
	}
	break;
	
  case 'deleteamenity':
	if( validVenueDetailPost( 'deleteamenity') )
	{
	  //echo '<!-- Delete Amenity -->' . chr(13) . chr(10);
	  if( isset( $_POST['venueid'] ) ) { $intVenue = trim( $_POST['venueid'] ); }
	  if( isset( $_POST['detailid'] ) ) { $intDetail = trim( $_POST['detailid'] ); }
	  if( $objConnection = dbAccessOpen() )
	  {
		if( $intVenue != -1 )
		{
			dbVenueDetailPostDelete( $objConnection, $intDetail );
		}
		dbAccessClose( $objConnection );
		$arrVenue = accessVenue($intVenue)[1];
	  }
	}
	break;
  case 'amenity':
	if( validVenueDetailPost( 'amenity') )
	{
	  //echo '<!-- Amenity -->' . chr(13) . chr(10);
	  if( $objConnection = dbAccessOpen() )
	  {
		switch( uploadAmenity( $objConnection ) )
		{
		  case 'trace1':
			$arrDetails[] =
			  array("idUser" => "1", "venueid" => "25", "idDetail" => "190", "dteEntry" => "2016-07-30 20:45:17", "strLocationName" => "Friends of the Aviary", "strDetailType" => "amenity", "arrAmenitiesNotes" => 
				  array("strAmenitiesType" => "summary",
					"strAmenitiesRoom" => "Baseball diamond",
					"strAmenitiesLength" => "50m",
					"strAmenitiesWidth" => "100m",
					"strAmenitiesHeight" => "",
					"strAmenitiesElevation" => "",
					"strAmenitiesDescription" => "The trace1.",
					"strAmenitiesInfobigEvent" => "big event",
					"strAmenitiesInfosmallEvent" => "small event",
					"strAmenitiesInfocamping" => "camping",
					"strAmenitiesInfomeetings" => "meetings",
					"strAmenitiesInfofightingIndoor" => "fighting indoor",
					"strAmenitiesInfofightingOutdoor" => "fighting outdoor",
					"strAmenitiesInfofencingIndoor" => "fencing indoor",
					"strAmenitiesInfofencingOutdoor" => "fencing outdoor",
					"strAmenitiesInfoarcheryIndoor" => "archery indoor",
					"strAmenitiesInfoarcheryOutdoor" => "archery outdoor",
					"strAmenitiesInfoclassesIndoor" => "classes indoor",
					"strAmenitiesInfoclassesOutdoor" => "classes outdoor",
					"strAmenitiesInfospecialty" => "specialty",
					"strAmenitiesInfokitchenstove" => "kitchenstove",
					"strAmenitiesInfokitchenoven" => "kitchen oven",
					"strAmenitiesInfokitchenwarmer" => "kitchen warmer",
					"strAmenitiesInfokitchenfridge" => "kitchen fridge",
					"strAmenitiesInfokitchenfreezer" => "kitchen freezer",
					"strAmenitiesInfokitchendishwasher" => "kitchen dish washer",
					"strAmenitiesInfoelectricity" => "electricity",
					"strAmenitiesInfowater" => "water",
					"strAmenitiesInfobathroom" => "bathroom",
					"strAmenitiesInfochangeroom" => "change room",
					"strAmenitiesInfoshower" => "shower",
					"strAmenitiesInfoparkingonsite" => "on-site parking",
					"strAmenitiesInfoparkingpaylot" => "pay lot parking",
					"strAmenitiesInfoparkingstreetfree" => "street parking (free)",
					"strAmenitiesInfoparkingstreetmetered" => "street parking (metered)",
					"strAmenitiesInfotransitbus" => "transit bus",
					"strAmenitiesInfotransitsubway" => "transit subway",
					"strAmenitiesInfotransittrain" => "transit train",
					"strAmenitiesInfoloadingzone" => "loading zone",
					"strAmenitiesInfoloadinginfront" => "loading in front",
					"strAmenitiesInfoloadingoutback" => "loading out back",
					"strAmenitiesInfowheelchairaccessible" => "wheel-chair accessible",
					"strAmenitiesInfoaccessibleelevator" => "elevator",
					"strAmenitiesInfoaccessibleramp" => "ramp",
					"strAmenitiesInfoaccessiblenoStairs" => "no stairs")
			);
			break;
		  default:
			break;
	
		}
		dbAccessClose( $objConnection );
	  }
	}
	break;
  case 'deletephoto':
	if( validVenueDetailPost( 'deletephoto') )
	{
	  //echo '<!-- Delete Photo -->' . chr(13) . chr(10);
	  if( isset( $_POST['venueid'] ) ) { $intVenue = trim( $_POST['venueid'] ); }
	  if( isset( $_POST['detailid'] ) ) { $intDetail = trim( $_POST['detailid'] ); }
	  if( $objConnection = dbAccessOpen() )
	  {
		if( $intVenue != -1 )
		{
			dbVenueDetailPostDelete( $objConnection, $intDetail );
		}
		dbAccessClose( $objConnection );
		$arrVenue = accessVenue($intVenue)[1];
	  }
	}
	break;
  case 'photo':
	if( validVenueDetailPost( 'photo') )
	{
	  //echo '<!-- Photo -->' . chr(13) . chr(10);
	  if( $objConnection = dbAccessOpen() )
	  {
		switch( uploadPhoto( $objConnection ) )
		{
		  case 'trace1':
			$arrDetails[] =
			array("idUser" => "1", "venueid" => "25", "idDetail" => "194", "dteEntry" => "2016-08-01 18:22:17", "strLocationName" => "Friends of the Aviary", "strDetailType" => "photo", "arrPhotoNotes" => 
			  array("strPhotoType" => "JPG",
				"strPhotoSourceFile" => "badDecisions.jpeg",
				"strPhotoServerFile" => "loadingMoonPhase400.gif",
				"strPhotoWidth" => "400",
				"strPhotoHeight" => "400",
				"strPhotoDescription" => "<!-- // photo upload failed. trace1 -->")
			);
			break;
		  case 'trace1b':
			$arrDetails[] =
			array("idUser" => "1", "venueid" => "25", "idDetail" => "194", "dteEntry" => "2016-08-01 18:22:17", "strLocationName" => "Friends of the Aviary", "strDetailType" => "photo", "arrPhotoNotes" => 
			  array("strPhotoType" => "JPG",
				"strPhotoSourceFile" => "badDecisions.jpeg",
				"strPhotoServerFile" => "loading8pt400.gif",
				"strPhotoWidth" => "400",
				"strPhotoHeight" => "400",
				"strPhotoDescription" => "<!-- // photo upload failed. trace1b -->")
			);
			break;
		  case 'trace2':
			$arrDetails[] =
			array("idUser" => "1", "venueid" => "25", "idDetail" => "194", "dteEntry" => "2016-08-01 18:22:17", "strLocationName" => "Friends of the Aviary", "strDetailType" => "photo", "arrPhotoNotes" => 
			  array("strPhotoType" => "JPG",
				"strPhotoSourceFile" => "badDecisions.jpeg",
				"strPhotoServerFile" => "loading12pt400.gif",
				"strPhotoWidth" => "400",
				"strPhotoHeight" => "400",
				"strPhotoDescription" => "<!-- // photo upload failed. trace2 -->")
			);
			break;
		  case 'trace3':
			$arrDetails[] =
			array("idUser" => "1", "venueid" => "25", "idDetail" => "194", "dteEntry" => "2016-08-01 18:22:17", "strLocationName" => "Friends of the Aviary", "strDetailType" => "photo", "arrPhotoNotes" => 
			  array("strPhotoType" => "JPG",
				"strPhotoSourceFile" => "badDecisions.jpeg",
				"strPhotoServerFile" => "loadingUSB400.gif",
				"strPhotoWidth" => "400",
				"strPhotoHeight" => "400",
				"strPhotoDescription" => "<!-- // photo upload failed. trace3 -->")
			);
			break;
		  case 'trace4':
			$arrDetails[] =
			array("idUser" => "1", "venueid" => "25", "idDetail" => "194", "dteEntry" => "2016-08-01 18:22:17", "strLocationName" => "Friends of the Aviary", "strDetailType" => "photo", "arrPhotoNotes" => 
			  array("strPhotoType" => "JPG",
				"strPhotoSourceFile" => "badDecisions.jpeg",
				"strPhotoServerFile" => "loadingRoundabout400.gif",
				"strPhotoWidth" => "400",
				"strPhotoHeight" => "400",
				"strPhotoDescription" => "<!-- // photo upload failed. trace4 -->")
			);
			break;
		  default:
			break;
		}
		dbAccessClose( $objConnection );
	  }
	}
	break;
  }
}
//  Todo: Retrieve 
$arrVenueDetails = array();
if( isset( $_POST['venueid'] ) )
{
	$arrVenueDetails = accessDetail( $_POST['venueid'] );
}
elseif( isset( $_GET['id'] ) )
{
	$arrVenueDetails = accessDetail( $_GET['id'] );
}
foreach( $arrVenueDetails as $arrDetail )
{
//	SELECT `idVenue`, `idDetail`, `idUser`, `dteEntry`, `strLocationName`, `strDetailType`, `txtDetailNotes` FROM `es_venues_details`
	$intUser = $arrDetail['idUser'];
	$intVenue = $arrDetail['idVenue'];
	$intDetail = $arrDetail['idDetail'];
	$strLocationName = $arrDetail['strLocationName'];
	$strEntry = $arrDetail['dteEntry'];
	$strDetailType = $arrDetail['strDetailType'];
	$txtDetailNotes = $arrDetail['txtDetailNotes'];
	$blnEditable = $arrDetail['blnEditable']; 

	// Fastest way to check if a string is JSON in PHP
	//if( !isJson( $txtDetailNotes ) ) { echo '<!-- '. $txtDetailNotes .' -->' . chr(13) . chr(10); }
    if( isJson( $txtDetailNotes ) && strpos( '.contact.amenity.photo.', '.'. $strDetailType .'.' ) !== false )
	{
/*
*/
		$arrDetailNotes = json_decode($txtDetailNotes, true);
//echo '<!-- ' . $strDetailType ; print_r( $arrDetailNotes ); echo ' -->' . chr(13) . chr(10);
		$arrDetails[] = array( "idUser" => $intUser, 
			"venueid" => $intVenue, 
			"idDetail" => $intDetail, 
			"dteEntry" => $strEntry, 
			"strLocationName" => $strLocationName, 
			"blnEditable" => $blnEditable,
			"strDetailType" => $strDetailType, 
			( $strDetailType == 'contact'?"arrContactNotes":
				( $strDetailType == 'amenity'?"arrAmenitiesNotes":
				( $strDetailType == 'photo'?"arrPhotoNotes":"txtDetailNotes"))) => $arrDetailNotes );
	}
}
// echo '<!-- ' . $strDetailType ; print_r( $arrDetails ); echo ' -->' . chr(13) . chr(10);
/*

Info: http://www.w3schools.com/jquery/ajax_post.asp

Example of JSON data to save:
	`es_venues_details`.`2`
	`es_venues_details`.`idDetail`
	`es_venues_details`.`idUser`
x	`es_venues_details`.`dteEntry`
x	`es_venues_details`.`blnArchive`
	`es_venues_details`.`strLocationName`
	`es_venues_details`.`strDetailType`
	`es_venues_details`.`txtDetailNotes`

{"idUser" : "1", "venueid" : "25", "strLocationName" : "Friends of the Aviary", "strDetailType" : "contact", "arrContactNotes" : 
	{"strContactType" : "conversation",
	"strContactPerson" : "",
	"strContactEvent" : "",
	"strContactDate" : "",
	"strContactText" : ""} 
} 
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
{"idUser" : "1", "venueid" : "25", "strLocationName" : "Friends of the Aviary", "strDetailType" : "photo", "arrPhotoNotes" : 
	{"strPhotoType" : "JPG",
	"strPhotoSourceFile" : "",
	"strPhotoServerFile" : "",
	"strPhotoDescription" : ""}
}


INSERT INTO `es_venues_details`
( `idVenue`, `idDetail`, `idUser`, `strLocationName`, `strDetailType`, `txtDetailNotes`)
VALUES

SELECT `idVenue`, `idDetail`, `strDetailType`, `txtDetailNotes` FROM `es_venues_details` WHERE `blnArchive` IS NULL AND `idVenue` IN ( 2,17 )

http://stackoverflow.com/questions/6041741/fastest-way-to-check-if-a-string-is-json-in-php


*/


echo '<?xml version="1.0" encoding="UTF-8"?>' . chr(13) . chr(10);

//  ToDo: Return the details of this venue
echo '<venueDetails>' . chr(13) . chr(10);
echo ' <posted>' . chr(13) . chr(10);
foreach( $_POST as $key => $item )
{
	// http://www.w3schools.com/xml/xml_syntax.asp
	$strSafeItem = str_replace( '<', '&lt;', 
		str_replace( '>', '&gt;', 
			str_replace( '"', '&quot;', 
				str_replace( "'", '&apos;', 
					str_replace( '&', '&amp;', $item ) ) ) ) );
	echo '  <'.$key.'>'. $strSafeItem .'</'.$key.'>' . chr(13) . chr(10);
}
echo ' </posted>' . chr(13) . chr(10);
echo ' <filed>' . chr(13) . chr(10);
foreach( $_FILES as $field => $file )
{
  echo '  <'.$field.'>' . chr(13) . chr(10);
  foreach( $file as $key => $item )
  {
	// http://www.w3schools.com/xml/xml_syntax.asp
	$strSafeItem = str_replace( '<', '&lt;', 
		str_replace( '>', '&gt;', 
			str_replace( '"', '&quot;', 
				str_replace( "'", '&apos;', 
					str_replace( '&', '&amp;', $item ) ) ) ) );
	echo '   <'.$key.'>'. $strSafeItem .'</'.$key.'>' . chr(13) . chr(10);
  }
  echo '  </'.$field.'>' . chr(13) . chr(10);
}
echo ' </filed>' . chr(13) . chr(10);
echo ' <success>'. $blnResult .'</success>' . chr(13) . chr(10);
echo ' <venue>' . chr(13) . chr(10);
foreach( $arrVenue as $key => $item )
{
	// http://www.w3schools.com/xml/xml_syntax.asp
	$strSafeItem = str_replace( '<', '&lt;', 
		str_replace( '>', '&gt;', 
			str_replace( '"', '&quot;', 
				str_replace( "'", '&apos;', 
					str_replace( '&', '&amp;', $item ) ) ) ) );
	echo '  <'.$key.'>'. $strSafeItem .'</'.$key.'>' . chr(13) . chr(10);
}
echo ' </venue>' . chr(13) . chr(10);
foreach( $arrDetails as $arrVenueDetail )
{
	echo ' <detail>' . chr(13) . chr(10);
	echo '  <idVenue>' . $arrVenueDetail [ 'venueid' ];
	echo '</idVenue>' . chr(13) . chr(10);
	echo '  <strLocationName>' . $arrVenueDetail [ 'strLocationName' ];
	echo '</strLocationName>' . chr(13) . chr(10);
	echo '  <idDetail>' . $arrVenueDetail [ 'idDetail' ];
	echo '</idDetail>' . chr(13) . chr(10);
	echo '  <idUser>' . $arrVenueDetail [ 'idUser' ];
	echo '</idUser>' . chr(13) . chr(10);
	echo '  <dteEntry>' . $arrVenueDetail [ 'dteEntry' ];
	echo '</dteEntry>' . chr(13) . chr(10);
	echo '  <blnEditable>' . $arrVenueDetail [ 'blnEditable' ];
	echo '</blnEditable>' . chr(13) . chr(10);
	echo '  <strDetailType>' . $arrVenueDetail [ 'strDetailType' ];
	echo '</strDetailType>' . chr(13) . chr(10);
	switch( $arrVenueDetail [ 'strDetailType' ] )
	{
  	  case 'contact':
		echo '  <contact>' . chr(13) . chr(10);
		foreach( $arrVenueDetail[ 'arrContactNotes' ] as $key => $item )
		{
			// http://www.w3schools.com/xml/xml_syntax.asp
			$strSafeItem = str_replace( '<', '&lt;', 
				str_replace( '>', '&gt;', 
					str_replace( '"', '&quot;', 
						str_replace( "'", '&apos;', 
							str_replace( '&', '&amp;', $item ) ) ) ) );
			echo '    <'.$key.'>'. $strSafeItem .'</'.$key.'>' . chr(13) . chr(10);
		}
		echo '  </contact>' . chr(13) . chr(10);
		break;
  	  case 'amenity':
		echo '  <amenity>' . chr(13) . chr(10);
		foreach( $arrVenueDetail[ 'arrAmenitiesNotes' ] as $key => $item )
		{
			// http://www.w3schools.com/xml/xml_syntax.asp
			$strSafeItem = str_replace( '<', '&lt;', 
				str_replace( '>', '&gt;', 
					str_replace( '"', '&quot;', 
						str_replace( "'", '&apos;', 
							str_replace( '&', '&amp;', $item ) ) ) ) );
			echo '    <'.$key.'>'. $strSafeItem .'</'.$key.'>' . chr(13) . chr(10);
		}
		echo '  </amenity>' . chr(13) . chr(10);
		break;
  	  case 'photo':
		echo '  <photo>' . chr(13) . chr(10);
		foreach( $arrVenueDetail[ 'arrPhotoNotes' ] as $key => $item )
		{
			// http://www.w3schools.com/xml/xml_syntax.asp
			$strSafeItem = str_replace( '<', '&lt;', 
				str_replace( '>', '&gt;', 
					str_replace( '"', '&quot;', 
						str_replace( "'", '&apos;', 
							str_replace( '&', '&amp;', $item ) ) ) ) );
			echo '    <'.$key.'>'. $strSafeItem .'</'.$key.'>' . chr(13) . chr(10);
		}
		echo '  </photo>' . chr(13) . chr(10);
		break;
	  default:
		break;
	}
	echo ' </detail>' . chr(13) . chr(10);
}
echo '</venueDetails>' . chr(13) . chr(10);
?>