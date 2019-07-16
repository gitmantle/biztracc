<?php
session_start();
//include_once("dbconfig.php");
include_once("functions.php");
$crndb = $_SESSION['s_crndb'];


function addCalendar($st, $et, $sub, $ade, $crndb){
 
  $ret = array();
  $stime = php2MySqlTime(js2PhpTime($st));
  $etime = php2MySqlTime(js2PhpTime($et));

  include_once("DBClass.php");
  $db = new DBClass();	
  
  $db->query("insert into ".$crndb.".jobs (subject, starttime, endtime, isalldayevent) values (:subject, :starttime, :endtime, :isalldayevent)");
  $db->bind(':subject',$sub);
  $db->bind(':starttime', $stime);
  $db->bind(':endtime', $etime);
  $db->bind(':isalldayevent', $ade);
  
  $db->execute();
  
  
/*  
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "insert into `jqcalendar` (`subject`, `starttime`, `endtime`, `isalldayevent`) values ('"
      .mysql_real_escape_string($sub)."', '"
      .php2MySqlTime(js2PhpTime($st))."', '"
      .php2MySqlTime(js2PhpTime($et))."', '"
      .mysql_real_escape_string($ade)."' )";
    //echo($sql);
		if(mysql_query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = mysql_error();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'add success';
      $ret['Data'] = mysql_insert_id();
    }
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
*/
	$db->closeDB();

}

function addDetailedCalendar($st, $et, $sub, $ade, $dscr, $loc, $color, $tz, $crndb){
	
  $ret = array();
  $stime = php2MySqlTime(js2PhpTime($st));
  $etime = php2MySqlTime(js2PhpTime($et));

  include_once("DBClass.php");
  $db = new DBClass();	 
  
  $db->query("insert into ".$crndb.".jobs (subject, starttime, endtime, isalldayevent, description, location, color) values (:subject, :starttime, :endtime, :isalldayevent, :description, :location, :color)");
  $db->bind(':starttime', $stime);
  $db->bind(':endtime', $etime);
  $db->bind(':subject', $sub);
  $db->bind(':isalldayevent', $ade);
  $db->bind(':description', $dscr);
  $db->bind(':location', $loc);
  $db->bind(':color', $color);
  $db->execute();
  
  
/*
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "insert into `jqcalendar` (`subject`, `starttime`, `endtime`, `isalldayevent`, `description`, `location`, `color`) values ('"
      .mysql_real_escape_string($sub)."', '"
      .php2MySqlTime(js2PhpTime($st))."', '"
      .php2MySqlTime(js2PhpTime($et))."', '"
      .mysql_real_escape_string($ade)."', '"
      .mysql_real_escape_string($dscr)."', '"
      .mysql_real_escape_string($loc)."', '"
      .mysql_real_escape_string($color)."' )";
    //echo($sql);
		if(mysql_query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = mysql_error();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'add success';
      $ret['Data'] = mysql_insert_id();
    }
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
  
*/

  $db->closeDB();
  
}


function listCalendarByRange($sd, $ed, $crndb){
  $ret = array();
  $ret['events'] = array();
  $ret["issort"] =true;
  $ret["start"] = php2JsTime($sd);
  $ret["end"] = php2JsTime($ed);
  $ret['error'] = null;
  
  include_once("DBClass.php");
  $db = new DBClass();	  
  
  //$db->query("select * from ".$crndb.".jobs where `starttime` between '".php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."' ORDER BY starttime ASC");
  $db->query("select * from ".$crndb.".jobs where state = 'Open' ORDER BY starttime ASC");
  $rows = $db->resultset();
  foreach($rows as $row) {
	  extract($row);
	  $ret['events'][] = array(
		$uid,
		$subject,
		php2JsTime(mySql2PhpTime($starttime)),
		php2JsTime(mySql2PhpTime($endtime)),
		$isalldayevent,
		($isalldayevent!=1 && date("Y-m-d",mySql2PhpTime($endtime))>date("Y-m-d",mySql2PhpTime($starttime))?1:0), //more than one day event
		//$row->InstanceType,
		0,//Recurring event,
		$color,
		1,//editable
		$location, 
		''//$attends
	  );
  }
  return $ret;
  
  $db->closeDB();
  
 // print_r($ret);
  
  
 /* 
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "select * from `jqcalendar` where `starttime` between '"
      .php2MySqlTime($sd)."' and '". php2MySqlTime($ed)."' ORDER BY starttime ASC";
    $handle = mysql_query($sql);
    //echo $sql;
    while ($row = mysql_fetch_object($handle)) {
      //$ret['events'][] = $row;
      //$attends = $row->AttendeeNames;
      //if($row->OtherAttendee){
      //  $attends .= $row->OtherAttendee;
      //}
      //echo $row->StartTime;
      $ret['events'][] = array(
        $row->Id,
        $row->Subject,
        php2JsTime(mySql2PhpTime($row->StartTime)),
        php2JsTime(mySql2PhpTime($row->EndTime)),
        $row->isalldayevent,
        ($row->isalldayevent!=1 && date("Y-m-d",mySql2PhpTime($row->EndTime))>date("Y-m-d",mySql2PhpTime($row->StartTime))?1:0), //more than one day event
        //$row->InstanceType,
        0,//Recurring event,
        $row->Color,
        1,//editable
        $row->Location, 
        ''//$attends
      );
    }
	}catch(Exception $e){
     $ret['error'] = $e->getMessage();
  }
  return $ret;
*/


}

function listCalendar($day, $type, $crndb){
  $phpTime = js2PhpTime($day);
  //echo $phpTime . "+" . $type;
  switch($type){
    case "month":
      $st = mktime(0, 0, 0, date("m", $phpTime), 1, date("Y", $phpTime));
      $et = mktime(0, 0, -1, date("m", $phpTime)+1, 1, date("Y", $phpTime));
      break;
    case "week":
      //suppose first day of a week is monday 
      $monday  =  date("d", $phpTime) - date('N', $phpTime) + 1;
      //echo date('N', $phpTime);
      $st = mktime(0,0,0,date("m", $phpTime), $monday, date("Y", $phpTime));
      $et = mktime(0,0,-1,date("m", $phpTime), $monday+7, date("Y", $phpTime));
      break;
    case "day":
      $st = mktime(0, 0, 0, date("m", $phpTime), date("d", $phpTime), date("Y", $phpTime));
      $et = mktime(0, 0, -1, date("m", $phpTime), date("d", $phpTime)+1, date("Y", $phpTime));
      break;
  }
  //echo $st . "--" . $et;
  return listCalendarByRange($st, $et, $crndb);
}

function updateCalendar($id, $st, $et, $crndb){

  $stime = php2MySqlTime(js2PhpTime($st));
  $etime = php2MySqlTime(js2PhpTime($et));

  include_once("DBClass.php");
  $db = new DBClass();	
  
  $db->query( "update ".$crndb.".jobs set starttime = :starttime, endtime = :endtime where id = :id");
  $db->bind(':starttime', $stime);
  $db->bind(':endtime', $etime);
  $db->bind(':id', $id);
  $db->execute();
	
/*	
  $ret = array();
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "update `jqcalendar` set"
      . " `starttime`='" . php2MySqlTime(js2PhpTime($st)) . "', "
      . " `endtime`='" . php2MySqlTime(js2PhpTime($et)) . "' "
      . "where `id`=" . $id;
    //echo $sql;
		if(mysql_query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = mysql_error();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'Succefully';
    }
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
 */
  $db->closeDB();
}

function updateDetailedCalendar($id, $st, $et, $sub, $ade, $dscr, $loc, $color, $tz, $crndb){
  
  include_once("DBClass.php");
  $db = new DBClass();	

  $stime = php2MySqlTime(js2PhpTime($st));
  $etime = php2MySqlTime(js2PhpTime($et));
  
  $db->query("update ".$crndb.".jobs set starttime = :starttime, endtime = :endtime, subject = :subject, isalldayevent = :isalldayevent, description = :description, location = :location, color = :color where uid = :id");
  $db->bind(':starttime', $stime);
  $db->bind(':endtime', $etime);
  $db->bind(':subject', $sub);
  $db->bind(':isalldayevent', $ade);
  $db->bind(':description', $dscr);
  $db->bind(':location', $loc);
  $db->bind(':color', $color);
  $db->bind(':id', $id);

  $db->execute();
  
  
  
  
/*  
  $ret = array();
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "update `jqcalendar` set"
      . " `starttime`='" . php2MySqlTime(js2PhpTime($st)) . "', "
      . " `endtime`='" . php2MySqlTime(js2PhpTime($et)) . "', "
      . " `subject`='" . mysql_real_escape_string($sub) . "', "
      . " `isalldayevent`='" . mysql_real_escape_string($ade) . "', "
      . " `description`='" . mysql_real_escape_string($dscr) . "', "
      . " `location`='" . mysql_real_escape_string($loc) . "', "
      . " `color`='" . mysql_real_escape_string($color) . "' "
      . "where `id`=" . $id;
    //echo $sql;
		if(mysql_query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = mysql_error();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'Succefully';
    }
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
*/  
  
  $db->closeDB(); 
  
}

function removeCalendar($id, $crndb){
	
  include_once("DBClass.php");
  $db = new DBClass();	
  
  $db->query("delete from ".$crndb.".jobs where `id`=" . $id);
  $db->execute();
	
/*
  $ret = array();
  try{
    $db = new DBConnection();
    $db->getConnection();
    $sql = "delete from `jqcalendar` where `id`=" . $id;
		if(mysql_query($sql)==false){
      $ret['IsSuccess'] = false;
      $ret['Msg'] = mysql_error();
    }else{
      $ret['IsSuccess'] = true;
      $ret['Msg'] = 'Succefully';
    }
	}catch(Exception $e){
     $ret['IsSuccess'] = false;
     $ret['Msg'] = $e->getMessage();
  }
  return $ret;
*/

$db->closeDB();
}



header('Content-type:text/javascript;charset=UTF-8');
$method = $_GET["method"];
switch ($method) {
    case "add":
        $ret = addCalendar($_POST["CalendarStartTime"], $_POST["CalendarEndTime"], $_POST["CalendarTitle"], $_POST["IsAllDayEvent"], $crndb);
        break;
    case "list":
        $ret = listCalendar($_POST["showdate"], $_POST["viewtype"], $crndb);
        break;
    case "update":
        $ret = updateCalendar($_POST["calendarId"], $_POST["CalendarStartTime"], $_POST["CalendarEndTime"], $crndb);
        break; 
    case "remove":
        $ret = removeCalendar( $_POST["calendarId"], $crndb);
        break;
    case "adddetails":
        $st = $_POST["stpartdate"] . " " . $_POST["stparttime"];
        $et = $_POST["etpartdate"] . " " . $_POST["etparttime"];
        if(isset($_GET["id"])){
            $ret = updateDetailedCalendar($_GET["id"], $st, $et, 
                $_POST["Subject"], isset($_POST["isalldayevent"])?1:0, $_POST["Description"], 
                $_POST["Location"], $_POST["colorvalue"], $_POST["timezone"], $crndb);
        }else{
            $ret = addDetailedCalendar($st, $et,                    
                $_POST["Subject"], isset($_POST["isalldayevent"])?1:0, $_POST["Description"], 
                $_POST["Location"], $_POST["colorvalue"], $_POST["timezone"], $crndb);
        }        
        break; 


}
echo json_encode($ret); 



?>