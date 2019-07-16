<?php
session_start();
    /*
     *
     * @ Multiple File upload script.
     *
     * @ Can do any number of file uploads
     * @ Just set the variables below and away you go
     *
     * @ Author: Kevin Waterson
     *
     * @copywrite 2008 PHPRO.ORG
     *
     */

    error_reporting(E_ALL);

$usersession = $_SESSION['usersession'];
date_default_timezone_set($_SESSION['s_timezone']);

include_once("../includes/DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$subscriber = $subid;
$sname = $row['uname'];
$userid = $user_id;
$subscriber = $subid;

$cluid = $_SESSION["s_memberid"];

$dt = date("Y-m-d");
$staffmember = $sname;

$cltdb = $_SESSION['s_cltdb'];


    /*** the upload directory ***/
    $upload_dir= '../documents/sub_'.$subscriber.'/clients';

    /*** numver of files to upload ***/
    $num_uploads = 20;

    /*** maximum filesize allowed in bytes ***/
    $max_file_size  = 5120000000;
 
    /*** the maximum filesize from php.ini ***/
    $ini_max = str_replace('M', '', ini_get('upload_max_filesize'));
    $upload_max = $ini_max * 5120000000;

    /*** a message for users ***/
    $msg = 'Please select files for uploading';

    /*** an array to hold messages ***/
    $messages = array();

    /*** check if a file has been submitted ***/
    if(isset($_FILES['userfile']['tmp_name']))
    {
		
        /** loop through the array of files ***/
        for($i=0; $i < count($_FILES['userfile']['tmp_name']);$i++)
        {
            // check if there is a file in the array
            if(!is_uploaded_file($_FILES['userfile']['tmp_name'][$i]))
            {
                $messages[] = 'No file uploaded';
            }
            /*** check if the file is less then the max php.ini size ***/
           // elseif($_FILES['userfile']['size'][$i] > $upload_max)
           // {
            //    $messages[] = "File size exceeds $upload_max php.ini limit";
           // }
            // check the file is less than the maximum file size
            elseif($_FILES['userfile']['size'][$i] > $max_file_size)
            {
                $messages[] = "File size exceeds $max_file_size limit";
            }
            else
            {
                // copy the file to the specified dir 
                if(@copy($_FILES['userfile']['tmp_name'][$i],$upload_dir.'/'.$_SESSION["s_memberid"].'__'.$_FILES['userfile']['name'][$i]))
                {
			
				$fl2 = $_FILES['userfile']['name'][$i];
				$subj = $_REQUEST['subject'.$i];
				//insert entry into documents table
				$db->query("insert into ".$cltdb.".documents (member_id,ddate,doc,staff,subject) values (:member_id,:ddate,:doc,:staff,:subject)");
				$db->bind(':member_id', $_SESSION["s_memberid"]);
				$db->bind(':ddate', $dt);
				$db->bind(':doc', $fl2);
				$db->bind(':staff', $staffmember);
				$db->bind('subject', $subj);
				
				$db->execute();
				
                }
                else
                {
                    /*** an error message ***/
                    $messages[] = 'Uploading '.$_FILES['userfile']['name'][$i].' Failed';
                }
            }
        }
		
		echo  "<script>";
		echo 'window.open("","editmembers").jQuery("#mdoclist").trigger("reloadGrid");';
		echo 'this.close();';
		echo '</script>';
		
    }
	
$db->closeDB();

?>
 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
 <html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
 <head>
 <title>Multiple File Upload</title>
 </head>

 <body>
 
 <h3><?php echo $msg; ?></h3>
 <p>
 <?php
    if(sizeof($messages) != 0)
    {
        foreach($messages as $err)
        {
            echo $err.'<br />';
        }
    }
 ?>
 </p>
 <p>Files to upload &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Subject </p>
 <form enctype="multipart/form-data" action="<?php echo htmlentities($_SERVER['PHP_SELF']);?>" method="post">
 <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size; ?>" />
 <?php
    $num = 0;
    while($num < $num_uploads)
    {
        echo '<div><input name="userfile[]" type="file" /> <input type="text" name="subject'.$num.'" size="70" ></div>';
        $num++;
    }
 ?>

 <input type="submit" value="Upload" />&nbsp;&nbsp;This may take a while depending on file size and number of files. Please be patient.
 </form>

 </body>
 </html>