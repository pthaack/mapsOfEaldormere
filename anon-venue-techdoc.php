<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<?php 
define('ROOTPATH', 'http://'.$_SERVER['SERVER_NAME'].dirname($_SERVER['PHP_SELF']));
?><head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Technical Documentation</title>
<!-- http://maps.google.com/maps/api/geocode/json?sensor=false&address=Mississauga -->
<style type="text/css"><!--
div { vertical-align: top }
div#anonEntry {position: fixed; left: 50%; top: 2em; height: 32em; overflow-y: auto; }
label {width: 140px; display: inline-block;}
.loading {position:absolute; top:0px; left:0px; z-index:1; display:none; filter: alpha(opacity=30);}
#map-container { width: 100%; height: 400px; }
#googleMap { width: 400px; height: 400px; display: block }
/* grabbed from VestYorvik website */
.wall, .wall TD, .wall TH {background-image:url('images/bkgrnd.gif'); background-color:#ffffd8 }
body, TD {font-family:"trebuchet ms"; background-color:#ffffd8 }
map {color:#993333}
h1 {font-family:Georgia; color:#000080}
h2 {font-family:Georgia; color:#000080}
p.footer {font-size:x-small; text-align:center}
// -->
</style>
</head>

<body>
<h1 class="wall">Technical Documentation</h1>
<p>The event site database is currently in its first draft stage.&nbsp; It is "functional" for data entry entry and editing, with reports coming soon.&nbsp; Please, use, abuse, take notes, observe and report.
<br />Attempts were made to use other systems to manage a list of venue addresses.&nbsp; <br />Excel has the problem with version control right out of the gate, and since 2011, compatibility has become an issue with the .xlsx extension.
<br />Google Sheets was pondered and started.&nbsp; Almost immediately, Google added and removed features that halted further development out of frustration.
<br />
We also looked at Google Maps which some groups have leveraged to display <a href="https://www.google.com/maps/d/viewer?hl=en&mid=1is_FKhILPKi_j_GfQgJ4upMrfgs"  target="_blank" >fight</a> and <a href="https://www.google.com/maps/d/u/0/viewer?hl=en&authuser=0&mid=1ptxJ2okS76-Rnx24EFyjMsm94qI"  target="_blank" >fence</a> practices.&nbsp; We felt that privacy issues were not being met.
<br />
Research was looked at using a PHP framework, like a CMS or WordPress with extensions.&nbsp; I just thought that they were way too complex for our needs and could come up with something adequate more quickly.
<br />The result is the work of months of thought, a first attempt, more thought, some peer pressure, a muse, and weeks of coding.</p>
<h2>Data Structure</h2>
<p>Data is stored in either in a MySQL database or as files in a data folder. 
<br />Only registered users have direct access to modules that use the MySQL database, and additions or changes made by them are signed with their user id.
<br />Data entered on the Anonymous page is stored as XML documents.&nbsp; Although anonymous, each file contains IP address information for abuse tracking.</p>
<ul>
<li>&mdash; header<div>
Each venue is defined by what-you-call-it and where-is-it.&nbsp; Extra information, who to contact, and how to contact them is just bonus.&nbsp; Anything more is just details.
<br />Users can add, edit, and delete to their hearts&#39; content.&nbsp; Deleted venues can be restored by the DB admin.</div></li>
<li>&mdash; detail<div>
Conversations (private/public), amenities, and photos are stored together in a separate table from the venue header data.
<br />Conversations are meant to record that the venue was contacted on this date, they talked to this person about this venue on that date.&nbsp; What they discussed is a matter of preference whether they want to make public or private.&nbsp; Some venues give special rates to not-for-profits that they don&#39;t want out in the wild.
<br />Amenities are a room-by-room accounting of dimensions and features.&nbsp; Checkboxes are provided to allow searches on features.
<br />Photos must be JPEG images because they are reduced to 800&times;600 to conserve space.&nbsp; If the file is not a JPEG, it will be rejected.&nbsp; Don&#39;t try to rename a file to a .jpg; it won&#39; work. 
</div></li>
<li>&mdash; user list<div>
Current volume expectations are about 50 users with one or two new or retired users per month.&nbsp; At those volumes, the data administrator can directly maintain user access.
<br />The username and password, privileges and permissions are stored in the clear, subject to technical review.
<br />To enter the system, the user needs to enter their username and password just once, and they have access until they close their browser. 
</div></li>
<li>&mdash; anonymous entries<div>
Users in a hurry, or those without a username, may use the anonymous entry page to make a quick post to get the ball rolling for a new entry.&nbsp; There is a description field and a bunch of buttons to confirm specific amenities.
<br />The data is stored in an XML file.&nbsp; The description field in the file is called <em>txtBodyHtmlArea</em>, and the data gathered from the click buttons are stored in a field called <em>txtClickInfo</em>.&nbsp; Also stored in the file are countermeasures from spam and abuse.&nbsp; I gather the IP address and post a cookie code.
</div></li>
<li>&mdash; photos<div>Photos must be in JPEG format.&nbsp; The file is reduced to 800&times;600 to conserve space.&nbsp; If the file is not a JPEG, it will be rejected.&nbsp; Don&#39;t try to rename a file to a .jpg; it won&#39;t work.</div>
</li>
</ul>

<h2>Data Entry</h2>
<ul>
<li>&mdash; <a href="<?=ROOTPATH?>/anon-venue-entry.php" target="_blank">Anonymous submission</a><div>Name and describe the venue.&nbsp; Click a few buttons.&nbsp; Hit submit.&nbsp; What could be easier?</div></li>
<li>&mdash; <a href="<?=ROOTPATH?>/auth-venue-bucket.php" target="_blank">Bucket entry</a><div>Each anonymously submitted venue must be entered into the system by a registered user.&nbsp; Just confirm that the address is not already in the system and move on.&nbsp; If the entry is a duplicate, delete it and go to the next one.&nbsp; If the entry is rude or nonsense, banish the user.&nbsp; They will not be able to make another entry until they apologize.&nbsp; Apologies are recorded.&nbsp; The data administrator has the power to exile the user if they have to apologize a lot.</div></li>
<li>&mdash; <a href="<?=ROOTPATH?>/auth-venue-edit.php" target="_blank">Add-edit entry</a><div>Full featured data entry.&nbsp; Change GPS location.&nbsp; Add conversation data.&nbsp; Add amenities.&nbsp; Add photos.&nbsp; Then delete them.<br>
If you didn&#39;t make the entry, then you cannot edit them, but you can add to them. </div>
</li>
</ul>

<h2>Security</h2>
<p>I have taken certain precautions to prevent the data gathered from getting into the wild.   
<br />In my mind, this is not our data.&nbsp; We have gathered it from friends.&nbsp; We have to protect it on their behalf.&nbsp;  
</p>
<ul>
<li>&mdash; log in authentication<div></div></li>
<ul>
<li>&mdash; Basic Access Authentication<div>Basic Access Authentication uses a browser request for username and password to access the site.&nbsp; It works fine for Chrome, Edge, and Safari, but it is considered obsolete by Facebook and Firefox.&nbsp; The problem is that the username and password are transmitted in the clear allowing sniffers to gain access to the site.</div></li>
<li>&mdash; Digest Access Authentication<div>Digest Access Authentication is the next step up from basic access authentication.&nbsp; It is more secure and reportedly compatible in all new browsers that currently block Basic Access Authentication.&nbsp; This is the type that is implemented pending the technical review.</div>
</li>
</ul>
<li>&mdash; private conversations <div></div></li>
<ul>
<li>&mdash; AES encryption<div>Conversations considered private are encrypted using AES.&nbsp; This uses a passphrase as a key to lock or unlock any conversation that needs to be kept private.&nbsp; The passphrase is not saved anywhere on the server.&nbsp; Any encoded data needs to be decoded with the original passphrase.</div></li>
<li>&mdash; cookies<div>It is possible that the passphrase is stored in cookies.&nbsp; Don&#39;t toss your cookies.</div></li>
<li>&mdash; metadata<div>Since the system cannot examine encrypted for the purpose of searching and filtering, certain standard check boxes are stored in the clear. Search for meeting spaces, fight practices, or large outdoor events to your heart&#39;s content. </div>
</li>
</ul>
<li>&mdash; anonymous access<div>All file names are obfuscated to hinder non-user access. Files that contain the data gathered from anonymous entries, as well as the photos, are stored using a non-sequential naming convention.&nbsp; The folder is likewise not accessible as a folder.&nbsp; But, if you happened to know the file name, you probably already have signed in to get it. </div>
</li>
</ul>

<h2>Future Development</h2>
<ul>
<li>&mdash; Improved map interface <strong>(DONE)</strong>
  <div>At the time of the initial design of the page, I got the impression that the API key was <a href="http://stackoverflow.com/questions/8775034/google-maps-api-do-i-need-to-buy" target="_blank" >not free</a> and compicated to implement.&nbsp; So I made my own interface that leveraged the anonymous map access.&nbsp; It worked the way the old MapQuest interface used to work.&nbsp; Now that I have my own API key, I have started looking into using the map interface that you may be more familiar with.&nbsp; But the object is not at all the same so there will be some tweeking of the interface before it is ready.</div></li>
<li>&mdash; Improved security <strong>(DONE)</strong>
  <div>Basic Access Authentication is obsolete, already.&nbsp; It works in older browsers like Chrome, Internet Explorer, and Safari, but Firefox and Facebook reject it outright.&nbsp; Digest Access Authentication is live.&nbsp; There was a problem where some apps did not perform the log in procedure, but the login failure page displays the workaround.&nbsp; <br />The original plan was to secure the site using <a href="https://en.wikipedia.org/wiki/Two-factor_authentication" target="_blank">two factor authentication</a> which is well demonstrated in this <a href="https://daplie.github.io/browser-authenticator/" target="_blank">demonstration</a>.&nbsp; I just felt that it would be too over-the-top for our purposes, but something to look into if this gets too big.</div></li>
<li>&mdash; Reports<div>The whole plan for this database was to get a set of sites that we can contact for events of a particular character.&nbsp; Security was job number one, as we don&#39;t want to let a price list or certain other information out into the wild.&nbsp; I will need to work with someone knowlegeable in visual design regarding how to build this.</div></li>
<li>&mdash; Menu<div>There are currently only four pages planned in the site.&nbsp; I have put all the links in a floor menu.&nbsp; We&#39;ll have to see if that is a problem. </div>
</li>
<li>&mdash; Technical and Practical review<div>These pages are a first draft of a venue database and a contact management system.&nbsp; I have put forth a best effort based on what some people have wanted to see in the site, but after trying it and seeing what they do and do not like about the methodologies, changes will need to be made.&nbsp; <br>
  At some point, the site will be released to the kingdom for hosting on their site. It is currently being hosted on my personal site.&nbsp; </div>
</li>
</ul>

<p>Built by <a href="https://web.facebook.com/flip.young" target="_blank">Philip Young</a> inspired by a <a target="_blank"	href="https://www.facebook.com/groups/2261836735?view=permalink&id=10153056354616736" >post</a> on Facebook.</p>
</body>
</html>
