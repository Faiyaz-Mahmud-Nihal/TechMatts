<?php
require_once 'admin_auth.php';
require_once 'db_connection.php';

$orderId = $_GET['id'] ?? 0;

if (!$orderId) {
    header('Location: admin-orders.php');
    exit;
}

// Fetch order details
$orderStmt = $pdo->prepare("
    SELECT o.*, 
           CONCAT(u.first_name, ' ', u.last_name) as customer_name,
           u.email as customer_email,
           u.phone as customer_phone
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.order_id = ?
");
$orderStmt->execute([$orderId]);
$order = $orderStmt->fetch();

if (!$order) {
    header('Location: admin-orders.php');
    exit;
}

// Fetch order items
$itemsStmt = $pdo->prepare("
    SELECT oi.*, 
           p.name as product_name,
           pi.image_url
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    LEFT JOIN (
        SELECT product_id, MIN(image_url) as image_url 
        FROM product_images 
        GROUP BY product_id
    ) pi ON p.product_id = pi.product_id
    WHERE oi.order_id = ?
");
$itemsStmt->execute([$orderId]);
$items = $itemsStmt->fetchAll();

// Format date
$orderDate = new DateTime($order['order_date']);
$formattedDate = $orderDate->format('F j, Y, g:i a');

// Determine status class
$statusClass = match(strtolower($order['status'])) {
    'pending' => 'status-pending',
    'processing' => 'status-processing',
    'shipped' => 'status-shipped',
    'delivered' => 'status-delivered',
    'cancelled' => 'status-cancelled',
    default => 'status-pending'
};
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #<?= htmlspecialchars($order['order_number']) ?> | TechMatts Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
    :root {
        --primary: #e2136e;
        --primary-dark: #c10e5d;
        --dark: #1e1e1e;
        --light: #f5f5f5;
        --gray: #aaa;
        --success: #4CAF50;
        --warning: #FFC107;
        --danger: #F44336;
        --info: #2196F3;
        --card-bg: #ffffff;
        --sidebar-bg: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    }
    
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    body {
        background-color: #f9f9f9;
        color: #333;
    }
    
    /* Admin Container and Sidebar - Match admin.php exactly */
    .admin-container {
        display: flex;
        min-height: 100vh;
    }
    
    .admin-sidebar {
        width: 280px;
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        color: white;
        padding: 0;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        position: fixed;
        height: 100vh;
        z-index: 100;
    }
    
    .admin-logo {
        padding: 25px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 10px;
        text-align: center;
        background-color: rgba(0,0,0,0.2);
    }
    
    .admin-logo h2 {
        color: white;
        font-size: 1.5rem;
        font-weight: 600;
        background: linear-gradient(to right, #e2136e, #ff8a00);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    
    .admin-menu {
        list-style: none;
        padding: 0 15px;
    }
    
    .admin-menu li {
        margin-bottom: 5px;
        position: relative;
    }
    
    .admin-menu a {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        color: #ddd;
        text-decoration: none;
        transition: all 0.3s;
        border-radius: 6px;
        font-size: 15px;
    }
    
    .admin-menu a:hover {
        background: rgba(255,255,255,0.1);
        color: white;
        transform: translateX(5px);
    }
    
    .admin-menu a.active {
        background: linear-gradient(90deg, rgba(226,19,110,0.2) 0%, rgba(226,19,110,0) 100%);
        color: white;
        border-left: 3px solid var(--primary);
    }
    
    .admin-menu a i {
        margin-right: 12px;
        width: 20px;
        text-align: center;
        font-size: 16px;
    }
    
    .admin-menu a.active i {
        color: var(--primary);
    }
    
    /* Main Content - Add margin to account for fixed sidebar */
    .admin-content {
        flex: 1;
        margin-left: 280px;
        padding: 30px;
        background-color: #f5f5f5;
        min-height: 100vh;
    }
    
    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 20px;
        border-bottom: 1px solid #eee;
    }
    
    .admin-header h1 {
        font-size: 28px;
        color: #333;
        font-weight: 600;
    }
    
    .user-profile {
        display: flex;
        align-items: center;
    }
    
    .user-profile img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 10px;
        object-fit: cover;
    }
    
    .user-profile span {
        font-weight: 500;
        color: #555;
    }
    
    /* Products Container */
    .products-container {
        background: var(--card-bg);
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    /* Search and Filter Section */
    .search-filter-container {
        background: var(--card-bg);
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        align-items: center;
    }
    
    .search-box {
        flex: 1;
        min-width: 250px;
        position: relative;
    }
    
    .search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray);
    }
    
    .search-box input {
        width: 100%;
        padding: 12px 15px 12px 40px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s;
        background: white;
    }
    
    .search-box input:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(226,19,110,0.1);
    }
    
    .filter-select {
        padding: 12px 15px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        background: white;
        color: #555;
        min-width: 180px;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .filter-select:focus {
        border-color: var(--primary);
        outline: none;
        box-shadow: 0 0 0 3px rgba(226,19,110,0.1);
    }
    
    .btn-add {
        background-color: var(--primary);
        color: white;
        padding: 12px 20px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s;
        border: none;
        cursor: pointer;
        margin-left: auto;
    }
    
    .btn-add:hover {
        background-color: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(226,19,110,0.3);
    }
    
    /* Products Table */
    .product-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        background: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
    
    .product-table th, .product-table td {
        padding: 15px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }
    
    .product-table th {
        background-color: #f9f9f9;
        font-weight: 600;
        color: #555;
        text-transform: uppercase;
        font-size: 13px;
        letter-spacing: 0.5px;
    }
    
    .product-table tr:last-child td {
        border-bottom: none;
    }
    
    .product-table tr:hover {
        background-color: rgba(226,19,110,0.03);
    }
    
    .product-img-container {
        display: flex;
        align-items: center;
    }
    
    .product-img {
        width: 50px;
        height: 50px;
        object-fit: cover;
        border-radius: 6px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        transition: transform 0.3s;
    }
    
    .product-img:hover {
        transform: scale(1.1);
    }
    
    .no-image {
        width: 50px;
        height: 50px;
        background: #f5f5f5;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 12px;
    }
    
    .product-name {
        font-weight: 500;
        color: #333;
    }
    
    .product-category {
        background: rgba(33, 150, 243, 0.1);
        color: var(--info);
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
        display: inline-block;
    }
    
    .product-price {
        font-weight: 600;
        color: #333;
    }
    
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    
    .status-active {
        background: rgba(76, 175, 80, 0.1);
        color: var(--success);
    }
    
    .status-inactive {
        background: rgba(244, 67, 54, 0.1);
        color: var(--danger);
    }
    
    .action-buttons {
        display: flex;
        gap: 8px;
    }
    
    .action-btn {
        padding: 8px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border: none;
        cursor: pointer;
    }
    
    .btn-edit {
        background-color: var(--info);
        color: white;
    }
    
    .btn-edit:hover {
        background-color: #1a83d8;
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(33, 150, 243, 0.3);
    }
    
    .btn-delete {
        background-color: var(--danger);
        color: white;
    }
    
    .btn-delete:hover {
        background-color: #d32f2f;
        transform: translateY(-2px);
        box-shadow: 0 3px 10px rgba(244, 67, 54, 0.3);
    }
    
    /* Responsive Adjustments */
    @media (max-width: 992px) {
        .admin-sidebar {
            width: 250px;
        }
        
        .admin-content {
            margin-left: 250px;
        }
    }
    
    @media (max-width: 768px) {
        .admin-sidebar {
            width: 100%;
            height: auto;
            position: relative;
        }
        
        .admin-content {
            margin-left: 0;
        }
        
        .search-filter-container {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-box, .filter-select {
            width: 100%;
        }
        
        .btn-add {
            width: 100%;
            justify-content: center;
        }
        
        .product-table {
            display: block;
            overflow-x: auto;
        }
    }

        
        /* Additional styles for order status */
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        
        .status-pending {
            background: #FFF3CD;
            color: #856404;
        }
        
        .status-processing {
            background: #CCE5FF;
            color: #004085;
        }
        
        .status-shipped {
            background: #D4EDDA;
            color: #155724;
        }
        
        .status-delivered {
            background: #D1ECF1;
            color: #0C5460;
        }
        
        .status-cancelled {
            background: #F8D7DA;
            color: #721C24;
        }
        
        .status-select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
            cursor: pointer;
        }
        
        .btn-update {
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-update:hover {
            background-color: var(--primary-dark);
        }
        
        /* Order details specific styles */
        .order-details {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .order-number {
            font-size: 24px;
            font-weight: 600;
            color: #333;
        }
        
        .order-status {
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        .status-pending { background: #FFF3CD; color: #856404; }
        .status-processing { background: #CCE5FF; color: #004085; }
        .status-shipped { background: #D4EDDA; color: #155724; }
        .status-delivered { background: #D1ECF1; color: #0C5460; }
        .status-cancelled { background: #F8D7DA; color: #721C24; }
        
        .order-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .customer-info, .order-summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        
        .section-title {
            font-size: 18px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
            color: #555;
            font-weight: 600;
        }
        
        .info-row {
            margin-bottom: 10px;
            display: flex;
        }
        
        .info-label {
            font-weight: 600;
            color: #555;
            min-width: 120px;
        }
        
        .info-value {
            color: #333;
        }
        
        .order-items {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .items-table th {
            background: #f9f9f9;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
            color: #333;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .item-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
            border: 1px solid #eee;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            font-weight: 600;
            color: #333;
        }
        
        .grand-total {
            font-size: 18px;
            color: var(--primary);
        }
        
        .action-buttons {
            margin-top: 30px;
            display: flex;
            gap: 15px;
        }
        
        .btn {
            padding: 12px 25px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .status-form {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 20px;
        }
        
        .status-select {
            padding: 10px;
            border-radius: 4px;
            border: 1px solid #ddd;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-logo">
                <h2>TechMatts Admin</h2>
            </div>
            <ul class="admin-menu">
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin-products.php"><i class="fas fa-box-open"></i> Products</a></li>
                <li><a href="admin-orders.php" class="active"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="admin-users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="admin-suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1>Order Details</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=e2136e&color=fff" alt="Admin">
                    <span>Admin</span>
                </div>
            </div>
            
            <div class="order-details">
                <div class="order-header">
                    <div>
                        <h2 class="order-number">Order #<?= htmlspecialchars($order['order_number']) ?></h2>
                        <p>Placed on <?= $formattedDate ?></p>
                    </div>
                    <div class="order-status <?= $statusClass ?>">
                        <?= ucfirst($order['status']) ?>
                    </div>
                </div>
                
                <div class="order-grid">
                    <div class="customer-info">
                        <h3 class="section-title">Customer Information</h3>
                        <div class="info-row">
                            <span class="info-label">Name:</span>
                            <span class="info-value"><?= htmlspecialchars($order['customer_name']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?= htmlspecialchars($order['customer_email']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Phone:</span>
                            <span class="info-value"><?= htmlspecialchars($order['customer_phone']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Shipping Address:</span>
                            <span class="info-value"><?= htmlspecialchars($order['shipping_address']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">District:</span>
                            <span class="info-value"><?= htmlspecialchars($order['shipping_district']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Postcode:</span>
                            <span class="info-value"><?= htmlspecialchars($order['shipping_postcode']) ?></span>
                        </div>
                    </div>
                    
                    <div class="order-summary">
                        <h3 class="section-title">Order Summary</h3>
                        <div class="info-row">
                            <span class="info-label">Payment Method:</span>
                            <span class="info-value"><?= ucfirst($order['payment_method']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Payment Status:</span>
                            <span class="info-value"><?= ucfirst($order['payment_status']) ?></span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Notes:</span>
                            <span class="info-value"><?= htmlspecialchars($order['notes'] ?? 'No notes') ?></span>
                        </div>
                        
                        <form class="status-form" id="status-form">
                            <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                            <select class="status-select" name="status">
                                <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                <option value="shipped" <?= $order['status'] === 'shipped' ? 'selected' : '' ?>>Shipped</option>
                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                                <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Update Status</button>
                        </form>
                    </div>
                </div>
                
                <div class="order-items">
                    <h3 class="section-title">Order Items</h3>
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 15px;">
                                        <img src="<?= htmlspecialchars($item['image_url'] ?? 'media/default-product.jpg') ?>" 
                                             alt="<?= htmlspecialchars($item['product_name']) ?>" class="item-image">
                                        <span><?= htmlspecialchars($item['product_name']) ?></span>
                                    </div>
                                </td>
                                <td><?= number_format($item['unit_price'], 2) ?>৳</td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($item['unit_price'] * $item['quantity'], 2) ?>৳</td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="total-row">
    <span>Subtotal:</span>
    <span><?= number_format($order['total_amount'], 2) ?>৳</span>
</div>
<?php if (isset($order['discount_amount']) && $order['discount_amount'] > 0): ?>
<div class="total-row">
    <span>Discount:</span>
    <span>-<?= number_format($order['discount_amount'], 2) ?>৳</span>
</div>
<?php endif; ?>
<div class="total-row grand-total">
    <span>Total:</span>
    <span><?= number_format(
            $order['total_amount'] - (isset($order['discount_amount']) ? $order['discount_amount'] : 0), 
            2
        ) ?>৳
    </span>
</div>
                
                <div class="action-buttons">
                    <a href="admin-orders.php" class="btn btn-secondary">Back to Orders</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Update order status
     document.getElementById('status-form').addEventListener('submit', async function(e) {
     e.preventDefault();
     
     const formData = new FormData(this);
     formData.append('action', 'update_order_status');
     
     try {
          const response = await fetch('admin_api.php', {
               method: 'POST',
               body: formData
          });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Order status updated successfully!');
            // Update the status display
            const statusElement = document.querySelector('.order-status');
            const newStatus = formData.get('status');
            statusElement.textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            statusElement.className = `order-status status-${newStatus}`;
        } else {
            alert('Error: ' + result.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    }
});
    </script>
</body>
</html>