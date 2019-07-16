<?php
session_start();
$usersession = $_SESSION['usersession'];

$lang ='en';  //indice of the "lang_...json" file with texts
$dir = 'backup/';  //folder to store the ZIP archive with SQL backup
date_default_timezone_set($_SESSION['s_timezone']);


$admindb = $_SESSION['s_admindb'];
if(isset($_SESSION['s_coyid'])) {
	$coyid = $_SESSION['s_coyid'];
}
$module = $_SESSION['s_module'];

//set object of backupmysql class
include 'backupmysql.class.php';
$bk = new backupmysql($lang, $dir);

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


if ($module == 'clt') {
	$dbclt = "infinint_sub".$subscriber;
}

if ($module == 'fin') {
	$dbclt = "infinint_fin".$subscriber."_".$coyid;
}

if ($module == 'log') {
	$dbclt = "infinint_log".$subscriber."_".$coyid;
}

if ($module == 'infinint_htb') {
	$dbclt = "htb".$subscriber."_".$coyid;
}


$dbHost=$_SESSION['s_server'];
$conn_data = array('host'=>$dbHost, 'user'=>'infinint_sagana', 'pass'=>'dun480can', 'dbname'=>$dbclt);

$db->closeDB();


$bk->setMysql($conn_data);
$tables = $bk->getTables();
$bk->getSqlBackup($tables);
$bk->saveBkZip($tables);


?>



</body>
</html>
