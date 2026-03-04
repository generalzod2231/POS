<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) { header("Location: login.php"); exit(); }
include 'db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM add_ins WHERE addin_id = ?");
    $stmt->bind_param("i", $id);

    try {
        if ($stmt->execute()) {
            echo "<script>alert('Add-in deleted successfully!'); window.location.href='admin.php';</script>";
        } else {
            echo "<script>alert('Error deleting add-in.'); window.location.href='admin.php';</script>";
        }
    } catch (mysqli_sql_exception $e) {
        // Safety net so it doesn't crash if the add-in was already sold to a customer
        echo "<script>alert('Cannot delete this add-in because it is part of an existing sale! Please clear your Sales Report first.'); window.location.href='admin.php';</script>";
    }
    $stmt->close();
} else { header("Location: admin.php"); }
$conn->close();
?>