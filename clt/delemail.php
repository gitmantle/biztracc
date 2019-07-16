<?php
session_start();

$cmuid = $_REQUEST['uid'];

	include_once("../includes/cltadmin.php");
	$oCm = new cltadmin;	
	
	$oCm->uid = $cmuid;
	
	$oCm->DelEmail();
	
?>

	<script>
			window.open("","editmembers").jQuery("#memaillist").trigger("reloadGrid");
			this.close();		
    </script>
