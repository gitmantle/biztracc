<?php

function getMenuItems() {

	$usersession = $_SESSION['usersession'];
	$admindb = $_SESSION['s_admindb'];
	if ($_SESSION['s_module'] == 'clt') {
		$moduledb = $_SESSION['s_cltdb'];
		$module = 'clt';
	}
	if ($_SESSION['s_module'] == 'fin') {
		$module = 'fin';
		$moduledb = $_SESSION['s_findb'];
	}
	if ($_SESSION['s_module'] == 'log') {
		$module = 'prc';
		$moduledb = $_SESSION['s_prcdb'];
	}
	if ($_SESSION['s_module'] == 'med') {
		$module = 'prc';
		$moduledb = $_SESSION['s_prcdb'];
	}
	if ($_SESSION['s_module'] == 'sal') {
		$module = 'prc';
		$moduledb = $_SESSION['s_prcdb'];
	}
	if ($_SESSION['s_module'] == 'adm') {
		$module = 'prc';
		$moduledb = $_SESSION['s_admindb'];
	}
	
	$table = $moduledb.".".$_SESSION['s_menutable'];

	global $dbase;
	global $dbs;
	global $db;
	
	
	include_once("DBClass.php");
	$dbm = new DBClass();
	
	$dbm->query("select * from sessions where session = :vusersession");
	$dbm->bind(':vusersession', $usersession);
	$row = $dbm->single();
	extract($row);
	
	if ($uberadmin == 'Y') {
		$usergroup = 'A';
		$dbm->query("update sessions set usergroup = 20 where session = :usersession");
		$dbm->bind(':usersession', $usersession);
		$dbm->execute();
		$_SESSION['s_ubranch'] = "";
	} else {
		$dbm->query("select usergroup,branch as ubranch from access where staff_id = :userid and module = :module");
		$dbm->bind(':userid', $user_id);
		$dbm->bind(':module', $module);
		$row = $dbm->single();
		$numrw = $dbm->rowCount();
		if ($numrw > 0) {
			extract($row);
			$dbm->query("update sessions set usergroup = :vusergroup where session = :vusersession");
			$dbm->bind(':vusergroup', $usergroup);
			$dbm->bind(':vusersession', $usersession);
			$dbm->execute();
		}
	}
	
	if ($usergroup == 'A') {
		$dbm->query("SELECT * FROM ".$table." ORDER BY `morder`");
	} else {
		$dbm->query("SELECT * FROM ".$table." WHERE `a".trim(strval($usergroup))."` = 'Y'  ORDER BY `morder`");
	}
	
	$mItems = '';

	$rows = $dbm->resultset();;
	foreach ($rows as $row) {
		extract($row);
		
		switch ($level) {
			case 0:
				$indent = '';
				break;
			case 1:
				$indent = '|';
				break;
			case 2:
				$indent = '||';
				break;
			case 3:
				$indent = '|||';
				break;				
			case 4:
				$indent = '||||';
				break;	
		}
		
		if ($indent == '') {
			$mItems .= '["'.$indent.$label.'","'.$onclick.'", "", "", "'.$tooltip.'", "", "0", "", "", "", "", ],';
		} else {
			$mItems .= '["'.$indent.$label.'","'.$onclick.'", "", "", "'.$tooltip.'", "", "", "", "", "", "", ],';
		}	
	}
	
	$dbm->closeDB();

	return $mItems;

}

?>