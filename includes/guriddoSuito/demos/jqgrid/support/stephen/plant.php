<?php
ini_set("display_errors","1");
require_once 'jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT idplant, name, latin_name FROM plant';
// set the ouput format to json
$grid->dataType = 'json';
$grid->table ="plant";

/// Obtaining last inser id
$grid->setPrimaryKeyId('idplant');
$grid->serialKey = true;
$grid->getLastInsert = true;
/////

// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('plant.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    //"caption"=>"plant",
    "rowNum"=>10,
    "sortname"=>"name",
    "viewrecords"=>false,
    "pginput"=>false
    //"rowList"=>array(10,20,50)
));

$setrowid = <<< ROWID
$("#grid2").on('jqGridInlineAfterSaveRow', function(ts, rowid, res, tmp, o) {
	var resp  = res.responseText;
	resp = resp.split("#");
	id = resp[1];
	$(this).trigger('reloadGrid');
	setTimeout(function() { $("#grid2").jqGrid('setSelection',id);}, 800);
});
ROWID;

$grid->setJSCode($setrowid);

$grid->setColProperty("idplant", array("width"=>30,"editoptions"=>array("readonly"=>"readonly")));
//$grid->setColProperty("idplant", array("editrules"=>array("required"=>true)));

$grid->setAutocomplete("name","#idplant","SELECT name, name, idplant FROM plant WHERE name LIKE ? ORDER BY name",null,true,true);
$grid->setAutocomplete("latin_name","#idplant","SELECT latin_name, latin_name, idplant FROM plant WHERE latin_name LIKE ? ORDER BY latin_name",null,true,true);

$grid->navigator = true;
$grid->setNavOptions('navigator', array("add"=>false,"edit"=>false,"excel"=>false, "search"=>true));
// and just enable the inline
$grid->inlineNav = true;
$grid->inlineNavEvent('add', 'onSuccess', 'function(a,b){console.log(a,b)}');
$grid->renderGrid('#grid2','#pager2',true, null, null, true,true);
