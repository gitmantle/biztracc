<?php
require_once '../../jq-config.php';
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
$grid->SelectCommand = 'SELECT EmployeeID, FirstName, LastName, BirthDate FROM employees';
// Set the table to where you add the data
$grid->table = 'employees';
// Set output format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// add a action column and instruct the formatter to create the needed butons
// for inline editing
$grid->addCol(array(
    "name"=>"actions",
    "formatter"=>"actions",
    "editable"=>false,
    "sortable"=>false,
    "resizable"=>false,
    "fixed"=>true,
    "width"=>60,
    // use keys to save or cancel a row.
    "formatoptions"=>array("keys"=>true)
    ), "first");
$grid->setColProperty('EmployeeID', array("key"=>true, "editable"=>false, "width"=>50, "label"=>"ID"));
$grid->setColProperty('BirthDate', 
        array("formatter"=>"date",
            "formatoptions"=>array("reformatAfterEdit"=>true, "srcformat"=>"Y-m-d H:i:s", "newformat"=>"m/d/Y H:i"),
// Ok. We use some trick here to create the datepicer on dataInit event
// when the element is created. Note the js: before the function.
// this instruct the grid to put a javascript code without additional formating
            "editoptions"=>array("dataInit"=>
                "js:function(elm){setTimeout(function(){
                    jQuery(elm).datetimepicker({dateFormat:'mm/dd/yy'});
                    jQuery('.ui-datepicker').css({'font-size':'75%'});
                },200);}")
            ));
// Set some grid options
$grid->table = 'employees';
$grid->setPrimaryKeyId('EmployeeID');
$grid->setDbTime('Y-m-d H:i:s');
$grid->setUserTime("m/d/Y H:i");

$grid->setGridOptions(array(
    "rowNum"=>10,
    "rowList"=>array(10,20,30),
    "sortname"=>"EmployeeID"
));
// Enjoy
$grid->renderGrid('#grid','#pager',true, null, null, true,true);
$conn = null;
?>
