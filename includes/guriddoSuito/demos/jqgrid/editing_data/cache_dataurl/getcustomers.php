<?php
ini_set("display_errors","1");
require_once '../../jq-config.php';
// include the jqGrid Class
//require_once ABSPATH."php/PHPSuito/jqGridUtils.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

$s = "<select>";
$q = jqGridDB::query($conn,"SELECT CustomerID, CompanyName FROM customers ORDER BY CompanyName");
while($row= jqGridDB::fetch_num($q)) {
	$s .= "<option value='".$row[0]."'>".$row[1]."</option>";
}
$s .= "</select>";
echo $s;

