<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

// Get the raw POST data
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request']);
    exit;
}

try {
    $pdo->beginTransaction();
    
    // Handle user ID - either from session or from request data
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        if (isset($data['user_id']) && $data['user_id'] > 1) {
            $_SESSION['user_id'] = $data['user_id'];
            $userId = $data['user_id'];
        } else {
            // For guests, use NULL
            $userId = null;
        }
    } else {
        $userId = $_SESSION['user_id'];
    }
    
    // 1. Create the order record
    $orderStmt = $pdo->prepare("
        INSERT INTO orders (
            user_id, 
            order_number, 
            total_amount, 
            status, 
            payment_method, 
            payment_status, 
            shipping_address, 
            shipping_district, 
            shipping_postcode, 
            shipping_phone, 
            notes
        ) VALUES (
            :user_id, 
            :order_number, 
            :total_amount, 
            'pending', 
            :payment_method, 
            'paid', 
            :shipping_address, 
            :shipping_district, 
            :shipping_postcode, 
            :shipping_phone, 
            :notes
        )
    ");
    
    $orderStmt->execute([
        ':user_id' => $userId,
        ':order_number' => 'ORD-' . time(),
        ':total_amount' => $data['total'],
        ':payment_method' => $data['payment_method'],
        ':shipping_address' => $data['shipping_address'],
        ':shipping_district' => $data['shipping_district'],
        ':shipping_postcode' => $data['shipping_postcode'] ?? '',
        ':shipping_phone' => $data['shipping_phone'],
        ':notes' => $data['notes'] ?? ''
    ]);
    
    $orderId = $pdo->lastInsertId();
    
    // 2. Create order items
    $itemStmt = $pdo->prepare("
        INSERT INTO order_items (
            order_id, 
            product_id, 
            quantity, 
            unit_price, 
            size
        ) VALUES (
            :order_id, 
            :product_id, 
            :quantity, 
            :unit_price, 
            :size
        )
    ");
    
    foreach ($data['items'] as $item) {
        $itemStmt->execute([
            ':order_id' => $orderId,
            ':product_id' => $item['id'],
            ':quantity' => $item['quantity'],
            ':unit_price' => $item['price'],
            ':size' => $item['size'] ?? null
        ]);
    }
    
    $pdo->commit();
    
    // Return success with order ID
    echo json_encode([
        'success' => true,
        'order_id' => $orderId,
        'order_number' => 'ORD-' . $orderId
    ]);
    
} catch (PDOException $e) {
    if (isset($pdo)) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString() // For debugging only
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>