<?php
session_start();

error_reporting(E_ERROR | E_WARNING | E_PARSE);

date_default_timezone_set($_SESSION['s_timezone']);
$usersession = $_SESSION['usersession'];

include_once("../includes/DBClass.php");
$db = new DBClass();

$cltdb = $_SESSION['s_cltdb'];

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];

$campid = $_SESSION['s_campid'];

$dt = date("Y-m-d");
$staffmember = $sname;

$db->closeDB();


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
function upload($file_id, $folder="", $types="",$campid) {
    if(!$_FILES[$file_id]['name']) return array('','No file specified');

    $file_title = $_FILES[$file_id]['name'];
    //Get file extension
    $ext_arr = split("\.",basename($file_title));
    $ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension

    //Not really uniqe - but for all practical reasons, it is
    //$uniqer = substr(md5(uniqid(rand(),1)),0,5);
    $file_name = $campid . '__' . $file_title;//Get Unique Name

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
            $result .= " : Folder don't exist.";
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
	
	list($file,$error) = upload('image','../documents/campaign','jpeg,gif,png,doc,xls,pdf,docx,xlsx,jpg,ppt,rtf,txt',$campid);
	if($error) {
		print $error;
	} else {
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$fl = split('__',$file);
		$fl2 = $fl[1];
		//insert entry into documents table
		$db->query("insert into ".$cltdb.".campaign_docs (campaign_id,ddate,doc,staff,subject,sub_id) values (:campaign_id,:ddate,:doc,:staff,:subject,:sub_id)");
		$db->bind(':campaign_id', $campid);
		$db->bind(':ddate', $dt);
		$db->bind(':doc', $fl2);
		$db->bind(':staff', $staffmember);
		$db->bind(':subject', $_REQUEST['subject']);
		$db->bind(':sub_id', $subscriber);
		
		$db->execute();
		$db->closeDB();
		
		echo  "<script>";
		echo 'window.open("","updtcampdocs").jQuery("#doclist").trigger("reloadGrid");';
		echo 'this.close();';
		echo '</script>';
		
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Upload Campaign Document</title>

<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">


</head>

<body>

<div id="swin">
<form action="" method="post" enctype="multipart/form-data">
<table width="500" border="0" align="center">
<tr>
<th colspan="2">Select file to upload to this Campaign</th>
</tr>
<tr>
<td align="right">Enter subject description</td>
<td align="left"><input type="text" name="subject" id="subject" /></td>
</tr>
<tr>
<td colspan="2" align="center"><input type="file" name="image" /></td>
</tr>
<tr>
  <td colspan="2" align="center">&nbsp;</td>
</tr>
<tr>
  <td colspan="2" align="center">Depending on filesize uploads can take several minutes - please wait</td>
</tr>
<tr>
  <td colspan="2" align="right"><input type="submit" value="Upload" name="action" /></td>
</tr>
</table>
</form>
</div>

</body>
</html>