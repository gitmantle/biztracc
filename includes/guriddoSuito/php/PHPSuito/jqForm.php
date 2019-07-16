<?php
/**
 * @author  Tony Tomov, (tony@trirand.com)
 * @copyright TriRand Ltd
 * @version  5.3.1
 * @package jqForm
 *
 * @abstract
 * A PHP class to create and work with HTML5 Forms.
 * 
 */
/** PHPSuito root directory */
if (!defined('PHPSUITO_ROOT')) {
    define('PHPSUITO_ROOT', dirname(__FILE__) . '/');
    require(PHPSUITO_ROOT . 'Autoloader.php');
}

class jqForm {
	/** 
	 * @var array 
	 * Array where all form elements are puted again with the 
	 * prperties
	 */
	protected $aelements=array();
	/**
	 *
	 * @var resource
	 * Holds the connection if it is set
	 */
	protected $conn;
	/**
	 *
	 * @var string
	 * Automativcally builded database type based on the driver
	 */
	protected $dbtype = 'none';
	/**
	 *
	 * @var string
	 * Build the id of the input elements if they is not set.
	 * The result id is a combination of the name of the form plus "_" plus
	 * the name of the input element
	 */
	protected $idprefix;
	/**
	 *
	 * @var integer
	 * Internal variable which is build based on the column layout
	 * @see setColumnLayout method
	 */
	private  $maxcol=2;
	/**
	 *
	 * @var mixed
	 * if the header is enabled this variable is a string which holds
	 * the form header
	 */
	private $frmheader = false;
	/**
	 *
	 * @var mixed
	 * if the footer is enabled this variable is a string which holds
	 * the form footer
	 */
	private $frmfooter = false;
	/**
	 *
	 * @var string
	 * @see setTableStyles
	 * Holds the inline style for the table where the form input elements
	 * 
	 */
	private $tblstyle="";
	/**
	 *
	 * @var string
	 * @see setTableStyles
	 * Holds the in line style for the label column in the table input elements
	 */
	private $labelstyle="";
	/**
	 *
	 * @var string
	 * @see setTableStyles
	 * Holds the in line style for the data column in the table input elements
	 */
	private $datastyle="";
	/**
	 *
	 * @var string
	 * Set a common class for the input, select and textarea elements
	 */
	public $inputclass = "ui-widget-content ui-corner-all";
	/**
	 *
	 * @var string
	 * a SQL query command which is used to obtain a data and put it into the
	 * form. A Ajax is used for this purpose after the form is created
	 */
	public $SelectCommand ="";
	/**
	 *
	 * @var string
	 * Set the table where the data will be stored after posted from the form.
	 */
	public $table="";
	/**
	 *
	 * @var array
	 * @see setPrimaryKeys
	 * Holds the primary key(s) for the table
	 */
	private $primarykeys = array();
	/**
	 *
	 * @var array
	 * @see addEvent
	 * Array contain a form related events
	 */
	private $formevents = array();
	/**
	 *
	 * @var array
	 * Store the fields from the table and it types.
	 * Used in the save method
	 */
	private $fields = array();
	/**
	 *
	 * @var string
	 * Used in __construct and determines the operation type.
	 * This allow to call different methods based on the operation sended from
	 * the form
	 */
	public $oper;
	/**
	 *
	 * @var string
	 * @see setUrl
	 * 
	 * Url from where to obtain and post data
	 */
	private $url;
	/**
	 *
	 * @var string
	 * Custom java script code to be executed after the form is created.
	 */
	private $customCode;
	/**
	 *
	 * @var array
	 * Holds all the options for the form. These options are used in the ajax
	 * submit
	 */
	private $formoptions = array();
	/**
	 *
	 * @var string
	 * Hold the error message if there are a error and send it to the client
	 */
	private $errorMesage;
	/**
	 *
	 * @var string
	 * The data format used in the database
	 */
	public $DbDate = 'Y-m-d';
	/**
	 *
	 * @var string
	 * The time format used in the database
	 */
	public $DbTime = 'H:i:s';
	/**
	 *
	 * @var string
	 * The datetime format used in the database
	 */
	public $DbDateTime = 'Y-m-d H:i:s';
	/**
	 *
	 * @var string
	 * The date format that should appear in the user input form
	 */
	public $UserDate = 'Y-m-d';
	/**
	 *
	 * @var string
	 * The time format that should appear in the user input form
	 */
	public $UserTime = 'H:i:s';
	/**
	 *
	 * @var string
	 * The datetime format that should appear in the user input form
	 */	
	public $UserDateTime = 'Y-m-d H:i:s';
	/**
	 *
	 * @var boolean
	 * Set if the primary key used is serial (autoincrement)
	 */
	public $serialKey = true;
	/**
	 *
	 * @var string
	 * Encoding used for the data
	 */
	public $encoding = 'utf-8';
	/**
	 *
	 * @var boolen
	 * Set if the update or add in the database should be enclosed in 
	 * transaction
	 */
	public $trans = true;
	/**
	 *
	 * @var boolen
	 * Enables/disables adding a data from the post into the table
	 */
	public $add = true;
	/**
	 *
	 * @var boolen
	 * Enables/disables updating a data from the post into the table
	 */
	
	public $edit = true;
	public $demo = false;

	protected $_checkboxon = false;
	protected $_files = false;
	protected $fileupload = array();
	protected $filedef = array(
		'dir'=>'./',
		'filetypes'=>'',
		'filesize'=>1048576, // bytes 1024*1024 = 1Mb
		'fileprefix'=>''
	);
	/**
	 * If set to true all errors from the server are shown in the client in a
	 * dialog. Curretly work only for grid and form edit.
	 * @var boolean
	 */
	public $showError = false;
	/*
	 * Array that holds the the current log
	 */
	protected static $queryLog = array();
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
	protected $logfile = "jqForm.log";
	/**
	 * Determines if we should perform server side validation.
	 * @var boolean
	 */
	public $serverValidate = false;
	public $serverSanitize = false;

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
	public static $styleshorizontal = array(
		"jqueryui"=>array(
			"formheader" => "ui-state-default ui-corner-all",
			"form"=>"ui-form-horizontal",
			"formfooter"=>"ui-form-footer",
			
			"input" => "ui-input ui-widget-content ui-corner-all ui-form-control",
			"textarea" => "ui-textarea ui-widget-content ui-corner-all ui-form-control",
			"select"=>"ui-select ui-widget-content ui-corner-all ui-form-control",
			"button"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary",
			"label"=>"ui-col-sm-2 ui-control-label",
			"checkbox"=>"ui-checkbox",
			"radio"=>"ui-radio",
			
			"group"=>"ui-form-group",
			"control"=>"ui-col-sm-10",
			"groupcontrol"=>"ui-col-sm-offset-2 ui-col-sm-10"
		),
		"bootstrap"=>array(
			"formheader" => "form-header",
			"form"=>"form-horizontal",
			"formfooter"=>"form-footer",
			
			"input"=> "form-control",
			"textarea" => "form-control",
			"select"=>"form-control",
			"button"=>"btn btn-default",
			"label"=>"col-sm-2 control-label",
			"checkbox"=>"checkbox",
			"radio"=>"radio",
			
			"group"=>"form-group",
			"control"=>"col-sm-10",
			"groupcontrol"=>"col-sm-offset-2 col-sm-10"
//			"actions"=>"form-group"
		)
	);
	public static $stylesvertical = array(
		"jqueryui"=>array(
			"formheader" => "ui-state-default ui-corner-all",
			"form"=>"ui-form-vertical",
			"formfooter"=>"ui-form-footer",
			
			"input" => "ui-input ui-widget-content ui-corner-all ui-form-control",
			"textarea" => "ui-textarea ui-widget-content ui-corner-all ui-form-control",
			"select"=>"ui-select ui-widget-content ui-corner-all ui-form-control",
			"button"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary",
			"label"=>"",
			"checkbox"=>"ui-checkbox",
			"radio"=>"ui-radio",
			
			"group"=>"ui-form-group",
			"control"=>"",
			"groupcontrol"=>""
		),
		"bootstrap"=>array(
			"formheader" => "form-header",
			"form"=>"form-vertical",
			"formfooter"=>"form-footer",
			
			"input"=> "form-control",
			"textarea" => "form-control",
			"select"=>"form-control",
			"button"=>"btn btn-default",
			"label"=>"",
			"checkbox"=>"checkbox",
			"radio"=>"radio",
			
			"group"=>"form-group",
			"control"=>"",
			"groupcontrol"=>""
//			"actions"=>"form-group"
		)
	);
	public static $stylesinline = array(
		"jqueryui"=>array(
			"formheader" => "ui-state-default ui-corner-all",
			"form"=>"ui-form-inline",
			"formfooter"=>"ui-form-footer",
			
			"input" => "ui-input ui-widget-content ui-corner-all ui-form-control",
			"textarea" => "ui-textarea ui-widget-content ui-corner-all ui-form-control",
			"select"=>"ui-select ui-widget-content ui-corner-all ui-form-control",
			"button"=>"ui-button ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary",
			"label"=>"",
			"checkbox"=>"ui-checkbox",
			"radio"=>"ui-radio",
			
			"group"=>"ui-form-group",
			"control"=>"",
			"groupcontrol"=>""
		),
		"bootstrap"=>array(
			"formheader" => "form-header",
			"form"=>"form-inline",
			"formfooter"=>"form-footer",
			
			"input"=> "form-control",
			"textarea" => "form-control",
			"select"=>"form-control",
			"button"=>"btn btn-default",
			"label"=>"",
			"checkbox"=>"checkbox",
			"radio"=>"radio",
			
			"group"=>"form-group",
			"control"=>"",
			"groupcontrol"=>""
//			"actions"=>"form-group"
		)		
	);
	public static $stylestable = array(
		"jqueryui"=>array(
			"formheader" => "ui-state-default ui-corner-all",
			"form"=>"ui-form-table",
			"formfooter"=>"ui-form-footer",
			
			"input" => "ui-widget-content ui-corner-all",
			"textarea" => "ui-widget-content ui-corner-all",
			"select"=>"ui-widget-content ui-corner-all",
			"button"=>"ui-widget ui-state-default ui-corner-all ui-button-text-icon-primary",
			"label"=>"",
			"checkbox"=>"checkbox",
			"radio"=>"radio",
			
			"group"=>"",
			"control"=>"",
			"groupcontrol"=>""
		),
		"bootstrap"=>array(
			"formheader" => "form-header",
			"form"=>"form-table",
			"formfooter"=>"form-footer",
			
			"input"=> "form-control",
			"textarea" => "form-control",
			"select"=>"form-control",
			"button"=>"btn btn-default",
			"label"=>"",
			"checkbox"=>"checkbox",
			"radio"=>"radio",
			
			"group"=>"form-group",
			"control"=>"",
			"groupcontrol"=>""
//			"actions"=>"form-group"
		)		
	);
	protected $activeStyle = 'jqueryui';
	
	protected $view_styles = array('horizontal','vertical','inline','table','nostyle');
	
	protected $view = 'horizontal';
	
	public function setView( $view = 'horizontal') {
		if(in_array($view, $this->view_styles)) {
			$this->view = $view;
		}
	}
	public function setStyling( $style = 'jqueryui')
	{
		if(strlen(trim($style)) > 0 ) {
			if(in_array($style, array_keys(self::$styleshorizontal))) {
				$this->activeStyle = $style;
			}
			
		}
		return 'jqueryui';
	}
	public function getViewStyle () 
	{
		switch ($this->view) {
			case 'horizontal' :
				$ret = self::$styleshorizontal[$this->activeStyle];
				break;
			case 'vertical' :
				$ret = self::$stylesvertical[$this->activeStyle];
				break;
			case 'inline' :
				$ret = self::$stylesinline[$this->activeStyle];
				break;
			case 'table' :
				$ret = self::$stylestable[$this->activeStyle];
				break;
			default :
				$ret = array();
		}
		return $ret;
	}
	public function getViewClass ( $activestyle, $class ) 
	{
		return isset($activestyle[$class]) ? $activestyle[$class] : "";
	}
	/**
	 * Constructor function
	 */
	function __construct($name,  $aproperties=array())
	{
		// reserved for the form elemnt
		$this->aelements[] = array();
		$this->oper = jqGridUtils::GetParam( 'jqform', 'no' );
		$this->setFormProperties($name, $aproperties);
	}
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
	 *
	 * Convert the properties stored in array $prop to string - i.e to DOM 
	 * attributes. 
	 * If the $exclude array is set these properties are not converted.
	 *
	 * @param array $prop
	 * @param array $exclude
	 * @return string 
	 * 
	 */
	protected function propToString( $prop, $exclude=null )
	{
		if($exclude && is_array($exclude)) {
			foreach($exclude as $k=>$v) {
				unset($prop[$v]);
			}
		}
		$ret = "";
		if(isset ($prop['novalidate']) && $prop['novalidate'] == "1") {
			$prop['novalidate'] = "novalidate";
		}
		if(isset ($prop['hidden']) && $prop['hidden'] == "1") {
			$prop['hidden'] = "hidden";
		}
		if(isset ($prop['disabled']) && $prop['disabled'] == "1") {
			$prop['disabled'] = "disabled";
		}
		if(isset ($prop['readonly']) && $prop['readonly'] == "1") {
			$prop['readonly'] = "readonly";
		}
		if(isset ($prop['required']) && $prop['required'] == "1") {
			$prop['required'] = "required";
		}
		if(isset ($prop['autofocus']) && $prop['autofocus'] == "1") {
			$prop['autofocus'] = "autofocus";
		}
		if(isset ($prop['checked']) && $prop['checked'] == "1") {
			$prop['autofocus'] = "checked";
		}
		if(isset ($prop['formnovalidate']) && $prop['formnovalidate'] == "1") {
			$prop['formnovalidate'] = "formnovalidate";
		}
		
		if(is_array($prop) && count($prop)>0) {
			$new_array = array_map(create_function('$key, $value', 'return $key." = \"".$value."\" ";'), array_keys($prop), array_values($prop));
			$ret = implode($new_array);
		}
		return $ret;
	}
	/**
	 * Set the connection used for add/update of the form post and 
	 * when get the certain data.
	 *
	 * @param resource $db 
	 */
	public function setConnection( $db )
	{
		if( $db ) {
			$this->conn = $db;
			if(class_exists('jqGridDB')) { $interface = jqGridDB::getInterface(); }
			else { $interface = 'none'; }
			if($interface == 'pdo' && is_object($this->conn))
			{
				$this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->dbtype = $this->conn->getAttribute(PDO::ATTR_DRIVER_NAME);
			} else {
				$this->dbtype = $interface;
			}
		}
		
	}
	/**
	 * Set the layout of the form. Can be:
	 * onecolumn - we have label and below this label the input form element.
	 * twocolumn - the leabel is at one column left and the data in another right
	 *
	 * @param string $layout 
	 */
	public function setColumnLayout ( $layout='twocolumn')
	{
		$this->view = 'table';
		if($layout && $layout == 'onecolumn') {
			$this->maxcol = 1;
		} else if($layout == 'twocolumn') {
			$this->maxcol = 2;
		} else {
			$this->view = $layout;
		}
	}
	/**
	 * Set the url from where to get data and add or update it.
	 *
	 * @param string $nurl 
	 */
	public function setUrl( $nurl ) 
	{
		if($nurl && strlen($nurl) > 0 )
		{
			$this->url = $nurl;
		}
	}
	
	/**
	 * 
	 * Set the label element in the form with certain properties
	 *
	 * @param array $aprop label prpperties
	 * @return string 
	 * 
	 */
	protected function setLabel( $aprop ) {
		$lbl = "";
		if($this->view == 'table') {
			$lbl .= "<td style=\"".$this->labelstyle."\"";
		if(isset ($aprop['labelalign']) ) {
			$lbl .= " align=\"".$aprop['labelalign']."\">";
		} else {
			$lbl .=">";
		}
		}
		$lbl .=" <label for=\"".$aprop['id']."\"";
		if( isset($aprop['labelclass'])) {
			$lbl .= " class=\"".$aprop['labelclass']."\">";
		} else {
			$lbl .=">";
		}
		if(isset($aprop['label'])) {
			$lbl .= $aprop['label'];
		}
		$lbl .= "</label>";
		if($this->view == 'table') {
		$lbl .= "</td>";
		}
		return $lbl;
	}
	/**
	 * 
	 * Set a common style for the table, labels and data input elements
	 * 
	 * @param string $table
	 * @param string $label
	 * @param string $data 
	 * 
	 */
	public function setTableStyles( $table='', $label='', $data='')
	{
		$this->tblstyle = $table;
		$this->labelstyle = $label;
		$this->datastyle = $data;
	}
	/**
	 * 
	 * Set the form properties in the elements array. 
	 *
	 * @param string $name - the name of the form
	 * @param array $aproperties properties
	 */
	protected function setFormProperties( $name, $aproperties=array() ) 
	{
		$this->aelements[0] = array("name"=>$name, "type"=>"form", "prop"=>$aproperties);
	}
	
	/**
	 * 
	 * Set the form header and properties.
	 *
	 * @param string $content content to be set in the header
	 * @param string $icon - icon name from UI Themeroller
	 * @param array $style properties in the gheader
	 * 
	 */
	public function setFormHeader($content, $icon="", $style=null)
	{
		if(!$style) {
			$style = array("style"=>"padding:6px;");
		}
		$hdr = "<div class=\"ui-state-default ui-corner-all\" ".$this->propToString($style).">";
		if($icon && strlen($icon)>0) {
			$hdr .="<span class=\"ui-icon ".$icon."\" style=\"float:left; margin:-2px 5px 0 0;\"></span>";
		}
		$hdr .= $content;
		$hdr .= "</div>";
		$this->frmheader = $hdr;
		//return $hdr;
	}
	/**
	 * 
	 * Set the form footer and properties.
	 *
	 * @param string $content content to be set in the header
	 * @param array $style properties in the gheader
	 * 
	 */
	public function setFormFooter($content, $style=null)
	{
		if(!$style) {
			$style = array("style"=>"padding:6px;");
		}
		$hdr = "<div ".$this->propToString($style).">";
		$hdr .= $content;
		$hdr .= "</div>";
		$this->frmfooter = $hdr;
		//return $hdr;
	}
	/**
	 * 
	 * Set the primary key(s) for future using in add/update.
	 *
	 * @param string $keys 
	 */
	public function setPrimaryKeys( $keys )
	{
		$this->primarykeys = explode(",", $keys);
		$this->primarykeys = array_map("trim", $this->primarykeys);
	}
	/**
	 * 
	 * Add a input element in elements array with name = $name of 
	 * type = $type and properties = $aproperties
	 *
	 * @param string $name the name of the input
	 * @param string $type the type of the input can be: 
	 * select, textarea, submit, reset, button, input
	 * @param array $aproperties  properties
	 * 
	 */
	public function addElement( $name, $type, $aproperties = array())
	{
		$this->aelements[] = array("name"=>$name, "type"=>$type, "prop"=>$aproperties);
		if(strtolower($type) == 'file') {
			$this->_files = true;
			$this->fileupload[$name] = $this->filedef;
		}
	}
	public function addHtmlElement($name,  $content='')
	{
		$this->aelements[] = array("name"=>$name, "type"=>'html', "prop"=>$content);
	}
	/**
	 * 
	 * Add a javascript event = $event with code = $code for field = $field
	 *
	 * @param string $field namre of the field
	 * @param string $event name of the event
	 * @param string $code java script code
	 * 
	 */
	public function addEvent ($field, $event, $code) 
	{
		$this->formevents[] = array("field"=>$field, "event"=>$event, "code"=>"js:".$code);
	}
	/**
	 * 
	 * Add a group in the elements array with input elements and properties
	 *
	 * @param string $name group name
	 * @param array $elemetnts array of input elements 
	 * @param array $properties of the group
	 * 
	 */
	public function addGroup( $name, $elemetnts, $properties)
	{
		$this->aelements[] = array("name"=>$name, "type"=>"group", "prop"=>$properties ,  "elem"=>$elemetnts);
	}
	/**
	 * Build the options for the select element. Both options datastring and
	 * datasql can be used. In this case the array values are used first
	 * and then sql as second 
	 *
	 * @param array $datastr array of type key=>value for the options
	 * @param type $datasql SQL wuery with minimun one field for the options
	 * @return string 
	 * 
	 */
	protected function _buildOptions( $datastr=null, $datasql='')
	{
		$str ="";
		if($datastr) {
			if(is_array($datastr)) {
				foreach($datastr as $k => $v ) 
				{
					$str .= '<option value="'.$k.'">'.$v.'</option>';
				}
			} else if(is_string($datastr) ) {
				$elements = explode(";", $datastr);
				foreach($elements as $k=>$v) {
					$elm = explode(":",$v);
					$str .= '<option value="'.$elm[0].'">'.$elm[1].'</option>';
				}
			}
		}
		if($datasql && strlen($datasql)>0 && $this->conn) {
			try {
				$query = jqGridDB::query($this->conn,$datasql);
				if($query){
					while($row = jqGridDB::fetch_num($query, $this->conn)) {
						if(count($row) == 2) {
							$str .= '<option value="'.$row[0].'">'.$row[1].'</option>';
						} else {
							$str .= '<option value="'.$row[0].'">'.$row[0].'</option>';
						}
					}
				}
			} catch(Exception $e) {
				
			}
		}
		return $str;
	}
	/**
	 * 
	 * Create a input element of type=$type name=$name and propertiies
	 * The method is used in renderForm to cretate the form elements based on the 
	 * elemets optiins array
	 *
	 * @param string $name the name of the element
	 * @param string $type the type of the input element
	 * @param array $prop valid HTML properties of the element
	 * @return string 
	 * 
	 */
	public function createElement($name, $type, $prop )
	{
		$retstr = "";
		$currStyle  = $this->getViewStyle();
		$cbr  = false;
		if(in_array($this->view, array('horizontal','vertical','inline'))) {
			$cbr = true;
		}
		if( !isset ($prop['class'])) {
			$prop['class'] = "";
		}
		switch (strtolower( $type ) )
		{
			case 'select':
				if(!isset ($prop['datalist'])) { $prop['datalist'] = ""; }
				if(!isset ($prop['datasql'])) { $prop['datasql'] = ""; }
				$prop['class'] .= $this->getViewClass( $currStyle, 'select');
				$retstr .= "<select name='".$name."' ".$this->propToString($prop, array('label','labelalign', 'labelclass', 'datalist', 'datasql')).">";
				$retstr .= $this->_buildOptions($prop['datalist'], $prop['datasql']);
				$retstr .="</select>";
				break;
			case 'textarea':
				$prop['class'] .= $this->getViewClass( $currStyle, 'textarea');
				$retstr .= "<textarea name='".$name."' ".$this->propToString($prop, array('label','labelalign', 'labelclass','type'))."/></textarea>";
				break;
			case 'button':
			case 'submit':
			case 'reset':
				$prop['type'] = $type;
				$prop['class'] .= $this->getViewClass( $currStyle, 'button');
				$retstr .= "<input ".$this->propToString($prop, array('label','labelalign', 'labelclass'))."/>";
				break;
			case 'radio':
				$prop['type'] = $type;
				$text = !isset ($prop['text']) ? "" : $prop['text'];
				if($cbr) {
					if(isset($prop['label'])) {
						$text = $prop['label'];
					}
					$retstr .= "<div class=\"".$this->getViewClass( $currStyle, 'radio')."\">\n";
					$retstr .= "<label>\n";
				} else {
					$prop['class'] .= $this->getViewClass( $currStyle, 'radio');
				}
				$retstr .= "<input name=\"".$name."\" ".$this->propToString($prop, array('label','labelalign', 'labelclass','text'))."/>".$text;
				if($cbr) {
					$retstr .= "</label>\n";					
					$retstr .= "</div>\n";
				}
				break;
			case 'checkbox' :
				$this->_checkboxon = true;
				if(!isset($prop['value'])) {
					$prop['value'] = 'on';
				}
				if(!isset($prop['offval'])) {
					$prop['offval'] = 'off';
				}
				$prop['type'] = $type;
				$text = !isset ($prop['text']) ? "" : $prop['text'];
				if($cbr) {
					if(isset($prop['label'])) {
						$text = $prop['label'];
					}
					$retstr .= "<div class=\"".$this->getViewClass( $currStyle, 'checkbox')."\">\n";
					$retstr .= "<label>\n";
				} else {
					$prop['class'] .= $this->getViewClass( $currStyle, 'checkbox');
				}
				$retstr .= "<input name=\"".$name."\" ".$this->propToString($prop, array('label','labelalign', 'labelclass','text'))."/>".$text;
				if($cbr) {
					$retstr .= "</label>\n";					
					$retstr .= "</div>\n";
				}
				break;
			default :
				$prop['type'] = $type;
				$prop['class'] .= $this->getViewClass( $currStyle, 'input');
				$retstr .= "<input ".$this->propToString($prop, array('label','labelalign', 'labelclass'))."/>";
		}
		return $retstr;
		
	}

	/**
	 * 
	 * Creates a group element with name = $name and properties = $properties, 
	 * containing input elements (array)
	 *
	 * @param string $name - the name of the group element
	 * @param array $elements - array of the elemets in the group
	 * @param type $prop properties of the group element
	 * @return string 
	 * 
	 */
	public function createGroup( $name, $elements, $prop=null)
	{
		if(!isset ($prop['style'])) { 
			$prop['style'] = "width:100%;"; 			
		} else {
			// try to find width
			if(strpos($prop['style'],"width") == FALSE ) {
				$prop['style'] .="width:100%;";
			}
		}
		if(!isset ($prop['separator']))  $prop['separator'] = " ";
		if(!isset ($prop['label']))  $prop['label'] = "";
		$gstr ="<div name='".$name."' ".$this->propToString($prop, array('label','labelalign', 'labelclass', 'separator')).">";
		$gstr .= $prop['label'].implode($prop['separator'], $elements);
		$gstr .= "</div>";
		return $gstr;
	}
	
	public function setUploadOptions( $name, $options)
	{
		if(is_array($options)) {
			$this->fileupload[$name] = jqGridUtils::array_extend($this->filedef, $options);
		}
	}
	protected function saveUploads()
	{
		$ret = true;
		if($this->_files) {
			if(!empty ($_FILES)) {
				function trv($value) {
					return trim($value);
				}
				foreach($this->fileupload as $k=>$v) {
					if((!empty($_FILES[$k])) && ($_FILES[$k]['error'] == 0)) {
						$filename = basename($_FILES[$k]['name']);
						$ext = substr($filename, strrpos($filename, '.') + 1);
						if( trim($v['filetypes']) == '' ){ 
							$ftype = false; 
							
						} else { 
							$ftype = explode(",",$v['filetypes']); 
						}
						if($ftype) {
							$ftype = array_map("trv", $ftype);
							if(!in_array($ext, $ftype)) {
								$ret = false;
								$this->errorMesage = "The file: ".$filename.". you attempted to upload is not allowed.";
								break;
							}
						}
						if(filesize($_FILES[$k]['tmp_name']) > $v['filesize']) {
							$ret = false;
							$this->errorMesage = "The file: ".$filename." you attempted to upload is too large.";
							break;
						}
						if(!is_writable($v['dir'])) {
							$ret = false;
							$this->errorMesage = "You cannot upload to the specified directory, please CHMOD it to 777.";
							break;
						}
						$newname = $v['dir'].$v['fileprefix'].$filename;
						if (!file_exists($newname)) {
							//Attempt to move the uploaded file to it's new place
							if($this->demo) {
								unlink($_FILES[$k]['tmp_name']);
								echo "File: ".$filename." succefully uploaded.";
							} else 	if ((move_uploaded_file($_FILES[$k]['tmp_name'],$newname))) {
								//echo "It's done! The file has been saved as: ".$newname;
							} else {
								$ret = false;
								$this->errorMesage = "Error: A problem occurred during file upload!";
								break;
							}
						} else {
							$ret = false;
							$this->errorMesage = "Error: File ".$_FILES[$k]["name"]." already exists";
							break;
						}					
					} else {
						if($_FILES[$k]['error'] != 4) {
							$ret = false;
							$this->errorMesage = "A problem occurred during file upload! Error Number: ".$_FILES[$k]['error'];
							break;
						}
					}
				}
			}
		}
		return $ret;
	}
	/**
	 * 
	 * Put a valid Java Script code after the form is rendered into the dom
	 *
	 * @param string $code
	 * 
	 */
    public function setJSCode($code)
    {
		$this->customCode = "js:".$code;
    }
	
	/**
	 * 
	 * Set the options for the ajax submit
	 *
	 * @param array $aoptions options for the ajax submit
	 * 
	 */
	public function setAjaxOptions( $aoptions )
	{
		if(is_array($aoptions) && count($aoptions)>0) 
		{
			$this->formoptions = jqGridUtils::array_extend($this->formoptions, $aoptions);
		}
	}
	// CRUD
	/**
	 * Build the fields array with a database fields from the table.
	 * Also we get the fields types
	 * Return false if the fields can not be build.
	 * 
	 * @return boolen
	 */
	protected function _buildFields()
	{
		$result = false;
		if($this->table) {
			//if ($this->buildfields) return true;
			$wh = ($this->dbtype == 'sqlite') ? "": " WHERE 1=2";
			$sql = "SELECT * FROM ".$this->table.$wh;
			if($this->debug) $this->logQuery($sql);
			try {
				$select =  jqGridDB::query($this->conn,$sql);
				if($select) {
					$colcount = jqGridDB::columnCount($select);
					$rev = array();
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
					$this->errorMesage = jqGridDB::errorMessage( $this->conn );
					throw new Exception($this->errorMesage);
				}

			} catch (Exception $e) {
				$result = false;
				if(!$this->errorMesage) { $this->errorMesage = $e->getMessage(); }
				if($this->showError) {
					$this->sendErrorHeader();
				} else {
					echo $this->errorMesage;
				}
			}
        }
        return $result;
	}
	/**
	 * Return the primary key(s) of the table as array
	 *
	 * @return array
	 * 
	 */
	public function  getPrimaryKeyId()
	{
		return $this->primarykeys;
	}
	
	private function outputDemoData( $sql, $data )
	{
		$str ="";
		$str .= "<div class='ui-widget ui-state-highlight' style='margin:5px 5px; padding:5px 5px;width:600px'>";
		$str .= "<div><b>Response:</b></div>";
		$str .= "<div>SQL Command:</div><div><b>".$sql."</b></div>";
		$str .= "<div>Data:</div><div><b>".implode(" ,<br/> ", $data)."</b></div>";
		$str .= "</div>";
		return $str;
	}
	/**
	 *
	 * Insert the data array into the database according to the table element.
	 * Return true on succes, false otherwiese.
	 * 
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

		$datefmt = $this->UserDate;
		$timefmt = $this->UserDateTime;
		if($this->serialKey) {
			foreach($this->primarykeys as $k => $v){
				unset($data[$v]);
			}
		}
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
			} else {
				switch ($t) {
					case 'date':
						$v = $datefmt != $this->DbDate ? jqGridUtils::parseDate($datefmt,$value,$this->DbDate) : $value;
					break;
					case 'datetime' :
						$v = $timefmt != $this->DbDateTime ? jqGridUtils::parseDate($timefmt,$value,$this->DbDateTime) : $value;
					break;
					case 'time':
						$v = $this->UserTime != $this->DbTime ? jqGridUtils::parseDate($this->UserTime,$value,$this->DbTime) : $value;
					break;
					case 'int':
						$v = (int)$value;
					default :
						$v = $value;
				}
			//if($this->decodeinput) $v = htmlspecialchars_decode($v);
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
			if($this->demo) {
				echo $this->outputDemoData($sql, $binds);
				return true;
			}
			// Prepare insert query
			$stmt =  jqGridDB::prepare($this->conn, $sql, $binds, false);
			//$this->parseSql($sql, $binds, false);
			if($stmt) {
				// Bind values to columns
				jqGridDB::bindValues($stmt, $binds, $types);
				// Execute
				if($this->trans) {
					try {
						jqGridDB::beginTransaction($this->conn);
						//$result = $this->_actionsCRUDGrid('add', 'before');
						if($this->debug) $this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primarykeys);
						$result = jqGridDB::execute($stmt, $binds, $this->conn);
						if($result) {
							$result = jqGridDB::commit($this->conn);
						}
						$ret = $result ? true : false;
						jqGridDB::closeCursor($this->dbtype == "adodb" ? $result : $stmt);
						if(!$ret) {
							$this->errorMessage = jqGridDB::errorMessage( $this->conn );
							throw new Exception($this->errorMesage);
						}
					} catch (Exception $e) {
						jqGridDB::rollBack($this->conn);
						$result = false;
						if(!$this->errorMesage) { $this->errorMesage = $e->getMessage(); }
						if($this->showError) {
							if($this->debug) { $this->debugout(); }
							$this->sendErrorHeader();
						} else {
							echo $this->errorMesage;
						}
					}
				} else {
					try {
						//$result = $this->_actionsCRUDGrid('add', 'before');
						//if($this->debug) $this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primaryKey);
						$result = jqGridDB::execute($stmt, $binds, $this->conn);
						$ret = $result ? true : false;
						jqGridDB::closeCursor($this->dbtype == "adodb" ? $result : $stmt);
						//if($result) $result = $this->_actionsCRUDGrid('add', 'after');
						if(!$ret) {
							$this->errorMessage = jqGridDB::errorMessage( $this->conn );
							throw new Exception($this->errorMesage);
						}
					} catch (Exception $e) {
						if(!$this->errorMesage) { $this->errorMesage = $e->getMessage(); }
						if($this->showError) {
							if($this->debug) { $this->debugout(); }
							$this->sendErrorHeader();
						} else {
							echo $this->errorMesage;
						}
					}
				}
			} else {
				$result = false;
			}
		} else {
			$result = false;
		}
		if($this->debug) { $this->debugout(); }
		return $result;
	}
	/**
	 *
	 * Update the data into the database according the table element
	 * A primaryKey should be set.
	 * 
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

		$datefmt = $this->UserDate;
		$timefmt = $this->UserDateTime;

		$tableFields = array_keys($this->fields);
		$rowFields = array_intersect($tableFields, array_keys($data));
		// Get "col = :col" pairs for the update query
		$updateFields = array();
		$binds = array();
		$types = array();
		$v2 = array();
		$t2 = array();
		//$pk = $this->getPrimaryKeyId();
		foreach($rowFields as $key => $field) {
			$t = $this->fields[$field]["type"];
			$value = $data[$field];
			if( strtolower($this->encoding) != 'utf-8' ) {
				$value = iconv("utf-8", $this->encoding."//TRANSLIT", $value);
			}
			if(strtolower($value) == 'null') {
				$v = NULL;
			} else {
				switch ($t) {
				case 'date':
					$v = $datefmt != $this->DbDate ? jqGridUtils::parseDate($datefmt,$value,$this->DbDate) : $value;
					break;
				case 'datetime' :
					$v = $timefmt != $this->DbDateTime ? jqGridUtils::parseDate($timefmt,$value,$this->DbDateTime) : $value;
					break;
				case 'time':
					$v = $this->UserTime != $this->DbTime ?  jqGridUtils::parseDate($this->UserTime,$value,$this->DbTime) : $value;
					break;
				default :
					$v = $value;
				}
			//if($this->decodeinput) $v = htmlspecialchars_decode($v);
			}
			if(in_array($field, $this->primarykeys ) /*$field != $pk*/ ) {				
				$v2[] = $v;
				$t2[] = $t;
			} else {
				$updateFields[] = $field . " = ?";
				$binds[] = $v;
				$types[] = $t;
			}
			unset($v);
		}
		if(count($v2)==0) { die("Primary value is missing"); }
		foreach($v2 as $kk=>$vv){
			$binds[] = $vv;
			$types[] = $t2[$kk];
		}
		$result = false;
		if(count($updateFields) > 0) {
			// build the statement
			$sql = "UPDATE " . $this->table .
				" SET " . implode(', ', $updateFields) .
				//" WHERE " . $pk . " = ?";
				" WHERE " . implode("= ? AND ", $this->primarykeys)." = ?";
			// if demo exit
			if($this->demo) {
				echo $this->outputDemoData($sql, $binds);
				return true;
			}

			// Prepare update query
			$stmt = jqGridDB::prepare($this->conn, $sql, $binds, false); /*$this->parseSql($sql, $binds, false)*/
			if($stmt) {
				// Bind values to columns
				jqGridDB::bindValues($stmt, $binds, $types);
				if($this->trans) {
					try {
						jqGridDB::beginTransaction($this->conn);
						//$result = $this->_actionsCRUDGrid('edit', 'before');
						if($this->debug) { 
							$this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primarykeys);
						}
						$result = jqGridDB::execute($stmt, $binds, $this->conn);
						$ret = $result ? true : false;
						jqGridDB::closeCursor($this->dbtype == "adodb" ? $result : $stmt);
						//if($result) {
							//$result = $this->_actionsCRUDGrid('edit', 'after');
						//}
						if( $ret )	{
							$result = jqGridDB::commit($this->conn);
						} else {
							$this->errorMessage = jqGridDB::errorMessage( $this->conn );
							throw new Exception($this->errorMesage);
						}
					} catch (Exception $e) {
						jqGridDB::rollBack($this->conn);
						$result = false;
						if(!$this->errorMesage) { $this->errorMesage = $e->getMessage(); }
						if($this->showError) {
							if($this->debug) { $this->debugout(); }
							$this->sendErrorHeader();
						} else {
							echo $this->errorMesage;
						}
					}
				} else {
					try {
						//$result = $this->_actionsCRUDGrid('edit', 'before');
						if($this->debug) {
							$this->logQuery($sql, $binds, $types, $data, $this->fields, $this->primarykeys);
						}
						$result = jqGridDB::execute($stmt, $binds, $this->conn);
						$ret = $result ? true : false;
						jqGridDB::closeCursor($this->dbtype == "adodb" ? $result : $stmt);
						//if($result) {
						//	$result = $this->_actionsCRUDGrid('edit', 'after');
						//}
						if(!$ret){
							$this->errorMessage = jqGridDB::errorMessage( $this->conn );
							throw new Exception($this->errorMesage);
						}
					} catch (Exception $e) {
						$result = false;
						if(!$this->errorMesage) { $this->errorMesage = $e->getMessage(); }
						if($this->showError) {
							if($this->debug) { $this->debugout(); }
							$this->sendErrorHeader();
						} else {
							echo $this->errorMesage;
						}
					}
				}
			}
		}
		if($this->debug) { $this->debugout(); }
		return $result;
	}

	/**
	 * 
	 * Automatically determines if the data posted from the form should add or
	 * updated. The method use inser and update method for this purpose
	 *
	 * @param array $data data to be saved into the table from the form post
	 * @return boolean 
	 * 
	 */
	public function save($data)
	{
		$keys = array();
		$types = array();
		foreach($this->primarykeys as $k => $v){
			$keys[] = $data[$v];
			$types[] = 'custom'; 
		}
		$sql = "SELECT COUNT(*) FROM ".$this->table." WHERE ".implode(" = ? AND ", $this->primarykeys)." = ? ";
		try {
			$stmt = jqGridDB::prepare($this->conn, $sql, $keys, false);
			jqGridDB::bindValues($stmt, $keys, $types);
			$result = jqGridDB::execute($stmt, $keys, $this->conn);
			if($this->dbtype == "adodb") {
				$stmt = $result;
			}
			if($result) {
				$res = jqGridDB::fetch_num($stmt, $this->conn);
				if((int)$res[0] == 1) {
					$result = $this->update($data);
				} else if( (int)$res[0] == 0 ) {
					$result = $this->insert($data);
				}
			}
			if(!$result) {
				$this->errorMessage = jqGridDB::errorMessage( $this->conn );
				throw new Exception($this->errorMesage);
			} 
			jqGridDB::closeCursor($stmt);
			if($result) {
				echo "success";
			}
		} catch (Exception $e) {
			$result = false;
			if(!$this->errorMesage) { $this->errorMesage = $e->getMessage(); }
			echo $this->errorMesage;		
		}
		return $result;
	}
	protected function SideBySideView ()
	{
		$str = "";
		$currStyle  = self::$styleshorizontal[$this->activeStyle];
		$row = $this->aelements[0]; 
		if(!isset($row['prop']['class'])) {
			$row['prop']['class'] = "";
		}
		$row['prop']['class'] .= " ".(isset($currStyle['form']) ? $currStyle['form'] : "");
		$str = "<form name=\"".$row['name']."\" ".$this->propToString($row['prop']).">";
		$str .= "<div class='ui-state-error ui-jqform-error' style='display:none'></div>";
		$acnt = count($this->aelements);
		$this->idprefix = $row['name']."_";
		if($this->frmheader) {
			$str .= $this->frmheader;
		}
		for($i = 1; $i < $acnt ; $i++) 
	{
			$row = $this->aelements[$i];
			if($row['type'] == 'html') {
				$str .= $row['prop'];
				continue;
			}
			if( !isset ($row['prop']['id'])) {
				$row['prop']['id'] = $this->idprefix.$row['name'];
			}
					
			$row['prop']['name'] = $row['name'];
			
			if($row['type'] == 'group') {
				if(!isset($row['prop']['class'])) {
					$row['prop']['class'] = "";
				}
				$row['prop']['class'] .= " ".(isset($currStyle['groupcontrol']) ? $currStyle['groupcontrol'] : "");
				
				$str .= "<div class=\"".(isset($currStyle['group']) ? $currStyle['group'] : "")."\">";
				$str .= $this->createGroup($row['name'], $row['elem'], $row['prop']);
				$str .= "</div>";
				continue;
			}
			
			$trs = $row['type'] == 'hidden' ? " style='display:none;'" : "";
			$trs .= " class=\"".(isset($currStyle['group']) ? $currStyle['group'] : "")."\"";
			//formgroup
			$str .="<div".$trs.">";
			if($row['type'] == 'checkbox' || $row['type'] == 'radio' ){
				$gs = (isset($currStyle['groupcontrol']) ? $currStyle['groupcontrol'] : "");
			} else {
				$lp = $row['prop'];
				if(isset($lp['labelclass'])) {
					$lp['labelclass'] .= " ". (isset($currStyle['label']) ? $currStyle['label'] : "");
				} else {
					$lp['labelclass'] = (isset($currStyle['label']) ? $currStyle['label'] : "");
				}
				$str .= $this->setLabel($lp);
				$gs = (isset($currStyle['control']) ? $currStyle['control'] : "");
			}
			$str .= "<div class=\"".$gs."\">";
			$str .= $this->createElement($row['name'], $row['type'], $row['prop']);
			$str .= "</div>";
			// endformgroup
			$str .="</div>";
		}
		if($this->frmfooter) {
			$str .= $this->frmfooter;
		}
		$str .= "</form>";
		return $str;
	}
	protected function InlineView ()
	{
		$str = "";
		$currStyle  = self::$stylesinline[$this->activeStyle];
			$row = $this->aelements[0];
		if(!isset($row['prop']['class'])) {
			$row['prop']['class'] = "";
		}
		$row['prop']['class'] .= " ".(isset($currStyle['form']) ? $currStyle['form'] : "");
			$str = "<form name=\"".$row['name']."\" ".$this->propToString($row['prop']).">";
		$str .= "<div class='ui-state-error ui-jqform-error' style='display:none'></div>";
		$acnt = count($this->aelements);
		$this->idprefix = $row['name']."_";
		if($this->frmheader) {
			$str .= $this->frmheader;
		}
		for($i = 1; $i < $acnt ; $i++) 
		{
			$row = $this->aelements[$i];
			if($row['type'] == 'html') {
				$str .= $row['prop'];
				continue;
			}
			if( !isset ($row['prop']['id'])) {
				$row['prop']['id'] = $this->idprefix.$row['name'];
			}
					
			$row['prop']['name'] = $row['name'];
			
			if($row['type'] == 'group') {
				if(!isset($row['prop']['class'])) {
					$row['prop']['class'] = "";
				}
				$row['prop']['class'] .= " ".(isset($currStyle['groupcontrol']) ? $currStyle['groupcontrol'] : "");
				
				//$str .= "<div class=\"".(isset($currStyle['group']) ? $currStyle['group'] : "")."\">";
				$str .= $this->createGroup($row['name'], $row['elem'], $row['prop']);
				//$str .= "</div>";
				continue;
			}
			
			$trs = $row['type'] == 'hidden' ? " style='display:none;'" : "";
			$trs .= " class=\"".(isset($currStyle['group']) ? $currStyle['group'] : "")."\"";
			//formgroup
			$str .="<div".$trs.">";
			if($row['type'] == 'checkbox' || $row['type'] == 'radio' ){
				$gs = (isset($currStyle['groupcontrol']) ? $currStyle['groupcontrol'] : "");
			} else {
				$lp = $row['prop'];
				if(isset($lp['labelclass'])) {
					$lp['labelclass'] .= " ". (isset($currStyle['label']) ? $currStyle['label'] : "");
				} else {
					$lp['labelclass'] = (isset($currStyle['label']) ? $currStyle['label'] : "");
				}
				$str .= $this->setLabel($lp);
				$gs = (isset($currStyle['control']) ? $currStyle['control'] : "");
			}
			//$str .= "<div class=\"".$gs."\">";
			$str .= $this->createElement($row['name'], $row['type'], $row['prop']);
			//$str .= "</div>";
			// endformgroup
			$str .="</div>";
		}
		if($this->frmfooter) {
			$str .= $this->frmfooter;
		}
		$str .= "</form>";
		return $str;
	}
	protected function VerticalView ()
	{
		$str = "";
		$currStyle  = self::$stylesvertical[$this->activeStyle];
		$row = $this->aelements[0]; 
		if(!isset($row['prop']['class'])) {
			$row['prop']['class'] = "";
		}
		$row['prop']['class'] .= " ".(isset($currStyle['form']) ? $currStyle['form'] : "");
		$str = "<form name=\"".$row['name']."\" ".$this->propToString($row['prop']).">";
		$str .= "<div class='ui-state-error ui-jqform-error' style='display:none'></div>";
		$acnt = count($this->aelements);
		$this->idprefix = $row['name']."_";
		if($this->frmheader) {
			$str .= $this->frmheader;
		}
		for($i = 1; $i < $acnt ; $i++) 
		{
			$row = $this->aelements[$i];
			if($row['type'] == 'html') {
				$str .= $row['prop'];
				continue;
			}
			if( !isset ($row['prop']['id'])) {
				$row['prop']['id'] = $this->idprefix.$row['name'];
			}
					
			$row['prop']['name'] = $row['name'];
			
			if($row['type'] == 'group') {
				if(!isset($row['prop']['class'])) {
					$row['prop']['class'] = "";
				}
				$row['prop']['class'] .= " ".(isset($currStyle['groupcontrol']) ? $currStyle['groupcontrol'] : "");
				
				//$str .= "<div class=\"".(isset($currStyle['group']) ? $currStyle['group'] : "")."\">";
				$str .= $this->createGroup($row['name'], $row['elem'], $row['prop']);
				//$str .= "</div>";
				continue;
			}
			
			$trs = $row['type'] == 'hidden' ? " style='display:none;'" : "";
			$trs .= " class=\"".(isset($currStyle['group']) ? $currStyle['group'] : "")."\"";
			//formgroup
			$str .="<div".$trs.">";
			if($row['type'] == 'checkbox' || $row['type'] == 'radio' ){
				$gs = (isset($currStyle['groupcontrol']) ? $currStyle['groupcontrol'] : "");
			} else {
				$lp = $row['prop'];
				if(isset($lp['labelclass'])) {
					$lp['labelclass'] .= " ". (isset($currStyle['label']) ? $currStyle['label'] : "");
				} else {
					$lp['labelclass'] = (isset($currStyle['label']) ? $currStyle['label'] : "");
				}
				$str .= $this->setLabel($lp);
				$gs = (isset($currStyle['control']) ? $currStyle['control'] : "");
			}
			//$str .= "<div class=\"".$gs."\">";
			$str .= $this->createElement($row['name'], $row['type'], $row['prop']);
			//$str .= "</div>";
			// endformgroup
			$str .="</div>";
		}
		if($this->frmfooter) {
			$str .= $this->frmfooter;
		}
		$str .= "</form>";
		return $str;
	}
	protected function TableView ()
	{
		$currStyle  = self::$stylestable[$this->activeStyle];
		$row = $this->aelements[0];
		if(!isset($row['prop']['class'])) {
			$row['prop']['class'] = "";
		}
		$row['prop']['class'] .= " ".(isset($currStyle['form']) ? $currStyle['form'] : "");

		$str = "<form name=\"".$row['name']."\" ".$this->propToString($row['prop']).">";
		$str .= "<div class='ui-state-error ui-jqform-error' style='display:none;'></div>";
			$id = $row['prop']['id'];
			$acnt = count($this->aelements);
			$this->idprefix = $row['name']."_";
			$str .="<table style=\"".$this->tblstyle."\">";
			if($this->frmheader) {
				$str .= "<thead><tr><th align='left' colspan=\"".$this->maxcol."\">";
				$str .= $this->frmheader;
				$str .= "</th></tr></thead>";
			}
			if($this->frmfooter) {
				$str .= "<tfoot><tr><td colspan=\"".$this->maxcol."\">";
				$str .= $this->frmfooter;
				$str .= "</td></tr></tfoot>";
			}
			$str .= "<tbody>";
			for($i = 1; $i < $acnt ; $i++) 
			{
				$row = $this->aelements[$i];
				if( !isset ($row['prop']['id'])) {
					$row['prop']['id'] = $this->idprefix.$row['name'];
				}
						
				$row['prop']['name'] = $row['name'];
				$trs = $row['type'] == 'hidden' ? " style='display:none;'" : "";
				$str .="<tr".$trs.">";
				if($row['type'] == 'group') {
					$str .= "<td colspan=\"".$this->maxcol."\">";
					$str .= $this->createGroup($row['name'], $row['elem'], $row['prop']);
					$str .= "</td></tr>";
					continue;
				}
				$str .= $this->setLabel($row['prop']);
				if($this->maxcol == 1) {
					$str .= "</tr><tr>";
				}
				$str .= "<td style=\"".$this->datastyle."\">";
				$str .= $this->createElement($row['name'], $row['type'], $row['prop']);
				$str .= "</td>";
				$str .="</tr>";
			}
			$str .= "</tbody></table>";
			$str .= "</form>";
		return $str;
	}
	
	protected function NostyleView ()
	{
		$str = "";
		$row = $this->aelements[0]; 
		if(!isset($row['prop']['class'])) {
			$row['prop']['class'] = "";
		}
		$str = "<form name=\"".$row['name']."\" ".$this->propToString($row['prop']).">";
		$str .= "<div class='ui-state-error ui-jqform-error' style='display:none'></div>";
		$acnt = count($this->aelements);
		$this->idprefix = $row['name']."_";
		if($this->frmheader) {
			$str .= $this->frmheader;
		}
		for($i = 1; $i < $acnt ; $i++) 
		{
			$row = $this->aelements[$i];
			if($row['type'] == 'html') {
				$str .= $row['prop'];
				continue;
			}
			if( !isset ($row['prop']['id'])) {
				$row['prop']['id'] = $this->idprefix.$row['name'];
			}
					
			$row['prop']['name'] = $row['name'];
			
			if($row['type'] == 'group') {
				if(!isset($row['prop']['class'])) {
					$row['prop']['class'] = "";
				}
				$str .= $this->createGroup($row['name'], $row['elem'], $row['prop']);
				continue;
			}
			
			$trs = $row['type'] == 'hidden' ? " style='display:none;'" : "";
			$str .="<div".$trs.">";
			$lp = $row['prop'];
			if(isset($lp['labelclass'])) {
				$lp['labelclass'] .= " ". (isset($currStyle['label']) ? $currStyle['label'] : "");
			} else {
				$lp['labelclass'] = (isset($currStyle['label']) ? $currStyle['label'] : "");
			}
			$str .= $this->setLabel($lp);
			$str .= $this->createElement($row['name'], $row['type'], $row['prop']);
			$str .="</div>";
		}
		if($this->frmfooter) {
			$str .= $this->frmfooter;
		}
		$str .= "</form>";
		return $str;

	}
	
	protected function _setView() {
		switch ($this->view) {
			case 'horizontal' :
				return $this->SideBySideView();
				//break;
			case 'vertical' :
				return $this->VerticalView();
				//break;
			case 'inline' :
				return $this->InlineView();
				//break;
			case 'table' :
				return $this->TableView();
			case 'nostyle' :
				return $this->NostyleView();
		}

	}
	/**
	 *
	 * The main method which should be called at end whenall other data for the 
	 * form is prepared. The pethod perform constructiong of the form,
	 * obtaining data and save the data posted from the client.
	 * 
	 * @param array $params parameters for the select coomand
	 * @return string 
	 * 
	 */
	public function renderForm( array $params=null)
	{
		$str ="";
		if( $this->oper == "no" ) {
			$row = $this->aelements[0];
			$id = $row['prop']['id'];
			$str = $this->_setView();
			if($this->demo) {
				$str .= '<div id="demo"></div>';
				if(isset ($this->formoptions['success']) && strlen($this->formoptions['success'])>0) {
					
				}  else {
					$this->formoptions['success'] = "js:function(response){ jQuery('#demo').empty().html(response).show('fast', 'swing', setTimeout(function(){ jQuery('#demo').fadeOut(); },2500) ) ;}";
				}
			}
			$cnt = count($this->formevents);
			$str .= "<script type='text/javascript'>";
			$str .= "jQuery(document).ready(function($) {";
			if($cnt > 0 ) {
				foreach($this->formevents as $k =>$v)
				{
					$str .= "jQuery('#".$v["field"]."').bind('".$v["event"]."',".jqGridUtils::encode($v["code"]).");";
				}
			}
			//$nm = $row['name'];
			$url = $this->url;
			if(!$url) {
				$url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
			}
			$aget = jqGridUtils::Strip($_GET);
			$apost = jqGridUtils::Strip($_POST);
			$aget = jqGridUtils::array_extend($aget, $apost);
			$aget['jqform'] = 'get';
			$ajson = json_encode($aget);
$func = <<<AJAXFUNC
jQuery.ajax({
	url:"$url",
	data: $ajson,
	type: 'POST',
	dataType: 'json',
	success : function(req, err) {
		$.each( req, function(i, n){ 
			var inel = $('[name="'+i+'"]',"#$id");
			try {
				if(inel.is(":checkbox")) {
					if(n == inel.val() ) {
						inel[!!jQuery.fn.prop ? 'prop' : 'attr']("checked",true);
					}
				
				} else {
					inel.val( n );
				}
			} catch (error) {}
		});
	}
});
AJAXFUNC;
			if($this->SelectCommand && strlen($this->SelectCommand)>0) {
				$str .= $func;
			}
			$this->formoptions['url'] = $url;
			if(!isset ($this->formoptions['data'])) {
				$this->formoptions['data'] = array();
			}
			if(true) {

				if(!isset($this->formoptions['beforeSerialize'])) {
$bfs = <<<BFS
function (form, data)
{
	var ts = this;
	jQuery("input:checkbox", form).each(function(){
		if( !jQuery(this).is(":checked") ) {
			ts.data[this.name] = jQuery(this).attr("offval");
		}
	});
	$(".ui-jqform-error",form).hide();
}\n
BFS;
					$this->formoptions['beforeSerialize'] = "js:".$bfs;
				} else {
$bfs = <<<BFS
var ts = this;
jQuery("input:checkbox", form).each(function(){
	if( !jQuery(this).is(":checked") ) {
		ts.data[this.name] = jQuery(this).attr("offval");
	}
});
$(".ui-jqform-error",form).hide();
BFS;
					$rpos= strrpos($this->formoptions['beforeSerialize'], '}');
					$this->formoptions['beforeSerialize'] = substr_replace($this->formoptions['beforeSerialize'],$bfs."}", $rpos);
				}
			}
			if(!isset($this->formoptions['error'])) {
$err = <<<ERR
function(response) {
		console.log(response);
	$(".ui-jqform-error","#$id").html( response.responseText.replace(/\\n/g, '<br/>')).show();	
}
ERR;
			$this->formoptions['error'] = "js:".$err;
			} else {
$err = <<<ERR
		console.log(response);
	$(".ui-jqform-error","#$id").html( response.responseText.replace(/\\n/g, '<br/>') ).show();	
ERR;
				$rpos= strrpos($this->formoptions['error'], '}');
				$this->formoptions['error'] = substr_replace($this->formoptions['error'],$err."}", $rpos);
			}
			$this->formoptions['data']["jqform"] = "save"; 
			$str .= "jQuery('#".$id."').submit( function(){jQuery(this).ajaxSubmit(".jqGridUtils::encode($this->formoptions)."); return false;});";
			if($this->customCode && strlen($this->customCode) > 0 ) {
				$str .= jqGridUtils::encode($this->customCode);
			}
			$str .= "jQuery('button, input:submit, input:reset','#".$id."').hover(function(){jQuery(this).addClass('ui-state-hover');},function(){ jQuery(this).removeClass('ui-state-hover');});";
			$str .= "});";
			$str .= "</script>";
			return $str;
		} else if($this->oper == 'get' && $this->conn) {
			$sqlstr = jqGridDB::limit($this->SelectCommand, $this->dbtype, 1, 0);
			if($this->dbtype != "adodb") {
				$stmt = jqGridDB::prepare($this->conn, $sqlstr, $params);
				$ret = jqGridDB::execute($stmt, $params, $this->conn);
			} else {
				$stmt = $sqlstr;
			}
			$result = jqGridDB::fetch_object($stmt, false, $this->conn);
			jqGridDB::closeCursor($stmt);
			//$sqlstr = null;
			return json_encode($result);
		} else if($this->oper == 'save' && $this->conn) {
			$ret = true;
			if( $this->_files ) {
				$ret = $this->saveUploads();
			}
			if($ret) {
				$sdata = jqGridUtils::Strip($_POST);
				$this->save($sdata);
			} else {
				echo $this->errorMesage;
			}
		}		
	}
}
