<?php
	include_once "../../includes/accesscontrol.php";
	include_once "../../includes/db2.php";

	$cid = $_REQUEST['cid'];
	$con = $_REQUEST['con'];
	$leadsource = $_REQUEST['ls'];
	$dept = $_REQUEST['dept'];
	$prod = $_REQUEST['prod'];
	$id = $_REQUEST['id'];
	$item_id = $_REQUEST['itemid'];
	$idnum = substr($id,3,strlen($id)-3);
?>

<head><title>Lead Capture Form</title></head>
<LINK REL="Stylesheet" HREF="../../style.css" TYPE="text/css">
<LINK REL="Stylesheet" HREF="../../includes/dhtmlgoodies_calendar/dhtmlgoodies_calendar.css" TYPE="text/css">
<script src="../../includes/prototype.js"></script>
<script src="../../includes/dhtmlgoodies_calendar/dhtmlgoodies_calendar.js"></script>
<script src="lead.js"></script>
<script>
pathToImages = '../../images/';
</script>

<form action="save_lead.php" method="POST">
<input type="hidden" id="dept" name="dept" value="<?php echo $dept; ?>">
<input type="hidden" id="prod" name="prod" value="<?php echo $prod; ?>">
<input type="hidden" id="idnum" name="idnum" value="<?php echo $idnum; ?>">

<input type="hidden" id="rcid" name="rcid" value="<?php echo $cid; ?>">
<input type="hidden" id="rcon" name="rcon" value="<?php echo $con; ?>">
<input type="hidden" id="ritemid" name="ritemid" value="<?php echo $item_id; ?>">



<table width="100%">
<tr>
	<td class="mhl">POS - Capture Lead Data <?php if($cid > 0) { echo " - Existing Customer <script>loadCurrentRecord('$cid','$dept','$prod','$con');</script>"; } else { echo "<script>loadNewRecord('$cid');</script>"; } ?></td>
</tr>
</table>

<div id="newrecord">

</div>

<div id="selectform">

</div>

<div id="leadform">

</div>

<?php if($cid > 0) { ?>
<script>loadLeadFormSelect('', document.getElementById('dept').value, '', '<?php echo $_GET['cid']; ?>')</script>
<?php } ?>