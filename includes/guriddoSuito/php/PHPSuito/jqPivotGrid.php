<?php
/**
 * @author  Tony Tomov, (tony@trirand.com)
 * @copyright TriRand Ltd
 * @version 5.3.1
 * @package jqPivotGrid
 *
 * @abstract
 * A PHP class to work with jqPivotGrid Tree.
 * The main purpose of this class is to provide the data from database to jqGrid,
 * The class requier a jqGrid classes to be loaded first.
 */
//require_once 'jqGrid.php';
/** PHPSuito root directory */
if (!defined('PHPSUITO_ROOT')) {
    define('PHPSUITO_ROOT', dirname(__FILE__) . '/');
    require(PHPSUITO_ROOT . 'Autoloader.php');
}

class jqPivotGrid extends jqGridRender
{
	private $pivotoptions =  array(
		'xDimension' => array(),
		'yDimension' => array(),
		'aggregates' => array()
	);
	private $ajaxoptions = array(
		"data" => array()
	);
	private $data;
	/**
	 * 
	 * Set varios options for the pivot grid
	 * @param array $aoptions
	 */
	public function setPivotOptions ( $aoptions ) 
	{
		if(is_array($aoptions)) {
			$this->pivotoptions = jqGridUtils::array_extend($this->pivotoptions,$aoptions);
		}
	}
	/**
	 * 
	 *  Set the xDimension array for the pivot grid
	 * @param array $param
	 */
	public function setxDimension( $param ) {
		if(is_array($param)) {
			$this->pivotoptions['xDimension'] = jqGridUtils::array_extend($this->pivotoptions['xDimension'],$param);
		}		
	}
	/**
	 * 
	 *  Set the xDimension array for the pivot grid
	 * @param array $param
	 */
	public function setyDimension( $param ) {
		if(is_array($param)) {
			$this->pivotoptions['yDimension'] = jqGridUtils::array_extend($this->pivotoptions['yDimension'],$param);
		}		
	}
	/**
	 * 
	 *  Set the aggregates array for the pivot grid
	 * @param array $param
	 */
	public function setaggregates( $param ) {
		if(is_array($param)) {
			$this->pivotoptions['aggregates'] = jqGridUtils::array_extend($this->pivotoptions['aggregates'],$param);
		}		
	}
	/**
	 * 
	 *  Set the ajax options for the pivot grid
	 * @param array $param
	 */
	public function setAjaxOptions( $param ) {
		if(is_array($param)) {
			$this->ajaxoptions = jqGridUtils::array_extend($this->ajaxoptions,$param);
		}		
	}
	public function setData( $mixdata ) {
		$this->data = $mixdata;
	}

	public function renderPivot($tblelement='', $pager='', $script=true, array $params=null, $createtbl=false, $createpg=false, $echo=true)
	{
		$goper = $this->oper ? $this->oper : 'nooper';
		if($goper == 'pivot') {
			$sql = null;
			$sqlId = $this->_setSQL();
			$this->dataType = 'json';
			$ret = $this->execute($sqlId, $params, $sql, false , 0, 0, '', '');
			if ($ret) {
				$result = new stdClass();
				$result->rows = jqGridDB::fetch_object($sql, true, $this->pdo);
				jqGridDB::closeCursor($sql);
				if($echo){
					$this->_gridResponse($result);
				} else {
					return $result;
				}
			} else {
				echo "Could not execute query!!!";
			}
		} else {
			$this->setAjaxOptions(array("data"=>array("oper"=>"pivot")));
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
			if(strlen($pager)>0) { 
				$this->setGridOptions(array("pager"=>$pager)); 
			}
			if($script) {
				$s .= "<script type='text/javascript'>";
				$s .= "jQuery(document).ready(function($) {";
			}
			$s .= "jQuery('".$tblelement."').jqGrid('jqPivot',".jqGridUtils::encode($this->data).",".jqGridUtils::encode($this->pivotoptions).",".jqGridUtils::encode($this->gridOptions).",".jqGridUtils::encode($this->ajaxoptions).");";
			if($this->navigator && strlen($pager)>0) {
				$this->setNavOptions('navigator', array('add'=>false, 'edit'=>false, 'del'=>false));
				$s .= "jQuery('".$tblelement."').bind('jqGridInitGrid.pivotGrid',(function(){jQuery('".$tblelement."').jqGrid('navGrid','".$pager."',".jqGridUtils::encode($this->navOptions);
				$s .= ",{},{},{},".jqGridUtils::encode($this->searchOptions);
				$s .= ",".jqGridUtils::encode($this->viewOptions).");}));";
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
