<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: login.php"); exit(); }
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("UPDATE add_ins SET name = ?, price = ? WHERE addin_id = ?");
    $stmt->bind_param("sdi", $name, $price, $id);

    if ($stmt->execute()) {
        echo "<script>alert('Add-In updated successfully!'); window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Error updating add-in.'); window.location.href='admin.php';</script>";
    }
    $stmt->close();
} else { header("Location: admin.php"); }
$conn->close();
?>