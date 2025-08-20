<?php
require_once 'db_connection.php';
session_start();

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit;
}

// Get user role
$stmt = $pdo->prepare("SELECT role FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user || $user['role'] !== 'supplier') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get action from either GET or POST
$action = $_GET['action'] ?? ($_POST['action'] ?? null);

if (!$action) {
    echo json_encode(['success' => false, 'message' => 'No action specified']);
    exit;
}

// Handle GET requests
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        switch ($action) {
            case 'stats':
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE added_by = ?");
                $stmt->execute([$_SESSION['user_id']]);
                $totalProducts = $stmt->fetchColumn();

                $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE added_by = ? AND is_active = 1");
                $stmt->execute([$_SESSION['user_id']]);
                $activeProducts = $stmt->fetchColumn();

                $stats = [
                    'total_products' => $totalProducts,
                    'active_products' => $activeProducts,
                    'product_change' => 5, // Placeholder - would calculate from DB in real implementation
                    'total_change' => 8    // Placeholder
                ];
                echo json_encode(['success' => true, 'stats' => $stats]);
                break;

            case 'get_products':
                $stmt = $pdo->prepare("
                    SELECT p.product_id, p.name, p.category, p.price, p.is_active, 
                           pi.image_url as main_image
                    FROM products p
                    LEFT JOIN product_images pi ON p.product_id = pi.product_id AND pi.is_thumbnail = 1
                    WHERE p.added_by = ?
                    ORDER BY p.created_at DESC
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $products = $stmt->fetchAll();
                echo json_encode(['success' => true, 'products' => $products]);
                break;

            case 'verify_product':
                if (empty($_GET['id'])) {
                    throw new Exception("Product ID is required");
                }
                
                $productId = $_GET['id'];
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_id = ? AND added_by = ?");
                $stmt->execute([$productId, $_SESSION['user_id']]);
                $exists = $stmt->fetchColumn() > 0;
                
                echo json_encode(['success' => $exists, 'message' => $exists ? 'Product verified' : 'Product not found or unauthorized']);
                break;

            default:
                echo json_encode(['success' => false, 'message' => 'Invalid action']);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($action) {
            case 'toggle_product_status':
                if (empty($_POST['product_id'])) {
                    throw new Exception("Product ID is required");
                }
                
                $productId = $_POST['product_id'];
                $isActive = $_POST['is_active'] === '1' ? 1 : 0;
                
                // Verify the product belongs to this supplier
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_id = ? AND added_by = ?");
                $stmt->execute([$productId, $_SESSION['user_id']]);
                if ($stmt->fetchColumn() === 0) {
                    throw new Exception("Product not found or unauthorized");
                }
                
                // Check if product has at least one image if activating
                if ($isActive) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ?");
                    $stmt->execute([$productId]);
                    $imageCount = $stmt->fetchColumn();
                    
                    if ($imageCount == 0) {
                        throw new Exception("Product must have at least one image to be activated");
                    }
                }
                
                $stmt = $pdo->prepare("UPDATE products SET is_active = ? WHERE product_id = ?");
                $stmt->execute([$isActive, $productId]);
                
                echo json_encode(['success' => true, 'message' => 'Product status updated']);
                break;

            case 'add_product':
                $required = ['name', 'category', 'price'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Required field '$field' is missing");
                    }
                }
                
                $pdo->beginTransaction();
                
                // Generate product ID
                $prefix = $_POST['category'] === 'mousepad' ? 'mp-' : 'pc-';
                $productId = $prefix . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
                
                // Insert product
                $pdo->prepare("
                    INSERT INTO products 
                    (product_id, name, description, category, type, price, is_active, added_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ")->execute([
                    $productId,
                    $_POST['name'],
                    $_POST['description'] ?? '',
                    $_POST['category'],
                    $_POST['type'] ?? null,
                    $_POST['price'],
                    $_POST['is_active'] ?? 1,
                    $_SESSION['user_id']
                ]);
                
                // Handle related data
                handleProductRelations($productId, $_POST, $_FILES);
                $pdo->commit();
                
                echo json_encode(['success' => true, 'message' => 'Product added successfully']);
                break;

            case 'update_product':
                $required = ['product_id', 'name', 'category', 'price'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Required field '$field' is missing");
                    }
                }

                // Verify the product belongs to this supplier
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE product_id = ? AND added_by = ?");
                $stmt->execute([$_POST['product_id'], $_SESSION['user_id']]);
                if ($stmt->fetchColumn() === 0) {
                    throw new Exception("Product not found or unauthorized");
                }

                if (isset($_POST['is_active']) && $_POST['is_active'] == 1) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ?");
                    $stmt->execute([$_POST['product_id']]);
                    $imageCount = $stmt->fetchColumn();
                    
                    if ($imageCount == 0) {
                        throw new Exception("Product must have at least one image to be activated");
                    }
                }
                
                $pdo->beginTransaction();
                
                // Update product
                $pdo->prepare("
                    UPDATE products 
                    SET name = ?, description = ?, category = ?, type = ?, price = ?, is_active = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE product_id = ?
                ")->execute([
                    $_POST['name'],
                    $_POST['description'] ?? '',
                    $_POST['category'],
                    $_POST['type'] ?? null,
                    $_POST['price'],
                    $_POST['is_active'] ?? 1,
                    $_POST['product_id']
                ]);
                
                // Delete existing relations
                $tables = [
                    'product_sizes',
                    'product_features',
                    'product_specs',
                    'product_categories',
                    'product_tags'
                ];
                
                foreach ($tables as $table) {
                    $pdo->prepare("DELETE FROM $table WHERE product_id = ?")->execute([$_POST['product_id']]);
                }
                
                // Handle related data
                handleProductRelations($_POST['product_id'], $_POST, $_FILES);
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

// Helper function to handle product relations (same as in admin_api.php)
function handleProductRelations($productId, $postData, $files) {
    global $pdo;
    
    $uploadDir = 'media/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Handle sizes (mousepads)
    if ($postData['category'] === 'mousepad' && !empty($postData['sizes'])) {
        $stmt = $pdo->prepare("INSERT INTO product_sizes (product_id, dimensions, sku) VALUES (?, ?, ?)");
        foreach ($postData['sizes'] as $size) {
            if (!empty($size['dimensions']) && !empty($size['sku'])) {
                $stmt->execute([$productId, $size['dimensions'], $size['sku']]);
            }
        }
    }
    
    // Handle features (mousepads)
    if ($postData['category'] === 'mousepad' && !empty($postData['features'])) {
        $stmt = $pdo->prepare("INSERT INTO product_features (product_id, feature) VALUES (?, ?)");
        foreach ($postData['features'] as $feature) {
            if (!empty($feature)) {
                $stmt->execute([$productId, $feature]);
            }
        }
    }
    
    // Handle specs (pc builds)
    if ($postData['category'] === 'pcbuild' && !empty($postData['specs'])) {
        $stmt = $pdo->prepare("INSERT INTO product_specs (product_id, spec) VALUES (?, ?)");
        foreach ($postData['specs'] as $spec) {
            if (!empty($spec)) {
                $stmt->execute([$productId, $spec]);
            }
        }
    }
    
    // Handle categories
    if (!empty($postData['categories'])) {
        $stmt = $pdo->prepare("INSERT INTO product_categories (product_id, category_name) VALUES (?, ?)");
        foreach ($postData['categories'] as $category) {
            if (!empty($category)) {
                $stmt->execute([$productId, $category]);
            }
        }
    }
    
    // Handle tags
    if (!empty($postData['tags'])) {
        $stmt = $pdo->prepare("INSERT INTO product_tags (product_id, tag_name) VALUES (?, ?)");
        foreach ($postData['tags'] as $tag) {
            if (!empty($tag)) {
                $stmt->execute([$productId, $tag]);
            }
        }
    }
    
    // Handle images
    if (!empty($files['images'])) {
        $stmt = $pdo->prepare("INSERT INTO product_images (product_id, image_url, is_thumbnail) VALUES (?, ?, ?)");
        $isFirst = true;
        
        foreach ($files['images']['tmp_name'] as $key => $tmpName) {
            if ($files['images']['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = basename($files['images']['name'][$key]);
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = uniqid() . '.' . $fileExt;
                $uploadPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($tmpName, $uploadPath)) {
                    $stmt->execute([$productId, $uploadPath, $isFirst ? 1 : 0]);
                    $isFirst = false;
                }
            }
        }
    }
}