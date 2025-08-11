<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

$productId = $_GET['id'] ?? '';

if (empty($productId)) {
    http_response_code(400);
    echo json_encode(['error' => 'Product ID is required']);
    exit;
}

try {
    // Get product
    $stmt = $pdo->prepare("SELECT * FROM products WHERE product_id = ? AND is_active = TRUE");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        http_response_code(404);
        echo json_encode(['error' => 'Product not found']);
        exit;
    }

    // Get features
    $stmt = $pdo->prepare("SELECT feature FROM product_features WHERE product_id = ?");
    $stmt->execute([$productId]);
    $product['features'] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // Get sizes if mousepad
    if ($product['category'] === 'mousepad') {
        $stmt = $pdo->prepare("SELECT dimensions, sku FROM product_sizes WHERE product_id = ?");
        $stmt->execute([$productId]);
        $product['sizes'] = $stmt->fetchAll();
    }

    // Get specs if pc build
    if ($product['category'] === 'pcbuild') {
        $stmt = $pdo->prepare("SELECT spec FROM product_specs WHERE product_id = ?");
        $stmt->execute([$productId]);
        $product['specs'] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    // Get images
    $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
    $stmt->execute([$productId]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    $product['image'] = $images[0]; // Main image
    $product['thumbnails'] = array_slice($images, 1); // Additional images

    // Get categories
    $stmt = $pdo->prepare("SELECT category_name FROM product_categories WHERE product_id = ?");
    $stmt->execute([$productId]);
    $product['categories'] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    // Get tags
    $stmt = $pdo->prepare("SELECT tag_name FROM product_tags WHERE product_id = ?");
    $stmt->execute([$productId]);
    $product['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

    echo json_encode($product);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}