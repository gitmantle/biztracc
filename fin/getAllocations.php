<?php
session_start();
$usersession = $_SESSION['usersession'];

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
// Write the SQL Query
$grid->SubgridCommand = "select ddate as Date, fromref as From_Ref, toref as To_Ref, amount as Amount from ".$findb.".allocations where (fromref = '".$rowid."' or toref = '".$rowid."')";

// set the ouput format to json
$grid->dataType = 'json';
// Use the build in function for the simple subgrid
$grid->querySubGrid(array(&$rowid));
$conn = null; 
?>



