<?php
session_start();

$staffid = $_REQUEST['staffid'];
$admindb = $_SESSION['s_admindb'];

require_once('../db.php');
mysql_select_db($admindb) or die(mysql_error());

$query = "select * from sessions where session = '".$usersession."'";
$result = mysql_query($query) or die(mysql_error());
$row = mysql_fetch_array($result);
extract($row);
$ad = $admin;
$sid = $subid;


$moduledb = $_SESSION['s_logdb'];
mysql_select_db($moduledb) or die(mysql_error());


/**
 * A function for easily uploading files. This function will automatically generate a new 
 *        file name so that files are not overwritten.
 * Taken From: http://www.bin-co.com/php/scripts/upload_function/
 * Arguments:    $file_id- The name of the input field contianing the file.
 *                $folder    - The folder to which the file should be uploaded to - it must be writable. OPTIONAL
 *                $types    - A list of comma(,) seperated extensions that can be uploaded. If it is empty, anything goes OPTIONAL
 * Returns  : This is somewhat complicated - this function returns an array with two values...
 *                The first element is randomly generated filename to which the file was uploaded to.
 *                The second element is the status - if the upload failed, it will be 'Error : Cannot upload the file 'name.txt'.' or something like that
 */
function upload($file_id, $folder="", $types="", $sid) {
    if(!$_FILES[$file_id]['name']) return array('','No file specified');

    $file_title = $_FILES[$file_id]['name'];
    //Get file extension
    $ext_arr = split("\.",basename($file_title));
    $ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension

    //Not really uniqe - but for all practical reasons, it is
    //$uniqer = substr(md5(uniqid(rand(),1)),0,5);
    $file_name = $file_title;//Get Unique Name

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
            $result .= $folder." : Folder don't exist.";
        } elseif(!is_writable($folder)) {
            $result .= $folder." : Folder not writable.";
        } elseif(!is_writable($uploadfile)) {
            $result .= $uploadfile." : File not writable.";
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
	
	list($file,$error) = upload('image','documents','pdf',$sid);
	if($error) {
		print $error;
	} else {
		
		$fl = explode('__',$file);
		$filename = $fl[1];
		$fl2 = explode('.',$filename);
		$filetitle = $_REQUEST['title'];
		//insert entry into documents table
		$query = "insert into gendocs (title,document) values ";
		$query .= "('".$filetitle."',";
		$query .= "'".$file."')";
		
		$result = mysql_query($query) or die(mysql_error().' '.$query);
		
		 echo  "<script>";
		   echo 'window.open("","gendocs").jQuery("#gendoclist").trigger("reloadGrid");';
		 echo 'this.close();';
		  echo '</script>';
		
	}
}


$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");



?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload General Information Document</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">


</head>

<body>

<div id="swin">
<form action="" method="post" enctype="multipart/form-data">
  <div id="docs" style="position:absolute;visibility:visible;top:1px;left:1px;height:230px;width:500px;border-width:thin thin thin thin; border-color:#999; border-style:solid; background-color: <?php echo $bgcolor; ?>; ">
<table width="500" border="0" align="center">
<tr>
<th align="center"><label style="color: <?php echo $tdfont; ?>">Select General Information PDF document to Upload</label></th>
</tr>
<tr>
  <td align="center">Document Title
    <input type="text" name="title" id="title" /></td>
</tr>
<tr>
  <td align="center"><label style="color: <?php echo $tdfont; ?>">File</label>
    <input type="file" name="image" /></td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center"><label style="color: <?php echo $tdfont; ?>">Depending on filesize, uploads can take several minutes - please wait</label></td>
</tr>
<tr>
  <td align="right"><input type="submit" value="Upload" name="action" /></td>
</tr>
<tr>
  <td>
    </td>
</tr>
</table>
</div>

</form>
</div>

</body>
</html>