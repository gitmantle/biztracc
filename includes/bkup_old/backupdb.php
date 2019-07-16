<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
if(isset($_SESSION['s_coyid'])) {
	$coyid = $_SESSION['s_coyid'];
}
$module = $_SESSION['s_module'];


include_once("../DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$db->query("select timezone from subscribers where subid = :subid");
$db->bind(':subid', $subid);
$row = $db->single();
extract($row);

date_default_timezone_set($timezone);
$d = date('Y-m-d');

$dbHost=$_SESSION['s_server'];
$dbUser="infinint_sagana";
$dbPass="dun480can";

if ($module == 'clt') {
	$dbclt = "infinint_sub".$subscriber;
	$bkupfileclt = '../../temp/infinint_sub'.$subscriber.'_'.$d.'.sql';
	$zipfileclt = '../../temp/infinint_sub'.$subscriber.'_'.$d.'.gz';
}

if ($module == 'fin') {
	$dbclt = "infinint_fin".$subscriber."_".$coyid;
	$bkupfileclt = '../../temp/infinint_fin'.$subscriber.'_'.$coyid.'_'.$d.'.sql';
	$zipfileclt = '../../temp/infinint_fin'.$subscriber.'_'.$coyid.'_'.$d.'.gz';
}

if ($module == 'log') {
	$dbclt = "infinint_log".$subscriber."_".$coyid;
	$bkupfileclt = '../../temp/infinint_log'.$subscriber.'_'.$coyid.'_'.$d.'.sql';
	$zipfileclt = '../../temp/infinint_log'.$subscriber.'_'.$coyid.'_'.$d.'.gz';
}

if ($module == 'infinint_htb') {
	$dbclt = "htb".$subscriber."_".$coyid;
	$bkupfileclt = '../../temp/infinint_htb'.$subscriber.'_'.$coyid.'_'.$d.'.sql';
	$zipfileclt = '../../temp/infinint_htb'.$subscriber.'_'.$coyid.'_'.$d.'.gz';
}

$db->closeDB();

function createsql($bkupfileclt,$dbUser,$dbPass,$dbclt) {
	$fp = @fopen( $bkupfileclt, 'w+' );
	if( !$fp ) {
	
		echo 'Impossible to create <b>'. $bkupfileclt .'</b>, please manually create one and assign it full write privileges: <b>777</b>';
		exit;
	}
	fclose( $fp );
	
	$command = 'mysqldump -u '. $dbUser .' -p'. $dbPass .' '. $dbclt .' > '. $bkupfileclt;
	
	$output = array(); 
	
	exec( $command, $output, $worked );
	
	switch( $worked ) {
	
		case 0:
			echo 'Successfully backed up - now download to your PC';
			break;
	
		case 1:
			echo 'Warning';
			break;
	
		case 2:
			echo 'Error';
			break;
	}	
	
}

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

if(isset($_REQUEST['backup'])){

	//call of backup function
	$message=createsql($bkupfileclt,$dbUser,$dbPass,$dbclt);
	echo $message;
	
	compress( $bkupfileclt, $zipfileclt );
	
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
