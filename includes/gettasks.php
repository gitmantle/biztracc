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

$cltdb = 'infinint_sub'.$subid;

$table = 'ztmp'.$user_id.'_tasks';
$stname = $sname;
$uno = $user_id;

$where = 'where done = "No" and todo_by = "'.$stname.'"';

$db->query("drop table if exists ".$cltdb.".".$table);
$db->execute();

$db->query("create table ".$cltdb.".".$table." ( todo_id int(11),complete_by date default '0000-00-00', task varchar(250) default '')  engine myisam");
$db->execute();

$db->query('SELECT todo_id as tid,complete_by as cdt,task as tsk from '.$cltdb.'.todo '.$where);
$rows = $db->resultset();
foreach ($rows as $row) {
	extract($row);
	$db->query('insert into '.$cltdb.'.'.$table.' (todo_id,complete_by,task) values ('.$tid.',"'.$cdt.'","'.$tsk.'")');
	$db->execute();
}

$db->closeDB();

$host = $_SESSION['s_server'];
define('DB_DSN','mysql:host='.$host.';dbname='.$cltdb);
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
$grid->SelectCommand = 'SELECT todo_id,complete_by,task from '.$cltdb.'.'.$table;



// set the ouput format to json
$grid->dataType = 'json';
// Let the grid create the model
$grid->setColModel();
// Set the url from where we obtain the data
$grid->setUrl('../includes/gettasks.php');
// Set grid caption using the option caption
$grid->setGridOptions(array(
    "caption"=>"Oustanding Task List for ".$sname,
    "rowNum"=>10,
    "sortname"=>"complete_by",
	"sortorder"=>"desc",
    "rowList"=>array(10,30,50),
	"height"=>200,
	"width"=>500
    ));


// Change some property of the field(s)
$grid->setColProperty("todo_id", array("label"=>"ID", "width"=>20, "hidden"=>true));
$grid->setColProperty("complete_by", array("label"=>"Complete by", "width"=>70, "formatter"=>"date", "formatoptions"=>array("srcformat"=> "Y-m-d", "newformat"=>"d/m/Y")));
$grid->setColProperty("task", array("label"=>"Task", "width"=>250));
$grid->setColProperty("act", array("label"=>"Actions", "width"=>90));



// Enable navigator
$grid->navigator = true;
// Disable some actions
$grid->setNavOptions('navigator', array("excel"=>false,"add"=>false,"edit"=>false,"del"=>false,"view"=>false));

// Run the script
$grid->renderGrid('#todolist','#todopager',true, null, null, true,true);

?>




