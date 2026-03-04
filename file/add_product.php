<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Grab the values submitted from the dropdown menu
    $name = $_POST['name'];
    $price = $_POST['price'];
    $type_id = $_POST['type_id']; 

    // 2. Insert the specific type_id into the database
    // "sdi" tells MariaDB to expect a String (name), Double (price), and Integer (type_id)
    $stmt = $conn->prepare("INSERT INTO products (name, base_price, category_id, type_id, has_sizes) VALUES (?, ?, 1, ?, 1)");
    $stmt->bind_param("sdi", $name, $price, $type_id);

    if ($stmt->execute()) {
        echo "<script>alert('Product added successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error adding product: " . $conn->error . "'); window.location.href='admin.php';</script>";
    }

    $stmt->close();
} else {
    header("Location: admin.php");
}

$conn->close();
?>