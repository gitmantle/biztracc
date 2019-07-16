<?php
/**
 * jqScheduler
 *
 * Database backend
 *
 * @version 1.0
 * @author Tony Tomov
 * @copyright (c) Tony Tomov
 */
require_once('backend.php');

final class Database extends Backend 
{
	private $db;
	private $table;
	private $user_id;
	protected $dbmap = array(
		"event_id"=>"id", // this is a id key
		"title"=>"title",
		"start_event"=>"start",
		"end_event"=>"end",
		"description"=>"description",
		"location"=>"location",
		"categories"=>"className",
		"access"=>"access",
		"all_day"=>"allDay",
		"user_id"=>"user_id"
	);
	private $encoding = "utf-8";
	private $dbtype;
	// 
	private $searchwhere = "";
	private $searchdata = array();
	
	private $wherecond = "";
	private $whereparams = array();
	private $qout = "";


	private $errorMsg = "";
	
	public function __construct($db) {
		$this->db = $db;
	}
  
	public function getError() {
		return $this->errorMsg;
	}
	
	public function setUser( $user) {
		if($user) {
			$this->user_id=$user;
		}
	}
	
	public function setTable( $table) {
		if($table) {
			$this->table=$table;
		}
	}
	
	public function setDbType( $dtype ) {
		$this->dbtype =$dtype;		
	}
	
	public function setSearchs( $where, array $whrval)
	{
		$this->searchwhere = $where;
		$this->searchdata = $whrval;
	}
	
	public function setWhere( $where, $param = array())
	{
		$this->wherecond = $where;
		$this->whereparams = $param;
	}
	
	public function setQuote( $q ) {
		$this->qout =$q;
	}
	
	public function getDBName ($jsname)
	{
		if(strlen(trim($jsname))>0) {
			$key = array_search($jsname, $this->dbmap);
			if($key !== false) {
				return $key;
			}
		}
		return $jsname;
	}
	
	public function remapPostData ($adata = array())
	{
		$newdata = array();
		foreach( $adata as $key => $val) 
		{
			$newkey = array_search($key, $this->dbmap);
			if( $newkey !== false) {
				$newdata[$newkey] = $val;
			} else {
				$newdata[$key] = $val;
			}
		}
		return $newdata;
	}
	
	public function getDBMap()
	{
		return $this->dbmap;
	}
	
	public function newEvent( $data = array() )
	{
			//$start, $end, $title, $description, $location, $categories, $access, $allDay) {
		$ret = true;
		$this->errorMsg = "";
		if (!empty($this->user_id) && !empty($this->table) ) {
			if(!isset($data['user_id'])) { return false; }
			if(is_array($this->user_id)) {
				if(!in_array($data['user_id'], $this->user_id)) { return false;}
			} else {
				if($data['user_id'] != $this->user_id) { return false; }
			}
			$tableFields = array_keys($this->dbmap);
			$binds = array();
			$idkey = $this->getDBName('id');
			if($idkey === false ) {
				$idkey = 'event_id'; //allways try
			}
			unset($tableFields[$idkey]);
			$newdata = $this->remapPostData($data);
			$rowFields = array_intersect($tableFields, array_keys($newdata));
			foreach($rowFields as $key => $val)
			{
				$insertFields[] = "?";
            //$field;
				$value = $newdata[$val];
				if( strtolower($this->encoding) != 'utf-8' ) {
					$value = iconv("utf-8", $this->encoding."//TRANSLIT", $value);
				}
				//if(in_array($val, array('user_id', 'start_event', 'end_event','all_day'))) {
					//$value = (int)$value;
				//}
				$binds[] = $value;
			}
			$sql = "";
			if(count($insertFields) > 0) {
				$sql = "INSERT INTO ".$this->qout.$this->table.$this->qout.
				" (".$this->qout . implode($this->qout.', '.$this->qout, $rowFields) .$this->qout.")" .
				" VALUES( " . implode(', ', $insertFields) . ")";

			}
			if(!$sql) return false;
			$lastid = false;
			try {
				jqGridDB::beginTransaction($this->db);
				$query = jqGridDB::prepare($this->db, $sql, $binds);
				$ado = jqGridDB::execute($query, $binds, $this->db);
				$lastid = jqGridDB::lastInsertId($this->db, $this->table, $idkey, $this->dbtype);
				jqGridDB::commit($this->db);
				jqGridDB::closeCursor($this->dbtype == "adodb" ? $ado : $query);
				$ret = $lastid;
			} catch (Exception $e) {
				jqGridDB::rollBack($this->db);
				$this->errorMsg = $e->getMessage();
				$ret = false;
			}			
		} else {
			$this->errorMsg = "Calender user or table are not set";
			$ret = false;
		}
		return $ret;
	}

	public function editEvent( $data=array() )
			//$id, $start, $end, $title, $description, $location, $categories, $access, $allDay)
	{
		$ret = true;
		$this->errorMsg = "";
		if (!empty($this->user_id) && !empty($this->table)) {
			if(!isset($data['user_id'])) { return false; }
			if(is_array($this->user_id)) {
				if(!in_array($data['user_id'], $this->user_id)) { return false;}
			} else {
				if($data['user_id'] != $this->user_id) { return false; }
			}

			$tableFields = array_keys($this->dbmap);
			$newdata = $this->remapPostData($data);
			$rowFields = array_intersect($tableFields, array_keys($newdata));
			$binds = array();
			$updateFields = array();
			$pk = $this->getDBName('id');
			if($pk === false ) {
				$pk = 'event_id'; //allways try
			}
			//$pk = 'event_id';
			//var_dump($rowFields);
			foreach($rowFields as $key => $field) {
				$value = $newdata[$field];
				if( strtolower($this->encoding) != 'utf-8' ) {
					$value = iconv("utf-8", $this->encoding."//TRANSLIT", $value);
				}
				///if(in_array($field, array('user_id', 'start_event', 'end_event','all_day'))) {
					//$value = (int)$value;
				//}
				if($field != $pk ) {
					$updateFields[] = $this->qout.$field.$this->qout. " = ?";
					$binds[] = $value;
				} else if($field == $pk) {
		            $v2 = (int)$value;
				}
			}
			if(!isset($v2)) die("Primary value is missing");
			$binds[] = $v2;
			$sql = "";
			if(count($updateFields) > 0) {
				$sql = "UPDATE ".$this->qout.$this->table.$this->qout.
					" SET ". implode(', ', $updateFields).
					" WHERE ".$this->qout.$pk.$this->qout." = ?";
				// Prepare update query
			}
			if(!$sql ) { return false; }
			try {
				$query = jqGridDB::prepare($this->db, $sql , $binds);
				$ado = jqGridDB::execute($query,  $binds, $this->db);
				jqGridDB::closeCursor($this->dbtype == "adodb" ? $ado : $query);
			} catch (Exception $e) {
				$this->errorMsg = $e->getMessage();
				$ret = false;
			}
		} else {
			$this->errorMsg = "Calender user or table are not set";
			$ret = false;
		}
		return $ret;
	}

	public function moveEvent($id, $start, $end, $allDay) {

		$ret = true;
		$this->errorMsg = "";		
		if (!empty($this->user_id) && !empty($this->table)) {
			$q = $this->qout;
            $param = array(
				(int)$start,
				(int)$end,
				(int)$allDay,
				(int)$id
			);
			// db fields
			$db_id = $this->getDBName('id');
			$db_start = $this->getDBName('start');
			$db_end = $this->getDBName('end');
			$db_allDay = $this->getDBName('allDay');
			try {
				$query = jqGridDB::prepare(
					$this->db,
					"UPDATE ".$q.$this->table.$q." SET ".$q.$db_start.$q."=?, ".$q.$db_end.$q."=?, ".$q.$db_allDay.$q."=? WHERE ".$q.$db_id.$q."=?",
					$param
				);
				$ado = jqGridDB::execute($query, $param, $this->db );
				jqGridDB::closeCursor($this->dbtype == "adodb" ? $ado : $query);
			} catch (Exception $e) {
				$this->errorMsg = $e->getMessage();
				$ret = false;				
			}
		} else {
			$this->errorMsg = "Calender user or table are not set";
			$ret = false;
		}
		return $ret;
	}
  
	public function resizeEvent($id, $start, $end) {
		$ret = true;
		$this->errorMsg = "";
		if (!empty($this->user_id) && !empty($this->table)) {
			$q = $this->qout;
            $param = array((int)$start,
				(int)$end,
				(int)$id
			);
			$db_id = $this->getDBName('id');
			$db_start = $this->getDBName('start');
			$db_end = $this->getDBName('end');
			try {
				$query = jqGridDB::prepare($this->db,
					"UPDATE ".$q.$this->table.$q." SET ".$q.$db_start.$q."=?, ".$q.$db_end.$q."=? WHERE ".$q.$db_id.$q."=?",
				$param );
				$ado = jqGridDB::execute($query, $param, $this->db);
				jqGridDB::closeCursor($this->dbtype == "adodb" ? $ado : $query);
			} catch(Exception $e) {
				$this->errorMsg = $e->getMessage();
				$ret = false;				
			}
		} else {
			$this->errorMsg = "Calender user or table are not set";
			$ret = false;
		}
		return $ret;
	}

	public function removeEvent($id) {
		$ret = true;
		$this->errorMsg = "";
		if (!empty($this->user_id) && !empty($this->table)) {
			$q = $this->qout;
            $param = array((int)$id);
			$db_id = $this->getDBName('id');
			try {
				$query = jqGridDB::prepare(
					$this->db,
					"DELETE FROM ".$q.$this->table.$q." WHERE ".$q.$db_id.$q."=?",
					$param
				);
				$ado = jqGridDB::execute($query, $param, $this->db);
				jqGridDB::closeCursor($this->dbtype == "adodb" ? $ado : $query);
			}
			catch (Exception $e) {
				$this->errorMsg = $e->getMessage();
				$ret = false;
			}	
		} else {
			$this->errorMsg = "Calender user or table are not set";
			$ret = false;
		}
		return $ret;
	}

	public function getEvents($start, $end) {
		$ret = true;
		$this->errorMsg = "";		
		
		if (!empty($this->user_id) && !empty($this->table) ) {
			$db_start = $this->getDBName('start');
			$db_user = $this->getDBName('user_id');
			$db_allDay = $this->getDBName('allDay');

			$q = $this->qout;
			$b = $q;// ? "'" : "";
			$sql = "SELECT ";
			$i =0;
			foreach($this->dbmap as $k=>$v) {
				$k = $q.$k.$q;
				$v = $b.$v.$b;
				$sql .= $i==0 ?  $k ." AS ".$v :  ", ".$k ." AS ".$v;
				$i++;
			}
			$sql .= ' FROM '.$q.$this->table.$q.' WHERE ';
			if($this->wherecond && strlen($this->wherecond) > 0)
			{
				$pos = stripos($this->wherecond, 'where');
				if($pos !== false) {
					$this->wherecond = substr_replace($this->wherecond,"", $pos, 5);
				}
				$sql .= $this->wherecond.' AND ';
			}
			$sqluser = "(";
			if(is_array($this->user_id)) {
				foreach($this->user_id as $k =>$v) {
					if($k != 0) {
						$sqluser .= " OR ".$db_user." = ? ";
					} else {
						$sqluser .= " ".$db_user." = ? ";
					}
				}
			} else {
				$sqluser .= " ".$db_user." = ? ";
			}
			$sqluser .= ")";
			if(strlen($this->searchwhere) > 0 ) {
				
				$sql .= $this->searchwhere.' AND '.$sqluser.' ORDER BY '.$db_start.' DESC';
				$params = $this->whereparams;
				foreach($this->searchdata as $k => $v)
				{
					$params[] = $v;
				}
				//$params[]
				if(is_array($this->user_id) ) {
					foreach($this->user_id as $k =>$v) {
						$params[] = (int)$v;
					}
				} else {
					$params[] = (int)$this->user_id;
				}
			} else {
				$sql .= $sqluser.' AND '.$db_start.' >= ? AND '.$db_start.' <= ? ORDER BY '.$db_start;
				$params = $this->whereparams;
				//$params[]
				if(is_array($this->user_id) ) {
					foreach($this->user_id as $k =>$v) {
						$params[] = (int)$v;
					}
				} else {
					$params[] = (int)$this->user_id;
				}
				$params[] = (int)$start;
				$params[] = (int)$end;
			}
			//var_dump($sql);
			$ret = array();
			try {
				$query = jqGridDB::prepare($this->db, $sql, $params );
				if($this->dbtype == "adodb") {
					$query = jqGridDB::execute($query, $params, $this->db);
				} else {
					jqGridDB::execute($query, $params, $this->db);
				}
				while($row = jqGridDB::fetch_assoc($query, $this->db))
				{
					//$row['end'] = $row[$this->dbmap['end_event']];
					//$row['start'] = $row[$this->dbmap['start_event']];
					$row['allDay'] = $row['allDay'] == 1 ? true : false;
					//unset($row[$this->dbmap['start_event']]);
					//($row[$this->dbmap['end_event']]);
					$ret[] = $row;
				}
				jqGridDB::closeCursor($query);
			} catch (Exception $e) {
				$this->errorMsg = $e->getMessage();
				$ret =  false;
			}
		} else {
			$this->errorMsg = "Calender user or table are not set";
			$ret = false;
		}
		return $ret;
	}
}
