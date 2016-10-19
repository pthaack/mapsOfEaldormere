<?php
include 'db-manager.php';
$blnSignedIn=accessAuthenticate();
$arrUser=accessUser(); 
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>View post entries</title>
</head>

<body>
<p><?php echo '<pre>'; print_r($_POST); echo '</pre>'; echo validVenuePost()?'TRUE':'FALSE'; ?></p>  
<?php
if( isset($_FILES['PhotoSourceFile']) )
{ echo '<p>Files:<br/><pre>'; print_r($_FILES); echo '</pre></p>'; }
if( isset($_FILES['PhotoSourceFile']['tmp_name']) )
{
 $strMimeType = mime_content_type($_FILES['PhotoSourceFile']['tmp_name']);
 echo '<p><pre>Mime:'. $strMimeType; echo '</pre></p>'; 
 if( $strMimeType == 'image/jpeg' )
 {echo '<p><pre>Dimensions:'; print_r(getimagesize($_FILES['PhotoSourceFile']['tmp_name'])); echo '</pre></p>';}
}
?>
<p><?php echo '<pre>'; print_r($arrUser); echo '</pre>'; ?></p>  
</body>
</html>
