<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "testdb"; // আগে XAMPP-এর phpMyAdmin থেকে এই নামে একটা DB বানিয়ে নাও

// কানেকশন তৈরি করো
$conn = new mysqli($servername, $username, $password, $database);

// কানেকশন চেক করো
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Database connected successfully!";
?>
