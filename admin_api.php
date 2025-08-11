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
                
            case 'delete_product':
                if (empty($_GET['id'])) {
                    throw new Exception("Product ID is required");
                }
                
                $productId = $_GET['id'];
                
                // Start transaction
                $pdo->beginTransaction();
                
                try {
                    // First delete all related records to satisfy foreign key constraints
                    $pdo->prepare("DELETE FROM product_sizes WHERE product_id = ?")->execute([$productId]);
                    $pdo->prepare("DELETE FROM product_features WHERE product_id = ?")->execute([$productId]);
                    $pdo->prepare("DELETE FROM product_specs WHERE product_id = ?")->execute([$productId]);
                    $pdo->prepare("DELETE FROM product_categories WHERE product_id = ?")->execute([$productId]);
                    $pdo->prepare("DELETE FROM product_tags WHERE product_id = ?")->execute([$productId]);
                    $pdo->prepare("DELETE FROM product_images WHERE product_id = ?")->execute([$productId]);
                    
                    // Then delete the product itself
                    $pdo->prepare("DELETE FROM products WHERE product_id = ?")->execute([$productId]);
                    
                    $pdo->commit();
                    echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action'])) {
    try {
        switch ($_GET['action']) {
            case 'add_product':
                // Validate required fields
                $required = ['name', 'category', 'price'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Required field '$field' is missing");
                    }
                }
                
                // Start transaction
                $pdo->beginTransaction();
                
                // Insert product
                $stmt = $pdo->prepare("
                    INSERT INTO products 
                    (product_id, name, description, category, type, price, is_active, added_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                // Generate product ID
                $prefix = $_POST['category'] === 'mousepad' ? 'mp-' : 'pc-';
                $productId = $prefix . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                
                $stmt->execute([
                    $productId,
                    $_POST['name'],
                    $_POST['description'] ?? '',
                    $_POST['category'],
                    $_POST['type'] ?? null,
                    $_POST['price'],
                    $_POST['is_active'] ?? 1,
                    $_SESSION['user_id']
                ]);
                
                // Handle sizes (for mousepads)
                if ($_POST['category'] === 'mousepad' && !empty($_POST['sizes'])) {
                    $sizeStmt = $pdo->prepare("
                        INSERT INTO product_sizes 
                        (product_id, dimensions, sku) 
                        VALUES (?, ?, ?)
                    ");
                    
                    foreach ($_POST['sizes'] as $size) {
                        if (!empty($size['dimensions']) && !empty($size['sku'])) {
                            $sizeStmt->execute([
                                $productId,
                                $size['dimensions'],
                                $size['sku']
                            ]);
                        }
                    }
                }
                
                // Handle features (for mousepads)
                if ($_POST['category'] === 'mousepad' && !empty($_POST['features'])) {
                    $featureStmt = $pdo->prepare("
                        INSERT INTO product_features 
                        (product_id, feature) 
                        VALUES (?, ?)
                    ");
                    
                    foreach ($_POST['features'] as $feature) {
                        if (!empty($feature)) {
                            $featureStmt->execute([
                                $productId,
                                $feature
                            ]);
                        }
                    }
                }
                
                // Handle specs (for pc builds)
                if ($_POST['category'] === 'pcbuild' && !empty($_POST['specs'])) {
                    $specStmt = $pdo->prepare("
                        INSERT INTO product_specs 
                        (product_id, spec) 
                        VALUES (?, ?)
                    ");
                    
                    foreach ($_POST['specs'] as $spec) {
                        if (!empty($spec)) {
                            $specStmt->execute([
                                $productId,
                                $spec
                            ]);
                        }
                    }
                }
                
                // Handle categories
                if (!empty($_POST['categories'])) {
                    $categoryStmt = $pdo->prepare("
                        INSERT INTO product_categories 
                        (product_id, category_name) 
                        VALUES (?, ?)
                    ");
                    
                    foreach ($_POST['categories'] as $category) {
                        if (!empty($category)) {
                            $categoryStmt->execute([
                                $productId,
                                $category
                            ]);
                        }
                    }
                }
                
                // Handle tags
                if (!empty($_POST['tags'])) {
                    $tagStmt = $pdo->prepare("
                        INSERT INTO product_tags 
                        (product_id, tag_name) 
                        VALUES (?, ?)
                    ");
                    
                    foreach ($_POST['tags'] as $tag) {
                        if (!empty($tag)) {
                            $tagStmt->execute([
                                $productId,
                                $tag
                            ]);
                        }
                    }
                }
                
                // Handle images
                if (!empty($_FILES['images'])) {
                    $imageStmt = $pdo->prepare("
                        INSERT INTO product_images 
                        (product_id, image_url, is_thumbnail) 
                        VALUES (?, ?, ?)
                    ");
                    
                    $uploadDir = 'media/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $isFirst = true;
                    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                            $fileName = basename($_FILES['images']['name'][$key]);
                            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                            $newFileName = uniqid() . '.' . $fileExt;
                            $uploadPath = $uploadDir . $newFileName;
                            
                            if (move_uploaded_file($tmpName, $uploadPath)) {
                                $imageStmt->execute([
                                    $productId,
                                    $uploadPath,
                                    $isFirst ? 1 : 0
                                ]);
                                $isFirst = false;
                            }
                        }
                    }
                }
                
                // Commit transaction
                $pdo->commit();
                
                echo json_encode(['success' => true, 'message' => 'Product added successfully']);
                break;
                
            case 'update_product':
                // Validate required fields
                $required = ['product_id', 'name', 'category', 'price'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Required field '$field' is missing");
                    }
                }
                
                // Check if product has at least one image when activating
                if (isset($_POST['is_active']) && $_POST['is_active'] == 1) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ?");
                    $stmt->execute([$_POST['product_id']]);
                    $imageCount = $stmt->fetchColumn();
                    
                    if ($imageCount == 0) {
                        throw new Exception("Product must have at least one image to be activated");
                    }
                }
                
                // Start transaction
                $pdo->beginTransaction();
                
                // Update product
                $stmt = $pdo->prepare("
                    UPDATE products 
                    SET name = ?, description = ?, category = ?, type = ?, price = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE product_id = ?
                ");
                
                $stmt->execute([
                    $_POST['name'],
                    $_POST['description'] ?? '',
                    $_POST['category'],
                    $_POST['type'] ?? null,
                    $_POST['price'],
                    $_POST['is_active'] ?? 1,
                    $_POST['product_id']
                ]);
                
                // Delete existing sizes, features, specs, categories, tags
                $pdo->prepare("DELETE FROM product_sizes WHERE product_id = ?")->execute([$_POST['product_id']]);
                $pdo->prepare("DELETE FROM product_features WHERE product_id = ?")->execute([$_POST['product_id']]);
                $pdo->prepare("DELETE FROM product_specs WHERE product_id = ?")->execute([$_POST['product_id']]);
                $pdo->prepare("DELETE FROM product_categories WHERE product_id = ?")->execute([$_POST['product_id']]);
                $pdo->prepare("DELETE FROM product_tags WHERE product_id = ?")->execute([$_POST['product_id']]);
                
                // Handle sizes (for mousepads)
                if ($_POST['category'] === 'mousepad' && !empty($_POST['sizes'])) {
                    $sizeStmt = $pdo->prepare("
                        INSERT INTO product_sizes 
                        (product_id, dimensions, sku) 
                        VALUES (?, ?, ?)
                    ");
                    
                    foreach ($_POST['sizes'] as $size) {
                        if (!empty($size['dimensions']) && !empty($size['sku'])) {
                            $sizeStmt->execute([
                                $_POST['product_id'],
                                $size['dimensions'],
                                $size['sku']
                            ]);
                        }
                    }
                }
                
                // Handle features (for mousepads)
                if ($_POST['category'] === 'mousepad' && !empty($_POST['features'])) {
                    $featureStmt = $pdo->prepare("
                        INSERT INTO product_features 
                        (product_id, feature) 
                        VALUES (?, ?)
                    ");
                    
                    foreach ($_POST['features'] as $feature) {
                        if (!empty($feature)) {
                            $featureStmt->execute([
                                $_POST['product_id'],
                                $feature
                            ]);
                        }
                    }
                }
                
                // Handle specs (for pc builds)
                if ($_POST['category'] === 'pcbuild' && !empty($_POST['specs'])) {
                    $specStmt = $pdo->prepare("
                        INSERT INTO product_specs 
                        (product_id, spec) 
                        VALUES (?, ?)
                    ");
                    
                    foreach ($_POST['specs'] as $spec) {
                        if (!empty($spec)) {
                            $specStmt->execute([
                                $_POST['product_id'],
                                $spec
                            ]);
                        }
                    }
                }
                
                // Handle categories
                if (!empty($_POST['categories'])) {
                    $categoryStmt = $pdo->prepare("
                        INSERT INTO product_categories 
                        (product_id, category_name) 
                        VALUES (?, ?)
                    ");
                    
                    foreach ($_POST['categories'] as $category) {
                        if (!empty($category)) {
                            $categoryStmt->execute([
                                $_POST['product_id'],
                                $category
                            ]);
                        }
                    }
                }
                
                // Handle tags
                if (!empty($_POST['tags'])) {
                    $tagStmt = $pdo->prepare("
                        INSERT INTO product_tags 
                        (product_id, tag_name) 
                        VALUES (?, ?)
                    ");
                    
                    foreach ($_POST['tags'] as $tag) {
                        if (!empty($tag)) {
                            $tagStmt->execute([
                                $_POST['product_id'],
                                $tag
                            ]);
                        }
                    }
                }
                
                // Handle new images
                if (!empty($_FILES['images'])) {
                    $imageStmt = $pdo->prepare("
                        INSERT INTO product_images 
                        (product_id, image_url, is_thumbnail) 
                        VALUES (?, ?, ?)
                    ");
                    
                    $uploadDir = 'media/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $isFirst = true;
                    foreach ($_FILES['images']['tmp_name'] as $key => $tmpName) {
                        if ($_FILES['images']['error'][$key] === UPLOAD_ERR_OK) {
                            $fileName = basename($_FILES['images']['name'][$key]);
                            $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                            $newFileName = uniqid() . '.' . $fileExt;
                            $uploadPath = $uploadDir . $newFileName;
                            
                            if (move_uploaded_file($tmpName, $uploadPath)) {
                                $imageStmt->execute([
                                    $_POST['product_id'],
                                    $uploadPath,
                                    $isFirst ? 1 : 0
                                ]);
                                $isFirst = false;
                            }
                        }
                    }
                }
                
                // Commit transaction
                $pdo->commit();
                
                echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
                break;
                
            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);