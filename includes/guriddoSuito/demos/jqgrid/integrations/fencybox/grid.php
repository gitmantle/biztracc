<?php
require_once '../../jq-config.php';
// include the jqGrid Class
require_once ABSPATH."php/PHPSuito/jqGrid.php";
// include the driver class

// Create the jqGrid instance
$grid = new jqGridRender();
// Lets create the model manually and set the formatters
$Model = array(
    array("name"=>"ID","width"=>50),
    array("name"=>"PhotoFileName","width"=>100),
    array("name"=>"Photo","width"=>100,"formatter"=>"js:formatImage", "unformat"=>"js:unformatImage"),
    array("name"=>"Rating","sorttype"=>"integer","formatter"=>"js:formatRating","unformat"=>"js:unformatRating"),
    array("name"=>"Link","formatter"=>"js:formatFancy")
);
// Let the grid create the model
$grid->setColModel($Model);
// Set grid option datatype to be local
$grid->setGridOptions(array("datatype"=>"local", "width"=>400,"height"=>350));
//We can add data manually using arrays
$data = array();
for($i=0;$i<9;$i++)
{
    $data[] = array("ID"=>$i+1,"PhotoFileName"=>($i+1).".jpg","Photo"=>($i+1).".jpg", "Rating"=>rand(-20,30),"Link"=>"Fancybox ".($i+1) );
}
// Let put it using the callGridMethod
$grid->callGridMethod("#grid", 'addRowData', array("ID",$data));
// We can put JS from php
$custom = <<<CUSTOM
// Funcybox formatter
function formatFancy(cellValue, options, rowObject) {
    var link = "<a id='inline' href='images/1.jpg' class='fancybox'>"+cellValue+"</a>";
return link;
}

function formatImage(cellValue, options, rowObject) {
    var imageHtml = "<img src='images/" + cellValue + "' originalValue='" + cellValue + "' />";
return imageHtml;
}
function unformatImage(cellValue, options, cellObject) {
    return $(cellObject.html()).attr("originalValue");
}
function formatRating(cellValue, options, rowObject) {
    var color = (parseInt(cellValue) > 0) ? "green" : "red";
    var cellHtml = "<span style='color:" + color + "' originalValue='" +
                   cellValue + "'>" + cellValue + "</span>";
    return cellHtml;
}
function unformatRating(cellValue, options, cellObject) {
    return $(cellObject.html()).attr("originalValue");
}
CUSTOM;

// Attach Funcybox after every load in Grid complete
$complete = <<< COMPL
function()
{
$(".fancybox", "#grid").fancybox();
}
COMPL;
// Let set the code which is executed at end
$grid->setJSCode($custom);
$grid->setGridEvent( 'gridComplete', $complete );
$grid->renderGrid('#grid','#pager',true, null, null, true,true);

