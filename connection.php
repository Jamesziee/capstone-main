<?php
$host = 'localhost';
$dbname = 'capstone';
$username = 'root';
$password = '';

// Create a connection using MySQLi
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>