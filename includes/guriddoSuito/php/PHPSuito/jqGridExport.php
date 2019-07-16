<?php
/**
 * @author  Tony Tomov, (tony@trirand.com)
 * @copyright TriRand Ltd
 * @package jqGridExport
 * @version 5.3.1
 * @abstract
 * This class extend the main jqGrid class and is used for export to csv,pdf and excel.
 * 
 *
 */

/** PHPSuito root directory */
if (!defined('PHPSUITO_ROOT')) {
    define('PHPSUITO_ROOT', dirname(__FILE__) . '/');
    require(PHPSUITO_ROOT . 'Autoloader.php');
}

class jqGridExport extends jqGrid
{
// PDF Settings
	/**
	 * The export to file to PDF
	 * @var string
	 */
	public $pdffile = 'exportdata.pdf';
	/**
	 * Holds the default settings for pdf export
	 * @var array
	 */
	protected $PDF = array(
		"page_orientation" => "P",
		"unit"=>"mm",
		"page_format"=>"A4",
		"creator"=>"jqGrid",
		"author"=>"jqGrid",
		"title"=>"jqGrid PDF",
		"subject"=>"Subject",
		"keywords"=>"table, grid",
		"margin_left"=>15,
		"margin_top"=>7,
		"margin_right"=>15,
		"margin_bottom"=>25,
		"margin_header"=>5,
		"margin_footer"=>10,
		"font_name_main"=>"helvetica",
		"font_size_main"=>10,
		"header_logo"=>"",
		"header_logo_width"=>0,
		"header_title"=>"",
		"header_string"=>"",
		"header"=>false,
		"footer"=>true,
		"font_monospaced"=>"courier",
		"font_name_data"=>"helvetica",
		"font_size_data"=>8,
		"image_scale_ratio"=>1.25,
		"grid_head_color"=>"#dfeffc",
		"grid_head_text_color"=>"#2e6e9e",
		"grid_draw_color"=>"#5c9ccc",
		"grid_header_height"=>6,
		"grid_row_color"=>"#ffffff",
		"grid_row_text_color"=>"#000000",
		"grid_row_height"=>5,
		"grid_alternate_rows"=>false,
		"path_to_pdf_class"=>"External/tcpdf/tcpdf.php",
		"shrink_cell" => true,
		"reprint_grid_header"=>false,
		"shrink_header" => true,
		"unicode" => true,
		"encoding" => "UTF-8",
		"destination"=> "D",
		"save_to_disk"=>false,
		"save_to_disk_only"=>false,
		"disk_file"=>"",
	);
	/**
	 * Set options for PDF export.
	 * @param array $apdf
	 */
	public function setPdfOptions( $apdf )
	{
		if(is_array($apdf) and count($apdf) > 0 ) {
			$this->PDF = jqGridUtils::array_extend($this->PDF, $apdf);
		}
	}
	/**
	 * Retun the pdf options for th buildin of pdf file
	 * @return array 
	 */
	public function getPdfOptions() 
	{
		return $this->PDF;
	}
// CSV Settings
	/**
	 * The export to file to CSV
	 * @var string
	 */
	protected $CSV = array(
		"file" => "",
		"save_to_disk"=>false,
		"save_to_disk_only"=>false,
		"disk_file"=>"",
		"separator"=>"",
		"sepreplace"=>"",
		"quote" => '"',
		"escquote"=> '"',
		"addtitles"=>true, 
		"replaceNewLine" => ' '
	);
	/**
	 * File name for the csv export
	 * @var string
	 * 
	 */
	public $csvfile = 'exportdata.csv';
	/**
	 * CSV separator
	 * @var string
	 */
	public $csvsep = ';';
	/**
	 * CSV string to replavce separator
	 * @var string
	 */
	public $csvsepreplace = ' ';
	/**
	 * Set options for CSV export.
	 * @param array $acsv
	 */
	public function setCsvOptions( $acsv )
	{
		if(is_array($acsv) and count($acsv) > 0 ) {
			$this->CSV = jqGridUtils::array_extend($this->CSV, $acsv);
		}
	}
	/**
	 * retun the options needed for building CSV file
	 * @return array
	 */
	public function getCsvOptions()
	{
		return $this->CSV;
	}
// Excel Settings
	/**
	 * The export to file to excel
	 * @var string
	 */
	public $exportfile = 'exportdata.xml';
	/**
	 * Holds the default settings for pdf export
	 * @var array
	 */
	protected $EXCEL = array(
		"file_type"=>"xml", //Excel2007,Excel5,xml
		"file"=>"",
		"save_to_disk"=>false,
		"save_to_disk_only"=>false,
		"disk_file"=>"",
		"start_cell" => "A1",
		"creator"=>"jqGrid",
		"author"=>"jqGrid",
		"title"=>"jqGrid Excel",
		"subject"=>"Office 2007 XLSX Document",
		"description"=>"Document created with Guriddo",
		"keywords"=>"Guriddo, jqGrid, Excel",
		"font"=>"Arial",
		"font_size"=>11,
		"header_cell"=>"", 
		"header_title"=>"",
		"protect" => false,
		"password"=>"Guriddo",
		"format_int"=>"#0",
		"format_num"=>"#.00",
		"format_text"=>"@",
		"format_date"=>"YYYY-MM-DD",
		"path_to_phpexcel_class"=>"External/phpexcel/PHPExcel.php"
	);
	/**
	 * Custom function which can be called to modify the grid output. Parameters
	 * passed to this function are the response object and the db connection
	 * @var function
	 */
	public $excelFunc = null;
	/**
	 * Custom call can be used again with custom function customFunc. We can call
	 * this using static defined functions in class customClass::customFunc - i.e
	 * $grid->customClass = Custom, $grid->customFunc = myfunc
	 * or $grid->customClass = new Custom(), $grid->customFunc = myfunc
	 * @var mixed
	 */
	public $excelClass = false;

	/**
	 * Set options for PDF export.
	 * @param array $apdf
	 */
	public function setExcelOptions( $apdf )
	{
		if(is_array($apdf) and count($apdf) > 0 ) {
			$this->EXCEL = jqGridUtils::array_extend($this->EXCEL, $apdf);
		}
	}
	public function getExcelOptions() 
	{
		return $this->EXCEL;
	}
	
	/**
	 *
	 * @var type for internal use
	 */
	protected $groupdata;

	public function groupCalcs( $func, $v, $name, $record, $sr)
	{
		switch (strtolower($func)) {
			case 'sum' :
			case 'avg' :
				if($v == '') { $v = 0; }
				$ret =  (float)$v + (float)$record[$name];
				break;
			case 'min' :
				if($v == '') {
					return $record[$name];
				}
				$ret =  min(array($v, $record[$name]));
				break;
			case 'max' :
				if($v == '') {
					return $record[$name];
				}
				$ret = max(array($v, $record[$name]));
				break;
			case 'count' :
				if($v == '') {$v=0;}
				if(isset($record[$name])) {
					$ret = $v+1;
				} else {
					$ret = 0;
				}
				break;
			default :
				$ret ='';
		}
		if($sr != null && is_numeric($ret)) {
			$ret = round($ret, $sr);
		}
		return $ret;
	}
	private function groupingSetup ($rs, $colmodel)
	{
		if(isset($this->gridOptions['groupingView'])) {
			$grpopt = $this->gridOptions['groupingView'];
		} else {
			printf('No grouping is set!');
			return false;			
		}
		if (!$rs) {
			printf('Bad Record set groupingSetup');
			return false;
		}		
		$this->groupdata = new ArrayIterator();
		$maxrows = $this->gSQLMaxRows;
		$ncols = jqGridDB::columnCount($rs);

		$nmodel = is_array($colmodel) ? count($colmodel) : -1;
		// find the actions collon
		if($nmodel > 0) {
			for ($i=0; $i < $nmodel; $i++) {
				if($colmodel[$i]['name']=='actions') {
					array_splice($colmodel, $i, 1);
					$nmodel--;
					break;
				}
			}
		}
		
		$model = false;
		if($colmodel && $nmodel== $ncols) {
			$model = true;
		}

		$grp = new stdClass();
		$grp->summary =array();
		$grp->columns = array();
		$grp->totalwidth = 0;
		$grpopt['groupIndex'] = array();
		for($i=0;$i<$ncols;$i++)
		{
			$atmp = array();
			$atmp['hidden'] = ($model && isset($colmodel[$i]["hidden"])) ? $colmodel[$i]["hidden"] : false;
			$atmp['select']= false;
			if($model && isset($colmodel[$i]["formatter"])) {
				$atmp['formatter'] = $colmodel[$i]["formatter"];
				$asl = isset($colmodel[$i]["formatoptions"]) ? $colmodel[$i]["formatoptions"] : (isset($colmodel[$i]["editoptions"]) ? $colmodel[$i]["editoptions"] : false);
				$atmp['formatoptions'] = $asl;
 				if($colmodel[$i]["formatter"]=="select") {
					//$asl = isset($colmodel[$i]["formatoptions"]) ? $colmodel[$i]["formatoptions"] : $colmodel[$i]["editoptions"];
					if(isset($asl["value"])) {
						$sep = isset($asl["separator"]) ? $asl["separator"] : ":";
						$delim = isset($asl["delimiter"]) ? $asl["delimiter"] : ";";
						$list = explode( $delim ,$asl["value"]);
						foreach($list as $key=>$val) {
							$items = explode( $sep ,$val);
							$atmp['select'][$items[0]] = $items[1];
						}
					}
				}
			}
			if( !isset($colmodel[$i]["summaryRound"]) ) {
				$colmodel[$i]["summaryRound"] = null;
			}
			$atmp['width'] = ($model && isset($colmodel[$i]["width"])) ? (int)$colmodel[$i]["width"] : 150;
			if($model) {
				$atmp['label'] = isset($colmodel[$i]["label"]) ? $colmodel[$i]["label"] : $colmodel[$i]["name"];
				$atmp["name"] = $colmodel[$i]["name"];
				$atmp['type'] = isset($colmodel[$i]["sorttype"]) ? $colmodel[$i]["sorttype"] : '';
			} else {
				$field = jqGridDB::getColumnMeta($i,$rs);
				$atmp['label'] = $field["name"];
				$atmp["name"] = $field["name"];
				$atmp['type'] = jqGridDB::MetaType($field, $this->dbtype);
			}
			$atmp['align'] = isset($colmodel[$i]["align"]) ? strtoupper(substr($colmodel[$i]["align"],0,1)) : "L";
			for($j=0;$j < count($grpopt['groupField']);$j++){
				if($atmp['name']== $grpopt['groupField'][$j]) {
					$grpopt['groupIndex'][$j] = $i;
				}
			}
			if( isset($colmodel[$i]["summaryType"]) ) {
				if( isset($colmodel[$i]["summaryDivider"]) ) {
					$grp->summary[] = array("nm"=> $atmp['name'],"st"=>$colmodel[$i]["summaryType"], "v"=>0, "sd"=>$colmodel[$i]["summaryDivider"], "vd"=>0, "sr"=>$colmodel[$i]["summaryRound"]);
				} else {
					$grp->summary[] = array("nm"=> $atmp['name'],"st"=>$colmodel[$i]["summaryType"], "v"=>0,"sr"=>$colmodel[$i]["summaryRound"] );
				}
			}
			$grp->totalwidth += $atmp['hidden'] ? 0 : $atmp['width'];
			$grp->columns[$i] = $atmp;
		}
		for($j=0;$j < count($grpopt['groupField']);$j++){
			if(!isset($grpopt['groupSummary'][$j])) {
				$grpopt['groupSummary'][$j] = false;
			}
		}
		$grp->lastvalues=array();
		$grp->groups =array();
		$grp->counters =array();
		$irow = 0;
		$fieldnames = array();
		$grlen = count($grpopt['groupIndex']);
		if($this->dbtype == 'mysqli') {
			//start the count from 1. First value has to be a reference to the stmt. because bind_param requires the link to $stmt as the first param.			
			$fieldnames[0] = &$rs;
			$res_arr = array();
			for ($i=0;$i<$ncols;$i++) {
				$fieldnames[$i+1] = &$res_arr[$grp->columns[$i]['name']]; //load the fieldnames into an array.
			}
			call_user_func_array('mysqli_stmt_bind_result', $fieldnames);
		}
		while($r = jqGridDB::fetch_assoc($rs, $this->pdo) )
		{
			// mysqli does not want to make this easy
			if($this->dbtype == 'mysqli') {
				$r = jqGridUtils::array_extend(array(),$res_arr);
			}
			$changed = 0;
			for($i=0;$i<$grlen; $i++) {
				$fieldInd = $grpopt['groupIndex'][$i];
				$fieldName = $grpopt['groupField'][$i];
				if(array_key_exists($fieldName, $r) ) {
					$v = $r[$fieldName];
					if($grp->columns[$fieldInd]['select']) {
						if(isset($grp->columns[$fieldInd]['select'][$v])) {
							$v = $grp->columns[$fieldInd]['select'][$v];
						}
					}
					if($irow == 0 ) {
						$grp->groups[] = array("idx"=>$i,"dataIndex"=>$fieldName,"value"=>$v, "startRow"=> $irow, "cnt"=>1, "summary"=> array() );
						$grp->lastvalues[$i] = $v;
						$grp->counters[$i] = array("cnt"=>1, "pos"=> count($grp->groups)-1, "summary"=>$grp->summary);
						foreach($grp->counters[$i]['summary'] as $sumkey => $sumval) {
							if(isset($sumval['sd'])) {
								$grp->counters[$i]['summary'][$sumkey]['vd'] = $this->groupCalcs( $sumval['st'], $sumval['vd'], $sumval['sd'], $r, $sumval['sr']);
							}
							$grp->counters[$i]['summary'][$sumkey]['v'] = $this->groupCalcs( $sumval['st'], $sumval['v'], $sumval['nm'], $r, $sumval['sr']);
						}
						$grp->groups[$grp->counters[$i]['pos']]['summary'] = $grp->counters[$i]['summary'];
					
					} else {
						if( $v !=  $grp->lastvalues[$i]) {
							$grp->groups[] = array("idx"=>$i,"dataIndex"=>$fieldName,"value"=>$v, "startRow"=> $irow, "cnt"=>1, "summary"=> array() );
							$grp->lastvalues[$i] = $v;
							$changed = 1;
							$grp->counters[$i] = array("cnt"=>1, "pos"=> count($grp->groups)-1, "summary"=>$grp->summary);
							foreach($grp->counters[$i]['summary'] as $sumkey => $sumval) {
								if(isset($sumval['sd'])) {
									$grp->counters[$i]['summary'][$sumkey]['vd'] = $this->groupCalcs( $sumval['st'], $sumval['vd'], $sumval['sd'], $r, $sumval['sr']);
								}
								$grp->counters[$i]['summary'][$sumkey]['v'] = $this->groupCalcs( $sumval['st'], $sumval['v'], $sumval['nm'], $r, $sumval['sr']);
							}
							$grp->groups[$grp->counters[$i]['pos']]['summary'] = $grp->counters[$i]['summary'];
						} else {
							if($changed == 1) {
								$grp->groups[] = array("idx"=>$i,"dataIndex"=>$fieldName,"value"=>$v, "startRow"=> $irow, "cnt"=>1, "summary"=> array() );
								$grp->lastvalues[$i] = $v;
								$grp->counters[$i] = array("cnt"=>1, "pos"=> count($grp->groups)-1, "summary"=>$grp->summary);
								foreach($grp->counters[$i]['summary'] as $sumkey => $sumval) {									
									if(isset($sumval['sd'])) {
										$grp->counters[$i]['summary'][$sumkey]['vd'] = $this->groupCalcs( $sumval['st'], $sumval['vd'], $sumval['sd'], $r, $sumval['sr']);
									}
									$grp->counters[$i]['summary'][$sumkey]['v'] = $this->groupCalcs( $sumval['st'], $sumval['v'], $sumval['nm'], $r, $sumval['sr']);
								}
								$grp->groups[$grp->counters[$i]['pos']]['summary'] = $grp->counters[$i]['summary'];
							} else {
								$grp->counters[$i]['cnt']++;
								$grp->groups[$grp->counters[$i]['pos']]['cnt'] = $grp->counters[$i]['cnt'];
								foreach($grp->counters[$i]['summary'] as $sumkey => $sumval) {
									if(isset($sumval['sd'])) {
										$grp->counters[$i]['summary'][$sumkey]['vd'] = $this->groupCalcs( $sumval['st'], $sumval['vd'], $sumval['sd'], $r, $sumval['sr']);
									}
									$grp->counters[$i]['summary'][$sumkey]['v'] = $this->groupCalcs( $sumval['st'], $sumval['v'], $sumval['nm'], $r, $sumval['sr']);
								}
								$grp->groups[$grp->counters[$i]['pos']]['summary'] = $grp->counters[$i]['summary'];
							}
						}
					}
				}
			}
			$this->groupdata[] = $r;
			$irow++;
			if($irow >= $maxrows) { break; }
		}
		return $grp;
	}
	protected function findGroupIdx( $ind , $offset, $grp) 
	{
		$ret = array();
		if($offset===0) {
			$ret = $grp[$ind];
		} else {
			$id = $grp[$ind]['idx'];
			if($id===0) { 
				$ret = $grp[$ind]; 	
			}
			for($i=$ind;$i >= 0; $i--) {
				if($grp[$i]['idx'] === $id-$offset) 
				{
					$ret =  $grp[$i];
					break;
				}
			}
		}
		return $ret;
	}
	protected function js_template( $format='') 
	{
		$numargs = func_num_args();
		$args = func_get_args();
		for ($i = 1;$i < $numargs; $i++) {
			if(is_array($args[$i])) {
				$nmarr = $args[$i];
				foreach( $nmarr as $k => $arr) {
					$args[0] = str_replace('{'.$arr['nm'].'}', $arr['v'], $args[0]);
				}
			} else {
				$args[0] = str_replace('{'.($i-1).'}', $args[$i], $args[0]);
			}
		}
		return isset($args[0]) ? $args[0] :'';
	}
	protected function tmp_file_name( $filename )
	{
		if ($filename == '-' || $filename == '') {
			$tmp_dir = sys_get_temp_dir();
			$filename = tempnam($tmp_dir, "jqGrid_Root");
		}
		return $filename;
	}
	/**
	 * 
	 * @param type $filename string
	 * @param type $mixedvar string or reource
	 * @return boolean|string true on success error description on error.
	 * 
	 *  Save the contents of the mixsedvar to disk. If filename is empty or - 
	 *  a temporary filename is created.
	 */
	protected function save_to_file($filename, $mixedvar)
	{
		$opw = "wb";
		$filename = $this->tmp_file_name($filename);
		$FILEH_ = fopen($filename, $opw);
		if ($FILEH_ == false) {
			return "Can't create temporary file.";
		}
		$fw = fwrite($FILEH_, $mixedvar);
		if ($fw == false) {
			return "Can't write to $filename. Check your path or permissions.";
		}		

		fclose($FILEH_);

		return true;
	}

	/**
	 * Convert a record set to csv data
	 *
	 * @param objec $rs the record set from the query
	 * @param array $colmodel colmodel which holds the names
	 * @param string $sep The string separator
	 * @param string $sepreplace with what to replace the separator if in string
	 * @param boolean $echo shoud be the  output echoed
	 * @param string $filename if echo is tru se the file name name for download
	 * @param boolen $addtitles should we set titles as first row
	 * @param string $quote
	 * @param string $escquote
	 * @param string $replaceNewLine with what to replace new line in the sstring
	 * @return string
	 */
	private function rs2csv($rs, $colmodel, $sep=';', $sepreplace=' ', $echo=true, $filename='exportdata.csv', $addtitles=true, $quote = '"', $escquote = '"', $replaceNewLine = ' ')
	{
		if (!$rs) { return ''; }
		// CONSTANTS
		$NEWLINE = "\r\n";
		$escquotequote = $escquote.$quote;
		$gSQLMaxRows = $this->gSQLMaxRows; // max no of rows to download
		$s = '';
		$ncols = jqGridDB::columnCount($rs);
		$model = false;
		$nmodel = is_array($colmodel) ? count($colmodel) : -1;
		// find the actions collon
		if($nmodel > 0) {
			for ($i=0; $i < $nmodel; $i++) {
				if($colmodel[$i]['name']=='actions') {
					array_splice($colmodel, $i, 1);
					$nmodel--;
					break;
				}
			}
		}
		if($colmodel && $nmodel== $ncols) {
			$model = true;
		}

		$fnames = array();
		for ($i=0; $i < $ncols; $i++) {
			if($model) {
				$fname = isset($colmodel[$i]["label"]) ? $colmodel[$i]["label"] : $colmodel[$i]["name"];
				$field["name"] = $colmodel[$i]["name"];
				$typearr[$i] = isset($colmodel[$i]["sorttype"]) ? $colmodel[$i]["sorttype"] : '';
			} else {
				$field = jqGridDB::getColumnMeta($i,$rs);
				$fname = $field["name"];
				$typearr[$i] = jqGridDB::MetaType($field, $this->dbtype);
			}
			$fnames[$i] = $field["name"];
			$v = $fname;
			if ($escquote) { $v = str_replace($quote,$escquotequote,$v); }
			$v = strip_tags(str_replace("\n", $replaceNewLine, str_replace("\r\n",$replaceNewLine,str_replace($sep,$sepreplace,$v))));
			//$elements[] = $v;

			$ahidden[$i] = ($model && isset($colmodel[$i]["hidden"])) ? $colmodel[$i]["hidden"] : false;
			if(!$ahidden[$i])
			{ 
				$elements[] = $v;
			}
			$aselect[$i] = false;
			if($model && isset($colmodel[$i]["formatter"])) {
				if($colmodel[$i]["formatter"]=="select") {
					$asl = isset($colmodel[$i]["formatoptions"]) ? $colmodel[$i]["formatoptions"] : (isset($colmodel[$i]["editoptions"]) ? $colmodel[$i]["editoptions"] : array());
					if(isset($asl["value"])) {
						$sep2 = isset($asl["separator"]) ? $asl["separator"] : ":";
						$delim = isset($asl["delimiter"]) ? $asl["delimiter"] : ";";
						$list = explode( $delim ,$asl["value"]);
						foreach($list as $key=>$val) {
							$items = explode( $sep2 ,$val);
							$aselect[$i][$items[0]] = $items[1];
						}
					}
				}
			}
		}
		if ($addtitles) {
			$s .= implode($sep, $elements).$NEWLINE;
		}
		$datefmt = $this->userdateformat;
		$timefmt = $this->usertimeformat;

		//Hack for mysqli driver
		if($this->dbtype == 'mysqli') {
			$fld = $rs->field_count;
			//start the count from 1. First value has to be a reference to the stmt. because bind_param requires the link to $stmt as the first param.
			//$count = 1;
			$fieldnames[0] = &$rs;
			for ($i=0;$i<$fld;$i++) {
				$fieldnames[$i+1] = &$res_arr[$i]; //load the fieldnames into an array.
			}
			call_user_func_array('mysqli_stmt_bind_result', $fieldnames);
		}
		$line = 0;
		while ($r = jqGridDB::fetch_num($rs, $this->pdo) /*!$rs->EOF*/) {
			if($this->dbtype == 'mysqli') { $r = $res_arr; }

			$elements = array();
			$i = 0;
////
			for ($i=0; $i < $ncols; $i++)
			{
				if(isset($ahidden[$i]) && $ahidden[$i]) { continue; }
				$v = $r[$i];
				if(is_array($aselect[$i])) {
					if(isset($aselect[$i][$v])) {
						$v1 = $aselect[$i][$v];
						if($v1)  { $v = $v1; }
					}
					$typearr[$i] = 'string';
				}
				$type = $typearr[$i];
				switch($type) {
				case 'date':
					$v = $datefmt != $this->dbdateformat ? jqGridUtils::parseDate($this->dbdateformat, $v, $datefmt) : $v;
					break;
				case 'datetime':
					$v = $timefmt != $this->dbtimeformat ? jqGridUtils::parseDate($this->dbtimeformat,$v,$timefmt) : $v;
					break;
				case 'numeric':
				case 'int':
					$v = trim($v);
					break;
				default:
					$v = trim($v);
					if (strlen($v) == 0) { $v = ''; }
				}

				if ($escquote) { $v = str_replace($quote,$escquotequote,trim($v)); }
				$v = strip_tags(str_replace("\n", $replaceNewLine, str_replace("\r\n",$replaceNewLine,str_replace($sep,$sepreplace,$v))));

				if (strpos($v,$sep) !== false || strpos($v,$quote) !== false) { $elements[] = "$quote$v$quote"; }
				else { $elements[] = $v; }
			} // for
////
			$s .= implode($sep, $elements).$NEWLINE;

			$line += 1;
			/*
			if ($echo) {
				if ($echo === true) { echo $s; }
				$s = '';
			}
			 * 
			 */
			if ($line >= $gSQLMaxRows) {
				break;
			}

		}
		if($this->tmpvar) {
			$elements = array();
			for ($i=0; $i < $ncols; $i++)
			{
				if(isset($ahidden[$i]) && $ahidden[$i]) { continue; }
				foreach($this->tmpvar as $key=>$vv) {
					if($fnames[$i]==$key) {
						$v = $vv;
						break;
					} else {
						$v = '';
					}
				}
				if ($escquote) { $v = str_replace($quote,$escquotequote,trim($v)); }
				$v = strip_tags(str_replace("\n", $replaceNewLine, str_replace("\r\n",$replaceNewLine,str_replace($sep,$sepreplace,$v))));

				if (strpos($v,$sep) !== false || strpos($v,$quote) !== false) { $elements[] = "$quote$v$quote"; }
				else { $elements[] = $v; }
			}
			$s .= implode($sep, $elements).$NEWLINE;
			}
		$es = $this->getCsvOptions();
		if(!isset($es['save_to_disk_only'])) {
			$es['save_to_disk_only'] = false;
		}
		if($es['save_to_disk_only']===true ) {
			$ret = $this->save_to_file($es['disk_file'], $s);
			if($ret !== true) {
				$this->errorMessage = $ret;
				$this->sendErrorHeader();
			}
			return;
		}
		if($es['save_to_disk'] === true) {
			$this->save_to_file($es['disk_file'], $s);
		}

		if($echo) {
			header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: private");
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=\"".$filename."\"");
			header("Accept-Ranges: bytes");
			echo $s; 
		} else {
			return $s;
		}
	}

	/**
	 * Public method to export a grid data to csv data.
	 * Can use the ExportCommand. If this command is not set uses _setSQL to set the query.
	 * The number of rows exported is limited from gSQLMaxRows variable
	 * @see _setSQL
	 * @param array $summary - set which columns should be sumarized in order to be displayed to the grid
	 * By default this parameter uses SQL SUM function: array("colmodelname"=>"sqlname");
	 * It can be set to use the other one this way
	 * array("colmodelname"=>array("sqlname"=>"AVG"));
	 * By default the first field correspond to the name of colModel the second to
	 * the database name
	 * @param array $params parameter values passed to the sql array(value)
	 * @param array $colmodel - different description for the headers
	 * @see rs2excel
	 * @param boolean $echo determines if the result should be returned or echoed
	 * @param string $filename the filename to which the sheet can be saved in case if $echo is true
	 * @param string $sep - the separator for the csv data
	 * @param string $sepreplace - with what to replace the separator if in data
	 * @return string
	 */
	public function exportToCsv(array $summary=null,array $params=null, array $colmodel=null, $echo=true, $filename='exportdata.csv', $sep=';', $sepreplace=' ')
	{
		$sql = null;
		if($this->dbtype == 'adodb') {
			$this->pdo->SetFetchMode(ADODB_FETCH_NUM);
		}
		$sql = $this->_rs($params, $summary, false);
		if ($sql) {
			if(!$this->CSV['file']) {
				$this->CSV['file'] = isset($filename) && strlen($filename) > 0 ? $filename : $this->csvfile;
			}
			if(!$this->CSV['separator']) {
				$this->CSV['separator'] = isset($sep) && strlen($sep) > 0  ? $sep : $this->csvsep;
			}
			if(!$this->CSV['sepreplace']) {
				$this->CSV['sepreplace'] = isset($sepreplace) &&  strlen($sepreplace) > 0 ? $sepreplace : $this->csvsepreplace;
			}
			//rs2csv($rs, $colmodel, $sep=',', $sepreplace=',', $echo=false, $addtitles=true, $quote = '"', $escquote = '"', $replaceNewLine = ' ')
			$ret = $this->rs2csv($sql, $colmodel, $this->CSV['separator'], $this->CSV['sepreplace'], $echo, $this->CSV['file'], $this->CSV['addtitles'], $this->CSV['quote'], $this->CSV['escquote'], $this->CSV['replaceNewLine']);
			jqGridDB::closeCursor($sql);
			return $ret;
		} else {
			return "Error:Could not execute the query";
		}
	}

	/**
	 * Convert a recordeset to pdf object
	 * @param object $rs the recorde set object from the query
	 * @param pdf created object $pdf
	 * @param array $colmodel can be either manually created array see rs2excel or genereted
	 * from setColModel methd.
	 * @return null
	 */
	protected function rs2pdf($rs, &$pdf, $colmodel=false, $summary=null)
	{
		$s ='';$rows=0;
		$gSQLMaxRows = $this->gSQLMaxRows; // max no of rows to download

		if (!$rs) {
			printf('Bad Record set rs2pdf');
			return false;
		}
		$typearr = array();
		$ncols = jqGridDB::columnCount($rs);
		$model = false;
		$nmodel = is_array($colmodel) ? count($colmodel) : -1;
		// find the actions collon
		if($nmodel > 0) {
			for ($i=0; $i < $nmodel; $i++) {
				if($colmodel[$i]['name']=='actions') {
					array_splice($colmodel, $i, 1);
					$nmodel--;
					break;
				}
			}
		}
		switch ($this->dbtype) {
			case 'oci8':
			case 'db2':
			case 'sqlsrv':
			case 'odbcsqlsrv':
				$nmodel++;
			break;
		}
		if($colmodel && $nmodel== $ncols) {
			$model = true;
		}
		$ahidden = array();
		$aselect = array();
		$totw = 0;
		$pw = $pdf->getPageWidth();
		$margins  = $pdf->getMargins();
		$pw = $pw - $margins['left']-$margins['right'];
		for ($i=0; $i < $ncols; $i++) {
			if(isset($colmodel[$i])) {
			$ahidden[$i] = ($model && isset($colmodel[$i]["hidden"])) ? $colmodel[$i]["hidden"] : false;
			$colwidth[$i] = ($model && isset($colmodel[$i]["width"])) ? (int)$colmodel[$i]["width"] : 150;
			if($ahidden[$i]) { continue; }
			$totw = $totw+$colwidth[$i];
		}
		}
		$pd = $this->PDF;
		// header

		$pdf->SetLineWidth(0.2);

		$field = array();
		$fnmkeys = array();

		function colorToDec( $pdfl, $str) {
			if(method_exists($pdfl,'convertHTMLColorToDec')) {
				return $pdfl->convertHTMLColorToDec(  $str );
			} else {
				$asp = $pdfl->getAllSpotColors();
				return TCPDF_COLORS::convertHTMLColorToDec( $str, $asp );
			}
		}

		function printTHeader($ncols, $maxheigh, $awidth, $aname, $ahidden, $pdf, $pd)
		{
			$pdf->SetFillColorArray(colorToDec( $pdf, $pd['grid_head_color']));
			$pdf->SetTextColorArray(colorToDec($pdf, $pd['grid_head_text_color']));
			$pdf->SetDrawColorArray(colorToDec($pdf, $pd['grid_draw_color']));
			$pdf->SetFont('', 'B');
			for ($i=0; $i < $ncols; $i++) {
				if($ahidden[$i]) {
					continue;
				}
				if(!$pd['shrink_header']) {
					$pdf->MultiCell($awidth[$i], $maxheigh, $aname[$i], 1, 'C', true, 0, '', '', true, 0, true, true, 0, 'B', false);
				} else {
					$pdf->Cell($awidth[$i], $pd['grid_header_height'], $aname[$i], 1, 0, 'C', 1, '', 1);
				}
			}
		}
		$maxheigh = $pd['grid_header_height'];
		for ($i=0; $i < $ncols; $i++) {
			// hidden columns
			$aselect[$i] = false;
			if($model && isset($colmodel[$i]) && isset($colmodel[$i]["formatter"])) {
				if($colmodel[$i]["formatter"]=="select") {
					$asl = isset($colmodel[$i]["formatoptions"]) ? $colmodel[$i]["formatoptions"] : (isset($colmodel[$i]["editoptions"]) ? $colmodel[$i]["editoptions"] : array());
					if(isset($asl["value"])) {
						$sep = isset($asl["separator"]) ? $asl["separator"] : ":";
						$delim = isset($asl["delimiter"]) ? $asl["delimiter"] : ";";
						$list = explode( $delim ,$asl["value"]);
						foreach($list as $key=>$val) {
							$items = explode( $sep ,$val);
							$aselect[$i][$items[0]] = $items[1];
						}
					}
				}
			}
			$fnmkeys[$i] = "";
			if($model && isset($colmodel[$i])) {
				$fname[$i] = isset($colmodel[$i]["label"]) ? $colmodel[$i]["label"] : $colmodel[$i]["name"];
				$typearr[$i] = isset($colmodel[$i]["sorttype"]) ? $colmodel[$i]["sorttype"] : '';
				$align[$i] = isset($colmodel[$i]["align"]) ? strtoupper(substr($colmodel[$i]["align"],0,1)) : "L";
			} else {
				$field = jqGridDB::getColumnMeta($i,$rs);
				$fname[$i] = $field["name"];
				$typearr[$i] = jqGridDB::MetaType($field, $this->dbtype);
				$align[$i] = "L";
			}
			if($fname[$i] == 'jqgrid_row') {
				$ahidden[$i] = true;
			}
			if($ahidden[$i]) {
				continue;
			}
			$fname[$i] = htmlspecialchars($fname[$i]);
			$fnmkeys[$i] = $model ? $colmodel[$i]["name"] : $fname[$i];
			$colwidth[$i]= ($colwidth[$i]/$totw)*100;
			$colwidth[$i] = ($pw/100)*$colwidth[$i];
			if (strlen($fname[$i])==0) { $fname[$i] = ''; }
			if(!$pd['shrink_header']) {
				$maxheigh = max($maxheigh, $pdf->getStringHeight($colwidth[$i], $fname[$i], false, true, '', 1) );
			} 
			//$maxheigh = $pd['grid_header_height'];
			//$pdf->Cell($colwidth[$i], $pd['grid_header_height'], $fname[$i], 1, 0, 'C', 1);
		}
		printTHeader($ncols, $maxheigh, $colwidth, $fname, $ahidden, $pdf, $pd);
		$pdf->Ln();
		//Hack for mysqli driver
		if($this->dbtype == 'mysqli') {
			$fld = $rs->field_count;
			//start the count from 1. First value has to be a reference to the stmt. because bind_param requires the link to $stmt as the first param.
			//$count = 1;
			$fieldnames[0] = &$rs;
			for ($i=0;$i<$fld;$i++) {
				$fieldnames[$i+1] = &$res_arr[$i]; //load the fieldnames into an array.
			}
			call_user_func_array('mysqli_stmt_bind_result', $fieldnames);
		}
		$datefmt = $this->userdateformat;
		$timefmt = $this->usertimeformat;

		$pdf->SetFillColorArray(colorToDec( $pdf, $pd['grid_row_color']));
		$pdf->SetTextColorArray(colorToDec( $pdf, $pd['grid_row_text_color']));
		$pdf->SetFont('');

		$fill = false;
		if(!$pd['shrink_cell']) {
			$dimensions = $pdf->getPageDimensions();
		}
		while ($r = jqGridDB::fetch_num($rs, $this->pdo))
		{
			if($this->dbtype == 'mysqli') { 
				$r = $res_arr;
			}
			$varr = array();
			$maxh = $pd['grid_row_height'];
			for ($i=0; $i < $ncols; $i++)
			{
				if(isset($ahidden[$i]) && $ahidden[$i]) continue;
				$v = $r[$i];
				if(is_array($aselect[$i])) {
					if(isset($aselect[$i][$v])) {
						$v1 = $aselect[$i][$v];
						if($v1)  $v = $v1;
					}
					$typearr[$i] = 'string';
				}
				$type = $typearr[$i];
				switch($type) {
				case 'date':
					$v = $datefmt != $this->dbdateformat ? jqGridUtils::parseDate($this->dbdateformat, $v, $datefmt) : $v;
					break;
				case 'datetime':
					$v = $timefmt != $this->dbtimeformat ? jqGridUtils::parseDate($this->dbtimeformat,$v,$timefmt) : $v;
					break;
				case 'numeric':
				case 'int':
					$v = trim($v);
					break;
				default:
					$v = trim($v);
					if (strlen($v) == 0) { $v = ''; }
				}
				if(!$pd['shrink_cell'])  {
					$varr[$i] = $v;
					$maxh = max($maxh, $pdf->getStringHeight($colwidth[$i], $v, false, true, '', 1) );
				} else {
					$pdf->Cell($colwidth[$i], $pd['grid_row_height'], $v, 1, 0,$align[$i], $fill,'',1);
				}
			} // for
			if(!$pd['shrink_cell'])  {
				$startY = $pdf->GetY();
				if (($startY + $maxh) + $dimensions['bm'] > ($dimensions['hk'])) {
					//this row will cause a page break, draw the bottom border on previous row and give this a top border
					//we could force a page break and rewrite grid headings here
					$pdf->AddPage();
					if($pd['reprint_grid_header']) {
						printTHeader($ncols, $maxheigh, $colwidth, $fname, $ahidden, $pdf, $pd);
						$pdf->Ln();
						$pdf->SetFillColorArray(colorToDec( $pdf, $pd['grid_row_color']));
						$pdf->SetTextColorArray(colorToDec( $pdf, $pd['grid_row_text_color']));
						$pdf->SetFont('');
					}
				}
				for ($i=0; $i < $ncols; $i++) {
					if(isset($ahidden[$i]) && $ahidden[$i]) { continue; }
					$pdf->MultiCell($colwidth[$i], $maxh, $varr[$i], 1, $align[$i], $fill, 0, '', '', true, 0, true, true, 0, 'T', false);
				}
			}
			if($pd['grid_alternate_rows']) {
				$fill=!$fill;
			}
			$pdf->Ln();
			$rows += 1;
			if ($rows >= $gSQLMaxRows) {
				break;
			} // switch
		} // while
		if($this->tmpvar) {
			$pdf->SetFont('', 'B');
			for ($i=0; $i < $ncols; $i++)
			{
				if(isset($ahidden[$i]) && $ahidden[$i]) { continue; }
				$vv = '';
				foreach($this->tmpvar as $key=>$v) {
					if($fnmkeys[$i]==$key) {
						$vv = $v;
						break;
					}
				}
				$pdf->Cell($colwidth[$i],  $pd['grid_row_height'], $vv, 1, 0,$align[$i], $fill,'',1);
			}
		}
	}

	protected function rs2pdf2( &$pdf, $grpinfo=null)
	{
		$ncols = count($grpinfo->columns);
		
		$totw = 0;
		$pw = $pdf->getPageWidth();
		$margins  = $pdf->getMargins();
		$pw = $pw - $margins['left']-$margins['right'];
		$pd = $this->PDF;
		// header

		$pdf->SetLineWidth(0.2);
		
		function colorToDec2( $pdfl, $str) {
			if(method_exists($pdfl,'convertHTMLColorToDec')) {
				return $pdfl->convertHTMLColorToDec(  $str );
			} else {
				$asp = $pdfl->getAllSpotColors();
				return TCPDF_COLORS::convertHTMLColorToDec( $str, $asp );
			}
		}
		
		function printTHeader2($ncols, $maxheigh, $grinfo, $pdf, $pd)
		{
			$pdf->SetFillColorArray(colorToDec2($pdf, $pd['grid_head_color']));
			$pdf->SetTextColorArray(colorToDec2($pdf, $pd['grid_head_text_color']));
			$pdf->SetDrawColorArray(colorToDec2($pdf, $pd['grid_draw_color']));
			$pdf->SetFont('', 'B');
			for ($i=0; $i < $ncols; $i++) {
				if($grinfo->columns[$i]['hidden']) {
					continue;
				}
				if(!$pd['shrink_header']) {
					$pdf->MultiCell($grinfo->columns[$i]['width'], $maxheigh, $grinfo->columns[$i]['label'], 1, 'C', true, 0, '', '', true, 0, true, true, 0, 'B', false);
				} else {
					$pdf->Cell($grinfo->columns[$i]['width'], $pd['grid_header_height'], $grinfo->columns[$i]['label'], 1, 0, 'C', 1, '', 1);
				}
			}
		}
		$totw = 0;
		$maxheigh = $pd['grid_header_height'];
		for ($i=0; $i < $ncols; $i++) {
			// hidden columns
			if($grpinfo->columns[$i]['name'] == 'jqgrid_row')
			{
				$grpinfo->columns[$i]['hidden']=true;
			}
			if($grpinfo->columns[$i]['hidden']) {
				continue;
			}
			$grpinfo->columns[$i]['width'] = ($grpinfo->columns[$i]['width']/$grpinfo->totalwidth)*$pw;
			$totw += $grpinfo->columns[$i]['width'];
			//$colwidth[$i]= ($colwidth[$i]/$totw)*100;
			//$colwidth[$i] = ($pw/100)*$colwidth[$i];
			if(!$pd['shrink_header']) {
				$maxheigh = max($maxheigh, $pdf->getStringHeight($colwidth[$i], $grpinfo->columns[$i]['label'], false, true, '', 1) );
			} 
		}
		printTHeader2($ncols, $maxheigh, $grpinfo, $pdf, $pd);
		$pdf->Ln();
		//Hack for mysqli driver
		$datefmt = $this->userdateformat;
		$timefmt = $this->usertimeformat;

		$pdf->SetFillColorArray(colorToDec2($pdf, $pd['grid_row_color']));
		$pdf->SetTextColorArray(colorToDec2($pdf, $pd['grid_row_text_color']));
		$pdf->SetFont('');

		$fill = false;
		$dimensions = array();
		if(!$pd['shrink_cell']) {
			$dimensions = $pdf->getPageDimensions();
		}
		$toEnd = 0;
		$grpopt = $this->gridOptions['groupingView'];
		$len = count($grpopt['groupField']);
		$sumreverse = array();
		if(isset($grpopt['groupSummary'])) {
			$sumreverse =array_reverse($grpopt['groupSummary']);
		}
		foreach($grpinfo->groups as $i => $n){
			$toEnd++;
			$v = $n['value'];
			if(!isset($grpopt['groupText'][$n['idx']])) 
			{
				$gt = "{0}";
			} else {
				$gt = strip_tags($grpopt['groupText'][$n['idx']]);
			}
			$pdf->SetFont('', 'B');
			$pdf->Cell($totw, $pd['grid_row_height'], str_repeat('  ', $n['idx']).$this->js_template($gt, $v, $n['cnt'], $n['summary'] ), 1, 0,'L' , 0,'',1);
			//$s .= '<ss:Row><ss:Cell ss:MergeAcross="'.($ncols-$hiddencount-1).'" ss:StyleID="headcol"><ss:Data ss:Type="String">'. str_repeat('&nbsp;&nbsp;', $n['idx']).$this->js_template($gt, $v, $n['cnt'], $n['summary'] ).'</ss:Data></ss:Cell></ss:Row>';
			$leaf = $len-1 === $n['idx'];
			$pdf->Ln();		
		//while ($r = jqGridDB::fetch_num($rs, $this->pdo))
		//{
			if($leaf) {
				if(isset($grpinfo->groups[$i+1])) {
					$gg = $grpinfo->groups[$i+1];
					$end = $gg['startRow'];
				} else {
					$gg = false;
					$end = $this->groupdata->count();
				}
				$pdf->SetFont('');
				foreach(new LimitIterator($this->groupdata, $n['startRow'], ($end-$n['startRow'])) as $row) 
				{
					$j=0;
					foreach ($row as $key => $r)
					{
						if($grpinfo->columns[$j]['hidden']==true) {
							$j++;
							continue;
						}
						$v = $r;
						if(is_array($grpinfo->columns[$j]['select'])) {
							if(isset($grpinfo->columns[$j]['select'][$v])) {
								$v1 = $grpinfo->columns[$j]['select'][$v];
								if($v1) { $v = $v1; }
							}
							$grpinfo->columns[$j]['type'] = 'string';
						}	

						$varr = array();
						$maxh = $pd['grid_row_height'];
						switch($grpinfo->columns[$j]['type']) {
							case 'date':
								$v = $datefmt != $this->dbdateformat ? jqGridUtils::parseDate($this->dbdateformat, $v, $datefmt) : $v;
								break;
							case 'datetime':
								$v = $timefmt != $this->dbtimeformat ? jqGridUtils::parseDate($this->dbtimeformat,$v,$timefmt) : $v;
								break;
							case 'numeric':
							case 'int':
								$v = trim($v);
								break;
							default:
								$v = trim($v);
							if (strlen($v) == 0) { $v = ''; }
						}
						if(!$pd['shrink_cell'])  {
							$varr[$key] = $v;
							$maxh = max($maxh, $pdf->getStringHeight($grpinfo->columns[$j]['width'], $v, false, true, '', 1) );
						} else {
							$pdf->Cell($grpinfo->columns[$j]['width'], $pd['grid_row_height'], $v, 1, 0,$grpinfo->columns[$j]['align'], $fill,'',1);
						}
						$j++;
					}
					if(!$pd['shrink_cell'])  {
						$startY = $pdf->GetY();
						if (($startY + $maxh) + $dimensions['bm'] > ($dimensions['hk'])) {
							//this row will cause a page break, draw the bottom border on previous row and give this a top border
							//we could force a page break and rewrite grid headings here
							$pdf->AddPage();
							if($pd['reprint_grid_header']) {
								printTHeader2($ncols, $maxheigh, $grpinfo, $pdf, $pd);
								$pdf->Ln();
								$pdf->SetFillColorArray(colorToDec2($pdf, $pd['grid_row_color']));
								$pdf->SetTextColorArray(colorToDec2($pdf, $pd['grid_row_text_color']));
								$pdf->SetFont('');
							}
						}
						for ($ii=0; $ii < $ncols; $ii++) {
							if($grpinfo->columns[$ii]['hidden']==true) continue;
							$pdf->MultiCell($grpinfo->columns[$ii]['width'], $maxh, $varr[$ii], 1, $grpinfo->columns[$ii]['align'], $fill, 0, '', '', true, 0, true, true, 0, 'T', false);
						}
					}
					if($pd['grid_alternate_rows']) {
						$fill=!$fill;
					}
					$pdf->Ln();
				} // iterator
				
				// footers
				if($gg) {
					for($jj=0;$jj<$len;$jj++) {
						if($gg['dataIndex']===$this->gridOptions['groupingView']['groupField'][$jj]) {
							break;
						}
					}
					$toEnd = $len - $jj;
				}
				for($ik=0; $ik<$toEnd;$ik++) {
					if(isset($sumreverse[$ik]) && !$sumreverse[$ik]) { continue; }
					$fdata = $this->findGroupIdx($i, $ik, $grpinfo->groups);
					$grlen = $fdata['cnt'];
					for ($ij=0; $ij < $ncols; $ij++) {
						if($grpinfo->columns[$ij]['hidden']==true) {
							continue;
						}
						$v = '';
						$tplfld = "{0}";
						foreach($fdata['summary'] as $sk=>$sv) {
							if($sv['nm'] == $this->colModel[$ij]['name']) {
								if(isset($this->colModel[$ij]['summaryTpl'])) {
									$tplfld = $this->colModel[$ij]['summaryTpl'];
								}
								if(is_string($sv['st']) && strtolower($sv['st']) == 'avg') {
									if(isset($sv['sd']) && (float)$sv['vd'] != 0) {
										$sv['v'] = $sv['v'] / $sv['vd'];
									} else if($sv['v'] && $grlen > 0) {
										$sv['v'] = $sv['v'] / $grlen;
									}
								}
								if(isset($this->colModel[$ij]['summaryRound'])) {
									$sv['v'] = round($sv['v'], $this->colModel[$ij]['summaryRound']);
								}
								$v = $this->js_template($tplfld, $sv['v']);
								break;
							}
						}
						$pdf->Cell($grpinfo->columns[$ij]['width'], $pd['grid_row_height'], $v, 1, 0,$grpinfo->columns[$ij]['align'], 0,'',1);
					}
					$pdf->Ln();
				}
				$toEnd = $jj;				
				
			} //leaf
		} // groupinfo
		if($this->tmpvar) {
			$pdf->SetFont('', 'B');
			for ($i=0; $i < $ncols; $i++)
			{
				if($grpinfo->columns[$i]['hidden']==true) continue;
				$vv = '';
				foreach($this->tmpvar as $key=>$v) {
					if($grpinfo->columns[$i]['name']==$key) {
						$vv = $v;
						break;
					}
				}
				$pdf->Cell($grpinfo->columns[$i]['width'], $pd['grid_row_height'], $vv, 1, 0,$grpinfo->columns[$i]['align'], $fill,'',1);
			}
		}
	}
	/**
	 * Export the recordset to pdf file.
	 * Can use the ExportCommand. If this command is not set uses _setSQL to set the query.
	 * The number of rows exported is limited from gSQLMaxRows variable
	 * @see _setSQL
	 * @param array $summary - set which columns should be sumarized in order to be displayed to the grid
	 * By default this parameter uses SQL SUM function: array("colmodelname"=>"sqlname");
	 * It can be set to use the other one this way
	 * array("colmodelname"=>array("sqlname"=>"AVG"));
	 * By default the first field correspond to the name of colModel the second to
	 * the database name
	 * @param array $params parameter values passed to the sql array(value)
	 * @param array $colmodel - different description for the headers - see rs2excel
	 * @param string $filename the filename to which the sheet can be saved in case if $echo is true
	 * @return
	 */

	public function exportToPdf(array $summary=null,array $params=null, array $colmodel=null, $filename='exportdata.pdf')
	{
		$sql = null;
		global $l;
		$grouping = isset($this->gridOptions['grouping'])  && $this->gridOptions['grouping'] == true;
		if($this->dbtype == 'adodb') {
			if($grouping) {
				$this->pdo->SetFetchMode(ADODB_FETCH_ASSOC);
			} else {
				$this->pdo->SetFetchMode(ADODB_FETCH_NUM);
			}
		}
		$sql = $this->_rs($params, $summary);
		if ($sql) {
			$pd = $this->PDF;
			try {
			include($pd['path_to_pdf_class']);
			// create new PDF document
			$pdf = new TCPDF($pd['page_orientation'], $pd['unit'], $pd['page_format'], $pd['unicode'], $pd['encoding'], false);

	// set document information
			$pdf->SetCreator($pd['creator']);
			$pdf->SetAuthor($pd['author']);
			$pdf->SetTitle($pd['title']);
			$pdf->SetSubject($pd['subject']);
			$pdf->SetKeywords($pd['keywords']);
			//set margins
			$pdf->SetMargins($pd['margin_left'], $pd['margin_top'], $pd['margin_right']);
			$pdf->SetHeaderMargin($pd['margin_header']);
			$pdf->setHeaderFont(Array($pd['font_name_main'], '', $pd['font_size_main']));
			if($pd['header'] === true) {
				$pdf->SetHeaderData($pd['header_logo'], $pd['header_logo_width'], $pd['header_title'], $pd['header_string']);
			} else {
				$pdf->setPrintHeader(false);
			}

			$pdf->SetDefaultMonospacedFont($pd['font_monospaced']);

			$pdf->setFooterFont(Array($pd['font_name_data'], '', $pd['font_size_data']));
			$pdf->SetFooterMargin($pd['margin_footer']);
			if($pd['footer'] !== true) {
				$pdf->setPrintFooter(false);
			}
			$pdf->setImageScale($pd['image_scale_ratio']);
			$pdf->SetAutoPageBreak(TRUE, 17);

			//set some language-dependent strings
			$pdf->setLanguageArray($l);
			$pdf->AddPage();
			$pdf->SetFont($pd['font_name_data'], '', $pd['font_size_data']);
			
			if($grouping) {
				$groupinfo = $this->groupingSetup($sql, $colmodel);
				$ret = $this->rs2pdf2( $pdf, $groupinfo);
			} else {
				$ret = $this->rs2pdf($sql, $pdf, $colmodel, $summary);
			}
				jqGridDB::closeCursor($sql);

			//$this->rs2pdf($sql, $pdf, $colmodel, $summary);
			//jqGridDB::closeCursor($sql);
			if(!isset($pd['save_to_disk_only'])) {
				$pd['save_to_disk_only'] = false;
			}
			$file_t = $this->tmp_file_name($pd['disk_file']);
			$file_t .= ".pdf";
			if($pd['save_to_disk_only']===true ) {
				$pdf->Output($file_t, 'F');
				return;
			}
			if($pd['save_to_disk'] == true) {
				$pdf->Output($file_t, 'F');
			}
			$pdf->Output($filename, $pd['destination']);
			exit();
			} catch (Exception $e) {
				//echo "Error:".$e->getMessage();
				return false;
			}
		} else {
			return "Error:Could not execute the query";
		}
	}

	/**
	 *
	 * From a given recordset returns excel xml file. If the summary array is
	 * defined add summary formula at last row.
	 * Return well formated xml excel string
	 * @param pdo recordset $rs recordset from pdo execute command
	 * @param array $colmodel diffrent descriptions for the headars, width, hidden cols
	 * This array is actually a colModel array in jqGrid.
	 * The array can look like
	 * Array(
	 *      [0]=>Array("label"=>"Some label", "width"=>100, "hidden"=>true, "name"=>"client_id", "formatter"=>"select", editoptions=>...),
	 *      [1]=>Array("label"=>"Other label", "width"=>80, "hidden"=>false, "name"=>"date",... ),
	 *      ...
	 * )
	 * @param boolean $echo determines if the result should be send to browser or returned as string
	 * @param string $filename filename to which file can be saved
	 * @param array $summary - set which columns should be sumarized in order to be displayed to the grid
	 * By default this parameter uses SQL SUM function: array("colmodelname"=>"sqlname");
	 * It can be set to use the other one this way
	 * array("colmodelname"=>array("sqlname"=>"AVG"));
	 * By default the first field correspond to the name of colModel the second to
	 * the database name
	 * @return string
	 */
	protected function rs2excel($rs, $colmodel=false, $echo = true, $filename='exportdata.xls', $summary=false)
	{
		$s ='';$rows=0;
		$gSQLMaxRows = $this->gSQLMaxRows; // max no of rows to download

		if (!$rs) {
			printf('Bad Record set rs2excel');
			return false;
		}
		$typearr = array();
		$ncols = jqGridDB::columnCount($rs);
		$hdr = '<?xml version="1.0" encoding="'.$this->encoding.'"?>';
		$hdr .='<?mso-application progid="Excel.Sheet"?>';
		$hdr .=  '<ss:Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">';

		// bold the header
		$hdr .= '<ss:Styles>'
			// header style
			.'<ss:Style ss:ID="1"><ss:Font ss:Bold="1"/></ss:Style>'
			// Short date style
			.'<ss:Style ss:ID="sd"><NumberFormat ss:Format="Short Date"/></ss:Style>'
			// long date format
			.'<ss:Style ss:ID="ld"><NumberFormat ss:Format="General Date"/></ss:Style>'
			// numbers
			.'<ss:Style ss:ID="nmb"><NumberFormat ss:Format="Standard"/></ss:Style>'
			// integers
			.'<ss:Style ss:ID="intnm"><NumberFormat ss:Format="General Number"/></ss:Style>'
			.'</ss:Styles>';
			//define the headers
		$hdr .= '<ss:Worksheet ss:Name="Sheet1">';
		$hdr .= '<ss:Table>';
		// if we have width definition set it
		$nmodel = is_array($colmodel) ? count($colmodel) : -1;
		// find the actions collon
		if($nmodel > 0) {
			for ($i=0; $i < $nmodel; $i++) {
				if($colmodel[$i]['name']=='actions') {
					array_splice($colmodel, $i, 1);
					$nmodel--;
					break;
				}
			}
		}
		switch ($this->dbtype) {
			case 'oci8':
			case 'db2':
			case 'sqlsrv':
			case 'odbcsqlsrv':
				$nmodel++;
			break;
		}		
		$model = false;
		if($colmodel && $nmodel== $ncols) {
			$model = true;
		}
		$hdr1 = '<ss:Row ss:StyleID="1">';
		$aSum = array();
		$aFormula = array();
		$ahidden = array();
		$aselect = array();
		$hiddencount = 0;
		for ($i=0; $i < $ncols; $i++) {
			// hidden columns
			$ahidden[$i] = ($model && isset($colmodel[$i]["hidden"])) ? $colmodel[$i]["hidden"] : false;
			$aselect[$i] = false;
			if($model && isset($colmodel[$i]["formatter"])) {
				if($colmodel[$i]["formatter"]=="select") {
					$asl = isset($colmodel[$i]["formatoptions"]) ? $colmodel[$i]["formatoptions"] : (isset($colmodel[$i]["editoptions"]) ? $colmodel[$i]["editoptions"] : array());
					if(isset($asl["value"])) {
						$sep = isset($asl["separator"]) ? $asl["separator"] : ":";
						$delim = isset($asl["delimiter"]) ? $asl["delimiter"] : ";";
						$list = explode( $delim ,$asl["value"]);
						foreach($list as $key=>$val) {
							$items = explode( $sep ,$val);
							$aselect[$i][$items[0]] = $items[1];
						}
					}
				}
			}
			// width
			$column = ($model && isset($colmodel[$i]["width"])) ? (int)$colmodel[$i]["width"] : 0;
			// pixel to point conversion
			if( $column > 0 ) {$column = $column*72/96; $hdr .= '<ss:Column ss:Width="'.$column.'"/>'; }
			else $hdr .= '<ss:Column ss:AutoFitWidth="1"/>';
			//names
			$field = array();
			if($model && isset($colmodel[$i]) ) {
				$fname = isset($colmodel[$i]["label"]) ? $colmodel[$i]["label"] : $colmodel[$i]["name"];
				$field["name"] = $colmodel[$i]["name"];
				$typearr[$i] = isset($colmodel[$i]["sorttype"]) ? $colmodel[$i]["sorttype"] : '';
			} else {
				$field = jqGridDB::getColumnMeta($i,$rs);
				$fname = $field["name"];
				$typearr[$i] = jqGridDB::MetaType($field, $this->dbtype);
			}
			if($field["name"] == "jqgrid_row") {
				$ahidden[$i] = true;
			}
			if($ahidden[$i]) {
				$hiddencount++;
				continue;
			}
			if($summary && is_array($summary)) {
				foreach($summary as $key => $val)
				{
					if(is_array($val)) {
						foreach($val as $fld=>$formula) {
							if ($field["name"] == $key ){
								$aSum[] = $i-$hiddencount;
								$aFormula[] = $formula;
							}
						}
					} else {
						if ($field["name"] == $key ){
							$aSum[] = $i-$hiddencount;
							$aFormula[] = "SUM";
						}
					}
				}
			}
			$fname = htmlspecialchars($fname);
			if (strlen($fname)==0) $fname = '';
			$hdr1 .= '<ss:Cell><ss:Data ss:Type="String">'.$fname.'</ss:Data></ss:Cell>';
		}
		$hdr1 .= '</ss:Row>';
		if (!$echo) $html = $hdr.$hdr1;
		//Hack for mysqli driver
		if($this->dbtype == 'mysqli') {
			$fld = $rs->field_count;
			//start the count from 1. First value has to be a reference to the stmt. because bind_param requires the link to $stmt as the first param.
			$count = 1;
			$fieldnames[0] = &$rs;
			for ($i=0;$i<$fld;$i++) {
				$fieldnames[$i+1] = &$res_arr[$i]; //load the fieldnames into an array.
			}
			call_user_func_array('mysqli_stmt_bind_result', $fieldnames);
		}
		while ($r = jqGridDB::fetch_num($rs, $this->pdo)) {
			if($this->dbtype == 'mysqli') { 
				$r = $res_arr;
			}
			$s .= '<ss:Row>';
			for ($i=0; $i < $ncols; $i++)
			{
				if(isset($ahidden[$i]) && $ahidden[$i]) continue;
				$v = $r[$i];
				if(is_array($aselect[$i])) {
					if(isset($aselect[$i][$v])) {
						$v1 = $aselect[$i][$v];
						if($v1)  $v = $v1;
					}
					$typearr[$i] = 'string';
				}
				switch($typearr[$i]) {
				case 'date':
					if(substr($v,0,4) == '0000' || empty($v) || $v=='NULL') {
						$v='1899-12-31T00:00:00.000';
						$s .= '<ss:Cell ss:StyleID="sd"><ss:Data ss:Type="DateTime">'.$v.'</ss:Data></ss:Cell>';
					} else if (!strpos($v,':')) {
						$v .= "T00:00:00.000";
						$s .= '<ss:Cell ss:StyleID="sd"><ss:Data ss:Type="DateTime">'.$v.'</ss:Data></ss:Cell>';
					} else {
						$thous = substr($v, -4);
						if( strpos($thous, ".") === false && strpos($v, ".") === false ) $v .= ".000";
						$s .= '<ss:Cell ss:StyleID="sd"><ss:Data ss:Type="DateTime">'.str_replace(" ","T",trim($v)).'</ss:Data></ss:Cell>';
					}
					break;
				case 'datetime':
					if(substr($v,0,4) == '0000' || empty($v) || $v=='NULL') {
						$v = '1899-12-31T00:00:00.000';
						$s .= '<ss:Cell ss:StyleID="ld"><ss:Data ss:Type="DateTime">'.$v.'</ss:Data></ss:Cell>';
					} else {
						$thous = substr($v, -4);
						if( strpos($thous, ".") === false && strpos($v, ".") === false) { $v .= ".000"; }
						$s .= '<ss:Cell ss:StyleID="ld"><ss:Data ss:Type="DateTime">'.str_replace(" ","T",trim($v)).'</ss:Data></ss:Cell>';
					}
					break;
				case 'int':
					$s .= '<ss:Cell ss:StyleID="intnm"><ss:Data ss:Type="Number">'.stripslashes((trim($v))).'</ss:Data></ss:Cell>';
					break;
				case 'numeric':
					$s .= '<ss:Cell ss:StyleID="nmb"><ss:Data ss:Type="Number">'.stripslashes((trim($v))).'</ss:Data></ss:Cell>';
					break;
				default:
					$v = htmlspecialchars(trim($v));
					if (strlen($v) == 0) $v = '';
					$s .= '<ss:Cell><ss:Data ss:Type="String">'.stripslashes($v).'</ss:Data></ss:Cell>';
				}
			} // for
			$s .= '</ss:Row>';

			$rows += 1;
			if ($rows >= $gSQLMaxRows) {
				break;
			} // switch
		} // while
		if(count($aSum)>0 && $rows > 0)
		{
			$s .= '<Row>';
			foreach($aSum as $ind => $ival)
			{
				$s .= '<Cell ss:StyleID="1" ss:Index="'.($ival+1).'" ss:Formula="='.$aFormula[$ind].'(R[-'.($rows).']C:R[-1]C)"><Data ss:Type="Number"></Data></Cell>';
			}
			$s .= '</Row>';
		}
		$s  = $hdr.$hdr1.$s.'</ss:Table></ss:Worksheet></ss:Workbook>';
		$es = $this->getExcelOptions();
		if(!isset($es['save_to_disk_only'])) {
			$es['save_to_disk_only'] = false;
		}
		if($es['save_to_disk_only']===true ) {
			$ret = $this->save_to_file($es['disk_file'], $s);
			if($ret !== true) {
				$this->errorMessage = $ret;
				$this->sendErrorHeader();
			}
			return;
		}
		if($es['save_to_disk'] === true) {
			$this->save_to_file($es['disk_file'], $s);
		}
		
		if ($echo) {
			header('Content-Type: application/ms-excel;');
			header("Content-Disposition: attachment; filename=".$filename);
			//echo $hdr.$hdr1;
			echo $s ;// . '</ss:Table></ss:Worksheet></ss:Workbook>';
		} else {
			//$html .= $s .'</ss:Table></ss:Worksheet></ss:Workbook>';
			return $s;
		}
	}

	/**
	 *  Convert records set from grid to excel implemeting the grouping 
	 * @param type $echo boolean set if true the result is echoed
	 * @param type $filename string the file nae when exporting
	 * @param type $grpinfo group info
	 * @return string xml
	 */
	protected function rs2excel2( $echo = true, $filename='exportdata.xls', $grpinfo=null)
	{
		$s ='';

		$ncols = count($grpinfo->columns);
		////jqGridDB::columnCount($rs);
		$hdr = '<?xml version="1.0" encoding="'.$this->encoding.'"?>';
		$hdr .='<?mso-application progid="Excel.Sheet"?>';
		$hdr .=  '<ss:Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet" xmlns:html="http://www.w3.org/TR/REC-html40">';
		// bold the header
		$hdr .= '<ss:Styles>'
			// header style
			.'<ss:Style ss:ID="1"><ss:Font ss:Bold="1"/></ss:Style>'
			// Short date style
			.'<ss:Style ss:ID="sd"><NumberFormat ss:Format="Short Date"/></ss:Style>'
			// long date format
			.'<ss:Style ss:ID="ld"><NumberFormat ss:Format="General Date"/></ss:Style>'
			// numbers
			.'<ss:Style ss:ID="nmb"><NumberFormat ss:Format="Standard"/></ss:Style>'
			// integers
			.'<ss:Style ss:ID="intnm"><NumberFormat ss:Format="General Number"/></ss:Style>'
			.'<ss:Style ss:ID="headcol"><ss:Font ss:Bold="1"/></ss:Style>'
			.'<ss:Style ss:ID="Default" ss:Name="Normal"> <Alignment ss:Vertical="Bottom"/><Borders/><Interior/><NumberFormat/><Protection/></ss:Style>'				
			.'</ss:Styles>';
			//define the headers
		$hdr .= '<ss:Worksheet ss:Name="Sheet1">';
		$hdr .= '<ss:Table>';
		// if we have width definition set it
		$hdr1 = '<ss:Row ss:StyleID="1">';
		$hiddencount = 0;
		for ($i=0; $i < $ncols; $i++) {
			// hidden columns
			//var_dump($grpinfo->columns[$i]['hidden'], $i);
			if($grpinfo->columns[$i]['name'] == 'jqgrid_row')
			{
				$grpinfo->columns[$i]['hidden']=true;
			}
			if($grpinfo->columns[$i]['hidden']==true) {
				$hiddencount++;
				continue;
			}
			// width
			// pixel to point conversion
			if( $grpinfo->columns[$i]['width'] > 0 ) {
				$hdr .= '<ss:Column ss:Width="'.((int)$grpinfo->columns[$i]['width']*72/96).'"/>'; 
			} else { 
				$hdr .= '<ss:Column ss:AutoFitWidth="1"/>';
			}
			//names
			$fname = htmlspecialchars($grpinfo->columns[$i]['label']);
			if (strlen($fname)==0) { $fname = ''; }
			$hdr1 .= '<ss:Cell><ss:Data ss:Type="String">'.$fname.'</ss:Data></ss:Cell>';
		}
		$hdr1 .= '</ss:Row>';
		if (!$echo) { $html = $hdr.$hdr1; }
		$toEnd = 0;
		$grpopt = $this->gridOptions['groupingView'];
		$len = count($grpopt['groupField']);
		$sumreverse = array();
		if(isset($grpopt['groupSummary'])) {
			$sumreverse =array_reverse($grpopt['groupSummary']);
		}
		foreach($grpinfo->groups as $i => $n){
			$toEnd++;
			$v = $n['value'];
			if(!isset($grpopt['groupText'][$n['idx']])) 
			{
				$gt = "{0}";
			} else {
				$gt = $grpopt['groupText'][$n['idx']];
			}
			$s .= '<ss:Row><ss:Cell ss:MergeAcross="'.($ncols-$hiddencount-1).'" ss:StyleID="headcol"><ss:Data ss:Type="String">'. str_repeat('&nbsp;&nbsp;', $n['idx']).$this->js_template($gt, $v, $n['cnt'], $n['summary'] ).'</ss:Data></ss:Cell></ss:Row>';
			$leaf = $len-1 === $n['idx'];
			if($leaf) {
				if(isset($grpinfo->groups[$i+1])) {
					$gg = $grpinfo->groups[$i+1];
					$end = $gg['startRow'];
				} else {
					$gg = false;
					$end = $this->groupdata->count();
				}
				// body records
				foreach(new LimitIterator($this->groupdata, $n['startRow'], ($end-$n['startRow'])) as $row) {
					$s .= '<ss:Row>';
					$j=0;
					foreach ($row as $key => $r)
					{
						if($grpinfo->columns[$j]['hidden']==true) {
							$j++;
							continue;
						}
						$v = $r;
						if(is_array($grpinfo->columns[$j]['select'])) {
							if(isset($grpinfo->columns[$j]['select'][$v])) {
								$v1 = $grpinfo->columns[$j]['select'][$v];
								if($v1)  { $v = $v1; }
							}
							$grpinfo->columns[$j]['type'] = 'string';
						}	
						switch($grpinfo->columns[$j]['type']) {
						case 'date':
							if(substr($v,0,4) == '0000' || empty($v) || $v=='NULL') {
								$v='1899-12-31T00:00:00.000';
								$s .= '<ss:Cell ss:StyleID="sd"><ss:Data ss:Type="DateTime">'.$v.'</ss:Data></ss:Cell>';
							} else if (!strpos($v,':')) {
								$v .= "T00:00:00.000";
								$s .= '<ss:Cell ss:StyleID="sd"><ss:Data ss:Type="DateTime">'.$v.'</ss:Data></ss:Cell>';
							} else {
								$thous = substr($v, -4);
								if( strpos($thous, ".") === false && strpos($v, ".") === false ) $v .= ".000";
								$s .= '<ss:Cell ss:StyleID="sd"><ss:Data ss:Type="DateTime">'.str_replace(" ","T",trim($v)).'</ss:Data></ss:Cell>';
							}
							break;
						case 'datetime':
							if(substr($v,0,4) == '0000' || empty($v) || $v=='NULL') {
								$v = '1899-12-31T00:00:00.000';
								$s .= '<ss:Cell ss:StyleID="ld"><ss:Data ss:Type="DateTime">'.$v.'</ss:Data></ss:Cell>';
							} else {
								$thous = substr($v, -4);
								if( strpos($thous, ".") === false && strpos($v, ".") === false) $v .= ".000";
								$s .= '<ss:Cell ss:StyleID="ld"><ss:Data ss:Type="DateTime">'.str_replace(" ","T",trim($v)).'</ss:Data></ss:Cell>';
							}
							break;
						case 'int':
							$s .= '<ss:Cell ss:StyleID="intnm"><ss:Data ss:Type="Number">'.stripslashes($v).'</ss:Data></ss:Cell>';
							break;
						case 'numeric':
							$s .= '<ss:Cell ss:StyleID="nmb"><ss:Data ss:Type="Number">'.stripslashes($v).'</ss:Data></ss:Cell>';
							break;
						default:
							$v = htmlspecialchars(trim($v));
							if (strlen($v) == 0) { $v = ''; }
							$s .= '<ss:Cell><ss:Data ss:Type="String">'.stripslashes($v).'</ss:Data></ss:Cell>';
						}
						$j++;
					} // for
					$s .= '</ss:Row>';
				} // body iterator
				// footers
				$jj = 0;
				if($gg) {
					for($jj=0;$jj<$len;$jj++) {
						if($gg['dataIndex']===$this->gridOptions['groupingView']['groupField'][$jj]) {
							break;
						}
					}
					$toEnd = $len - $jj;
				}
				for($ik=0; $ik<$toEnd;$ik++) {
					if(isset($sumreverse[$ik]) &&  !$sumreverse[$ik]) { continue; }
					$s .= '<ss:Row>';
					$fdata = $this->findGroupIdx($i, $ik, $grpinfo->groups);
					$grlen = $fdata['cnt'];
					for ($ij=0; $ij < $ncols; $ij++) {
						if($grpinfo->columns[$ij]['hidden']==true) {
							continue;
						}
						$tmpdata = '<ss:Cell><ss:Data ss:Type="String"></ss:Data></ss:Cell>';
						$tplfld = "{0}";
						foreach($fdata['summary'] as $sk=>$sv) {
							if($sv['nm'] == $this->colModel[$ij]['name']) {
								if(isset($this->colModel[$ij]['summaryTpl'])) {
									$tplfld = $this->colModel[$ij]['summaryTpl'];
								}
								if(is_string($sv['st']) && strtolower($sv['st']) == 'avg') {
									if(isset($sv['sd']) && (float)$sv['vd'] != 0) {
										$sv['v'] = $sv['v'] / $sv['vd'];
									} else if($sv['v'] && $grlen > 0) {
										$sv['v'] = $sv['v'] / $grlen;
									}
								}
								if(isset($this->colModel[$ij]['summaryRound'])) {
									$sv['v'] = round($sv['v'], $this->colModel[$ij]['summaryRound']);
								}
								$styleid = "Default";
								
								if(is_integer($sv['v'])) {
									$styleid = "intnum";
								} else if(is_float ($sv['v'])) {
									$styleid = "nmb";
								}
								$tv = $this->js_template($tplfld, $sv['v']);
								$stype = is_numeric($tv) ? 'Number' : 'String';
								$tmpdata = '<ss:Cell ss:StyleID="'.$styleid.'"><ss:Data ss:Type="'.$stype.'">'.$tv.'</ss:Data></ss:Cell>';
								break;
							}
						}
						$s .= $tmpdata;
					}
					$s .= '</ss:Row>';
				}
				$toEnd = $jj;
			} // leaf
		} // groups
		// free memory brefore output
		$this->groupdata = null;
		if($this->tmpvar) {
			$s .= '<ss:Row>';
			for ($i=0; $i < $ncols; $i++)
			{
				if($grpinfo->columns[$i]['hidden']==true) {
					continue;
				}

				$vv = '';
				foreach($this->tmpvar as $key=>$v) {
					if($grpinfo->columns[$i]['name']==$key) {
						$vv = $v;
						break;
					}
				}
				if($vv) {
					$s .= '<ss:Cell ss:StyleID="nmb"><ss:Data ss:Type="Number">'.stripslashes($vv).'</ss:Data></ss:Cell>';
				} else {
					$s .= '<ss:Cell><ss:Data ss:Type="String"></ss:Data></ss:Cell>';
				}
			}
			$s .= '</ss:Row>';
		}
		$s  = $hdr.$hdr1.$s.'</ss:Table></ss:Worksheet></ss:Workbook>';
		$es = $this->getExcelOptions();
		if(!isset($es['save_to_disk_only'])) {
			$es['save_to_disk_only'] = false;
		}
		if($es['save_to_disk_only']===true ) {
			$ret = $this->save_to_file($es['disk_file'], $s);
			if($ret !== true) {
				$this->errorMessage = $ret;
				$this->sendErrorHeader();
			}
			return;
		}
		if($es['save_to_disk'] === true) {
			$this->save_to_file($es['disk_file'], $s);
		}

		if ($echo) {
			header('Content-Type: application/ms-excel;');
			header("Content-Disposition: attachment; filename=".$filename);
			//echo $hdr.$hdr1;
			echo $s;// . '</ss:Table></ss:Worksheet></ss:Workbook>';
		} else {
			//$html .= $s .'</ss:Table></ss:Worksheet></ss:Workbook>';
			return $s;
		}
	}
	/**
	 * Set properties in excel worksheet using the $EXCEL array definitions
	 * @param object $objPHPExcel created php excel object
	 */
	protected function setExcelProp( $objPHPExcel) {
		$es = $this->getExcelOptions();
		$objPHPExcel->getDefaultStyle()->getFont()->setName($es['font'])->setSize($es['font_size']);
		$objPHPExcel->getProperties()->setCreator($es['creator'])
							 ->setLastModifiedBy($es['author'])
							 ->setTitle($es['title'])
							 ->setSubject($es['subject'])
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords($es['keywords']);
		if($es['protect'] === true) {
			$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);	// Needs to be set to true in order to enable any worksheet protection!
			$objPHPExcel->getActiveSheet()->protectCells($es['start_cell'].":".$es['end_cell'], $es['password']);
		}
	}
	/**
	 * Send the excel file to a browser 
	 * @param type $objPHPExcel
	 */
	protected function saveExcel( $objPHPExcel ) {
		$es = $this->getExcelOptions();
		$fn = $es['file'] !== '' ? $es['file'] : $this->exportfile;
		if(!isset($es['save_to_disk_only'])) {
			$es['save_to_disk_only'] = false;
		}
		$filename = $this->tmp_file_name($es['disk_file']);
		switch ($es['file_type']) {
			case 'Excel2007':
				$ext = pathinfo($fn, PATHINFO_EXTENSION);
				if($ext != 'xlsx') {
					$fn .= '.xlsx'; 
				}
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
				if($es['save_to_disk_only']===true ) {
					$objWriter->save($filename);
					return;
				}
				if($es['save_to_disk'] === true) {
					$objWriter->save($filename);
				}
				// Redirect output to a client’s web browser (Excel2007)
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename='.$fn);
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');
				// If you're serving to IE over SSL, then the following may be needed
				header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
				header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header ('Pragma: public'); // HTTP/1.0
				$objWriter->save('php://output');
				break;
			case 'Excel5':
				$ext = pathinfo($fn, PATHINFO_EXTENSION);
				if($ext != 'xls') {
					$fn .= '.xls'; 
				}
				$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
				if($es['save_to_disk_only']===true ) {
					$objWriter->save($filename);
					return;
				}
				if($es['save_to_disk'] === true) {
					$objWriter->save($filename);
				}
				// Redirect output to a client’s web browser (Excel5)
				header('Content-Type: application/vnd.ms-excel');
				header('Content-Disposition: attachment;filename='.$fn);
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');

				// If you're serving to IE over SSL, then the following may be needed
				header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
				header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header ('Pragma: public'); // HTTP/1.0
				$objWriter->save('php://output');
				break;
			default:
				break;
		}
	}
	/**
	 * Return default type formating defined in global options
	 * @param string $e_type field type
	 * @param array $e_opt exceloptions array containing default format string
	 * @return string formatting
	 */
	protected function excel_fmt( $e_type, $e_opt ) {
		$ret = '';
		switch ($e_type) {
			case 'int':
				$ret = $e_opt['format_int'];
				break;
			case 'numeric':
				$ret = $e_opt['format_num'];
				break;
			case 'text':
				$ret = $e_opt['format_text'];
				break;
			case 'date':
				$ret = $e_opt['format_date'];
				break;
			default:
				$ret = $e_opt['format_text'];
				break;
		}
		return $ret;
	}
	
	/**
	 * Build the excel object (ready to export) using phpexcel lib
	 * @param object $rs the record set build from grid
	 * @param array $colmodel column model for the grid
	 * @return boolean
	 */
	public function rs2phpexcel($rs, $colmodel=false )
	{
		$rows=0;
		$gSQLMaxRows = $this->gSQLMaxRows; // max no of rows to download

		if (!$rs) {
			printf('Bad Record set rs2excel');
			return false;
		}
		$es = $this->getExcelOptions();
		/** Include PHPExcel */
		try { 
			require_once $es['path_to_phpexcel_class'];

			$objPHPExcel = new PHPExcel();
			$objWorksheet = $objPHPExcel->getActiveSheet();
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
				
		$typearr = array();
		$ncols = jqGridDB::columnCount($rs);
		// if we have width definition set it
		$nmodel = is_array($colmodel) ? count($colmodel) : -1;
		// find the actions collon
		if($nmodel > 0) {
			for ($i=0; $i < $nmodel; $i++) {
				if($colmodel[$i]['name']=='actions') {
					array_splice($colmodel, $i, 1);
					$nmodel--;
					break;
				}
			}
		}
		switch ($this->dbtype) {
			case 'oci8':
			case 'db2':
			case 'sqlsrv':
			case 'odbcsqlsrv':
				$nmodel++;
			break;
		}		
		$model = false;
		if($colmodel && $nmodel== $ncols) {
			$model = true;
		}
		$aSum = array();
		$aFormula = array();
		$ahidden = array();
		$aselect = array();
		$hiddencount = 0;
		$fmtstr = array();
		$fnmkeys =array();
		list ($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($es['start_cell']);
		$currentColumn = $startColumn;
		if($es['header_title']) {
			$ht = $es['header_cell'] ? $es['header_cell'] : $currentColumn.$startRow;
			$objWorksheet->setCellValue($ht, $es['header_title']);
			//++$currentColumn;
			if(!$es['header_cell']) {
				++$startRow;
			}
		}
		for ($i=0; $i < $ncols; $i++) {
			//names
			$field = array();
			$fnmkeys[$i] = "";
			if($model && isset($colmodel[$i]) ) {
				$fname = isset($colmodel[$i]["label"]) ? $colmodel[$i]["label"] : $colmodel[$i]["name"];
				$field["name"] = $colmodel[$i]["name"];
				$typearr[$i] = isset($colmodel[$i]["sorttype"]) ? $colmodel[$i]["sorttype"] : '';
			} else {
				$field = jqGridDB::getColumnMeta($i,$rs);
				$fname = $field["name"];
				$typearr[$i] = jqGridDB::MetaType($field, $this->dbtype);
			}
			// hidden columns
			$ahidden[$i] = ($model && isset($colmodel[$i]["hidden"])) ? $colmodel[$i]["hidden"] : false;
			$aselect[$i] = false;
			if($model && isset($colmodel[$i]["formatter"])) {
				$cfmt = $colmodel[$i]["formatter"];
				$asl = isset($colmodel[$i]["formatoptions"]) ? $colmodel[$i]["formatoptions"] : (isset($colmodel[$i]["editoptions"]) ? $colmodel[$i]["editoptions"] : array());
				switch ($cfmt) {
					case "select":
						if(isset($asl["value"])) {
							$sep = isset($asl["separator"]) ? $asl["separator"] : ":";
							$delim = isset($asl["delimiter"]) ? $asl["delimiter"] : ";";
							$list = explode( $delim ,$asl["value"]);
							foreach($list as $key=>$val) {
								$items = explode( $sep ,$val);
								$aselect[$i][$items[0]] = $items[1];
							}
						}
						break;
					case "date" :
						if(isset($asl['newformat']) && $asl['newformat'] !== '') {
							$fmtstr[$i]  = $asl['newformat'];
						} else {
							if($typearr[$i] == 'date') {
								$fmtstr[$i] = $this->getUserDate();
							} else  {
								$fmtstr[$i] = $this->getUserTime();
							}
						}
						$fmtstr[$i] = jqGridUtils::phpToExcelDate($fmtstr[$i] );
						break;
				}
			}
			$fmtstr[$i] = !isset($fmtstr[$i]) ? $this->excel_fmt($typearr[$i], $es) : $fmtstr[$i];
			if(isset($colmodel[$i]["formatoptions"])) {
				$cmfo = $colmodel[$i]["formatoptions"];
				if( isset($cmfo['excel_format']) ) {
					$fmtstr[$i] = $cmfo['excel_format'];
				}
			}
			if($field["name"] == "jqgrid_row") {
				$ahidden[$i] = true;
			}
			if($ahidden[$i]) {
				$hiddencount++;
				continue;
			}
			$fnmkeys[$i] = $model ? $colmodel[$i]["name"] : $fname[$i];
			// width
			$objWorksheet->setCellValue($currentColumn . $startRow, $fname);
			$objWorksheet->getColumnDimension($currentColumn)->setAutoSize(true);
			++$currentColumn;
		}
		$objWorksheet->getStyle($es['start_cell'].':'.$currentColumn.$startRow)->getFont()->setBold(true);
		++$startRow;
		
		
		//Hack for mysqli driver
		if($this->dbtype == 'mysqli') {
			$fld = $rs->field_count;
			//start the count from 1. First value has to be a reference to the stmt. because bind_param requires the link to $stmt as the first param.
			//$count = 1;
			$fieldnames[0] = &$rs;
			for ($i=0;$i<$fld;$i++) {
				$fieldnames[$i+1] = &$res_arr[$i]; //load the fieldnames into an array.
			}
			call_user_func_array('mysqli_stmt_bind_result', $fieldnames);
		}
		while ($r = jqGridDB::fetch_num($rs, $this->pdo)) {
			$currentColumn = $startColumn;
			if($this->dbtype == 'mysqli') {
				$r = $res_arr;
			}
			for ($i=0; $i < $ncols; $i++)
			{
				if(isset($ahidden[$i]) && $ahidden[$i]) {
					continue;
				}
				$v = $r[$i];
				if(is_array($aselect[$i])) {
					if(isset($aselect[$i][$v])) {
						$v1 = $aselect[$i][$v];
						if($v1)  {
							$v = $v1;
						}
					}
					$typearr[$i] = 'string';
				}
				switch($typearr[$i]) {
				case 'date':
				case 'datetime':
					if(substr($v,0,4) == '0000' || empty($v) || $v=='NULL') {
						$v='1970-01-01';
					}
					$objWorksheet->setCellValue($currentColumn . $startRow, PHPExcel_Shared_Date::stringToExcel($v));
					break;
				case 'int':
				case 'numeric':
					$objWorksheet->setCellValueExplicit($currentColumn . $startRow, stripslashes((trim($v))), PHPExcel_Cell_DataType::TYPE_NUMERIC);
					break;
				default:
					$objWorksheet->setCellValueExplicit($currentColumn . $startRow, $v, PHPExcel_Cell_DataType::TYPE_STRING);				
				}
				if($fmtstr[$i]) {
					$objWorksheet->getStyle($currentColumn . $startRow)->getNumberFormat()->setFormatCode( $fmtstr[$i] );
				}
				++$currentColumn;
			} // for
			++$startRow;
			$rows += 1;
			if ($rows >= $gSQLMaxRows) {
				break;
			} // switch
		} // while
		// Summary data here
		$currentColumn = $startColumn;
		if($this->tmpvar) {
			for ($i=0; $i < $ncols; $i++)
			{
				if(isset($ahidden[$i]) && $ahidden[$i]) { continue; }
				$vv = '';
				foreach($this->tmpvar as $key=>$v) {
					if($fnmkeys[$i]==$key) {
						$vv = $v;
						break;
					}
				}
				$objWorksheet->setCellValue($currentColumn . $startRow, $vv);
				++$currentColumn;
				//$pdf->Cell($colwidth[$i],  $pd['grid_row_height'], $vv, 1, 0,$align[$i], $fill,'',1);
			}
		}
		$this->setExcelOptions(array("end_cell"=>$currentColumn . $startRow));
		$this->setExcelProp($objPHPExcel);
		if($this->excelClass) {
			try {
				$objPHPExcel = call_user_func(array($this->excelClass,$this->excelFunc), $objPHPExcel);
			} catch (Exception $e) {
				echo "Can not call the method class - ".$e->getMessage();
			}
		} else if(function_exists($this->excelFunc)) {
				$objPHPExcel = call_user_func($this->excelFunc,$objPHPExcel);
		}
		
		$this->saveExcel($objPHPExcel);
		return true;
	}

	/**
	 *  Convert records set from grid to excel implemeting the grouping 
	 * @param type $echo boolean set if true the result is echoed
	 * @param type $filename string the file nae when exporting
	 * @param type $grpinfo group info
	 * @return string xml
	 */
	public function rs2phpexcel2( $echo = true, $filename='exportdata.xls', $grpinfo=null)
	{
		$s ='';

		$ncols = count($grpinfo->columns);
		$es = $this->getExcelOptions();
		/** Include PHPExcel */
		try { 
			require_once $es['path_to_phpexcel_class'];

			$objPHPExcel = new PHPExcel();
			$objWorksheet = $objPHPExcel->getActiveSheet();
		} catch (Exception $e) {
			echo $e->getMessage();
			exit();
		}
		list ($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($es['start_cell']);
		$currentColumn = $startColumn;
		if($es['header_title']) {
			$ht = $es['header_cell'] ? $es['header_cell'] : $currentColumn.$startRow;
			$objWorksheet->setCellValue($ht, $es['header_title']);
			//++$currentColumn;
			if(!$es['header_cell']) {
				++$startRow;
			}
		}
		$fmtstr = array();
		$hiddencount = 0;
		for ($i=0; $i < $ncols; $i++) {
			// hidden columns
			if($grpinfo->columns[$i]['name'] == 'jqgrid_row')
			{
				$grpinfo->columns[$i]['hidden']=true;
			}
			if($grpinfo->columns[$i]['hidden']==true) {
				$hiddencount++;
				continue;
			}
			if(isset($grpinfo->columns[$i]['formatter']) && $grpinfo->columns[$i]['formatter'] == 'date') {
				$asl = $grpinfo->columns[$i]['formatoptions'];
				if(isset($asl['newformat']) && $asl['newformat'] !== '') {
					$fmtstr[$i]  = $asl['newformat'];
				} else {
					if($grpinfo->columns[$i]['type'] == 'date') {
						$fmtstr[$i] = $this->getUserDate();
					} else  {
						$fmtstr[$i] = $this->getUserTime();
					}
				}
				$fmtstr[$i] = jqGridUtils::phpToExcelDate($fmtstr[$i] );
			}
			
			$fmtstr[$i] = !isset($fmtstr[$i]) ? $this->excel_fmt($grpinfo->columns[$i]['type'], $es) : $fmtstr[$i];
			if(isset($grpinfo->columns[$i]['formatoptions'])) {
				$cmfo = $grpinfo->columns[$i]['formatoptions'];
				if( isset($cmfo['excel_format']) ) {
					$fmtstr[$i] = $cmfo['excel_format'];
				}
			}

			//names
			$fname = $grpinfo->columns[$i]['label'];			
			$objWorksheet->setCellValue($currentColumn . $startRow, $fname);
			$objWorksheet->getColumnDimension($currentColumn)->setAutoSize(true);
			$maxColumn = $currentColumn;
			++$currentColumn;

		}
		$objWorksheet->getStyle($es['start_cell'].':'.$currentColumn.$startRow)->getFont()->setBold(true);
		
		++$startRow;
		$toEnd = 0;
		$grpopt = $this->gridOptions['groupingView'];
		$len = count($grpopt['groupField']);
		$sumreverse = array();
		if(isset($grpopt['groupSummary'])) {
			$sumreverse =array_reverse($grpopt['groupSummary']);
		}
		foreach($grpinfo->groups as $i => $n){
			$toEnd++;
			$v = $n['value'];
			if(!isset($grpopt['groupText'][$n['idx']])) 
			{
				$gt = "{0}";
			} else {
				$gt = $grpopt['groupText'][$n['idx']];
			}
			$currentColumn = $startColumn;
			$objWorksheet->setCellValue($currentColumn . $startRow, str_repeat('  ', $n['idx']).strip_tags($this->js_template($gt, $v, $n['cnt'], $n['summary'] )));
			$objWorksheet->getStyle($currentColumn.$startRow)->getFont()->setBold(true);
			$objWorksheet->mergeCells($currentColumn.$startRow.":".$maxColumn.$startRow);
			++$startRow;
			//$s .= '<ss:Row><ss:Cell ss:MergeAcross="'.($ncols-$hiddencount-1).'" ss:StyleID="headcol"><ss:Data ss:Type="String">'. str_repeat('&nbsp;&nbsp;', $n['idx']).$this->js_template($gt, $v, $n['cnt'], $n['summary'] ).'</ss:Data></ss:Cell></ss:Row>';
			$leaf = $len-1 === $n['idx'];
			if($leaf) {
				if(isset($grpinfo->groups[$i+1])) {
					$gg = $grpinfo->groups[$i+1];
					$end = $gg['startRow'];
				} else {
					$gg = false;
					$end = $this->groupdata->count();
				}
				// body records
				foreach(new LimitIterator($this->groupdata, $n['startRow'], ($end-$n['startRow'])) as $row) {
					//$s .= '<ss:Row>';
					$currentColumn = $startColumn;
					$j=0;
					foreach ($row as $key => $r)
					{
						if($grpinfo->columns[$j]['hidden']==true) {
							$j++;
							continue;
						}
						$v = $r;
						if(is_array($grpinfo->columns[$j]['select'])) {
							if(isset($grpinfo->columns[$j]['select'][$v])) {
								$v1 = $grpinfo->columns[$j]['select'][$v];
								if($v1)  { $v = $v1; }
							}
							$grpinfo->columns[$j]['type'] = 'string';
						}	
						switch($grpinfo->columns[$j]['type']) {
							case 'date':
							case 'datetime':
								if(substr($v,0,4) == '0000' || empty($v) || $v=='NULL') {
									$v='1970-01-01';
								}
								$objWorksheet->setCellValue($currentColumn . $startRow, PHPExcel_Shared_Date::stringToExcel($v));
								//$objWorksheet->getStyle($currentColumn . $startRow)->getNumberFormat()->setFormatCode( jqGridUtils::phpToExcelDate($fmtstr[$j] ));
								break;
							case 'int':
							case 'numeric':
								$objWorksheet->setCellValueExplicit($currentColumn . $startRow, stripslashes((trim($v))), PHPExcel_Cell_DataType::TYPE_NUMERIC);
								break;
							default:
								//$objWorksheet->setCellValue($currentColumn . $startRow, $v);
								$objWorksheet->setCellValueExplicit($currentColumn . $startRow, $v, PHPExcel_Cell_DataType::TYPE_STRING);
						}
						if($fmtstr[$j]) {
							$objWorksheet->getStyle($currentColumn . $startRow)->getNumberFormat()->setFormatCode( $fmtstr[$j] );
						}
						$j++;
						++$currentColumn;
					} // for
					//$s .= '</ss:Row>';
					++$startRow;
				} // body iterator
				// footers
				$jj = 0;
				if($gg) {
					for($jj=0;$jj<$len;$jj++) {
						if($gg['dataIndex']===$this->gridOptions['groupingView']['groupField'][$jj]) {
							break;
						}
					}
					$toEnd = $len - $jj;
				}
				for($ik=0; $ik<$toEnd;$ik++) {
					if(isset($sumreverse[$ik]) &&  !$sumreverse[$ik]) { continue; }
					//$s .= '<ss:Row>';
					$fdata = $this->findGroupIdx($i, $ik, $grpinfo->groups);
					$grlen = $fdata['cnt'];
					$currentColumn = $startColumn;
					for ($ij=0; $ij < $ncols; $ij++) {
						if($grpinfo->columns[$ij]['hidden']==true) {
							continue;
						}
						//$tmpdata = '';
						$tv ='';
						$tplfld = "{0}";
						//$stype = 'String';
						foreach($fdata['summary'] as $sk=>$sv) {
							if($sv['nm'] == $this->colModel[$ij]['name']) {
								if(isset($this->colModel[$ij]['summaryTpl'])) {
									$tplfld = $this->colModel[$ij]['summaryTpl'];
								}
								if(is_string($sv['st']) && strtolower($sv['st']) == 'avg') {
									if(isset($sv['sd']) && (float)$sv['vd'] != 0) {
										$sv['v'] = $sv['v'] / $sv['vd'];
									} else if($sv['v'] && $grlen > 0) {
										$sv['v'] = $sv['v'] / $grlen;
									}
								}
								if(isset($this->colModel[$ij]['summaryRound'])) {
									$sv['v'] = round($sv['v'], $this->colModel[$ij]['summaryRound']);
								}
								//$styleid = "Default";
								
								//if(is_integer($sv['v'])) {
									//$styleid = "intnum";
								//} else if(is_float ($sv['v'])) {
									//$styleid = "nmb";
								//}
								$tv = $this->js_template($tplfld, $sv['v']);
								//$stype = is_numeric($tv) ? 'Number' : 'String';
								//$tmpdata = '<ss:Cell ss:StyleID="'.$styleid.'"><ss:Data ss:Type="'.$stype.'">'.$tv.'</ss:Data></ss:Cell>';
								break;
							}
						}
						if(is_numeric($tv)) {
							$objWorksheet->setCellValueExplicit($currentColumn . $startRow, $tv, PHPExcel_Cell_DataType::TYPE_NUMERIC);
						} else {
							$objWorksheet->setCellValue($currentColumn . $startRow, $tv);
						}
						if($fmtstr[$ij]) {
							$objWorksheet->getStyle($currentColumn . $startRow)->getNumberFormat()->setFormatCode( $fmtstr[$ij] );
						}	
						//$s .= $tmpdata;
						++$currentColumn;
					}
					++$startRow;
					//$s .= '</ss:Row>';
				}
				$toEnd = $jj;
			} // leaf
		} // groups
		// free memory brefore output
		$this->groupdata = null;
		$currentColumn = $startColumn;
		if($this->tmpvar) {
			for ($i=0; $i < $ncols; $i++)
			{
				if($grpinfo->columns[$i]['hidden']==true) {
					continue;
				}
				$vv = '';
				foreach($this->tmpvar as $key=>$v) {
					if($grpinfo->columns[$i]['name']==$key) {
						$vv = $v;
						break;
					}
				}
				$objWorksheet->setCellValue($currentColumn . $startRow, $vv);
				if($fmtstr[$i]) {
					$objWorksheet->getStyle($currentColumn . $startRow)->getNumberFormat()->setFormatCode( $fmtstr[$i] );
				}				
				++$currentColumn;
				//$pdf->Cell($colwidth[$i],  $pd['grid_row_height'], $vv, 1, 0,$align[$i], $fill,'',1);
			}
		}
		$this->setExcelOptions(array("end_cell"=>$currentColumn . $startRow));
		$this->setExcelProp($objPHPExcel);
		if($this->excelClass) {
			try {
				$objPHPExcel = call_user_func(array($this->excelClass,$this->excelFunc), $objPHPExcel);
			} catch (Exception $e) {
				echo "Can not call the method class - ".$e->getMessage();
			}
		} else if(function_exists($this->excelFunc)) {
				$objPHPExcel = call_user_func($this->excelFunc,$objPHPExcel);
		}
		
		$this->saveExcel($objPHPExcel);
		return true;
	}

	/**
	 * Export the recordset to excel xml file.
	 * Can use the ExportCommand. If this command is not set uses _setSQL to set the query.
	 * The number of rows exported is limited from gSQLMaxRows variable
	 * @see _setSQL
	 * @param array $summary - set which columns should be sumarized in order to be displayed to the grid
	 * By default this parameter uses SQL SUM function: array("colmodelname"=>"sqlname");
	 * It can be set to use the other one this way
	 * array("colmodelname"=>array("sqlname"=>"AVG"));
	 * By default the first field correspond to the name of colModel the second to
	 * the database name
	 * @param array $params parameter values passed to the sql array(value)
	 * @param array $colmodel - different description for the headers - see rs2excel
	 * @param boolean $echo determines if the result should be returned or echoed
	 * @param string $filename the filename to which the sheet can be saved in case if $echo is true
	 * @return string
	 */
	public function exportToExcel(array $summary=null,array $params=null, array $colmodel=null,$echo = true, $filename='exportdata.xml')
	{
		$sql = null;
		$grouping = isset($this->gridOptions['grouping'])  && $this->gridOptions['grouping'] == true;
		if($this->dbtype == 'adodb') {
			if($grouping) {
				$this->pdo->SetFetchMode(ADODB_FETCH_ASSOC);
			} else {
				$this->pdo->SetFetchMode(ADODB_FETCH_NUM);
			}
		}
		$sql = $this->_rs($params, $summary);
		if (!$sql) {
			return "Error:Could not execute the query";
		}
		$es = $this->getExcelOptions();
		// autodetect grouping
		if($grouping) {
			$groupinfo = $this->groupingSetup($sql, $colmodel);
			jqGridDB::closeCursor($sql);
			if(strtolower($es['file_type']) == 'xml') {
				$ret = $this->rs2excel2( $echo, $filename, $groupinfo);
			} else {
				$ret = $this->rs2phpexcel2( $echo, $filename, $groupinfo);
			}
		} else {
			if(strtolower($es['file_type']) == 'xml') {
				$ret = $this->rs2excel($sql, $colmodel, $echo, $filename, $summary);
			} else {
				$ret = $this->rs2phpexcel($sql, $colmodel );
			}
			jqGridDB::closeCursor($sql);
		}
		return $ret;
	}
}
