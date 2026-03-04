<?php 
session_start(); 
 
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
 
include 'db.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Brew & Bean</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* SCROLLING FIX */
        body { overflow: auto !important; height: auto !important; padding-bottom: 50px; }
        h1, h2 { text-align: center; margin-bottom: 20px; }
        .container { display: block; max-width: 1200px; margin: 0 auto; padding: 20px; overflow: visible !important; }
        header { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; }
        .header-buttons { display: flex; gap: 10px; }
        
        .logout-btn { background-color: #d9534f; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-size: 14px; font-weight: bold; }
        .logout-btn:hover { background-color: #c9302c; }
        .pos-btn { background-color: #5cb85c; color: white; text-decoration: none; padding: 8px 15px; border-radius: 5px; font-size: 14px; font-weight: bold; }
        .pos-btn:hover { background-color: #4cae4c; }
        .finish-btn { background-color: #3498db; color: white; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-weight: bold; font-size: 15px; box-shadow: 0 4px 6px rgba(52, 152, 219, 0.2); transition: 0.2s; display: block; text-align: center;}
        .finish-btn:hover { background-color: #2980b9; transform: translateY(-2px); }
 
        .delete-sale { color: #c0392b; text-decoration: none; font-weight: bold; }
        .delete-sale:hover { text-decoration: underline; }
        .clear-all-btn { background-color: #d9534f; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block; border: none; cursor: pointer; font-size: 16px; }
        .clear-all-btn:hover { background-color: #c9302c; }
        .sales-header-actions { text-align: right; margin-bottom: 15px; }
 
        .action-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; width: 100%; }
        .search-input { padding: 10px; width: 250px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
        .add-btn { background-color: #8C6A51; color: white; padding: 10px 15px; border-radius: 5px; font-weight: bold; border: none; cursor: pointer; font-size: 15px; }
        .add-btn:hover { background-color: #6d4c41; }
 
        .form-group { text-align: left; margin-bottom: 15px; }
        .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
        .form-group input[type="text"], .form-group input[type="number"], .form-group select { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; font-family: 'Poppins', sans-serif;}
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; } 
        
        /* --- KDS TICKET STYLING --- */
        .active-order-card { 
            background: #fff; 
            border: 1px solid #e0d5c1;
            border-left: 8px solid #8C6A51; 
            padding: 20px; 
            margin-bottom: 15px; 
            border-radius: 8px; 
            display: flex; 
            justify-content: space-between; 
            align-items: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.03);
        }
        .order-badge { background: #3e2723; color: white; padding: 4px 10px; border-radius: 6px; font-size: 12px; font-weight: bold; margin-left: 10px; letter-spacing: 0.5px; }
        
        .kds-items-container { margin-top: 15px; background: #faf8f5; padding: 15px; border-radius: 8px; border: 1px solid #f0e6d6;}
        .kds-item { margin-bottom: 12px; border-bottom: 1px dashed #d1c7b7; padding-bottom: 10px; }
        .kds-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
        .kds-drink { font-size: 18px; font-weight: 700; color: #3e2723; display: block; }
        .kds-addins { font-size: 14px; color: #c0392b; display: block; padding-left: 20px; margin-top: 4px; font-weight: 600; }

        .modal-overlay {
            display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0, 0, 0, 0.6); z-index: 1000;
            justify-content: center; align-items: center;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .modal-box {
            background: #fffcf5; padding: 30px; border-radius: 8px; text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2); transform: scale(0.5);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); 
        }
        .modal-overlay.show { display: flex; opacity: 1; }
        .modal-overlay.show .modal-box { transform: scale(1); }

        .modal-btn {
            padding: 10px 20px; margin: 10px 10px 0; border: none;
            border-radius: 5px; cursor: pointer; font-weight: bold; font-size: 16px;
        }
        .btn-yes { background-color: #d9534f; color: white; }
        .btn-yes:hover { background-color: #c9302c; }
        .btn-no { background-color: #bdc3c7; color: #333; }
        .btn-no:hover { background-color: #95a5a6; }
    </style>
</head>
<body>
 
<header>
    <div style="width: 150px;"></div>
    <div>Admin Dashboard</div>
    <div class="header-buttons">
        <a href="index.php" class="pos-btn">🛒 Go to POS Kiosk</a>
        <a href="logout.php" class="logout-btn">🚪 Logout</a>
    </div>
</header>
 
<div class="container">
 
    <div class="admin-tabs">
        <button class="tab-btn active" onclick="switchTab('active-orders-tab', this)">👨‍🍳 Active Orders</button>
        <button class="tab-btn" onclick="switchTab('inventory-tab', this)">📦 Menu Items</button>
        <button class="tab-btn" onclick="switchTab('addins-tab', this)">🥤 Add-Ins</button>
        <button class="tab-btn" onclick="switchTab('sales-tab', this)">💰 Sales Report</button>
    </div>
 
    <div id="active-orders-tab" class="admin-section active">
        <h1>👨‍🍳 Active Orders</h1>
        
        <div id="live-active-orders">
            <?php
            $active_sql = "
                SELECT 
                    o.order_id, o.order_group_id, o.order_type, o.customer_name, o.order_date as sale_date, o.total_price,
                    GROUP_CONCAT(
                        CONCAT(
                            p.name, 
                            IF(oi.size != '', CONCAT(' - ', oi.size), ''),
                            ' [₱', oi.subtotal, ']',
                            IFNULL((
                                SELECT CONCAT(' (', GROUP_CONCAT(CONCAT(oia.modifier, ' ', a.name, ' [+₱', a.price, ']') SEPARATOR ', '), ')')
                                FROM order_item_addins oia
                                JOIN add_ins a ON oia.addin_id = a.addin_id
                                WHERE oia.order_item_id = oi.order_item_id
                            ), '')
                        ) SEPARATOR '<br>'
                    ) as item_names
                FROM orders o
                JOIN order_items oi ON o.order_id = oi.order_id
                JOIN products p ON oi.product_id = p.product_id
                WHERE o.status = 'pending'
                GROUP BY o.order_id
                ORDER BY o.order_date ASC
            "; 
            
            $active_result = $conn->query($active_sql);
            if($active_result && $active_result->num_rows > 0):
                while($order = $active_result->fetch_assoc()):
                    $type_icon = ($order['order_type'] == 'Dine In') ? '🍽️ Dine In' : '🥡 Takeout';
            ?>
            <div class="active-order-card">
                <div style="flex: 1; padding-right: 20px;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 8px;">
                        <div style="display: flex; align-items: center;">
                            <h3 style="margin: 0; color: #d35400; font-size: 22px;">🗣️ <?= htmlspecialchars($order['customer_name']) ?></h3>
                            <span class="order-badge"><?= $type_icon ?></span>
                        </div>
                        <div style="background: #e8f5e9; color: #2e7d32; padding: 6px 15px; border-radius: 6px; font-weight: 900; font-size: 18px; border: 1px solid #c8e6c9;">
                            Total: ₱<?= number_format($order['total_price'], 2) ?>
                        </div>
                    </div>
                    
                    <small style="color: #888; font-family: monospace; font-size: 13px;">
                        Order ID: <?= $order['order_group_id'] ?> | Time: <?= $order['sale_date'] ?>
                    </small>
                    
                    <div class="kds-items-container">
                        <?php
                            $items_array = explode('<br>', $order['item_names']);
                            foreach($items_array as $item_text):
                                if (preg_match('/^(.*?)\s*\((.*?)\)$/', $item_text, $matches)) {
                                    $drink_name = htmlspecialchars($matches[1]);
                                    
                                    // NEW: We split the add-ins by the comma and build a clean column!
                                    $add_ins_raw = explode(', ', $matches[2]);
                                    $add_ins_html = "";
                                    
                                    foreach ($add_ins_raw as $index => $addon) {
                                        $safe_addon = htmlspecialchars($addon);
                                        if ($index == 0) {
                                            $add_ins_html .= "↳ " . $safe_addon . "<br>";
                                        } else {
                                            // Invisible arrow ensures perfect alignment!
                                            $add_ins_html .= "<span style='visibility: hidden;'>↳ </span>" . $safe_addon . "<br>";
                                        }
                                    }
                                    
                                    echo "<div class='kds-item'>
                                            <span class='kds-drink'>☕ {$drink_name}</span>
                                            <span class='kds-addins' style='line-height: 1.6; display: block;'>{$add_ins_html}</span>
                                          </div>";
                                } else {
                                    echo "<div class='kds-item'>
                                            <span class='kds-drink'>☕ " . htmlspecialchars($item_text) . "</span>
                                          </div>";
                                }
                            endforeach;
                        ?>
                    </div>
                </div>
                <div style="margin-left: 15px;">
                    <a href="finish_order.php?id=<?= $order['order_group_id'] ?>" class="finish-btn">✅ Finish Order</a>
                </div>
            </div>
            <?php 
                endwhile; 
            else:
                echo "<p style='text-align:center; color:#888; margin-top:50px; font-size: 18px;'>No active orders. Waiting for customers...</p>";
            endif; 
            ?>
        </div>
    </div>
 
    <div id="inventory-tab" class="admin-section">
        <h1>📦 Menu Items</h1>
        <div class="action-bar">
            <input type="text" id="searchInput" class="search-input" onkeyup="filterTable()" placeholder="🔍 Search coffees...">
            <button type="button" class="add-btn" onclick="openAddModal()">➕ Add New Product</button>
        </div>
        <table id="inventoryTable">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php
            $result = $conn->query("SELECT product_id as id, name, base_price as price FROM products");
            while($row = $result->fetch_assoc()):
            ?>
            <tr>
            <form action="update_product.php" method="POST">
                <td><?= $row['id'] ?></td>
                <td><input type="text" name="name" class="product-name-input" value="<?= $row['name'] ?>" style="width: 90%;"></td>
                <td><input type="number" name="price" value="<?= $row['price'] ?>" step="0.01" style="width: 80px;"></td>
                <td>
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <button type="submit">Update</button>
                    <a href="delete_product.php?id=<?= $row['id'] ?>" class="delete">Delete</a>
                </td>
            </form>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>

    <div id="addins-tab" class="admin-section">
        <h1>🥤 Manage Add-Ins</h1>
        <div class="action-bar">
            <input type="text" id="searchAddinsInput" class="search-input" onkeyup="filterAddinsTable()" placeholder="🔍 Search add-ins...">
            <button type="button" class="add-btn" onclick="openAddAddinModal()">➕ Add New Add-In</button>
        </div>
        <table id="addinsTable">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
            <?php
            $res_addins = $conn->query("SELECT addin_id as id, name, price FROM add_ins");
            while($row_a = $res_addins->fetch_assoc()):
            ?>
            <tr>
            <form action="update_addin.php" method="POST">
                <td><?= $row_a['id'] ?></td>
                <td><input type="text" name="name" class="addin-name-input" value="<?= $row_a['name'] ?>" style="width: 90%;"></td>
                <td><input type="number" name="price" value="<?= $row_a['price'] ?>" step="0.01" style="width: 80px;"></td>
                <td>
                    <input type="hidden" name="id" value="<?= $row_a['id'] ?>">
                    <button type="submit">Update</button>
                    <a href="delete_addin.php?id=<?= $row_a['id'] ?>" class="delete">Delete</a>
                </td>
            </form>
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
 
    <div id="sales-tab" class="admin-section">
        <h2>💰 Sales Report</h2>
        
        <div class="sales-header-actions">
            <a href="export_sales.php" style="background-color: #27ae60; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; margin-right: 10px; display: inline-block;">📊 Export to Excel</a>
            <button type="button" class="clear-all-btn" onclick="openDeleteModal()">Clear All Sales Report</button>
        </div>
        
        <div id="live-sales-report">
            <table>
                <tr>
                    <th>Date & Time</th>
                    <th>Order ID</th>
                    <th>Name/Alyas</th>
                    <th>Order Type</th>
                    <th>Items Ordered</th>
                    <th>Total Value</th>
                    <th>Action</th> 
                </tr>
                <?php
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
                
                if($sales_result && $sales_result->num_rows > 0):
                    while($sale = $sales_result->fetch_assoc()):
                ?>
                <tr>
                    <td><?= $sale['sale_date'] ?></td>
                    <td><?= $sale['order_group_id'] ?></td>
                    <td><strong><?= htmlspecialchars($sale['customer_name']) ?></strong></td> 
                    <td><strong><?= $sale['order_type'] ?></strong></td> 
                    <td><?= $sale['item_names'] ?></td> 
                    <td>₱<?= number_format($sale['total_order_price'], 2) ?></td>
                    <td>
                        <a href="delete_sale.php?date=<?= urlencode($sale['sale_date']) ?>" 
                           class="delete-sale" 
                           onclick="return confirm('Are you sure you want to remove this record?')">
                           Remove
                        </a>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7">No completed sales yet.</td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
 
</div>

<div class="modal-overlay" id="customDeleteModal">
    <div class="modal-box">
        <h2 style="margin-top: 0; color: #d9534f;">⚠️ Delete all?</h2>
        <p>Yes or no.</p>
        <button class="modal-btn btn-yes" onclick="confirmDeleteAll()">Yes</button>
        <button class="modal-btn btn-no" onclick="closeDeleteModal()">No</button>
    </div>
</div>

<div class="modal-overlay" id="addProductModal">
    <div class="modal-box" style="width: 400px; border-top: 6px solid #8C6A51;"> 
        <h2 style="margin-top: 0; color: #4A3022;">➕ Add New Product</h2>
        <form action="add_product.php" method="POST">
            <div class="form-group">
                <label style="color: #4A3022;">Product Name:</label>
                <input type="text" name="name" required placeholder="e.g. Matcha Latte" style="border: 1px solid #D1C7B7; border-radius: 6px;">
            </div>
            <div class="form-group">
                <label style="color: #4A3022;">Price (₱):</label>
                <input type="number" name="price" step="0.01" required placeholder="0.00" style="border: 1px solid #D1C7B7; border-radius: 6px;">
            </div>
            
            <div class="form-group">
                <label style="color: #4A3022;">Category Type:</label>
                <select name="type_id" required style="border: 1px solid #D1C7B7; border-radius: 6px;">
                    <option value="1"> Hot</option>
                    <option value="2"> Iced</option>
                    <option value="3"> Blended</option>
                </select>
            </div>

            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 25px;">
                <button type="submit" class="modal-btn" style="background-color: #8C6A51; color: white; flex: 1;">Save Product</button>
                <button type="button" class="modal-btn btn-no" onclick="closeAddModal()" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="addAddinModal">
    <div class="modal-box" style="width: 400px; border-top: 6px solid #8C6A51;"> 
        <h2 style="margin-top: 0; color: #4A3022;">➕ Add New Add-In</h2>
        <form action="add_addin.php" method="POST">
            <div class="form-group">
                <label style="color: #4A3022;">Add-In Name:</label>
                <input type="text" name="name" required placeholder="e.g. Vanilla Syrup" style="border: 1px solid #D1C7B7; border-radius: 6px;">
            </div>
            <div class="form-group">
                <label style="color: #4A3022;">Price (₱):</label>
                <input type="number" name="price" step="0.01" required placeholder="0.00" style="border: 1px solid #D1C7B7; border-radius: 6px;">
            </div>
            <div style="display: flex; gap: 10px; justify-content: center; margin-top: 25px;">
                <button type="submit" class="modal-btn" style="background-color: #8C6A51; color: white; flex: 1;">Save Add-In</button>
                <button type="button" class="modal-btn btn-no" onclick="closeAddAddinModal()" style="flex: 1;">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
// Tab Switching
function switchTab(tabId, clickedButton) {
    document.querySelectorAll('.admin-section').forEach(section => { section.classList.remove('active'); });
    document.querySelectorAll('.tab-btn').forEach(btn => { btn.classList.remove('active'); });
    document.getElementById(tabId).classList.add('active');
    clickedButton.classList.add('active');
}

// Modal Functions
function openDeleteModal() { document.getElementById('customDeleteModal').classList.add('show'); }
function closeDeleteModal() { document.getElementById('customDeleteModal').classList.remove('show'); }
function confirmDeleteAll() { window.location.href = 'clear_all_sales.php'; }

function openAddModal() { document.getElementById('addProductModal').classList.add('show'); }
function closeAddModal() { document.getElementById('addProductModal').classList.remove('show'); }

// Add-In Modal Functions
function openAddAddinModal() { document.getElementById('addAddinModal').classList.add('show'); }
function closeAddAddinModal() { document.getElementById('addAddinModal').classList.remove('show'); }

// Table Search (Products)
function filterTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("inventoryTable");
    let tr = table.getElementsByTagName("tr");
    for (let i = 1; i < tr.length; i++) {
        let nameInput = tr[i].querySelector(".product-name-input");
        if (nameInput) {
            let txtValue = nameInput.value;
            tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}

// Table Search (Add-Ins)
function filterAddinsTable() {
    let input = document.getElementById("searchAddinsInput");
    let filter = input.value.toLowerCase();
    let table = document.getElementById("addinsTable");
    let tr = table.getElementsByTagName("tr");
    for (let i = 1; i < tr.length; i++) {
        let nameInput = tr[i].querySelector(".addin-name-input");
        if (nameInput) {
            let txtValue = nameInput.value;
            tr[i].style.display = txtValue.toLowerCase().indexOf(filter) > -1 ? "" : "none";
        }
    }
}

// REAL-TIME DATA SYNC (AJAX POLLING)
function syncAdminDashboard() {
    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            let parser = new DOMParser();
            let doc = parser.parseFromString(html, "text/html");
            
            let liveOrders = doc.getElementById('live-active-orders').innerHTML;
            document.getElementById('live-active-orders').innerHTML = liveOrders;
            
            let liveSales = doc.getElementById('live-sales-report').innerHTML;
            document.getElementById('live-sales-report').innerHTML = liveSales;
        })
        .catch(error => console.log("Sync error:", error));
}

setInterval(syncAdminDashboard, 3000);
</script>
 
</body>
</html>