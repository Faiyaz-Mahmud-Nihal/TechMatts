<?php
require_once 'db_connection.php';
require_once 'auth.php';

header('Content-Type: application/json');

$orderId = $_GET['id'] ?? 0;

if (!$orderId) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    exit;
}

try {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Not logged in']);
        exit;
    }

    $userId = $_SESSION['user_id'];

    // Get the main order details with user verification
    $orderStmt = $pdo->prepare("
        SELECT o.*, CONCAT(u.first_name, ' ', u.last_name) as shipping_name
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.user_id
        WHERE o.order_id = ? AND o.user_id = ?
    ");
    $orderStmt->execute([$orderId, $userId]);
    $order = $orderStmt->fetch();

    if (!$order) {
        http_response_code(404);
        echo json_encode(['error' => 'Order not found or not authorized']);
        exit;
    }

    // Get order items
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
    $itemsStmt->execute([$orderId]);
    $order['items'] = $itemsStmt->fetchAll();

    echo json_encode($order);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}