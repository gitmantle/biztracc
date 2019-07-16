<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
if(isset($_SESSION['s_coyid'])) {
	$coyid = $_SESSION['s_coyid'];
}
$module = $_SESSION['s_module'];

date_default_timezone_set($_SESSION['s_timezone']);
$d = date('Y-m-d');

include_once("../DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$dbHost=$_SESSION['s_server'];
$dbUser="infinint_sagana";
$dbPass="dun480can";

if ($module == 'clt') {
	$dbclt = "infinint_sub".$subscriber;
	$bkupfileclt = '../../temp/infinint_sub'.$subscriber.'_'.$d;
	$zipfileclt = '../../temp/infinint_sub'.$subscriber.'_'.$d.'.gz';
}

if ($module == 'fin') {
	$dbclt = "infinint_fin".$subscriber."_".$coyid;
	$bkupfileclt = '../../temp/infinint_fin'.$subscriber.'_'.$coyid.'_'.$d;
	$zipfileclt = '../../temp/infinint_fin'.$subscriber.'_'.$coyid.'_'.$d.'.gz';
}

if ($module == 'log') {
	$dbclt = "infinint_log".$subscriber."_".$coyid;
	$bkupfileclt = '../../temp/infinint_log'.$subscriber.'_'.$coyid.'_'.$d;
	$zipfileclt = '../../temp/infinint_log'.$subscriber.'_'.$coyid.'_'.$d.'.gz';
}

if ($module == 'infinint_htb') {
	$dbclt = "htb".$subscriber."_".$coyid;
	$bkupfileclt = '../../temp/infinint_htb'.$subscriber.'_'.$coyid.'_'.$d;
	$zipfileclt = '../../temp/infinint_htb'.$subscriber.'_'.$coyid.'_'.$d.'.gz';
}

$db->closeDB();

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



?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Database Backup</title>

<script>
function downloadsql() {
	var bkupfile = "<?php echo $zipfileclt; ?>";
	
	window.location.href=bkupfile; 
}


</script>


</head>
<body>
<table align="center" width="350">
  <form method='post'>
    <tr>
      <td align="center">1.</td>
      <td align="center"><input type="submit"  name="backup" value="Backup Database" /></td>
    </tr>
    <tr>
      <td align="center">2.</td>
      <td align="center"><input type="button" name="download" id="download" value="Download to your PC" onclick="downloadsql();return false;" /></td>
    </tr>
  </form>
</table>
</body>
