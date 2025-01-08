<?php 
$host = 'localhost';
$db_name = 'carrental_db';
$username = 'root';
$password = '';


//E change lang ng port number og 3306(or onsa imo port) kay kanang 3308 akoa ng port number
$conn = new mysqli($host, $username, $password, $db_name, 3306);

if ($conn->connect_error) {
    die("Connection failed " . $conn->connect_error);
    
}   



?>