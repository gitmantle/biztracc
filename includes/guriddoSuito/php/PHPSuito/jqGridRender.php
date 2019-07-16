<?php
/**
 *
 * @author  Tony Tomov, (tony@trirand.com)
 * @copyright TriRand Ltd
 * @package jqGrid
 * @version 5.3.1
 * @abstract
 * This is a top level class which do almost evething with the grid without to write
 * Java Script code.
 */

/** PHPSuito root directory */
if (!defined('PHPSUITO_ROOT')) {
    define('PHPSUITO_ROOT', dirname(__FILE__) . '/');
    require(PHPSUITO_ROOT . 'Autoloader.php');
}


class jqGridRender extends jqGridEdit
{
	/**
	 * Default grid parameters
	 * @var array
	 */
	protected $gridOptions = array(
		//"width"=>"650",
		"hoverrows"=>false,
		"viewrecords"=>true,
		"jsonReader"=>array("repeatitems"=>false, "subgrid"=>array("repeatitems"=>false)),
		"xmlReader"=>array("repeatitems"=>false, "subgrid"=>array("repeatitems"=>false)),
		"gridview"=>true
	);
	/**
	 * Enable/disable navigator in the grid. Default false
	 * @var boolean
	 */
	public $navigator = false;
	/**
	 * Enable/disable the export to excel
	 * @var boolean
	 */
	public $export = true;
	/**
	 *
	 * @var type boolean
	 * Enables export of the current hidden fields in colModel set with JavaScritp
	 * 
	 */
	public $currentHidden = false;
	/**
	 * Enable/disable tollbar search. Default false
	 * @var boolean
	 */
	public $toolbarfilter = false;
	/**
	 *
	 * @var type boolean
	 * If set to true add the inline navigator buttons for inline editing
	 */
	public $inlineNav = false;
	/**
	 * If set to true put the form edit options as grid option so they can be used from other places
	 * @var boolean
	 */
	public $sharedEditOptions = false;
	/**
	 * If set to true put the form add options as grid option so they can be used from other places
	 * @var boolean
	 */
	public $sharedAddOptions = false;
	/**
	 * If set to true put the form delete options as grid option so they can be used from other places
	 * @var boolean
	 */
	public $sharedDelOptions = false;
	/**
	 * Default navigaror options
	 * @var array
	 */
	protected $navOptions = array("edit"=>true,"add"=>true,"del"=>true,"search"=>true,"refresh"=>true, "view"=>false, "excel"=>true, "pdf"=>false, "csv"=>false, "columns"=>false);
	/**
	 * Default editing form dialog options
	 * @var array
	 */
	protected $editOptions = array("drag"=>true,"resize"=>true,"closeOnEscape"=>true, "dataheight"=>150, "errorTextFormat"=>"js:function(r){ return r.responseText;}");
	/**
	 * Default add form dialog options
	 * @var array
	 */
	protected $addOptions = array("drag"=>true,"resize"=>true,"closeOnEscape"=>true, "dataheight"=>150, "errorTextFormat"=>"js:function(r){ return r.responseText;}");
	/**
	 * Default view form dialog options
	 * @var array
	 */
	protected $viewOptions = array("drag"=>true,"resize"=>true,"closeOnEscape"=>true, "dataheight"=>150);
	/**
	 * Default delete form dialog options
	 * @var array default
	 */
	protected $delOptions = array("errorTextFormat"=>"js:function(r){ return r.responseText;}");
	/**
	 * Default search options
	 * @var array
	 */
	protected $searchOptions = array("drag"=>true, "closeAfterSearch"=>true, "multipleSearch"=>true);
	/**
	 * Default fileter toolbar search options
	 * @var array
	 */
	protected $filterOptions = array("stringResult"=>true);
	/**
	 *
	 * Holds the colModel for the grid. Can be passed as param or created
	 * automatically
	 * @var array
	 */
	protected $colModel = array();
	/**
	 *
	 * When set to false some set comands are not executed for spped improvements
	 * Usual this is done after setColModel.
	*var boolen
	 */
	protected $runSetCommands = true;
	/**
	 * Holds the grid methods.
	 * @var array
	 */
	protected $gridMethods = array();
	/**
	 * Custom java script code which is set after creation of the grid
	 * @var string
	 */
	protected $customCode = "";

	/**
	 *  Holds the button options when perform export to excel, pdf, csv ....
	 *  $var array
	 */
	protected $expoptions = array(
		"excel" => array("caption"=>"", "title"=>"Export To Excel", "buttonicon"=>"ui-icon-newwin"),
		"pdf" => array("caption"=>"", "title"=>"Export To Pdf", "buttonicon"=>"ui-icon-print"),
		"csv" => array("caption"=>"", "title"=>"Export To CSV", "buttonicon"=>"ui-icon-document"),
		"columns"=>array("caption"=>"", "title"=>"Visible Columns", "buttonicon"=>"ui-icon-calculator", "options"=>array())
	);
	/**
	 *
	 * @var type array - holds the parameters and events for inline editiong
	 */
	protected $inlineNavOpt = array("addParams"=>array(), "editParams"=>array());
	/**
	 * Return the generated colModel
	 * @return array
	 */
	public function getColModel()
	{
		return $this->colModel;
	}

	/**
	 *
	 * Return a jqGrid option specified by the key, false if the option can not be found.
	 * @param string $key the named grid option
	 * @return mixed
	 */
	public function getGridOption($key)
	{
		$ret = false;
		if(array_key_exists($key, $this->gridOptions)) { 
			$ret =  $this->gridOptions[$key];
		}
		return $ret;
	}

	/**
	 *
	 * Set a grid option. The method uses array with keys corresponding
	 * to the jqGrid options as described in jqGrid docs
	 * @param array $aoptions A key name pair. Some options can be array to.
	 */
	public function setGridOptions($aoptions)
	{
		//if($this->runSetCommands) {
			if(is_array($aoptions)) {
				$this->gridOptions = jqGridUtils::array_extend($this->gridOptions,$aoptions);
			}
		//}
	}

	/**
	 *
	* Set a editing url. Note that this set a url from where to obtain and/or edit
	 * data.
	 * Return false if runSetCommands is already runned (false)
	 * @param string $newurl the new url
	 * @return boolean
	 */
	public function setUrl($newurl)
	{
		if(!$this->runSetCommands) { return false; }
		if(strlen($newurl) > 0)
		{
			$this->setGridOptions(array("url"=>$newurl,"editurl"=>$newurl, "cellurl"=>$newurl));
			return true;
		}
		return false;
	}

	/**
	 *
	 * Prepares a executuion of a simple subgrid
	 * Return false if no name options for the subgrid.
	 * @param string $suburl Url from where to get the data
	 * @param array $subnames Required - the names that should correspond to fields of the data
	 * @param array $subwidth (optional) - sets a width of the subgrid columns. Default 100
	 * @param array $subalign (optional) - set the aligmend of the columns. default center
	 * @param array $subparams (optional) additional parameters that can be passed when the subgrid
	 * plus icon is clicked. The names should be present in colModel in order to pass the values
	 * @return boolean
	 */
	public function setSubGrid ($suburl='', $subnames=false, $subwidth=false, $subalign=false, $subparams=false, $mapping=false)
	{
		if(!$this->runSetCommands) { return false; }
		if($subnames && is_array($subnames)) {
			$scount = count($subnames);
			for($i=0;$i<$scount;$i++) {
				if(!isset($subwidth[$i])) { $subwidth[$i] = 100; }
				if(!isset($subalign[$i])) { $subalign[$i] = 'center'; }
			}
			$this->setGridOptions(array("gridview"=>false,"subGrid"=>true,"subGridUrl"=>$suburl,"subGridModel"=>array(array("name"=>$subnames,"width"=>$subwidth,"align"=>$subalign,"params"=>$subparams, "mapping"=>$mapping))));
			return true;
		}
		return false;
	}

	/**
	 *
	 * Prepares a subgrid in the grid expecting any valid html content provieded
	 * via the $suggridurl
	 * @param string $subgridurl url from where to get html content
	 * @return boolean
	 * @param array $subgridnames a array with names from colModel which
	 * values will be posted to the server
	 */
	public function setSubGridGrid($subgridurl, $subgridnames=null)
	{
		if(!$this->runSetCommands) { return false; }
		$this->setGridOptions(array("subGrid"=>true,"gridview"=>false));
		$setval = (is_array($subgridnames) && count($subgridnames)>0 ) ? 'true' : 'false';
		if($setval=='true') {
			$anames  = implode(",", $subgridnames);
		} else {
			$anames = '';
		}
$subgr = <<<SUBGRID
function(subgridid,id)
{
	var data = {subgrid:subgridid, rowid:id};
	if('$setval' == 'true') {
		var anm= '$anames';
		anm = anm.split(",");
		var rd = jQuery(this).jqGrid('getRowData', id);
		if(rd) {
			for(var i=0; i<anm.length; i++) {
				if(rd[anm[i]]) {
					data[anm[i]] = rd[anm[i]];
				}
			}
		}
	}
	data = $.isFunction(this.p.serializeSubGridData)? this.p.serializeSubGridData.call(this, data) : data;
    $("#"+jQuery.jgrid.jqID(subgridid)).load('$subgridurl',data);
}
SUBGRID;
		$this->setGridEvent('subGridRowExpanded', $subgr);
		return true;
	}

	/**
	 *
	 * Construct the select used in the grid. The select element can be used in the
	 * editing modules, in formatter or in search module
	 * @param string $colname (requiered) valid colname in colmodel
	 * @param mixed $data can be array (with pair key value) or string which is
	 * the SQL command which is executed to obtain the values. The command should contain a
	 * minimun two fields. The first is the key and the second is the value whch will
	 * be displayed in the select
	 * @param boolean $formatter deternines that the select should be used in the
	 * formatter of type select. Default is true
	 * @param boolean $editing determines if the select should be used in editing
	 * modules. Deafult is true
	 * @param boolean $seraching determines if the select should be present in
	 * the search module. Deafult is true.
	 * @param array $defvals Set the default value if none is selected. Typically this
	 * is usefull in serch modules. Can be something like arrar(""=>"All");
	 * @return boolean
	 */
	public function setSelect($colname, $data, $formatter=true, $editing=true, $seraching=true, $defvals=array(), $sep = ":", $delim=";" )
	{
		$s1 = "";
		//array();
		//new stdClass();
		$prop = array();
		//$oper = $this->GridParams["oper"];
		$goper = $this->oper ? $this->oper : 'nooper';
		if(($goper == 'nooper' || $goper == $this->GridParams["excel"] || $goper == "pdf" || $goper=="csv")) 
		{ 
			$runme = true;
		} else {
			$runme = !in_array($goper, array_values($this->GridParams));
		}
		if(!$this->runSetCommands && !$runme) { return false; }

		if(count($this->colModel) > 0 && $runme)
		{
			if(is_string($data)) {
				$aset = jqGridDB::query($this->pdo,$data);
				if($aset) {
					$i = 0;
					while($row = jqGridDB::fetch_num($aset, $this->pdo))
					{
						if($i == 0) {
							$s1 .= $row[0].$sep.$row[1];
						} else {
							$s1 .= $delim.$row[0].$sep.$row[1];
						}
						$i++;
					}
				}
				jqGridDB::closeCursor($aset);
			} else if(is_array($data) ) {
				$i=0;
				foreach($data as $k=>$v)
				{
					if($i == 0) {
						$s1 .= $k.$sep.$v;
					} else {
						$s1 .= $delim.$k.$sep.$v;
					}
					$i++;
				}
				//$s1 = $data;
			}
			if(is_array($defvals) && count($defvals)>0) {
				//$s1 = $defvals+$s1;
				foreach($defvals as $k=>$v) {
					$s1 = $k.$sep.$v.$delim.$s1;
				}
			}
			if($editing)  {
				$prop = array_merge( $prop,array('edittype'=>'select','editoptions'=>array('value'=>$s1, 'separator'=>$sep, 'delimiter'=>$delim)) );
			}
			if($formatter)
			{
				$prop = array_merge( $prop,array('formatter'=>'select','formatoptions'=>array('value'=>$s1, 'separator'=>$sep, 'delimiter'=>$delim)) );
			}
			if($seraching) {
				$prop = array_merge( $prop,array("stype"=>"select","searchoptions"=>array("value"=>$s1, 'separator'=>$sep, 'delimiter'=>$delim)) );
			}
			if(count($prop)>0){
				$this->setColProperty($colname, $prop);
			}
			return true;
		}
		return false;
	}
	/**
	 * Construct autocompleter used in the grid. The autocomplete can be used in the
	 * editing modules or/and in search module.
	 * @uses jqAutocomplete class. This requiere to include jqAutocomplete.php in order
	 * to work
	 * @param string $colname (requiered) valid colname in colModel
	 * @param string $target if set determines the input element on which the
	 * value will be set after the selection in the autocompleter
	 * @param mixed $data can be array or string which is
	 * the SQL command which is executed to obtain the values. The command can contain
	 * one, two or three fields.
	 * If the field in SQL command is one, then this this field will be displayed
	 * and setted as value in the element.
	 * If the fields in SQL command are two,  then the second field will be displayed
	 * but the first will be setted as value in the element.
	 * @param array $options - array with options for the autocomplete. Can be
	 * all available options from jQuery UI autocomplete in pair name=>value.
	 * In case of events a "js:" tag should be added before the value.
	 * Additionally to this the following options can be used - cache, searchType,
	 * ajaxtype, itemLiength. For more info refer to docs.
	 * @param boolean $editing determines if the autocomplete should be used in editing
	 * modules. Deafult is true
	 * @param boolean $seraching determines if the autocomplete should be present in
	 * the search module. Deafult is true.
	 */
	public function setAutocomplete($colname, $target=false, $data='', $options=null, $editing = true, $searching=false)
	{
		try {
			$ac = new jqAutocomplete($this->pdo, $this->odbc);
			$ac->encoding = $this->encoding;
			if(is_string($data)) {
				$ac->SelectCommand = $data;
				$url = $this->getGridOption('url');
				if(!$url) {
					$url = basename(__FILE__);
				}
				$ac->setSource($url);
			} else if(is_array($data)) {
				$ac->setSource($data);
			}
			if($colname) {
				if($ac->isNotACQuery()) {
					// options to remove
					//cache, searchType,loadAll, ajaxtype, scroll, height, itemLength
					if(is_array($options) && count($options)>0 ) {
						if(isset($options['cache'])) {
							$ac->cache= $options['cache'];
							unset($options['cache']);
						}
						if(isset($options['searchType'])) {
							$ac->searchType= $options['searchType'];
							unset($options['searchType']);
						}
						if(isset($options['ajaxtype'])) {
							$ac->ajaxtype= $options['ajaxtype'];
							unset($options['ajaxtype']);
						}
						if(isset($options['scroll'])) {
							$ac->scroll= $options['scroll'];
							unset($options['scroll']);
						}
						if(isset($options['height'])) {
							$ac->height= $options['height'];
							unset($options['height']);
						}
						if(isset($options['itemLength'])) {
							$ac->setLength($options['itemLength']);
							unset($options['itemLength']);
						}
						if(isset($options['fontsize']) ) {
							$ac->fontsize = $options['fontsize'];
							unset($options['fontsize']);
						}
						if(isset($options['zIndex']) ) {
							$ac->zIndex = $options['zIndex'];
							unset($options['zIndex']);
						}
						if(isset($options['strictcheck']) ) {
							$ac->strictcheck = $options['strictcheck'];
							unset($options['strictcheck']);
						}
						$ac->setOption($options);
					}
					if($editing) {
						$script = $ac->renderAutocomplete($colname, $target, false, false);
						$script = str_replace("jQuery('".$colname."')", "jQuery(el)", $script);
						$script = "setTimeout(function(){".$script."},200);";
						$this->setColProperty($colname,array("editoptions"=>array("dataInit"=>"js:function(el){".$script."}")));
					}
					if($searching) {
						$ac->setOption('select', "js:function(e,u){ $(e.target).trigger('change');}");
						$script = $ac->renderAutocomplete($colname, false, false, false);
						$script = str_replace("jQuery('".$colname."')", "jQuery(el)", $script);
						$script = "setTimeout(function(){".$script."},100);";
						$this->setColProperty($colname,array("searchoptions"=>array("dataInit"=>"js:function(el){".$script."}")));
					}
				} else {
					if(isset($options['searchType'])) {
						$ac->searchType= $options['searchType'];
					}
					if(isset($options['itemLength'])) {
						$ac->setLength($options['itemLength']);
					}
					$ac->renderAutocomplete($colname, $target, true, true, false);
				}
			}
		} catch (Exception $e) {
			$e->getMessage();
		}
	}
	/**
	 *
	 * Construct a pop up calender used in the grid. The datepicker can be used in the
	 * editing modules or/and in search module.
	 * @uses jqCalender class. This requiere to include jqCalender.php in order
	 * to work
	 * @param string $colname (requiered) valid colname in colModel
	 * @param array $options - array with options for the datepicker. Can be
	 * all available options from jQuery UI datepicker in pair name=>value.
	 * In case of events a "js:" tag should be added before the value.
	 * @param boolean $editing determines if the datepicker should be used in editing
	 * modules. Deafult is true
	 * @param boolean $seraching determines if the datepicker should be present in
	 * the search module. Deafult is true.
	 */
	public function setDatepicker($colname, $options=null, $editing=true, $searching=true)
	{
		try {
			if($colname){
				if($this->runSetCommands) {
					$dp = new jqCalendar();
					if(isset($options['buttonIcon']) ) {
						$dp->buttonIcon = $options['buttonIcon'];
						unset($options['buttonIcon']);
					}
					if(isset($options['buttonOnly']) ) {
						$dp->buttonOnly = $options['buttonOnly'];
						unset($options['buttonOnly']);
					}
					if(isset($options['fontsize']) ) {
						$dp->fontsize = $options['fontsize'];
						unset($options['fontsize']);
					}
					if(is_array($options) && count($options) > 0 ) {
						$dp->setOption($options);
					}
					if(!isset ($options['dateFormat'])) {
						$ud = $this->getUserDate();
						$ud = jqGridUtils::phpTojsDate($ud);
						$dp->setOption('dateFormat', $ud);
					}
					$script = $dp->renderCalendar($colname, false, false);
					$script = str_replace("jQuery('".$colname."')", "jQuery(el)", $script);
					$script = "setTimeout(function(){".$script."},100);";
					if($editing) {
						$this->setColProperty($colname,array("editoptions"=>array("dataInit"=>"js:function(el){".$script."}")));
					}
					if($searching) {
						$this->setColProperty($colname,array("searchoptions"=>array("dataInit"=>"js:function(el){".$script."}")));
					}
				}
			}
		} catch (Exception $e) {
			$e->getMessage();
		}
	}
	public function validationFromModel($validations=array(), $sanitations = array())
	{
		foreach($this->colModel as $key => $rule) {
			if(isset($rule['editrules'])) {
				$this->validations[$rule['name']]= $rule['editrules'];
			}
		}
		if(is_array($validations) && count($validations)>0) {
			$this->validations = array_merge($this->validations, $validations);
		}
		if(is_array($sanitations) && count($sanitations)>0) {
			$this->sanatations = array_merge($this->sanatations, $sanitations);
		}
	}
	/**
	 *
	 * Set a valid grid event
	 * @param string $event - valid grid event
	 * @param string $code Javascript code which will be executed when the event raises
	 * @return bolean
	 */
	public function setGridEvent($event,$code)
	{
		if(!$this->runSetCommands) { return false; }
		$event = trim($event);
		$this->gridOptions[$event] = "js:".$code;
		return true;
	}

	/**
	 *
	 * Set options in the navigator for the diffrent actions
	 * @param string $module - can be navigator, add, edit, del, view
	 * @param array $aoptions options that are applicable to this module
	 * The key correspond to the options in jqGrid
	 * @return boolean
	 */
	public function setNavOptions($module,$aoptions)
	{
		$ret = false;
		if(!$this->runSetCommands) { return $ret; }
		switch ($module)
		{
			case 'navigator' :
				$this->navOptions = array_merge($this->navOptions,$aoptions);
				$ret = true;
				break;
			case 'add' :
				$this->addOptions = array_merge($this->addOptions,$aoptions);
				$ret = true;
				break;
			case 'edit' :
				$this->editOptions = array_merge($this->editOptions,$aoptions);
				$ret = true;
				break;
			case 'del' :
				$this->delOptions = array_merge($this->delOptions,$aoptions);
				$ret = true;
				break;
			case 'search' :
				$this->searchOptions = array_merge($this->searchOptions,$aoptions);
				$ret = true;
				break;
			case 'view' :
				$this->viewOptions = array_merge($this->viewOptions,$aoptions);
				$ret = true;
				break;
		}
		return $ret;
	}

	/**
	 *
	 * Set a event in the navigator or in the diffrent modules add,edit,del,view, search
	 * @param string $module - can be navigator, add, edit, del, view
	 * @param string $event - valid event for the particular module
	 * @param string $code - javascript code to be executed when the event occur
	 * @return boolean
	 */
	public function setNavEvent($module,$event,$code)
	{
		$ret = false;
		if(!$this->runSetCommands) { return $ret; }
		$event = trim($event);
		switch ($module)
		{
			case 'navigator' :
				$this->navOptions[$event] = "js:".$code;
				$ret = true;
				break;
			case 'add' :
				$this->addOptions[$event] = "js:".$code;
				$ret = true;
				break;
			case 'edit' :
				$this->editOptions[$event] = "js:".$code;
				$ret = true;
				break;
			case 'del' :
				$this->delOptions[$event] = "js:".$code;
				$ret = true;
				break;
			case 'search' :
				$this->searchOptions[$event] = "js:".$code;
				$ret = true;
				break;
			case 'view' :
				$this->viewOptions[$event] = "js:".$code;
				$ret = true;
    			break;
		}
		return $ret;
	}
	/**
	 * Set a options for inline editing in particulear module
	 * @param string $module - can be navigator or add or edit
	 * @param array $aoptions array of options
	 * @return boolean 
	 */
	public function inlineNavOptions ($module, $aoptions)
	{
		$ret = false;
		if(!$this->runSetCommands) { return $ret; }
		switch ($module)
		{
			case 'navigator':
				$this->inlineNavOpt = array_merge($this->inlineNavOpt,$aoptions);
				$ret = true;
				break;
			case 'add':
				$this->inlineNavOpt['addParams'] = array_merge($this->inlineNavOpt['addParams'],$aoptions);
				$ret = true;
				break;
			case 'edit':
				$this->inlineNavOpt['editParams'] = array_merge($this->inlineNavOpt['editParams'],$aoptions);
				$ret = true;
				break;
		}
		return $ret;
	}
	
	/**
	 *
	 * Set a event for inline editing in particulear module
	 * @param string $module - can be add or edit
	 * @param string $event the name of the event
	 * @param string $code the javascript code for this event
	 * @return boolean 
	 */
	public function inlineNavEvent ($module, $event, $code)
	{
		$ret = false;
		if(!$this->runSetCommands) { return $ret; }
		$event = trim($event);
		if($module == "add") {
			$this->inlineNavOpt['addParams'][$event] = "js:".$code;
			$ret = true;
		} else if( $module=="edit") {
			$this->inlineNavOpt['editParams'][$event] = "js:".$code;
			$ret = true;
		}
		return $ret;
	}
	/**
	 * Return a array of the all events and options for the inline navigator
	 * @return type 
	 */
	public function getInlineOptions()
	{
		return $this->inlineNavOpt;
	}
	/**
	 *
	 * Set options for the tolbar filter when enabled
	 * @param array $aoptions valid options for the filterToolbat
	 */
	public function setFilterOptions($aoptions)
	{
		 if($this->runSetCommands) {
			if(is_array($aoptions)) {
				$this->filterOptions = jqGridUtils::array_extend($this->filterOptions,$aoptions);
			}
		}
	}
	/**
	 * Construct a code for execution of valid grid method. This code is putted
	 * after the creation of the grid
	 * @param string $grid valid grid id should be putted as #mygrid
	 * @param string $method valid grid method
	 * @param array $aoptions contain the parameters passed to
	 * the method. Omit this parameter if the method does not have parameters
	 * @param integer $delay If set > 0 the method is called in setTimeout with the delay
	 */
	public function callGridMethod($grid, $method, array $aoptions=null, $delay=0)
	{
		if($this->runSetCommands) {
			$prm = '';
			if(is_array($aoptions) && count($aoptions) > 0)
			{
				$prm = jqGridUtils::encode($aoptions);
				$prm = substr($prm, 1);
				$prm = substr($prm,0, -1);
				$prm = ",".$prm;
			}
			if(strpos($grid,"#") === false || strpos($grid,"#") > 0) {
				$grid = "#".$grid;
			}
			$s1 = ''; 
			$s2 = '';
			if($delay > 0) {
				$s1 = "setTimeout(function() { ";
				$s2 = "}, ".$delay.");";
			}
			$this->gridMethods[] = $s1."jQuery('".$grid."').jqGrid('".$method."'".$prm.");".$s2;
		}
	}
	/**
	 * Put a javascript arbitrary code after all things are created. The method is executed
	 * only once when the grid is created.
	 * @param string $code - javascript to be executed
	 */
	public function setJSCode($code)
	{
		if($this->runSetCommands)
		{
			$this->customCode = "js:".$code;
		}
	}
	/**
	 * Construct the column model of the grid. The model can be passed as array
	 * or can be constructed from sql. See _setSQL() to determine which SQL is
	 * used. The method try to determine the primary key and if it is found is
	 * set as key:true to the appropriate field. If the primary key can not be
	 * determined set the first field as key:true in the colModel.
	 * Return true on success.
	 * @see _setSQL
	 * @param array $model if set construct the model ignoring the SQL command
	 * @param array $params if a sql command is used parametters passed to the SQL
	 * @param array $labels if this parameter is set it set the labels in colModel.
	 * The array should be associative which key value correspond to the name of
	 * colModel
	 * @return boolean
	 */
	public function setColModel(array $model=null, array $params=null, array $labels=null)
	{
		$goper = $this->oper ? $this->oper : 'nooper';
		// excel, nooper, !(in_array....)
		if(($goper == 'nooper' || $goper == $this->GridParams["excel"] || $goper == "pdf" || $goper=="csv")) { 
			$runme = true;
		} else { 
			$runme = !in_array($goper, array_values($this->GridParams)) || $this->serverValidate;
		}
		if($runme) {
			if(is_array($model) && count($model)>0) {
				$this->colModel = $model;
				if($this->primaryKey) {
					$this->setColProperty($this->primaryKey ,array("key"=>true));
				}
				return true;
			}
			$sql = null;
			$sqlId = $this->_setSQL();
			if(!$sqlId) { return false; }
			$nof = ($this->dbtype == 'sqlite' || $this->dbtype == 'db2' || $this->dbtype == 'array' || $this->dbtype == 'mongodb' || $this->dbtype == 'adodb') ? 1 : 0;
			//$sql = $this->parseSql($sqlId, $params);
			$ret = $this->execute($sqlId, $params, $sql, true, $nof, 0 );
			//$this->execute($sqlId, $params, $sql, $limit, $nrows, $offset)
			if ($ret)
			{
				if(is_array($labels) && count($labels)>0) { 
					$names = true;
				} else {
					$names = false;
				}
				$colcount = jqGridDB::columnCount($sql);
				for($i=0;$i<$colcount;$i++) {
					$meta = jqGridDB::getColumnMeta($i,$sql);
					if(strtolower($meta['name']) == 'jqgrid_row') { continue; } //Oracle, IBM DB2
					if($names && array_key_exists($meta['name'], $labels)) {
						$this->colModel[] = array('label'=>$labels[$meta['name']], 'name'=>$meta['name'], 'index'=>$meta['name'], 'sorttype'=> jqGridDB::MetaType($meta,$this->dbtype));
					} else {
						$this->colModel[] = array('name'=>$meta['name'], 'index'=>$meta['name'], 'sorttype'=> jqGridDB::MetaType($meta,$this->dbtype));
					}
				}
				jqGridDB::closeCursor($sql);
				if($this->primaryKey) {
					$pk = $this->primaryKey;
				} else  {
					$pk = jqGridDB::getPrimaryKey($this->table, $this->pdo, $this->dbtype);
					if($pk !== false ) { 
						$this->primaryKey = $pk;
					}
				}
				if($pk === false ) {
					$pk = 0;
				}
				$this->setColProperty($pk ,array("key"=>true));
			} else {
				$this->errorMessage = jqGridDB::errorMessage($sql);
				if($this->showError) {
					$this->sendErrorHeader();
				}
				return $ret;
			}
		}
		if(!$runme) {
			$this->runSetCommands = false;
		}
		return true;
	}
	/**
	 * Set a new property in the constructed colModel
	 * Return true on success.
	 * @param mixed $colname valid coulmn name or index in colModel
	 * @param array $aproperties the key name properties.
	 * @return boolean
	 */
	public function setColProperty ( $colname, array $aproperties)
	{
		//if(!$this->runSetCommands) return;
		$ret = false;
		if(!is_array($aproperties)) { return $ret; }
		if(count($this->colModel) > 0 )
		{
			if(is_int($colname)) {
				$this->colModel[$colname] = jqGridUtils::array_extend($this->colModel[$colname],$aproperties);
				$ret = true;
			} else {
				foreach($this->colModel as $key=>$val)
				{
					if($val['name'] == trim($colname))
					{
						$this->colModel[$key] = jqGridUtils::array_extend($this->colModel[$key],$aproperties);
						$ret = true;
						break;
					}
				}
			}
		}
		return $ret;
	}
	/**
	 * Add a column at the first or last position in the colModel and sets a certain
	 * properties to it
	 * @param array $aproperties data representing the column properties - including
	 * name, label...
	 * @param string $position can be first or last or number - default is first.
	 * If a number is set the column is added before the position corresponded
	 * to the position in colmodel
	 * @return boolean
	 */
	public function addCol (array $aproperties, $position='last') {
		if(!$this->runSetCommands) { return false; }
		if(is_array($aproperties) && count($aproperties)>0 && strlen($position)) {
			$cmcnt = count($this->colModel);
			if( $cmcnt > 0 ) {
				if( strtolower($position) === 'first')
				{
					array_unshift($this->colModel, $aproperties);
				} else if(strtolower($position) === 'last'){
					array_push($this->colModel, $aproperties);
				} else if( (int)$position >= 0 && (int)$position <= $cmcnt-1 ) {
					$a = array_slice($this->colModel, 0, $position+1);
					$b = array_slice($this->colModel, $position+1);
					array_push($a, $aproperties);
					$this->colModel = array();
					foreach($b as $cm) {
						$a[] = $cm;
					}
					$this->colModel =  $a;
				}
				$aproperties = null;
				return true;
			}
		}
		return false;
	}
	/**
	 *
	 * Set a various options for the buttons on the pager. tite, caption , icon
	 *
	 * @param string $exptype
	 * @param array $aoptions
	 */
	public function setButtonOptions( $exptype, $aoptions)
	{
		if(is_array($aoptions) && count($aoptions)  > 0) {
			switch ($exptype) {
				case 'excel' :
					$this->expoptions['excel'] = jqGridUtils::array_extend($this->expoptions['excel'], $aoptions);
					break;
				case 'pdf' :
					$this->expoptions['pdf'] = jqGridUtils::array_extend($this->expoptions['pdf'], $aoptions);
					break;
				case 'csv' :
					$this->expoptions['csv'] = jqGridUtils::array_extend($this->expoptions['csv'], $aoptions);
					break;
				case 'columns':
					$this->expoptions['columns'] = jqGridUtils::array_extend($this->expoptions['columns'], $aoptions);
					break;
			}
		}
	}
	/**
	 * Sets the hidden property in colModel if it is exported
	 * Used in exports (pdf, csv, excel)
	 */
	private function colModelHidden () {
		$cm = jqGridUtils::GetParam('colModel');
		$cmn = json_decode($cm);
		foreach ($cmn as $key=>$item) {
			$this->setColProperty($item->name, array("hidden"=>$item->hidden));
		}
	}
	
	/**
	 * Main method which do allmost everthing for the grid.
	 * Construct the grid, perform CRUD operations, perform Query and serch operations,
	 * export to excel, set a jqGrid method, and javascript code
	 * @param string $tblelement the id of the table element to costrict the grid
	 * @param string $pager the id for the pager element
	 * @param boolean $script if set to true add a script tag before constructin the grid.
	 * @param array $summary - set which columns should be sumarized in order to be displayed to the grid
	 * By default this parameter uses SQL SUM function: array("colmodelname"=>"sqlname");
	 * It can be set to use other one this way :
	 * array("colmodelname"=>array("sqlname"=>"AVG"));
	 * By default the first field correspond to the name of colModel the second to
	 * the database name
	 * @param array $params parameters passed to the query
	 * @param boolean $createtbl if set to true the table element is created automatically
	 * from this method. Default is false
	 * @param boolean $createpg if set to true the pager element is created automatically
	 * from this script. Default false.
	 * @param boolean $echo if set to false the function return the string representing
	 * the grid
	 * @return mixed.
	 */
	public function renderGrid($tblelement='', $pager='', $script=true, array $summary=null, array $params=null, $createtbl=false, $createpg=false, $echo=true)
	{
		$oper = $this->GridParams["oper"];
		$goper = $this->oper ? $this->oper : 'nooper';
		if($goper == $this->GridParams["autocomplete"]) {
			return false;
		} else if($goper == $this->GridParams["excel"]) {
			if(!$this->export) { 
				return false;
			}
			if($this->currentHidden) {
				$this->colModelHidden();
			}
			$this->exportToExcel($summary, $params, $this->colModel, true, $this->exportfile);
		} else if($goper == "pdf") {
			if(!$this->export) { return false; }
			if($this->currentHidden) {
				$this->colModelHidden();
			}
			$this->exportToPdf($summary, $params, $this->colModel, $this->pdffile);
		} else if($goper == "csv") {
			if(!$this->export) { return false; }
			if($this->currentHidden) {
				$this->colModelHidden();
			}
			$this->exportToCsv($summary, $params, $this->colModel, true, $this->csvfile, $this->csvsep, $this->csvsepreplace);
		} else if(in_array($goper, array_values($this->GridParams)) ) {
			if( $this->inlineNav ) { $this->getLastInsert = true; }
			return $this->editGrid( $summary, $params, $goper, $echo);
		} else {
			if(!isset ($this->gridOptions["datatype"]) ) { 
				$this->gridOptions["datatype"] = $this->dataType;
			}
			// hack for editable=true as default
			$ed = true;
			if(isset ($this->gridOptions['cmTemplate'])) {
				$edt = $this->gridOptions['cmTemplate'];
				$ed = isset($edt['editable']) ? $edt['editable'] : true;
			}
			foreach($this->colModel as $k=>$cm) {
				if(!isset($this->colModel[$k]['editable'])) {
					$this->colModel[$k]['editable'] = $ed;
				}
			}
			$this->gridOptions['colModel'] = $this->colModel;
			if(isset ($this->gridOptions['postData'])) { $this->gridOptions['postData'] = jqGridUtils::array_extend($this->gridOptions['postData'], array($oper=>$this->GridParams["query"])); }
			else { $this->setGridOptions(array("postData"=>array($oper=>$this->GridParams["query"]))); }
			if(isset($this->primaryKey))  {
				$this->GridParams["id"] = $this->primaryKey;
			}
			$this->setGridOptions(array("prmNames"=>$this->GridParams));
			$s = '';
			if($createtbl) {
				$tmptbl = $tblelement;
				if(strpos($tblelement,"#") === false) {
					$tblelement = "#".$tblelement;
				} else {
					$tmptbl = substr($tblelement,1);
				}
				$s .= "<table id='".$tmptbl."'></table>";
			}
			if(strlen($pager)>0) {
				$tmppg = $pager;
				if(strpos($pager,"#") === false) {
					$pager = "#".$pager;
				} else {
					$tmppg = substr($pager,1);
				}
				if ($createpg ) {
					$s .= "<div id='".$tmppg."'></div>";
				}
			}
			// set the Error handler for data
			if(!isset($this->gridOptions['loadError']))  {
				$err = "function(xhr,status, err){ try {jQuery.jgrid.info_dialog(jQuery.jgrid.errors.errcap,'<div class=\"ui-state-error\">'+ xhr.responseText +'</div>', jQuery.jgrid.edit.bClose,{buttonalign:'right'});} catch(e) { alert(xhr.responseText);} }";
				$this->setGridEvent('loadError',$err );
			}
			//if(!isset($this->editOptions['mtype']) && $this->showError) {
				//$this->setNavEvent('edit', 'afterSubmit', "function(res,pdata){ var result = res.responseText.split('#'); if(result[0]=='$this->successmsg') return [true,result[1],result[2]]; else return [false,result[1],'']; }");
			//}
			//if(!isset($this->addOptions['mtype']) && $this->showError) {
				//$this->setNavEvent('add', 'afterSubmit', "function(res,pdata){ var result = res.responseText.split('#'); if(result[0]=='$this->successmsg') return [true,result[1],result[2]]; else return [false,result[1],''];}");
			//}
			if(strlen($pager)>0) { $this->setGridOptions(array("pager"=>$pager)); }
			//$this->editOptions['mtype'] = $this->mtype;
			//$this->addOptions['mtype'] = $this->mtype;
			//$this->delOptions['mtype'] = $this->mtype;
			if($this->sharedEditOptions==true) {
				$this->gridOptions['editOptions'] = $this->editOptions;
			}
			if($this->sharedAddOptions==true) {
				$this->gridOptions['addOptions'] = $this->addOptions;
			}
			if($this->sharedDelOptions==true) {
				$this->gridOptions['delOptions'] = $this->delOptions;
			}
			if($script) {
				$s .= "<script type='text/javascript'>";
				$s .= "jQuery(document).ready(function($) {";
			}
			$s .= "jQuery('".$tblelement."').jqGrid(".jqGridUtils::encode($this->gridOptions).");";
			if($this->navigator && strlen($pager)>0) {
				$s .= "jQuery('".$tblelement."').jqGrid('navGrid','".$pager."',".jqGridUtils::encode($this->navOptions);
				$s .= ",".jqGridUtils::encode($this->editOptions);
				$s .= ",".jqGridUtils::encode($this->addOptions);
				$s .= ",".jqGridUtils::encode($this->delOptions);
				$s .= ",".jqGridUtils::encode($this->searchOptions);
				$s .= ",".jqGridUtils::encode($this->viewOptions).");";
				$curhidden = $this->currentHidden ? "1" : "0";
				if($this->navOptions["excel"]==true)
				{
					$eurl = $this->getGridOption('url');
$exexcel = <<<EXCELE
onClickButton : function(e)
{
	try {
		jQuery("$tblelement").jqGrid('excelExport',{tag:'excel', url:'$eurl', exporthidden:$curhidden, exportgrouping: false});
	} catch (e) {
		window.location= '$eurl?oper=excel';
	}
}
EXCELE;
					$s .= "jQuery('".$tblelement."').jqGrid('navButtonAdd','".$pager."',{id:'".$tmppg."_excel', caption:'".$this->expoptions['excel']['caption']."',title:'".$this->expoptions['excel']['title']."',".$exexcel.",buttonicon:'".$this->expoptions['excel']['buttonicon']."'});";
				}

				if($this->navOptions["pdf"]==true)
				{
					$eurl = $this->getGridOption('url');
$expdf = <<<PDFE
onClickButton : function(e)
{
	try {
		jQuery("$tblelement").jqGrid('excelExport',{tag:'pdf', url:'$eurl', exporthidden:$curhidden, exportgrouping: false});
	} catch (e) {
		window.location= '$eurl?oper=pdf';
	}
}
PDFE;
					$s .= "jQuery('".$tblelement."').jqGrid('navButtonAdd','".$pager."',{id:'".$tmppg."_pdf',caption:'".$this->expoptions['pdf']['caption']."',title:'".$this->expoptions['pdf']['title']."',".$expdf.", buttonicon:'".$this->expoptions['pdf']['buttonicon']."'});";
				}

				if($this->navOptions["csv"]==true)
				{
					$eurl = $this->getGridOption('url');
$excsv = <<<CSVE
onClickButton : function(e)
{
	try {
		jQuery("$tblelement").jqGrid('excelExport',{tag:'csv', url:'$eurl', exporthidden:$curhidden, exportgrouping: false});
	} catch (e) {
		window.location= '$eurl?oper=csv';
	}
}
CSVE;
					$s .= "jQuery('".$tblelement."').jqGrid('navButtonAdd','".$pager."',{id:'".$tmppg."_csv',caption:'".$this->expoptions['csv']['caption']."',title:'".$this->expoptions['csv']['title']."',".$excsv.",buttonicon:'".$this->expoptions['csv']['buttonicon']."'});";
				}

				if($this->navOptions["columns"]==true)
				{
					$clopt = jqGridUtils::encode($this->expoptions['columns']['options']);
$excolumns = <<<COLUMNS
onClickButton : function(e)
{
	jQuery("$tblelement").jqGrid('columnChooser',$clopt);
}
COLUMNS;
					$s .= "jQuery('".$tblelement."').jqGrid('navButtonAdd','".$pager."',{id:'".$tmppg."_col',caption:'".$this->expoptions['columns']['caption']."',title:'".$this->expoptions['columns']['title']."',".$excolumns.",buttonicon:'".$this->expoptions['columns']['buttonicon']."'});";
				}
			}
			// inline navigator
			if($this->inlineNav && strlen($pager)>0) {
$aftersave = <<<AFTERS
function (id, res)
{
	res = res.responseText.split("#");
	try {
		$(this).jqGrid('setCell', id, res[0], res[1]);
		$("#"+id, "#"+this.p.id).removeClass("jqgrid-new-row").attr("id",res[1] );
		$(this)[0].p.selrow = res[1];
	} catch (asr) {}
}
AFTERS;
				$this->inlineNavOpt['addParams'] = jqGridUtils::array_extend($this->inlineNavOpt['addParams'], array("aftersavefunc"=>"js:".$aftersave));
				$this->inlineNavOpt['editParams'] = jqGridUtils::array_extend($this->inlineNavOpt['editParams'], array("aftersavefunc"=>"js:".$aftersave));
				$s .= "jQuery('".$tblelement."').jqGrid('inlineNav','".$pager."',".jqGridUtils::encode($this->inlineNavOpt).");\n";				
			}
			// toolbar filter
			if($this->toolbarfilter){
				$s .= "jQuery('".$tblelement."').jqGrid('filterToolbar',".jqGridUtils::encode($this->filterOptions).");\n";
			}
			// grid methods
			$gM = count($this->gridMethods);
			if($gM>0) {
				for($i=0; $i<$gM; $i++) {
					$s .= $this->gridMethods[$i]."\n";
				}
			}
			//at end the custom code
			if(strlen($this->customCode)>0) {
				$s .= jqGridUtils::encode($this->customCode);
			}
			if($script) { $s .= " });</script>"; }
			if($echo) {
				echo $s;
			}
			return $echo ? "" : $s;
		}
	}
}
