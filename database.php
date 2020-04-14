<?php


define("DSN", "mysql:host=localhost;dbname=testdb");
define("USERNAME", "root");
define("PASSWORD", "");


$options = array(PDO::ATTR_PERSISTENT => true);

try {
$conn = new PDO(DSN, USERNAME, PASSWORD, $options);

$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $ex) {
    echo "A database error has occured" ;
    echo $ex->getMessage();
}