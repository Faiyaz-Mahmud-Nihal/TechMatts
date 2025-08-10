<?php
require_once 'db_connection.php';
session_start();

header('Content-Type: application/json');

// Check if user is admin
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get user role
$stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Handle different actions
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    try {
        switch ($_GET['action']) {
            case 'stats':
                // Get stats
                $stats = [];
                
                // Total Products
                $stmt = $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1");
                $stats['total_products'] = $stmt->fetchColumn();
                
                // Total Orders
                $stmt = $pdo->query("SELECT COUNT(*) FROM orders");
                $stats['total_orders'] = $stmt->fetchColumn();
                
                // Pending Orders
                $stmt = $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
                $stats['pending_orders'] = $stmt->fetchColumn();
                
                // Total Users
                $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1");
                $stats['total_users'] = $stmt->fetchColumn();
                
                // Calculate percentage changes (example - you'll need to implement actual comparison with previous period)
                $stats['product_change'] = 5; // This should be calculated from your data
                $stats['order_change'] = 12;  // This should be calculated from your data
                $stats['pending_change'] = -3; // This should be calculated from your data
                $stats['user_change'] = 8;    // This should be calculated from your data
                
                echo json_encode(['success' => true, 'stats' => $stats]);
                break;
                
            case 'recent_orders':
                // Get recent orders
                $stmt = $pdo->query("
                    SELECT o.order_id, o.order_number, o.order_date, o.total_amount, o.status, 
                           CONCAT(u.first_name, ' ', u.last_name) AS customer_name
                    FROM orders o
                    JOIN users u ON o.user_id = u.user_id
                    ORDER BY o.order_date DESC
                    LIMIT 10
                ");
                $orders = $stmt->fetchAll();
                
                echo json_encode(['success' => true, 'orders' => $orders]);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);