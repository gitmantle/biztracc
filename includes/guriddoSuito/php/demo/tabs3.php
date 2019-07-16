<?php
function tabs(array $files)
{
$tabshead = <<<HEAD1
<div id="tabs"  style="margin-left:18px;">
    <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active"><a href="#DescriptionContent"><span>Info</span></a></li>
        <li role="presentation" ><a href="#HTMLContent"><span>HTML</span></a></li>
        <li role="presentation" ><a href="#PHPCode"><span>PHP</span></a></li>
    </ul>
	<div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="DescriptionContent" style="font-size:1.1em !important">
HEAD1;
echo $tabshead;
// info file
$filename = "info.txt";
$lines = file($filename);

// Loop through our array, show HTML source as HTML source; and line numbers too.
foreach ($lines as $line_num => $line) {
    echo $line . "<br />\n";
}
// html content
echo "</div>";
echo '<div role="tabpanel" class="tab-pane" id="HTMLContent" style="font-size:1.1em !important">';
$filename = "default.php";
highlight_file($filename);
echo "</div>";
// php code
echo '<div role="tabpanel" class="tab-pane" id="PHPCode" style= "font-size:1em !important">';
for($i=0;$i<count($files);$i++)
{
    $filename = $files[$i];
    echo '<span style="font-size:1.2em !important">';
    echo "<b>".$filename."</b>.<br />\n";
    if(strlen($filename)>0)
    {
        highlight_file($filename);
    }
    echo "<span>";
}
echo "</div>"; // php code
echo "</div>"; // 
echo "</div>"; // tabs
echo '<script type="text/javascript">';
//echo '//$("#tabs").tab();';
echo "$('#tabs ul li a').click(function (e) { e.preventDefault();  $(this).tab('show'); });";
echo '</script>';
$google = <<<GOOGLE
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
try { var pageTracker = _gat._getTracker("UA-5463047-4"); pageTracker._trackPageview(); } catch(err) {}
</script>
GOOGLE;
echo $google;
}
?>
