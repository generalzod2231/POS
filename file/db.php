<?php
$conn = new mysqli("localhost", "root", "", "brew_bean");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
