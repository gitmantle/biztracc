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
		
			
			if  (($_FILES["file"]["type"] == "image/jpeg")||($_FILES["file"]["type"] == "image/pjpeg"))  {		
				
				$file_id = $_FILES['file']['name'];
				$folder = 'incidents';
				$types = 'jpeg,gif,png,jpg,pjpeg';
				
				if(!$_FILES['file']['name']){
					return array('','No file specified');
				}
			
				$file_title = $_FILES['file']['name'];
				
				/*
				//Get file extension
				$ext_arr = explode("\.",basename($file_title));
				$ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension
				*/
				
				$ext = 'jpg';
			
				//Not really uniqe - but for all practical reasons, it is
				//$uniqer = substr(md5(uniqid(rand(),1)),0,5);
				$file_name = $file_title;//Get Unique Name
			
			/*
				$all_types = explode(",",strtolower($types));
				if($types) {
					if(in_array($ext,$all_types)){
					} else {
						$result = "'".$_FILES['file']['name']."' is not a valid file."; //Show error if any.
						return array('',$result);
					}
				}
			*/
			
			
				//Where the file must be uploaded to
				if($folder) {
					$folder .= '/';//Add a '/' at the end of the folder
					$uploadfile = $folder . $file_name;
				}
				
				
				$result = '';
				//Move the file from the stored location to the new location
				if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
					$result = "Cannot upload the file '".$_FILES['file']['name']."'"; //Show error if any.
					if(!file_exists($folder)) {
						$result .= " : Folder don't exist.";
					} elseif(!is_writable($folder)) {
						$result .= " : Folder not writable.";
					} elseif(!is_writable($uploadfile)) {
						$result .= " : File not writable.";
					}
					$file_name = '';
					
				} else {
					if(!$_FILES['file']['size']) { //Check if the file is made
						@unlink($uploadfile);//Delete the Empty file
						$file_name = '';
						$result = "Empty file found - please use a valid file."; //Show the error message
					} else {
						chmod($uploadfile,0755);//0777 to Make it universally writable.
					}
				}
			
				return array($file_name,$result);
				
				
			}
		}
	}
?>




</body>
</html>