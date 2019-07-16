<?php

session_start();

ini_set('display_errors', true);


?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload Image</title>
</head>

<body>
<form id="form1"  method="post" enctype="multipart/form-data" name="form1" >
  <table width="650" border="0">
    <tr>
      <td>Select an Image to Upload</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td><input type="file" name="file" /></td>
    </tr>
    <tr>
    	<td><input type="submit" name="save" value="Upload"></td>
  </table>
</form>


<?php

	if(isset($_POST['save'])) {
		$ok = "Y";
		if ($_FILES["file"]["error"] > 0) {
			echo '<script>';
			$err = "Error: " . $_FILES["file"]["error"];
			echo 'alert('.$err.')';
			echo '</script>';	
			$ok = 'N';
		} 
		
		if ($ok == 'Y') {
		
			include "wsuploadpic.php";

		}
	}
?>




</body>
</html>