<?php 
echo '<!DOCTYPE html>';
echo '<html xmlns="http://www.w3.org/1999/xhtml">';
echo '<head>';
echo '<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />';
echo '<title>Anonymous Entry</title>';
echo '<link rel="stylesheet" href="anon-entry-style.css" >';
echo '</head>';
echo '<body>';

/* *********************************************************** 
Validate
Read server file list
Write file
Return: Write success+message 
	/ Write failure+message 
	/ Request to data
// $xmlReport->asXML( 'rpt-Invoice02.xml' );   // method to save XML
	
*********************************************************** */

// If just minimum data fields, return new file request OR return old file exists.
$blnMinumumData = true;
$blnError = false;
$blnFileFound = false;
$txtEntry = '';
$txtFilename = 'anon'. uniqid( false ) .'.xml';
$txtBodyHtmlArea = '';
$txtLANIP = '';
$txtWANIP = '';
/*
Array
(
    [anon-entry] => name, address, phone number
    [Submit] => Submit
    [IPClientJ] => 199.119.233.169
    [IPResponseIP] => 192.168.33.238 or perhaps 192.168.69.1 or perhaps 192.168.134.1
    [apology] => please
    [IPRemoteHost] => 
    [IPRemoteAddr] => 199.119.233.169
    [IPLocalAddr] => 
)
*/

if( isset($_POST['anon-entry']) )
{
  $txtBodyHtmlArea = $_POST['anon-entry'];
}
else
{
  $blnError = true;
}

if( isset($_POST['IPClientJ']) && isset($_POST['IPRemoteAddr']) )
{
  $txtWANIP = ( $_POST['IPClientJ'] != '' ? $_POST['IPClientJ'] : $_POST['IPRemoteAddr'] );
}
elseif( isset($_POST['IPRemoteAddr']) )
{
  $txtWANIP = $_POST['IPRemoteAddr'];
}
else
{
  $blnError = true;
}

if( isset($_POST['IPResponseIP']) && isset($_POST['IPRemoteHost']) )
{
  $txtLANIP = ( $_POST['IPResponseIP'] != '' ? $_POST['IPResponseIP'] : $_POST['IPRemoteHost'] );
}
elseif( isset($_POST['IPRemoteHost']) && isset($_POST['IPLocalAddr']) )
{
  $txtLANIP = ( $_POST['IPRemoteHost'] != '' ? $_POST['IPRemoteHost'] : $_POST['IPLocalAddr'] );
}
else
{
  $blnError = true;
}
$strClickInfo = '';
foreach($_POST as $strKey => $strValue) {
  if( strpos( $strKey, 'addInfo' ) === 0 )
  {
    $strClickInfo .= $strValue . chr(13) . chr(10);
  }
  echo '<!-- '. $strKey . ' -->'  . chr(13) . chr(10);
}

  if( $blnError != true )
  {
    $xmlOutContent = '<?xml version="1.0" encoding="iso-8859-1"?>'  . chr( 13 ) . chr( 10 ) 
					. '<entryLines>' . chr( 13 ) . chr( 10 ) 
					. '<txtBodyHtmlArea>'.htmlspecialchars( htmlentities( $txtBodyHtmlArea ) ) .'</txtBodyHtmlArea>'  . chr( 13 ) . chr( 10 ) 
					. '<txtClickInfo>'.htmlspecialchars( htmlentities( $strClickInfo ) ) .'</txtClickInfo>'  . chr( 13 ) . chr( 10 ) 
					. '<LANIP>'.htmlspecialchars( htmlentities( $txtLANIP ) ) .'</LANIP>'  . chr( 13 ) . chr( 10 ) 
					. '<WANIP>'.htmlspecialchars( htmlentities( $txtWANIP ) ) .'</WANIP>'  . chr( 13 ) . chr( 10 ) 
					. (isset($_COOKIE['secretId']) ? '<SECRET>'.$_COOKIE['secretId'].'</SECRET>' .chr(13).chr(10) : '' )
					. '<BROWSER>'. str_replace( chr( 13 ) . chr( 10 ), '<br />', htmlspecialchars( $_SERVER['HTTP_USER_AGENT'] ) ) .'</BROWSER>'  . chr( 13 ) . chr( 10 ) 
					. '</entryLines>'; 
					
	if( strpos(getcwd(),'/data') === false ) {
		chdir( 'data' );
		}
    $docOutFile = fopen( $txtFilename, 'x' );
	fwrite( $docOutFile, $xmlOutContent );
	fclose( $docOutFile );

    echo "<h3>Thank you for letting us know.</h3>";	    
    echo "<div><em>" . str_replace( chr( 13 ) . chr( 10 ), '<br />', htmlspecialchars( $txtBodyHtmlArea ) ) . "</em></div>";	    
  }
  else
  {
    echo "<h3>Epic fail.</h3>";	    
    echo "<div><em>No data received.</em></div>";	      
    echo '<div>'. str_replace( chr( 13 ) . chr( 10 ), '<br />', htmlspecialchars( $_SERVER['HTTP_USER_AGENT'] ) ) .'</div>';	    
  }	
  echo '<div> &nbsp; <br /> &nbsp; <a href="anon-venue-entry.php">&lt;&mdash; Go Back</a> </div>';	    
	

echo '</body>';
echo '</html>';
 ?>