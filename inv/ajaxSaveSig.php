<?php
session_start();
// Get signature string from _POST
$data = $_POST['signature'];
$clientid = $_POST['clientid'];
$data = str_replace('image/jsignature;base30,', '', $data);
$data = implode($data);

include 'jSignature_Tools_Base30.php';
 
// Create jSignature object
$signature = new jSignature_Tools_Base30();
 
// Decode base30 format
$a = $signature->Base64ToNative($data);
 
// Create a image            
$im = imagecreatetruecolor(1295, 328);
 
// Save transparency for PNG
imagesavealpha($im, true);
 
// Fill background with transparency
$trans_colour = imagecolorallocatealpha($im, 0, 0, 0, 127);
imagefill($im, 0, 0, $trans_colour);
 
// Set pen thickness
imagesetthickness($im, 4);
 
// Set pen color to blue            
$blue = imagecolorallocate($im, 0, 0, 255);
 
// Loop through array pairs from each signature word
for ($i = 1; $i < count($a); $i++)
{
    // Loop through each pair in a word
    for ($j = 0; $j < count($a[$i]['x']); $j++)
    {
         // Make sure we are not on the last coordinate in the array
         if ( ! isset($a[$i]['x'][$j]) or ! isset($a[$i]['x'][$j+1])) break;
              // Draw the line for the coordinate pair
              imageline($im, $a[$i]['x'][$j], $a[$i]['y'][$j], $a[$i]['x'][$j+1], $a[$i]['y'][$j+1], $blue);
         }
    }
 
    // Save image to a folder   
	$tm = time(); 
    $filename = '../inv/signatures/'.$clientid.'-'.$tm.'.png'; // Make folder path is writeable
    imagepng($im, $filename); // Removing $filename will output to browser instead of saving
	
	$_SESSION['s_signature'] = $filename;
 
    // Clean up
    imagedestroy($im);

?>
