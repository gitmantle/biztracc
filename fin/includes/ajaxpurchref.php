<?php
session_start();

$purchref = $_REQUEST['purchref'];

$_SESSION['s_purchref'] = $purchref;

return;

?>