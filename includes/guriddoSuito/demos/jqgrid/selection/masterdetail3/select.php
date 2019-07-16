<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGridUtils.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
jqGridDB::query($conn,"SET NAMES utf8");
$empid = jqGridUtils::GetParam('id_emp');

// Get details
$SQL = "SELECT * FROM employees WHERE EmployeeID <=".(int)$empid;
$qres = jqGridDB::query($conn, $SQL);
// $result = jqGridDB::fetch_assoc($qres,$conn);
$s = "<select>";
while($result = jqGridDB::fetch_assoc($qres,$conn) ) {
	$s .= "<option>".$result['FirstName']." ".$result['LastName'].'</option>';
}
$s .= "</select>";
echo $s;
jqGridDB::closeCursor($qres);

