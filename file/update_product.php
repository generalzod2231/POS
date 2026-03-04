<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Grab the data sent from the Admin Dashboard form
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    // 2. Update the new academic columns (base_price and product_id)
    $stmt = $conn->prepare("UPDATE products SET name = ?, base_price = ? WHERE product_id = ?");
    
    // "sdi" means: String (name), Double/Decimal (price), Integer (id)
    $stmt->bind_param("sdi", $name, $price, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Product updated successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error updating product: " . $conn->error . "'); window.location.href='admin.php';</script>";
    }

    $stmt->close();
} else {
    header("Location: admin.php");
}

$conn->close();
?>