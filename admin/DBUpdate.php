<?php
class DBUpdate{
	
	public $db_template;
	public $db2update;

//**************************************************************
	function updatedb()
//**************************************************************
	{

	include_once("../includes/DBClass.php");
	$db = new DBClass();
	
	$template_tables = array();
	
	// get array of tables from template
	$db->query("show tables from ".$this->db_template);
	$i = 0;
	$rows = $db->resultset();
	foreach ($rows as $row) {
		$i = $i + 1;
		$template_table[$i] = $row[1];
		
		
	}


print_r($template_table);
	
	// iterate through array of template tables
	
	
	// find corresponding table in production database
	
	
	// if exists
	
	
		// create array of columns from template table
		
		
		
		// if column exists in production db update it
		
		
		// else create it
		
	
	// else create it
	
	
	

	$db->closeDB();
	
	}

}


?>