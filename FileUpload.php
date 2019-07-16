<?php

$server = "mysql3.webhost.co.nz";
$user = "logtracc9";
$pwd = "dun480can";
$connect = mysql_connect($server,$user,$pwd) or die("Check your database connection");	mysql_select_db($dbase) or die(mysql_error());

date_default_timezone_set($_SESSION['s_timezone']);

$dbs = 'sub30';
mysql_select_db($dbs) or die(mysql_error());

$dt = date("Y-m-d");


//retreive post variables
$fileName = $_FILES['File']['name'];
$fileTempName = $_FILES['File']['tmp_name'];

$numelements = count($fileName);

$fl = explode('__',$fileName);
$memid = $fl[0];
$fname = $fl[1];
$q = "select sub_id from members where member_id = ".$memid;
$r = mysql_query($q) or die(mysql_error().' '.$q.' - '.$fileName);
$row = mysql_fetch_array($r);
extract($row);
$subscriber = $sub_id;




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
 

function upload($file_id, $folder="", $types="",$memid) {
    if(!$_FILES[$file_id]['name']) return array('','No file specified');

    $file_title = $_FILES[$file_id]['name'];
    //Get file extension
    $ext_arr = explode("\.",basename($file_title));
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

    $result = 'Sent';
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



if($_FILES['File']['name']) {
	
	//insert entry into documents table
	$query = "insert into documents (member_id,ddate,doc,subject,sub_id) values ";
	$query .= "(".$memid.",'";
	$query .= $dt."','";
	$query .= $fname."',";
	$query .= "'From Outlook',";
	$query .= $subscriber.")";	
	
	$result = mysql_query($query) or die(mysql_error().' '.$query);	
		
	
	list($file,$error) = upload('File','documents/clients','jpeg,gif,png,doc,xls,pdf,docx,xlsx,jpg,ppt,rtf,txt',$memid);

	
} else {
	echo "not legitimate file name etc.";
}


?>


