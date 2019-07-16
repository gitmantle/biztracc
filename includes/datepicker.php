<?php

// include the jqUtils Class. The class is needed in all jqSuite components.
require_once "jquery/php/jqUtils.php";

// include the datepicker Class
require_once "jquery/php/jqCalendar.php";

$dp = new jqCalendar();
// set to have button icon
$dp->buttonIcon = true;
// Set it to mydate element
$dp->renderCalendar("#mydate");


?>