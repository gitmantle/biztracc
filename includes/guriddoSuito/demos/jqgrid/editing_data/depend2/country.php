<?php
require_once '../../jq-config.php';
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
try {
	$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
	$SQL =  "SELECT DISTINCT Country id, Country value FROM customers ORDER BY Country";
	$collation = jqGridDB::query($conn, "SET NAMES utf8");
	$country = jqGridDB::query($conn, $SQL);
	$result = jqGridDB::fetch_object($country, true, $conn);
	echo json_encode($result);
} catch (Exception $e) {
	echo $e->getMessage();
}
