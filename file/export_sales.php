<?php
session_start();

// Security check: ensure admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

include 'db.php';

// Set the headers to force the browser to download a CSV file
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=Brew_And_Bean_Sales_Report_' . date('Y-m-d') . '.csv');

// Open the output stream
$output = fopen('php://output', 'w');

// Output the column headers for Excel
fputcsv($output, array('Date & Time', 'Order ID', 'Name/Alyas', 'Order Type', 'Items Ordered', 'Total Value (PHP)'));

// Query to get all completed sales (Matches your admin.php logic perfectly)
$sales_sql = "
    SELECT 
        o.order_group_id, o.order_type, o.customer_name, o.order_date as sale_date, o.total_price as total_order_price,
        GROUP_CONCAT(CONCAT(p.name, IF(oi.size != '', CONCAT(' - ', oi.size), '')) SEPARATOR ', ') as item_names
    FROM orders o
    JOIN order_items oi ON o.order_id = oi.order_id
    JOIN products p ON oi.product_id = p.product_id
    WHERE o.status = 'completed'
    GROUP BY o.order_id
    ORDER BY o.order_date DESC
"; 

$sales_result = $conn->query($sales_sql);
$grand_total = 0;

// Loop through the data and write it to the CSV
if ($sales_result && $sales_result->num_rows > 0) {
    while ($sale = $sales_result->fetch_assoc()) {
        $grand_total += $sale['total_order_price']; // Add to grand total
        
        fputcsv($output, array(
            $sale['sale_date'],
            $sale['order_group_id'],
            $sale['customer_name'],
            $sale['order_type'],
            $sale['item_names'],
            number_format($sale['total_order_price'], 2, '.', '') // Standard number format for Excel math
        ));
    }
}

// Add an empty row for clean spacing
fputcsv($output, array('', '', '', '', '', ''));

// Add the final Grand Total row at the bottom!
fputcsv($output, array('', '', '', '', 'GRAND TOTAL IN DAY:', number_format($grand_total, 2, '.', '')));

// Close the file output
fclose($output);
exit();
?>