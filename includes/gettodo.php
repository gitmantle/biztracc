<?php
session_start();
ini_set('display_errors', true);
$usersession = $_SESSION['usersession'];

include_once("DBClass.php");
$db = new DBClass();

$db->query("select * from sessions where session = :vusersession");
$db->bind(':vusersession', $usersession);
$row = $db->single();
$subid = $row['subid'];
$user_id = $row['user_id'];
$sname = $row['uname'];

if (isset($_REQUEST['st_mask'])) {
	$stname = $_REQUEST['st_mask'];
} else {
	$stname = $sname;
}
if (isset($_REQUEST['uno'])) {
	$uno = $_REQUEST['uno'];
} else {
	$uno = $user_id;
}

$moduledb = 'infinint_sub'.$subid;

$where = 'where todo_by = "'.$stname.'"';

$table = 'ztmp'.$uno.'_tasks';

$db->query("drop table if exists ".$moduledb.".".$table);
$db->execute();

$db->query("create table ".$moduledb.".".$table." ( todo_id int(11),enter_date date default '0000-00-00',enter_staff varchar(50) default '',todo_by varchar(50) default '',complete_by date default '0000-00-00', task varchar(250) default '',done char(3) default '',category varchar(20) default '' )  engine myisam");
$db->execute();

$db->query('SELECT todo_id as tid,enter_date as edt,enter_staff as est,todo_by as tdt,complete_by as cdt,task as tsk,done as dn,category as cat from '.$moduledb.'.todo '.$where);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query('insert into '.$moduledb.'.'.$table.' (todo_id,enter_date,enter_staff,todo_by,complete_by,task,done,category) values (:todo_id,:enter_date,:enter_staff,:todo_by,:complete_by,:task,:done,:category)');
	$db->bind(':todo_id', $tid);
	$db->bind(':enter_date', $edt);
	$db->bind(':enter_staff', $est);
	$db->bind(':todo_by', $tdt);
	$db->bind(':complete_by', $cdt);
	$db->bind(':task', $tsk);
	$db->bind(':done', $dn);
	$db->bind(':category', $cat);
	
	$db->execute();
}

$db->closeDB();

$host = $_SESSION['s_server'];
define('DB_DSN','mysql:host='.$host.';dbname='.$moduledb);
define('DB_USER', 'infinint_sagana');     // Your MySQL username
define('DB_PASSWORD', 'dun480can'); // ...and password

define('ABSPATH', dirname(__FILE__).'/');

// include the jqGrid Class
require_once "../includes/jquery/php/jqGrid.php";
// include the PDO driver class
require_once "../includes/jquery/php/jqGridPdo.php";
// Connection to the server
$conn = new PDO(DB_DSN,DB_USER,DB_PASSWORD);
// Tell the db that we use utf-8
$conn->query("SET NAMES utf8");

// Create the jqGrid instance
$grid = new jqGridRender($conn);

//$grid->debug = true;

// Write the SQL Query
// We suppose that mytable exists in your database
$grid->SelectCommand = 'SELECT todo_id,enter_date,enter_staff,todo_by,complete_by,task,done,category from '.$table;

// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('gettodo.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Task List",
    "rowNum"=>12,
    "sortname"=>"complete_by",
	"sortorder"=>"desc",
    "rowList"=>array(12,30,50),
	"height"=>400,
	"width"=>970
    ));

$grid->addCol(array("name"=>"act"),"last");

// Change some property of the field(s)
$grid->setColProperty("todo_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("enter_date", array("label"=>"Entered", "width"=>80, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("enter_staff", array("label"=>"By", "width"=>150));
$grid->setColProperty("todo_by", array("label"=>"Responsibility of", "width"=>150));
$grid->setColProperty("complete_by", array("label"=>"Complete by", "width"=>80,"formatter"=>"date","formatoptions"=>array("srcformat"=>"Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("task", array("label"=>"Task", "width"=>200));
$grid->setColProperty("done", array("label"=>"Done", "width"=>40));
$grid->setColProperty("category", array("label"=>"Category", "width"=>90));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>95));

$loadevent = <<<LOADCOMPLETE
function(rowid){
	var ids = jQuery("#todolist").getDataIDs();
	for(var i=0;i<ids.length;i++){
			var rowd = $("#todolist").getRowData(ids[i]);
			var cl = ids[i];
			var dn = rowd.done;
			be = '<img src="../images/edit.png" title="Edit" onclick="javascript:tedittodo('+cl+')" ></ids>';
			em = '<img src="../images/email.png" title="Email" onclick="javascript:temailtodo()" ></ids>';
			if (dn == 'Yes') {
				fn = '<img src="../images/tick.png" title="Done"></ids>';
			} else {
				fn = '<img src="../images/hourglass.png" title="Mark as Done" onclick="javascript:tdonetodo('+cl+')" ></ids>';
			}
			de = '<img src="../images/delete.png" title="Delete" onclick="javascript:tdeltodo('+cl+')" ></ids>';
			jQuery("#todolist").setRowData(ids[i],{act:be+'&nbsp;&nbsp;&nbsp'+em+'&nbsp;&nbsp;&nbsp'+fn+'&nbsp;&nbsp;&nbsp'+de}); 
	}
}
LOADCOMPLETE;

$grid->setGridEvent("loadComplete",$loadevent);

// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));
// add a custom button via the build in callGridMethod
// note the js: before the function
$buttonoptions = array("#todopager",
    array("buttonicon"=>"ui-icon-plus","caption"=>"","position"=>"first","title"=>"Add a ToDo Item", "onClickButton"=>"js: function(){taddtodo('M');}")
);
$grid->callGridMethod("#todolist", "navButtonAdd", $buttonoptions); 

$buttonoption2 = array("#todopager",
    array("buttonicon"=>"ui-icon-newwin","caption"=>"","position"=>"last","title"=>"Export to Excel", "onClickButton"=>"js: function(){xl_todo();}")
);
$grid->callGridMethod("#todolist", "navButtonAdd", $buttonoption2); 

// Run the script
$grid->renderGrid('#todolist','#todopager',true, null, null, true,true);

?>




