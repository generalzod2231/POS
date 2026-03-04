<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php';

if (isset($_GET['date'])) {
    $date = $_GET['date'];

    // Find the specific Order ID matching that timestamp
    $stmt = $conn->prepare("SELECT order_id FROM orders WHERE order_date = ? AND status = 'completed'");
    $stmt->bind_param("s", $date);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $order_id = $row['order_id'];
        
        // Delete the add-ins, then the drinks, then the main order
        $conn->query("DELETE oia FROM order_item_addins oia JOIN order_items oi ON oia.order_item_id = oi.order_item_id WHERE oi.order_id = $order_id");
        $conn->query("DELETE FROM order_items WHERE order_id = $order_id");
        $conn->query("DELETE FROM orders WHERE order_id = $order_id");
    }

    echo "<script>alert('Sale record removed successfully!'); window.location.href='admin.php';</script>";
} else {
    header("Location: admin.php");
}

$conn->close();
?>