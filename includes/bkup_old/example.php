<?php
session_start();
/*
$admindb = $_SESSION['s_admindb'];
if(isset($_SESSION['s_coyid'])) {
	$coyid = $_SESSION['s_coyid'];
}
$module = $_SESSION['s_module'];
*/
date_default_timezone_set($_SESSION['s_timezone']);
$d = date('Y-m-d');
/*
require_once('../../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;
*/
$dbHost='localhost';
$dbUser="logtracc9";
$dbPass="dun480can";

$dbclt = "sub30";
$bkupfileclt = '../../temp/'.$dbclt.'_'.$d;
$zipfileclt = '../../temp/'.$dbclt.'_'.$d.'.gz';

function compress( $srcFileName, $dstFileName )
{
   // getting file content
   $fp = fopen( $srcFileName, "r" );
   $data = fread ( $fp, filesize( $srcFileName ) );
   fclose( $fp );
  
   // writing compressed file
   $zp = gzopen( $dstFileName, "w9" );
   gzwrite( $zp, $data );
   gzclose( $zp );
}


require('backup_restore.class.php');
$newImport = new backup_restore ($dbHost,$dbclt,$dbUser,$dbPass,'*', $bkupfileclt);

if(isset($_REQUEST['backup'])){

//call of backup function
$message=$newImport -> backup ();
compress( $bkupfileclt, $zipfileclt );

echo $message;
}

if(isset($_REQUEST['restore'])){

//call of restore function
$message=$newImport -> restore ();
echo $message;
}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"  dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Buffer Now (Back up And Restore Script)</title>
</head>

<body>

<table align="center" width="50%"><tr>
<form method='post'>
<td>
<input type="submit"  name="backup" value="I will make Backup">
</td><td>
<input type="submit" name="restore" value="I Will Restore">
</form></td></tr></table>

</body>