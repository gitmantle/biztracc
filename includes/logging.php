<?php
 
    function send_to_log($sReport,$bTimeStamp=false)
    {
        //global $g_sSystemRoot;
        //$sDocRoot=$g_sSystemRoot;
        if(isset($_SERVER["DOCUMENT_ROOT"])&&strlen($_SERVER["DOCUMENT_ROOT"]))
            $sDocRoot=$_SERVER["DOCUMENT_ROOT"];        
        $sDocRoot=trim(str_ireplace("\\","/",$sDocRoot),"/");\
		
		//$sDocRoot = "c:/wamp64/www/biztracc";
        
    	if(is_array($sReport))
		{
			$sReport = print_r($sReport,true);
			
			if($bTimeStamp)
	            $sReport = date("F j, Y, H:i:s").": ".$sReport;
	        file_put_contents("{$sDocRoot}/logs/log.txt","{$sReport}\n",FILE_APPEND);
		}
		else
		{
			
			
			echo "{$sDocRoot}/includes/logs/log.txt","{$sReport}\n";
			
	        if($bTimeStamp)
	            $sReport = date("F j, Y, H:i:s").": ".$sReport;
	        file_put_contents("{$sDocRoot}/includes/logs/log.txt","{$sReport}\n",FILE_APPEND);
		}
    } 
	
	

	
?>