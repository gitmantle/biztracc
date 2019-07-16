<?php
require_once 'jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
require_once ABSPATH."php/PHPSuito/jqAutocomplete.php";
// include the driver class
require_once ABSPATH."php/PHPSuito/DBdrivers/jqGridPdo.php";

// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");
// Create the jqGrid instance
$grid = new jqGridRender($conn);
// Write the SQL Query
$grid->SelectCommand = 'SELECT a.idresearch, b.idplant, b.name, b.latin_name, a.Article, a.Citation, a.plant_idplant FROM research a, plant b WHERE a.plant_idplant=b.idplant';
// set the ouput format to json
$grid->dataType = 'json';
$grid->table ="research";
$grid->setPrimaryKeyId("idresearch");
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('grid.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Research",
    "rowNum"=>10,
    "sortname"=>"idresearch",
    "hoverrows"=>true,
    "rowList"=>array(10,20,50),
    ));
// Change some property of the field(s)
// Change some property of the field(s)
$grid->setColProperty("idresearch", array("hidden"=>true));
$grid->setColProperty("idplant", array("label"=>"Id", "width"=>20, "editoptions"=>array("readonly"=>"readonly")));
$grid->setColProperty('latin_name', array("label"=>"Latin Name", "width"=>75));
$grid->setColProperty('name', array("label"=>"Common Name", "width"=>75));
$grid->setColProperty("plant_idplant", array("width"=>30,"editoptions"=>array("readonly"=>"readonly")));

$grid->setAutocomplete("name","#plant_idplant","SELECT  b.idplant, b.name, a.plant_idplant FROM research a, plant b WHERE b.name LIKE ? ORDER BY b.name",null,true,true);
$grid->setAutocomplete("latin_name","#plant_idplant","SELECT b.idplant, b.latin_name, a.plant_idplant FROM research a, plant b WHERE b.latin_name LIKE ? ORDER BY latin_name",null,true,true);
$grid->navigator = true;
//NOTE THE recreateForm
$grid->setNavOptions('navigator', array("del"=>false,"excel"=>false,"search"=>false,"refresh"=>true));
$grid->setNavOptions('edit', array("height"=>'auto',"dataheight"=>"auto","width"=>'auto', "recreateForm"=>true));
$grid->setNavOptions('add', array("height"=>'auto',"dataheight"=>"auto", "width"=>'auto', "recreateForm"=>true));
// Enjoy

$form = <<< FORM
function(){
   var ajaxDialog = $('<div id="ajax-dialog" style="display:hidden" title="Plant Edit"></div>').appendTo('body');
   data = {};
   ajaxDialog.load(
      'plant.php',
       data,
       function(response, status){
           ajaxDialog.dialog({
               width: 'auto',
               modal:true,
               open: function(ev, ui){
                  $(".ui-dialog").css('font-size','0.9em');
               },
               close: function(e,ui) {
                   ajaxDialog.remove();
               }
           });
        }
    );
}
FORM;
$buttonoptions = array("#pager",
    array(
      "caption"=>" Add / Edit Plants",
      "onClickButton"=>"js:".$form
    )
);
$grid->callGridMethod("#grid", "navButtonAdd", $buttonoptions);


$grid->showError = true;
// set autocomplete. Serch for name and ID, but select a ID
// set it only for editing and not on serch


// Enjoy
$grid->setSubGridGrid("subgrid.php");

$grid->renderGrid('#grid','#pager',true, null, null, true,true);
