<?php
session_start();

include_once("../includes/DBClass.php");
$db = new DBClass();

$dbsys = "information_schema";

// populate  database drop down
$db->query("select schema_name from ".$dbsys.".schemata");
$rows = $db->resultset();
$db_options = '<option value="0">Select</option>';
foreach ($rows as $row) {
	extract($row);
	$selected = '';
	$db_options .= '<option value="'.$schema_name.'"'.$selected.'>'.$schema_name.'</option>';
}

	
$db->closeDB();

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Database update</title>

<script>

function post() {

	//add validation here if required.
	var ok = "Y";
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('db_update').submit();
	}
	
}

</script>

</head>

<body>
<form name="db_update" id="db_update">
 	<input type="hidden" name="savebutton" id="savebutton" value="N">
    <table width="600" border="0" cellspacing="0" align="center" >
      <tr>
      	<td>Database to be updated</td>
        <td align="left"><select name="dbname" id="dbname"><?php echo $db_options;?></select></td>
      </tr>
      <tr>
      	<td>Database template to use</td>
        <td align="left"><input type="text" name="dbtemplate" id="dbtemplate" ></td>
      </tr>
      <tr>
      	<td>&nbsp;</td>
        <td><input type="button" value="Update" name="save" onClick="post()">
      </td>
	</table>
</form>
</body>

</html> 

<?php

	if(isset($_REQUEST['savebutton']) && $_REQUEST['savebutton'] == "Y") {
		$db2update = $_REQUEST['dbname'];
		$templatedb = $_REQUEST['dbtemplate'];
		
		include_once("DBUpdate.php");
		$oDb = new DBUpdate;
		
		$oDb->db_template = $templatedb;
		$oDb->db2update = $db2update;
		
		$oDb->updatedb();


	}

?>