<?php
	session_start();
	
	$coyidno = $_SESSION['s_coyid'];
	$drcr = $_REQUEST['drcr'];
	$drind = $_REQUEST['drind'];
	$crind = $_REQUEST['crind'];

	require_once("../../db.php");

	$moduledb = $_SESSION['s_findb'];
	mysql_select_db($moduledb) or die(mysql_error());
	$query = "select accountno,branch,asset,hcode from fixassets order by hcode, asset";	
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
			$DRaccountsList .= '<option value="'.$accountno.'~'.$branch.'~0~'.$asset.'"'.$selected.'>'.$asset.' - '.$branch.'</option>';
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
			$CRaccountsList .= '<option value="'.$accountno.'~'.$branch.'~0~'.$asset.'"'.$selected.'>'.$asset.' - '.$branch.'</option>';
			$i = $i + 1;
		}		
	}

if ($drcr == 'dr') {
	echo $DRaccountsList;
} else {
	echo $CRaccountsList;
}

?>