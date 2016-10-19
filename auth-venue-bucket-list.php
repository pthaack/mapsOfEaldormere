<?php
	include 'db-manager.php';
	$blnSignedIn=accessAuthenticate();
	if( strpos(getcwd(),'/data') === false ) {
		chdir( 'data' );
		}
	$txtFilename = '';
	$txtDeleteFile = '';
	if( isset($_GET['fn']) ) 
	{
		$txtFilename = $_GET['fn'];
	}
	if( isset($_POST['delete']) ) 
	{
		$txtDeleteFile = $_POST['delete'];
	}
	$txtFileList = '' . chr( 13 ) . chr( 10 );
	if( $objDir = opendir( '.' ) )
	{	
		/* This is the correct way to loop over the directory. -- http://php.net/manual/en/function.readdir.php -- */
		while (false !== ($strEntry = readdir($objDir))) 
		{
			if(  $txtDeleteFile == $strEntry && substr( $strEntry, 0, 4 ) == 'anon' )
			{
				if( unlink( $txtDeleteFile ) )
				//if( rename( $txtDeleteFile, 'auth' . substr( $txtDeleteFile, 4) ) )
				{
					$txtFileList .= '<deleted>'. $strEntry . '</deleted>' . chr( 13 ) . chr( 10 );
					//$txtFileList .= '<rename>'. 'auth' . substr( $txtDeleteFile, 4) . '</rename>' . chr( 13 ) . chr( 10 );
				}
				else
				{
					$txtFileList .= '<notdeleted>'. $strEntry . '</notdeleted>' . chr( 13 ) . chr( 10 );
				}
			}
			elseif( substr( $strEntry, 0, 4 ) == 'anon' )
			{
				$txtFileList .= '<fileName>'. $strEntry . '</fileName>' . chr( 13 ) . chr( 10 );
				if(  $txtFilename == '' && substr( $strEntry, 0, 4 ) == 'anon' )
				{
					$txtFilename = $strEntry;
				}
			}
		}

	}
	if(  $txtFilename != '' )
	{
		$txtFileContent = file_get_contents( $txtFilename );
		if( strpos( $txtFileContent, '?>' ) > 0 )
		{
			$txtFileContent = substr( $txtFileContent, strpos( $txtFileContent, '?>' )+2 );
		}
	}

echo '<?xml version="1.0" encoding="iso-8859-1"?>';
?><bucketFiles>
  <bucketList><?= $txtFileList ?>
  </bucketList>
  <anonFile>
    <fileName><?= $txtFilename ?></fileName>
	<fileContent><?= $txtFileContent ?></fileContent>
  </anonFile>
</bucketFiles>