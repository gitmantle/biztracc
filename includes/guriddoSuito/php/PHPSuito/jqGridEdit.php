<?php
/**
 * @author  Tony Tomov, (tony@trirand.com)
 * @copyright TriRand Ltd
 * @package jqGridEdit
 * @version 5.3.1
 * @abstract
 * This class extend the main jqGrid class and is used for CRUD operations.
 * Can work on table. Also the table should have one primary key.
 *
 * Usage:
 *
 * 1.Suppose the table has a primary key and this key is serial (autoincrement)
 *
 * $mygrid new jqGridEdit($db);
 * $mygrid->setTable('invoices');
 * $mygrid->editGrid();
 *
 * In this case the parameter names - i.e names with : should correspond to the names
 * in colModel in jqGrid definition.
 */

/** PHPSuito root directory */
if (!defined('PHPSUITO_ROOT')) {
    define('PHPSUITO_ROOT', dirname(__FILE__) . '/');
    require(PHPSUITO_ROOT . 'Autoloader.php');
}


class jqGridEdit  extends jqGridExport
{
	/**
	* Field names and data types from the table
	* @var array
	*/
	protected $fields = array();
	/**
	 * Stores the message which will be send to client grid in case of succesfull
	 * operation
	 * @var string
	 */
	protected $successmsg = "";
	/**
	 * Set the message which will be send to client side grid when succefull CRUD
	 * operation is performed. Usually afterSubmit event should be used in this case.
	 * @param string $msg
	 */
	public function setSuccessMsg($msg)
	{
		if($msg) {
			$this->successmsg = $msg;
		}
	}
	/**
	* Defines if the primary key is serial (autoincrement)
	* @var boolean
	*/
	public $serialKey = true;
	/**
	 * Allow a obtaining and sending the last id (in case of serial key) to
	 * the client.
	 * @var bolean
	 */
	public $getLastInsert = false;
	/**
	 * Stores the last inserted id in case when getLastInsert is true
	 * @var mixed.
	 */
	protected $lastId =null;	
	/**
	*
	* Tell the class if the fields should be get from the query.
	* If set to false the $fields array should be set manually in type
	* $fields = array("dbfield1" => array("type"=>"integer")...);
	* @var boolean
	*/	
	//protected $buildfields = false;
	/**
	* If true every CRUD is enclosed within begin transaction - commit/rollback
	* @var boolean
	*/
	public $trans = true;
	/**
	 * Enables/disables adding of record into the table
	 * @var boolean
	 */
	public $add = true;
	/**
	 * Enables/disables updating of record into the table
	 * @var boolean
	 */
	public $edit = true;
	/**
	 * Enables/disables deleting of record into the table
	 * @var boolean
	 */
	public $del = true;
	/**
	 * Determines the type of accepting the input data. Can be POST or GET
	 * @var string
	 */
	public $mtype = "POST";
	/**
	 * Decodes the input for update and insert opeartions using the html_entity_decode
	 * @var booolen
	 */
	public $decodeinput = false;
	/**
	 * @var boolean default false
	 * 
	 * Determines if we should perform server side validation.
	 */
	public $serverValidate = false;
	/**
	 * 
	 * @var boolean default false
	 * 
	 * Determines if we should perform server side sanitatation.
	 */
	public $serverSanitize = false;
	/**
	 * 
	 * @var type array 
	 * 
	 * Set validations rules used to perform server validaion
	 */
	protected $validations = array();
	public function setValidationRules( $arule ) {
		if(is_array($arule)) {
			$this->validations  = $arule;
		}
	}
	protected $sanatations = array();
	public function setSanitatationRules( $arule ) {
		if(is_array($arule)) {
			$this->sanatations  = $arule;
		}
	}
	/**
	* Return the primary key of the table
	* @return string
	*/
	public function getPrimaryKeyId()
	{
		return $this->primaryKey;
	}
	/**
	* Set a primary key for the table
	* @param string $keyid
	*/
	public function setPrimaryKeyId($keyid)
	{
		$this->primaryKey = $keyid;
	}
	/**
	 * Set table for CRUD and build the fields
	 * @param string $_newtable
	 *
	 */
	public function setTable($_newtable)
	{
		$this->table= $_newtable;
	}
	/**
	 * Build the fields array with a database fields from the table.
	 * Also we get the fields types
	 * Return false if the fields can not be build.
	 * @return boolen
	 */
	protected function _buildFields()
	{
		$result = false;
		if(strlen(trim($this->table))>0 ) {
			//if ($this->buildfields) return true;
			$wh = ($this->dbtype == 'sqlite') ? "": " WHERE 1=2";
			$sql = "SELECT * FROM ".$this->table.$wh;
			if($this->debug) {
				$this->logQuery($sql);
				$this->debugout();				
			}
			try {
				$select =  jqGridDB::query($this->pdo,$sql);
				if($select) {
					$colcount = jqGridDB::columnCount($select);
					//$rev = array();
					$this->fields = array();
					for($i=0;$i<$colcount;$i++)
					{
						$meta = jqGridDB::getColumnMeta($i, $select);
						$type = jqGridDB::MetaType($meta, $this->dbtype);
						$this->fields[$meta['name']] = array('type'=>$type);
					}
					jqGridDB::closeCursor($select);
					//$this->buildfields = true;
					$result = true;
				} else {
					$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
					throw new Exception($this->errorMessage);
				}
			} catch (Exception $e) {
				$result = false;
				if(!$this->errorMessage) { $this->errorMessage = $e->getMessage(); }
			}
		} else {
			$this->errorMessage = "No database table is set to operate!";
		}
		if($this->showError && !$result) {
			$this->sendErrorHeader();
		}
		return $result;
	}
	/**
	 * Stores all the dataa need to perform SQL command after add is succesfull
	 * @var array
	 */
	protected $_addarray = array();
	protected $_addarrayb = array();
	/**
	 * Stores all the dataa need to perform SQL command after edit is succesfull
	 * @var array
	 */
	protected $_editarray = array();
	protected $_editarrayb = array();
	/**
	 * Stores all the dataa need to perform SQL command after delete is succesfull
	 * @var array
	 */
	protected $_delarray = array();
	protected $_delarrayb = array();
	/**
	 * Executes the sql command after the crud is succefull
	 * @param string $oper can be add,edit,del
	 * @return true if ooperation is succefull.
	 */
	protected function _actionsCRUDGrid($oper, $event)
	{
		$ret = true;
		switch($oper) {
		case 'add':
			if($event == 'before') {
				$ar = $this->_addarrayb;
			} else {
				$ar = $this->_addarray;
			}
			$acnt = count($ar);
			if($acnt > 0)
			{
				for($i=0;$i<$acnt; $i++)
				{
					if($this->debug) { $this->logQuery($ar[$i]['sql'], $ar[$i]['params']); }
					$stmt = jqGridDB::prepare($this->pdo, $ar[$i]['sql'], $ar[$i]['params']);
					$result = jqGridDB::execute($stmt, $ar[$i]['params'], $this->pdo); //DB2
					if(!$result) {
						$ret = false;
						break;
					}
					jqGridDB::closeCursor($this->dbtype == "adodb" ? $result : $stmt);
				}
			}
			break;
		case 'edit':
			if($event == 'before') {
				$ar = $this->_editarrayb;
			} else {
				$ar = $this->_editarray;
			}
			$acnt = count($ar);
			if($acnt > 0)
			{
				for($i=0;$i<$acnt; $i++)
				{
					if($this->debug) { $this->logQuery($ar[$i]['sql'], $ar[$i]['params']); }
					$stmt = jqGridDB::prepare($this->pdo,$ar[$i]['sql'], $ar[$i]['params']);
					$result = jqGridDB::execute( $stmt, $ar[$i]['params'], $this->pdo ); //DB2
					if(!$result) {
						$ret = false;
						break;
					}
					jqGridDB::closeCursor($this->dbtype == "adodb" ? $result : $stmt);
				}
			}
			break;
		case 'del':
			if($event == 'before') {
				$ar = $this->_delarrayb;
			} else {
				$ar = $this->_delarray;
			}
			$acnt = count($ar);
			if($acnt > 0)
			{
				for($i=0;$i<$acnt; $i++)
				{
					if($this->debug) { $this->logQuery($ar[$i]['sql'],$ar[$i]['params']); }
					$stmt = jqGridDB::prepare($this->pdo,$ar[$i]['sql'],$ar[$i]['params']);
					$result = $stmt ? jqGridDB::execute( $stmt, $ar[$i]['params'], $this->pdo ) : false;
					if(!$result) {
						$ret = false;
						break;
					}
					jqGridDB::closeCursor($this->dbtype == "adodb" ? $result : $stmt);
				}
			}
			break;
		}
		return $ret;
	}

	/**
	 * Run a sql command(s) before the operation from the CRUD.
	 * Can run a unlimited
	 *
	 * @param string $oper - the operation after which the command should be run
	 * @param string $sql - the sql command
	 * @param array $params  - parameters passed to the sql query.
	 */
	public function setBeforeCrudAction($oper, $sql, $params = null)
	{
		switch ($oper)
		{
			case 'add':
				$this->_addarrayb[] = array("sql"=>$sql,"params"=>$params);
				break;
			case 'edit':
				$this->_editarrayb[] = array("sql"=>$sql,"params"=>$params);
				break;
			case 'del':
				$this->_delarrayb[] = array("sql"=>$sql,"params"=>$params);
				break;
		}
	}

	/**
	 * Run a sql command(s) after the operation from the CRUD is succefull.
	 * Can run a unlimited
	 *
	 * @param string $oper - the operation after which the command should be run
	 * @param string $sql - the sql command
	 * @param array $params  - parameters passed to the sql query.
	 */
	public function setAfterCrudAction($oper, $sql, $params = null)
	{
		switch ($oper)
		{
			case 'add':
				$this->_addarray[] = array("sql"=>$sql,"params"=>$params);
				break;
			case 'edit':
				$this->_editarray[] = array("sql"=>$sql,"params"=>$params);
				break;
			case 'del':
				$this->_delarray[] = array("sql"=>$sql,"params"=>$params);
				break;
		}
	}

	/**
	 * Return the fields generated for CRUD operations
	 * @return array
	 */
	public function getFields()
	{
		return $this->fields;
	}
	/**
	 *
	 * Insert the data array into the database according to the table element.
	 * A primaryKey should be set. If the key is not set It can be obtained
	 * from jqGridDB::getPrimaryKey
	 * Return true on succes, false otherwiese.
	 * @todo in the future we should return the last insert id from the table
	 * @param array $data associative array which key values correspond to the
	 * names in the table.
	 * @return boolean
	 */
	public function insert($data)
	{
		if(!$this->add) { return false; }
		if(!$this->_buildFields()) {
			return false;
		}
		if(!$this->checkPrimary()) {
			return false;
		}
		
		if($this->serverSanitize) {
			if(is_array($this->sanatations) && count($this->sanatations) == 0 ) {
				// search for sanitize option in validation array
				foreach($this->validations as $key => $values) {
					foreach( $values as $prop=> $val) {
						if($prop == 'sanitize' && $values[$prop]==true) {
							$this->sanatations[] = $key;
							break;
						}
					}
				}
			}
		}		
		if($this->serverValidate) {
			$validator = new jqValidator($this->validations, $this->sanatations);
			$validator->linebreak = "<br/>";
			if( !$validator->validate( $data ) ) {
				$this->errorMessage = $validator->getJSON();
				$this->sendErrorHeader();
				return false;
			}
		}
		
		if($this->serverSanitize) {
			if(!$this->serverValidate) {
				$validator = new jqValidator($this->validations, $this->sanatations);
			}
			$data = $validator->sanatize($data);
		}

		$datefmt = $this->userdateformat;
		$timefmt = $this->usertimeformat;
		if($this->serialKey) { unset($data[$this->getPrimaryKeyId()]); }
		$tableFields = array_keys($this->fields);
		$rowFields = array_intersect($tableFields, array_keys($data));
		// Get "col = :col" pairs for the update query
		$insertFields = array();
		$binds = array();
		$types = array();
		$v ='';
		foreach($rowFields as $key => $val)
		{
			$insertFields[] = "?";
			//$field;
			$t = $this->fields[$val]["type"];
			$value = $data[$val];
			if( strtolower($this->encoding) != 'utf-8' ) {
				$value = iconv("utf-8", $this->encoding."//TRANSLIT", $value);
			}
			if(strtolower($value)=='null') {
				$v = NULL;
			} else if (trim($value) == "") {
				$v = $value;
			} else {
				switch ($t) {
					case 'date':
						$v = $datefmt != $this->dbdateformat ? jqGridUtils::parseDate($datefmt,$value,$this->dbdateformat) : $value;
						break;
					case 'datetime' :
						$v = $timefmt != $this->dbtimeformat ? jqGridUtils::parseDate($timefmt,$value,$this->dbtimeformat) : $value;
						break;
					case 'time':
						$v = jqGridUtils::parseDate($timefmt,$value,'H:i:s');
						break;
					default :
						$v = $value;
				}
				if($this->decodeinput) { $v = htmlspecialchars_decode($v); }
			}
			$types[] = $t;
			$binds[] = $v;
			unset($v);
		}
		$result = false;
		if(count($insertFields) > 0) {
			// build the statement
			$sql = "INSERT INTO " . $this->table .
				" (" . implode(', ', $rowFields) . ")" .
				" VALUES( " . implode(', ', $insertFields) . ")";
			// Prepare insert query
			$stmt = $this->parseSql($sql, $binds, false);
			if($stmt) {
				// Bind values to columns
				jqGridDB::bindValues($stmt, $binds, $types);

				// Execute
				if($this->trans) {
					try {
						jqGridDB::beginTransaction($this->pdo);
						$result = $this->_actionsCRUDGrid('add', 'before');
						if($this->debug) { $this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primaryKey); }
						$ret = false;
						if( $result ) {
							$ret = jqGridDB::execute($stmt, $binds, $this->pdo);
						}
						$result = $ret ? true : false;
						if( $result ) {
							if($this->serialKey && $this->getLastInsert) {
								$this->lastId = jqGridDB::lastInsertId($this->pdo, $this->table, $this->primaryKey, $this->dbtype);
								if(!is_numeric($this->lastId) ) {
									$result = false;
								}
							}
						}
						if($result) {
							$saver = $this->showError;
							$this->showError = false;
							$result = $this->_actionsCRUDGrid('add', 'after');
							$this->showError = $saver;
						}
						if($result) {
							$result = jqGridDB::commit($this->pdo);
						}
						if(!$result) {
							$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
							throw new Exception($this->errorMessage);
						}
					} catch (Exception $e) {
						jqGridDB::rollBack($this->pdo);
						$result = false;
						if(!$this->errorMessage) { $this->errorMessage = $e->getMessage(); }
					}
					try {
						jqGridDB::closeCursor($this->dbtype== "adodb" ? $ret : $stmt);
					} catch (Exception $e) {}
				} else {
					try {
						$result = $this->_actionsCRUDGrid('add', 'before');
						if($this->debug) { $this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primaryKey); }
						$ret = false;
						if( $result ) {
							$ret = jqGridDB::execute($stmt, $binds, $this->pdo);
						}
						$result = $ret ? true : false;
						if(!$result) {
							$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
							throw new Exception($this->errorMessage);
						}
						jqGridDB::closeCursor($this->dbtype == "adodb" ? $ret : $stmt);
						if($this->serialKey && $this->getLastInsert && $result) {
							$this->lastId = jqGridDB::lastInsertId($this->pdo, $this->table, $this->primaryKey, $this->dbtype);
							if(!is_numeric($this->lastId) ) {
								$result = false;
							}
						}
						if($result) { $result = $this->_actionsCRUDGrid('add', 'after'); }
						if(!$result) {
							$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
							throw new Exception($this->errorMessage);
						}
					} catch (Exception $e) {
						$result = false;
						if(!$this->errorMessage) { $this->errorMessage = $e->getMessage(); }
					}
				}
			} else {
				$this->errorMessage = "Error when preparing a INSERT statement!";
				$result = false;
			}
		} else {
			$this->errorMessage = "Data posted does not match insert fields!";
			$result = false;
		}
		if($this->debug) { $this->debugout(); }
		if($this->showError && !$result) {
			$this->sendErrorHeader();
		}
		return $result;
	}
	/**
	 *
	 * Update the data into the database according the table element
	 * A primaryKey should be set. If the key is not set It can be obtained
	 * from jqGridDB::getPrimaryKey
	 * Return true on success, false when the operation is not succefull
	 * @todo possibility to set additional where clause
	 * @param array $data associative array which key values correspond to the
	 * names in the table
	 * @return boolean
	 */
	public function update($data)
	{
		if(!$this->edit) { return false; }
		if(!$this->_buildFields()) {
			return false;
		}
		if(!$this->checkPrimary()) {
			return false;
		}
		$datefmt = $this->userdateformat;
		$timefmt = $this->usertimeformat;

		//$custom = false;
		
		if($this->serverSanitize) {
			if(is_array($this->sanatations) && count($this->sanatations) == 0 ) {
				// search for sanitize option in validation array
				foreach($this->validations as $key => $values) {
					foreach( $values as $prop=> $val) {
						if($prop == 'sanitize' && $values[$prop]==true) {
							$this->sanatations[] = $key;
							break;
						}
					}
				}
			}
		}
		if($this->serverValidate) {
			$validator = new jqValidator($this->validations, $this->sanatations);
			$validator->linebreak = "<br/>";
			if( !$validator->validate( $data ) ) {
				$this->errorMessage = $validator->getJSON();
				$this->sendErrorHeader();
				return false;
			}
		}
		if($this->serverSanitize) {
			if(!$this->serverValidate) {
				$validator = new jqValidator($this->validations, $this->sanatations);
			}
			$data = $validator->sanatize($data);
		}
		
		$tableFields = array_keys($this->fields);
		$rowFields = array_intersect($tableFields, array_keys($data));
		// Get "col = :col" pairs for the update query
		$updateFields = array();
		$binds = array();
		$types = array();
		$pk = $this->getPrimaryKeyId();
		foreach($rowFields as $key => $field) {
			$t = $this->fields[$field]["type"];
			$value = $data[$field];
			if( strtolower($this->encoding) != 'utf-8' ) {
				$value = iconv("utf-8", $this->encoding."//TRANSLIT", $value);
			}
			if(strtolower($value) == 'null') {
				$v = NULL;
			} else if(trim($value) == "") {
				$v = $value;
			} else {
				switch ($t) {
					case 'date':
						$v = $datefmt != $this->dbdateformat ? jqGridUtils::parseDate($datefmt,$value,$this->dbdateformat) : $value;
						break;
					case 'datetime' :
						$v = $timefmt != $this->dbtimeformat ? jqGridUtils::parseDate($timefmt,$value,$this->dbtimeformat) : $value;
						break;
					case 'time':
						$v = jqGridUtils::parseDate($timefmt,$value,'H:i:s');
						break;
					default :
						$v = $value;
				}
				if($this->decodeinput) { $v = htmlspecialchars_decode($v); }
			}
			if($field != $pk ) {
				$updateFields[] = $field . " = ?";
				$binds[] = $v;
				$types[] = $t;
			} else if($field == $pk) {
				$v2 = $v;
				$t2 = $t;
			}
			unset($v);
		}
		$result = false;
		if(!isset($v2))  {
			$this->errorMessage = "Primary key/value is missing or is not correctly set!";
			if($this->showError) {
				$this->sendErrorHeader();
			}
			return $result;
		}
		$binds[] = $v2;
		$types[] = $t2;
		if(count($updateFields) > 0) {
			// build the statement
			$sql = "UPDATE " . $this->table .
				" SET " . implode(', ', $updateFields) .
				" WHERE " . $pk . " = ?";
			// Prepare update query
			$stmt = $this->parseSql($sql, $binds, false);
			if($stmt) {
				// Bind values to columns
				jqGridDB::bindValues($stmt, $binds, $types);
				if($this->trans) {
					try {
						jqGridDB::beginTransaction($this->pdo);
						$result = $this->_actionsCRUDGrid('edit', 'before');
						if($this->debug) { $this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primaryKey);}
						$ret = false;
						if($result) {
							$ret = jqGridDB::execute($stmt, $binds, $this->pdo);
						}
						$result = $ret ? true : false;
						if(!$result) {
							$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
							throw new Exception($this->errorMessage);
						}
						jqGridDB::closeCursor($this->dbtype== "adodb" ? $ret : $stmt);
						if($result) {
							$result = $this->_actionsCRUDGrid('edit', 'after');
						}
						if($result)	{
							$result = jqGridDB::commit($this->pdo);
						} else {
							$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
							throw new Exception($this->errorMessage);
						}
					} catch (Exception $e) {
						jqGridDB::rollBack($this->pdo);
						$result = false;
						if(!$this->errorMessage) { $this->errorMessage = $e->getMessage(); }
					}
				} else {
					try {
						$result = $this->_actionsCRUDGrid('edit', 'before');
						if($this->debug) { $this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primaryKey); }
						$ret = false;
						if($result) {
							$ret = jqGridDB::execute($stmt, $binds, $this->pdo);
						}
						$result = $ret ? true : false;
						if(!$result) {
							$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
							throw new Exception($this->errorMessage);
						}
						jqGridDB::closeCursor($this->dbtype== "adodb" ? $ret : $stmt);
						
						if($result) {
							$result = $this->_actionsCRUDGrid('edit', 'after');
						}
						if(!$result){
							$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
							throw new Exception($this->errorMessage);
						}
					} catch (Exception $e) {
						$result = false;
						if(!$this->errorMessage) $this->errorMessage = $e->getMessage();
					}
				}
			} else {
				$this->errorMessage = "Error when preparing a UPDATE statement!";
			}
		} else {
			$this->errorMessage = "Data posted does not match update fields!";
		}
		if($this->debug) { $this->debugout(); }
		if($this->showError && !$result) {
			$this->sendErrorHeader();
		}
		return $result;
	}
	/**
	 *
	 * Return the last inserted id from the insert method in cas getLastInsert is 
	 * set to true
	 * @return mixed
	 */
	
	public function getLastInsertId ()
	{
		return $this->lastId;
	}
		
	/**
	 *
	 * Delete the data into the database according the table element
	 * A primaryKey should be set. If the key is not set It can be obtained
	 * from jqGridDB::getPrimaryKey
	 * Return true on success, false when the operation is not succefull
	 * @todo possibility to set additional where clause
	 * @param array $data associative array which key values correspond to the
	 * names in the delete command
	 * @return boolean
	 */
	public function delete(array $data, $where='', array $params=null )
	{
		$result = false;
		if(!$this->del) { return $result; }
		//SQL Server hack
		if(!$this->checkPrimary()) {
			return $result;
		}		
		$ide = null;
		$binds = array(&$ide);
		$types = array();
		$odbc = strpos($this->dbtype, 'odbc');
	
		if(count($data)>0) {
			if($where && strlen($where)>0) {
				$id = "";
				$sql = "DELETE FROM ".$this->table." WHERE ".$where;
				$stmt = $this->parseSql($sql, $params);
				$delids = "";
				$custom = true;
			} else {
				$id = $this->getPrimaryKeyId();
				if(!isset($data[$id])) {
					$this->errorMessage = "Missed data id value to perform delete!";
					if($this->showError) {
						$this->sendErrorHeader();
					}
					return $result;
				}
				$sql = "DELETE FROM ".$this->table." WHERE ".$id. "=?";
				$stmt = $odbc === false ? $this->parseSql($sql, $binds, false) : true;
				$delids = explode(",",$data[$id]);
				$custom = false;
			}
			$types[0] = 'custom';
			if($stmt) {
				if($this->trans) {
					try {
						jqGridDB::beginTransaction($this->pdo);
						$result = $this->_actionsCRUDGrid('del', 'before');
						if( $custom ) {
							if($this->debug) { $this->logQuery($sql, $params, false, $data, null, $this->primaryKey); }
							$result = jqGridDB::execute( $stmt, $params, $this->pdo );
							if($result) {
								jqGridDB::closeCursor($this->dbtype== "adodb" ? $result : $stmt);
							}
						} else {
							foreach($delids as $i => $ide) {
								$delids[$i] = trim($delids[$i]);
								$binds[0] = &$delids[$i];
								if($this->debug)  { $this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primaryKey); }
								if( $odbc === false ) {
									jqGridDB::bindValues($stmt, $binds, $types);
									$result = jqGridDB::execute($stmt, $binds, $this->pdo);
								} else {
									$stmt = jqGridDB::prepare($this->pdo,$sql, $binds, false, false);
									$result = jqGridDB::execute($stmt, $binds, $this->pdo);
									if(!$result) { break;}
									jqGridDB::closeCursor($stmt);
								}
								if(!$result) {
									break;
								}
								unset($binds[0]);
							}
							if ( $odbc === false  && $result) {
								jqGridDB::closeCursor($this->dbtype== "adodb" ? $result : $stmt);
							}
						}
						$ret = $result ? true : false;
						if($ret)  { $result = $this->_actionsCRUDGrid('del', 'after'); }
						else { $result = false; }
						if($result)  {
							jqGridDB::commit($this->pdo);
						} else {
							$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
							throw new Exception($this->errorMessage);
						}
					} catch (Exception $e) {
						jqGridDB::rollBack($this->pdo);
						$result = false;
						if(!$this->errorMessage) $this->errorMessage = $e->getMessage();
					}
				} else {
					try {
						$result = $this->_actionsCRUDGrid('del', 'before');
						if($result)  {
							if($custom) {
								$result = jqGridDB::execute( $stmt, $params, $this->pdo );
								if($result) {
									jqGridDB::closeCursor($this->dbtype== "adodb" ? $result : $stmt);
								}
							} else {
								foreach($delids as $i => $ide) {
									$delids[$i] = trim($delids[$i]);
									$binds[0] = &$delids[$i];
									if($this->debug) { $this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primaryKey); }
									if( $odbc === false ) {
										jqGridDB::bindValues($stmt, $binds, $types);
										$result = jqGridDB::execute($stmt, $binds, $this->pdo);
									} else {
										$stmt = jqGridDB::prepare($this->pdo,$sql, $binds, false, false);
										$result = jqGridDB::execute($stmt, $binds, $this->pdo);
										if($result) {
											jqGridDB::closeCursor($stmt);
										}
									}
									if(!$result) {
										break;
									}
									unset($binds[0]);
								}
								if($odbc == false && $result) {
									jqGridDB::closeCursor($this->dbtype== "adodb" ? $result : $stmt);
								}
							}
						}
						$ret = $result ? true : false;
						if($ret) { $result = $this->_actionsCRUDGrid('del', 'after');}
						else { $result = false; }
						if(!$result) {
							$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
							throw new Exception($this->errorMessage);
						}
					} catch (Exception $e){
						$result = false;
						if(!$this->errorMessage) { $this->errorMessage = $e->getMessage(); }
					}
				}
			}
		}
		if($this->debug) { $this->debugout(); }
		if($this->showError && !$result) {
			$this->sendErrorHeader();
		}
		return $result;
	}

	/**
	 * Check for primary key and if not set try to obtain it
	 * Return true on success
	 *
	 * @return boolean
	 */
	protected function checkPrimary()
	{
		$result =  true;
		$errmsg = "Primary key can not be found!";
		if(strlen(trim($this->table))>0 && !$this->primaryKey) {
			$this->primaryKey = jqGridDB::getPrimaryKey($this->table, $this->pdo, $this->dbtype);
			if(!$this->primaryKey) {
				$this->errorMessage = $errmsg." ".jqGridDB::errorMessage($this->pdo);
				$result = false;
			}
		}
		if($this->showError && !$result) {
			$this->sendErrorHeader();
		}
		return $result;
	}
	/**
	 * Perform the all CRUD operations depending on the oper param send from the grid
	 * and the table element
	 * If the primaryKey is not set we try to obtain it using jqGridDB::getPrimaryKey
	 * If the primary key is not set or can not be obtained the operation is aborted.
	 * Also the method call the queryGrid to perform the grid ouput
	 * @param array $summary - set which columns should be sumarized in order to be displayed to the grid
	 * By default this parameter uses SQL SUM function: array("colmodelname"=>"sqlname");
	 * It can be set to use the other one this way
	 * array("colmodelname"=>array("sqlname"=>"AVG"));
	 * By default the first field correspond to the name of colModel the second to
	 * the database name
	 * @param array $params additional parameters that can be passed to the query
	 * @param string $oper if set the requested oper operation is performed without to check
	 * the parameter sended from the grid.
	 */
	public function editGrid(array $summary=null, array $params=null, $oper=false, $echo = true)
	{
		if(!$oper) {
			$oper = $this->oper ? $this->oper : "grid";
		}
		switch ($oper)
		{
			case $this->GridParams["editoper"] :
					$data = strtolower($this->mtype)=="post" ? jqGridUtils::Strip($_POST) : jqGridUtils::Strip($_GET);
					if( $this->update($data) )
					{
						if($this->successmsg) {
							echo $this->successmsg;
						}
					}
				break;
			case $this->GridParams["addoper"] :
					$data = strtolower($this->mtype)=="post" ? jqGridUtils::Strip($_POST) : jqGridUtils::Strip($_GET);
					if($this->insert($data) ) {
						if($this->getLastInsert) {  // inline edit here
							echo $this->getPrimaryKeyId()."#".$this->lastId;
						} else {
							if($this->successmsg) {
								echo $this->successmsg;
							}
						}
					}
				break;
			case $this->GridParams["deloper"] :
				$data = strtolower($this->mtype)=="post" ? jqGridUtils::Strip($_POST) : jqGridUtils::Strip($_GET);
				if($this->delete($data))
				{
					if($this->successmsg) {
						echo $this->successmsg;
					}
				}
				break;
			default :
				return $this->queryGrid($summary, $params, $echo);
		}
	}
}
