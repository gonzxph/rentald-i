<?php 
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "carrental_db";

//E change lang ng port number og 3306(or onsa imo port) kay kanang 3308 akoa ng port number
$conn = new mysqli($servername, $username, $password, $dbname, 3308);

if ($conn->connect_error) {
    die("Connection failed " . $conn->connect_error);
    
}   



?>