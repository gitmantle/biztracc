

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
</head>

<body>


<?php
session_start();

//date_default_timezone_set($_SESSION['s_timezone']);
date_default_timezone_set($_SESSION['s_timezone']);


$filename = '../../backups/bkup'.date('Y-m-d').'.sql';
$host = 'localhost';
$user = 'infinint_sagana';
$password = 'dun480can';
$database = 'infinint_fin40_19';



$fp = @fopen( $filename, 'w+' );
if( !$fp ) {

    echo 'Impossible to create <b>'. $filename .'</b>, please manually create one and assign it full write privileges: <b>777</b>';
    exit;
}
fclose( $fp );

$command = 'mysqldump -u '. $user .' -p'. $password .' --add-drop-database --databases '. $database .' > '. $filename;

exec( $command );

?>

</body>
</html>