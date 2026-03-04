<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// 1. Delete the add-ins linked to completed orders (Bridge Table)
$conn->query("DELETE oia FROM order_item_addins oia 
              JOIN order_items oi ON oia.order_item_id = oi.order_item_id 
              JOIN orders o ON oi.order_id = o.order_id 
              WHERE o.status = 'completed'");

// 2. Delete the specific drinks linked to completed orders
$conn->query("DELETE oi FROM order_items oi 
              JOIN orders o ON oi.order_id = o.order_id 
              WHERE o.status = 'completed'");

// 3. Delete the completed orders themselves
$conn->query("DELETE FROM orders WHERE status = 'completed'");

echo "<script>alert('All sales history has been cleared!'); window.location.href='admin.php';</script>";

$conn->close();
?>