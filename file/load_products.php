<?php
include 'db.php';

$category = isset($_GET['category']) ? $_GET['category'] : 'All';
$category = $conn->real_escape_string(trim($category)); 

// The JOIN connects the Products to the Product_Type table for filtering
$sql = "SELECT p.product_id AS id, p.name, p.base_price AS price, p.has_sizes, c.category_name, t.type_name 
        FROM products p
        JOIN product_category c ON p.category_id = c.category_id
        LEFT JOIN product_type t ON p.type_id = t.type_id";

// Filters the menu when you click the Hot, Iced, or Blended buttons
if ($category !== 'All') {
    $sql .= " WHERE t.type_name = '$category'";
}

$result = $conn->query($sql);

if ($result && $result->num_rows > 0):
    while($row = $result->fetch_assoc()):
        
        $formatted_name = strtolower(str_replace(' ', '', $row['name']));
        $image_url = $formatted_name . '.png';
        
        $has_sizes = isset($row['has_sizes']) ? $row['has_sizes'] : 1;
        $click_action = "onclick=\"handleProductClick({$row['id']}, '" . addslashes($row['name']) . "', {$row['price']}, {$has_sizes})\"";
?>
    <div class="card" <?= $click_action ?>>
        <img src="<?= $image_url ?>" 
             alt="<?= $row['name'] ?>" 
             style="mix-blend-mode: multiply;"
             onerror="this.src='https://cdn-icons-png.flaticon.com/512/751/751621.png'">
        <h3><?= $row['name'] ?></h3>
        <div class="price">₱<?= number_format($row['price'], 2) ?></div>
        
        </div>
<?php 
    endwhile; 
else: 
?>
    <div style="grid-column: 1 / -1; text-align: center; padding: 50px; color: #8C6A51;">
        <h3>No drinks in this category yet!</h3>
    </div>
<?php endif; ?>