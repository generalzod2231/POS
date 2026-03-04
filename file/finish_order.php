<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if (isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $stmt = $conn->prepare("UPDATE orders SET status = 'completed' WHERE order_group_id = ?");
    $stmt->bind_param("s", $order_id);

    if ($stmt->execute()) {
        echo "<script>alert('Order finished successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error finishing the order.'); window.location.href='admin.php';</script>";
    }
    $stmt->close();
} else {
    header("Location: admin.php");
}
$conn->close();
?>