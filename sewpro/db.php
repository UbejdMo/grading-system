<?php
$servername = "localhost"; // Replace with your server name if different
$username = "root"; // Default username for XAMPP is 'root'
$password = ""; // Default password for XAMPP is empty
$dbname = "sewpro"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Konektimi Dështoi: " . $conn->connect_error);
}
?>
