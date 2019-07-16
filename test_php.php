<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Test PHP</title>
</head>

<body>

<?php


include_once("includes/DBClass.php");
$db = new DBClass();

$findb = 'infinint_fin40_19';

$db->query("select symbol from ".$findb.".forex where currency = 'GBP'");
$row = $db->single();
extract($row);

echo 'symbol is '.$symbol;



$db->closeDB();

?>

</body>
</html>