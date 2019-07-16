<?php
session_start();
//ini_set('display_errors', true);

$usersession = $_SESSION['usersession'];

$tfile = $_SESSION['s_tfile'];
$type = $_SESSION['s_findoc'];
$id = $_REQUEST['id'];

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

$hdate = date('Y-m-d');
$ttime = strftime("%H:%M", time());

$findb = $_SESSION['s_findb'];

$db->query("select * from ".$findb.".".$tfile." where uid = ".$id);
$row = $db->single();
$item = $row['item'];
$include = $row['include'];
$tab = $row['tab'];
$title = $row['title'];
$xcoord = $row['xcoord'];
$ycoord = $row['ycoord'];
$fontparam = $row['font'];
$drawcolor = $row['hdrawcolor'];
$fillcolor = $row['hfillcolor'];
$textcolor = $row['htextcolor'];
$linewidth = $row['linewidth'];
$cellwidth = $row['cellwidth'];
$gridwidths = $row['gridwidths'];
$cellheight = $row['cellheight'];
$content = $row['content'];
$border = $row['border'];
$align = $row['align'];
$fill = $row['fill'];
$alpha = $row['alpha'];
if ($item == 'page') {
	$st = explode(',',$content);
	$orient = $st[0];
	$units = $st[1];
	$paper = $st[2];
}
$st = explode(',',$fontparam);
$font = $st[0];
$attrib = $st[1];
$fontsize = $st[2];
if ($item == 'image') {
	$i_file = $content;
}	

$db->closeDB();

$theme = $_SESSION['deftheme'];
require_once("../includes/backgrounds.php");

// populate include list
    $arr = array('N', 'Y');
	$include_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $include) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$include_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate orientation list
    $arr = array('P', 'L');
	$orient_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $orient) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$orient_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate paper list
    $arr = array('A4', 'A3', 'A5');
	$paper_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $paper) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$paper_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}

// populate font list
    $arr = array('Arial', 'Times', 'Courier');
	$font_options = '';
    for($i = 0; $i < count($arr); $i++)	{
		if ($arr[$i] == $font) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}	
		$font_options .= '<option value="'.$arr[$i].'"'.$selected.'>'.$arr[$i].'</option>';
 	}






?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Edit Financial Document Properties</title>
<link rel="stylesheet" href="../includes/mantle.css" media="screen" type="text/css">
<link type="text/css" href="../includes/jquery/themes/<?php echo $theme; ?>/jquery.ui.all.css" rel="stylesheet" />
<link rel="stylesheet" type="text/css" href="../includes/jquery/themes/ui.jqgrid.css" />
<link rel="stylesheet" media="screen" type="text/css" href="../includes/color_picker/css/colorpicker.css" />
<script src="../includes/jquery/js/jquery.js" type="text/javascript"></script>
<script src="../includes/jquery/js/i18n/grid.locale-en.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery-ui-custom.min.js" type="text/javascript"></script>
<script src="../includes/jquery/js/jquery.jqGrid.min.js" type="text/javascript"></script>
<script type="text/javascript" src="../includes/jquery/ui/jquery.ui.widget.js"></script>
<script src="../includes/color_picker/jscolor.js"></script>
<script>

window.name = "hs_editprops";


function stopRKey(evt) {
  var evt = (evt) ? evt : ((event) ? event : null);
  var node = (evt.target) ? evt.target : ((evt.srcElement) ? evt.srcElement : null);
  if ((evt.keyCode == 13) && (node.type=="text"))  {return false;}
}



function post() {

	//add validation here if required.
	var ok = "Y";
	
	if (ok == "Y") {
		document.getElementById('savebutton').value = "Y";
		document.getElementById('edprop').submit();
	}
	
	
}

</script>
<style type="text/css">
<!--
.style1 {
	font-size: large
}
.star {
	color: #F00;
}
-->
</style>
</head>
<body>
<form name="edprop" id="edprop" method="post" enctype="multipart/form-data">
  <input type="hidden" name="savebutton" id="savebutton" value="N">
  <table width="882" border="0" align="left">
    <tr bgcolor="<?php echo $bghead; ?>">
      <td class="boxlabelleft"><label style="color: <?php echo $thfont; ?> "><strong>Edit Properties of <?php echo $title; ?></strong></label></td>
    </tr>
  </table>
  <table width="880" border="0" align="left" bgcolor="#FFFFFF">
  <?php
  	if ($item == 'page') {
		echo '<tr>';
			echo '<td class="boxlabelleft">Portrait or Landscape</td>';
			echo '<td><select name="orient" id="orient">'.$orient_options.'</select></td>';
		echo '</tr>';		
		echo '<tr>';
			echo '<td class="boxlabelleft">Paper size</td>';
			echo '<td><select name="paper" id="paper">'.$paper_options.'</select></td>';
		echo '</tr>';
	} else {
		echo '<tr>';
			echo '<td class="boxlabelleft">Include</td>';
			echo '<td><select name="include" id="include">'.$include_options.'</select></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td class="boxlabelleft">X coordinate - distance in mm from left</td>';
			echo '<td class="boxlabelleft"><input name="xcoord" id="xcoord" type="text" size="7" value="'.$xcoord.'"></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td class="boxlabelleft">Y coordinate - distance in mm from top</td>';
			echo '<td class="boxlabelleft"><input name="ycoord" id="ycoord" type="text" size="7" value="'.$ycoord.'"></td>';
		echo '</tr>'; 
		if ($tab == 'label') {
			echo '<tr>';
				echo '<td class="boxlabelleft">Content</td>';
				echo '<td class="boxlabelleft"><input name="content" id="content" type="text" size="30" value="'.$content.'"></td>';
			echo '</tr>'; 
		}
		echo '<tr>';
			echo '<td class="boxlabelleft">Font</td>';
			echo '<td class="boxlabelleft"><select name="font" id="font">'.$font_options.'</select></td>';
		echo '</tr>';       
		echo '<tr>';
			echo '<td class="boxlabelleft">Font attribute - Bold, Italic, Underline or any combination</td>';
			echo '<td class="boxlabelleft"><input name="fattrib" id="fattrib" type="text" size="10" value="'.$attrib.'"></td>';
		echo '</tr>';      
		echo '<tr>';
			echo '<td class="boxlabelleft">Font size</td>';
			echo '<td class="boxlabelleft"><input name="fsize" id="fsize" type="text" size="7" value="'.$fontsize.'"></td>';
		echo '</tr>';       
 		echo '<tr>';
			echo '<td class="boxlabelleft">Drawing colour - lines, rectangles, borders etc </td>';
		?>	
        	<td> <input name="drawcolor" class="jscolor" value="<?php echo $drawcolor; ?>"></td>
        
         <?php
			  //echo '<td class="boxlabelleft"><input name="drawcolor" id="drawcolor" type="text" size="20" value="'.$drawcolor.'"></td>';
		echo '</tr>';  
  		echo '<tr>';
			echo '<td class="boxlabelleft">Fill colour </td>';
		?>	
        	<td> <input name="fillcolor" class="jscolor" value="<?php echo $fillcolor; ?>"></td>
        
         <?php
			
			//echo '<td class="boxlabelleft"><input name="fillcolor" id="fillcolor" type="text" size="20" value="'.$fillcolor.'"></td>';
		echo '</tr>';    
 		echo '<tr>';
			echo '<td class="boxlabelleft">Text colour </td>';
		?>	
        	<td> <input name="textcolor" class="jscolor" value="<?php echo $textcolor; ?>"></td>
        
         <?php
			
			//echo '<td class="boxlabelleft"><input name="textcolor" id="textcolor" type="text" size="20" value="'.$textcolor.'"></td>';
		echo '</tr>';  
		echo '<tr>';
			echo '<td class="boxlabelleft">Line thickness</td>';
			echo '<td class="boxlabelleft"><input name="linewidth" id="linewidth" type="text" size="7" value="'.$linewidth.'"></td>';
		echo '</tr>';       
		if ($tab != 'box') {
			echo '<tr>';
				echo '<td class="boxlabelleft">Width of content cell in mm</td>';
				echo '<td class="boxlabelleft"><input name="cellwidth" id="cellwidth" type="text" size="7" value="'.$cellwidth.'"></td>';
			echo '</tr>';       
			echo '<tr>';
				echo '<td class="boxlabelleft">Height of content cell in mm</td>';
				echo '<td class="boxlabelleft"><input name="cellheight" id="cellheight" type="text" size="7" value="'.$cellheight.'"></td>';
			echo '</tr>'; 
			echo '<tr>';
				echo '<td class="boxlabelleft">Draw cell border 0 = No, 1 = Yes</td>';
				echo '<td class="boxlabelleft"><input name="border" id="border" type="text" size="7" value="'.$border.'"></td>';
			echo '</tr>';       
			echo '<tr>';
				echo '<td class="boxlabelleft">Fill cell with colour 0 = No, 1 = Yes</td>';
				echo '<td class="boxlabelleft"><input name="fill" id="fill" type="text" size="7" value="'.$fill.'"></td>';
			echo '</tr>'; 
		} else {
			echo '<tr>';
				echo '<td class="boxlabelleft">Width of box in mm</td>';
				echo '<td class="boxlabelleft"><input name="cellwidth" id="cellwidth" type="text" size="7" value="'.$cellwidth.'"></td>';
			echo '</tr>';       
			echo '<tr>';
				echo '<td class="boxlabelleft">Height of box in mm</td>';
				echo '<td class="boxlabelleft"><input name="cellheight" id="cellheight" type="text" size="7" value="'.$cellheight.'"></td>';
			echo '</tr>'; 
			echo '<tr>';
				echo '<td class="boxlabelleft">Fill cell with colour 0 = No, 1 = Yes</td>';
				echo '<td class="boxlabelleft"><input name="fill" id="fill" type="text" size="7" value="'.$fill.'"></td>';
			echo '</tr>';	
		}
		if ($tab == 'box') {
			echo '<tr>';
				echo '<td class="boxlabelleft">Transparency - range from 0 (transparent) to 1 (opaque)</td>';
				echo '<td class="boxlabelleft"><input name="alpha" id="alpha" type="text" size="7" value="'.$alpha.'"></td>';
			echo '</tr>';
		}
		if ($item == 'griddetail') {
			echo '<tr>';
				echo '<td class="boxlabelleft">Grid Widths - mm for each column eg. 25,50,15 etc</td>';
				echo '<td class="boxlabelleft"><input name="gridwidths" id="gridwidths" type="text" size="50" value="'.$gridwidths.'"></td>';
			echo '</tr>';       
			echo '<tr>';
				echo '<td class="boxlabelleft">Alignment in cell L= left, R = Right, C = Centre</td>';
				echo '<td class="boxlabelleft"><input name="align" id="align" type="text" size="50" value="'.$align.'"></td>';
			echo '</tr>';       
			echo '<tr>';
				echo '<td class="boxlabelleft">C - character, N - numeric</td>';
				echo '<td class="boxlabelleft"><input name="content" id="content" type="text" size="75" value="'.$content.'"></td>';
			echo '</tr>';  
			echo '<tr>';
			echo '<td class="boxlabelleft">Document columns</td>';
			echo '<td class="boxlabelleft">';
			switch ($type) {
				case 'qot':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 's_o':
					echo 'Code,Item,Quantity,Unit';
					break;
				case 'p_o':
					echo 'Code,Item,Quantity,Unit';
					break;
				case 'grn':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 'inv':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 'c_s':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 'c_p':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 'rec':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 'pay':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 'crn':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 'ret':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 'req':
					echo 'Code,Item,Quantity,Unit Price,Value,Discount,Tax,Total';
					break;
				case 'pkl':
					echo 'Code,Item,Unit,Required,Picked';
					break;
				case 'd_n':
					echo 'Code,Item,Quantity,Unit';
					break;
			}
			echo '</td>';
			echo '</tr>';			
		} 
		if ($item == 'gridtitle') {
		
			echo '<tr>';
				echo '<td class="boxlabelleft">Grid Widths - mm for each column eg. 25,50,15 etc</td>';
				echo '<td class="boxlabelleft"><input name="gridwidths" id="gridwidths" type="text" size="50" value="'.$gridwidths.'"></td>';
			echo '</tr>';       
			echo '<tr>';
				echo '<td class="boxlabelleft">Alignment in cell L= left, R = Right, C = Centre</td>';
				echo '<td class="boxlabelleft"><input name="align" id="align" type="text" size="50" value="'.$align.'"></td>';
			echo '</tr>';       
			echo '<tr>';
				echo '<td class="boxlabelleft">Content</td>';
				echo '<td class="boxlabelleft"><input name="content" id="content" type="text" size="75" value="'.$content.'" readonly></td>';
			echo '</tr>';  
			
		}
		if ($item == 'image') {
			echo '<tr>';
				echo '<td class="boxlabelleft">Logo image - jpg recommend 500 x 333 pixels</td>';
				echo '<td><input type="file" name="image" /></td>';
			echo '</tr>'; 
			echo '<tr>';
				echo '<td>&nbsp;</td>';
				echo '<td class="boxlabelleft"><input type="submit" name="savepic" value="Upload Selected Picture"> </td>';
			echo '</tr>';

		}
   }

   
   ?> 
   <tr>
     <td><input name="save" type="button" value="Save" onclick="post()" ></td>
   </tr>  
   
  </table>
  <script>document.onkeypress = stopRKey;</script>
</form>

<?php

	if(isset($_POST['savepic']) && trim($_POST['savepic']) != '' ) {
		$target_dir = "../images/";
		$t_file = basename($_FILES["image"]["name"]);
		$uploadOk = 1;
		$imageFileType = strtoupper(pathinfo($t_file,PATHINFO_EXTENSION));
		$target_file = $target_dir.$t_file;
		
	  // Check if image file is a actual image or fake image
		  $check = getimagesize($_FILES["image"]["tmp_name"]);
		  if($check !== false) {
			  echo "<script>";
			  echo 'alert("File is an image - " . $check["mime"] . ".");';
			  echo "</script>";
			  $uploadOk = 1;
		  } else {
			  echo "<script>";
			  echo 'alert("File is not an image.");';
			  echo "</script>";
			  $uploadOk = 0;
		  }
		
		// Check if file already exists
		if (file_exists($target_file)) {
			echo "<script>";
			echo 'alert("Sorry, file already exists.");';
			echo "</script>";
			$uploadOk = 0;
		}
		// Check file size
		if ($_FILES["image"]["size"] > 500000) {
			echo "<script>";
			echo 'alert("Sorry, your file is too large.");';
			echo "</script>";
			$uploadOk = 0;
		}
		
		// Allow certain file formats
		if($imageFileType != "JPG" ) {
			echo "<script>";
			echo 'alert("Sorry, only JPG files are allowed.");';
			echo "</script>";
			$uploadOk = 0;
		}

		// Check if $uploadOk is set to 0 by an error
		if ($uploadOk == 0) {
			echo "<script>";
			echo 'alert("Sorry, your file was not uploaded.");';
			echo "</script>";
		// if everything is ok, try to upload file
		} else {
			if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
				
				include_once("../includes/DBClass.php");
				$db = new DBClass();
				
				$db->query("update ".$findb.".".$tfile." set content = :ipic where item = :id");
				$db->bind(':ipic', '../images/'.$target_file);
				$db->bind(':id', 'image');
				$db->execute();
				$db->closeDB();
				
				$i_file = '../images/'.$target_file;
				
				echo "<script>";
				echo "location.reload();";
				echo "</script>";
			} else {
				echo "<script>";
				echo 'alert("Sorry, there was an error uploading your file.");';
				echo "</script>";
			}
		}
	}

	if($_REQUEST['savebutton'] == "Y") {
		
		
		function hex2rgb($hex) {
		  $hex = str_replace('#', '', $hex);
		  if ( strlen($hex) == 6 ) {
			  $r = hexdec(substr($hex, 0, 2));
			  $g = hexdec(substr($hex, 2, 2));
			  $b = hexdec(substr($hex, 4, 2));
		   }
		   else if ( strlen($hex) == 3 ) {
			  $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
			  $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
			  $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
		   }
		   else {
			  $r = '0';
			  $g = '0';
			  $b = '0';
		   }
		   return $r.','.$g.','.$b;
		}
		
		
		$sinclude = $_REQUEST['include'];
		$sxcoord = $_REQUEST['xcoord'];
		$sycoord = $_REQUEST['ycoord'];
		$sfontparam = $_REQUEST['font'];
		$sdrawcolor = hex2rgb($_REQUEST['drawcolor']);
		$sfillcolor = hex2rgb($_REQUEST['fillcolor']);
		$stextcolor = hex2rgb($_REQUEST['textcolor']);
		$shdrawcolor = $_REQUEST['drawcolor'];
		$shfillcolor = $_REQUEST['fillcolor'];
		$shtextcolor = $_REQUEST['textcolor'];
		$slinewidth = $_REQUEST['linewidth'];
		$scellwidth = $_REQUEST['cellwidth'];
		$sgridwidths = $_REQUEST['gridwidths'];
		if ($sgridwidths == '') {
			$sgridwidths = ' ';
		}
		$scellheight = $_REQUEST['cellheight'];
		$scontent = $_REQUEST['content'];
		if ($scontent == '') {
			$scontent = ' ';
		}
		if ($item == 'image') {
			$scontent = $i_file;
		}
		$sborder = $_REQUEST['border'];
		$salign = $_REQUEST['align'];
		$sfill = $_REQUEST['fill'];
		$salpha = $_REQUEST['alpha'];
		
		if ($item == 'page') {
			$st = explode(',',$content);
			$orient = $st[0];
			$units = $st[1];
			$paper = $st[2];
			$spage = $orient.','.$units.','.$paper;
			if ($spage == '') {
				$content = ' ';
			}

		}
		$font = $_REQUEST['font'];
		$attrib = $_REQUEST['fattrib'];
		$fontsize = $_REQUEST['fsize'];	
		$sfont = $font.','.$attrib.','.$fontsize;
		
/*		
		echo 'include '.$sinclude;
		echo 'xcoord '.$sxcoord ;
		echo 'ycoord '.$sycoord;
		echo 'drawcolor '.$sdrawcolor ;
		echo 'fillcolor '.$sfillcolor ;
		echo 'textcolor '.$stextcolor ;
		echo 'linewidth ',$slinewidth;
		echo 'cellwidth '.$scellwidth ;
		echo 'gridwidths '.$sgridwidths;
		echo 'cellheight '.$scellheight;
		echo 'content '.$scontent;
		echo 'border '.$sborder;
		echo 'align '.$salign;
		echo 'fill '.$sfill;
		echo 'page '.$spage;
		echo 'font '.$sfont;
		echo 'id '.$id;
*/		
		
		
		
		
		include_once("../includes/DBClass.php");
		$db = new DBClass();
		
		$db->query("update ".$findb.".".$tfile." set include = :include, xcoord = :xcoord, ycoord = :ycoord, font = :font, hdrawcolor = :hdrawcolor, hfillcolor = :hfillcolor, htextcolor = :htextcolor, drawcolor = :drawcolor, fillcolor = :fillcolor, textcolor = :textcolor, linewidth = :linewidth, cellwidth = :cellwidth, gridwidths = :gridwidths, cellheight = :cellheight, content = :content, border = :border, align = :align, fill = :fill, alpha = :alpha where uid = :id");
		$db->bind(':id', $id);
		$db->bind(':include', $sinclude);
		$db->bind(':xcoord', $sxcoord);
		$db->bind(':ycoord', $sycoord);
		$db->bind(':font', $sfont);
		$db->bind(':drawcolor', $sdrawcolor);
		$db->bind(':fillcolor', $sfillcolor);
		$db->bind(':textcolor', $stextcolor);
		$db->bind(':hdrawcolor', $shdrawcolor);
		$db->bind(':hfillcolor', $shfillcolor);
		$db->bind(':htextcolor', $shtextcolor);
		$db->bind(':linewidth', $slinewidth);
		$db->bind(':cellwidth', $scellwidth);
		$db->bind(':gridwidths', $sgridwidths);
		$db->bind(':cellheight', $scellheight);
		$db->bind(':content', $scontent);
		$db->bind(':border', $sborder);
		$db->bind(':align', $salign);
		$db->bind(':fill', $sfill);
		$db->bind(':alpha', $salpha);

		$db->execute();
		$db->closeDB();
		
		echo '<script>';
		echo 'window.opener.refreshfindocgrid();';
		echo 'this.close();';
		echo '</script>';
	}


?>

</body>
</html>
