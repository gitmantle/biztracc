<?php
	session_start();
	
	$coyidno = $_SESSION['s_coyid'];
	$drcr = trim($_REQUEST['drcr']);
	$crind = $_REQUEST['crind'];
	$drind = $_REQUEST['drind'];

	require_once("../../db.php");

	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());
	$query = "select members.lastname,members.firstname,client_company_xref.client_id,client_company_xref.drno,client_company_xref.drsub,client_company_xref.subname,client_company_xref.sortcode from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyidno." and client_company_xref.drno != '' and client_company_xref.blocked = 'No' order by sortcode";

	$result = mysql_query($query) or die($query);
	if ($drcr == 'dr') {
		$DRaccountsList = "<option value=\"\">Select Account</option>";
		$i = 0;
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			if ($i == $drind-1) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			if ($drsub == 0) {
				$DRaccountsList .= '<option value="'.$drno.'~'.' '.'~'.$drsub.'~'.$lastname.'"'.$selected.'>'.trim(strtoupper($lastname)).' '.trim($firstname).'  '.$drno.'-'.$drsub.'</option>';
			} else {
				$DRaccountsList .= '<option value="'.$drno.'~'.' '.'~'.$drsub.'~'.$lastname.' '.$subname.'"'.$selected.'>'.trim(ucwords(strtolower($lastname))).' '.trim($subname).'  '.$drno.'-'.$drsub.'</option>';
			}
			$i = $i + 1;
		}
	}
	if ($drcr == 'cr') {
		$CRaccountsList = "<option value=\"\">Select Account</option>";
		$i = 0;
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			if ($i == $crind-1) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			if ($drsub == 0) {
				$CRaccountsList .= '<option value="'.$drno.'~'.' '.'~'.$drsub.'~'.$lastname.'"'.$selected.'>'.trim(strtoupper($lastname)).' '.trim($firstname).'  '.$drno.'-'.$drsub.'</option>';
			} else {
				$CRaccountsList .= '<option value="'.$drno.'~'.' '.'~'.$drsub.'~'.$lastname.' '.$subname.'"'.$selected.'>'.trim(ucwords(strtolower($lastname))).' '.trim($subname).'  '.$drno.'-'.$drsub.'</option>';
			}
			$i = $i + 1;
		}
	}

if ($drcr == 'dr') {
	echo $DRaccountsList;
} else {
	echo $CRaccountsList;
}

?>