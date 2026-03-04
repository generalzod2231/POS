<?php
include 'db.php';

$addins_query = $conn->query("SELECT name, price FROM add_ins ORDER BY name ASC");

if ($addins_query && $addins_query->num_rows > 0) {
    while($addin = $addins_query->fetch_assoc()) {
        $safeName = htmlspecialchars($addin['name']);
        $safePrice = (float)$addin['price'];
        
        echo '<button class="add-in-item" data-base="'.$safeName.'" onclick="toggleAddIn(this, \''.addslashes($safeName).'\', '.$safePrice.')">';
        echo '<span class="add-in-name">'.$safeName.'</span>';
        echo '</button>';
    }
} else {
    echo '<p style="grid-column: 1 / -1; text-align: center; color: #8C6A51; font-size: 13px;">No add-ins available.</p>';
}

$conn->close();
?>