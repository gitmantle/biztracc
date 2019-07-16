<?php
session_start();
$module = $_SESSION['s_module'];

if ($module == 'clt') {
	$hed = 'Client Management Database';
}
if ($module == 'fin') {
	$hed = 'Financial Management Database';
}
if ($module == 'log') {
	$hed = 'Trucking Database';
}


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en"  dir="ltr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Backup Database</title>
</head>

<body>

<table align="center" width="50%">
<tr>
<td align="center">Backup your <?php echo $hed; ?></td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center">This routine will backup your database into a file then compress it. You will then be given the opportunity to download the compressed file onto your own computer from where you should should store it in a safe place. These backup files are named with the date of creation and should be kept on a generational basis.</td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr>
	<td align="center"><font color="red">REMEMBER to also create a backup of the Client Relationship Management database from the Housekeeping menu within that part of the system.</font></td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center"><input type="button" name="bkup" id="bkup" value="Continue" onclick="bkupdb()"/></td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr id="comp">
	<td align="center">Backup file created</td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center"><input type="button" name="download" id="download" value="Download to your PC" onclick="downloadsql();return false;" /></td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr id="down">
	<td align="center">Backup file downloaded</td>
</tr>
</table>

<script>
  document.getElementById('comp').style.visibility = 'hidden';
  document.getElementById('download').style.visibility = 'hidden';
  document.getElementById('down').style.visibility = 'hidden';
</script>

</body>