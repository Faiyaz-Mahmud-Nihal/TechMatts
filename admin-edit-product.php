<?php
require_once 'admin_auth.php';
require_once 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product | TechMatts Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #e2136e;
            --primary-dark: #c10e5d;
            --dark: #1e1e1e;
            --light: #f5f5f5;
            --gray: #aaa;
            --success: #4CAF50;
            --warning: #FFC107;
            --danger: #F44336;
            --info: #2196F3;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f9f9f9;
            color: #333;
        }
        
        .admin-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .admin-sidebar {
            width: 280px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            color: white;
            padding: 0;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            position: fixed;
            height: 100vh;
            z-index: 100;
        }
        
        .admin-logo {
            padding: 25px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 10px;
            text-align: center;
            background-color: rgba(0,0,0,0.2);
        }
        
        .admin-logo h2 {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            background: linear-gradient(to right, #e2136e, #ff8a00);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .admin-menu {
            list-style: none;
            padding: 0 15px;
        }
        
        .admin-menu li {
            margin-bottom: 5px;
            position: relative;
        }
        
        .admin-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: #ddd;
            text-decoration: none;
            transition: all 0.3s;
            border-radius: 6px;
            font-size: 15px;
        }
        
        .admin-menu a:hover {
            background: rgba(255,255,255,0.1);
            color: white;
            transform: translateX(5px);
        }
        
        .admin-menu a.active {
            background: linear-gradient(90deg, rgba(226,19,110,0.2) 0%, rgba(226,19,110,0) 100%);
            color: white;
            border-left: 3px solid var(--primary);
        }
        
        .admin-menu a i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 16px;
        }
        
        /* Main Content Styles */
        .admin-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
            background-color: #f5f5f5;
            min-height: 100vh;
        }
        
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .admin-header h1 {
            font-size: 28px;
            color: #333;
            font-weight: 600;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
        }
        
        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        
        .user-profile span {
            font-weight: 500;
            color: #555;
        }
        
        /* Form Styles */
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            max-width: 900px;
            margin: 0 auto;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .form-row {
            display: flex;
            gap: 20px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background-color: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .btn:hover {
            background-color: var(--primary-dark);
        }
        
        .btn-secondary {
            background-color: #6c757d;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
        }
        
        .image-preview {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        .image-preview-item {
            position: relative;
            width: 120px;
            height: 120px;
            border: 1px dashed #ddd;
            border-radius: 4px;
            overflow: hidden;
        }
        
        .image-preview-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .remove-image {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        
        .dynamic-field {
            margin-bottom: 15px;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 4px;
            position: relative;
        }
        
        .remove-field {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 2px 8px;
            cursor: pointer;
        }
        
        .add-field {
            background: var(--success);
            color: white;
            border: none;
            border-radius: 4px;
            padding: 8px 15px;
            margin-bottom: 20px;
            cursor: pointer;
        }
        
        .existing-images {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .existing-image {
            position: relative;
            width: 120px;
            height: 120px;
        }
        
        .existing-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 4px;
        }
        
        .existing-image .remove-existing {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <div class="admin-sidebar">
            <div class="admin-logo">
                <h2>TechMatts Admin</h2>
            </div>
            <ul class="admin-menu">
                <li><a href="admin.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="admin-products.php" class="active"><i class="fas fa-box-open"></i> Products</a></li>
                <li><a href="admin-orders.php"><i class="fas fa-shopping-cart"></i> Orders</a></li>
                <li><a href="admin-users.php"><i class="fas fa-users"></i> Users</a></li>
                <li><a href="admin-suppliers.php"><i class="fas fa-truck"></i> Suppliers</a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
        
        <!-- Main Content -->
        <div class="admin-content">
            <div class="admin-header">
                <h1>Edit Product</h1>
                <div class="user-profile">
                    <img src="https://ui-avatars.com/api/?name=Admin&background=e2136e&color=fff" alt="Admin">
                    <span>Admin</span>
                </div>
            </div>
            
            <div class="form-container">
                <form id="edit-product-form" enctype="multipart/form-data">
                    <input type="hidden" id="product-id" name="product_id">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-name">Product Name</label>
                            <input type="text" id="product-name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-category">Category</label>
                            <select id="product-category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="mousepad">Mousepad</option>
                                <option value="pcbuild">PC Build</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="product-price">Price (৳)</label>
                            <input type="number" id="product-price" name="price" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="product-status">Status</label>
                            <select id="product-status" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="product-description">Description</label>
                        <textarea id="product-description" name="description"></textarea>
                    </div>
                    
                    <!-- Mousepad specific fields -->
                    <div id="mousepad-fields" style="display: none;">
                        <div class="form-group">
                            <label for="product-type">Type</label>
                            <input type="text" id="product-type" name="type" placeholder="e.g., Speed, Control">
                        </div>
                        
                        <div class="form-group">
                            <label>Sizes</label>
                            <div id="size-fields">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-field" onclick="addSizeField()">Add Size</button>
                        </div>
                        
                        <div class="form-group">
                            <label>Features</label>
                            <div id="feature-fields">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-field" onclick="addFeatureField()">Add Feature</button>
                        </div>
                    </div>
                    
                    <!-- PC Build specific fields -->
                    <div id="pcbuild-fields" style="display: none;">
                        <div class="form-group">
                            <label>Specifications</label>
                            <div id="spec-fields">
                                <!-- Will be populated by JavaScript -->
                            </div>
                            <button type="button" class="add-field" onclick="addSpecField()">Add Specification</button>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Categories (for filtering)</label>
                        <div id="category-fields">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-field" onclick="addCategoryField()">Add Category</button>
                    </div>
                    
                    <div class="form-group">
                        <label>Tags</label>
                        <div id="tag-fields">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        <button type="button" class="add-field" onclick="addTagField()">Add Tag</button>
                    </div>
                    
                    <div class="form-group">
                        <label>Existing Images</label>
                        <div class="existing-images" id="existing-images">
                            <!-- Will be populated by JavaScript -->
                        </div>
                        
                        <label for="product-images">Add New Images</label>
                        <input type="file" id="product-images" name="images[]" multiple accept="image/*">
                        <small>First image will be used as the main image</small>
                        
                        <div class="image-preview" id="image-preview"></div>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn">Update Product</button>
                        <a href="admin-products.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Show/hide category-specific fields
        document.getElementById('product-category').addEventListener('change', function() {
            const category = this.value;
            document.getElementById('mousepad-fields').style.display = category === 'mousepad' ? 'block' : 'none';
            document.getElementById('pcbuild-fields').style.display = category === 'pcbuild' ? 'block' : 'none';
        });
        
        // Image preview for new images
        document.getElementById('product-images').addEventListener('change', function(e) {
            const preview = document.getElementById('image-preview');
            preview.innerHTML = '';
            
            for (const file of e.target.files) {
                const reader = new FileReader();
                const previewItem = document.createElement('div');
                previewItem.className = 'image-preview-item';
                
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    previewItem.appendChild(img);
                    
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-image';
                    removeBtn.innerHTML = '&times;';
                    removeBtn.onclick = function() {
                        previewItem.remove();
                        // TODO: Remove from file list
                    };
                    previewItem.appendChild(removeBtn);
                };
                
                reader.readAsDataURL(file);
                preview.appendChild(previewItem);
            }
        });
        
        // Dynamic field functions
        let sizeFieldCount = 0;
        let featureFieldCount = 0;
        let specFieldCount = 0;
        let categoryFieldCount = 0;
        let tagFieldCount = 0;
        
        function addSizeField(dimensions = '', sku = '') {
            const container = document.getElementById('size-fields');
            const field = document.createElement('div');
            field.className = 'dynamic-field';
            field.innerHTML = `
                <div class="form-row">
                    <div class="form-group">
                        <label>Dimensions</label>
                        <input type="text" name="sizes[${sizeFieldCount}][dimensions]" placeholder="e.g., 900 X 400 X 4mm" value="${dimensions}">
                    </div>
                    <div class="form-group">
                        <label>SKU</label>
                        <input type="text" name="sizes[${sizeFieldCount}][sku]" placeholder="e.g., product-900-400-1" value="${sku}">
                    </div>
                </div>
                <button type="button" class="remove-field" onclick="removeField(this)">Remove</button>
            `;
            container.appendChild(field);
            sizeFieldCount++;
        }
        
        function addFeatureField(feature = '') {
            const container = document.getElementById('feature-fields');
            const field = document.createElement('div');
            field.className = 'dynamic-field';
            field.innerHTML = `
                <input type="text" name="features[]" placeholder="e.g., Watersplash Proof" value="${feature}">
                <button type="button" class="remove-field" onclick="removeField(this)">Remove</button>
            `;
            container.appendChild(field);
            featureFieldCount++;
        }
        
        function addSpecField(spec = '') {
            const container = document.getElementById('spec-fields');
            const field = document.createElement('div');
            field.className = 'dynamic-field';
            field.innerHTML = `
                <input type="text" name="specs[]" placeholder="e.g., AMD Ryzen 7 7700" value="${spec}">
                <button type="button" class="remove-field" onclick="removeField(this)">Remove</button>
            `;
            container.appendChild(field);
            specFieldCount++;
        }
        
        function addCategoryField(category = '') {
            const container = document.getElementById('category-fields');
            const field = document.createElement('div');
            field.className = 'dynamic-field';
            field.innerHTML = `
                <input type="text" name="categories[]" placeholder="e.g., Mousepad, Pre Order" value="${category}">
                <button type="button" class="remove-field" onclick="removeField(this)">Remove</button>
            `;
            container.appendChild(field);
            categoryFieldCount++;
        }
        
        function addTagField(tag = '') {
            const container = document.getElementById('tag-fields');
            const field = document.createElement('div');
            field.className = 'dynamic-field';
            field.innerHTML = `
                <input type="text" name="tags[]" placeholder="e.g., deal, design" value="${tag}">
                <button type="button" class="remove-field" onclick="removeField(this)">Remove</button>
            `;
            container.appendChild(field);
            tagFieldCount++;
        }
        
        function removeField(button) {
            button.parentElement.remove();
        }
        
        // Load product data
        document.addEventListener('DOMContentLoaded', async function() {
            const productId = new URLSearchParams(window.location.search).get('id');
            if (!productId) {
                alert('No product ID specified');
                window.location.href = 'admin-products.php';
                return;
            }
            
            try {
                const response = await fetch(`get_product.php?id=${productId}`);
                const product = await response.json();
                
                if (!product) {
                    alert('Product not found');
                    window.location.href = 'admin-products.php';
                    return;
                }
                
                // Populate basic fields
                document.getElementById('product-id').value = product.product_id;
                document.getElementById('product-name').value = product.name;
                document.getElementById('product-category').value = product.category;
                document.getElementById('product-price').value = product.price;
                document.getElementById('product-description').value = product.description || '';
                document.getElementById('product-status').value = product.is_active ? '1' : '0';
                document.getElementById('product-type').value = product.type || '';
                
                // Trigger category change to show/hide fields
                document.getElementById('product-category').dispatchEvent(new Event('change'));
                
                // Populate sizes (for mousepads)
                if (product.category === 'mousepad' && product.sizes) {
                    product.sizes.forEach(size => {
                        addSizeField(size.dimensions, size.sku);
                    });
                }
                
                // Populate features (for mousepads)
                if (product.category === 'mousepad' && product.features) {
                    product.features.forEach(feature => {
                        addFeatureField(feature);
                    });
                }
                
                // Populate specs (for pc builds)
                if (product.category === 'pcbuild' && product.specs) {
                    product.specs.forEach(spec => {
                        addSpecField(spec);
                    });
                }
                
                // Populate categories
                if (product.categories) {
                    product.categories.forEach(category => {
                        addCategoryField(category);
                    });
                }
                
                // Populate tags
                if (product.tags) {
                    product.tags.forEach(tag => {
                        addTagField(tag);
                    });
                }
                
                // Populate existing images
                const existingImagesContainer = document.getElementById('existing-images');
                if (product.image && product.thumbnails) {
                    const allImages = [product.image, ...product.thumbnails];
                    allImages.forEach((image, index) => {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'existing-image';
                        imgDiv.innerHTML = `
                            <img src="${image}" alt="Product Image ${index + 1}">
                            <button class="remove-existing" data-image="${image}">&times;</button>
                        `;
                        existingImagesContainer.appendChild(imgDiv);
                    });
                    
                    // Add click handlers for remove image buttons
                    document.querySelectorAll('.remove-existing').forEach(btn => {
                        btn.addEventListener('click', function(e) {
                            e.preventDefault();
                            if (confirm('Are you sure you want to remove this image?')) {
                                const imageUrl = this.dataset.image;
                                // TODO: Send request to remove image
                                this.parentElement.remove();
                            }
                        });
                    });
                }
                
            } catch (error) {
                console.error('Error loading product:', error);
                alert('Error loading product data');
                window.location.href = 'admin-products.php';
            }
        });
        
        // Form submission
        document.getElementById('edit-product-form').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('admin_api.php?action=update_product', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Product updated successfully!');
                    window.location.href = 'admin-products.php';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('An error occurred. Please try again.');
                console.error(error);
            }
        });
    </script>
</body>
</html>