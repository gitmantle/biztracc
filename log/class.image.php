<?php

class AffichImage
{
	// show directory content
	function showDir($incid, $dir, $i, $lar, $lon, $lin)
	{
		// style to make the border round
		print("<style>.box { border-style: outset ; border-width: 1px ; border-color: #A3C5CC ; border-radius: 8px ; -moz-border-radius: 8px }</style>") ;
		//script to open new windows to display the picture
		print("<script language='JavaScript'>
		<!--
		function MM_openBrWindow(theURL,winName,features) { //v2.0
		  window.open(theURL,winName,features);
		}
		//-->
		</script>") ;
		
		
		$i++;
		//if($checkDir = opendir($dir))
		//{
			$cFile = 0;
			
			$moduledb = $_SESSION['s_logdb'];
			mysql_select_db($moduledb) or die(mysql_error());
		
			$qp = "select picture from incpictures where incident_id = ".$incid;
			$rp = mysql_query($qp) or die (mysql_error());
			while ($row = mysql_fetch_array($rp)) {
				extract($row);
				$listFile[$cFile] = $picture;
				$cFile++;
			}

			// show files
			if(count(@$listFile) > 0)
			{
				print("<table cellspacing='1' cellpadding='0' border='0'><tr>") ;
				
				sort($listFile);
				
				$x = 0 ;
				
				for($k = 0; $k < count($listFile); $k++)
				{
					$spacer = "";
					
					for($l = 0; $l < $i; $l++)
						$spacer .= "&emsp;";
					
					if($x % $lin == 0)
						print("</tr><tr>") ;

					$img = $listFile[$k] ;
					
					$tmp = explode(".", $img) ;
					
					$x++ ;
					
					//display only that type of images
					if($tmp[1] == 'jpg' OR $tmp[1] == 'bmp' OR $tmp[1] == 'gif' OR $tmp[1] == 'png' 
					OR $tmp[1] == 'tga' OR $tmp[1] == 'tif' OR $tmp[1] == 'eps')
					{
						
						print("<td>") ;
						print("<table class='box' bgcolor='#9ABBC1' cellspacing='0' cellpadding='0' border='0' width='" . ($lon+10) . "' height='" . ($lar+27) . "'><tr>") ;
						
						//do not display this line if you do not want the number on the picture
						print("<td align='center'><small><b>" . ($k+1) . "</b></small></td></tr><tr>") ;
						
						print("<td align='center'>") ;
					
						$srcimg = $dir . "/" . $spacer . $img ;
					
						list($width, $height, $type, $attr) = getimagesize($srcimg);
					
						print("<a href='#' onClick=\"MM_openBrWindow('display.php?img=$srcimg&lon=$width&lar=$height','','width=800,height=600,left=100,top=100,scrollbars=no,toolbars=no')\">") ;
						print("<img src='" . $srcimg . "' width='" . $lar . "' height='" . $lon . "' alt='" . $img . "' title='" . $img . "' border='0'></a></td>");
						print("</tr></table>\n") ;
						print("</td>") ;
					}
					else 
						$x-- ;
				}
				print("</tr></table>\n") ;
				
				//tell how many image found in the folder
				print("<br><small>" . $k . " Images displayed<b></b></small>") ;
			}
			//closedir($checkDir);
		//}
	}

}

?>