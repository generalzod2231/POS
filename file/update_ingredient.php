<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $stock = $_POST['stock_quantity'];

    // Update just the stock quantity for that specific ingredient
    $stmt = $conn->prepare("UPDATE ingredients SET stock_quantity = ? WHERE id = ?");
    $stmt->bind_param("di", $stock, $id);

    if ($stmt->execute()) {
        header("Location: admin.php"); // Instantly refreshes the page
        exit();
    } else {
        echo "<script>alert('Error updating ingredient stock.'); window.location.href='admin.php';</script>";
    }
    
    $stmt->close();
}
$conn->close();
?>