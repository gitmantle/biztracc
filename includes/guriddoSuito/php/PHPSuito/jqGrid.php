<?php
/**
 * @author  Tony Tomov, (tony@trirand.com)
 * @copyright TriRand Ltd
 * @version 5.3.1
 * @package jqGrid
 *
 * @abstract
 * A PHP class to work with jqGrid jQuery plugin.
 * The main purpose of this class is to provide the data from database to jqGrid,
 * simple subgrid and export to excel. Also can be used for search provided
 * from toolbarFilter and searchGrid methods
 *
 * How to use:
 *
 * Using table
 *
 * require_once 'jqGridPdo.php';
 * $dsn = "mysql:host=localhost;dbname=griddemo";
 * $db = new PDO($dsn, 'username', 'password');
 *
 * $mygrid = new jqGrid($db);
 * $mygrid->table = "invoices";
 * $mygrid->queryGrid();
 *
 * Using custom SQL
 *
 * $mygrid = new jqGrid($db);
 * $mygrid->SelectCommand ="SELECT * FROM invoices";
 * $mygrid->queryGrid();
 *
 * Using xml file where the sql commands are stored
 *
 * $mygrid = new jqGrid($db);
 * $mygrid->readFromXML = true
 * $mygrid->SelectCommand ="xmlfile.getInvoiceTable";
 * $mygrid->queryGrid();
 *
 * Using summary fields. Note that in this case in jqGrid footerrow and
 * userDataOnFooter should be set
 *
 * $mygrid = new jqGrid($db);
 * $mygrid->table = "invoices";
 * $mygrid->queryGrid(array("amount"=>"amount");
 */
/** PHPSuito root directory */
if (!defined('PHPSUITO_ROOT')) {
    define('PHPSUITO_ROOT', dirname(__FILE__) . '/');
    require(PHPSUITO_ROOT . 'Autoloader.php');
}

//require_once 'jqGridUtils.php';

class jqGrid
{
	/**
	 * Get te current version
	 * @var string
	 */
	public $version = '5.3.1';
	/**
	 *
	 * Stores the connection passed to the constructor
	 * @var resourse
	 */
	protected $pdo;
	/**
	 * internal setting to determine the odbc type 
	 * @var string 
	 */
	protected $odbc;
	/**
	 * Used to perform case insensitive search in PostgreSQL. The variable is
	 * detected automatically depending on the griver from jqGrid{driver}.php
	 * @var string
	 */
	protected $I = '';
	/**
	 * This is detected automatically from the passed connection. Used to
	 * construct the appropriate pagging for the database and in case of
	 * PostgreSQL to set case insensitive search
	 * @var string
	 */
	protected $dbtype;
	/**
	 *
	 * Holds the modified select command used into grid
	 * @var string
	 */
	protected $select="";
	protected $params=null;
	/**
	 *
	 * Date format accepted in the database. See getDbDate
	 * and setDbDate and datearray. Also this format is used to automatically
	 * convert the date for CRUD and search operations
	 * @var string
	 */
	protected $dbdateformat = 'Y-m-d';
	/**
	 *
	 * Datetime format accepted in the database. See getDbTime
	 * and setDbTime and datearray. Also this format is used to automatically
	 * convert the date for CRUD and search operations
	 * @var string
	 */
	protected $dbtimeformat = 'Y-m-d H:i:s';
	/**
	 * The date format used by the user when a search is performed and CRUD operation
	 * See setUserDate and getUserDate. Also this format is used to automatically convert the date
	 * passed from grid to database. Used in CRUD operations and search
	 * @var string
	 */
	protected $userdateformat = 'd/m/Y';
	/**
	 *
	 * The datetime format used by the user when a search is performed and CRUD operation
	 * See setUserTime and getUserTime. Also this format is used to automatically convert the datetime
	 * passed from grid to database. Used in CRUD operations and search
	 * @var string
	 */
	protected $usertimeformat = 'd/m/Y H:i:s';
	/*
	 * Array that holds the the current log
	 */
	protected static $queryLog = array();

	/**
	 * Temporary variable for internal use
	 * @var mixed
	 */
	protected $tmpvar = false;
	/**
	 * Log query
	 *
	 * @param string $sql
	 * @param array $data
	 * @param array $types
	 */
	public function logQuery($sql, $data = null, $types=null, $input= null, $fld=null, $primary='')
	{
		self::$queryLog[] = array(
			'time' => date('Y-m-d H:i:s'),
			'query' => $sql,
			'data' => $data,
			'types'=> $types,
			'fields' => $fld,
			'primary' => $primary,
			'input' => $input
			);
	}
	/**
	 * Enable disable debuging
	 * @var boolean
	 */
	public $debug = false;
	/**
	 * Determines if the log should be written to file or echoed.
	 * Ih set to created is a file jqGrid.log in the directory where the script is
	 * @var boolean
	 */
	public $logtofile = true;
	/**
	 * @var type string
	 * The name or/and path to the log file. The directory 
	 * should be writtable
	 * 
	 */
	protected $logfile = "jqGrid.log";
	/**
	 * 
	 * @param type $file string
	 * Set the name or/and path of the log file.
	 */
	public function setLogFile( $file) 
	{
		if($file && is_string($file)) {
			$this->logfile = $file;
		}
	}
	/**
	 * Prints all executed SQL queries to file or console
	 * @see $logtofile
	 */
	public function debugout()
	{
		if($this->logtofile) {
			$fh = @fopen( $this->logfile, "a+" );
			if( $fh ) {
				$the_string = "Executed ".count(self::$queryLog)." query(s) - ".date('Y-m-d H:i:s')."\n";
				$the_string .= print_r(self::$queryLog,true);
				fputs( $fh, $the_string, strlen($the_string) );
				fclose( $fh );
				return( true );
			} else {
				echo "Can not write to log!";
			}
		} else {
			echo "<pre>\n";
			print_r(self::$queryLog);
			echo "</pre>\n";
		}
	}
	/**
	 * If set to true all errors from the server are shown in the client in a
	 * dialog. Curretly work only for grid and form edit.
	 * @var boolean
	 */
	public $showError = false;
	/**
	 * Last error message from the server
	 * @var string
	 */
	public $errorMessage = '';
	/**
	 *  Function to simulate 500 Internal error so that it sends the error
	 * to the client. It is activated only if $showError is true.
	 */
	public function sendErrorHeader () {
		if($this->errorMessage) {
			header($_SERVER["SERVER_PROTOCOL"]." 500 Internal Server error.");
			if($this->customClass) {
				try {
					$this->errorMessage = call_user_func(array($this->customClass,$this->customError),$this->oper,$this->errorMessage);
				} catch (Exception $e) {
					echo "Can not call the method class - ".$e->getMessage();
				}
			} else if(function_exists($this->customError)) {
				$this->errorMessage = call_user_func($this->customError,$this->oper,$this->errorMessage);
			}
			die($this->errorMessage);
		}
	}
	/**
     * Holds the parameters that are send from the grid to the connector.
	 * Correspond to the prmNames in jqGrid Java Script lib
	 * @todo these parameters should be changed according to the jqGrid js
	 * @var array
	 */
	protected $GridParams = array(
		"page" => "page",
		"rows" => "rows",
		"sort" => "sidx",
		"order" => "sord",
		"search" => "_search",
		"nd" => "nd",
		"id" => "id",
		"filter" => "filters",
		"searchField" => "searchField",
		"searchOper" => "searchOper",
		"searchString" => "searchString",
		"oper" => "oper",
		"query" => "grid",
		"addoper" => "add",
		"editoper" => "edit",
		"deloper" => "del",
		"excel" => "excel",
		"subgrid"=>"subgrid",
		"totalrows" => "totalrows",
		"autocomplete"=>"autocmpl"
	);
	/**
	 * The output format for the grid. Can be json or xml
	 * @var string the
	 */
	public $dataType = "xml";
	/**
	 * Default enconding passed to the browser
	 * @var string
	 */
	public $encoding ="utf-8";
	/**
	 * If set to true uses the PHP json_encode if available. If this is set to
	 * false a custom encode function in jqGrid is used. Also use this to false
	 * if the encoding of your database is not utf-8
	 * @deprecated this not needed anymore also the related option is $encoding
	 * @var boolean
	 */
	public $jsonencode = true;
	/**
	 * Store the names which are dates. The name should correspond of the name
	 * in colModel. Used to perform the conversion from userdate and dbdate
	 * @var array
	 */
	public $datearray = array();
	/**
	 * Store the names for the int fields when database is MongoDB. Used to perform
	 * right serach in MongoDB. The array should contain the right names as listed
	 * into the collection
	 * @var array
	 */
	public $mongointegers = array();
	/**
	 * Array which set which fields should be selected in case of mongodb.
	 * If the array is empty all fields will be selected from the collection.
	 * @var array
	 */
	public $mongofields = array();
	/**
	 * In case if no table is set, this holds the sql command for
	 * retrieving the data from the db to the grid
	 * @var string
	 */
	public $SelectCommand = "";
	/**
	 *
	 * Set the sql command for exporting. If not set a _setSQL
	 * function is used to set a sql for export
	 * @see _setSQL
	 * @var string
	 */
	public $ExportCommand = "";
	/**
	 * Maximum number of rows to be exported for the exporting
	 * @var integer
	 */
	public $gSQLMaxRows = 1000;
	/**
	 * Set a sql command used for the simple subgrid
	 * @var string
	 */
	public $SubgridCommand = "";
	/**
	 * set a table to display a data to the grid
	 * @var string
	 */
	public $table = "";
	/**
	* Holds the primary key for the table
	* @var string
	*/
	protected $primaryKey;
	/**
	 *
	 * Obtain the SQL qurery from XML file.
	 * In this case the SelectCommand should be set as xmlfile.sqlid.
	 * The xmlfile is the name of xml file where we store the sql commands,
	 * sqlid is the id of the required sql.
	 * The simple xml file can look like this
	 * < ?xml version="1.0" encoding="UTF-8"?>
	 * <queries>
	 * <sql Id="getUserById">
	 *   Select *
	 *   From users
	 *   Where Id = ?
	 *   </sql>
	 *  <sql Id="validateUser">
	 *   Select Count(Id)
	 *   From users
	 *   Where Email = ? AND Password = ?
	 *  </sql>
	 * </queries>
	 * Important note: The class first look for readFromXML, then for
	 * selectCommand and last for a table.
	 * @var boolean
	 */
	public $readFromXML = false;
	/**
	 * Used to store the additional userdata which will be transported
	 * to the grid when the request is made. Used in addRowData method
	 * @var <array>
	 */
	protected $userdata = null;
	/**
	 * Custom function which can be called to modify the grid output. Parameters
	 * passed to this function are the response object and the db connection
	 * @var function
	 */
	public $customFunc = null;
	/**
	 * Custom call can be used again with custom function customFunc. We can call
	 * this using static defined functions in class customClass::customFunc - i.e
	 * $grid->customClass = Custom, $grid->customFunc = myfunc
	 * or $grid->customClass = new Custom(), $grid->customFunc = myfunc
	 * @var mixed
	 */
	public $customClass = false;
	public $customError = null;
	/**
	 * Defines if the xml otput should be enclosed in CDATA when xml output is enabled
	 * @var boolean
	 */
	public $xmlCDATA = false;
	/**
	 * Optimizes the search SQL when used in MySQL with big data sets.
	 * Use this option carefully on complex SQL
	 * @var boolean
	 */
	public $optimizeSearch = false;
	/**
	 * If set to true make the search case insensitive.
	 * The upper/lower SQL function and upper/lower php functions should be set
	 * @var type boolean
	 */
	public $ignoreCaseSearch = false;
	protected $caseSearchOpt =  array ( "sqlfunc" => "UPPER", "phpfunc" => "strtoupper" );
	public function setSearchOptions( $aopt ) {
		if(is_array($aopt)) {
			$this->caseSearchOpt = array_merge($this->caseSearchOpt, $aopt);
		}
	}
	/**
	 *
	 * @var boolean
	 */
	public $cacheCount = false;
	/**
	 * Internal set in queryGrid if we should use count query in order to set
	 * the grid output
	 * @var boolean
	 */
	public $performcount = true;
	/**
	 *
	 * @var string
	 * Determines the current operation of the grid - i.e add, edit , del search
	 * Can be queried at any time
	 */
	public $oper;
	/**
	 *
	 * @var boolean
	 * 
	 * If set to true the summary data is calculated using alias.
	 * Example if we have $sql then adding the needed summary fields to $s
	 * as SUM(a) AS a... , then we do
	 * SELECT COUNT(*) AS COUNTR ".$s." FROM ($sql) gridalias
	 */
	public $summaryalias =  true; 

	/**
	 *
	 * Constructor
	 * @param resource -  $db the database connection passed to the constructor
	 */
	function __construct($db=null, $odbctype='')
	{
		if(class_exists('jqGridDB')) {
			$interface = jqGridDB::getInterface();
		} else {
			$interface = 'local';
		}
		$this->pdo = $db;
		if($interface == 'pdo' && is_object($this->pdo))
		{
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->dbtype = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
			if($this->dbtype == 'pgsql') { $this->I = 'I'; }
		} else {
			$this->dbtype = $interface.$odbctype;
			$this->odbc = $odbctype;
		}
		if($interface == 'array') {
			$this->summaryalias = false;
		}
		$oper = $this->GridParams["oper"];
		$this->oper = jqGridUtils::GetParam($oper,false);
	}
	/**
	 * Prepares a $sqlElement and binds a parameters $params
	 * Return prepared sql statement
	 * @param string $sqlElement sql to be prepared
	 * @param array $params - parameters passed to the sql
	 * @return string
	 */
	protected function parseSql($sqlElement, $params, $bind=true)
	{
		$sql = jqGridDB::prepare($this->pdo,$sqlElement, $params, $bind);
		return $sql;
	}
	/**
	 * Executes a prepared sql statement. Also if limit is set to true is used
	 * to return limited set of records
	 * Return true on success
	 * @param string $sqlId - sql to pe executed
	 * @param array $params - array of values which are passed as parameters
	 * to the sql
	 * @param resource $sql - pointer to the constructed sql
	 * @param boolean $limit - if set to true we use a pagging mechanizm
	 * @param integer $nrows - number of rows to return
	 * @param integer $offset - the offset from which the nrows should be returned
	 * @return boolean
	 */
	protected function execute($sqlId, $params, &$sql, $limit=false,$nrows=-1,$offset=-1, $order='', $sort='')
	{
		if($this->dbtype == 'mongodb') {
			return jqGridDB::mongoexecute($sqlId, $params, $sql, $limit, $nrows=0, $offset, $order, $sort, $this->mongofields);
		}
		if($this->dbtype == 'array') {
			if($params && is_array($params)) {
				foreach($params as $k=>$v) {
					$params[$k] = "'".$v."'";
				}
			}
		}
		$this->select= $sqlId;
		if($limit) {
			if($this->dbtype == "adodb") {
				//$conn, $sql,$numrows=-1,$offset=-1,$inputarr=false 
				$sql =  jqGridDB::limit($this->pdo, $this->select, $nrows, $offset, $params );
				if($this->debug) { $this->logQuery($sql->sql, $params); }
				return $sql ? true : false;
			} else  {
				$this->select = jqGridDB::limit($this->select, $this->dbtype, $nrows,$offset, $order, $sort );
			}
		}
		if($this->debug) { $this->logQuery($this->select, $params); }
		$this->params = $params;
		try {
			$sql = $this->parseSql($this->select, $params);
			$ret = true;
			if($sql) {
				if($this->dbtype == "adodb") { 
					$sql = jqGridDB::execute($sql, $params, $this->pdo); //DB2
					$ret = $sql ? true : false;
				} else {
					$ret = jqGridDB::execute($sql, $params, $this->pdo); //DB2
				}
				
			}
			//$sql = $this->pdo->Execute($sql, $params);
			if(!$ret) {
				$this->errorMessage = jqGridDB::errorMessage( $this->pdo );
				throw new Exception($this->errorMessage);
			}
		} catch (Exception $e) {
			if(!$this->errorMessage) { $this->errorMessage = $e->getMessage(); }
			if($this->showError) {
				$this->sendErrorHeader();
			} else {
				echo $this->errorMessage;
			}
			return false;
		}
		return true;
	}

	/**
	 * Read a xml file and the SelectCommand and return the sql string
	 * Return string if the query is found false if not.
	 * @param string $sqlId the string of type xmlfile.sqlId
	 * @return mixed
	 */
	protected function getSqlElement($sqlId)
	{
		$tmp = explode('.', $sqlId);
		$sqlFile = trim($tmp[0]) . '.xml';
		if(file_exists($sqlFile)) {
			$root = simplexml_load_file($sqlFile);
			foreach($root->sql as $sql)
			{
				if ($sql['Id'] == $tmp[1]) {
					if(isset ($sql['table']) && strlen($sql['table'])>0 ) {
						$this->table = $sql['table'];
					}
					if(isset ($sql['primary']) && strlen($sql['primary'])>0 ) {
						$this->primaryKey = $sql['primary'];
					}
					return $sql;
				}
			}
		}
		return false;
	}
	/**
	 * Returns object which holds the total records in the query and optionally
	 * the sum of the records determined in sumcols
	 * @param string $sql - string to be parsed
	 * @param array $params - parameters passed to the sql query
	 * @param array $sumcols - array which holds the sum of the setted rows.
	 * The array should be associative where the index corresponds to the names
	 * of colModel in the grid, and the value correspond to the actual name in
	 * the query
	 * @return object
	 */
	protected function _getcount($sql, array $params=null, array $sumcols=null)
	{
		$qryRecs = new stdClass();
		$qryRecs->COUNTR = 0;
		$s ='';
		if(is_array($sumcols) && !empty($sumcols)) {
			foreach($sumcols as $k=>$v) {
				if(is_array($v)) {
					foreach($v as $dbfield=>$oper){
						$s .= ",".trim($oper)."(".$dbfield.") AS ".$k;
					}
				} else {
					$s .= ",SUM(".$v.") AS ".$k;
				}
			}
		}
		$sql = str_replace("\r", " ", $sql, $count1);
		if ($this->summaryalias === true ||
			preg_match("/^\s*SELECT\s+DISTINCT/is", $sql) ||
			preg_match('/\s+GROUP\s+BY\s+/is',$sql) ||
			preg_match('/\s+UNION\s+/is',$sql) ||
			substr_count(strtoupper($sql), 'SELECT ') > 1 ||
			substr_count(strtoupper($sql), ' FROM ') > 1 ||
			$this->dbtype == 'oci8'	) {
			// ok, has SELECT DISTINCT or GROUP BY so see if we can use a table alias
			// but this is only supported by oracle and postgresql... and at end in mysql5
			//if($this->dbtype == 'pgsql' )
				$rewritesql = "SELECT COUNT(*) AS COUNTR ".$s." FROM ($sql) gridalias";
				//else $rewritesql = "SELECT COUNT(*) AS COUNT ".$s." FROM ($sql)";
		} else {
			// now replace SELECT ... FROM with SELECT COUNT(*) FROM
			$rewritesql = preg_replace('/^\s*SELECT\s.*\s+FROM\s/Uis','SELECT COUNT(*) AS COUNTR '.$s.' FROM ',$sql);
		}

		if (isset($rewritesql) && $rewritesql != $sql) {
			//if (preg_match('/\sLIMIT\s+[0-9]+/i',$sql,$limitarr)) $rewritesql .= $limitarr[0];
			// ok, remove the parsing for limit since it is problem in subquery with limit.
			// in this case limit should not be used in the main query
			$qryRecs = $this->queryForObject($rewritesql, $params, false);
			if ($qryRecs) { return $qryRecs; }
		}
		return $qryRecs;
	}

	/**
	 * Return the object from the query
	 * @param string $sqlId the sql to be queried
	 * @param array $params - parameter values passed to the sql
	 * @param boolean $fetchAll - if set to true fetch all records
	 * @return object
	 */
	protected function queryForObject($sqlId, $params, $fetchAll=false)
	{
		$sql = null;
		$ret = $this->execute($sqlId, $params, $sql, false);
		if ($ret) {
			$ret = jqGridDB::fetch_object($sql,$fetchAll,$this->pdo);
			jqGridDB::closeCursor($sql);
		}
		return $ret;
	}
	/**
	 *
	 * Recursivley build the sql query from a json object
	 * @param object $group the object to parse
	 * @param array $prm parameters array
	 * @return array - first element is the where clause secon is the array of values to pass
	 */
	protected function getStringForGroup( $group, $prm )
	{
		$i_ = $this->I;
		$sopt = array('eq' => "=",'ne' => "<>",'lt' => "<",'le' => "<=",'gt' => ">",'ge' => ">=",'bw'=>" {$i_}LIKE ",'bn'=>" NOT {$i_}LIKE ",'in'=>' IN ','ni'=> ' NOT IN','ew'=>" {$i_}LIKE ",'en'=>" NOT {$i_}LIKE ",'cn'=>" {$i_}LIKE ",'nc'=>" NOT {$i_}LIKE ", 'nu'=>'IS NULL', 'nn'=>'IS NOT NULL');
		$s = "(";
		if( isset ($group['groups']) && is_array($group['groups']) && count($group['groups']) >0 )
		{
			for($j=0; $j<count($group['groups']);$j++ )
			{
				if(strlen($s) > 1 ) {
					$s .= " ".$group['groupOp']." ";
				}
				try {
					$dat = $this->getStringForGroup($group['groups'][$j], $prm);
					$s .= $dat[0];
					$prm = $prm + $dat[1];
				} catch (Exception $e) {
					echo $e->getMessage();
				}
			}
		}
		if (isset($group['rules']) && count($group['rules'])>0 ) {
			try{
				$s_s = $s_p = false;
				if($this->ignoreCaseSearch) {
					$s_s = isset($this->caseSearchOpt['sqlfunc']) ? $this->caseSearchOpt['sqlfunc'] : false;
					$s_p = isset($this->caseSearchOpt['phpfunc']) ? $this->caseSearchOpt['phpfunc'] : false;
				}
				foreach($group['rules'] as $key=>$val) {
					if (strlen($s) > 1) {
						$s .= " ".$group['groupOp']." ";
					}
					$field = $val['field'];
					$op = $val['op'];
					$v = $val['data'];
					if( strtolower($this->encoding) != 'utf-8' ) {
						$v = iconv("utf-8", $this->encoding."//TRANSLIT", $v);
					}
					if( $s_s && $s_p ) {
						$field =  $s_s."( ".$field ." )";
						$v = call_user_func( $s_p, $v);
					}
					if( $op ) {
						if(in_array($field,$this->datearray)){
							$v = jqGridUtils::parseDate($this->userdateformat,$v,$this->dbdateformat);
						}
						switch ($op)
						{
							case 'bw':
							case 'bn':
								$s .= $field.' '.$sopt[$op]." ?";
								$prm[] = "$v%";
								break;
							case 'ew':
							case 'en':
								$s .= $field.' '.$sopt[$op]." ?";
								$prm[] = "%$v";
								break;
							case 'cn':
							case 'nc':
								$s .= $field.' '.$sopt[$op]." ?";
								$prm[] = "%$v%";
								break;
							case 'in':
							case 'ni':
								$in_array = explode(",",$v);
								$tmp = str_repeat('?,', count($in_array) - 1) . '?';
								$s .= $field.' '.$sopt[$op]."( $tmp )";
								$prm = array_merge($prm, $in_array);
								break;
							case 'nu':
							case 'nn':
								$s .= $field.' '.$sopt[$op]." ";
								//$prm[] = $v;
								break;
							default :
								$s .= $field.' '.$sopt[$op]." ?";
								$prm[] = $v;
								break;
						}
					}
				}
			} catch (Exception $e) 	{
				echo $e->getMessage();
			}
		}
		$s .= ")";
		if ($s == "()") {
			return array("",$prm); // ignore groups that don't have rules
		} else {
			return array($s,$prm);
		}
	}

	/**
	 * Builds the search where clause when the user perform a search
	 * Return arrray the first element is a strinng with the where clause,
	 * the second element is array containing the value parameters passed to
	 * the sql.
	 *
	 * @param array $prm - parameters passed to the sql
	 * @return array
	 */
	protected function _buildSearch( array $prm=null, $str_filter = '' )
	{
		$filters = ($str_filter && strlen($str_filter) > 0 ) ? $str_filter : jqGridUtils::GetParam($this->GridParams["filter"], "");
		$rules = array();
		// multiple filter
		if($filters) {
			$count = 0;
			//$filters = str_replace('$', '\$', $filters, $count);
			if( function_exists('json_decode') && strtolower(trim($this->encoding)) == "utf-8" && $count==0 ) {
				$jsona = json_decode($filters,true);
			} else {
				$jsona = jqGridUtils::decode($filters);
			}
			if(isset($jsona) && is_array($jsona)) {
				$gopr = $jsona['groupOp'];
				$rules[0]['data'] = 'dummy'; //$jsona['rules'];
			}
		// single filter
		} else if (jqGridUtils::GetParam($this->GridParams['searchField'],'')){
			$gopr = '';
			$rules[0]['field'] = jqGridUtils::GetParam($this->GridParams['searchField'],'');
			$rules[0]['op'] = jqGridUtils::GetParam($this->GridParams['searchOper'],'');
			$rules[0]['data'] = jqGridUtils::GetParam($this->GridParams['searchString'],'');
			$jsona = array();
			$jsona['groupOp'] = "AND";
			$jsona['rules'] = $rules;
			$jsona['groups'] = array();
		}
		$ret = array("",$prm);
		if(isset($jsona) && $jsona) {
			if($rules && count($rules) > 0 ) {
				if(!is_array($prm)) { $prm=array(); }
				$ret = $this->getStringForGroup($jsona, $prm);
				if(count($ret[1]) == 0 ) { $ret[1] = null; }
			}
		}
		return $ret;
	}
	/**
	 * Build a search string from filter string posted from the grid
	 * Usually use this functio separatley
	 * @param string $filter
	 * @param string $otype
	 * @return mixed - string or array depending on $otype param
	 */
	public function buildSearch ( $filter, $otype = 'str' )
	{
		$ret = $this->_buildSearch( null, $filter );
		if($otype === 'str') {
			$s2a = explode("?",$ret[0]);
			$csa = count($s2a);
			$s = "";
			for($i=0; $i < $csa-1; $i++)
			{
				$s .= $s2a[$i]." '".$ret[1][$i]."' ";
			}
			$s .= $s2a[$csa-1];
			return $s;
		}
		return $ret;
	}
	/**
	 * Bulid the sql based on $readFromXML, $SelectCommand and $table variables
	 * The logic is: first we look if readFromXML is set to true, then we look for
	 * SelectCommand and at end if none of these we use the table varable
	 * Return string or false if the sql found
	 * @return mixed
	 */
	protected function _setSQL()
	{
		$sqlId = false;
		if($this->readFromXML==true && strlen($this->SelectCommand) > 0 ){
			$sqlId = $this->getSqlElement($this->SelectCommand);
		} else if($this->SelectCommand && strlen($this->SelectCommand) > 0) {
			$sqlId = $this->SelectCommand;
		} else if($this->table && strlen($this->table)>0) {
			if($this->dbtype == 'mongodb') {
				$sqlId = $this->table;
			} else {
				$sqlId = "SELECT * FROM ".(string)$this->table;
			}
		}
		if($this->dbtype == 'mongodb') {
			$sqlId = $this->pdo->selectCollection($sqlId);
		}
		return $sqlId;
	}
	/**
	 * Return the current date format used from the client
	 * @return string
	 */
	public function getUserDate()
	{
		return $this->userdateformat;
	}
	/**
	 * Set a new user date format using PHP convensions
	 * @param string $newformat - the new format
	 */
	public function setUserDate($newformat)
	{
		$this->userdateformat = $newformat;
	}
	/**
	 * Return the current datetime format used from the client
	 * @return string
	 */
	public function getUserTime()
	{
		return $this->usertimeformat;
	}
	/**
	 * Set a new user datetime format using PHP convensions
	 * @param string $newformat - the new format
	 */
	public function setUserTime($newformat)
	{
		$this->usertimeformat = $newformat;
	}
	/**
	 * Return the current date format used in the undelayed database
	 * @return string
	 */
	public function getDbDate()
	{
		return $this->dbdateformat;
	}
	/**
	 * Set a new  database date format using PHP convensions
	 * @param string $newformat - the new database format
	 */
	public function setDbDate($newformat)
	{
		$this->dbdateformat = $newformat;
	}
	/**
	 * Return the current datetime format used in the undelayed database
	 * @return string
	 */
	public function getDbTime()
	{
		return $this->dbtimeformat;
	}
	/**
	 * Set a new  database datetime format using PHP convensions
	 * @param string $newformat - the new database format
	 */
	public function setDbTime($newformat)
	{
		$this->dbtimeformat = $newformat;
	}
	/**
	 *
	 * Return the associative array which contain the parameters
	 * that are sended from the grid to request, search, update delete data.
	 * @return array
	 */
	public function getGridParams()
	{
		return $this->GridParams;
	}
	/**
	 * Set a grid parameters to identify the action from the grid
	 * Note that these should be set in the grid - i.e the parameters from the grid
	 * should equal to the GridParams.
	 * @param array $_aparams set new parameter.
	 */
	public function setGridParams($_aparams)
	{
		if(is_array($_aparams) && !empty($_aparams)) {
			$this->GridParams = array_merge($this->GridParams, $_aparams);
		}
	}
	/**
	 * Will select, getting rows from $offset (1-based), for $nrows.
	 * This simulates the MySQL "select * from table limit $offset,$nrows" , and
	 * the PostgreSQL "select * from table limit $nrows offset $offset". Note that
	 * MySQL and PostgreSQL parameter ordering is the opposite of the other.
	 * eg. Also supports Microsoft SQL Server
	 * SelectLimit('select * from table',3); will return rows 1 to 3 (1-based)
	 * SelectLimit('select * from table',3,2); will return rows 3 to 5 (1-based)
	 * Return object containing the limited record set
	 * @param string $limsql - optional sql clause
	 * @param integer is the number of rows to get
	 * @param integer is the row to start calculations from (1-based)
	 * @param array	array of bind variables
	 * @return object
	 */
	public function selectLimit($limsql='', $nrows=-1, $offset=-1, array $params=null, $order='', $sort='')
	{
		$sql = null;
		$sqlId = strlen($limsql)>0 ? $limsql : $this->_setSQL();
		if(!$sqlId) { return false; }
		$ret = $this->execute($sqlId, $params, $sql, true,$nrows,$offset, $order, $sort);
		if ($ret) {
			$ret = jqGridDB::fetch_object($sql, true, $this->pdo);
			jqGridDB::closeCursor($sql);
			return $ret;
		} else {
			return $ret;
		}
	}
	/**
	 * Return the result of the query to jqGrid. Support searching
	 * @param array $summary - set which columns should be sumarized in order to be displayed to the grid
	 * By default this parameter uses SQL SUM function: array("colmodelname"=>"sqlname");
	 * It can be set to use the other one this way
	 * array("colmodelname"=>array("sqlname"=>"AVG"));
	 * By default the first field correspond to the name of colModel the second to
	 * the database name
	 * @param array $params - parameter values passed to the sql
	 * @param boolen $echo if set to false return the records as object, otherwiese json encoded or xml string
	 * depending on the dataType variable
	 * @return mixed
	 */
	public function queryGrid( array $summary=null, array $params=null, $echo=true)
	{
		$sql = null;
		$sqlId = $this->_setSQL();
		if(!$sqlId) { return false; }
		//$page = $this->GridParams['page'];
		$page = (int)jqGridUtils::GetParam( $this->GridParams['page'],'1'); // get the requested page
		//$limit = $this->GridParams['rows'];
		$limit = (int)jqGridUtils::GetParam( $this->GridParams['rows'],'20'); // get how many rows we want to have into the grid
		//$sidx = $this->GridParams['sort'];
		$sidx = jqGridUtils::GetParam( $this->GridParams['sort'],''); // get index row - i.e. user click to sort
		//$sord = $this->GridParams['order'];
		$sord = jqGridUtils::GetParam( $this->GridParams['order'],''); // get the direction
		//$search = $this->GridParams['search'];
		$search = jqGridUtils::GetParam( $this->GridParams['search'],'false'); // get the direction
		$totalrows = jqGridUtils::GetParam($this->GridParams['totalrows'],'');
		$sord = preg_replace("/[^a-zA-Z0-9]/", "", $sord);
		$sidx = preg_replace("/[^a-zA-Z0-9. _,()]/", "", $sidx);
		$performcount = true;
		$gridcnt = false;
		$gridsrearch = '1';
		if($this->cacheCount) {
			$gridcnt = jqGridUtils::GetParam('grid_recs',false);
			$gridsrearch = jqGridUtils::GetParam('grid_search','1');
			if($gridcnt && (int)$gridcnt >= 0 ) $performcount = false;
		}
		if($search == 'true') {
			if($this->dbtype == 'mongodb') {
				$params = jqGridDB::_mongoSearch($params, $this->GridParams, $this->encoding, $this->datearray, $this->mongointegers);
			} else {
				$sGrid = $this->_buildSearch($params);
				if($this->optimizeSearch === true || $this->dbtype=='array') {
					$whr = "";
					if($sGrid[0]) {
						if(preg_match("/\s+WHERE\s+/is",$sqlId)) {// to be refined
							$whr = " AND ".$sGrid[0];
						} else {
							$whr = " WHERE ".$sGrid[0];
						}
					}
					$sqlId .= $whr;
				} else {
					$whr = $sGrid[0] ? " WHERE ".$sGrid[0] : "";
					$sqlId = "SELECT * FROM (".$sqlId.") gridsearch".$whr;
				}
				$params = $sGrid[1];
				if($this->cacheCount && $gridsrearch !="-1") {
					$tmps = crc32($whr."data".implode(" ",$params));
					if($gridsrearch != $tmps) {
						$performcount = true;
					}
					$gridsrearch = $tmps;
				}
			}
		} else {
			if($this->cacheCount && $gridsrearch !="-1") {
				if($gridsrearch != '1') {
					$performcount = true;
				}
			}
		}
		$performcount = $performcount && $this->performcount;
		if($performcount) {
			if($this->dbtype == 'mongodb') {
				$qryData = jqGridDB::_mongocount($sqlId, $params, $summary);
			} else {
				$qryData = $this->_getcount($sqlId,$params,$summary);
			}
			if(is_object($qryData)) {
				if(!isset($qryData->countr)) { $qryData->countr = null; }
				if(!isset($qryData->COUNTR)) { $qryData->COUNTR = null; }
				$count = $qryData->COUNTR ? $qryData->COUNTR : ($qryData->countr ?  $qryData->countr : 0);
			} else {
				$count = isset($qryData['COUNTR']) ? $qryData['COUNTR'] : 0;
			}
		} else {
			$count = $gridcnt;
		}
		if( $count > 0 ) {
			$total_pages = ceil($count/$limit);
		} else {
			$count = 0;
			$total_pages = 0;
			$page = 0;
		}
		if ($page > $total_pages) {  $page=$total_pages; }
		$start = $limit*$page - $limit; // do not put $limit*($page - 1)
		if ($start<0) { $start = 0; }
		/*
		if($this->dbtype == 'sqlsrv' || $this->dbtype == 'odbcsqlsrv') {
			$difrec = abs($start-$count);
			if( $difrec < $limit)
			{
				$limit = $difrec;
			}
		}
		 * 
		 */
		$result = new stdClass();
		if(is_array($summary)) {
			if(is_array($qryData))  { unset($qryData['COUNTR']);}
			else { unset($qryData->COUNTR,$qryData->countr); }
			foreach($qryData as $k=>$v) {
				if ($v == null) { $v = 0; }
				$result->userdata[$k] = $v;
			}
		}
		if($this->cacheCount) {
			$result->userdata['grid_recs'] = $count;
			$result->userdata['grid_search'] = $gridsrearch;
			$result->userdata['outres'] = $performcount;
		}
		if($this->userdata) {
			if(!isset ($result->userdata)) { $result->userdata = array(); }
			$result->userdata = jqGridUtils::array_extend($result->userdata, $this->userdata);
		}
		$result->records = $count;
		$result->page = $page;
		$result->total = $total_pages;
		$uselimit = true;
		if($totalrows ) {
			$totalrows = (int)$totalrows;
			if(is_int($totalrows)) {
				if($totalrows === -1) {
					$uselimit = false;
				} else if($totalrows >0 ){
					$limit = $totalrows;
				}
			}
		}
		// build search before order clause
		if($this->dbtype !== 'mongodb' && $this->dbtype !== 'sqlsrv' && $this->dbtype !== 'odbcsqlsrv') {
			if($sidx) { $sqlId .= " ORDER BY ".$sidx." ".$sord; }
		}
		$ret = $this->execute($sqlId, $params, $sql, $uselimit ,$limit,$start, $sidx, $sord);
		if ($ret) {
			$result->rows = jqGridDB::fetch_object($sql, true, $this->pdo);
			jqGridDB::closeCursor($sql);
			if($this->customClass) {
				try {
					$result = call_user_func(array($this->customClass,$this->customFunc),$result,$this->pdo);
				} catch (Exception $e) {
					echo "Can not call the method class - ".$e->getMessage();
				}
			} else if(function_exists($this->customFunc)) {
					$result = call_user_func($this->customFunc,$result,$this->pdo);
			}
			if($echo){
				$this->_gridResponse($result, jqGridUtils::GetParam('callback',false));
			} else {
				if($this->debug) { $this->debugout(); }
				return $result;
			}
		} else {
			echo "Could not execute query!!!";
		}
		if($this->debug) $this->debugout();
	}
	/**
	 *
	 * Function return the generated SQL query with 
	 * parameter placeholders. It should be called after the main methods
	 * queryGrid, editGrid, renderGrid in order to get the right query
	 * 
	 * @return string
	 *  
	 */
	public function getSqlQuery()
	{
		return $this->select;
	}
	public function getQueryParams()
	{
		return $this->params;
	}

	/**
	 * Convert the query to record set and set the summary data if available
	 * @param array $params parameters to the qurery
	 * @param array $summary summary option
	 * @param boolean excel
	 * @return recordset object
	 */
	protected function _rs($params=null, $summary=null, $excel=false)
	{
		if($this->ExportCommand && strlen($this->ExportCommand)>0 ) {
			$sqlId = $this->ExportCommand;
		} else {
			$sqlId = $this->_setSQL();
		}
		if(!$sqlId) { return false; }

		//$sidx = $this->GridParams['sort'];
		$sidx = jqGridUtils::GetParam($this->GridParams['sort'], ''); // get index row - i.e. user click to sort
		//$sord = $this->GridParams['order'];
		$sord = jqGridUtils::GetParam($this->GridParams['order'],''); // get the direction
		//$search = $this->GridParams['search'];
		$search = jqGridUtils::GetParam($this->GridParams['search'],'false'); // get the direction
		$sord = preg_replace("/[^a-zA-Z0-9]/", "", $sord);
		$sidx = preg_replace("/[^a-zA-Z0-9. _,]/", "", $sidx);

		if($search == 'true') {
			if($this->dbtype == 'mongodb') {
				$params = jqGridDB::_mongoSearch($params, $this->GridParams, $this->encoding, $this->datearray, $this->mongointegers);
			} else {
				$sGrid = $this->_buildSearch( $params);
				if( $this->dbtype=='array') {
					$whr = "";
					if($sGrid[0]) {
						if(preg_match("/\s+WHERE\s+/is",$sqlId)) { // to be refined
							$whr = " AND ".$sGrid[0];
						} else {
							$whr = " WHERE ".$sGrid[0];
						}
					}
					$sqlId .= $whr;
				} else {
					$whr = $sGrid[0] ? " WHERE ".$sGrid[0] : "";
					$sqlId = "SELECT * FROM (".$sqlId.") gridsearch".$whr;
				}
				$params = $sGrid[1];
			}
		}
		if($this->dbtype !== 'mongodb' && $this->dbtype !== 'sqlsrv' && $this->dbtype !== 'odbcsqlsrv') {
			if($sidx) { $sqlId .= " ORDER BY ".$sidx." ".$sord; }
		}
		if( is_array($summary)) {
			if($this->dbtype == 'mongodb') {
				$qryData = jqGridDB::_mongocount($sqlId, $params, $summary);
			} else {
				$qryData = $this->_getcount($sqlId, $params, $summary);
			}
			unset($qryData->COUNTR,$qryData->countr);
			foreach($qryData as $k=>$v)
			{
				if ($v == null) { $v = 0; }
				$this->tmpvar[$k] = $v;
			}
		}
		if($this->userdata) {
			if(!$this->tmpvar) {
				$this->tmpvar = array();
			}
			$this->tmpvar = jqGridUtils::array_extend($this->tmpvar, $this->userdata);

		}
		if($this->debug) {
			$this->logQuery($sqlId, $params);
			$this->debugout();
		}
		$this->execute($sqlId, $params, $sql, true, $this->gSQLMaxRows, 0, $sidx, $sord );
		return $sql;
	}


	/**
	 *
	 * Return the result of the query for the simple subgrid
	 * The format depend of dataType variable
	 * @param array $params parameters passed to the query
	 * @param boolean $echo if set to false return object containing the data
	 */
	public function querySubGrid($params, $echo=true)
	{
		if($this->SubgridCommand && strlen($this->SubgridCommand)>0) {
			$result = new stdClass();
			$result->rows = $this->queryForObject($this->SubgridCommand, $params,true);
			if($echo) {
				$this->_gridResponse($result, jqGridUtils::GetParam('callback',false));
			} else {
				return $result;
			}
		}
	}
	/**
	 *
	 * Check in which format data should be returned to the grid based on dataType property
	 * Add the appropriate headers and echo the result
	 * @param string $response can be xml, json
	 * @callback string if set returned data is in jsonp rules - i.e $callback( json )
	 */
	protected function _gridResponse($response, $callback =  null)
	{
		if($this->dataType=="xml")
		{
			if(isset($response->records)) {
				$response->rows["records"]= $response->records;
				unset($response->records);
			}
			if(isset($response->total)) {
				$response->rows["total"]= $response->total;
				unset($response->total);
			}
			if(isset($response->page)) {
				$response->rows["page"]= $response->page;
				unset($response->page);
			}
			if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") )
			{
				header("Content-type: application/xhtml+xml;charset=",$this->encoding);
			} else {
				header("Content-type: text/xml;charset=".$this->encoding);
			}
			echo jqGridUtils::toXml($response,'root', null, $this->encoding, $this->xmlCDATA );
		} else if ($this->dataType=="json" || $this->dataType =="jsonp") {
			$jsonp = $this->dataType =="jsonp" && $callback;
			if($jsonp) {
				header("Access-Control-Allow-Origin: *");
			}
			header("Content-type: text/x-json;charset=".$this->encoding);
			if(function_exists('json_encode') && strtolower($this->encoding) == 'utf-8') {
				if($jsonp) {
					echo $callback."(". json_encode($response) . ")";
				} else {
					echo json_encode($response);
				}
			} else {
				if($jsonp) {
					echo $callback."(". jqGridUtils::encode($response). ")";
				} else {
					echo jqGridUtils::encode($response);
				}
			}
		}
	}
	/**
	 * Add a custom data to the grid footer row if it is enabled.
	 * Also can be used to transport additional data in userdata array to be
	 * used later at client side.
	 * The syntax is $grid->addUserData(array("Name1"=>"Data1",...));
	 * The method is executed after the sumarry rows are executed, so it can
	 * overwrite some summary data which is palced on the footer.
	 * @param array $adata
	 */
	public function addUserData($adata){
		if(is_array($adata))
			$this->userdata = $adata;
	}
}

