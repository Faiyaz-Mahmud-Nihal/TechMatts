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

if (!$user || $user['role'] !== 'admin') {
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
                $stats = [
                    'total_products' => $pdo->query("SELECT COUNT(*) FROM products WHERE is_active = 1")->fetchColumn(),
                    'total_orders' => $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
                    'pending_orders' => $pdo->query("SELECT COUNT(*) FROM orders WHERE status = 'pending'")->fetchColumn(),
                    'total_users' => $pdo->query("SELECT COUNT(*) FROM users WHERE is_active = 1")->fetchColumn(),
                    'product_change' => 5,
                    'order_change' => 12,
                    'pending_change' => -3,
                    'user_change' => 8
                ];
                echo json_encode(['success' => true, 'stats' => $stats]);
                break;

            case 'recent_orders':
                $orders = $pdo->query("
                    SELECT o.order_id, o.order_number, o.order_date, o.total_amount, o.status, 
                           CONCAT(u.first_name, ' ', u.last_name) AS customer_name
                    FROM orders o
                    JOIN users u ON o.user_id = u.user_id
                    ORDER BY o.order_date DESC
                    LIMIT 10
                ")->fetchAll();
                echo json_encode(['success' => true, 'orders' => $orders]);
                break;

            case 'delete_product':
                if (empty($_GET['id'])) {
                    throw new Exception("Product ID is required");
                }
                
                $productId = $_GET['id'];
                $pdo->beginTransaction();
                
                try {
                    $tables = [
                        'product_sizes',
                        'product_features',
                        'product_specs',
                        'product_categories',
                        'product_tags',
                        'product_images'
                    ];
                    
                    foreach ($tables as $table) {
                        $pdo->prepare("DELETE FROM $table WHERE product_id = ?")->execute([$productId]);
                    }
                    
                    $pdo->prepare("DELETE FROM products WHERE product_id = ?")->execute([$productId]);
                    $pdo->commit();
                    
                    echo json_encode(['success' => true, 'message' => 'Product deleted successfully']);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
                break;

            case 'delete_user':
                if (empty($_GET['id'])) {
                    throw new Exception("User ID is required");
                }
                
                $userId = $_GET['id'];
                
                // Prevent deleting yourself
                if ($userId == $_SESSION['user_id']) {
                    throw new Exception("You cannot delete your own account");
                }
                
                $pdo->beginTransaction();
                
                try {
                    // First delete from suppliers table if exists
                    $pdo->prepare("DELETE FROM suppliers WHERE supplier_id = ?")->execute([$userId]);
                    
                    // Then delete from users table
                    $pdo->prepare("DELETE FROM users WHERE user_id = ?")->execute([$userId]);
                    
                    $pdo->commit();
                    
                    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
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

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        switch ($action) {
            case 'update_order_status':
                if (empty($_POST['order_id']) || empty($_POST['status'])) {
                    throw new Exception("Order ID and status are required");
                }
                
                $validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
                if (!in_array($_POST['status'], $validStatuses)) {
                    throw new Exception("Invalid status value");
                }
                
                $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
                $stmt->execute([$_POST['status'], $_POST['order_id']]);
                
                echo json_encode(['success' => true, 'message' => 'Order status updated']);
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

                if (isset($_POST['is_active']) && $_POST['is_active'] == 1) {
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM product_images WHERE product_id = ?");
                    $stmt->execute([$_POST['product_id']]);
                    $imageCount = $stmt->fetchColumn();
                    
                    if ($imageCount == 0) {
                        throw new Exception("Product must have at least one image to be activated");
                    }
                }
                
                $pdo->beginTransaction();
                
                // Update product - Make sure price is included here
                $pdo->prepare("
                    UPDATE products 
                    SET name = ?, description = ?, category = ?, type = ?, price = ?, price_range = CONCAT(?, '৳'), is_active = ?, updated_at = CURRENT_TIMESTAMP
                    WHERE product_id = ?
                ")->execute([
                    $_POST['name'],
                    $_POST['description'] ?? '',
                    $_POST['category'],
                    $_POST['type'] ?? null,
                    $_POST['price'],
                    $_POST['price'], // This sets price_range to the same value as price with '৳' appended
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

            case 'add_user':
                $required = ['first_name', 'last_name', 'email', 'phone', 'role', 'is_active'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Required field '$field' is missing");
                    }
                }
                
                // Check if passwords match if provided
                if (!empty($_POST['password']) && $_POST['password'] !== $_POST['confirm_password']) {
                    throw new Exception("Passwords do not match");
                }
                
                // Check if email already exists
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
                $stmt->execute([$_POST['email']]);
                if ($stmt->fetchColumn() > 0) {
                    throw new Exception("Email already exists");
                }
                
                $pdo->beginTransaction();
                
                try {
                    // Hash password if provided
                    $passwordHash = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : '';
                    
                    // Insert user
                    $stmt = $pdo->prepare("
                        INSERT INTO users 
                        (first_name, last_name, email, phone, password_hash, role, is_active, address, district, postcode)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([
                        $_POST['first_name'],
                        $_POST['last_name'],
                        $_POST['email'],
                        $_POST['phone'],
                        $passwordHash,
                        $_POST['role'],
                        $_POST['is_active'],
                        $_POST['address'] ?? null,
                        $_POST['district'] ?? null,
                        $_POST['postcode'] ?? null
                    ]);
                    
                    $userId = $pdo->lastInsertId();
                    
                    // If role is supplier, add to suppliers table
                    if ($_POST['role'] === 'supplier') {
                        $stmt = $pdo->prepare("
                            INSERT INTO suppliers 
                            (supplier_id, company_name, contact_person, supplier_since, bank_account, tax_id)
                            VALUES (?, ?, ?, ?, ?, ?)
                        ");
                        $stmt->execute([
                            $userId,
                            $_POST['company_name'] ?? null,
                            $_POST['contact_person'] ?? null,
                            $_POST['supplier_since'] ?? null,
                            $_POST['bank_account'] ?? null,
                            $_POST['tax_id'] ?? null
                        ]);
                    }
                    
                    $pdo->commit();
                    
                    echo json_encode(['success' => true, 'message' => 'User added successfully']);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
                break;

            case 'update_user':
                $required = ['user_id', 'first_name', 'last_name', 'email', 'phone', 'role', 'is_active'];
                foreach ($required as $field) {
                    if (empty($_POST[$field])) {
                        throw new Exception("Required field '$field' is missing");
                    }
                }
                
                // Check if passwords match if provided
                if (!empty($_POST['password']) && $_POST['password'] !== $_POST['confirm_password']) {
                    throw new Exception("Passwords do not match");
                }
                
                $userId = $_POST['user_id'];
                
                // Prevent changing your own role or status
                if ($userId == $_SESSION['user_id']) {
                    if ($_POST['role'] !== 'admin') {
                        throw new Exception("You cannot change your own role");
                    }
                    if ($_POST['is_active'] != 1) {
                        throw new Exception("You cannot deactivate your own account");
                    }
                }
                
                $pdo->beginTransaction();
                
                try {
                    // Check if email already exists for another user
                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ? AND user_id != ?");
                    $stmt->execute([$_POST['email'], $userId]);
                    if ($stmt->fetchColumn() > 0) {
                        throw new Exception("Email already exists for another user");
                    }
                    
                    // Get current user data to determine if we need to update password
                    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE user_id = ?");
                    $stmt->execute([$userId]);
                    $currentUser = $stmt->fetch();
                    
                    $passwordHash = !empty($_POST['password']) 
                        ? password_hash($_POST['password'], PASSWORD_DEFAULT) 
                        : $currentUser['password_hash'];
                    
                    // Update user
                    $stmt = $pdo->prepare("
                        UPDATE users SET 
                        first_name = ?, 
                        last_name = ?, 
                        email = ?, 
                        phone = ?, 
                        password_hash = ?, 
                        role = ?, 
                        is_active = ?, 
                        address = ?, 
                        district = ?, 
                        postcode = ? 
                        WHERE user_id = ?
                    ");
                    $stmt->execute([
                        $_POST['first_name'],
                        $_POST['last_name'],
                        $_POST['email'],
                        $_POST['phone'],
                        $passwordHash,
                        $_POST['role'],
                        $_POST['is_active'],
                        $_POST['address'] ?? null,
                        $_POST['district'] ?? null,
                        $_POST['postcode'] ?? null,
                        $userId
                    ]);
                    
                    // Handle supplier data
                    if ($_POST['role'] === 'supplier') {
                        // Check if supplier record exists
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM suppliers WHERE supplier_id = ?");
                        $stmt->execute([$userId]);
                        $supplierExists = $stmt->fetchColumn() > 0;
                        
                        if ($supplierExists) {
                            // Update existing supplier
                            $stmt = $pdo->prepare("
                                UPDATE suppliers SET
                                company_name = ?,
                                contact_person = ?,
                                supplier_since = ?,
                                bank_account = ?,
                                tax_id = ?
                                WHERE supplier_id = ?
                            ");
                            $stmt->execute([
                                $_POST['company_name'] ?? null,
                                $_POST['contact_person'] ?? null,
                                $_POST['supplier_since'] ?? null,
                                $_POST['bank_account'] ?? null,
                                $_POST['tax_id'] ?? null,
                                $userId
                            ]);
                        } else {
                            // Insert new supplier
                            $stmt = $pdo->prepare("
                                INSERT INTO suppliers 
                                (supplier_id, company_name, contact_person, supplier_since, bank_account, tax_id)
                                VALUES (?, ?, ?, ?, ?, ?)
                            ");
                            $stmt->execute([
                                $userId,
                                $_POST['company_name'] ?? null,
                                $_POST['contact_person'] ?? null,
                                $_POST['supplier_since'] ?? null,
                                $_POST['bank_account'] ?? null,
                                $_POST['tax_id'] ?? null
                            ]);
                        }
                    } else {
                        // Remove from suppliers table if role changed from supplier
                        $pdo->prepare("DELETE FROM suppliers WHERE supplier_id = ?")->execute([$userId]);
                    }
                    
                    $pdo->commit();
                    
                    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
                } catch (Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }
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

// Helper function to handle product relations
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