<?php
	session_start();
	$subid = $_REQUEST['cid'];
	$cid = $_REQUEST['coyid'];
	$c = explode('~',$cid);
	
	$coyid = $c[0];
	$coytaxyear = $c[1];
	
	include_once("../includes/DBClass.php");
	$db = new DBClass();	
	
	$db->query("select coyname from companies where coyid = ".$coyid);
	$row = $db->single();
	extract($row);
	
	$_SESSION['s_coyid'] = $coyid;
	$_SESSION['s_coyname'] = $coyname;
	$_SESSION['s_coytaxyear'] = $coytaxyear;
	
	$db->query("select processes.process, processes.folder from processes,access where processes.access_id = access.access_id and access.module = 'prc' and subid = ".$subid." and coyid = ".$coyid);
	$rows = $db->resultset();
	if (count($rows) > 0) { 
		$prc_options = "";
		foreach ($rows as $row) {
			extract($row);
			$prc_options .= "<option value=\"".$folder."\">".$process."</option>";
		}				
	} else {
		$prc_options = "<option value=\"\">No Processes</option>";
	}
	
	echo $prc_options;
	
	$db->closeDB();

?>