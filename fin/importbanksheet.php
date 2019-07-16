<?php
session_start();

$admindb = $_SESSION['s_admindb'];
$coyid = $_SESSION['s_coyid'];
$coyname = $_SESSION['s_coyname'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$subscriber = $subid;


function upload($file_id, $folder="", $types="",$coyid) {
    if(!$_FILES[$file_id]['name']) return array('','No file specified');

    $file_title = $_FILES[$file_id]['name'];
    //Get file extension
    $ext_arr = explode("\.",basename($file_title));
    $ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension

    //Not really uniqe - but for all practical reasons, it is
    //$uniqer = substr(md5(uniqid(rand(),1)),0,5);
    $file_name = $coyid . '__banksheet.'.$ext;//Get Unique Name

    $all_types = explode(",",strtolower($types));
    if($types) {
        if(in_array($ext,$all_types));
        else {
            $result = "'".$_FILES[$file_id]['name']."' is not a valid file."; //Show error if any.
            return array('',$result);
        }
    }

    //Where the file must be uploaded to
    if($folder) $folder .= '/';//Add a '/' at the end of the folder
    $uploadfile = $folder . $file_name;

    $result = '';
    //Move the file from the stored location to the new location
    if (!move_uploaded_file($_FILES[$file_id]['tmp_name'], $uploadfile)) {
        $result = "Cannot upload the file '".$_FILES[$file_id]['name']."'"; //Show error if any.
        if(!file_exists($folder)) {
            $result .= " : Folder does not exist.";
        } elseif(!is_writable($folder)) {
            $result .= " : Folder not writable.";
        } elseif(!is_writable($uploadfile)) {
            $result .= " : File not writable.";
        }
        $file_name = '';
        
    } else {
        if(!$_FILES[$file_id]['size']) { //Check if the file is made
            @unlink($uploadfile);//Delete the Empty file
            $file_name = '';
            $result = "Empty file found - please use a valid file."; //Show the error message
        } else {
            chmod($uploadfile,0755);//0777 to Make it universally writable.
        }
    }

    return array($file_name,$result);
} 



if($_FILES['image']['name']) {
	
	list($file,$error) = upload('image','../../bankrec','xlsx',$coyid);
	if($error) {
	  print $error;
	} else {
	  echo '<script>';
	  echo 'var x = 0, y = 0;'; // default values	
	  echo 'x = window.screenX +5;';
	  echo 'y = window.screenY +5;';
	  echo "window.open('matchbanksheet.php?','ast','toolbar=0,scrollbars=1,height=760,width=920,resizable=1,left='+x+',screenX='+x+',top='+y+',screenY='+y);";
	  echo 'this.close();';
	  echo '</script>';
	}
}


$theme = $_SESSION['deftheme'];
require_once("../../includes/backgrounds.php");



?>


<html>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Import Bank Statement</title>
<link rel="stylesheet" href="../../includes/mantle.css" media="screen" type="text/css">

<script>
function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}

</script>

<style type="text/css">
<!--
.style1 {font-size: large}
-->
</style>
</head>


<body>

<form action="" method="post" enctype="multipart/form-data">
<table width="800" border="0" align="center" bgcolor="<?php echo $bgcolor; ?>">
    <tr>
      <td><div align="center" class="style1"><u>Import Bank Statement</u></div></td>
    </tr>
    <tr>
    	<td align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">Please ensure the spreadsheet you obtained from your bank is Excel2007 format (.xlsx) - otherwise convert and save it first.</label></td>
    </tr>
    <tr>
      <td align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">File</label>
      <input type="file" name="image" size="70"/></td>
    </tr>
    <tr>
      <td align="center">&nbsp;</td>
    </tr>
    <tr>
      <td align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">By clicking the Upload button you accept you have chosen the correct file to upload in the correct format and agree Logtracc Systems Limited is in no way responsible for the correctness or suitability of the imported data.</label></td>
    </tr>
    <tr>
      <td align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">Depending on filesize, uploads can take several minutes - please wait</label></td>
    </tr>
    <tr>
      <td align="center"><label style="color: <?php echo $tdfont; ?>; font-size:14px;">Once the file is uploaded you will be asked to match the file's columns to the data fields acceptable to Logtracc</label></td>
    </tr>
    <tr>
      <td align="right"><input type="submit" value="Upload" name="action" /></td>
    </tr>
</table>


<script>document.onkeypress = stopRKey;</script> 
</form>
</body>
</html>
