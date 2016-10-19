<?php

// ToDo: post data (Working since 2016-07-18)
// ToDo: add area click on the map to move geolocation by clicking (working since july 8)
// ToDo: load up nearby point.
// ToDo: display nearby paths	https://developers.google.com/maps/documentation/javascript/examples/polygon-simple
// ToDo: display nearby areas. http://stackoverflow.com/questions/7316963/drawing-a-circle-google-static-maps
// ToDo: Move or delete file after successful post.
// ToDo: overlay "thinking" onto updating map http://www.andrewdavidson.com/articles/spinning-wait-icons/wait20.gif

/*
*/
define('ROOTPATH', 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']));

include 'db-manager.php';
$blnSignedIn=accessAuthenticate();
$arrUser=accessUser(); 
$blnValidPost = validVenuePost();  // ToDo: If post data found is good, process, delete file, grab the next one.
if( $blnValidPost )
{
	$objConn = dbAccessOpen();
	if( $objConn ) 
	{
		$blnValidPost = dbVenuePostNew( $objConn );
		dbAccessClose( $objConn );
	}
}
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html><?php
//	echo( '<!-- POST: ' . $blnValidPost . ' End: ' . $intContentDataEnd . ' LF: ' . strpos( $txtFileContent, chr( 10 ), $intContentDataBegin ) . chr( 13) . chr( 10 ) .' -->' . chr( 13 ). chr( 10 ) );
$strPreview='No more previews left.';
if( strpos(getcwd(),'/data') === false ) {
	chdir( 'data' );
	}
$txtFilename = '';
if( isset($_GET['fn']) ) 
{
	$txtFilename = $_GET['fn'];
}
$txtFileList = '' . chr( 13 ) . chr( 10 );
if( $objDir = opendir( '.' ) )
{	
	/* This is the correct way to loop over the directory. -- http://php.net/manual/en/function.readdir.php -- */
	while (false !== ($strEntry = readdir($objDir))) 
	{
		if(  substr( $strEntry, 0, 4 ) == 'anon' )
		{
			$txtFileList .= '<fileName>'. $strEntry . '</fileName>' . chr( 13 ) . chr( 10 );
		}
		if(  $txtFilename == '' && substr( $strEntry, 0, 4 ) == 'anon' )
		{
			$txtFilename = $strEntry;
		}
		elseif(  $txtFilename == $strEntry && $blnValidPost ) 
		{
			$txtFilename = '';
		}
	}

}
if(  $txtFilename != '' )
{
	$txtFileContent = file_get_contents( $txtFilename );
	$intContentDataBegin = strpos( $txtFileContent, '<txtBodyHtmlArea>' );
	$intContentDataEnd = strpos( $txtFileContent, '</txtBodyHtmlArea>' );
	if( $intContentDataBegin >= 0 && $intContentDataEnd >= 0 )
	{
		$strPreview = '';
//	echo( '<!-- Begin: ' . $intContentDataBegin . ' End: ' . $intContentDataEnd . ' LF: ' . strpos( $txtFileContent, chr( 10 ), $intContentDataBegin ) . chr( 13) . chr( 10 ) .' -->' . chr( 13 ). chr( 10 ) );
		$txtFileContent = substr( $txtFileContent, 0, $intContentDataBegin ) . str_replace( chr(10),'&lt;br /&gt;',str_replace( chr(13),'&lt;br /&gt;',str_replace( chr(13).chr(10),'&lt;br /&gt;',substr( $txtFileContent, $intContentDataBegin, $intContentDataEnd - $intContentDataBegin )))) . substr( $txtFileContent, $intContentDataEnd, strlen( $txtFileContent ) - $intContentDataEnd );  
//	echo( '<!-- Begin: ' . $intContentDataBegin . ' End: ' . $intContentDataEnd . ' LF: ' . strpos( $txtFileContent, chr( 10 ), $intContentDataBegin ) . chr( 13 ). chr( 10 ) . substr( $txtFileContent, $intContentDataBegin, $intContentDataEnd - $intContentDataBegin ) . chr( 13) . chr( 10 ) .' -->' . chr( 13) . chr( 10 ) );
	}
	$intClickInfoBegin = strpos( $txtFileContent, '<txtClickInfo>' );
	$intClickInfoEnd = strpos( $txtFileContent, '</txtClickInfo>' );
	if( $intClickInfoBegin >= 0 && $intClickInfoEnd >= 0 )
	{
		$strPreview = '';
//	echo( '<!-- Begin: ' . $intClickInfoBegin . ' End: ' . $intClickInfoEnd . ' LF: ' . strpos( $txtFileContent, chr( 10 ), $intClickInfoBegin ) . chr( 13) . chr( 10 ) .' -->' . chr( 13 ). chr( 10 ) );
		$txtFileContent = substr( $txtFileContent, 0, $intClickInfoBegin ) . str_replace( chr(10),'&lt;br /&gt;',str_replace( chr(13),'&lt;br /&gt;',str_replace( chr(13).chr(10),'&lt;br /&gt;',substr( $txtFileContent, $intClickInfoBegin, $intClickInfoEnd - $intClickInfoBegin )))) . substr( $txtFileContent, $intClickInfoEnd, strlen( $txtFileContent ) - $intClickInfoEnd );  
//	echo( '<!-- Begin: ' . $intClickInfoBegin . ' End: ' . $intClickInfoEnd . ' LF: ' . strpos( $txtFileContent, chr( 10 ), $intClickInfoBegin ) . chr( 13 ). chr( 10 ) . substr( $txtFileContent, $intClickInfoBegin, $intClickInfoEnd - $intClickInfoBegin ) . chr( 13) . chr( 10 ) .' -->' . chr( 13) . chr( 10 ) );
	}
	$objXML = new xml2Array();
	$arrOutput = $objXML->parse($txtFileContent);
	echo( '<!-- ' . chr(13) . chr(10) );
	print_r( str_replace( chr(10),'<br />',str_replace( chr(13),'<br />',str_replace( chr(13).chr(10),'<br />',$arrOutput[0][children][0][tagData]))) . chr(13) . chr(10) );
	foreach( $arrOutput as $objItem ) 
	{
	  print_r( $objItem ); 
	  echo( chr(13) . chr(10) ); 
	  if( $objItem[name]=='ENTRYLINES' )
	  {
	    foreach( $objItem[children] as $objChild )
		{
		  print_r( $objChild ); 
		  echo( chr(13) . chr(10) ); 
		  if( $objChild[name]=='TXTBODYHTMLAREA' )
		  {
		    $strPreview .= str_replace( chr(13).chr(10),'<br />',$objChild[tagData]);
		  }
		  if( $objChild[name]=='TXTCLICKINFO' )
		  {
		    $strPreview .= '<br />' . str_replace( chr(13).chr(10),'<br />',$objChild[tagData]);
		  }
		}
	  }
	  else
	  {
	    echo( 'Shaka!' . chr(13) . chr(10) ); 
	  }
	};
	print_r($arrOutput);
	echo( ' -->' . chr(13) . chr(10) );
	
}

class xml2Array {
    
    var $arrOutput = array();
    var $resParser;
    var $strXmlData;
    
    function parse($strInputXML) {
    
            $this->resParser = xml_parser_create ();
            xml_set_object($this->resParser,$this);
            xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");
            
            xml_set_character_data_handler($this->resParser, "tagData");
        
            $this->strXmlData = xml_parse($this->resParser,$strInputXML );
            if(!$this->strXmlData) {
               die(sprintf("XML error: %s at line %d",
            xml_error_string(xml_get_error_code($this->resParser)),
            xml_get_current_line_number($this->resParser)));
            }
                            
            xml_parser_free($this->resParser);
            
            return $this->arrOutput;
    }
    function tagOpen($parser, $name, $attrs) {
       $tag=array("name"=>$name,"attrs"=>$attrs); 
       array_push($this->arrOutput,$tag);
    }
    
    function tagData($parser, $tagData) {
       if(trim($tagData)) {
            if(isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
                $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
            } 
            else {
                $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $tagData;
            }
       }
    }
    
    function tagClosed($parser, $name) {
       $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
       array_pop($this->arrOutput);
    }
}

?>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Entry from notes</title>
<!-- http://maps.google.com/maps/api/geocode/json?sensor=false&address=Mississauga -->
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>   
<style type="text/css"><!--
div { vertical-align: top }
div#anonEntry {position: fixed; left: 50%; top: 2em; height: 32em; overflow-y: auto; }
label {width: 140px; display: inline-block;}
.loading {position:absolute; top:0px; left:0px; z-index:1; display:none; filter: alpha(opacity=30);}
/* grabbed from VestYorvik website */
.wall, .wall TD, .wall TH {background-image:url('images/bkgrnd.gif'); background-color:#ffffd8 }
body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }
map {color:#993333}
h1 {font-family:Georgia; color:#000080}
h2 {font-family:Georgia; color:#000080}
p.footer {font-size:x-small; text-align:center}
// -->
</style>
<script type="text/javascript"><!--
  var imgLoadTN = new Image();
  var imgLoadLG = new Image();
  var imgLoadNT = new Image();
  imgLoadTN.src='images/loading16pt100.gif';
  imgLoadLG.src='images/loading12pt400.gif';
  imgLoadNT.src='images/loading8pt400.gif';

$(function(){
  jQuery('#mapLock').change(function(){if($('#mapLock').attr('checked')=='checked'){jQuery('#latitude').attr('disabled','');jQuery('#longitude').attr('disabled','');}else{jQuery('#latitude').attr('disabled',false);jQuery('#longitude').attr('disabled',false);}});

jQuery('#locationName').change(function(){getCoordinates();});
jQuery('#address').change(function(){getCoordinates();});
jQuery('#city').change(function(){getCoordinates();});
jQuery('#province').change(function(){getCoordinates();});
jQuery('#postalCode').change(function(){getCoordinates();});
jQuery('#latitude').change(function(){redrawMaps();});
jQuery('#longitude').change(function(){redrawMaps();});
<?php
if( !$blnValidPost && isset($_POST['locationName']) ) { echo 'redrawMaps();'; }
?>

// get the list of files from the bucket list.  Returns the list of files, and the contents of the requested file.
$.get('<?=ROOTPATH?>/auth-venue-bucket-list.php?fn=<?=$txtFilename?>',function(xmlData,status)
  {
    if( status == 'success' )
    {
      var objXMLdata = $.parseXML( xmlData ); 
      var strCurrent = $( objXMLdata ).find('anonFile').find( 'fileName' ).text(); 
      $( objXMLdata ).find('bucketList').find( 'fileName' ).each(function()
      {
        if($(this).text()==strCurrent)
        {
          var strPrev = $(this).prev().text(); 
		  var strNext = $(this).next().text();
		  
          $('#btnDelete').click(function(){
			var request = $.post('<?=ROOTPATH?>/auth-venue-bucket-list.php',
			  {
				delete: strCurrent
			  },
				function(data,status){
					console.log("Data: " + data + "\nStatus: " + status);
					window.location.href = '<?=ROOTPATH?>/auth-venue-bucket.php?fn='+ strNext; 
				}
			  );
          });
          $('#btnBanish').click(function(){
            window.location.href = '<?=ROOTPATH?>/auth-venue-bucket.php?fn='+ strNext; 
          });
          $('#btnPrevious').click(function(){
            window.location.href = '<?=ROOTPATH?>/auth-venue-bucket.php?fn='+ strPrev; 
          }); 
          $('#btnNext').click(function(){
            window.location.href = '<?=ROOTPATH?>/auth-venue-bucket.php?fn='+ strNext; 
          });
        }
      }); 
    }
  }
);

//alert('There will be a password-secured log-in here.  That\'s next.\nFormatting is coming, too.\nIn the meatime, see what fields are missing.')
});

function getCoordinates()
{
  var strAddress= ''+ $('#locationName').val();
  strAddress= ($.trim( strAddress ) != '' ? strAddress +', ' : '' ) + $('#Address').val();
  strAddress= ($.trim( strAddress ) != '' ? strAddress +', ' : '' ) + $('#city').val();
  strAddress= ($.trim( strAddress ) != '' ? strAddress +', ' : '' ) + $('#province').val();
  strAddress= ($.trim( strAddress ) != '' ? strAddress +', ' : '' ) + $('#postalCode').val();
  if( $('#mapLock').attr('checked')!='checked' )
  {
  jQuery.getJSON( 'http://maps.google.com/maps/api/geocode/json?sensor=false&address=' + ( $.trim( strAddress ) != '' ? encodeURIComponent( strAddress ) : 'Mississauga%2C+Ontario' ), function(oResult){if(oResult.status=="OK")
{ $('#latitude').val(oResult.results[0].geometry.location.lat);$('#longitude').val(oResult.results[0].geometry.location.lng);redrawMaps();if($('#city').val()==''){var str=oResult.results[0].formatted_address;var str1=parseAddress(oResult.results[0].address_components,'sublocality_level_1');var str2=parseAddress(oResult.results[0].address_components,'locality');$('#city').val( ( ( str ).indexOf( str1 ) >= 0 ? str1 : str2 ) );console.log(str); console.log(str1); console.log(str2); };if($('#province').val()==''){$('#province').val(parseAddress(oResult.results[0].address_components,'administrative_area_level_1'))};if($('#Address').val()==''){var str=oResult.results[0].formatted_address;var str1=parseAddress(oResult.results[0].address_components,'street_number');var str2=parseAddress(oResult.results[0].address_components,'route');$('#Address').val( ( str.indexOf( str1 )>=0 ? str1 + ' ': '' ) + ( str.indexOf( str2 )>=0 ? str2 : '' ) ); } } } );
  }
}

// ToDo: overlay "thinking" onto updating map 
/**
http://stackoverflow.com/questions/1964839/how-can-i-create-a-please-wait-loading-animation-using-jquery
http://www.andrewdavidson.com/articles/spinning-wait-icons/wait20.gif
http://stackoverflow.com/questions/476679/preloading-images-with-jquery
http://www.designchemical.com/blog/index.php/jquery/quick-and-easy-jquery-image-swap/
**/
// Get the new map data from Google API. 
// ToDo: get the nearby venues from the database. Add in the markers.
function redrawMaps(){
  var imgTN = new Image();
  var imgLG = new Image();
  var strMarkers = '';
  jQuery('#detailMap').attr('src',imgLoadNT.src);	// Loading markers
  var request = $.get('<?=ROOTPATH?>/auth-venue-nearby-list.php?lat='+ jQuery('#latitude').val() +'&lng='+ jQuery('#longitude').val(),function(xmlData,status)
	{
		if( status == 'success' )
		{
		  var objXMLdata = $.parseXML( xmlData ); 
		  $( objXMLdata ).find('nearbyList').each(function()
			// Load up on markers if there are any.
			{
			strMarkers += '&markers=color:0x2952CC%7Csize:small%7C' 
				+ $(this).find('strGeoLatitude').text()
				+ '%2C' +  $(this).find('strGeoLongitude').text();
			}); 
		  console.log( 'marker:' + strMarkers );
		  jQuery('#detailMap').attr('src',imgLoadLG.src);	// Loading detail
		  imgLG.src='http://maps.google.com/maps/api/staticmap?center='+ jQuery('#latitude').val() +','+ jQuery('#longitude').val() +'&zoom=14&size=400x400'+ strMarkers +'&markers='+ jQuery('#latitude').val() +'%2C'+ jQuery('#longitude').val() +'&sensor=false&maptype=hybrid'; // Larger detail map
		  setTimeout( function() { jQuery('#detailMap').attr('src',imgLG.src); jQuery('#loadingLG').css('display','none'); }, 50 ); // display larger detail map
		}
		if( status == 'error' )
		{
		  console.log( 'error:' + strMarkers );
		  jQuery('#detailMap').attr('src',imgLoadLG.src);	// Loading detail
		  imgLG.src='http://maps.google.com/maps/api/staticmap?center='+ jQuery('#latitude').val() +','+ jQuery('#longitude').val() +'&zoom=14&size=400x400&markers='+ jQuery('#latitude').val() +'%2C'+ jQuery('#longitude').val() +'&sensor=false&maptype=hybrid'; // Larger detail map
		  setTimeout( function() { jQuery('#detailMap').attr('src',imgLG.src); jQuery('#loadingLG').css('display','none'); }, 50 ); // display larger detail map
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
  jQuery('#thumbnailMap').attr('src',imgLoadTN.src); // Loading thumbnail
//  jQuery('#loadingTN').css('display','block');
//  jQuery('#loadingLG').css('display','block');
  
  imgTN.src='http://maps.google.com/maps/api/staticmap?center='+ jQuery('#latitude').val() +','+ jQuery('#longitude').val() +'&zoom=8&size=100x100&sensor=false'; // Thumbnail map
  setTimeout( function() { jQuery('#thumbnailMap').attr('src',imgTN.src); jQuery('#loadingTN').css('display','none'); }, 500 ); // display little map
  jQuery('#geolatitude').val(jQuery('#latitude').val());
  jQuery('#geolongitude').val(jQuery('#longitude').val());
}

function parseAddress( oAddress, sComponent )
{
  for( var intI=0; intI<oAddress.length; intI++ )
  {
    if( oAddress[intI].types[0]==sComponent ) return( oAddress[intI].short_name );
  }
}

function moveCordinates(strDirection){
  dblMoveNorth = 0.00;
  dblMoveEast = 0.00;
  switch(strDirection) {
  	case 'NorthEast':
	  dblMoveNorth = 0.0079000;
	  dblMoveEast =  0.0117000;	  
	  break;
  	case 'East':
	  dblMoveNorth = 0.00;
	  dblMoveEast = 0.0117000;	  
	  break;
  	case 'SouthEast':
	  dblMoveNorth = -0.0079000;
	  dblMoveEast =   0.0117000;	  
	  break;
  	case 'South':
	  dblMoveNorth = -0.0079000;
	  dblMoveEast = 0.00;	  
	  break;
  	case 'SouthWest':
	  dblMoveNorth = -0.0079000;
	  dblMoveEast =  -0.0117000;	  
	  break;
  	case 'West':
	  dblMoveNorth = 0.00;
	  dblMoveEast = -0.0117000;	  
	  break;
  	case 'NorthWest':
	  dblMoveNorth = 0.0079000;
	  dblMoveEast = -0.0117000;	  
	  break;
  	case 'North':
	  dblMoveNorth = 0.0079000;
	  dblMoveEast = 0.00;	  
	  break;
  	case 'WeeNorthEast':
	  dblMoveNorth = 0.00079000;
	  dblMoveEast =  0.00117000;	  
	  break;
  	case 'WeeEast':
	  dblMoveNorth = 0.00;
	  dblMoveEast = 0.00117000;	  
	  break;
  	case 'WeeSouthEast':
	  dblMoveNorth = -0.00079000;
	  dblMoveEast =   0.00117000;	  
	  break;
  	case 'WeeSouth':
	  dblMoveNorth = -0.00079000;
	  dblMoveEast = 0.00;	  
	  break;
  	case 'WeeSouthWest':
	  dblMoveNorth = -0.00079000;
	  dblMoveEast =  -0.00117000;	  
	  break;
  	case 'WeeWest':
	  dblMoveNorth = 0.00;
	  dblMoveEast = -0.00117000;	  
	  break;
  	case 'WeeNorthWest':
	  dblMoveNorth = 0.00079000;
	  dblMoveEast = -0.00117000;	  
	  break;
  	case 'WeeNorth':
	  dblMoveNorth = 0.00079000;
	  dblMoveEast = 0.00;	  
	  break;
  	case 'WayNorthEast':
	  dblMoveNorth = 0.079000;
	  dblMoveEast =  0.117000;	  
	  break;
  	case 'WayEast':
	  dblMoveNorth = 0.00;
	  dblMoveEast = 0.117000;	  
	  break;
  	case 'WaySouthEast':
	  dblMoveNorth = -0.079000;
	  dblMoveEast =   0.117000;	  
	  break;
  	case 'WaySouth':
	  dblMoveNorth = -0.079000;
	  dblMoveEast = 0.00;	  
	  break;
  	case 'WaySouthWest':
	  dblMoveNorth = -0.079000;
	  dblMoveEast =  -0.117000;	  
	  break;
  	case 'WayWest':
	  dblMoveNorth = 0.0;
	  dblMoveEast = -0.117000;	  
	  break;
  	case 'WayNorthWest':
	  dblMoveNorth = 0.079000;
	  dblMoveEast = -0.117000;	  
	  break;
  	case 'WayNorth':
	  dblMoveNorth = 0.079000;
	  dblMoveEast = 0.00;	  
	  break;
	default:
	  dblMoveNorth = 0.00;
	  dblMoveEast = 0.00;
	  break;
  }
  jQuery('#latitude').val( 1* jQuery('#latitude').val() + dblMoveNorth );
  jQuery('#longitude').val( 1* jQuery('#longitude').val() + dblMoveEast );
  redrawMaps(); 
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
<form action="php-view-post.php" method="post" name="venueEntry" target="_self">
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
else
{
?>
<div class="wall"><h1>Event Venue Database - Bucketing</h1></div>

<map name="localmap">
  <area shape="rect" coords="267,0,400,133" href="#topOfForm" alt="NorthEast" onclick="moveCordinates('NorthEast')">
  <area shape="rect" coords="267,134,400,266" href="#topOfForm" alt="East" onclick="moveCordinates('East')">
  <area shape="rect" coords="267,267,400,400" href="#topOfForm" alt="SouthEast" onclick="moveCordinates('SouthEast')">
  <area shape="rect" coords="134,267,266,400" href="#topOfForm" alt="South" onclick="moveCordinates('South')">
  <area shape="rect" coords="0,267,133,400" href="#topOfForm" alt="SouthWest" onclick="moveCordinates('SouthWest')">
  <area shape="rect" coords="0,134,133,266" href="#topOfForm" alt="West" onclick="moveCordinates('West')">
  <area shape="rect" coords="0,0,133,133" href="#topOfForm" alt="NorthWest" onclick="moveCordinates('NorthWest')">
  <area shape="rect" coords="134,0,266,133" href="#topOfForm" alt="North" onclick="moveCordinates('North')">

  <area shape="rect" coords="223,134,266,178" href="#topOfForm" alt="NorthEast" onclick="moveCordinates('WeeNorthEast')">
  <area shape="rect" coords="223,179,266,222" href="#topOfForm" alt="East" onclick="moveCordinates('WeeEast')">
  <area shape="rect" coords="223,223,266,266" href="#topOfForm" alt="SouthEast" onclick="moveCordinates('WeeSouthEast')">
  <area shape="rect" coords="179,223,222,266" href="#topOfForm" alt="South" onclick="moveCordinates('WeeSouth')">
  <area shape="rect" coords="134,223,178,266" href="#topOfForm" alt="SouthWest" onclick="moveCordinates('WeeSouthWest')">
  <area shape="rect" coords="134,179,178,222" href="#topOfForm" alt="West" onclick="moveCordinates('WeeWest')">
  <area shape="rect" coords="134,134,178,178" href="#topOfForm" alt="NorthWest" onclick="moveCordinates('WeeNorthWest')">
  <area shape="rect" coords="179,134,222,178" href="#topOfForm" alt="North" onclick="moveCordinates('WeeNorth')">
</map>

<map name="regionalmap">
  <area shape="rect" coords="67,0,100,33" href="#topOfForm" alt="NorthEast" onclick="moveCordinates('WayNorthEast')">
  <area shape="rect" coords="67,34,100,66" href="#topOfForm" alt="East" onclick="moveCordinates('WayEast')">
  <area shape="rect" coords="67,67,100,100" href="#topOfForm" alt="SouthEast" onclick="moveCordinates('WaySouthEast')">
  <area shape="rect" coords="34,67,66,100" href="#topOfForm" alt="South" onclick="moveCordinates('WaySouth')">
  <area shape="rect" coords="0,67,33,100" href="#topOfForm" alt="SouthWest" onclick="moveCordinates('WaySouthWest')">
  <area shape="rect" coords="0,34,33,66" href="#topOfForm" alt="West" onclick="moveCordinates('WayWest')">
  <area shape="rect" coords="0,0,33,33" href="#topOfForm" alt="NorthWest" onclick="moveCordinates('WayNorthWest')">
  <area shape="rect" coords="34,0,67,33" href="#topOfForm" alt="North" onclick="moveCordinates('WayNorth')">
</map>

<form action="auth-venue-bucket.php<?=isset($_GET['fn'])?'?fn='.$txtFilename:''?>" method="post" name="venueEntry" target="_self">
  <div id="address">
	<div id="divLocationName">
		<a name"topOfForm"></a><label for="locationName">Name of location</label> 
		<input type="text" name="locationName" id="locationName"<?= !$blnValidPost && isset($_POST['locationName']) ? ' value="'. $_POST['locationName'] .'"': ''?>>
	</div>
	<div id="divAddress">
		<label for="address">Address</label>
		<textarea name="address" id="Address"><?= !$blnValidPost && isset($_POST['address']) ?  $_POST['address'] : ''?></textarea>
	</div>
	<div id="divCity">
		<label for="city">City</label>
		<input type="text" name="city" id="city"<?= !$blnValidPost && isset($_POST['city']) ? ' value="'. $_POST['city'] .'"': ''?>>
	</div>
	<div id="divProvince">
		<label for="province">Province</label>
		<input type="text" name="province" id="province"<?= !$blnValidPost && isset($_POST['province']) ? ' value="'. $_POST['province'] .'"': ''?>>
	</div>
	<div id="divPostalCode">
		<label for="postalCode">Postal Code</label>
		<input type="text" name="postalCode" id="postalCode"<?= !$blnValidPost && isset($_POST['postalCode']) ? ' value="'. $_POST['postalCode'] .'"': ''?>>
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
			<option value="arena" <?=$strIcon=='arena'?' selected':''?>>Hockey arena</option>
			<option value="soccer" <?=$strIcon=='soccer'?' selected':''?>>Soccer field</option>
			<option value="usfootball" <?=$strIcon=='usfootball'?' selected':''?>>Football field</option>
			<option value="baseball" <?=$strIcon=='baseball'?' selected':''?>>Baseball field</option>
			<option value="summercamp" <?=$strIcon=='summercamp'?' selected':''?>>Campground</option>
			<option value="school" <?=$strIcon=='school'?' selected':''?>>School</option>
			<option value="dance_class" <?=$strIcon=='dance_class'?' selected':''?>>Dancing</option>
			<option value="shootingrange" <?=$strIcon=='shootingrange'?' selected':''?>>Range</option>
			<option value="statue" <?=$strIcon=='statue'?' selected':''?>>Point of Interest</option>
			<option value="church" <?=$strIcon=='church'?' selected':''?>>Church</option>
			<option value="tower" <?=$strIcon=='tower'?' selected':''?>>Tower</option>
			<option value="palace" <?=$strIcon=='palace'?' selected':''?>>Castle</option>
			<option value="citywalls" <?=$strIcon=='citywalls'?' selected':''?>>City Walls</option>
			<option value="shopping" <?=$strIcon=='shopping'?' selected':''?>>Shopping mall</option>
			<option value="fuel" <?=$strIcon=='fuel'?' selected':''?>>Gas station</option>
		</select>
		<!-- img src="images/menu-icon-help.gif" height="18px" width="18px" title="Select the icon best suited for this venue.  (Click for more)" id="locationIconInfo" onClick="javascript:jQuery('#locationIconInfoWindow').css('display','block');void(0);" / -->
	</div>
    <div id="divLatitude">
	  <label for="latitude">Latitude</label>
	  <input name="latitude" id="latitude" type="text" value="<?= !$blnValidPost && isset($_POST['geolatitude']) ? $_POST['geolatitude']: '43.5890452'?>">
	  <input name="geolatitude" id="geolatitude" type="hidden" value="<?= !$blnValidPost && isset($_POST['geolatitude']) ? $_POST['geolatitude']: '43.5890452'?>">
    </div>
    <div id="divLongitude">
	  <label for="longitude">Longitude</label>
	  <input name="longitude" id="longitude" type="text" value="<?= !$blnValidPost && isset($_POST['geolongitude']) ? $_POST['geolongitude']: '-79.6441198'?>">
	  <input name="geolongitude" id="geolongitude" type="hidden" value="<?= !$blnValidPost && isset($_POST['geolongitude']) ? $_POST['geolongitude']: '-79.6441198'?>">
    </div>
    <div id="divMapLock">
      <label for="mapLock">Lock map location?</label>
      <input name="mapLock" id="mapLock" type="checkbox" value="maplock">
    </div>
    <img src="http://maps.google.com/maps/api/staticmap?center=43.5934163,-79.6455198&zoom=14&size=400x400&markers=color:0x1A3380%7Csize:tiny%7C43.5954395%2C-79.64688579999999&markers=color:0x2952CC%7Csize:small%7C43.5934163%2C-79.6455198&markers=color:0x3366FF%7Csize:mid%7C43.5904529%2C-79.645155&markers=color:0x7094FF%7C43.5890452%2C-79.6441198&sensor=false" alt="detail map" id="detailMap" width="400" height="400" border="0" usemap="#localmap" >
	<img id="loadingLG" src="images/loading12pt400.gif" class="loading" />
	<img src="http://maps.google.com/maps/api/staticmap?center=43.5890452,-79.6441198&zoom=8&size=100x100&sensor=false" alt="thumbnail map" id="thumbnailMap" width="100" height="100" border="0" usemap="#regionalmap" >
	<img id="loadingTN" src="images/loading16pt100.gif" class="loading" /></div>
  <div id="siteNotes">
	<div id="divPhoneNumber">
		<label for="phoneNumber">Phone Number</label>
		<input type="text" name="phoneNumber" id="phoneNumber"<?= !$blnValidPost && isset($_POST['phoneNumber']) ? ' value="'. $_POST['phoneNumber'] .'"': ''?>></div>
	<div id="divContact">
		<label for="contact">Contact</label>
		<input type="text" name="contact" id="contact"<?= !$blnValidPost && isset($_POST['contact']) ? ' value="'. $_POST['contact'] .'"': ''?>></div>
	<div id="divEmail">
		<label for="email">Email</label>
		<input type="text" name="email" id="email"<?= !$blnValidPost && isset($_POST['email']) ? ' value="'. $_POST['email'] .'"': ''?>></div>
	<div id="divWebSite">
		<label for="webSite">Web site</label>
		<input type="text" name="webSite" id="webSite"<?= !$blnValidPost && isset($_POST['webSite']) ? ' value="'. $_POST['webSite'] .'"': ''?>></div>
	<div id="divExtraNotes">
		<label for="extraNotes">Extra notes</label>
		<textarea name="extraNotes" cols="30" rows="5" id="extraNotes"><?= !$blnValidPost && isset($_POST['extraNotes']) ? $_POST['extraNotes'] : ''?></textarea></div>
  </div>
  <div id="formButtons"> &nbsp; 
    <input name="submit" type="submit" value="Submit" id="submit">  &nbsp;
     &nbsp;
	<input name="reset" type="reset" value="Reset">
	  &nbsp;
	  <?php
	  echo '<input name="filename" id="filename" type="hidden" value="'. $txtFilename .'">'; 
	  ?></div>
  <div id="anonEntry">&nbsp;
    <input name="Previous" id="btnPrevious" type="button" value="Prev" title="Go to previous anonymous entry"> &nbsp; &nbsp; 
    <input name="Delete" id="btnDelete" type="button" value="Del" title="Delete this anonymous entry and get the next one"> &nbsp; 
    <input name="Banish" id="btnBanish" type="button" value="Ban" title="Mark this entry as spam and block all entries from this address"> &nbsp; &nbsp; 
    <input name="Next" id="btnNext" type="button" value="Next" title="Go to next anonymous entry">
  &nbsp; <br>
  <span class="preview" id="anonEntryPreview"><?=$strPreview?></span></div>
  <div id="anotherLink"><a href="anon-venue-entry.php">Anonymous entry</a> &nbsp; &nbsp; <a href="auth-venue-edit.php">Editor</a></div>
</form>
<?php
}
?>
</body>
</html>
