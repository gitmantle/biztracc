<?php
ini_set('display_errors', true);

				
				$file_id = $_FILES['file']['name'];
				$folder = 'incidents';
				$types = 'jpeg,gif,png,jpg,pjpeg';
				$result = 2;
				
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
					$result = "5 Cannot upload the file '".$_FILES['file']['name']."'"; //Show error if any.
					if(!file_exists($folder)) {
						$result .= "5 : Folder doesn't exist.";
					} elseif(!is_writable($folder)) {
						$result .= "5 : Folder not writable.";
					} elseif(!is_writable($uploadfile)) {
						$result .= "5 : File not writable.";
					}
					$file_name = '';
					
				} else {
					if(!$_FILES['file']['size']) { //Check if the file is made
						@unlink($uploadfile);//Delete the Empty file
						$file_name = '';
						$result = "5 Empty file found - please use a valid file."; //Show the error message
					} else {
						chmod($uploadfile,0755);//0777 to Make it universally writable.
					}
				}
			
				//return array($file_name,$result);
				echo $result;
	
?>


