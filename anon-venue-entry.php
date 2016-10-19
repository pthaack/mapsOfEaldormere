<?
$dteExpire=time()+60*60*24*365.25;
if (isset($_COOKIE['secretId']))
{
	setcookie('secretId', $_COOKIE['secretId'] , $dteExpire);
}
else
{
	setcookie('secretId', uniqid( false ), $dteExpire);
}
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Anonymous Entry</title>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>   

<link rel="stylesheet" href="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.css">
<link rel="stylesheet" href="anon-entry-style.css" >
<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="http://code.jquery.com/mobile/1.4.2/jquery.mobile-1.4.2.min.js"></script>

<script type="text/javascript"><!-- 
/*

Tasks: 
Create nojavascript form (OK) 
Create PHP action page to create XML file (ok) 
Create jQuery scripts. Default to nojavascript if jQuery is down. (ok)
Create PHP AJAX page to create and update XML file 
Create IP capture (server and client), session ID and cookie 
Banish.  You know who you are. 

*/

$(function()
{
if( jQuery('body').innerWidth() > 1000 )
{
	console.log( 'body innerWidth: ' +  jQuery('body').innerWidth()  );
	jQuery('body').css("width","50em"); 
	jQuery('#anon-entry').css("width","40em"); 

alert('There will be no password-secured log-in here.  That\'s not the intention.\nFormatting is in flux, too.\nIn the meatime, see what fields are missing.  I thought that the free form field would be adequate for address and amenities.')
}
jQuery('#anon-entry').css("height","10em"); 
var eleApg = document.createElement('input');
    eleApg.type='button';
	eleApg.name='apologise';
	eleApg.value='Apologise';
	eleApg.id="apologize";
//	jQuery('#Reset').after(' &nbsp; &nbsp; ',eleApg); 
	
var eleIPCJ = document.createElement('input');
    eleIPCJ.type='hidden';
	eleIPCJ.name='IPClientJ';
	eleIPCJ.value='';
	eleIPCJ.id="IPClientJ";
//	$.getJSON("http://jsonip.appspot.com?callback=?",
//      function(data){
//        try {
//		  eleIPCJ.value=data.ip;
//            }
//        catch (e) {
          $.getJSON("http://jsonip.com?callback=?",
				function(data){
					eleIPCJ.value=data.ip;
				  });
//		}
//	  });
	jQuery('#IPRemoteHost').before(eleIPCJ); 

var eleRIP = document.createElement('input');
    eleRIP.type='hidden';
	eleRIP.name='IPResponseIP';
//	eleRIP.value=response.ip;
	eleRIP.id="IPResponseIP";
	jQuery('#IPRemoteHost').before(eleRIP);  


/* ***********************************************************    
Begin apology set up
**************************************************************    
	
jQuery('#Reset').fadeOut(1000); 
jQuery('#submit').fadeOut(2000); 
jQuery('#anon-entry').fadeOut(3000); 
jQuery('#anon-ins').html('You have violated the terms of agreement (TOA) by repeatedly posting frivolous posts with non-information.&nbsp; You are banned until you apologise.'); 
jQuery('#apologize').click(function()
	{
	jQuery('#Reset').fadeIn(1000);
	jQuery('#submit').fadeIn(2000);
	jQuery('#anon-entry').fadeIn(3000);
	jQuery('#anon-ins').html('Please, provide the name, address, phone number, website of the venue.&nbsp; Also, include type of venue (meeting space, event location, symposium, camping), number of rooms, restrictions, et cetera.&nbsp; <br>Enter as much as you know.');
	jQuery(this).fadeOut(4000);
var eleApgy = document.createElement('input');
    eleApgy.type='hidden';
	eleApgy.name='apology';
	eleApgy.value='please';
	eleApgy.id="apology";
	jQuery('#IPRemoteHost').before(eleApgy); 
	}); 
/* ***********************************************************    
  End apology set up
**************************************************************    */ 
	
// Redirect form to real destination.
$('#anon-form').get(0).setAttribute('action', '<?=( isset($_GET['test']) ? 'php-view-post.php' : 'anon-venue-xml-post.php' )?>');
});

// -->
</script>

</head>

<body class="venue-sites">
<h2 data-role="header" class="ui-content" >Tell me about this venue</h2>
<p id="anon-ins" class="ui-content" data-role="main" >Please, provide the name, address, phone number, website of the venue.&nbsp; Also, include type of venue (meeting space, event location, symposium, camping), number of rooms, restrictions, et cetera.&nbsp; <br>
Enter as much as you know.</p>
<form action="anon-venue-xml-post.php" method="post" name="form1" id="anon-form" target="_self">
	<div>
	  <textarea id="anon-entry" name="anon-entry" class="boxentry" ></textarea>
	</div>
	<div>

	<div data-role="controlgroup" data-type="horizontal" >
<label for="bigEvent">big event</label><input type="checkbox" id="bigEvent" name="addInfobigEvent" value="big event"> 
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="smallEvent">small event</label><input type="checkbox" id="smallEvent" name="addInfosmallEvent" value="small event">    
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="camping">camping</label><input type="checkbox" id="camping" name="addInfocamping" value="camping">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="meetings">meetings</label><input type="checkbox" id="meetings" name="addInfomeetings" value="meetings">     
	</div>
	<h3>fighting</h3>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="fightingIndoor">indoor</label><input type="checkbox" id="fightingIndoor" name="addInfofightingIndoor" value="fighting indoor">    <label for="fightingOutdoor">outdoor</label><input type="checkbox" id="fightingOutdoor" name="addInfofightingOutdoor" value="fighting outdoor"> 
	</div>
	<h3>fencing</h3>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="fencingIndoor">indoor</label><input type="checkbox" id="fencingIndoor" name="addInfofencingIndoor" value="fencing indoor">    <label for="fencingOutdoor">outdoor</label><input type="checkbox" id="fencingOutdoor" name="addInfofencingOutdoor"value="fencing outdoor">     
	</div>
	<h3>archery</h3>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="archeryIndoor">indoor</label><input type="checkbox" id="archeryIndoor" name="addInfoarcheryIndoor" value="archery indoor">    <label for="archeryOutdoor">outdoor</label><input type="checkbox" id="archeryOutdoor" name="addInfoarcheryOutdoor" value="archery outdoor"> 
	</div>
	<h3>classes</h3>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="classesIndoor">indoor</label><input type="checkbox" id="classesIndoor" name="addInfoclassesIndoor" value="classes indoor">    <label for="classesOutdoor">outdoor</label><input type="checkbox" id="classesOutdoor" name="addInfoclassesOutdoor" value="classes outdoor">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="specialty" title="The venue has a unique quality, e.g. equestrian, paintball, tall-ship, etc.">specialty</label><input type="checkbox" id="specialty" name="addInfospecialty" value="specialty" title="The venue has a unique quality, e.g. equestrian, paintball, tall-ship, etc." >     
	</div>
	<h3>merchanting</h3>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="merchantsIndoor">indoor</label><input type="checkbox" id="merchantsIndoor" name="addInfomerchantsIndoor" value="merchants indoor">    <label for="merchantsOutdoor">outdoor</label><input type="checkbox" id="merchantsOutdoor" name="addInfomerchantsOutdoor" value="merchants outdoor"> 
	</div>
	<h3>kitchen</h3>
	<div data-role="controlgroup" data-type="horizontal" >     
    <label for="kitchenstove">stove</label><input type="checkbox" id="kitchenstove" name="addInfokitchenstove" value="kitchenstove">    <label for="kitchenoven">oven</label><input type="checkbox" id="kitchenoven" name="addInfokitchenoven" value="kitchen oven">    <label for="kitchenwarmer">warmer</label><input type="checkbox" id="kitchenwarmer" name="addInfokitchenwarmer" value="kitchen warmer">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="kitchenfridge">fridge</label><input type="checkbox" id="kitchenfridge" name="addInfokitchenfridge" value="kitchen fridge">    <label for="kitchenfreezer">freezer</label><input type="checkbox" id="kitchenfreezer" name="addInfokitchenfreezer" value="kitchen freezer">    <label for="kitchendishwasher">dish washer</label><input type="checkbox" id="kitchendishwasher" name="addInfokitchendishwasher" value="kitchen dish washer">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="electricity">electricity</label><input type="checkbox" id="electricity" name="addInfoelectricity" value="electricity">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="water">drinking water</label><input type="checkbox" id="water" name="addInfowater" value="water">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="bathroom">bathroom</label><input type="checkbox" id="bathroom" name="addInfobathroom" value="bathroom">    <label for="changeroom">change room</label><input type="checkbox" id="changeroom" name="addInfochangeroom" value="change room">    <label for="shower">shower</label><input type="checkbox" id="shower" name="addInfoshower" value="shower">     
	</div>
	<h3>parking</h3>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="parkingonsite">on-site</label><input type="checkbox" id="parkingonsite" name="addInfoparkingonsite" value="on-site parking">    <label for="parkingpaylot">pay lot</label><input type="checkbox" id="parkingpaylot" name="addInfoparkingpaylot" value="pay lot parking">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="parkingstreetfree">street (free)</label><input type="checkbox" id="parkingstreetfree" name="addInfoparkingstreetfree" value="street parking (free)">    <label for="parkingstreetmetered">street (metered)</label><input type="checkbox" id="parkingstreetmetered" name="addInfoparkingstreetmetered" value="street parking (metered)">     
	</div>
	<h3>transit</h3>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="transitbus">bus</label><input type="checkbox" id="transitbus" name="addInfotransitbus" value="transit bus">    <label for="transitsubway">subway</label><input type="checkbox" id="transitsubway" name="addInfotransitsubway" value="transit subway">    <label for="transittrain">train</label><input type="checkbox" id="transittrain" name="addInfotransittrain" value="transit train">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="loadingzone">loading zone</label><input type="checkbox" id="loadingzone" name="addInfoloadingzone" value="loading zone">    <label for="loadinginfront">in front</label><input type="checkbox" id="loadinginfront" name="addInfoloadinginfront" value="loading in front">    <label for="loadingoutback">out back</label><input type="checkbox" id="loadingoutback" name="addInfoloadingoutback" value="loading out back">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="wheelchairaccessible">wheel-chair accessible</label><input type="checkbox" id="wheelchairaccessible" name="addInfowheelchairaccessible" value="wheel-chair accessible">    <label for="accessibleelevator">elevator</label><input type="checkbox" id="accessibleelevator" name="addInfoaccessibleelevator" value="elevator">     
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
    <label for="accessibleramp">ramp</label><input type="checkbox" id="accessibleramp" name="addInfoaccessibleramp" value="ramp">    <label for="accessiblenoStairs">no stairs</label><input type="checkbox" id="accessiblenoStairs" name="addInfoaccessiblenoStairs" value="no stairs"> 
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="havePhotos">&ldquo;I have photos&rdquo;</label><input type="checkbox" id="havePhotos" name="addInfohavePhotos" value="I have photos">
	</div>
	<div data-role="controlgroup" data-type="horizontal" >
<label for="haveFloorPlans">&ldquo;I have floor plans&rdquo;</label><input type="checkbox" id="haveFloorPlans" name="addInfohaveFloorPlans" value="I have floor plans">
	</div>

	</div>
	<div data-role="controlgroup" data-type="horizontal" >
   &nbsp; 
   <input type="submit" id="submit" name="Submit" value="Submit">
   &nbsp; &nbsp; 
    <input name="Reset" type="reset" id="Reset" value="Reset"> 
	&nbsp;
	<input type="hidden" name="IPRemoteHost" id="IPRemoteHost" value="<?= $_SERVER['REMOTE_HOST'] ?>">
	<input type="hidden" name="IPRemoteAddr" id="IPRemoteADDR" value="<?= $_SERVER['REMOTE_ADDR'] ?>">
	<input type="hidden" name="IPLocalAddr" id="IPLocalAddr" >
	</div>
</form>
<p>&nbsp;  </p><!--
/* ***********************************************************    
Begin LOCAL IP Lookup
**************************************************************    */  -->
<script>

// NOTE: window.RTCPeerConnection is "not a constructor" in FF22/23
var RTCPeerConnection = /*window.RTCPeerConnection ||*/ window.webkitRTCPeerConnection || window.mozRTCPeerConnection;

if (RTCPeerConnection) (function () {
    var rtc = new RTCPeerConnection({iceServers:[]});
    if (window.mozRTCPeerConnection) {      // FF needs a channel/stream to proceed
        rtc.createDataChannel('', {reliable:false});
    };
    
    rtc.onicecandidate = function (evt) {
        if (evt.candidate) grepSDP(evt.candidate.candidate);
    };
    rtc.createOffer(function (offerDesc) {
        grepSDP(offerDesc.sdp);
        rtc.setLocalDescription(offerDesc);
    }, function (e) { console.warn("offer failed", e); });
    
    
    var addrs = Object.create(null);
    addrs["0.0.0.0"] = false;
    function updateDisplay(newAddr) {
        if (newAddr in addrs) return;
        else addrs[newAddr] = true;
        var displayAddrs = Object.keys(addrs).filter(function (k) { return addrs[k]; });
        jQuery('#IPResponseIP').val( displayAddrs.join(" or perhaps ") || "n/a" );
    }
    
    function grepSDP(sdp) {
        var hosts = [];
        sdp.split('\r\n').forEach(function (line) { // c.f. http://tools.ietf.org/html/rfc4566#page-39
            if (~line.indexOf("a=candidate")) {     // http://tools.ietf.org/html/rfc4566#section-5.13
                var parts = line.split(' '),        // http://tools.ietf.org/html/rfc5245#section-15.1
                    addr = parts[4],
                    type = parts[7];
                if (type === 'host') updateDisplay(addr);
            } else if (~line.indexOf("c=")) {       // http://tools.ietf.org/html/rfc4566#section-5.7
                var parts = line.split(' '),
                    addr = parts[2];
                updateDisplay(addr);
            }
        });
    }
})(); else {
    jQuery('#IPResponseIP').val( '<code>ifconfig | grep inet | grep -v inet6 | cut -d\" \" -f2 | tail -n1</code>In Chrome and Firefox your IP should display automatically, by the power of WebRTCskull.');
}

</script>  <!--
/* ***********************************************************    
  End LOCAL IP Lookup
**************************************************************    */ -->
</body>
</html>