<?php
ob_start();
require_once 'db_connection.php';
require_once 'auth.php';

// Clear any previous output
ob_end_clean();

header('Content-Type: application/json');

try {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in', 'session' => $_SESSION]);
        exit;
    }

    $userId = $_SESSION['user_id'];
    error_log("Fetching orders for user ID: $userId"); // Log the user ID

    // Get orders with their items
    $ordersStmt = $pdo->prepare("
        SELECT 
            o.order_id,
            o.order_number,
            o.order_date,
            o.total_amount,
            o.status,
            o.payment_method,
            o.payment_status,
            o.shipping_address,
            o.shipping_district,
            o.shipping_phone,
            o.notes
        FROM orders o
        WHERE o.user_id = ?
        ORDER BY o.order_date DESC
    ");
    
    if (!$ordersStmt->execute([$userId])) {
        $error = $ordersStmt->errorInfo();
        error_log("Database error: " . print_r($error, true));
        throw new PDOException("Failed to execute query");
    }
    
    $orders = $ordersStmt->fetchAll();
    error_log("Found orders: " . count($orders)); // Log number of orders found

    if (empty($orders)) {
        echo json_encode([]);
        exit;
    }

    // Get items for each order
    $result = [];
    foreach ($orders as $order) {
        $itemsStmt = $pdo->prepare("
            SELECT 
                oi.*,
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
        $itemsStmt->execute([$order['order_id']]);
        $order['items'] = $itemsStmt->fetchAll();
        $result[] = $order;
    }

    error_log("Final orders data: " . json_encode($result));
    echo json_encode($result);
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}