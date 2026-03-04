<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: login.php"); exit(); }
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("INSERT INTO add_ins (name, price) VALUES (?, ?)");
    $stmt->bind_param("sd", $name, $price);

    if ($stmt->execute()) {
        echo "<script>alert('Add-In added successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error adding add-in.'); window.location.href='admin.php';</script>";
    }
    $stmt->close();
} else { header("Location: admin.php"); }
$conn->close();
?>