<?php
session_start();
$usersession = $_SESSION['usersession'];

$admindb = $_SESSION['s_admindb'];
if(isset($_SESSION['s_coyid'])) {
	$coyid = $_SESSION['s_coyid'];
}

date_default_timezone_set($_SESSION['s_timezone']);
$d = date('Y-m-d');

include_once("../DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$module = $_SESSION['s_module'];
if ($module == 'clt') {
	$hed = 'Client Management Database';
	$fl = 'Your client management backup files will all be named backup-infinint_sub'.$subscriber.'_(date of backup) (time of backup).sql.zip';
	$moduledb = 'infinint_sub'.$subscriber;
}
if ($module == 'fin') {
	$hed = 'Financial Management Database';
	$fl = 'Your financial management backup files will all be named backup-infinint_fin'.$subscriber.'_'.$coyid.'_(date of backup) (time of backup).sql.zip';
	$moduledb = 'infinint_fin'.$subscriber.'_'.$coyid;
}
if ($module == 'log') {
	$hed = 'Trucking Database';
	$fl = 'Your logging backup files will all be named backup-infinint_log'.$subscriber.'_'.$coyid.'_(date of backup) (time of backup).sql.zip';
	$moduledb = 'infinint_log'.$subscriber.'_'.$coyid;
}
if ($module == 'htb') {
	$hed = 'Cmeds Database';
	$fl = 'Your cmeds backup files will all be named backup-infinint_htb'.$subscriber.'_'.$coyid.'_(date of backup) (time of backup).sql.zip';
	$moduledb = 'infinint_htb'.$subscriber.'_'.$coyid;
}


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
 


function upload($file_id, $folder="", $types="") {
    if(!$_FILES[$file_id]['name']) return array('','No file specified');
    $file_title = $_FILES[$file_id]['name'];
    //Get file extension
    $ext_arr = explode(".",basename($file_title));
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
            $result .= " : Folder doesn't exist.";
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
$db->closeDB();


if(isset($_POST['save'])) {
	include_once("../DBClass.php");
	$db = new DBClass();

	if($_FILES['userfile']['name']) {
		list($file,$error) = upload('userfile','backup','zip');
		if($error) {
			echo  "<script>";
			echo 'alert("'.$error.'");';
			echo 'this.close();';
			echo '</script>';
		} else {

			$sqlfile = $_FILES['userfile']['name'];
			
			$lang ='en';  //indice of the "lang_...json" file with texts
			$dir = 'backup/';  //folder to store the ZIP archive with SQL backup
			date_default_timezone_set($_SESSION['s_timezone']);
						
			//set object of backupmysql class
			include 'backupmysql.class.php';
			$bk = new backupmysql($lang, $dir);			
			$dbHost=$_SESSION['s_server'];
			$conn_data = array('host'=>$dbHost, 'user'=>'infinint_sagana', 'pass'=>'dun480can', 'dbname'=>$moduledb);	
			
	 		$bk->setMysql($conn_data);
			$bk->restore($sqlfile);

			echo  "<script>";
			echo 'alert("File uploaded and backup restored");';
			echo 'this.close();';
			echo '</script>';
						
		}
	}
}


?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Restore Backup</title>

<link rel="stylesheet" href="../../includes/mantle.css" media="screen" type="text/css">

</head>

<body>

<div id="swin">
<form action="" name="form1" id="form1" method="post" enctype="multipart/form-data">
<table width="500" border="0" align="center">
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center">Browse to and select the appropriate backup file.</td>
</tr>
<tr>
  <td align="center"><?php echo $fl; ?></td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center"><input name="userfile" type="file" /></td>
</tr>
<tr>
  <td align="center">&nbsp;</td>
</tr>
<tr>
  <td align="center">Depending on filesize uploads can take several minutes - please wait</td>
</tr>
<tr>
  <td align="right"><input type="submit" value="Upload and Restore" name="save" /></td>
</tr>
</table>
</form>
</div>

</body>
</html>