<?php
define('ROOTPATH', 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']));

include 'db-manager.php';
$blnSignedIn=accessAuthenticate();	// Sign in if not already done
$arrUser=accessUser(); 	// Grab user data
$blnValidPost = validVenuePost();  // ToDo: If post data found is good, process. Stay on current record. Notify if save failed.
if( $blnValidPost )
{
	$objConn = dbAccessOpen();
	if( $objConn ) 
	{
		$blnValidPost = dbVenuePostUpdate( $objConn );
		dbAccessClose( $objConn );
	}
} 
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><?php
$strGoogleAPIkey='AIzaSyDO0vSeg58MtXboS4iJuTT778pKPUAsnl8';

$arrVenueList = accessVenue($_GET['id']); 
//echo '<!-- Venue list:' . chr(13) . chr( 10 ); print_r($arrVenueList); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
//$arrCloseVenues = accessNearby( 45.0911364,-75.2871138 );
//echo '<!-- ' . chr(13) . chr( 10 ); print_r($arrCloseVenues); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
$strIcon = '';
if( isJson($arrVenueList[1]['strGeolocationPoints']) ){
	$arrGeolocationPoints = json_decode($arrVenueList[1]['strGeolocationPoints'], true);
//echo '<!-- Geo list:' . chr(13) . chr( 10 ); print_r($arrGeolocationPoints); echo chr(13) . chr( 10 ) .'-->' . chr(13) . chr( 10 );
	if( isset($arrGeolocationPoints['icon']) ) {
		$strIcon = $arrGeolocationPoints['icon'];
	 }
	}
/**/
?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Venue Editor</title>
<!-- http://maps.google.com/maps/api/geocode/json?sensor=false&address=Mississauga -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>   
<script src="http://maps.googleapis.com/maps/api/js?key=<?=$strGoogleAPIkey?>"></script>
<!--  Use AES encryption to protect private conversations.  -->
<script src="scripts/cryptojs/components/core-min.js"></script>
<script src="scripts/cryptojs/components/enc-utf16-min.js"></script>
<script src="scripts/cryptojs/components/enc-base64-min.js"></script>
<script src="scripts/cryptojs/rollups/aes.js"></script>
<!-- script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/components/core-min.js"></script -->
<!-- script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/components/enc-utf16-min.js"></script -->
<!-- script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/components/enc-base64-min.js"></script -->
<!-- script src="http://crypto-js.googlecode.com/svn/tags/3.1.2/build/rollups/aes.js"></script -->

<style type="text/css"><!--
div { vertical-align: top }
div#navButtons {position: fixed; left: 425px; top: 1em; height: 2em; overflow-y: auto; }
div#detailEntry {position: fixed; left: 425px; top: 3em; height: 90%; overflow-y: auto; }
label {width: 140px; display: inline-block;}
.pausefloat { margin:auto; display: block; top:10% }
.pausewindow { display:none; position:fixed; z-index: 20; width:90%; left: 5%; top: 5%; height:90%; border-width:thick; border-color:#004080; border-style:groove; background-color:#ffffd8; }
.loading {position:absolute; top:0px; left:0px; z-index:-1; display:none; filter: alpha(opacity=30);}
.infowindow { display:none; position:fixed; z-index: 10; width:30%; left: 60%; top: 4em; border-width:thick; border-color:#004080; border-style:groove; background-color:#ffffd8; background-image:url("images/bkgrnd.gif"); }
label.privacy {font-size:x-small; text-align:right}
#googleMap { width: 400px; height: 400px; display: block }
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

<script type="text/javascript"><!--
  var objGoogleMap;
  var objMarker;
  var objMarkers = Array();
  var objInfoWindow;
  var imgPause = Array();

// Initialize change functions when the page finishes loading.
$(function(){
// Initialize change function on the Lock Map checkbox. Note: disabled fields cannot be posted in some browsers.
  jQuery('#mapLock').change(function(){if($('#mapLock').attr('checked')=='checked'){jQuery('#latitude').attr('disabled','');jQuery('#longitude').attr('disabled','');}else{jQuery('#latitude').attr('disabled',false);jQuery('#longitude').attr('disabled',false);}});

// Initialize change functions on fields that change the map.
jQuery('#locationName').change(function(){getCoordinates();});
jQuery('#Address').change(function(){getCoordinates();});
jQuery('#city').change(function(){getCoordinates();});
jQuery('#province').change(function(){getCoordinates();});
jQuery('#postalCode').change(function(){getCoordinates();});
jQuery('#icon').change(function(){getCoordinates();});
jQuery('#latitude').change(function(){redrawMaps();});
jQuery('#longitude').change(function(){redrawMaps();});
google.maps.event.addDomListener(window, 'load', initialize);

// Done: Initialize change functions on fields that work with detail fields.
jQuery('#ContactComboBox').change(function(){displayContactDetailsFields();});
jQuery('#AmenitiesComboBox').change(function(){displayAmenityDetailsFields();});
jQuery('#btnVenue').click(function(){postVenue();});
jQuery('#btnNewContact').click(function(){postContact();});
jQuery('#btnNewAmenity').click(function(){postAmenity();});
jQuery('#btnNewPhoto').click(function(){postPhoto();});
// Run for the first time
displayContactDetailsFields();
displayAmenityDetailsFields();
$.post('<?=ROOTPATH?>/auth-venue-detail-post.php?id=<?=$arrVenueList[1]['idVenue']?>',
        function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			postDisplay( data ); 
        });

jQuery('#btnDelete').click(function(){
			postDelete(<?=$arrVenueList[2]['idVenue']?>); 
            // window.location.href = '<?=ROOTPATH?>/auth-venue-edit.php?id=<?=$arrVenueList[2]['idVenue']?>';  // after delete go to next 
          }); 
jQuery('#btnPrevious').click(function(){
            window.location.href = '<?=ROOTPATH?>/auth-venue-edit.php?id=<?=$arrVenueList[0]['idVenue']?>';  
          }); 
jQuery('#btnNext').click(function(){
            window.location.href = '<?=ROOTPATH?>/auth-venue-edit.php?id=<?=$arrVenueList[2]['idVenue']?>'; 
          });
// jQuery('#venueid').val(< ?=$arrVenueList[1]['idVenue']? >); 
jQuery('#btnNew').click(function(){
            window.location.href = '<?=ROOTPATH?>/auth-venue-edit.php?id=-1';  
          }); 
jQuery('#contactPlaintext').change(function(){encryptSecret();jQuery('#contactEncryptKey').change(function(){encryptSecret();});});
jQuery('#contactEncrypttext').change(function(){decryptSecret();jQuery('#contactEncryptKey').change(function(){decryptSecret();});});


jQuery(document).ajaxError(function(){ 		
	pauseScreenOff();
	});
setTimeout( pauseScreensLoad, 1500 );	
});

// Get the longitude and latitude from Google API. (Online)
function getCoordinates()
{
  var strHTMLAddress= $.trim( $('#locationName').val() ) != '' ? '<strong>'+ $.trim( $('#locationName').val() )+'</strong>' : '';
  strHTMLAddress= ($.trim( strHTMLAddress ) != '' ? strHTMLAddress +'<br />' : '' ) + $('#Address').val();
  strHTMLAddress= ($.trim( strHTMLAddress ) != '' ? strHTMLAddress +'<br />' : '' ) + $('#city').val();
  strHTMLAddress= ($.trim( strHTMLAddress ) != '' ? strHTMLAddress +'<br />' : '' ) + $('#province').val();
  strHTMLAddress= ($.trim( strHTMLAddress ) != '' ? strHTMLAddress +'<br />' : '' ) + $('#postalCode').val();
  objInfoWindow.setContent( $.trim( strHTMLAddress ) != '' ? strHTMLAddress : 'Enter a location description' );
  objMarker.setTitle( $.trim( $('#locationName').val() ) != '' ? $.trim( $('#locationName').val() ) : 'Enter a location description');

  var strAddress= ''+ $('#locationName').val();
  strAddress+= ($.trim( strAddress ) != '' && $.trim( $('#Address').val() ) != '' ? ', ' : '' ) + $('#Address').val();
  strAddress+= ($.trim( strAddress ) != '' && $.trim( $('#city').val() ) != ''  ? ', ' : '' ) + $('#city').val();
  strAddress+= ($.trim( strAddress ) != '' && $.trim( $('#province').val() ) != ''  ? ', ' : '' ) + $('#province').val();
  strAddress+= ($.trim( strAddress ) != '' && $.trim( $('#postalCode').val() ) != ''  ? ', ' : '' ) + $('#postalCode').val();
  if( $('#mapLock').attr('checked')!='checked' )
  {
	  console.log( 'Address:' + strAddress );
	  jQuery.getJSON( 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=' + ( $.trim( strAddress ) != '' ? encodeURIComponent( strAddress ) : 'Mississauga%2C+Ontario' ), function(oResult){
	  if(oResult.status=="OK")
	  { 
		$('#latitude').val(oResult.results[0].geometry.location.lat);
		$('#longitude').val(oResult.results[0].geometry.location.lng);
		redrawMaps();
		if($('#city').val()=='')
		{
			var str=oResult.results[0].formatted_address;
			var str1=parseAddress(oResult.results[0].address_components,'sublocality_level_1');
			var str2=parseAddress(oResult.results[0].address_components,'locality');
			$('#city').val( ( ( str ).indexOf( str1 ) >= 0 ? str1 : str2 ) );
			console.log(str); 
			console.log(str1); 
			console.log(str2); 
		};
		if($('#province').val()=='')
		{
			$('#province').val(parseAddress(oResult.results[0].address_components,'administrative_area_level_1'))};
			if($('#Address').val()==''){var str=oResult.results[0].formatted_address;
				var str1=parseAddress(oResult.results[0].address_components,'street_number');
				var str2=parseAddress(oResult.results[0].address_components,'route');
				$('#Address').val( ( str.indexOf( str1 )>=0 ? str1 + ' ': '' ) + ( str.indexOf( str2 )>=0 ? str2 : '' ) );
			} 
		} 
	  } );
  }
  var strMarkerImage;
  switch( $('#icon').val() )
	{
	case 'archery':
		strMarkerImage = 'images/archery.png';
		break;
	case 'pirates':
		strMarkerImage = 'images/pirates.png';
		break;
	case 'battlefield':
		strMarkerImage = 'images/battlefield.png';
		break;
	case 'arena':
		strMarkerImage = 'images/icehockey.png';
		break;
	case 'soccer':
		strMarkerImage = 'images/soccer.png';
		break;
	case 'usfootball':
		strMarkerImage = 'images/usfootball.png';
		break;
	case 'baseball':
		strMarkerImage = 'images/baseball.png';
		break;
	case 'summercamp':
		strMarkerImage = 'images/summercamp.png';
		break;
	case 'park':
		strMarkerImage = 'images/tree.png';
		break;
	case 'school':
		strMarkerImage = 'images/school.png';
		break;
	case 'parkshelter':
		strMarkerImage = 'images/shelter_picnic.png';
		break;
	case 'dance_class':
		strMarkerImage = 'images/dance_class.png';
		break;
	case 'shootingrange':
		strMarkerImage = 'images/shootingrange.png';
		break;
	case 'statue':
		strMarkerImage = 'images/statue-2.png';
		break;
	case 'church':
		strMarkerImage = 'images/icon-sevilla.png';
		break;
	case 'tower':
		strMarkerImage = 'images/tower.png';
		break;
	case 'palace':
		strMarkerImage = 'images/palace-2.png';
		break;
	case 'citywalls':
		strMarkerImage = 'images/citywalls.png';
		break;
	case 'fairgrounds':
		strMarkerImage = 'images/ferriswheel.png';
		break;
	case 'townhall':
		strMarkerImage = 'images/townhall.png';
		break;
	case 'communitycentre':
		strMarkerImage = 'images/communitycentre.png';
		break;
	case 'grocery':
		strMarkerImage = 'images/conveniencestore.png';
		break;
	case 'fishing':
		strMarkerImage = 'images/restaurant_fish.png';
		break;
	case 'conference':
		strMarkerImage = 'images/conference.png';
		break;
	case 'fuel':
		strMarkerImage = 'images/fillingstation.png';
		break;
	case 'mall':
		strMarkerImage = 'images/mall.png';
		break;
	default:
		strMarkerImage = 'images/star-3.png';
		break;
	}
  var markerIcon = { url : strMarkerImage }; 	
  objMarker.setIcon(markerIcon);
}

// Get the new map data from Google API. 
// Done: get the nearby venues from the database. Add in the markers.
function redrawMaps(){
  var objLatLng = new google.maps.LatLng( jQuery('#latitude').val(), jQuery('#longitude').val() );
  var strHTMLAddress= $.trim( $('#locationName').val() ) != '' ? '<strong>'+ $.trim( $('#locationName').val() )+'</strong>' : '';
  strHTMLAddress= ($.trim( strHTMLAddress ) != '' ? strHTMLAddress +'<br />' : '' ) + $('#Address').val();
  strHTMLAddress= ($.trim( strHTMLAddress ) != '' ? strHTMLAddress +'<br />' : '' ) + $('#city').val();
  strHTMLAddress= ($.trim( strHTMLAddress ) != '' ? strHTMLAddress +'<br />' : '' ) + $('#province').val();
  strHTMLAddress= ($.trim( strHTMLAddress ) != '' ? strHTMLAddress +'<br />' : '' ) + $('#postalCode').val();
  objMarker.setPosition( objLatLng );
  objGoogleMap.panTo( objLatLng ); 
  setTimeout( getNewMarkers, 2000 );  // There is a delay between panning to a map and setting the coordinates.  Don't want the default.
  objMarker.setTitle( $.trim( $('#locationName').val() ) != '' ? $.trim( $('#locationName').val() ) : 'Enter a location description');
  objInfoWindow.setContent( $.trim( strHTMLAddress ) != '' ? strHTMLAddress : 'Enter a location description' );
  jQuery('#geolatitude').val(jQuery('#latitude').val());
  jQuery('#geolongitude').val(jQuery('#longitude').val());
  getNearby();
}


function getNewMarkers()
{
  strZoomLevel = objGoogleMap.getZoom();
  strLatLng = objGoogleMap.getCenter();

  // Loading markers
  var request = $.get('<?=ROOTPATH?>/auth-venue-onmap-list.php?lat='+ strLatLng.lat() +'&lng='+ strLatLng.lng() +'&zoom='+ strZoomLevel,function(xmlData,status)
	{
		if( status == 'success' )
		{
		  var objXMLdata = $.parseXML( xmlData ); 
		  var fltZoom = objGoogleMap.getZoom();
		  $( objXMLdata ).find('nearbyList').each(function()
			// Load up on markers if there are any.
			{
			// Custom markers: https://mapicons.mapsmarker.com/numbers-letters/special-characters/?style=&custom_color=ff0707
			/*
			License
			Icons are availabe unter the Creative Commons Attribution-Share Alike 3.0 Unported license (CC BY SA 3.0) which lets you remix, tweak, and build upon our work even for commercial reasons, as long as you credit the project and license your new creations under the identical terms.
			Please credit as follows: Maps Icons Collection https://mapicons.mapsmarker.com
			*/
			  var fltScale = 0.75; 
			  var strMarkerImage = 'images/symbol_blank.png';
			  if(  $(this).find('strGeolocationPoints').text().indexOf( 'icon' ) >= 0 )
			  {
				try { 
					var objJSONpoints = jQuery.parseJSON($(this).find('strGeolocationPoints').text());
					var blnLocalOnly = false;
					switch( objJSONpoints.icon )
					{
					case 'archery':
						strMarkerImage = 'images/archery.png';
						break;
					case 'pirates':
						strMarkerImage = 'images/pirates.png';
						break;
					case 'battlefield':
						strMarkerImage = 'images/battlefield.png';
						break;
					case 'arena':
						strMarkerImage = 'images/icehockey.png';
						break;
					case 'soccer':
						strMarkerImage = 'images/soccer.png';
						break;
					case 'usfootball':
						strMarkerImage = 'images/usfootball.png';
						break;
					case 'baseball':
						strMarkerImage = 'images/baseball.png';
						break;
					case 'summercamp':
						strMarkerImage = 'images/summercamp.png';
						break;
					case 'park':
						strMarkerImage = 'images/tree.png';
						break;
					case 'school':
						strMarkerImage = 'images/school.png';
						break;
					case 'parkshelter':
						strMarkerImage = 'images/shelter_picnic.png';
						break;
					case 'dance_class':
						strMarkerImage = 'images/dance_class.png';
						break;
					case 'shootingrange':
						strMarkerImage = 'images/shootingrange.png';
						break;
					case 'statue':
						strMarkerImage = 'images/statue-2.png';
						break;
					case 'conference':
						strMarkerImage = 'images/conference.png';
						break;
					case 'church':
						strMarkerImage = 'images/icon-sevilla.png';
						break;
					case 'tower':
						strMarkerImage = 'images/tower.png';
						break;
					case 'fishing':
						strMarkerImage = 'images/restaurant_fish.png';
						break;
					case 'fairgrounds':
						strMarkerImage = 'images/ferriswheel.png';
						break;
					case 'palace':
						strMarkerImage = 'images/palace-2.png';
						break;
					case 'citywalls':
						strMarkerImage = 'images/citywalls.png';
						break;
					case 'townhall':
						strMarkerImage = 'images/townhall.png';
						break;
					case 'communitycentre':
						strMarkerImage = 'images/communitycentre.png';
						break;
					case 'grocery':
						strMarkerImage = 'images/conveniencestore.png';
						blnLocalOnly = (fltZoom<14);
						fltScale = 0.67;
						console.log( 'Zoom level: ' + fltZoom + ' will show: ' + blnLocalOnly );
						break;
					case 'fuel':
						strMarkerImage = 'images/fillingstation.png';
						blnLocalOnly = (fltZoom<14);
						fltScale = 0.67;
						break;
					case 'mall':
						strMarkerImage = 'images/mall.png';
						blnLocalOnly = (fltZoom<14);
						fltScale = 0.67;
						break;
					default:
						break;
					}
				}
				catch (e) { console.log( e.message ); }
			  }
		  
		  if( $(this).find('idVenue').text() == jQuery('#venueid').val() )
		  {
			strMarkerImage = strMarkerImage	!= 'images/symbol_blank.png' ? strMarkerImage : 'images/star-3.png';
			var markerIcon = { url : strMarkerImage }; 	
			objMarker.setIcon(markerIcon);
		  }
		  else
		  { 
		    if( blnLocalOnly !== true )
			{
				if( typeof objMarkers[$(this).find('idVenue').text()] === 'undefined' )
				{		    
				  objMarkers[$(this).find('idVenue').text()] = new google.maps.Marker({
					position: new google.maps.LatLng($(this).find('strGeoLatitude').text(),$(this).find('strGeoLongitude').text()),
					map: objGoogleMap,
					draggable: false,
					animation: google.maps.Animation.DROP,
					icon: {
						size: new google.maps.Size(Math.ceil(32 * fltScale), Math.ceil(38 * fltScale)),
						scaledSize: new google.maps.Size(Math.ceil(32 * fltScale), Math.ceil(38 * fltScale)),
						url: strMarkerImage
						},
					title: $.trim($(this).find('strLocationName').text()) != '' ? $(this).find('strLocationName').text() : $(this).find('strGeoLatitude').text()+','+$(this).find('strGeoLongitude').text()
				  });
			  
				  var infoMarker = new google.maps.InfoWindow({
				  content: $.trim($(this).find('strLocationName').text())+'<br/>' +$.trim($(this).find('strCity').text())+', ' +$.trim($(this).find('strProvince').text())
				  });
			
				  google.maps.event.addListener(infoMarker, 'click', function() {
					  objInfoWindow.open(objGoogleMap,infoMarker);
					  });
				}
			}
		}	  
			}); 

		}
	}
  );
  request.error(function(jqXHR, textStatus, errorThrown) {
	if (textStatus == 'timeout')
	console.log('The server is not responding');
	
	if (textStatus == 'error')
	console.log(errorThrown);
	
	// Etc
	});

}
function getNearby()
{
  var request = $.get('<?=ROOTPATH?>/auth-venue-nearby-list.php?lat='+ jQuery('#latitude').val() +'&lng='+ jQuery('#longitude').val(),function(xmlData,status)
	{
		if( status == 'success' )
		{
		  var objXMLdata = $.parseXML( xmlData );
		  var strNearbyHTML = ''; 
		  $( objXMLdata ).find('nearbyList').each(function()
			// Load up on markers if there are any.
			{
				if(  $(this).find('idVenue').text() != '<?=$_GET['id']?>' ) // Only display if NOT this venue
				{
					strNearbyHTML += (strNearbyHTML != '' ? '<br />' : '' ) 
						+ '<a href="auth-venue-edit.php?id='+ $(this).find('idVenue').text() +'">' 
						+ $(this).find('strLocationName').text() + '</a>';
				}
			}); 
		  jQuery('#detailEntryNearby').html( strNearbyHTML );	// Display headers for nearby
		}
	}
  );
}

function initialize() {
  var myLatLng = {lat: 43.5890452, lng: -79.6441198};
  var mapProp = {
    center:new google.maps.LatLng(43.5890452,-79.6441198),
    mapTypeId:google.maps.MapTypeId.HYBRID,
    zoom:14
  };
   objGoogleMap=new google.maps.Map(document.getElementById("googleMap"), mapProp);
  var fltScale = 1; 
  objMarker = new google.maps.Marker({
    position: myLatLng,
    map: objGoogleMap,
    draggable: true,
    animation: google.maps.Animation.DROP,
			icon: {
				size: new google.maps.Size(Math.ceil(32 * fltScale), Math.ceil(38 * fltScale)),
				scaledSize: new google.maps.Size(Math.ceil(32 * fltScale), Math.ceil(38 * fltScale)),
				url: 'images/star-3.png'
				},
    title: 'Enter a location description'
  });
  objMarker.addListener('position_changed',function(){
	jQuery('#latitude').val(this.getPosition().lat());
	jQuery('#longitude').val(this.getPosition().lng());
  });
  objMarker.addListener('mouseup',function(){
	redrawMaps();
  });
  objInfoWindow = new google.maps.InfoWindow({
  content:"Enter location content"
  });

  google.maps.event.addListener(objMarker, 'click', function() {
	objInfoWindow.open(objGoogleMap,objMarker);
	});
	
  objGoogleMap.addListener('dragend',function(){
	getNewMarkers();
	getNearby();
  });
  objGoogleMap.addListener('zoom_changed',function(){
	getNewMarkers();
  });
  redrawMaps();
}

function parseAddress( oAddress, sComponent )
{
  for( var intI=0; intI<oAddress.length; intI++ )
  {
    if( oAddress[intI].types[0]==sComponent ) return( oAddress[intI].short_name );
  }
}

function displayContactDetailsFields()
{ 
  switch( jQuery('#ContactComboBox').val() )
  {
  	case 'conversation':
		jQuery('label[for="ContactPerson"]').css('display','inline-block');
		jQuery('#contactPerson').css('display','inline');
		jQuery('label[for="ContactEvent"]').css('display','inline-block');
		jQuery('#contactEvent').css('display','inline');
		jQuery('label[for="ContactDate"]').css('display','inline-block');
		jQuery('#contactDate').css('display','inline');
		jQuery('#contactPlaintext').css('display','inline');
		jQuery('#contactInfo').css('display','inline');
		jQuery('#contactEntry').css('display','inline');
		jQuery('#contactSurvey').css('display','inline');
		jQuery('label[for="ContactEncryptKey"]').css('display','inline-block');
		jQuery('#contactEncryptKey').css('display','inline');
		jQuery('label[for="chkKeyCookie"]').css('display','inline-block');
		jQuery('#chkKeyCookie').css('display','inline-block');
		jQuery('label[for="ContactEncrypttext"]').css('display','inline-block');
		jQuery('#contactEncrypttext').css('display','inline');
		jQuery('#btnNewContact').css('display','inline');
		break;
  	case 'event':
		jQuery('label[for="ContactPerson"]').css('display','inline-block');
		jQuery('#contactPerson').css('display','inline');
		jQuery('label[for="ContactEvent"]').css('display','inline-block');
		jQuery('#contactEvent').css('display','inline');
		jQuery('label[for="ContactDate"]').css('display','inline-block');
		jQuery('#contactDate').css('display','inline');
		jQuery('#contactPlaintext').css('display','inline');
		jQuery('#contactInfo').css('display','inline');
		jQuery('#contactEntry').css('display','inline');
		jQuery('#contactSurvey').css('display','inline');
		jQuery('label[for="ContactEncryptKey"]').css('display','inline-block');
		jQuery('#contactEncryptKey').css('display','inline');
		jQuery('label[for="ContactEncrypttext"]').css('display','none');
		jQuery('#contactEncrypttext').css('display','none');
		jQuery('label[for="chkKeyCookie"]').css('display','inline-block');
		jQuery('#chkKeyCookie').css('display','inline-block');
		jQuery('#btnNewContact').css('display','inline');
		break;
  	case 'meeting':
		jQuery('label[for="ContactPerson"]').css('display','inline-block');
		jQuery('#contactPerson').css('display','inline-block');
		jQuery('label[for="ContactEvent"]').css('display','none');
		jQuery('#contactEvent').css('display','none');
		jQuery('label[for="ContactDate"]').css('display','inline-block');
		jQuery('#contactDate').css('display','inline-block');
		jQuery('#contactPlaintext').css('display','inline-block');
		jQuery('#contactInfo').css('display','inline');
		jQuery('#contactEntry').css('display','inline');
		jQuery('#contactSurvey').css('display','inline');
		jQuery('label[for="ContactEncryptKey"]').css('display','inline-block');
		jQuery('#contactEncryptKey').css('display','inline-block');
		jQuery('label[for="chkKeyCookie"]').css('display','inline-block');
		jQuery('#chkKeyCookie').css('display','inline-block');
		jQuery('label[for="ContactEncrypttext"]').css('display','none');
		jQuery('#contactEncrypttext').css('display','none');
		jQuery('#btnNewContact').css('display','block');
		break;
  	case 'financial':
		jQuery('label[for="ContactPerson"]').css('display','inline-block');
		jQuery('#contactPerson').css('display','inline-block');
		jQuery('label[for="ContactEvent"]').css('display','inline-block');
		jQuery('#contactEvent').css('display','inline-block');
		jQuery('label[for="ContactDate"]').css('display','inline-block');
		jQuery('#contactDate').css('display','inline-block');
		jQuery('#contactPlaintext').css('display','inline-block');
		jQuery('#contactInfo').css('display','inline');
		jQuery('#contactEntry').css('display','none');
		jQuery('#contactSurvey').css('display','none');
		jQuery('label[for="ContactEncryptKey"]').css('display','inline-block');
		jQuery('#contactEncryptKey').css('display','inline-block');
		jQuery('label[for="ContactEncrypttext"]').css('display','none');
		jQuery('#contactEncrypttext').css('display','none');
		jQuery('label[for="chkKeyCookie"]').css('display','inline-block');
		jQuery('#chkKeyCookie').css('display','inline-block');
		jQuery('#btnNewContact').css('display','inline');
		break;
	default:
		jQuery('label[for="ContactPerson"]').css('display','none');
		jQuery('#contactPerson').css('display','none');
		jQuery('label[for="ContactEvent"]').css('display','none');
		jQuery('#contactEvent').css('display','none');
		jQuery('label[for="ContactDate"]').css('display','none');
		jQuery('#contactDate').css('display','none');
		jQuery('#contactPlaintext').css('display','none');
		jQuery('#contactInfo').css('display','none');
		jQuery('#contactEntry').css('display','none');
		jQuery('#contactSurvey').css('display','none');
		jQuery('label[for="ContactEncryptKey"]').css('display','none');
		jQuery('#contactEncryptKey').css('display','none');
		jQuery('label[for="ContactEncrypttext"]').css('display','none');
		jQuery('#contactEncrypttext').css('display','none');
		jQuery('label[for="chkKeyCookie"]').css('display','none');
		jQuery('#chkKeyCookie').css('display','none');
		jQuery('#btnNewContact').css('display','none');
		break;
  }

}

function displayAmenityDetailsFields()
{ 
  switch( jQuery('#AmenitiesComboBox').val() )
  {
  	case 'summary':
		jQuery('#amenitiesRoom').css('display','inline-block');
		jQuery('label[for="AmenitiesRoom"]').css('display','inline-block');
		jQuery('#amenitiesLength').css('display','none');
		jQuery('label[for="AmenitiesLength"]').css('display','none');
		jQuery('#amenitiesWidth').css('display','none');
		jQuery('label[for="AmenitiesWidth"]').css('display','none');
		jQuery('#amenitiesHeight').css('display','none');
		jQuery('label[for="AmenitiesHeight"]').css('display','none');
		jQuery('#amenitiesElevation').css('display','none');
		jQuery('label[for="AmenitiesElevation"]').css('display','none');
		jQuery('#amenitiesDescription').css('display','inline-block');
		jQuery('label[for="AmenitiesDescription"]').css('display','inline-block');
		jQuery('#amenitiesInfo').css('display','inline');
		jQuery('#bigEventGroup').css('display','block');
		jQuery('#smallEventGroup').css('display','block');
		jQuery('#campingGroup').css('display','block');
		jQuery('#meetingsGroup').css('display','block');
		jQuery('#electricityGroup').css('display','block');
		jQuery('#bathroomGroup').css('display','block');
		jQuery('#accessibleGroup').css('display','block');
		jQuery('#elevatorGroup').css('display','block');
		jQuery('#fightingHeader').css('display','inline-block');
		jQuery('#fightingGroup').css('display','block');
		jQuery('#fencingHeader').css('display','inline-block');
		jQuery('#fencingGroup').css('display','block');
		jQuery('#archeryHeader').css('display','inline-block');
		jQuery('#archeryGroup').css('display','block');
		jQuery('#merchantHeader').css('display','inline-block');
		jQuery('#merchantGroup').css('display','block');
		jQuery('#classesHeader').css('display','inline-block');
		jQuery('#classesGroup').css('display','block');
		jQuery('#specialtyGroup').css('display','block');
		jQuery('#kitchenHeader').css('display','inline-block');
		jQuery('#kitchenGroup').css('display','block');
		jQuery('#kitchenfridgeGroup').css('display','block');
		jQuery('#parkingHeader').css('display','inline-block');
		jQuery('#parkingGroup').css('display','block');
		jQuery('#parkingstreetGroup').css('display','block');
		jQuery('#transitHeader').css('display','inline-block');
		jQuery('#transitGroup').css('display','block');
		jQuery('#loadingGroup').css('display','block');
		jQuery('#btnNewAmenity').css('display','block');
		break;
  	case 'room':
		jQuery('#amenitiesRoom').css('display','inline-block');
		jQuery('label[for="AmenitiesRoom"]').css('display','inline-block');
		jQuery('#amenitiesLength').css('display','inline');
		jQuery('label[for="AmenitiesLength"]').css('display','inline-block');
		jQuery('#amenitiesWidth').css('display','inline-block');
		jQuery('label[for="AmenitiesWidth"]').css('display','inline-block');
		jQuery('#amenitiesHeight').css('display','inline-block');
		jQuery('label[for="AmenitiesHeight"]').css('display','inline-block');
		jQuery('#amenitiesElevation').css('display','none');
		jQuery('label[for="AmenitiesElevation"]').css('display','none');
		jQuery('#amenitiesDescription').css('display','inline-block');
		jQuery('label[for="AmenitiesDescription"]').css('display','inline-block');
		jQuery('#amenitiesInfo').css('display','inline');
		jQuery('#bigEventGroup').css('display','block');
		jQuery('#smallEventGroup').css('display','block');
		jQuery('#campingGroup').css('display','block');
		jQuery('#meetingsGroup').css('display','block');
		jQuery('#electricityGroup').css('display','block');
		jQuery('#bathroomGroup').css('display','block');
		jQuery('#accessibleGroup').css('display','block');
		jQuery('#elevatorGroup').css('display','block');
		jQuery('#fightingHeader').css('display','inline-block');
		jQuery('#fightingGroup').css('display','block');
		jQuery('#fencingHeader').css('display','inline-block');
		jQuery('#fencingGroup').css('display','block');
		jQuery('#archeryHeader').css('display','inline-block');
		jQuery('#archeryGroup').css('display','block');
		jQuery('#merchantHeader').css('display','inline-block');
		jQuery('#merchantGroup').css('display','block');
		jQuery('#classesHeader').css('display','inline-block');
		jQuery('#classesGroup').css('display','block');
		jQuery('#specialtyGroup').css('display','block');
		jQuery('#kitchenHeader').css('display','none');
		jQuery('#kitchenGroup').css('display','none');
		jQuery('#kitchenfridgeGroup').css('display','none');
		jQuery('#parkingHeader').css('display','none');
		jQuery('#parkingGroup').css('display','none');
		jQuery('#parkingstreetGroup').css('display','none');
		jQuery('#transitHeader').css('display','none');
		jQuery('#transitGroup').css('display','none');
		jQuery('#loadingGroup').css('display','none');
		jQuery('#btnNewAmenity').css('display','block');
		break;
  	case 'gym':
		jQuery('#amenitiesRoom').css('display','inline-block');
		jQuery('label[for="AmenitiesRoom"]').css('display','inline-block');
		jQuery('#amenitiesLength').css('display','inline-block');
		jQuery('label[for="AmenitiesLength"]').css('display','inline-block');
		jQuery('#amenitiesWidth').css('display','inline-block');
		jQuery('label[for="AmenitiesWidth"]').css('display','inline-block');
		jQuery('#amenitiesHeight').css('display','none');
		jQuery('label[for="AmenitiesHeight"]').css('display','none');
		jQuery('#amenitiesElevation').css('display','none');
		jQuery('label[for="AmenitiesElevation"]').css('display','none');
		jQuery('#amenitiesDescription').css('display','inline-block');
		jQuery('label[for="AmenitiesDescription"]').css('display','inline-block');
		jQuery('#amenitiesInfo').css('display','inline');
		jQuery('#bigEventGroup').css('display','block');
		jQuery('#smallEventGroup').css('display','block');
		jQuery('#campingGroup').css('display','block');
		jQuery('#meetingsGroup').css('display','block');
		jQuery('#electricityGroup').css('display','block');
		jQuery('#bathroomGroup').css('display','block');
		jQuery('#accessibleGroup').css('display','block');
		jQuery('#elevatorGroup').css('display','block');
		jQuery('#fightingHeader').css('display','inline-block');
		jQuery('#fightingGroup').css('display','block');
		jQuery('#fencingHeader').css('display','inline-block');
		jQuery('#fencingGroup').css('display','block');
		jQuery('#archeryHeader').css('display','inline-block');
		jQuery('#archeryGroup').css('display','block');
		jQuery('#merchantHeader').css('display','inline-block');
		jQuery('#merchantGroup').css('display','block');
		jQuery('#classesHeader').css('display','inline-block');
		jQuery('#classesGroup').css('display','block');
		jQuery('#specialtyGroup').css('display','block');
		jQuery('#kitchenHeader').css('display','none');
		jQuery('#kitchenGroup').css('display','none');
		jQuery('#kitchenfridgeGroup').css('display','none');
		jQuery('#parkingHeader').css('display','none');
		jQuery('#parkingGroup').css('display','none');
		jQuery('#parkingstreetGroup').css('display','none');
		jQuery('#transitHeader').css('display','none');
		jQuery('#transitGroup').css('display','none');
		jQuery('#loadingGroup').css('display','none');
		jQuery('#btnNewAmenity').css('display','block');
		break;
  	case 'field':
		jQuery('#amenitiesRoom').css('display','inline-block');
		jQuery('label[for="AmenitiesRoom"]').css('display','inline-block');
		jQuery('#amenitiesLength').css('display','inline-block');
		jQuery('label[for="AmenitiesLength"]').css('display','inline-block');
		jQuery('#amenitiesWidth').css('display','inline-block');
		jQuery('label[for="AmenitiesWidth"]').css('display','inline-block');
		jQuery('#amenitiesHeight').css('display','none');
		jQuery('label[for="AmenitiesHeight"]').css('display','none');
		jQuery('#amenitiesElevation').css('display','none');
		jQuery('label[for="AmenitiesElevation"]').css('display','none');
		jQuery('#amenitiesDescription').css('display','inline-block');
		jQuery('label[for="AmenitiesDescription"]').css('display','inline-block');
		jQuery('#amenitiesInfo').css('display','inline');
		jQuery('#bigEventGroup').css('display','block');
		jQuery('#smallEventGroup').css('display','block');
		jQuery('#campingGroup').css('display','block');
		jQuery('#meetingsGroup').css('display','block');
		jQuery('#electricityGroup').css('display','block');
		jQuery('#bathroomGroup').css('display','block');
		jQuery('#accessibleGroup').css('display','block');
		jQuery('#elevatorGroup').css('display','none');
		jQuery('#fightingHeader').css('display','inline-block');
		jQuery('#fightingGroup').css('display','block');
		jQuery('#fencingHeader').css('display','inline-block');
		jQuery('#fencingGroup').css('display','block');
		jQuery('#archeryHeader').css('display','inline-block');
		jQuery('#archeryGroup').css('display','block');
		jQuery('#merchantHeader').css('display','inline-block');
		jQuery('#merchantGroup').css('display','block');
		jQuery('#classesHeader').css('display','inline-block');
		jQuery('#classesGroup').css('display','block');
		jQuery('#specialtyGroup').css('display','block');
		jQuery('#kitchenHeader').css('display','none');
		jQuery('#kitchenGroup').css('display','none');
		jQuery('#kitchenfridgeGroup').css('display','none');
		jQuery('#parkingHeader').css('display','inline-block');
		jQuery('#parkingGroup').css('display','block');
		jQuery('#parkingstreetGroup').css('display','block');
		jQuery('#transitHeader').css('display','inline-block');
		jQuery('#transitGroup').css('display','block');
		jQuery('#loadingGroup').css('display','none');
		jQuery('#btnNewAmenity').css('display','block');
		break;
  	case 'dais':
		jQuery('#amenitiesRoom').css('display','inline-block');
		jQuery('label[for="AmenitiesRoom"]').css('display','inline-block');
		jQuery('#amenitiesLength').css('display','inline-block');
		jQuery('label[for="AmenitiesLength"]').css('display','inline-block');
		jQuery('#amenitiesWidth').css('display','inline-block');
		jQuery('label[for="AmenitiesWidth"]').css('display','inline-block');
		jQuery('#amenitiesHeight').css('display','inline-block');
		jQuery('label[for="AmenitiesHeight"]').css('display','inline-block');
		jQuery('#amenitiesElevation').css('display','inline-block');
		jQuery('label[for="AmenitiesElevation"]').css('display','inline-block');
		jQuery('#amenitiesDescription').css('display','inline-block');
		jQuery('label[for="AmenitiesDescription"]').css('display','inline-block');
		jQuery('#amenitiesInfo').css('display','inline');
		jQuery('#bigEventGroup').css('display','block');
		jQuery('#smallEventGroup').css('display','block');
		jQuery('#campingGroup').css('display','none');
		jQuery('#meetingsGroup').css('display','none');
		jQuery('#electricityGroup').css('display','none');
		jQuery('#bathroomGroup').css('display','none');
		jQuery('#accessibleGroup').css('display','block');
		jQuery('#elevatorGroup').css('display','block');
		jQuery('#fightingHeader').css('display','none');
		jQuery('#fightingGroup').css('display','none');
		jQuery('#fencingHeader').css('display','none');
		jQuery('#fencingGroup').css('display','none');
		jQuery('#archeryHeader').css('display','none');
		jQuery('#archeryGroup').css('display','none');
		jQuery('#merchantHeader').css('display','none');
		jQuery('#merchantGroup').css('display','none');
		jQuery('#classesHeader').css('display','none');
		jQuery('#classesGroup').css('display','none');
		jQuery('#specialtyGroup').css('display','none');
		jQuery('#kitchenHeader').css('display','none');
		jQuery('#kitchenGroup').css('display','none');
		jQuery('#kitchenfridgeGroup').css('display','none');
		jQuery('#parkingHeader').css('display','none');
		jQuery('#parkingGroup').css('display','none');
		jQuery('#parkingstreetGroup').css('display','none');
		jQuery('#transitHeader').css('display','none');
		jQuery('#transitGroup').css('display','none');
		jQuery('#loadingGroup').css('display','none');
		jQuery('#btnNewAmenity').css('display','block');
		break;
  	case 'kitchen':
		jQuery('#amenitiesRoom').css('display','inline-block');
		jQuery('label[for="AmenitiesRoom"]').css('display','inline-block');
		jQuery('#amenitiesLength').css('display','inline-block');
		jQuery('label[for="AmenitiesLength"]').css('display','inline-block');
		jQuery('#amenitiesWidth').css('display','inline-block');
		jQuery('label[for="AmenitiesWidth"]').css('display','inline-block');
		jQuery('#amenitiesHeight').css('display','none');
		jQuery('label[for="AmenitiesHeight"]').css('display','none');
		jQuery('#amenitiesElevation').css('display','none');
		jQuery('label[for="AmenitiesElevation"]').css('display','none');
		jQuery('#amenitiesDescription').css('display','inline-block');
		jQuery('label[for="AmenitiesDescription"]').css('display','inline-block');
		jQuery('#amenitiesInfo').css('display','inline');
		jQuery('#bigEventGroup').css('display','block');
		jQuery('#smallEventGroup').css('display','block');
		jQuery('#campingGroup').css('display','block');
		jQuery('#meetingsGroup').css('display','block');
		jQuery('#electricityGroup').css('display','block');
		jQuery('#bathroomGroup').css('display','none');
		jQuery('#accessibleGroup').css('display','block');
		jQuery('#elevatorGroup').css('display','block');
		jQuery('#fightingHeader').css('display','none');
		jQuery('#fightingGroup').css('display','none');
		jQuery('#fencingHeader').css('display','none');
		jQuery('#fencingGroup').css('display','none');
		jQuery('#archeryHeader').css('display','none');
		jQuery('#archeryGroup').css('display','none');
		jQuery('#merchantHeader').css('display','none');
		jQuery('#merchantGroup').css('display','none');
		jQuery('#classesHeader').css('display','none');
		jQuery('#classesGroup').css('display','none');
		jQuery('#specialtyGroup').css('display','none');
		jQuery('#kitchenHeader').css('display','inline-block');
		jQuery('#kitchenGroup').css('display','block');
		jQuery('#kitchenfridgeGroup').css('display','block');
		jQuery('#parkingHeader').css('display','none');
		jQuery('#parkingGroup').css('display','none');
		jQuery('#parkingstreetGroup').css('display','none');
		jQuery('#transitHeader').css('display','none');
		jQuery('#transitGroup').css('display','none');
		jQuery('#loadingGroup').css('display','none');
		jQuery('#btnNewAmenity').css('display','block');
		break;
  	case 'bathroom':
		jQuery('#amenitiesRoom').css('display','inline-block');
		jQuery('label[for="AmenitiesRoom"]').css('display','inline-block');
		jQuery('#amenitiesLength').css('display','inline-block');
		jQuery('label[for="AmenitiesLength"]').css('display','inline-block');
		jQuery('#amenitiesWidth').css('display','inline-block');
		jQuery('label[for="AmenitiesWidth"]').css('display','inline-block');
		jQuery('#amenitiesHeight').css('display','none');
		jQuery('label[for="AmenitiesHeight"]').css('display','none');
		jQuery('#amenitiesElevation').css('display','none');
		jQuery('label[for="AmenitiesElevation"]').css('display','none');
		jQuery('#amenitiesDescription').css('display','inline-block');
		jQuery('label[for="AmenitiesDescription"]').css('display','inline-block');
		jQuery('#amenitiesInfo').css('display','inline');
		jQuery('#bigEventGroup').css('display','block');
		jQuery('#smallEventGroup').css('display','block');
		jQuery('#campingGroup').css('display','block');
		jQuery('#meetingsGroup').css('display','block');
		jQuery('#electricityGroup').css('display','none');
		jQuery('#bathroomGroup').css('display','block');
		jQuery('#accessibleGroup').css('display','block');
		jQuery('#elevatorGroup').css('display','none');
		jQuery('#fightingHeader').css('display','none');
		jQuery('#fightingGroup').css('display','none');
		jQuery('#fencingHeader').css('display','none');
		jQuery('#fencingGroup').css('display','none');
		jQuery('#archeryHeader').css('display','none');
		jQuery('#archeryGroup').css('display','none');
		jQuery('#merchantHeader').css('display','none');
		jQuery('#merchantGroup').css('display','none');
		jQuery('#classesHeader').css('display','none');
		jQuery('#classesGroup').css('display','none');
		jQuery('#specialtyGroup').css('display','none');
		jQuery('#kitchenHeader').css('display','none');
		jQuery('#kitchenGroup').css('display','none');
		jQuery('#kitchenfridgeGroup').css('display','none');
		jQuery('#parkingHeader').css('display','none');
		jQuery('#parkingGroup').css('display','none');
		jQuery('#parkingstreetGroup').css('display','none');
		jQuery('#transitHeader').css('display','none');
		jQuery('#transitGroup').css('display','none');
		jQuery('#loadingGroup').css('display','none');
		jQuery('#btnNewAmenity').css('display','block');
		break;
  	case 'elevator':
		jQuery('#amenitiesRoom').css('display','inline-block');
		jQuery('label[for="AmenitiesRoom"]').css('display','inline-block');
		jQuery('#amenitiesLength').css('display','inline-block');
		jQuery('label[for="AmenitiesLength"]').css('display','inline-block');
		jQuery('#amenitiesWidth').css('display','inline-block');
		jQuery('label[for="AmenitiesWidth"]').css('display','inline-block');
		jQuery('#amenitiesHeight').css('display','inline-block');
		jQuery('label[for="AmenitiesHeight"]').css('display','inline-block');
		jQuery('#amenitiesElevation').css('display','none');
		jQuery('label[for="AmenitiesElevation"]').css('display','none');
		jQuery('#amenitiesDescription').css('display','inline-block');
		jQuery('label[for="AmenitiesDescription"]').css('display','inline-block');
		jQuery('#amenitiesInfo').css('display','inline');
		jQuery('#bigEventGroup').css('display','none');
		jQuery('#smallEventGroup').css('display','none');
		jQuery('#campingGroup').css('display','none');
		jQuery('#meetingsGroup').css('display','none');
		jQuery('#electricityGroup').css('display','none');
		jQuery('#bathroomGroup').css('display','none');
		jQuery('#accessibleGroup').css('display','block');
		jQuery('#elevatorGroup').css('display','block');
		jQuery('#fightingHeader').css('display','none');
		jQuery('#fightingGroup').css('display','none');
		jQuery('#fencingHeader').css('display','none');
		jQuery('#fencingGroup').css('display','none');
		jQuery('#archeryHeader').css('display','none');
		jQuery('#archeryGroup').css('display','none');
		jQuery('#merchantHeader').css('display','none');
		jQuery('#merchantGroup').css('display','none');
		jQuery('#classesHeader').css('display','none');
		jQuery('#classesGroup').css('display','none');
		jQuery('#specialtyGroup').css('display','none');
		jQuery('#kitchenHeader').css('display','none');
		jQuery('#kitchenGroup').css('display','none');
		jQuery('#kitchenfridgeGroup').css('display','none');
		jQuery('#parkingHeader').css('display','none');
		jQuery('#parkingGroup').css('display','none');
		jQuery('#parkingstreetGroup').css('display','none');
		jQuery('#transitHeader').css('display','none');
		jQuery('#transitGroup').css('display','none');
		jQuery('#loadingGroup').css('display','none');
		jQuery('#btnNewAmenity').css('display','block');
		break;
  	case 'parking':
		jQuery('#amenitiesRoom').css('display','inline-block');
		jQuery('label[for="AmenitiesRoom"]').css('display','inline-block');
		jQuery('#amenitiesLength').css('display','none');
		jQuery('label[for="AmenitiesLength"]').css('display','none');
		jQuery('#amenitiesWidth').css('display','none');
		jQuery('label[for="AmenitiesWidth"]').css('display','none');
		jQuery('#amenitiesHeight').css('display','none');
		jQuery('label[for="AmenitiesHeight"]').css('display','none');
		jQuery('#amenitiesElevation').css('display','none');
		jQuery('label[for="AmenitiesElevation"]').css('display','none');
		jQuery('#amenitiesDescription').css('display','inline-block');
		jQuery('label[for="AmenitiesDescription"]').css('display','inline-block');
		jQuery('#amenitiesInfo').css('display','inline');
		jQuery('#bigEventGroup').css('display','block');
		jQuery('#smallEventGroup').css('display','block');
		jQuery('#campingGroup').css('display','block');
		jQuery('#meetingsGroup').css('display','block');
		jQuery('#electricityGroup').css('display','block');
		jQuery('#bathroomGroup').css('display','none');
		jQuery('#accessibleGroup').css('display','block');
		jQuery('#elevatorGroup').css('display','block');
		jQuery('#fightingHeader').css('display','none');
		jQuery('#fightingGroup').css('display','none');
		jQuery('#fencingHeader').css('display','none');
		jQuery('#fencingGroup').css('display','none');
		jQuery('#archeryHeader').css('display','none');
		jQuery('#archeryGroup').css('display','none');
		jQuery('#merchantHeader').css('display','none');
		jQuery('#merchantGroup').css('display','none');
		jQuery('#classesHeader').css('display','none');
		jQuery('#classesGroup').css('display','none');
		jQuery('#specialtyGroup').css('display','none');
		jQuery('#kitchenHeader').css('display','none');
		jQuery('#kitchenGroup').css('display','none');
		jQuery('#kitchenfridgeGroup').css('display','none');
		jQuery('#parkingHeader').css('display','inline-block');
		jQuery('#parkingGroup').css('display','block');
		jQuery('#parkingstreetGroup').css('display','block');
		jQuery('#transitHeader').css('display','none');
		jQuery('#transitGroup').css('display','none');
		jQuery('#loadingGroup').css('display','block');
		jQuery('#btnNewAmenity').css('display','block');
		break;
	default:
		jQuery('#amenitiesRoom').css('display','none');
		jQuery('label[for="AmenitiesRoom"]').css('display','none');
		jQuery('#amenitiesLength').css('display','none');
		jQuery('label[for="AmenitiesLength"]').css('display','none');
		jQuery('#amenitiesWidth').css('display','none');
		jQuery('label[for="AmenitiesWidth"]').css('display','none');
		jQuery('#amenitiesHeight').css('display','none');
		jQuery('label[for="AmenitiesHeight"]').css('display','none');
		jQuery('#amenitiesElevation').css('display','none');
		jQuery('label[for="AmenitiesElevation"]').css('display','none');
		jQuery('#amenitiesDescription').css('display','none');
		jQuery('label[for="AmenitiesDescription"]').css('display','none');
		jQuery('#amenitiesInfo').css('display','none');
		jQuery('#bigEventGroup').css('display','none');
		jQuery('#smallEventGroup').css('display','none');
		jQuery('#campingGroup').css('display','none');
		jQuery('#meetingsGroup').css('display','none');
		jQuery('#electricityGroup').css('display','none');
		jQuery('#bathroomGroup').css('display','none');
		jQuery('#accessibleGroup').css('display','none');
		jQuery('#elevatorGroup').css('display','none');
		jQuery('#fightingHeader').css('display','none');
		jQuery('#fightingGroup').css('display','none');
		jQuery('#fencingHeader').css('display','none');
		jQuery('#fencingGroup').css('display','none');
		jQuery('#archeryHeader').css('display','none');
		jQuery('#archeryGroup').css('display','none');
		jQuery('#merchantHeader').css('display','none');
		jQuery('#merchantGroup').css('display','none');
		jQuery('#classesHeader').css('display','none');
		jQuery('#classesGroup').css('display','none');
		jQuery('#specialtyGroup').css('display','none');
		jQuery('#kitchenHeader').css('display','none');
		jQuery('#kitchenGroup').css('display','none');
		jQuery('#kitchenfridgeGroup').css('display','none');
		jQuery('#parkingHeader').css('display','none');
		jQuery('#parkingGroup').css('display','none');
		jQuery('#parkingstreetGroup').css('display','none');
		jQuery('#transitHeader').css('display','none');
		jQuery('#transitGroup').css('display','none');
		jQuery('#loadingGroup').css('display','none');
		jQuery('#btnNewAmenity').css('display','none');
		break;
  }
}

function encryptSecret()
{
	var txtPlaintext = jQuery('#contactPlaintext').val();
	var txtSecretKey = jQuery('#contactEncryptKey').val();
	var txtCoded = '';
//	alert( 'Plain text:'+ txtPlaintext +'\nSecret key:' + txtSecretKey );
	if( txtSecretKey != '' )
	{
		txtCoded = '' + CryptoJS.AES.encrypt(txtPlaintext, txtSecretKey);
	}
	else
	{
		txtCoded = txtPlaintext;
	} 
	if( txtPlaintext != '' ) 
	{
	 jQuery('#contactEncrypttext').val(txtCoded); 
	}
}

function decryptSecret()
{
	var txtPlaintext = '';
	var txtSecretKey = jQuery('#contactEncryptKey').val();
	var txtCoded = jQuery('#contactEncrypttext').val();
//	alert( 'Plain text:'+ txtPlaintext +'\nSecret key:' + txtSecretKey );
	if( txtSecretKey != '' )
	{
		try {
			txtPlaintext = '' + CryptoJS.AES.decrypt(txtCoded, txtSecretKey).toString(CryptoJS.enc.Utf8);
		}
		catch( e ) { 
			console.log( 'Wrong secret key:' + e ); 
			txtPlaintext = '' + CryptoJS.AES.decrypt(txtCoded, txtSecretKey);
		}
	}
	else
	{
		txtPlaintext = txtCoded;
	} 
	if( txtCoded != '' ) 
	{
	 jQuery('#contactPlaintext').val(txtPlaintext); 
	 jQuery('#contactEncryptKey').change(function(){decryptSecret();});
	}
}

function decryptMessage( elePlain, eleKey, eleCode )
{
	elePlain = '#' + (elePlain == '' ? 'contactPlaintext' : elePlain );
	eleKey = '#' + (eleKey == '' ? 'contactEncryptKey' : eleKey );
	eleCode = '#' + (eleCode == '' ? 'contactEncrypttext' : eleCode );
	var txtPlaintext = '';
	var txtSecretKey = jQuery(eleKey).val();
	var txtCoded = jQuery(eleCode).text();
//	alert( 'Plain text:'+ txtPlaintext +'\nSecret key:' + txtSecretKey );
	if( txtSecretKey != '' )
	{
		try {
			txtPlaintext = escapeLineBreak( escapeHtml( '' + CryptoJS.AES.decrypt(txtCoded, txtSecretKey).toString(CryptoJS.enc.Utf8) ) );
			setCookieForSecretKey( document.getElementById(jQuery(eleKey).prop('id')) );
		}
		catch( e ) { 
			console.log( 'Wrong secret key:' + e ); 
			txtPlaintext = txtCoded; //escapeLineBreak( escapeHtml( CryptoJS.AES.decrypt(txtCoded, txtSecretKey) ) );
		}
	}
	else
	{
		txtPlaintext = txtCoded;
	} 
	return( txtPlaintext );
}

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

function setCookieForSecretKey( eleKey )
{
	// eleKey is the actual field with the key
	// http://www.w3schools.com/js/js_cookies.asp
	var strKey = eleKey.value;
	var strID = eleKey.id;
	document.cookie = strID + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC"; // Delete old cookie
	var intExdays = 3652.5; // Expires in ten years
	var dteExpiry = new Date();
	dteExpiry.setTime(dteExpiry.getTime() + (intExdays*24*60*60*1000));
	var strExpires = "expires="+ dteExpiry.toUTCString();
	document.cookie = strID + "=" + strKey + "; " + strExpires;
	return strID + "=" + strKey + "; " + strExpires;
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

function postVenue()
{
	pauseScreenOn();
	var request = $.post('<?=ROOTPATH?>/auth-venue-detail-post.php',
	{
		venueid: jQuery('#venueid').val(),
		locationName: jQuery('#locationName').val(),
		detailtype: 'venue',
		address: jQuery('#Address').val(),
		city: jQuery('#city').val(),
		province: jQuery('#province').val(),
		postalCode: jQuery('#postalCode').val(),
		icon: jQuery('#icon').val(),
		geolatitude: jQuery('#geolatitude').val(),
		geolongitude: jQuery('#geolongitude').val(),
		geolocationStyle: jQuery('#geolocationStyle').val(),
		phoneNumber: jQuery('#phoneNumber').val(),
		contact: jQuery('#contact').val(),
		email: jQuery('#email').val(),
		webSite: jQuery('#webSite').val(),
		extraNotes: jQuery('#extraNotes').val()
	},
        function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			pauseScreenOff();
			var objXMLdata = $.parseXML( data );
			jQuery('#venueid').val( $(objXMLdata).find('venue').find('idVenue').text() );
        });
	//console.log('Posted Venue.');
}

function postDelete( intNextVenue )
{
	pauseScreenOn();
	var request = $.post('<?=ROOTPATH?>/auth-venue-detail-post.php',
	{
		venueid: jQuery('#venueid').val(),
		locationName: jQuery('#locationName').val(),
		detailtype: 'deletevenue',
		delete: true
	},
        function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			pauseScreenOff();
			var objXMLdata = $.parseXML( data );
			jQuery('#venueid').val( $(objXMLdata).find('venue').find('idVenue').text() );
			window.location.href = '<?=ROOTPATH?>/auth-venue-edit.php?id='+ encodeURI(intNextVenue);
        });
	//console.log('Posted Venue.');
}

function postDeleteContact( intDetailId, strContactPerson )
{
	pauseScreenOn();
	var request = $.post('<?=ROOTPATH?>/auth-venue-detail-post.php',
	{
		venueid: jQuery('#venueid').val(),
		detailid: intDetailId,  
		detailtype: 'deletecontact',
		ContactPerson: strContactPerson,  
		delete: true
	},
        function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			pauseScreenOff();
			var objXMLdata = $.parseXML( data );
			jQuery('#venueid').val( $(objXMLdata).find('venue').find('idVenue').text() );
			postDisplay( data ); 
        });
	//console.log('Posted Venue.');
}

function postContact()
{
	pauseScreenOn();
	var request = $.post('<?=ROOTPATH?>/auth-venue-detail-post.php',
	{
		venueid: jQuery('#venueid').val(),
		locationName: jQuery('#locationName').val(),
		detailtype: 'contact',
		ContactComboBox: jQuery('#ContactComboBox').val(),
		ContactPerson: jQuery('#contactPerson').val(),
		ContactEvent: jQuery('#contactEvent').val(),
		ContactDate: jQuery('#contactDate').val(),
		ContactEncryptKey: jQuery('#contactEncryptKey').val(),
		ContactEncrypttext: jQuery('#contactEncrypttext').val()
	},
        function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			postDisplay( data ); 
			pauseScreenOff();
			jQuery('#ContactComboBox').val('');
			jQuery('#contactPerson').val('');
			jQuery('#contactEvent').val('');
			jQuery('#contactDate').val('');
			jQuery('#contactPlaintext').val('');
			jQuery('#contactEncryptKey').val('');
			jQuery('#chkKeyCookie').prop('checked','');
			jQuery('#contactEncrypttext').val('');
			displayContactDetailsFields('');
        });
	//console.log('Posted Contact.');

}

function postDeleteAmenity( intDetailId, strAmenitiesRoom )
{
	pauseScreenOn();
	var request = $.post('<?=ROOTPATH?>/auth-venue-detail-post.php',
	{
		venueid: jQuery('#venueid').val(),
		detailid: intDetailId,  
		detailtype: 'deleteamenity',
		AmenitiesRoom: strAmenitiesRoom,  
		delete: true,
		
	},
        function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			pauseScreenOff();
			var objXMLdata = $.parseXML( data );
			jQuery('#venueid').val( $(objXMLdata).find('venue').find('idVenue').text() );
			postDisplay( data ); 
        });
	//console.log('Posted Venue.');
}

function postAmenity()
{
	var data = new FormData($('input[name^="Amenities"],textarea[name="AmenitiesDescription"],input[name="venueid"],input[name="locationName"]'));   
		pauseScreenOn();
		jQuery.each($('select[name="AmenitiesComboBox"],'
				+'input[name="AmenitiesRoom"],'
				+'input[name="AmenitiesLength"],'
				+'input[name="AmenitiesWidth"],'
				+'input[name="AmenitiesHeight"],'
				+'input[name="AmenitiesElevation"],'
				+'textarea[name="AmenitiesDescription"],'
				+'input[name="venueid"],input[name="locationName"]'), function(i, field) {
			data.append(field.name, field.value);
	console.log( field );  
		});
			
		data.append('detailtype', 'amenity');

		jQuery.each($('input[name^="AmenitiesInfo"]'), function(i, field) {
			if( jQuery('input[name="'+ field.name +'"]').parent().css('display') != 'none' )
			{
	console.log( field );  
				data.append(field.name, jQuery('input[name="'+ field.name +'"]').prop('checked') ? jQuery('input[name="'+ field.name +'"]').val() : '' );
			}
		});
/*	var request = $.post('<?=ROOTPATH?>/auth-venue-detail-post.php',
	{
		venueid: jQuery('#venueid').val(),
		locationName: jQuery('#locationName').val(),
		AmenitiesComboBox: jQuery('#AmenitiesComboBox').val(),
		AmenitiesRoom: jQuery('#amenitiesRoom').val(),
		AmenitiesLength: jQuery('#amenitiesLength').val(),
		AmenitiesWidth: jQuery('#amenitiesWidth').val(),
		AmenitiesHeight: jQuery('#amenitiesHeight').val(),
		AmenitiesElevation: jQuery('#amenitiesElevation').val(),
		AmenitiesDescription: jQuery('#amenitiesDescription').val(),
		AmenitiesInfobigEvent: jQuery('#bigEvent').prop('checked') ? jQuery('#bigEvent').val() : '',
		AmenitiesInfosmallEvent: jQuery('#smallEvent').prop('checked') ? jQuery('#smallEvent').val() : '',
		AmenitiesInfocamping: jQuery('#camping').prop('checked') ? jQuery('#camping').val() : '',
		AmenitiesInfomeetings: jQuery('#meetings').prop('checked') ? jQuery('#meetings').val() : '',
		AmenitiesInfofightingIndoor: jQuery('#fightingIndoor').prop('checked') ? jQuery('#fightingIndoor').val() : '',
		AmenitiesInfofightingOutdoor: jQuery('#fightingOutdoor').prop('checked') ? jQuery('#fightingOutdoor').val() : '',
		AmenitiesInfofencingIndoor: jQuery('#fencingIndoor').prop('checked') ? jQuery('#fencingIndoor').val() : '',
		AmenitiesInfofencingOutdoor: jQuery('#fencingOutdoor').prop('checked') ? jQuery('#fencingOutdoor').val() : '',
		AmenitiesInfoarcheryIndoor: jQuery('#archeryIndoor').prop('checked') ? jQuery('#archeryIndoor').val() : '',
		AmenitiesInfoarcheryOutdoor: jQuery('#archeryOutdoor').prop('checked') ? jQuery('#archeryOutdoor').val() : '',
		AmenitiesInfomerchantIndoor: jQuery('#merchantIndoor').prop('checked') ? jQuery('#merchantIndoor').val() : '',
		AmenitiesInfomerchantOutdoor: jQuery('#merchantOutdoor').prop('checked') ? jQuery('#merchantOutdoor').val() : '',
		AmenitiesInfoclassesIndoor: jQuery('#classesIndoor').prop('checked') ? jQuery('#classesIndoor').val() : '',
		AmenitiesInfoclassesOutdoor: jQuery('#classesOutdoor').prop('checked') ? jQuery('#classesOutdoor').val() : '',
		AmenitiesInfospecialty: jQuery('#specialty').prop('checked') ? jQuery('#specialty').val() : '',
		AmenitiesInfokitchenstove: jQuery('#kitchenstove').prop('checked') ? jQuery('#kitchenstove').val() : '',
		AmenitiesInfokitchenoven: jQuery('#kitchenoven').prop('checked') ? jQuery('#kitchenoven').val() : '',
		AmenitiesInfokitchenwarmer: jQuery('#kitchenwarmer').prop('checked') ? jQuery('#kitchenwarmer').val() : '',
		AmenitiesInfokitchenfridge: jQuery('#kitchenfridge').prop('checked') ? jQuery('#kitchenfridge').val() : '',
		AmenitiesInfokitchenfreezer: jQuery('#kitchenfreezer').prop('checked') ? jQuery('#kitchenfreezer').val() : '',
		AmenitiesInfokitchendishwasher: jQuery('#kitchendishwasher').prop('checked') ? jQuery('#kitchendishwasher').val() : '',
		AmenitiesInfoelectricity: jQuery('#electricity').prop('checked') ? jQuery('#electricity').val() : '',
		AmenitiesInfowater: jQuery('#water').prop('checked') ? jQuery('#water').val() : '',
		AmenitiesInfobathroom: jQuery('#bathroom').prop('checked') ? jQuery('#bathroom').val() : '',
		AmenitiesInfochangeroom: jQuery('#changeroom').prop('checked') ? jQuery('#changeroom').val() : '',
		AmenitiesInfoshower: jQuery('#shower').prop('checked') ? jQuery('#shower').val() : '',
		AmenitiesInfoparkingonsite: jQuery('#parkingonsite').prop('checked') ? jQuery('#parkingonsite').val() : '',
		AmenitiesInfoparkingpaylot: jQuery('#parkingpaylot').prop('checked') ? jQuery('#parkingpaylot').val() : '',
		AmenitiesInfoparkingstreetfree: jQuery('#parkingstreetfree').prop('checked') ? jQuery('#parkingstreetfree').val() : '',
		AmenitiesInfoparkingstreetmetered: jQuery('#parkingstreetmetered').prop('checked') ? jQuery('#parkingstreetmetered').val() : '',
		AmenitiesInfotransitbus: jQuery('#transitbus').prop('checked') ? jQuery('#transitbus').val() : '',
		AmenitiesInfotransitsubway: jQuery('#transitsubway').prop('checked') ? jQuery('#transitsubway').val() : '',
		AmenitiesInfotransittrain: jQuery('#transittrain').prop('checked') ? jQuery('#transittrain').val() : '',
		AmenitiesInfoloadingzone: jQuery('#loadingzone').prop('checked') ? jQuery('#loadingzone').val() : '',
		AmenitiesInfoloadinginfront: jQuery('#loadinginfront').prop('checked') ? jQuery('#loadinginfront').val() : '',
		AmenitiesInfoloadingoutback: jQuery('#loadingoutback').prop('checked') ? jQuery('#loadingoutback').val() : '',
		AmenitiesInfowheelchairaccessible: jQuery('#wheelchairaccessible').prop('checked') ? jQuery('#wheelchairaccessible').val() : '',
		AmenitiesInfoaccessibleelevator: jQuery('#accessibleelevator').prop('checked') ? jQuery('#accessibleelevator').val() : '',
		AmenitiesInfoaccessibleramp: jQuery('#accessibleramp').prop('checked') ? jQuery('#accessibleramp').val() : '',
		AmenitiesInfoaccessiblenoStairs: jQuery('#accessiblenoStairs').prop('checked') ? jQuery('#accessiblenoStairs').val() : ''
	},
        function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			postDisplay( data ); 
        });
		*/
	//console.log('Posted Amenities.');
	var ppiFormMethod = 'POST';
	var ppiFormActionURL = '<?=ROOTPATH?>/auth-venue-detail-post.php';
	var request = $.ajax({
		type: ppiFormMethod,
		data: data,
		url: ppiFormActionURL,
		cache: false,
		contentType: false,
		processData: false,
		success: function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			postDisplay( data ); 
			pauseScreenOff();
			jQuery('#AmenitiesComboBox').val('');
			jQuery('#amenitiesRoom').val('');
			jQuery('#amenitiesLength').val('');
			jQuery('#amenitiesWidth').val('');
			jQuery('#amenitiesHeight').val('');
			jQuery('#amenitiesElevation').val('');
			jQuery('#amenitiesDescription').val('');
			jQuery('#bigEvent').prop('checked','');
			jQuery('#smallEvent').prop('checked','');
			jQuery('#camping').prop('checked','');
			jQuery('#meetings').prop('checked','');
			jQuery('#fightingIndoor').prop('checked','');
			jQuery('#fightingOutdoor').prop('checked','');
			jQuery('#fencingIndoor').prop('checked','');
			jQuery('#fencingOutdoor').prop('checked','');
			jQuery('#archeryIndoor').prop('checked','');
			jQuery('#archeryOutdoor').prop('checked','');
			jQuery('#merchantIndoor').prop('checked','');
			jQuery('#merchantOutdoor').prop('checked','');
			jQuery('#classesIndoor').prop('checked','');
			jQuery('#classesOutdoor').prop('checked','');
			jQuery('#specialty').prop('checked','');
			jQuery('#kitchenstove').prop('checked','');
			jQuery('#kitchenoven').prop('checked','');
			jQuery('#kitchenwarmer').prop('checked','');
			jQuery('#kitchenfridge').prop('checked','');
			jQuery('#kitchenfreezer').prop('checked','');
			jQuery('#kitchendishwasher').prop('checked','');
			jQuery('#electricity').prop('checked','');
			jQuery('#water').prop('checked','');
			jQuery('#bathroom').prop('checked','');
			jQuery('#changeroom').prop('checked','');
			jQuery('#shower').prop('checked','');
			jQuery('#parkingonsite').prop('checked','');
			jQuery('#parkingpaylot').prop('checked','');
			jQuery('#parkingstreetfree').prop('checked','');
			jQuery('#parkingstreetmetered').prop('checked','');
			jQuery('#transitbus').prop('checked','');
			jQuery('#transitsubway').prop('checked','');
			jQuery('#transittrain').prop('checked','');
			jQuery('#loadingzone').prop('checked','');
			jQuery('#loadinginfront').prop('checked','');
			jQuery('#loadingoutback').prop('checked','');
			jQuery('#wheelchairaccessible').prop('checked','');
			jQuery('#accessibleelevator').prop('checked','');
			jQuery('#accessibleramp').prop('checked','');
			jQuery('#accessiblenoStairs').prop('checked','');
			displayAmenityDetailsFields();
			}
        });
}

function postPhoto()
{
// http://stackoverflow.com/questions/5392344/sending-multipart-formdata-with-jquery-ajax.
/*
var opts = {
    url: 'php/upload.php',
    data: data,
    cache: false,
    contentType: false,
    processData: false,
    type: 'POST',
    success: function(data){
        alert(data);
    }
};
*/
/*
var data = new FormData($('input[name^="media"]'));     
jQuery.each($('input[name^="media"]')[0].files, function(i, file) {
    data.append(i, file);
});

$.ajax({
    type: ppiFormMethod,
    data: data,
    url: ppiFormActionURL,
    cache: false,
    contentType: false,
    processData: false,
    success: function(data){
        alert(data);
    }
});
*/

/*
	var request = $.post('<?=ROOTPATH?>/auth-venue-detail-post.php',
	{
		venueid: jQuery('#venueid').val(),
		locationName: jQuery('#locationName').val(),
		PhotoType: jQuery('#photoType').val(),
		PhotoSourceFile: jQuery('#photoSourceFile').val(),
		PhotoServerFile: jQuery('#photoServerFile').val(),
		PhotoDescription: jQuery('#photoDescription').val()
	},
        function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			postDisplay( data ); 
        });
	//console.log('Posted Photo.');
*/
	var data = new FormData($('input[name^="Photo"],input[name="venueid"],input[name="locationName"]'));   
	console.log( data );  
		pauseScreenOn();
		jQuery.each($('input[name="PhotoSourceFile"]')[0].files, function(i, file) {
			data.append('PhotoSourceFile', file);
//	console.log( i );  
//	console.log( file );  
		});

		data.append('detailtype', 'photo');

		jQuery.each($('input[name="PhotoType"],input[name="PhotoServerFile"],input[name="PhotoDescription"],input[name="venueid"],input[name="locationName"]'), function(i, field) {
			data.append(field.name, field.value);
//	console.log( field );  
//	console.log( i );  
//	console.log( field.name );  
//	console.log( field.value );  
		});
	var ppiFormMethod = 'POST';
	var ppiFormActionURL = '<?=ROOTPATH?>/auth-venue-detail-post.php';
	var request = $.ajax({
		type: ppiFormMethod,
		data: data,
		url: ppiFormActionURL,
		cache: false,
		contentType: false,
		processData: false,
		success: function(data,status){
//            console.log("Data: " + data + "\nStatus: " + status);
			postDisplay( data ); 
			$('#photoSourceFile').val('');
			$('#photoDescription').val('');
			pauseScreenOff();
			}
        });

}

function postDeletePhoto( intDetailId, strServerFile )
{
	pauseScreenOn();
	var request = $.post('<?=ROOTPATH?>/auth-venue-detail-post.php',
	{
		venueid: jQuery('#venueid').val(),
		detailid: intDetailId,  
		detailtype: 'deletephoto',
		PhotoServerFile: strServerFile,  
		delete: true
	},
        function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			pauseScreenOff();
			var objXMLdata = $.parseXML( data );
			jQuery('#venueid').val( $(objXMLdata).find('venue').find('idVenue').text() );
			postDisplay( data ); 
        });
	console.log('Delete Photo.');
}

function postDisplay( xmlData )
{
	var objXMLdata = $.parseXML( xmlData );
	var strContactHTML = ''; 
	var strAmenityHTML = ''; 
	var strPhotoHTML = ''; 
	$( objXMLdata ).find('venue').each(function()
	// Load up venue data, if any.
	{
	});
	$( objXMLdata ).find('detail').each(function()
	{
		switch( $(this).find('strDetailType').text() )
		{
		  case 'contact':
	// Load up on contact data, if there are any.
			switch( $(this).find('contact').find('strContactType').text() )
			{
			  case 'conversation':
				strContactHTML += '<em>Phone conversation</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strContactHTML += ' &nbsp; &nbsp; <span id="contact' 
						+ $(this).find('idDetail').text() + '" title="Delete this comment" '
						+ 'onclick="javascript:postDeleteContact('+ $(this).find('idDetail').text() + ', \''
						+ $(this).find('contact').find('strContactPerson').text() + '\')">&times;</span>';
				}
				strContactHTML += '<br />';
				break;
			  case 'event':
				strContactHTML += '<em>Event</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strContactHTML += ' &nbsp; &nbsp; <span id="contact' 
						+ $(this).find('idDetail').text() + '" title="Delete this comment" '
						+ 'onclick="javascript:postDeleteContact('+ $(this).find('idDetail').text() + ', \''
						+ $(this).find('contact').find('strContactPerson').text() + '\')">&times;</span>';
				}
				strContactHTML += '<br />';
				break;
			  case 'meeting':
				strContactHTML += '<em>On-site meeting</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strContactHTML += ' &nbsp; &nbsp; <span id="contact' 
						+ $(this).find('idDetail').text() + '" title="Delete this comment" '
						+ 'onclick="javascript:postDeleteContact('+ $(this).find('idDetail').text() + ', \''
						+ $(this).find('contact').find('strContactPerson').text() + '\')">&times;</span>';
				}
				strContactHTML += '<br />';
				break;
			  case 'financial':
				strContactHTML += '<em>Financial transaction</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strContactHTML += ' &nbsp; &nbsp; <span id="contact' 
						+ $(this).find('idDetail').text() + '" title="Delete this comment" '
						+ 'onclick="javascript:postDeleteContact('+ $(this).find('idDetail').text() + ', \''
						+ $(this).find('contact').find('strContactPerson').text() + '\')">&times;</span>';
				}
				strContactHTML += '<br />';
				break;
			  default:
				strContactHTML += '<em>' + escapeHtml($(this).find('contact').find('strContactType').text()) + '</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strContactHTML += ' &nbsp; &nbsp; <span id="contact' 
						+ $(this).find('idDetail').text() + '" title="Delete this comment" '
						+ 'onclick="javascript:postDeleteContact('+ $(this).find('idDetail').text() + ', \''
						+ $(this).find('contact').find('strContactPerson').text() + '\')">&times;</span>';
				}
				strContactHTML += '<br />';
				break;
			}
			strContactHTML += ( $(this).find('contact').find('strContactPerson').text().trim()!='' ? '&nbsp; Contact:&nbsp; '+ escapeHtml($(this).find('contact').find('strContactPerson').text()) + '<br />' : '' );
			strContactHTML += ( $(this).find('contact').find('strContactEvent').text().trim()!='' ? '&nbsp; Event:&nbsp; '+ escapeHtml($(this).find('contact').find('strContactEvent').text()) + '<br />' : '' );
			strContactHTML += ( $(this).find('contact').find('strContactDate').text().trim()!='' ? '&nbsp; Date:&nbsp; '+ escapeHtml($(this).find('contact').find('strContactDate').text()) + '<br />' : '' );
			
			// Display details. Check if the data has a decryption code in your cookies.
			var strSecretKey = getCookieForSecretKey('key'+ $(this).find('idDetail').text());
			if( strSecretKey == '' || decryptText(strSecretKey,$(this).find('contact').find('strContactText').text().trim())=='' )
			{
				strContactHTML += ( $(this).find('contact').find('strContactText').text().trim()!='' ? '&nbsp; &nbsp; <span id="msg'+ $(this).find('idDetail').text() + '" class="dont-break-out">'+ escapeLineBreak( escapeHtml($(this).find('contact').find('strContactText').text()) ) + '</span><span id="code'+ $(this).find('idDetail').text() + '" style="display:none">'+ escapeLineBreak( escapeHtml($(this).find('contact').find('strContactText').text()) ) + '</span><br />' : '' );
				strContactHTML += ( $(this).find('contact').find('strContactText').text().trim().search(/\s/i)==-1 ? '<label class="privacy" for="key'+ $(this).find('idDetail').text() + '" >Secret key</label> <input type="text" id="key'+ $(this).find('idDetail').text() + '" onkeyup="$(\'#msg'+ $(this).find('idDetail').text() +'\').html(decryptMessage(\'msg'+ $(this).find('idDetail').text() + '\',\'key'+ $(this).find('idDetail').text() + '\',\'code'+ $(this).find('idDetail').text() + '\'))"><br />' : '' );
			}
			else
			{
				strContactHTML += ( decryptText(strSecretKey,$(this).find('contact').find('strContactText').text().trim())!='' ? '&nbsp; &nbsp; <span id="msg'+ $(this).find('idDetail').text() + '">'+ escapeLineBreak( decryptText(strSecretKey,$(this).find('contact').find('strContactText').text()) ) + '</span><span id="code'+ $(this).find('idDetail').text() + '" style="display:none">'+ escapeLineBreak( escapeHtml($(this).find('contact').find('strContactText').text()) ) + '</span><br />' : '' );
			}
			strContactHTML += '<br />';
		  	break;
		  case 'amenity':
	// Load up on amenity data, if any.
			switch( $(this).find('amenity').find('strAmenitiesType').text() )
			{
			  case 'summary':
				strAmenityHTML += '<em>Amenity summary</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" '
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			  case 'room':
				strAmenityHTML += '<em>Room</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" '
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			  case 'gym':
				strAmenityHTML += '<em>Gymnasium</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" '
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			  case 'field':
				strAmenityHTML += '<em>Field</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" '
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			  case 'dais':
				strAmenityHTML += '<em>Dais or Stage</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" '
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			  case 'kitchen':
				strAmenityHTML += '<em>Kitchen or Bar</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" '
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			  case 'bathroom':
				strAmenityHTML += '<em>Bathroom or Changeroom</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" '
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			  case 'elevator':
				strAmenityHTML += '<em>Elevator</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" ' 
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			  case 'parking':
				strAmenityHTML += '<em>Parking and access</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" ' 
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			  default:
				strAmenityHTML += '<em>' + escapeHtml($(this).find('amenity').find('strAmenitiesType').text()) + '</em>';
				if( $(this).find('blnEditable').text() == '1' )
				{
					strAmenityHTML += ' &nbsp; &nbsp; <span id="amenity' 
					+ $(this).find('idDetail').text() + '" title="Delete this comment" '
					+ 'onclick="javascript:postDeleteAmenity('+ $(this).find('idDetail').text() + ', \''
					+ $(this).find('amenity').find('strAmenitiesRoom').text() + '\')">&times;</span>';
				}
				strAmenityHTML += '<br />';
				break;
			}
			strAmenityHTML += ( $(this).find('amenity').find('strAmenitiesRoom').text()!='' ? '&nbsp; Room:&nbsp; '+ escapeHtml($(this).find('amenity').find('strAmenitiesRoom').text()) + '<br />' : '' );
			strAmenityHTML += ( $(this).find('amenity').find('strAmenitiesLength').text()!='' ? '&nbsp; Length:&nbsp; '+ escapeHtml($(this).find('amenity').find('strAmenitiesLength').text()) + '<br />' : '' );
			strAmenityHTML += ( $(this).find('amenity').find('strAmenitiesWidth').text()!='' ? '&nbsp; Width:&nbsp; '+ escapeHtml($(this).find('amenity').find('strAmenitiesWidth').text()) + '<br />' : '' );
			strAmenityHTML += ( $(this).find('amenity').find('strAmenitiesHeight').text()!='' ? '&nbsp; Height:&nbsp; '+ escapeHtml($(this).find('amenity').find('strAmenitiesHeight').text()) + '<br />' : '' );
			strAmenityHTML += ( $(this).find('amenity').find('strAmenitiesElevation').text()!='' ? '&nbsp; Elevation:&nbsp; '+ escapeHtml($(this).find('amenity').find('strAmenitiesElevation').text()) + '<br />' : '' );
			strAmenityHTML += ( $(this).find('amenity').find('strAmenitiesDescription').text()!='' ? '&nbsp; &nbsp; '+ escapeLineBreak( escapeHtml($(this).find('amenity').find('strAmenitiesDescription').text()) ) + '<br />' : '' );
		  	strAmenityHTML += '<br />';
			$(this).find('amenity').children().each(function(){
//				alert($(this).prop('tagName').indexOf('strAmenitiesInfo') +''+ $(this).prop('tagName'));
				if($(this).prop('tagName').indexOf('strAmenitiesInfo')===0){
					strAmenityHTML += ( $(this).text()!='' ? '&nbsp; &nbsp; '+ escapeLineBreak( escapeHtml($(this).text()) ) + '<br />' : '' );}
			});
		  	strAmenityHTML += '<br />';
		  	break;
		  case 'photo':
	// Load up on photo data, if any.
			var imgFile = new Image();
			imgFile.src='data/'+ encodeURI($(this).find('photo').find('strPhotoServerFile').text()) +'';
			var intWidth = ( !Number.isNaN( parseInt($(this).find('photo').find('strPhotoWidth').text() ) ) ? parseInt($(this).find('photo').find('strPhotoWidth').text()) : (imgFile.naturalWidth>32?imgFile.naturalWidth:32) );
			var intHeight = ( !Number.isNaN( parseInt($(this).find('photo').find('strPhotoHeight').text() ) ) ? parseInt($(this).find('photo').find('strPhotoHeight').text()) : (imgFile.naturalHeight>24?imgFile.naturalHeight:24) );
			if( intWidth > 320 || intHeight > 240 ) 
			{ 
			  if( intWidth > 320 )
			  {
				console.log( intHeight = Math.ceil( intHeight / intWidth * 320 ) );
				intWidth = 320;
			  }
			  if( intHeight > 240 )
			  {
				console.log( intWidth = Math.ceil( intWidth / intHeight * 240 ) );
				intHeight = 240;
			  }
			}
			strPhotoHTML += '<!-- '+ escapeHtml($(this).find('photo').find('strPhotoType').text()) +' -->';
			strPhotoHTML += '<img class="photogallery" src="data/'+ encodeURI($(this).find('photo').find('strPhotoServerFile').text()) +'"';
			strPhotoHTML += ' width="' + intWidth +'"';
			strPhotoHTML += ' height="' + intHeight +'"';
			strPhotoHTML += ' alt="' + escapeHtml($(this).find('photo').find('strPhotoDescription').text()) +'" ';
			strPhotoHTML += ' title="' + escapeHtml($(this).find('photo').find('strPhotoDescription').text()) +'" />' + '';
			if( $(this).find('blnEditable').text() == '1' )
			{
				strPhotoHTML += ' &nbsp; &nbsp; <span id="photo' 
						+ $(this).find('idDetail').text() + '" title="Delete this photo" '
						+ 'onclick="javascript:postDeletePhoto('+ $(this).find('idDetail').text() + ', \''
						+ $(this).find('photo').find('strPhotoServerFile').text() + '\')">&times;</span>';
			}
			strPhotoHTML += '<br />';
			strPhotoHTML += ( $(this).find('photo').find('strPhotoSourceFile').text()!='' ? '&nbsp; &nbsp; '+ escapeHtml($(this).find('photo').find('strPhotoSourceFile').text()) + '<br />' : '' );
			strPhotoHTML += ( $(this).find('photo').find('strPhotoDescription').text()!='' ? '&nbsp; &nbsp; '+ escapeLineBreak( escapeHtml($(this).find('photo').find('strPhotoDescription').text()) ) + '<br />' : '' );
		  	strPhotoHTML += '<br />';
		  	break;
		  default:
		  	break;
			
		}
	});
	// Display collected info
	$('#detailEntryContacts').html(strContactHTML);
	$('#detailEntryAmenities').html(strAmenityHTML);
	$('#detailEntryPhotos').html(strPhotoHTML);
	pauseScreenOff();
}

function searchWindowShowAmenities()
{
	switch( $('#searchThisAmenity').val() )
	{
	case '':
		$('#searchAmenityCheckBoxes').css('display','none');
		break;
	case 'summary':
		$('#searchAmenityCheckBoxes').css('display','block');
		break;
	case 'room':
		$('#searchAmenityCheckBoxes').css('display','block');
		break;
	case 'gym':
		$('#searchAmenityCheckBoxes').css('display','block');
		break;
	case 'field':
		$('#searchAmenityCheckBoxes').css('display','block');
		break;
	case 'dais':
		$('#searchAmenityCheckBoxes').css('display','block');
		break;
	case 'kitchen':
		$('#searchAmenityCheckBoxes').css('display','block');
		break;
	case 'bathroom':
		$('#searchAmenityCheckBoxes').css('display','block');
		break;
	case 'elevator':
		$('#searchAmenityCheckBoxes').css('display','block');
		break;
	case 'parking':
		$('#searchAmenityCheckBoxes').css('display','block');
		break;
	default:
		$('#searchAmenityCheckBoxes').css('display','none');
		break;
	} 
	if( $('#searchHasAmenities').prop('checked') )
	{
		$('#searchAmenityCheckBoxes').css('display','block');
	} 
}

// Given a central location, do an extra Google search and then do the GetResults function.
function searchGetCoordinates()
{
  pauseScreenOn();
  var strAddress= '';
  jQuery.each($('input[name="searchDistanceFrom"]'), function(i, field) {
		if( $(this).prop('checked') ) 
		{
			if( field.value == 'map' ) 
			{
				var fltLat = objGoogleMap.getCenter().lat();
				var fltLng = objGoogleMap.getCenter().lng();
				searchWindowGetResults( fltLat, fltLng );
			}
			else
			{
			  strAddress= ''+ $('input[name="searchDistanceFromHere"]').val();
			  jQuery.getJSON( 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=' + ( $.trim( strAddress ) != '' ? encodeURIComponent( strAddress ) : 'Mississauga%2C+Ontario' ), function(oResult){
				if(oResult.status=="OK")
				{ 
					var fltLat = oResult.results[0].geometry.location.lat;
					var fltLng = oResult.results[0].geometry.location.lng;
					searchWindowGetResults( fltLat, fltLng );
				}
				else
				{
					// error on the JSON.  Report on screen.
					jQuery('#searchWindowResults').html('<strong>Results</strong><br />lookup '+ oResult.status);
					pauseScreenOff();
				} 
			  });	
			}
		}
	});
}

// given a point to centre on, pass parameters onto AJAX.
function searchWindowGetResults( fltLat, fltLng )
{ 
	pauseScreenOn();
	var data = new FormData();
	var fltZoom = objGoogleMap.getZoom();
	var fltLatDist = 0.7;
	var fltLngDist = 1.0;
	data.append('detailtype', 'search');
	if( $('input[name="searchVenueName"]').val() != '' ) {   
		data.append('searchVenueName', $('input[name="searchVenueName"]').val()); }
	jQuery.each($('input[name="searchDistance"]'), function(i, field) {
		if( $(this).prop('checked') ) 
		{
			if( field.value != 'map' ) 
			{
				fltLatDist = field.value / 110.54;
				fltLngDist = field.value / 111.320*Math.cos( Math.PI *fltLat / 180 );
			}
			else
			{
				fltLatDist = 360 / Math.pow( 2, fltZoom );
				fltLngDist = fltLatDist / Math.cos( Math.PI *fltLat / 180 );
			}
		}
	})
	
	var strIcon=';';$('#searchIcon option:selected').each(function() {strIcon+=$(this).val()+';';});
	data.append('icon', strIcon);
	data.append('latitude',fltLat);
	data.append('longitude',fltLng);
	data.append('zoomlevel',fltZoom);
	data.append('latitudedistance',fltLatDist);
	data.append('longitudedistance',fltLngDist);
	jQuery.each($('input[name="searchDistance"]'), function(i, field) {
		if( $(this).prop('checked') ) {
			data.append(field.name, field.value); }
	})
	if( $('input[name="searchHasContacts"]').prop('checked') ) {   
		data.append('searchHasContacts', $('input[name="searchHasContacts"]').val()); }
	if( $('input[name="searchThisContact"]').val() != '' ) {   
		data.append('searchThisContact', $('input[name="searchThisContact"]').val()); }

	if( $('input[name="searchHasAmenities"]').prop('checked') ) {   
		data.append('searchHasAmenities', $('input[name="searchHasAmenities"]').val()); }
	if( $('select[name="searchThisAmenity"]').val() != '' ) {   
		data.append('searchThisAmenity', $('select[name="searchThisAmenity"]').val()); }
	jQuery.each($('input[name^="searchAmenity"]'), function(i, field) {
		if( jQuery('input[name="'+ field.name +'"]').parent().css('display') != 'none' )
		{
			if( $('input[name="'+ field.name +'"]').prop('checked') ) {   
				data.append(field.name, jQuery('input[name="'+ field.name +'"]').prop('checked') ? jQuery('input[name="'+ field.name +'"]').val() : '' );
			}
		}
	});

	if( $('input[name="searchHasPhotos"]').prop('checked') ) {   
		data.append('searchHasPhotos', $('input[name="searchHasPhotos"]').text()); }

	// http://stackoverflow.com/questions/14344207/how-to-convert-distancemiles-to-degrees 
	// Latitude: 1 deg = 110.54 km
	// Longitude: 1 deg = 111.320*cos(latitude) km
	jQuery('#searchWindowResults').html('<strong>Results</strong><br />coming soon');
	
	/* 
	This is a very complex search.  Part of the calculation is in Javascript/jQuery.  Part by Google's server.  Part will be done in PHP.
	If the distance is from the centre, that is one less search.
	If the distance is from a city centre or from a postal code, jQuery has to request JSON data from Google.
	
	If neither distance nor centre is chosen, should I just default to the map centre and zoom level?
	
	All other filters will be performed in PHP.
	<div id="searchInfoWindow" class="infowindow" style="height: 80%; overflow-y: auto; display: block;"><img src="images/menu-icon-search.png" onclick="javascript:searchWindowGetResults();">
			<img src="images/menu-icon-close.png" onclick="javascript:jQuery('#searchInfoWindow').css('display','none');void(0);"><br><strong>Search window</strong><br>  <br>
			<label for="searchVenueName">Location Name</label>
			<input type="text" name="searchVenueName" value=""><br> <br>
			<input type="radio" name="searchDistanceFrom" value="city">		
			Distance from City <input type="text" name="searchDistanceFromHere" value=""><br> 
			<input type="radio" name="searchDistanceFrom" value="postal">
			Distance from postal code<br> 
			<input type="radio" name="searchDistanceFrom" value="map" checked="checked">
			Distance from Centre of map<br> <br>
			<input type="radio" name="searchDistance" value="5">5km 
			<input type="radio" name="searchDistance" value="10">10km 
			<input type="radio" name="searchDistance" value="15">15km 
			<input type="radio" name="searchDistance" value="20">20km <br>
			<input type="radio" name="searchDistance" value="25">25km 
			<input type="radio" name="searchDistance" value="50">50km 
			<input type="radio" name="searchDistance" value="75">75km 
			<input type="radio" name="searchDistance" value="100">100km<br> 
			<input type="radio" name="searchDistance" value="map" checked="checked">On the map <br>
			<input type="checkbox" name="searchHasContacts" value="">has contacts<br> 
			
				this contact <input type="text" name="searchThisContacts" value=""><br> 
			
			<input type="checkbox" name="searchHasAmenities" value="">has amenities<br> 
			
			<label for="searchThisAmenity">this amenity</label>
			<select id="AmenitiesSearchBox" name="searchThisAmenity">
				<option selected="selected"> -- Select type of amenity -- </option>
				<option value="summary">Amenity summary</option>
				<option value="room">Room</option>
				<option value="gym">Gymnasium</option>
				<option value="field">Field</option>
				<option value="dais">Dais or Stage</option>
				<option value="kitchen">Kitchen or Bar</option>
				<option value="bathroom">Bathroom or Changeroom</option>
				<option value="elevator">Elevator</option>
				<option value="parking">Parking and access</option>
			</select><br>
			<div id="searchAmenityCheckBoxes" style="display: block;">
				<label for="searchBigEvent">big event</label><input type="checkbox" id="searchBigEvent" name="searchBigEvent" value="big event"> 
				<label for="searchSmallEvent">small event</label><input type="checkbox" id="searchSmallEvent" name="searchSmallEvent" value="small event"> 
				<label for="searchCamping">camping</label><input type="checkbox" id="searchCamping" name="searchCamping" value="camping"> 
				<label for="searchMeetings">meetings</label><input type="checkbox" id="searchMeetings" name="searchMeetings" value="meetings"> 
				<label for="searchElectricity">electricity</label><input type="checkbox" id="searchElectricity" name="searchElectricity" value="electricity">
				<label for="searchWater">drinking water</label><input type="checkbox" id="searchWater" name="searchWater" value="water"> 
				<label for="searchBathroom">bathroom</label><input type="checkbox" id="searchBathroom" name="searchBathroom" value="bathroom">   
				<label for="searchChangeroom">change room</label><input type="checkbox" id="searchChangeroom" name="searchChangeroom" value="change room"> 
				<label for="searchShower">shower</label><input type="checkbox" id="searchShower" name="searchShower" value="shower"> 
				<label for="searchWheelchairaccessible" style="width: 200px">wheel-chair accessible</label><input type="checkbox" id="searchWheelchairaccessible" name="searchWheelchairaccessible" value="wheel-chair accessible"> 
				<label for="searchAccessibleelevator">elevator</label><input type="checkbox" id="searchAccessibleelevator" name="searchAccessibleelevator" value="elevator">
				<label for="searchAccessibleramp">ramp</label><input type="checkbox" id="searchAccessibleramp" name="searchAccessibleramp" value="ramp"> 
				<label for="searchAccessiblenoStairs">no stairs</label><input type="checkbox" id="searchAccessiblenoStairs" name="searchAccessiblenoStairs" value="no stairs">
				<h3 id="searchFightingHeader">fighting</h3>
				<label for="searchFightingIndoor">indoor</label><input type="checkbox" id="searchFightingIndoor" name="searchFightingIndoor" value="fighting indoor">    
				<label for="searchFightingOutdoor">outdoor</label><input type="checkbox" id="searchFightingOutdoor" name="searchFightingOutdoor" value="fighting outdoor">
				<h3 id="searchFencingHeader">fencing</h3>
				<label for="searchFencingIndoor">indoor</label><input type="checkbox" id="searchFencingIndoor" name="searchFencingIndoor" value="fencing indoor">    
				<label for="searchFencingOutdoor">outdoor</label><input type="checkbox" id="searchFencingOutdoor" name="searchFencingOutdoor" value="fencing outdoor">  
				<h3 id="searchArcheryHeader">archery</h3>
				<label for="searchArcheryIndoor">indoor</label><input type="checkbox" id="searchArcheryIndoor" name="searchArcheryIndoor" value="archery indoor">    
				<label for="searchArcheryOutdoor">outdoor</label><input type="checkbox" id="searchArcheryOutdoor" name="searchArcheryOutdoor" value="archery outdoor"> 
				<h3 id="searchClassesHeader">classes</h3>
				<label for="searchClassesIndoor">indoor</label><input type="checkbox" id="searchClassesIndoor" name="searchClassesIndoor" value="classes indoor">    
				<label for="searchClassesOutdoor">outdoor</label><input type="checkbox" id="searchClassesOutdoor" name="searchClassesOutdoor" value="classes outdoor"> 
				<label for="searchSpecialty" title="The venue has a unique quality, e.g. equestrian, paintball, tall-ship, etc.">specialty</label><input type="checkbox" id="searchSpecialty" name="searchSpecialty" value="specialty" title="The venue has a unique quality, e.g. equestrian, paintball, tall-ship, etc.">
				<h3 id="searchKitchenHeader">kitchen</h3>
				<label for="searchKitchenstove">stove</label><input type="checkbox" id="searchKitchenstove" name="searchKitchenstove" value="kitchenstove">
				<label for="searchKitchenoven">oven</label><input type="checkbox" id="searchKitchenoven" name="searchKitchenoven" value="kitchen oven">    
				<label for="searchKitchenwarmer">warmer</label><input type="checkbox" id="searchKitchenwarmer" name="searchKitchenwarmer" value="kitchen warmer">  
				<label for="searchKitchenfridge">fridge</label><input type="checkbox" id="searchKitchenfridge" name="searchKitchenfridge" value="kitchen fridge">    
				<label for="searchKitchenfreezer">freezer</label><input type="checkbox" id="searchKitchenfreezer" name="searchKitchenfreezer" value="kitchen freezer">    
				<label for="searchKitchendishwasher">dish washer</label><input type="checkbox" id="searchKitchendishwasher" name="searchKitchendishwasher" value="kitchen dish washer"> 
				<h3 id="searchParkingHeader">parking</h3>
				<label for="searchParkingonsite">on-site</label><input type="checkbox" id="searchParkingonsite" name="searchParkingonsite" value="on-site parking">    
				<label for="searchParkingpaylot">pay lot</label><input type="checkbox" id="searchParkingpaylot" name="searchParkingpaylot" value="pay lot parking"> 
				<label for="searchParkingstreetfree">street (free)</label><input type="checkbox" id="searchParkingstreetfree" name="searchParkingstreetfree" value="street parking (free)">    
				<label for="searchParkingstreetmetered">street (metered)</label><input type="checkbox" id="searchParkingstreetmetered" name="searchParkingstreetmetered" value="street parking (metered)"> 
				<h3 id="searchTransitHeader">transit</h3>
				<label for="searchTransitbus">bus</label><input type="checkbox" id="searchTransitbus" name="searchTransitbus" value="transit bus">    
				<label for="searchTransitsubway">subway</label><input type="checkbox" id="searchTransitsubway" name="searchTransitsubway" value="transit subway">    
				<label for="searchTransittrain">train</label><input type="checkbox" id="searchTransittrain" name="searchTransittrain" value="transit train">
				<label for="searchLoadingzone">loading zone</label><input type="checkbox" id="searchLoadingzone" name="searchLoadingzone" value="loading zone">    
				<label for="searchLoadinginfront">in front</label><input type="checkbox" id="searchLoadinginfront" name="searchLoadinginfront" value="loading in front">    
				<label for="searchLoadingoutback">out back</label><input type="checkbox" id="searchLoadingoutback" name="searchLoadingoutback" value="loading out back"> 
				  <br>&nbsp;  
			</div>
			
			<input type="checkbox" name="searchHasPhotos" value="">has photos <br> &nbsp;<br> 
			<div id="searchWindowResults"></div><br> &nbsp;
		</div>
	*/
	var ppiFormMethod = 'POST';
	var ppiFormActionURL = '<?=ROOTPATH?>/auth-venue-search-list.php';
	var request = $.ajax({
		type: ppiFormMethod,
		data: data,
		url: ppiFormActionURL,
		cache: false,
		contentType: false,
		processData: false,
		success: function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			//postDisplay( data ); 
			var objXMLdata = $.parseXML( data );
			var strSearchResults = '<strong>Results</strong><br />';
			$( objXMLdata ).find('searchFiles').find('searchList').each(function()
				{
					strSearchResults += '<a href="<?=ROOTPATH?>/auth-venue-edit.php?id='+ $(this).find('idVenue').text() +'">'+ $(this).find('strLocationName').text() +'</a><br />';
				});
			jQuery('#searchWindowResults').html(strSearchResults);
			pauseScreenOff();
			}
        });
	console.log('Posted search.');
}

// Given a central location, do an extra Google search and then do the GetResults function.
function printGetCoordinates()
{
  pauseScreenOn();
  var strAddress= '';
  jQuery.each($('input[name="searchDistanceFrom"]'), function(i, field) {
		if( $(this).prop('checked') ) 
		{
			if( field.value == 'map' ) 
			{
				var fltLat = objGoogleMap.getCenter().lat();
				var fltLng = objGoogleMap.getCenter().lng();
				printWindowGetResults( fltLat, fltLng );
			}
			else
			{
			  strAddress= ''+ $('input[name="searchDistanceFromHere"]').val();
			  jQuery.getJSON( 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=' + ( $.trim( strAddress ) != '' ? encodeURIComponent( strAddress ) : 'Mississauga%2C+Ontario' ), function(oResult){
				if(oResult.status=="OK")
				{ 
					var fltLat = oResult.results[0].geometry.location.lat;
					var fltLng = oResult.results[0].geometry.location.lng;
					printWindowGetResults( fltLat, fltLng );
				}
				else
				{
					// error on the JSON.  Report on screen.
					jQuery('#searchWindowResults').html('<strong>Results</strong><br />lookup '+ oResult.status);
					pauseScreenOff();
				} 
			  });	
			}
		}
	});
}

// given a point to centre on, pass parameters onto POST.
function printWindowGetResults( fltLat, fltLng )
{ 
	pauseScreenOn();
	//var data = new FormData();
	var data = new Object();
	var fltZoom = objGoogleMap.getZoom();
	var fltLatDist = 0.7;
	var fltLngDist = 1.0;

	data.detailtype = 'search';
	if( $('input[name="searchVenueName"]').val() != '' ) {   
		data.searchVenueName = $('input[name="searchVenueName"]').val(); }
	jQuery.each($('input[name="searchDistance"]'), function(i, field) {
		if( $(this).prop('checked') ) 
		{
			if( field.value != 'map' ) 
			{
				fltLatDist = field.value / 110.54;
				fltLngDist = field.value / 111.320*Math.cos( Math.PI *fltLat / 180 );
			}
			else
			{
				fltLatDist = 360 / Math.pow( 2, fltZoom );
				fltLngDist = fltLatDist / Math.cos( Math.PI *fltLat / 180 );
			}
		}
	})
	
	var strIcon=';';$('#searchIcon option:selected').each(function() {strIcon+=$(this).val()+';';});
	data.icon = strIcon;
	data.latitude = fltLat;
	data.longitude = fltLng;
	data.zoomlevel = fltZoom;
	data.latitudedistance = fltLatDist;
	data.longitudedistance = fltLngDist;
	jQuery.each($('input[name="searchDistance"]'), function(i, field) {
		if( $(this).prop('checked') ) {
			 data[field.name] = field.value; 
			}
	})
	if( $('input[name="searchHasContacts"]').prop('checked') ) {   
		data.searchHasContacts = $('input[name="searchHasContacts"]').val(); }
	if( $('input[name="searchThisContact"]').val() != '' ) {   
		data.searchThisContact = $('input[name="searchThisContact"]').val(); }

	if( $('input[name="searchHasAmenities"]').prop('checked') ) {   
		data.searchHasAmenities = $('input[name="searchHasAmenities"]').val(); }
	if( $('select[name="searchThisAmenity"]').val() != '' ) {   
		data.searchThisAmenity = $('select[name="searchThisAmenity"]').val(); }
	jQuery.each($('input[name^="searchAmenity"]'), function(i, field) {
		if( jQuery('input[name="'+ field.name +'"]').parent().css('display') != 'none' )
		{
			if( $('input[name="'+ field.name +'"]').prop('checked') ) {   
				data[field.name] = ( jQuery('input[name="'+ field.name +'"]').prop('checked') ? jQuery('input[name="'+ field.name +'"]').val() : '' );
			}
		}
	});

	if( $('input[name="searchHasPhotos"]').prop('checked') ) {   
		data.searchHasPhotos = $('input[name="searchHasPhotos"]').text(); }

	// http://stackoverflow.com/questions/24908686/jquery-open-page-in-a-new-tab-while-passing-post-data
	data.ts = Date.now();	
	data.cmd = 'nextPage';	
	/*
	data.append('detailtype', 'search');
	if( $('input[name="searchVenueName"]').val() != '' ) {   
		data.append('searchVenueName', $('input[name="searchVenueName"]').val()); }
	jQuery.each($('input[name="searchDistance"]'), function(i, field) {
		if( $(this).prop('checked') ) 
		{
			if( field.value != 'map' ) 
			{
				fltLatDist = field.value / 110.54;
				fltLngDist = field.value / 111.320*Math.cos( Math.PI *fltLat / 180 );
			}
			else
			{
				fltLatDist = 360 / Math.pow( 2, fltZoom );
				fltLngDist = fltLatDist / Math.cos( Math.PI *fltLat / 180 );
			}
		}
	})
	
	var strIcon=';';$('#searchIcon option:selected').each(function() {strIcon+=$(this).val()+';';});
	data.append('icon', strIcon);
	data.append('latitude',fltLat);
	data.append('longitude',fltLng);
	data.append('zoomlevel',fltZoom);
	data.append('latitudedistance',fltLatDist);
	data.append('longitudedistance',fltLngDist);
	jQuery.each($('input[name="searchDistance"]'), function(i, field) {
		if( $(this).prop('checked') ) {
			data.append(field.name, field.value); }
	})
	if( $('input[name="searchHasContacts"]').prop('checked') ) {   
		data.append('searchHasContacts', $('input[name="searchHasContacts"]').val()); }
	if( $('input[name="searchThisContact"]').val() != '' ) {   
		data.append('searchThisContact', $('input[name="searchThisContact"]').val()); }

	if( $('input[name="searchHasAmenities"]').prop('checked') ) {   
		data.append('searchHasAmenities', $('input[name="searchHasAmenities"]').val()); }
	if( $('select[name="searchThisAmenity"]').val() != '' ) {   
		data.append('searchThisAmenity', $('select[name="searchThisAmenity"]').val()); }
	jQuery.each($('input[name^="searchAmenity"]'), function(i, field) {
		if( jQuery('input[name="'+ field.name +'"]').parent().css('display') != 'none' )
		{
			if( $('input[name="'+ field.name +'"]').prop('checked') ) {   
				data.append(field.name, jQuery('input[name="'+ field.name +'"]').prop('checked') ? jQuery('input[name="'+ field.name +'"]').val() : '' );
			}
		}
	});

	if( $('input[name="searchHasPhotos"]').prop('checked') ) {   
		data.append('searchHasPhotos', $('input[name="searchHasPhotos"]').text()); }

	// http://stackoverflow.com/questions/24908686/jquery-open-page-in-a-new-tab-while-passing-post-data
	data.append('ts', Date.now());	
	data.append('cmd', 'nextPage');	
	*/
/*
	var request = $.post('<?=ROOTPATH?>/auth-venue-report.php',
		{
		 listdata: data,
		 ts: Date.now()
		},
		*/
/*
	var ppiFormMethod = 'POST';
	var ppiFormActionURL = '<?=ROOTPATH?>/auth-venue-report.php';
	var request = $.ajax({
		type: ppiFormMethod,
		data: data,
		url: ppiFormActionURL,
		cache: false,
		contentType: false,
		processData: false,
		success: function(data,status){
            console.log("Data: " + data + "\nStatus: " + status);
			window.open('<?=ROOTPATH?>/auth-venue-report.php');
			pauseScreenOff();
        });
	console.log('Printed search.');
		*/
	
	gotoUrl('<?=ROOTPATH?>/auth-venue-report.php', data, 'post');
}

	// http://stackoverflow.com/questions/2367594/open-url-while-passing-post-data-with-jquery
/**
 * Function that will redirect to a new page & pass data using submit
 * @param {type} path -> new url
 * @param {type} params -> JSON data to be posted
 * @param {type} method -> GET or POST
 * @returns {undefined} -> NA
 */
function gotoUrl(path, params, method) {
    //Null check
    method = method || "post"; // Set method to post by default if not specified.

    // The rest of this code assumes you are not using a library.
    // It can be made less wordy if you use one.
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);

    //Fill the hidden form
    if (typeof params === 'string') {
        var hiddenField = document.createElement("input");
        hiddenField.setAttribute("type", "hidden");
        hiddenField.setAttribute("name", 'data');
        hiddenField.setAttribute("value", params);
        form.appendChild(hiddenField);
    }
    else {
        for (var key in params) {
            if (params.hasOwnProperty(key)) {
                var hiddenField = document.createElement("input");
                hiddenField.setAttribute("type", "hidden");
                hiddenField.setAttribute("name", key);
                if(typeof params[key] === 'object'){
                    hiddenField.setAttribute("value", JSON.stringify(params[key]));
                }
                else{
                    hiddenField.setAttribute("value", params[key]);
                }
                form.appendChild(hiddenField);
            }
        }
    }

    document.body.appendChild(form);
    form.submit();
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

function pauseScreenOn() 
{
	$('#pauseLoading').attr('src', imgPause[ Math.floor( 9*Math.random() ) ].src);
	$('#pauseScreen').css('display','block'); 
}
function pauseScreenOff() {	$('#pauseScreen').css('display','none'); }
function pauseScreensLoad()
{
	imgPause[0] = new Image();
	imgPause[0].src = 'images/medieval-castle-gif.gif';
	imgPause[1] = new Image();
	imgPause[1].src = 'images/mounted-knight-walking.gif';
	imgPause[2] = new Image();
	imgPause[2].src = 'images/sparkle-book.gif'; 
	imgPause[3] = new Image();
	imgPause[3].src = 'images/tristan-and-isolde.gif';
	imgPause[4] = new Image();
	imgPause[4].src = 'images/the-king.gif';
	imgPause[5] = new Image();
	imgPause[5].src = 'images/loading8pt400.gif';
	imgPause[6] = new Image();
	imgPause[6].src = 'images/loading12pt400.gif';
	imgPause[7] = new Image();
	imgPause[7].src = 'images/loadingMoonPhase400.gif';
	imgPause[8] = new Image();
	imgPause[8].src = 'images/loadingRoundabout400.gif';
	
}

function pgResizeImage( imgFile, intImgHeight, intImgWidth ) {
	//imgFile.src='data/'+ encodeURI($(this).find('photo').find('strPhotoServerFile').text()) +'';
	var intWidth = ( !Number.isNaN( parseInt(intImgWidth) ) ? parseInt(intImgWidth) : (imgFile.naturalWidth>32?imgFile.naturalWidth:32) );
	var intHeight = ( !Number.isNaN( parseInt(intImgHeight ) ) ? parseInt(intImgHeight) : (imgFile.naturalHeight>24?imgFile.naturalHeight:24) );
	if( intWidth > 320 || intHeight > 240 ) 
	{ 
	  if( intWidth > 320 )
	  {
		console.log( intHeight = Math.ceil( intHeight / intWidth * 320 ) );
		intWidth = 320;
	  }
	  if( intHeight > 240 )
	  {
		console.log( intWidth = Math.ceil( intWidth / intHeight * 240 ) );
		intHeight = 240;
	  }
	}
}
// -->
</script>
</head>

<body>
<?php

if( $blnSignedIn !== true )
{

?>
<p>You are not signed in</p>
<form action="php-view-post.php" method="post" name="venueEntry" target="_self" >
  <div id="signin">
	<div id="divUserName">
		<label for="userName">Name of user</label>
		<input type="text" name="userName" id="UserName" />
	</div>
	<div id="divPassword">
		<label for="password">Password</label>
		<input type="password" textarea name="password" id="Password" />
	</div>
  </div>
  <div id="formButtons"> &nbsp; 
    <input name="submit" type="submit" value="Submit">  &nbsp;
     &nbsp;
	<input name="reset" type="reset" value="Reset">
	  &nbsp; </div>
</form>
<?php

}
else  // if( $blnSignedIn === true )
{

?>
<div class="wall"><h1>Event Venue Editor</h1></div>

<form action="auth-view-post.php" enctype="multipart/form-data" method="post" name="venueEntry" target="_self">
  <div id="address">
	<div id="divLocationName">
		<a name"topOfForm"></a><label for="locationName">Name of location</label> 
		<input type="text" name="locationName" id="locationName"<?= !$blnValidPost && isset($_POST['locationName']) ? ' value="'. $_POST['locationName'] .'"': ' value="'. $arrVenueList[1]['strLocationName'] .'"' ?>>
	</div>
	<div id="divAddress">
		<label for="address">Address</label>
		<textarea name="address" id="Address"><?= !$blnValidPost && isset($_POST['address']) ?  $_POST['address'] : $arrVenueList[1]['strAddress']?></textarea>
	</div>
	<div id="divCity">
		<label for="city">City</label>
		<input type="text" name="city" id="city"<?= !$blnValidPost && isset($_POST['city']) ? ' value="'. $_POST['city'] .'"': ' value="'. $arrVenueList[1]['strCity'] .'"' ?>>
	</div>
	<div id="divProvince">
		<label for="province">Province</label>
		<input type="text" name="province" id="province"<?= !$blnValidPost && isset($_POST['province']) ? ' value="'. $_POST['province'] .'"': ' value="'. $arrVenueList[1]['strProvince'] .'"' ?>>
	</div>
	<div id="divPostalCode">
		<label for="postalCode">Postal Code</label>
		<input type="text" name="postalCode" id="postalCode"<?= !$blnValidPost && isset($_POST['postalCode']) ? ' value="'. $_POST['postalCode'] .'"': ' value="'. $arrVenueList[1]['strPostalCode'] .'"' ?>>
	</div>
  </div>
  <div id="offsitemaps">
    <!-- div id="divLocationStyle">
	  <label for="locationStyle">Location Type</label>
	  <input type="radio" name="geolocationStyle" value="point" checked> point &nbsp;
	  <input type="radio" name="geolocationStyle" value="path"> path &nbsp; 
	  <input type="radio" name="geolocationStyle" value="area"> area
    </div  -->
	<div id="locationIcon">
		<label for="icon">Location Icon</label>
		<select id="icon" name="icon">
			<option value="" >Default</option>
			<option value="archery" <?=$strIcon=='archery'?' selected':''?>>Archery</option>
			<option value="pirates" <?=$strIcon=='pirates'?' selected':''?>>Arrrr!</option>
			<option value="battlefield" <?=$strIcon=='battlefield'?' selected':''?>>Battlefield</option>
			<option value="fairgrounds" <?=$strIcon=='fairgrounds'?' selected':''?>>Fairgrounds</option>
			<option value="park" <?=$strIcon=='park'?' selected':''?>>Park</option>
			<option value="parkshelter" <?=$strIcon=='parkshelter'?' selected':''?>>Picnic shelter</option>
			<option value="arena" <?=$strIcon=='arena'?' selected':''?>>Hockey arena</option>
			<option value="soccer" <?=$strIcon=='soccer'?' selected':''?>>Soccer field</option>
			<option value="usfootball" <?=$strIcon=='usfootball'?' selected':''?>>Football field</option>
			<option value="baseball" <?=$strIcon=='baseball'?' selected':''?>>Baseball field</option>
			<option value="summercamp" <?=$strIcon=='summercamp'?' selected':''?>>Campground</option>
			<option value="shootingrange" <?=$strIcon=='shootingrange'?' selected':''?>>Range</option>
			<option value="conference" <?=$strIcon=='conference'?' selected':''?>>Conference centre</option>
			<option value="statue" <?=$strIcon=='statue'?' selected':''?>>Point of Interest</option>
			<option value="church" <?=$strIcon=='church'?' selected':''?>>Church</option>
			<option value="school" <?=$strIcon=='school'?' selected':''?>>School</option>
			<option value="townhall" <?=$strIcon=='townhall'?' selected':''?>>Town hall</option>
			<option value="communitycentre" <?=$strIcon=='communitycentre'?' selected':''?>>Community centre</option>
			<option value="tower" <?=$strIcon=='tower'?' selected':''?>>Tower</option>
			<option value="palace" <?=$strIcon=='palace'?' selected':''?>>Castle</option>
			<option value="citywalls" <?=$strIcon=='citywalls'?' selected':''?>>City Walls</option>
			<option value="dance_class" <?=$strIcon=='dance_class'?' selected':''?>>Dancing</option>
			<option value="fishing" <?=$strIcon=='fishing'?' selected':''?>>Fish and game</option>
			<option value="grocery" <?=$strIcon=='grocery'?' selected':''?>>Groceries</option>
			<option value="fuel" <?=$strIcon=='fuel'?' selected':''?>>Gas station</option>
			<option value="mall" <?=$strIcon=='mall'?' selected':''?>>Mall</option>
		</select>
		<img src="images/menu-icon-help.gif" height="18px" width="18px" title="Select the icon best suited for this venue.  (Click for more)" id="locationIconInfo" onClick="javascript:jQuery('#locationIconInfoWindow').css('display','block');void(0);" />
	</div>
    <div id="divLatitude">
	  <label for="latitude">Latitude</label>
	  <input name="latitude" id="latitude" type="text" value="<?= !$blnValidPost && isset($_POST['geolatitude']) ? $_POST['geolatitude']:  $arrVenueList[1]['strGeoLatitude'] ?>">
	  <input name="geolatitude" id="geolatitude" type="hidden" value="<?= !$blnValidPost && isset($_POST['geolatitude']) ? $_POST['geolatitude']: $arrVenueList[1]['strGeoLatitude'] ?>">
    </div>
    <div id="divLongitude">
	  <label for="longitude">Longitude</label>
	  <input name="longitude" id="longitude" type="text" value="<?= !$blnValidPost && isset($_POST['geolongitude']) ? $_POST['geolongitude']:  $arrVenueList[1]['strGeoLongitude'] ?>">
	  <input name="geolongitude" id="geolongitude" type="hidden" value="<?= !$blnValidPost && isset($_POST['geolongitude']) ? $_POST['geolongitude']:  $arrVenueList[1]['strGeoLongitude'] ?>">
    </div>
    <div id="divMapLock">
      <label for="mapLock">Lock map location?</label>
      <input name="mapLock" id="mapLock" type="checkbox" value="maplock">
    </div>
    <div id="googleMap"></div>
  </div>
  <div id="siteNotes">
	<div id="divPhoneNumber">
		<label for="phoneNumber">Phone Number</label>
		<input type="text" name="phoneNumber" id="phoneNumber"<?= !$blnValidPost && isset($_POST['phoneNumber']) ? ' value="'. $_POST['phoneNumber'] .'"': ' value="'. $arrVenueList[1]['strPhoneNumber'] .'"' ?>></div>
	<div id="divContact">
		<label for="contact">Contact</label>
		<input type="text" name="contact" id="contact"<?= !$blnValidPost && isset($_POST['contact']) ? ' value="'. $_POST['contact'] .'"': ' value="'. $arrVenueList[1]['strContact'] .'"' ?>></div>
	<div id="divEmail">
		<label for="email">Email</label>
		<input type="text" name="email" id="email"<?= !$blnValidPost && isset($_POST['email']) ? ' value="'. $_POST['email'] .'"':  ' value="'. $arrVenueList[1]['strEmail'] .'"' ?>></div>
	<div id="divWebSite">
		<label for="webSite">Web site</label>
		<input type="text" name="webSite" id="webSite"<?= !$blnValidPost && isset($_POST['webSite']) ? ' value="'. $_POST['webSite'] .'"': ' value="'. $arrVenueList[1]['strWebSite'] .'"' ?>></div>
	<div id="divExtraNotes">
		<label for="extraNotes">Extra notes</label>
		<textarea name="extraNotes" cols="30" rows="5" id="extraNotes"><?= !$blnValidPost && isset($_POST['extraNotes']) ? $_POST['extraNotes'] : $arrVenueList[1]['txtExtraNotes'] ?></textarea></div>
  </div>
  <div id="formButtons"> &nbsp; 
    <input name="submit" id="btnVenue" type="button" value="Save" <?=$arrVenueList[1]['idVenue'] != '-1' && $arrVenueList[1]['blnEditable'] != '1' ? ' disabled' : ''?>>  &nbsp;
     &nbsp;
	<input name="reset" id="btnReset" type="reset" value="Reset">
	  &nbsp;
	  <?php
	  echo '<input name="venueid" id="venueid" type="hidden" value="'. $arrVenueList[1]['idVenue'] .'">'; 
	  ?></div>
<!--  **************************  -->
  <div id="navButtons">&nbsp;
    <input name="New" id="btnNew" type="button" value="New" title="Start with a new entry"> &nbsp; &nbsp; 
    <input name="Previous" id="btnPrevious" type="button" value="Prev" title="Go to previous entry"> &nbsp; &nbsp; 
    <input name="Delete" id="btnDelete" type="button" value="Del" title="Delete this entry and get the next one" <?= $arrVenueList[1]['blnEditable'] != '1' ? ' disabled' : ''?>> &nbsp; &nbsp; 
    <input name="Next" id="btnNext" type="button" value="Next" title="Go to next entry"> &nbsp; &nbsp; 
    <input name="Search" id="btnSearch" type="button" value="Search" title="Go to search screen" onClick="javascript:jQuery('#searchInfoWindow').css('display','block');void(0);">
  &nbsp; <br></div>
		<div id="ContactEntryWindow" class="infowindow" style="width:80%; left: 10%"><img src="images/menu-icon-copypaste.jpg" onClick="javascript:jQuery('#contactPlaintext').html(jQuery('#ConversationPreFilled').val());jQuery('#ContactEntryWindow').css('display','none');void(0);" />
		<img src="images/menu-icon-close.png" onClick="javascript:jQuery('#ContactEntryWindow').css('display','none');void(0);" /><br />
		<textarea id="ConversationPreFilled" cols="90" rows="25" >Event Site Form
Researched by _______________________________________  Date ________________________

Site Name ___________________________________________  Phone _______________________

Address ____________________________________________________________________________

     Contact(s)	______________________________       _______________________________
     Position(s) _____________________________       _______________________________
     Phone(s)	______________________________       _______________________________

Cost/Time ___________________________________________  Deposit _____________________

Additional Costs/Fees ______________________________________________________________

Reserve When? ______________________________________________________________________

Restrictions on Reservations _______________________________________________________

Special Considerations/Problems ____________________________________________________
____________________________________________________________________________________

Alcohol Permitted? ______  Restrictions ____________________________________________

Candles Permitted? ______  Restrictions ____________________________________________

Parking ____________________________________________________________________________

Public Transit _____________________________________________________________________

Handicap Accessibility (incl. restrooms) ___________________________________________

Main Hall ___________________________________________  Capacity ____________________
_____________________________________________________  Tables (#/size) _____________
_____________________________________________________  Chairs (#) __________________
_____________________________________________________  Stage? ______________________

Kitchen _____________________________________________  Ovens (#) ___________________
_____________________________________________________  Burners (#) _________________
_____________________________________________________  Serving Pieces? _____________

Restrooms __________________________________________________________________________

Changing Areas _____________________________________________________________________

Extra Rooms/Spaces _________________________________________________________________
____________________________________________________________________________________
____________________________________________________________________________________

Outdoor Areas/Martial Activities ___________________________________________________
____________________________________________________________________________________
____________________________________________________________________________________

Comments ___________________________________________________________________________
____________________________________________________________________________________

Previous Events/Autocrats __________________________________________________________
____________________________________________________________________________________
</textarea>
		</div>
	

		<div id="ContactSurveyWindow" class="infowindow" style="width:80%; left: 10%"><img src="images/menu-icon-copypaste.jpg" onClick="javascript:jQuery('#contactPlaintext').html(jQuery('#SurveyPreFilled').val());jQuery('#ContactSurveyWindow').css('display','none');void(0);" />
		<img src="images/menu-icon-close.png" onClick="javascript:jQuery('#ContactSurveyWindow').css('display','none');void(0);" /><br />
		<textarea id="SurveyPreFilled" cols="90" rows="25" >1. What is the site name? 
2. Please provide site address and contact information
3. Cost of Site, including extras
4. Maximum Occupancy
5. When was this site last used?
6. Does The Site Have The Following:
Disabled Access
Area for Armoured Combat
Area for Archery/Thrown Weapons
Area for Rapier Combat
Drop Off/Pick Up Area
Change rooms
Outdoor Play Area for Children
Adequate Parking
Showers
A Stage or other Platform for Court
Separate Rooms for Royalty
Other (please specify)
7. Bar type
Event supplies drinks and permit
Event pays for site bartender
Site has own bartender
Wet site/camping area
Dry site/No bar permitted
Other (please specify)
8. Kitchen Type
Large Professional Kitchen
Medium Professional Kitchen
Small Professional Kitchen
Limited Kitchen
No Kitchen
Please describe kitchen equipment including dishwasher, ovens, warmers and cold storage options.
9. Type of event site is suitable for:
Large Kingdom Level Event
Small Kingdom Level Event
Crown Tournament
Coronation
Kingdom A&S
Special Themed Event 
Outdoor Oriented Event
Small Local Event
Other (please specify)
10. Please provide your contact information for follow up if needed.
Name 
Email Address
</textarea>
		</div>
		<div id="contactInfoWindow" class="infowindow" onClick="javascript:jQuery('#contactInfoWindow').css('display','none');void(0);">Select the type of conversation first.&nbsp; Type the conversation details and summary in the <strong>Conversation</strong>&nbsp;field.&nbsp; If the conversation details are supposed to be confidential, enter a password phrase in the <strong>Secret key</strong>&nbsp;field.&nbsp; The text will be converted to AES encrypted data and stored.&nbsp; When you come back to the record, enter the password phrase to retrieve the original text.&nbsp; If you forget the password phrase, don&#x27;t; the password phrase is not stored on the server or anywhere.&nbsp; Select <strong>Cookie</strong>&nbsp;to save the passphrase to your computer.&nbsp; It will automatically decrypt your saved data.&nbsp; Remember not to delete your cookies.<br />The information is stored here so that you can share it securely with some and not others, but not let it appear accidentally on any reports. </div>
		<div id="amenitiesInfoWindow" class="infowindow" onClick="javascript:jQuery('#amenitiesInfoWindow').css('display','none');void(0);">Select the type of amenity first.&nbsp; Choose a good <strong>Room name</strong>.&nbsp; If the conversation details are supposed to be confidential, enter a password phrase in the <strong>Length</strong>, <strong>Width</strong>, and <strong>
Height</strong>&nbsp;fields.&nbsp; A field has unlimited height.&nbsp; A stage or a dais has some <strong>
Elevation</strong>&nbsp; above the floor of the room.&nbsp; Add any useful <strong>Description</strong> .<br />The information is stored here so that you can share it with others.&nbsp;  It will appear on reports. </div>
		<div id="searchInfoWindow" class="infowindow" style="height:80%; overflow-y:auto" ><img src="images/menu-icon-search.png" onClick="javascript:if($('#searchDistanceFromHere').val()!=''){searchGetCoordinates();}else{searchWindowGetResults(objGoogleMap.getCenter().lat(),objGoogleMap.getCenter().lng());}" /> &nbsp;
			<img src="images/menu-icon-print.jpg" onClick="javascript:if($('#searchDistanceFromHere').val()!=''){printGetCoordinates();}else{printWindowGetResults(objGoogleMap.getCenter().lat(),objGoogleMap.getCenter().lng());}" /> &nbsp;
			<img src="images/menu-icon-close.png" onClick="javascript:jQuery('#searchInfoWindow').css('display','none');void(0);" /><br /><strong>Search window</strong><br />  <br /> 
			<div id="searchWindowResults"></div><br /> 
			<label for="searchVenueName">Location Name</label>
			<input type="text" name="searchVenueName" value="" /><br /> <br />
			<input type="radio" name="searchDistanceFrom" value="city" />		
			Distance from City <input type="text" id="searchDistanceFromHere" name="searchDistanceFromHere" value="" /><br /> 
			<input type="radio" name="searchDistanceFrom" value="postal" />
			Distance from postal code<br /> 
			<input type="radio" name="searchDistanceFrom" value="map" checked="checked" />
			Distance from Centre of map<br /> <br />
			<input type="radio" name="searchDistance" value="5" />5km 
			<input type="radio" name="searchDistance" value="10" />10km 
			<input type="radio" name="searchDistance" value="15" />15km <br />
			<input type="radio" name="searchDistance" value="20" />20km 
			<input type="radio" name="searchDistance" value="25" />25km 
			<input type="radio" name="searchDistance" value="50" />50km <br />
			<input type="radio" name="searchDistance" value="75" />75km 
			<input type="radio" name="searchDistance" value="100" />100km<br /> 
			<input type="radio" name="searchDistance" value="map" checked="checked" />On the map <br />
			<label for="searchIcon">Location Icon</label>
			<select id="searchIcon" name="searchIcon" size="4" multiple="multiple">
				<option value="" selected="selected">-- Any --</option>
				<option value="archery">Archery</option>
				<option value="pirates">Arrrr!</option>
				<option value="battlefield">Battlefield</option>
				<option value="fairgrounds">Fairgrounds</option>
				<option value="park">Park</option>
				<option value="parkshelter">Picnic shelter</option>
				<option value="arena">Hockey arena</option>
				<option value="soccer">Soccer field</option>
				<option value="usfootball">Football field</option>
				<option value="baseball">Baseball field</option>
				<option value="summercamp">Campground</option>
				<option value="shootingrange">Range</option>
				<option value="conference">Conference centre</option>
				<option value="statue">Point of Interest</option>
				<option value="church">Church</option>
				<option value="school">School</option>
				<option value="townhall">Town hall</option>
				<option value="communitycentre">Community centre</option>
				<option value="tower">Tower</option>
				<option value="palace">Castle</option>
				<option value="citywalls">City Walls</option>
				<option value="dance_class">Dancing</option>
				<option value="fishing">Fish and game</option>
				<option value="grocery">Groceries</option>
				<option value="fuel">Gas station</option>
				<option value="mall">Mall</option>
			</select><br />
			<input type="checkbox" name="searchHasContacts" value="has contacts" />has contacts<br /> 
			
				this contact <input type="text" name="searchThisContact" value="" /><br /> 
			
			<input type="checkbox" id="searchHasAmenities" name="searchHasAmenities" value="has amenities" onChange="javascript:searchWindowShowAmenities();" />has amenities<br /> 
			
			<label for="searchThisAmenity">this amenity</label>
			<select id="searchThisAmenity" name="searchThisAmenity" onChange="javascript:searchWindowShowAmenities();">
				<option value="" selected="selected"> -- Select type of amenity -- </option>
				<option value="summary">Amenity summary</option>
				<option value="room">Room</option>
				<option value="gym">Gymnasium</option>
				<option value="field">Field</option>
				<option value="dais">Dais or Stage</option>
				<option value="kitchen">Kitchen or Bar</option>
				<option value="bathroom">Bathroom or Changeroom</option>
				<option value="elevator">Elevator</option>
				<option value="parking">Parking and access</option>
			</select><br />
			<div id="searchAmenityCheckBoxes" style="display:none">
				<label for="searchAmenityBigEvent">big event</label><input type="checkbox" id="searchAmenityBigEvent" name="searchAmenityBigEvent" value="big event"> 
				<label for="searchAmenitySmallEvent">small event</label><input type="checkbox" id="searchAmenitySmallEvent" name="searchAmenitySmallEvent" value="small event"> 
				<label for="searchAmenityCamping">camping</label><input type="checkbox" id="searchAmenityCamping" name="searchAmenityCamping" value="camping"> 
				<label for="searchAmenityMeetings">meetings</label><input type="checkbox" id="searchAmenityMeetings" name="searchAmenityMeetings" value="meetings"> 
				<label for="searchAmenityElectricity">electricity</label><input type="checkbox" id="searchAmenityElectricity" name="searchAmenityElectricity" value="electricity">
				<label for="searchAmenityWater">drinking water</label><input type="checkbox" id="searchAmenityWater" name="searchAmenityWater" value="water"> 
				<label for="searchAmenityBathroom">bathroom</label><input type="checkbox" id="searchAmenityBathroom" name="searchAmenityBathroom" value="bathroom">   
				<label for="searchAmenityChangeroom">change room</label><input type="checkbox" id="searchAmenityChangeroom" name="searchAmenityChangeroom" value="change room"> 
				<label for="searchAmenityShower">shower</label><input type="checkbox" id="searchAmenityShower" name="searchAmenityShower" value="shower"> 
				<label for="searchAmenityWheelchairaccessible" style="width: 200px">wheel-chair accessible</label><input type="checkbox" id="searchAmenityWheelchairaccessible" name="searchAmenityWheelchairaccessible" value="wheel-chair accessible"> 
				<label for="searchAmenityAccessibleelevator">elevator</label><input type="checkbox" id="searchAmenityAccessibleelevator" name="searchAmenityAccessibleelevator" value="elevator">
				<label for="searchAmenityAccessibleramp">ramp</label><input type="checkbox" id="searchAmenityAccessibleramp" name="searchAmenityAccessibleramp" value="ramp"> 
				<label for="searchAmenityAccessiblenoStairs">no stairs</label><input type="checkbox" id="searchAmenityAccessiblenoStairs" name="searchAmenityAccessiblenoStairs" value="no stairs">
				<h3 id="searchAmenityFightingHeader">fighting</h3>
				<label for="searchAmenityFightingIndoor">indoor</label><input type="checkbox" id="searchAmenityFightingIndoor" name="searchAmenityFightingIndoor" value="fighting indoor">    
				<label for="searchAmenityFightingOutdoor">outdoor</label><input type="checkbox" id="searchAmenityFightingOutdoor" name="searchAmenityFightingOutdoor" value="fighting outdoor">
				<h3 id="searchAmenityFencingHeader">fencing</h3>
				<label for="searchAmenityFencingIndoor">indoor</label><input type="checkbox" id="searchAmenityFencingIndoor" name="searchAmenityFencingIndoor" value="fencing indoor">    
				<label for="searchAmenityFencingOutdoor">outdoor</label><input type="checkbox" id="searchAmenityFencingOutdoor" name="searchAmenityFencingOutdoor"value="fencing outdoor">  
				<h3 id="searchAmenityArcheryHeader">archery</h3>
				<label for="searchAmenityArcheryIndoor">indoor</label><input type="checkbox" id="searchAmenityArcheryIndoor" name="searchAmenityArcheryIndoor" value="archery indoor">    
				<label for="searchAmenityArcheryOutdoor">outdoor</label><input type="checkbox" id="searchAmenityArcheryOutdoor" name="searchAmenityArcheryOutdoor" value="archery outdoor"> 
				<h3 id="searchAmenityMerchantHeader">merchant</h3>
				<label for="searchAmenityMerchantIndoor">indoor</label><input type="checkbox" id="searchAmenityMerchantIndoor" name="searchAmenityMerchantIndoor" value="merchant indoor">    
				<label for="searchAmenityMerchantOutdoor">outdoor</label><input type="checkbox" id="searchAmenityMerchantOutdoor" name="searchAmenityMerchantOutdoor" value="merchant outdoor"> 
				<h3 id="searchAmenityClassesHeader">classes</h3>
				<label for="searchAmenityClassesIndoor">indoor</label><input type="checkbox" id="searchAmenityClassesIndoor" name="searchAmenityClassesIndoor" value="classes indoor">    
				<label for="searchAmenityClassesOutdoor">outdoor</label><input type="checkbox" id="searchAmenityClassesOutdoor" name="searchAmenityClassesOutdoor" value="classes outdoor"> 
				<label for="searchAmenitySpecialty" title="The venue has a unique quality, e.g. equestrian, paintball, tall-ship, etc.">specialty</label><input type="checkbox" id="searchAmenitySpecialty" name="searchAmenitySpecialty" value="specialty" title="The venue has a unique quality, e.g. equestrian, paintball, tall-ship, etc." >
				<h3 id="searchAmenityKitchenHeader">kitchen</h3>
				<label for="searchAmenityKitchenstove">stove</label><input type="checkbox" id="searchAmenityKitchenstove" name="searchAmenityKitchenstove" value="kitchenstove">
				<label for="searchAmenityKitchenoven">oven</label><input type="checkbox" id="searchAmenityKitchenoven" name="searchAmenityKitchenoven" value="kitchen oven">    
				<label for="searchAmenityKitchenwarmer">warmer</label><input type="checkbox" id="searchAmenityKitchenwarmer" name="searchAmenityKitchenwarmer" value="kitchen warmer">  
				<label for="searchAmenityKitchenfridge">fridge</label><input type="checkbox" id="searchAmenityKitchenfridge" name="searchAmenityKitchenfridge" value="kitchen fridge">    
				<label for="searchAmenityKitchenfreezer">freezer</label><input type="checkbox" id="searchAmenityKitchenfreezer" name="searchAmenityKitchenfreezer" value="kitchen freezer">    
				<label for="searchAmenityKitchendishwasher">dish washer</label><input type="checkbox" id="searchAmenityKitchendishwasher" name="searchAmenityKitchendishwasher" value="kitchen dish washer"> 
				<h3 id="searchAmenityParkingHeader">parking</h3>
				<label for="searchAmenityParkingonsite">on-site</label><input type="checkbox" id="searchAmenityParkingonsite" name="searchAmenityParkingonsite" value="on-site parking">    
				<label for="searchAmenityParkingpaylot">pay lot</label><input type="checkbox" id="searchAmenityParkingpaylot" name="searchAmenityParkingpaylot" value="pay lot parking"> 
				<label for="searchAmenityParkingstreetfree">street (free)</label><input type="checkbox" id="searchAmenityParkingstreetfree" name="searchAmenityParkingstreetfree" value="street parking (free)">    
				<label for="searchAmenityParkingstreetmetered">street (metered)</label><input type="checkbox" id="searchAmenityParkingstreetmetered" name="searchAmenityParkingstreetmetered" value="street parking (metered)"> 
				<label for="searchAmenityLoadingzone">loading zone</label><input type="checkbox" id="searchAmenityLoadingzone" name="searchAmenityLoadingzone" value="loading zone">    
				<h3 id="searchAmenityTransitHeader">transit</h3>
				<label for="searchAmenityTransitbus">bus</label><input type="checkbox" id="searchAmenityTransitbus" name="searchAmenityTransitbus" value="transit bus">    
				<label for="searchAmenityTransitsubway">subway</label><input type="checkbox" id="searchAmenityTransitsubway" name="searchAmenityTransitsubway" value="transit subway">    
				<label for="searchAmenityTransittrain">train</label><input type="checkbox" id="searchAmenityTransittrain" name="searchAmenityTransittrain" value="transit train">
				  <br />&nbsp;  
			</div>
			
			<input type="checkbox" name="searchHasPhotos" value="" />has photos <br /> &nbsp;<br />&nbsp;
		</div>
		<div id="locationIconInfoWindow" class="infowindow" style="width:34em; left: 21em" onClick="javascript:jQuery('#locationIconInfoWindow').css('display','none');void(0);">Use the drop-down to select the type of icon to represent this venue.<br />
	<div class="rTable">
	<div class="rTableRow">
	<div class="rTableCell"><img src="images/archery.png" /> Archery </div>
	<div class="rTableCell"><img src="images/pirates.png" /> Arrrr! </div>
	<div class="rTableCell"><img src="images/battlefield.png" /> Battlefield </div></div>
	<div class="rTableRow">
	<div class="rTableCell"><img src="images/ferriswheel.png" /> Fairgrounds </div>
	<div class="rTableCell"><img src="images/tree.png" /> Park </div>
	<div class="rTableCell"><img src="images/shelter_picnic.png" /> Picnic shelter </div></div>
	<div class="rTableRow"></div>
	<div class="rTableRow">
	<div class="rTableCell"><img src="images/icehockey.png" /> Hockey arena </div>
	<div class="rTableCell"><img src="images/soccer.png" /> Soccer field </div>
	<div class="rTableCell"><img src="images/usfootball.png" /> Football field </div></div>
	<div class="rTableRow">
	<div class="rTableCell"><img src="images/baseball.png" /> Baseball field </div>
	<div class="rTableCell"><img src="images/summercamp.png" /> Campground </div>
	<div class="rTableCell"><img src="images/shootingrange.png" /> Range </div></div>
	<div class="rTableRow"></div>
	<div class="rTableRow">
	<div class="rTableCell"><img src="images/conference.png" /> Conference Centre </div>
	<div class="rTableCell"><img src="images/statue-2.png" /> Point of Interest </div>
	<div class="rTableCell"><img src="images/icon-sevilla.png" /> Church </div></div>
	<div class="rTableRow">
	<div class="rTableCell"><img src="images/school.png" /> School </div>
	<div class="rTableCell"><img src="images/townhall.png" /> Town hall </div>
	<div class="rTableCell"><img src="images/communitycentre.png" /> Community centre </div></div>
	<div class="rTableRow"></div>
	<div class="rTableRow">
	<div class="rTableCell"><img src="images/tower.png" /> Tower </div>
	<div class="rTableCell"><img src="images/palace-2.png" /> Castle </div>
	<div class="rTableCell"><img src="images/citywalls.png" /> City Walls </div></div>
	<div class="rTableRow">
	<div class="rTableCell"><img src="images/dance_class.png" /> Dancing </div>
	<div class="rTableCell"><img src="images/restaurant_fish.png" /> Fish and game </div></div>
	<div class="rTableRow"></div>
	<div class="rTableRow">
	<div class="rTableCell"><img src="images/conveniencestore.png" /> Groceries </div>
	<div class="rTableCell"><img src="images/mall.png" /> Mall </div>
	<div class="rTableCell"><img src="images/fillingstation.png" /> Gas station </div></div>
	</div>
	<br />Coming soon<br /><img src="images/picnic-2.png" />       <img src="images/castle-2.png" /> 
	<img src="images/home-2.png" />      <img src="images/hotfoodcheckpoint.png" />
	<img src="images/laundromat.png" />       <img src="images/bank.png" />
	<img src="images/parking-meter.png" />    <img src="images/toilets.png" />
	<br />
	<a href="https://mapicons.mapsmarker.com" title="Maps Icons Collection"><img src="images/miclogo-88x31.gif" target="mapicons" /></a>               
 </div>
<!--  **************************  -->
  <div id="detailEntry"><!-- ToDo: Display list of contacts --><!-- ToDo: Display list of amenities --><!-- ToDo: Display list of photos --><!-- ToDo: Display list of nearby -->
  &nbsp;<br /><strong>Nearby</strong><div class="preview" id="detailEntryNearby"></div> 
  <br /><strong>Contacts and Conversations</strong><div class="preview" id="detailEntryContacts"></div>
  <div id="divContactNotes">
		<!-- label for="ContactComboBox">Select type of contact</label -->
		<select id="ContactComboBox" name="ContactComboBox">
			<option value="" selected="selected"> -- Select type of contact -- </option>
			<option value="conversation">Phone conversation</option>
			<option value="event">Event</option>
			<option value="meeting">On-site meeting</option>
			<option value="financial">Financial transaction</option>
		</select><br />
<!-- ToDo: relate combobox to associated contact -->
		<!-- unnecessary field: who owns the conversation. -->
		<!-- unnecessary field: date of conversation. -->
		<label for="ContactPerson">Contact person</label>
		<input name="ContactPerson" id="contactPerson" type="text" title="Provide the name(s) of the person(s) in the conversation, except yourself."><br />
		<label for="ContactEvent">Event</label>
		<input name="ContactEvent" id="contactEvent" type="text" title="Provide the name of the event or demo."><br />
		<label for="ContactDate">Event date</label>
		<input name="ContactDate" id="contactDate" type="text" title="Provide the expected or exact date of the event. In the case of a multiday event, enter just the opening day."><br />
		<!-- label for="ContactPlaintext">Extra notes</label -->
		<textarea name="ContactPlaintext" cols="30" rows="5" id="contactPlaintext" title="Type the conversation here.  If you need to keep the details private, enter a secret key."></textarea><img src="images/menu-icon-phone.png" title="Open a conversation form.  If you need to keep the details private, enter a secret key." id="contactEntry" onClick="javascript:jQuery('#ContactEntryWindow').css('display','block');void(0);" /><img src="images/menu-icon-phone.png" title="Open a conversation form.  If you need to keep the details private, enter a secret key." id="contactSurvey" onClick="javascript:jQuery('#ContactSurveyWindow').css('display','block');void(0);" /><img src="images/menu-icon-help.gif" title="Type the conversation here.  If you need to keep the details private, enter a secret key. (Click for more)" id="contactInfo" onClick="javascript:jQuery('#contactInfoWindow').css('display','block');void(0);" /><br />
		<label class="privacy" for="ContactEncryptKey">Secret key</label>
		<input name="ContactEncryptKey" id="contactEncryptKey" type="text" title="Provide a secret key to keep this conversation private. The key will not be stored.  You must remember it yourself."><label class="privacy" for="chkKeyCookie">Cookie</label><input type="checkbox" name="chkKeyCookie" id="chkKeyCookie" title="Select Cookie to save the passphrase to your computer.&nbsp; Remember not to delete your cookies." /><br />
		<label class="privacy" for="ContactEncrypttext">Stored text</label>
		<textarea name="ContactEncrypttext" cols="25" rows="1" id="contactEncrypttext" title="This is the exact text that will be stored."></textarea> </div>
    <input name="NewContact" id="btnNewContact" type="button" value="Save" title="Add a new contact or log a private conversation"><br /> &nbsp; &nbsp;
  <br /><strong>Rooms and Amenities</strong><div class="preview" id="detailEntryAmenities"></div>
		<!-- label for="AmenitiesComboBox">Select type of contact</label -->
		<select id="AmenitiesComboBox" name="AmenitiesComboBox" >
			<option value="" selected="selected"> -- Select type of amenity -- </option>
			<option value="summary">Amenity summary</option>
			<option value="room">Room</option>
			<option value="gym">Gymnasium</option>
			<option value="field">Field</option>
			<option value="dais">Dais or Stage</option>
			<option value="kitchen">Kitchen or Bar</option>
			<option value="bathroom">Bathroom or Changeroom</option>
			<option value="elevator">Elevator</option>
			<option value="parking">Parking and access</option>
		</select><br />
		<label for="AmenitiesRoom">Room name</label>
		<input name="AmenitiesRoom" id="amenitiesRoom" type="text" title="Provide a name or identifier for the room."><br />
		<label for="AmenitiesLength">Length</label>
		<input name="AmenitiesLength" id="amenitiesLength" type="text" title="Provide the horizontal dimension of the room.">
		<label for="AmenitiesWidth">Width</label>
		<input name="AmenitiesWidth" id="amenitiesWidth" type="text" title="Provide the horizontal dimension of the room.">
		<label for="AmenitiesHeight">Height</label><!-- for inside areas, only -->
		<input name="AmenitiesHeight" id="amenitiesHeight" type="text" title="Provide the vertical dimension of the room.">
		<label for="AmenitiesElevation">Elevation</label><!-- for stage or dias, only -->
		<input name="AmenitiesElevation" id="amenitiesElevation" type="text" title="Provide the vertical lift of the stage or dais."><br />
		<label for="AmenitiesDescription">Description</label><br />
		<textarea name="AmenitiesDescription" cols="30" rows="3" id="amenitiesDescription" title="Provide a description of the amenity."></textarea><img src="images/menu-icon-help.gif" title="Type the amenity descripton here.  (Click for more)" id="amenitiesInfo" onClick="javascript:jQuery('#amenitiesInfoWindow').css('display','block');void(0);"> 
<!--  **************************  -->
<!-- ToDo: relate checkboxes to associated amenities -->
	<div data-role="controlgroup" data-type="horizontal" id="bigEventGroup" ><!-- for all amenities -->
<label for="bigEvent">big event</label><input type="checkbox" id="bigEvent" name="AmenitiesInfobigEvent" value="big event"> 
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="smallEventGroup" ><!-- for all amenities -->
<label for="smallEvent">small event</label><input type="checkbox" id="smallEvent" name="AmenitiesInfosmallEvent" value="small event">    
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="campingGroup" ><!-- for all amenities -->
<label for="camping">camping</label><input type="checkbox" id="camping" name="AmenitiesInfocamping" value="camping">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="meetingsGroup" ><!-- for all amenities -->
<label for="meetings">meetings</label><input type="checkbox" id="meetings" name="AmenitiesInfomeetings" value="meetings">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="electricityGroup" ><!-- for all amenities -->
<label for="electricity">electricity</label><input type="checkbox" id="electricity" name="AmenitiesInfoelectricity" value="electricity">     
<label for="water">drinking water</label><input type="checkbox" id="water" name="AmenitiesInfowater" value="water">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="bathroomGroup" ><!-- for bathroom, gym, and fields, only -->
<label for="bathroom">bathroom</label><input type="checkbox" id="bathroom" name="AmenitiesInfobathroom" value="bathroom">    <label for="changeroom">change room</label><input type="checkbox" id="changeroom" name="AmenitiesInfochangeroom" value="change room">    <label for="shower">shower</label><input type="checkbox" id="shower" name="AmenitiesInfoshower" value="shower">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="accessibleGroup" ><!-- for parking and elevator, only -->
<label for="wheelchairaccessible" style="width: 200px">wheel-chair accessible</label><input type="checkbox" id="wheelchairaccessible" name="AmenitiesInfowheelchairaccessible" value="wheel-chair accessible">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="elevatorGroup" ><!-- for parking and elevator, only -->
    <label for="accessibleelevator">elevator</label><input type="checkbox" id="accessibleelevator" name="AmenitiesInfoaccessibleelevator" value="elevator">    <label for="accessibleramp">ramp</label><input type="checkbox" id="accessibleramp" name="AmenitiesInfoaccessibleramp" value="ramp">    <label for="accessiblenoStairs">no stairs</label><input type="checkbox" id="accessiblenoStairs" name="AmenitiesInfoaccessiblenoStairs" value="no stairs"> 
	</div>
	<h3 id="fightingHeader">fighting</h3><!-- for rooms, gyms, and fields, only -->
	<div data-role="controlgroup" data-type="horizontal" id="fightingGroup" ><!-- for rooms, gyms, and fields, only -->
    <label for="fightingIndoor">indoor</label><input type="checkbox" id="fightingIndoor" name="AmenitiesInfofightingIndoor" value="fighting indoor">    <label for="fightingOutdoor">outdoor</label><input type="checkbox" id="fightingOutdoor" name="AmenitiesInfofightingOutdoor" value="fighting outdoor"> 
	</div>
	<h3 id="fencingHeader">fencing</h3><!-- for rooms, gyms, and fields, only -->
	<div data-role="controlgroup" data-type="horizontal" id="fencingGroup" ><!-- for rooms, gyms, and fields, only -->
    <label for="fencingIndoor">indoor</label><input type="checkbox" id="fencingIndoor" name="AmenitiesInfofencingIndoor" value="fencing indoor">    <label for="fencingOutdoor">outdoor</label><input type="checkbox" id="fencingOutdoor" name="AmenitiesInfofencingOutdoor"value="fencing outdoor">     
	</div>
	<h3 id="archeryHeader">archery</h3><!-- for rooms, gyms, and fields, only -->
	<div data-role="controlgroup" data-type="horizontal" id="archeryGroup" ><!-- for rooms, gyms, and fields, only -->
    <label for="archeryIndoor">indoor</label><input type="checkbox" id="archeryIndoor" name="AmenitiesInfoarcheryIndoor" value="archery indoor">    <label for="archeryOutdoor">outdoor</label><input type="checkbox" id="archeryOutdoor" name="AmenitiesInfoarcheryOutdoor" value="archery outdoor"> 
	</div>
	<h3 id="merchantHeader">merchant</h3><!-- for rooms, gyms, and fields, only -->
	<div data-role="controlgroup" data-type="horizontal" id="merchantGroup" ><!-- for rooms, gyms, and fields, only -->
    <label for="merchantIndoor">indoor</label><input type="checkbox" id="merchantIndoor" name="AmenitiesInfomerchantIndoor" value="merchant indoor">    <label for="merchantOutdoor">outdoor</label><input type="checkbox" id="merchantOutdoor" name="AmenitiesInfomerchantOutdoor" value="merchant outdoor"> 
	</div>
	<h3 id="classesHeader">classes</h3><!-- for rooms, gyms, and fields, only -->
	<div data-role="controlgroup" data-type="horizontal" id="classesGroup" ><!-- for rooms, gyms, and fields, only -->
    <label for="classesIndoor">indoor</label><input type="checkbox" id="classesIndoor" name="AmenitiesInfoclassesIndoor" value="classes indoor">    <label for="classesOutdoor">outdoor</label><input type="checkbox" id="classesOutdoor" name="AmenitiesInfoclassesOutdoor" value="classes outdoor">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="specialtyGroup" ><!-- for rooms, gyms, and fields, only -->
<label for="specialty" title="The venue has a unique quality, e.g. equestrian, paintball, tall-ship, etc.">specialty</label><input type="checkbox" id="specialty" name="AmenitiesInfospecialty" value="specialty" title="The venue has a unique quality, e.g. equestrian, paintball, tall-ship, etc." >     
	</div>
	<h3 id="kitchenHeader">kitchen</h3><!-- for kitchen, only -->
	<div data-role="controlgroup" data-type="horizontal" id="kitchenGroup" ><!-- for kitchen, only -->     
    <label for="kitchenstove">stove</label><input type="checkbox" id="kitchenstove" name="AmenitiesInfokitchenstove" value="kitchenstove">    <label for="kitchenoven">oven</label><input type="checkbox" id="kitchenoven" name="AmenitiesInfokitchenoven" value="kitchen oven">    <label for="kitchenwarmer">warmer</label><input type="checkbox" id="kitchenwarmer" name="AmenitiesInfokitchenwarmer" value="kitchen warmer">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="kitchenfridgeGroup" ><!-- for kitchen, only -->
    <label for="kitchenfridge">fridge</label><input type="checkbox" id="kitchenfridge" name="AmenitiesInfokitchenfridge" value="kitchen fridge">    <label for="kitchenfreezer">freezer</label><input type="checkbox" id="kitchenfreezer" name="AmenitiesInfokitchenfreezer" value="kitchen freezer">    <label for="kitchendishwasher">dish washer</label><input type="checkbox" id="kitchendishwasher" name="AmenitiesInfokitchendishwasher" value="kitchen dish washer">     
	</div>
	<h3 id="parkingHeader">parking</h3><!-- for parking and fields, only -->
	<div data-role="controlgroup" data-type="horizontal" id="parkingGroup" ><!-- for parking and fields, only -->
    <label for="parkingonsite">on-site</label><input type="checkbox" id="parkingonsite" name="AmenitiesInfoparkingonsite" value="on-site parking">    <label for="parkingpaylot">pay lot</label><input type="checkbox" id="parkingpaylot" name="AmenitiesInfoparkingpaylot" value="pay lot parking">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="parkingstreetGroup" ><!-- for parking and fields, only -->
    <label for="parkingstreetfree">street (free)</label><input type="checkbox" id="parkingstreetfree" name="AmenitiesInfoparkingstreetfree" value="street parking (free)">    <label for="parkingstreetmetered">street (metered)</label><input type="checkbox" id="parkingstreetmetered" name="AmenitiesInfoparkingstreetmetered" value="street parking (metered)">     
	</div>
	<h3 id="transitHeader">transit</h3><!-- for parking and fields, only -->
	<div data-role="controlgroup" data-type="horizontal" id="transitGroup" ><!-- for parking and fields, only -->
    <label for="transitbus">bus</label><input type="checkbox" id="transitbus" name="AmenitiesInfotransitbus" value="transit bus">    <label for="transitsubway">subway</label><input type="checkbox" id="transitsubway" name="AmenitiesInfotransitsubway" value="transit subway">    <label for="transittrain">train</label><input type="checkbox" id="transittrain" name="AmenitiesInfotransittrain" value="transit train">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" id="loadingGroup" ><!-- for parking, only -->
<label for="loadingzone">loading zone</label><input type="checkbox" id="loadingzone" name="AmenitiesInfoloadingzone" value="loading zone">    <label for="loadinginfront">in front</label><input type="checkbox" id="loadinginfront" name="AmenitiesInfoloadinginfront" value="loading in front">    <label for="loadingoutback">out back</label><input type="checkbox" id="loadingoutback" name="AmenitiesInfoloadingoutback" value="loading out back">   <br />&nbsp;  
	</div>

<!--  **************************  -->
    <input name="NewAmenity" id="btnNewAmenity" type="button" value="Save" title="Add details about a room onsite or other amenities"><br /> &nbsp; &nbsp; 
  <br /><strong>Photos</strong><div class="preview" id="detailEntryPhotos">
  </div>
  <div data-role="controlgroup" data-type="horizontal" id="photoGroup" ><!-- for photos, only -->
	  <input type="hidden" id="photoType" name="PhotoType" value="JPEG">
	  <input type="hidden" id="photoServerFile" name="PhotoServerFile" value="<?=uniqid()?>">   
	  <label for="PhotoSourceFile">Add Photo</label> <br />
	  <input type="file" id="photoSourceFile" name="PhotoSourceFile" accept=".jpg,.jpeg">   
	  <br />
	  <label for="PhotoDescription">Description</label> <input type="text" id="photoDescription" name="PhotoDescription">   
	  <br />&nbsp;  
	<!--?=$strPreviewPhotos?!--></div>
    <input name="NewPhoto" id="btnNewPhoto" type="button" value="Save" title="Add a new photo and description to the set"><br /> &nbsp; 
  </div>
  <div id="anotherLink"><a href="anon-venue-entry.php">Anonymous entry</a> &nbsp; &hellip; &nbsp; <a href="auth-venue-bucket.php">Bucketing</a> &nbsp; &hellip; &nbsp; <a href="anon-venue-techdoc.php">Documentation</a></div>
  <div id="outsourceCredits"><a href="https://mapicons.mapsmarker.com" target="_blank">Maps Icons Collection </a> &nbsp; &hellip; &nbsp; <a href="http://digipiph.com/blog/submitting-multipartform-data-using-jquery-and-ajax" target="_blank">AJAX help</a></div>
<!--  **************************  -->
  <div id="pauseScreen" class="pausewindow"><img class="pausefloat" src="images/mounted-knight-walking.gif" id="pauseLoading" /></div>
</form>
<?php
}
?>
</body>
</html>
