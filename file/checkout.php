<?php
include 'db.php';

$json = file_get_contents('php://input');
$payload = json_decode($json, true); 

if (!empty($payload) && !empty($payload['items'])) {
    
    $order_type = $payload['type']; 
    $customer_name = $payload['customer']; 
    $cart = $payload['items']; 
    $order_group_id = 'ORD-' . mt_rand(1000, 9999); 

    // 1. Calculate Grand Total
    $total_order_price = 0;
    foreach ($cart as $item) {
        $total_order_price += isset($item['totalPrice']) ? $item['totalPrice'] : $item['basePrice'];
    }

    // 2. Insert into the main `orders` table
    $stmt_order = $conn->prepare("INSERT INTO orders (order_group_id, customer_name, order_type, total_price, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt_order->bind_param("sssd", $order_group_id, $customer_name, $order_type, $total_order_price);
    $stmt_order->execute();
    $order_id = $conn->insert_id; // Get the ID of the order we just made

    // Prepare statements for the next tables
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, size, quantity, subtotal) VALUES (?, ?, ?, 1, ?)");
    
    // --- STEP 2 FIX: Updated SQL to include the new modifier column ---
    $stmt_bridge = $conn->prepare("INSERT INTO order_item_addins (order_item_id, addin_id, modifier) VALUES (?, ?, ?)");

    foreach ($cart as $item) {
        $product_id = $item['id'];
        $size = isset($item['size']) ? $item['size'] : '';
        $subtotal = isset($item['totalPrice']) ? $item['totalPrice'] : $item['basePrice'];

        // 3. Insert the drink into `order_items`
        $stmt_item->bind_param("iisd", $order_id, $product_id, $size, $subtotal);
        $stmt_item->execute();
        $order_item_id = $conn->insert_id; // Get the ID of the drink we just inserted

        // 4. Connect Add-ins using the Bridge Table
        if (!empty($item['addIns'])) {
            foreach ($item['addIns'] as $addon) {
                $base_name = $addon['baseName']; 
                
                // --- STEP 2 FIX: Catch the modifier word from the POS frontend ---
                $modifier_word = isset($addon['modifier']) ? $addon['modifier'] : 'Add';
                
                // Find the ID of the add-in
                $res = $conn->query("SELECT addin_id FROM add_ins WHERE name = '$base_name' LIMIT 1");
                if ($res && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $addin_id = $row['addin_id'];
                    
                    // Link the drink to the add-in ALONG with the Extra/Lite/Add word
                    $stmt_bridge->bind_param("iis", $order_item_id, $addin_id, $modifier_word);
                    $stmt_bridge->execute();
                }
            }
        }
    }

    echo "Order sent to kitchen! " . $customer_name . "'s receipt printed.";
} else {
    echo "Error: Cart is empty.";
}
?>