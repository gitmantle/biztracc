<?php
class DBConnection{
	function getConnection(){
		/*
	  //change to your database server/user name/password
		mysql_connect("localhost","root","dun480can") or
         die("Could not connect: " . mysql_error());
    //change to your database name
		mysql_select_db("jqcalendar") or 
		     die("Could not select database: " . mysql_error());
		*/
		
$dbuser = "root";
$moduledb = "jqcalendar";
$host = 'localhost';
define('DB_DSN','mysql:host='.$host.';dbname='.$moduledb);
define('DB_USER', $dbuser);     // Your MySQL username
define('DB_PASSWORD', 'dun480can'); // ...and password

define('ABSPATH', dirname(__FILE__).'/');		
		
			 
	}
}
?>