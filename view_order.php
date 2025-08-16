<?php
require_once 'db_connection.php';
require_once 'auth.php';

// Set proper content type first
header('Content-Type: text/html; charset=UTF-8');

// Check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit;
}

// Get order ID from URL
$orderId = $_GET['id'] ?? 0;
if (!$orderId) {
    header('Location: order.html');
    exit;
}

// Fetch order details
try {
    $sql = "SELECT o.*, 
           CONCAT(u.first_name, ' ', u.last_name) as customer_name,
           u.phone as customer_phone,
           o.shipping_address,
           o.shipping_district,
           o.shipping_postcode
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.order_id = ? AND o.user_id = ?";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$orderId, $_SESSION['user_id']]);
    $order = $stmt->fetch();

    if (!$order) {
        header('Location: order.html');
        exit;
    }

    // Fetch order items
    $itemsSql = "SELECT oi.*, 
               p.name as product_name, 
               pi.image_url,
               (oi.unit_price * oi.quantity) as item_total
        FROM order_items oi
        JOIN products p ON oi.product_id = p.product_id
        LEFT JOIN (
            SELECT product_id, MIN(image_url) as image_url 
            FROM product_images 
            GROUP BY product_id
        ) pi ON p.product_id = pi.product_id
        WHERE oi.order_id = ?";
        
    $stmt = $pdo->prepare($itemsSql);
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll();

    // Calculate values (without delivery cost)
    $subtotal = array_reduce($items, function($total, $item) {
        return $total + ($item['unit_price'] * $item['quantity']);
    }, 0);
    
    $discount = $order['discount_amount'] ?? 0;
    $total = $subtotal - $discount;
    $paid = ($order['payment_status'] === 'paid') ? $total : 0;
    $due = $total - $paid;

    // Format date
    $orderDate = new DateTime($order['order_date']);
    $formattedDate = $orderDate->format('F j, Y');

    // Determine status color
    $statusClass = match(strtolower($order['status'])) {
        'pending' => 'status-pending',
        'processing' => 'status-processing',
        'shipped' => 'status-shipped',
        'delivered' => 'status-delivered',
        'cancelled' => 'status-cancelled',
        default => 'status-pending'
    };

    // Now include the HTML template AFTER all variables are set
    include 'order_template.php';

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
