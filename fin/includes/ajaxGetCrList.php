<?php
	session_start();
	
	$coyidno = $_SESSION['s_coyid'];
	$drcr = $_REQUEST['drcr'];
	$drind = $_REQUEST['drind'];
	$crind = $_REQUEST['crind'];

	require_once("../../db.php");

	$moduledb = $_SESSION['s_cltdb'];
	mysql_select_db($moduledb) or die(mysql_error());
	$query = "select members.lastname,members.firstname,client_company_xref.client_id,client_company_xref.crno,client_company_xref.crsub,client_company_xref.subname,client_company_xref.sortcode from members left join client_company_xref on members.member_id = client_company_xref.client_id where client_company_xref.company_id = ".$coyidno." and client_company_xref.crno != '' and client_company_xref.blocked = 'No' order by sortcode";

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
			if ($crsub == 0) {
				$DRaccountsList .= '<option value="'.$crno.'~'.' '.'~'.$crsub.'~'.$lastname.'"'.$selected.'>'.trim(strtoupper($lastname)).' '.trim($firstname).'  '.$crno.'-'.$crsub.'</option>';
			} else {
				$DRaccountsList .= '<option value="'.$crno.'~'.' '.'~'.$crsub.'~'.$lastname.' '.$subname.'"'.$selected.'>'.trim(ucwords(strtolower($lastname))).' '.trim($subname).'  '.$crno.'-'.$crsub.'</option>';
			}
			$i = $i + 1;
		}
	} else {
		$CRaccountsList = "<option value=\"\">Select Account</option>";
		$i = 0;
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			if ($i == $crind-1) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			if ($crsub == 0) {
				$CRaccountsList .= '<option value="'.$crno.'~'.' '.'~'.$crsub.'~'.$lastname.'"'.$selected.'>'.trim(strtoupper($lastname)).' '.trim($firstname).'  '.$crno.'-'.$crsub.'</option>';
			} else {
				$CRaccountsList .= '<option value="'.$crno.'~'.' '.'~'.$crsub.'~'.$lastname.'"'.$selected.' '.$subname.'>'.trim(ucwords(strtolower($lastname))).' '.trim($subname).'  '.$crno.'-'.$crsub.'</option>';
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