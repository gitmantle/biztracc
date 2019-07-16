<?php
session_start();

$findb = $_SESSION['s_findb'];

include 'jq-config.php';
// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);

// Get the needed parameters passed from the main grid
$rowid = jqGridUtils::Strip($_REQUEST["id"]);
if(!$rowid) die("Missed parameters"); 

// Create the jqGrid instance
$grid = new jqGrid($conn);

// enable debugging
//$grid->debug = true;

// Write the SQL Query
$grid->SubgridCommand = "select `item`,`quantity`,`unit`,`value` from ".$findb.".invtrans where `ref_no` = '".$rowid."'";

// set the ouput format to json
$grid->dataType = 'json';
// Use the build in function for the simple subgrid
$grid->querySubGrid(array(&$rowid));
$conn = null; 
?>



