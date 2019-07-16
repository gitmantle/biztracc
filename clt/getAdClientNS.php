<?php
session_start();
$usersession = $_SESSION['usersession'];

$cltdb = $_SESSION['s_cltdb'];

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
$grid->SubgridCommand = "select address_type.address_type as Type,addresses.street_no as Addr_1,addresses.ad1 as Addr_2,addresses.suburb as Suburb,addresses.town as Town from ".$cltdb.".addresses,".$cltdb.".address_type where addresses.address_type_id = address_type.address_type_id and addresses.member_id = ".$rowid;
 

// set the ouput format to json
$grid->dataType = 'json';
// Use the build in function for the simple subgrid
$grid->querySubGrid(array(&$rowid));
$conn = null; 
?>



