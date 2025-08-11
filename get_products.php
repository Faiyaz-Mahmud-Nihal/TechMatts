<?php
require_once 'db_connection.php';

header('Content-Type: application/json');

try {
    // Get all products
    $stmt = $pdo->query("SELECT * FROM products WHERE is_active = TRUE");
    $products = $stmt->fetchAll();

    // For each product, get related data
    foreach ($products as &$product) {
        // Get features
        $stmt = $pdo->prepare("SELECT feature FROM product_features WHERE product_id = ?");
        $stmt->execute([$product['product_id']]);
        $product['features'] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        // Get sizes if mousepad
        if ($product['category'] === 'mousepad') {
            $stmt = $pdo->prepare("SELECT dimensions, sku FROM product_sizes WHERE product_id = ?");
            $stmt->execute([$product['product_id']]);
            $product['sizes'] = $stmt->fetchAll();
        }

        // Get specs if pc build
        if ($product['category'] === 'pcbuild') {
            $stmt = $pdo->prepare("SELECT spec FROM product_specs WHERE product_id = ?");
            $stmt->execute([$product['product_id']]);
            $product['specs'] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        }

        // Get images
        $stmt = $pdo->prepare("SELECT image_url FROM product_images WHERE product_id = ?");
        $stmt->execute([$product['product_id']]);
        $images = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        $product['image'] = $images[0]; // Main image
        $product['thumbnails'] = array_slice($images, 1); // Additional images

        // Get categories
        $stmt = $pdo->prepare("SELECT category_name FROM product_categories WHERE product_id = ?");
        $stmt->execute([$product['product_id']]);
        $product['categories'] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);

        // Get tags
        $stmt = $pdo->prepare("SELECT tag_name FROM product_tags WHERE product_id = ?");
        $stmt->execute([$product['product_id']]);
        $product['tags'] = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
    }

    echo json_encode($products);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}