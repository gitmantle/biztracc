<?php

//Our MySQL connection details.
define('MYSQL_SERVER', 'localhost');
define('MYSQL_DATABASE_NAME', 'test');
define('MYSQL_USERNAME', 'root');
define('MYSQL_PASSWORD', '');

//Instantiate the PDO object and connect to MySQL.
$pdo = new PDO(
        'mysql:host=' . MYSQL_SERVER . ';dbname=' . MYSQL_DATABASE_NAME, 
        MYSQL_USERNAME, 
        MYSQL_PASSWORD
);

//The name of the table that we want the structure of.
$tableToDescribe = 'users';

//Query MySQL with the PDO objecy.
//The SQL statement is: DESCRIBE [INSERT TABLE NAME]
$statement = $pdo->query('DESCRIBE ' . $tableToDescribe);

//Fetch our result.
$result = $statement->fetchAll(PDO::FETCH_ASSOC);

//The result should be an array of arrays,
//with each array containing information about the columns
//that the table has.
var_dump($result);

//For the sake of this tutorial, I will loop through the result
//and print out the column names and their types.
foreach($result as $column){
    echo $column['Field'] . ' - ' . $column['Type'], '<br>';
}

?>