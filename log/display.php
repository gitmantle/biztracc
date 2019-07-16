<?php

$img = $_GET['img'] ;
$lon = $_GET['lon'] ;
$lar = $_GET['lar'] ;

print('<style type="text/css">');
       print('img { 
            height:100%;
            width:100%}');
print('</style>');

print("<html><title>" . $img . "</title><body onBlur=\"window.close()\">") ;

//print("<div id='Layer1' style='position:absolute; width:" . $lon . "px; height:" . $lar . "px; z-index:1; left: 0; top: 0'> ") ;
print("<div id='Layer1' style='position:absolute; width:800px; height:600px; z-index:1; left: 0; top: 0'> ") ;

print("<a href='#' onClick=\"window.close()\"><img src='" . $img . "' border='0' title='Click to close'></a>") ;

print("</div></body></html>") ;

?>