<?php
ini_set('display_errors', true);

$xpicfile = $_FILES['image']['name'];

/*
To extract an uploaded file from an HTTP request, we need to parse the form data that is encoded in the "multipart/form-data" format. In PHP, the form data in an HTTP request is automatically parsed. The PHP engine stores the information of the uploaded files in the $_FILES array.

Now let's say the name attribute value of the <input type="file"> element in a certain HTML/XHTML/XHTML MP document is myFile. To obtain the information about the uploaded file, use the following lines of PHP script:


/* Get the size of the uploaded file in bytes. */
//$fileSize = $_FILES['myFile']['size'];

/* Get the name (including path information) of the temporary file created by PHP that contains the same contents as the uploaded file. */
//$tmpFile = $_FILES['myFile']['tmp_name'];

/* Get the name of the uploaded file at the client-side. Some browsers include the whole path here (e.g. e:\files\myFile.txt), so you may need to extract the file name from the path. This information is provided by the client browser, which means you should be cautious since it may be a wrong value provided by a malicious user. */
//$fileName = $_FILES['myFile']['name'];

/* Get the content type (MIME type) of the uploaded file. This information is provided by the client browser, which means you should be cautious since it may be a wrong value provided by a malicious user. */
//$contentType = $_FILES['myFile']['type'];



function upload($file_id, $folder="", $types="") {
    if(!$_FILES[$file_id]['name']) return array('','No file specified');

    $file_title = $_FILES[$file_id]['name'];
    //Get file extension
    $ext_arr = split("\.",basename($file_title));
    $ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension

    //Not really uniqe - but for all practical reasons, it is
    //$uniqer = substr(md5(uniqid(rand(),1)),0,5);
    $file_name = $file_title;//Get Unique Name
	
    $result = '';

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
	
	if ($result == "") {
		$retval = 2;
	} else {
		$retval = '5 - '.$result;
	}
} 

	
	upload($xpicfile,'incidents','jpeg,gif,png,doc,xls,pdf,docx,xlsx,jpg,ppt,rtf,txt');
	
	return $retval;
	
?>


