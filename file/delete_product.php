<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Looking for the new academic column name: product_id
    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $id);

    try {
        if ($stmt->execute()) {
            echo "<script>alert('Product deleted successfully!'); window.location.href='admin.php';</script>";
        } else {
            echo "<script>alert('Error deleting product.'); window.location.href='admin.php';</script>";
        }
    } catch (mysqli_sql_exception $e) {
        // This catches the strict database rule! You cannot delete a product if it's currently sitting in the Sales Report.
        echo "<script>alert('Cannot delete this drink because it is part of an existing sale! Please clear your Sales Report first to delete this item.'); window.location.href='admin.php';</script>";
    }

    $stmt->close();
} else {
    header("Location: admin.php");
}

$conn->close();
?>