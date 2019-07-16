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
      <td><input type="file" name="image" /></td>
    </tr>
    <tr>
    	<td><input type="submit" name="save" value="Upload"></td>
  </table>
</form>


<?php

if(isset($_POST['save'])) {
		
  include_once("includes/DBClass.php");
  $db = new DBClass();
	
  if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) { 
    $tmpName  = $_FILES['image']['tmp_name'];  
    $fp = fopen($tmpName, 'rb'); // read binary

	$db->query("INSERT INTO infinint_fin40_20.stkmast ( picture ) VALUES ( :picture )");
	$db->bind(':picture', $fp, PDO::PARAM_LOB);
	$db->execute();
	
	$last = $db->lastInsertId();
	
	$db->query("SELECT picture FROM infinint_fin40_20.stkmast WHERE itemid = ".$last);
	$row = $db->single();
	extract($row);
	echo '<img src="data:image/jpeg;base64,'.base64_encode( $picture ).'"/>';	
	
	
	$db->closeDB();	
  } 

}
	
?>




</body>
</html>