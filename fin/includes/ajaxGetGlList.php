<?php
	session_start();
	
	$coyidno = $_SESSION['s_coyid'];
	$dc = $_REQUEST['drcr'];
	$drind = $_REQUEST['drind'];
	$crind = $_REQUEST['crind'];

	require_once("../../db.php");

	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());
	$query = "select accountno,branch,sub,account from glmast where ctrlacc = 'N' and blocked = 'N' order by accountno,branch,sub";
	$result = mysql_query($query) or die($query);
	if ($dc == 'dr') {
		$DRaccountsList = "<option value=\"\">Select Account</option>";
		$i = 0;
		while ($row = mysql_fetch_array($result)) {
			extract($row);
			if ($i == $drind-1) {
				$selected = 'selected="selected"';
			} else {
				$selected = '';
			}
			$DRaccountsList .= '<option value="'.$accountno.'~'.$branch.'~'.$sub.'~'.$account.'"'.$selected.'>'.$account.' - '.$accountno.'-'.$sub.' '.$branch.'</option>';
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
			$CRaccountsList .= '<option value="'.$accountno.'~'.$branch.'~'.$sub.'~'.$account.'"'.$selected.'>'.$account.' - '.$accountno.'-'.$sub.' '.$branch.'</option>';
			$i = $i + 1;
		}		
	}


if ($dc == 'dr') {
	echo $DRaccountsList;
} else {
	echo $CRaccountsList;
}

?>