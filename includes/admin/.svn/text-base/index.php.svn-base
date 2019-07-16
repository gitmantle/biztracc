<?php
ini_set('display_errors', true);

?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Kenny Administration</title>

<script>

function post() {

	//add validation here if required.
	var slname = document.getElementById('sublname').value;
	var afname = document.getElementById('adminfname').value;
	var alname = document.getElementById('adminlname').value;
	var auname = document.getElementById('adminusername').value;
	var apwd = document.getElementById('adminpassword').value;
	if (slname == "") {
		alert("Please enter a subcriber surname or company name.");
		ok = "N";
		return false;
	}
	if (afname == "") {
		alert("Please enter an administrator's first name.");
		ok = "N";
		return false;
	}
	if (alname == "") {
		alert("Please enter an administrator's surname.");
		ok = "N";
		return false;
	}
	if (auname == "") {
		alert("Please enter an administrator's username.");
		ok = "N";
		return false;
	}
	if (apwd == "") {
		alert("Please enter an administrator's password.");
		ok = "N";
		return false;
	}
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('admin').submit();
	}
	
	
}



</script>

</head>

<body>

<form action="" method="post" name="admin" id="admin" enctype="multipart/form-data">
 <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="700" border="0">
    <tr>
      <td colspan="2" align="center">Add a New Subscriber to Kenny</td>
    </tr>
    <tr>
      <td>Subscriber's First Name (if applicable)</td>
      <td><input type="text" name="subfname" id="subfname" /></td>
    </tr>
    <tr>
      <td>Subscriber's Last or Company Name</td>
      <td><input type="text" name="sublname" id="sublname" /></td>
    </tr>
    <tr>
      <td>Subscriber's Email</td>
      <td><input type="text" name="subemail" id="subemail" /></td>
    </tr>
    <tr>
      <td>Subscriber's Top Logo (167px w by 70px h) jpg only </td>
      <td><input type="file" name="toplogo" /></td>
    </tr>
    <tr>
      <td>Subscriber's DNA Logo (561px w by 234px h) jpg only </td>
      <td><input type="file" name="dnalogo" /></td>
    </tr>
    <tr>
      <td>Subscriber Administrator's First Name</td>
      <td><input type="text" name="adminfname" id="adminfname" /></td>
    </tr>
    <tr>
      <td>Subscriber Administrator's Surname</td>
      <td><input type="text" name="adminlname" id="adminlname" /></td>
    </tr>
    <tr>
      <td>Subscriber Administrator's Email</td>
      <td><input type="text" name="adminemail" id="adminemail" /></td>
    </tr>
    <tr>
      <td>Subscriber Administrator's username</td>
      <td><input type="text" name="adminusername" id="adminusername" /></td>
    </tr>
    <tr>
      <td>Subscriber Administrator's password</td>
      <td><input type="text" name="adminpassword" id="adminpassword" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input type="button" name="btnadmin" id="btnadmin" value="Submit" onclick-"post();"/></td>
    </tr>
  </table>
</form>


</body>

<?php

if($_REQUEST['savebutton'] == "Y") {
	
	$query = 'insert into subscribers (lastname,clt) values ("'.$_REQUEST['sublname'].'","Y"';
	$result = mysql_query($query) or die(mysql_error().$query);																	  
	$newsub = mysql_insert_id();
	
	
	echo '<script>';
	echo 'alert("This subscriber ID is '.$newsub.'. Make a not of it.");';
	echo '</script>';
	
	
	
	if ($_REQUEST['subfname'] != '') {
		$q = 'update subscribers set firstname = "'.$_REQUEST['subfname'].'" where sub_id = '.$newsub;
		$r = mysql_query($q);
	}

	if ($_REQUEST['subemail'] != '') {
		$q = 'update subscribers set email = "'.$_REQUEST['subemail'].'" where sub_id = '.$newsub;
		$r = mysql_query($q);
	}

	function upload($file_id, $folder="", $types="", $file_name) {
		if(!$_FILES[$file_id]['name']) return array('','No file specified');
	
		$file_title = $_FILES[$file_id]['name'];
		//Get file extension
		$ext_arr = split("\.",basename($file_title));
		$ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension
	
		//Not really uniqe - but for all practical reasons, it is
		//$uniqer = substr(md5(uniqid(rand(),1)),0,5);
	
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
	
	
	if($_FILES['toplogo']['name']) {
		$filename = "sub_".$newsub."logo.jpg";
		list($file,$error) = upload('toplogo','../../images','jpeg,gif,png,doc,xls,pdf,docx,xlsx,jpg,ppt,rtf,txt',$filename);
		if($error) {
			print $error;
		}
		$q = "update subscribers set toplogo = '".$filename."' where sub_id = ".$newsub;
		$r = mysql_query($q);
	}

	if($_FILES['dnalogo']['name']) {
		$filename = "sub_".$newsub."dnalogo.jpg";
		list($file,$error) = upload('dnalogo','../../images','jpeg,gif,png,doc,xls,pdf,docx,xlsx,jpg,ppt,rtf,txt',$filename);
		if($error) {
			print $error;
		}
		$q = "update subscribers set logo = '".$filename."' where sub_id = ".$newsub;
		$r = mysql_query($q);
	}

	$q = 'insert into staff (firstname,lastname,username,password,admin,sub_id) values (';
	$q .= '"'.$_REQUEST["adminfname"].'",';
	$q .= '"'.$_REQUEST["adminlname"].'",';
	$q .= '"'.md5($_REQUEST["adminusername"]).'",';
	$q .= '"'.md5($_REQUEST["adminpassword"]).'",';
	$q .= '"N",';
	$q .= $newsub.')';
	$r = mysql_query($q) or die (mysql_error().$q);
	$newstaff = mysql_insert_id();
	
	if ($_REQUEST['adminemail'] != '') {
		$q = 'update staff set email = "'.$_REQUEST["adminemail"].'" where sub_id = '.$newstaff;
		$r = mysql_query($q);
	}
	
	$q = 'insert into access (staff_id,module,usergroup) values ('.$newstaff.',"clt",20)';
	$r = mysql_query($q);

}

?>



</html>