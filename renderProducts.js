// renderProducts.js
document.addEventListener('DOMContentLoaded', function() {
    // Initialize cart
    updateCartCounter();
    
    // Render products if on shop page
    if (document.querySelector('.products-grid')) {
        renderAllProducts();
        setupCategoryFilter();
    }
    
    // Load product details if on product page
    if (window.location.pathname.includes('product-details.html')) {
        loadProductDetails();
    }
});

function renderAllProducts() {
    const container = document.getElementById('products-container');
    if (!container) return;
    
    container.innerHTML = products.map(product => `
        <div class="product-card" data-category="${product.category}">
            <a href="product-details.html?id=${product.id}">
                <div class="product-image">
                    <img src="${product.image}" alt="${product.name}">
                </div>
                <h3>${product.name}</h3>
            </a>
            <div class="product-info">
                <p class="price">${product.priceRange || product.price}৳</p>
                <button class="add-to-cart" 
                    data-id="${product.id}"
                    data-name="${product.name}"
                    data-price="${product.price}"
                    data-image="${product.image}">
                    Add to Cart
                </button>
            </div>
        </div>
    `).join('');
    
    // Add cart event listeners
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', addToCartHandler);
    });
}

function setupCategoryFilter() {
    document.querySelectorAll('.category-filter').forEach(filter => {
        filter.addEventListener('click', function(e) {
            e.preventDefault();
            const category = this.dataset.category;
            
            // Update active filter
            document.querySelectorAll('.category-filter').forEach(f => f.classList.remove('active'));
            this.classList.add('active');
            
            // Filter products
            document.querySelectorAll('.product-card').forEach(card => {
                card.style.display = (category === 'all' || card.dataset.category === category) 
                    ? 'block' 
                    : 'none';
            });
        });
    });
}

function loadProductDetails() {
    const productId = new URLSearchParams(window.location.search).get('id');
    const product = products.find(p => p.id === productId);
    
    if (!product) {
        document.getElementById('product-container').innerHTML = `
            <div class="error">Product not found</div>
        `;
        return;
    }
    
    // Update product details page
    document.title = `${product.name} | TechMatts`;
    document.getElementById('main-product-image').src = product.image;
    document.getElementById('main-product-image').alt = product.name;
    
    // Render thumbnails
    const thumbsContainer = document.querySelector('.thumbnails');
    thumbsContainer.innerHTML = product.thumbnails.map((thumb, i) => `
        <div class="thumbnail ${i === 0 ? 'active' : ''}" data-image="${thumb}">
            <img src="${thumb}" alt="${product.name} - View ${i+1}">
        </div>
    `).join('');
    
    // Add thumbnail click handlers
    document.querySelectorAll('.thumbnail').forEach(thumb => {
        thumb.addEventListener('click', function() {
            document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('main-product-image').src = this.dataset.image;
        });
    });
    
    // Update product info
    document.querySelector('.product-name').textContent = product.name;
    document.querySelector('.product-price').textContent = product.priceRange || `${product.price}৳`;
    
    // Update specs/features - DIFFERENT HANDLING FOR PC BUILDS
    const featuresList = document.querySelector('.product-features ul');
    if (product.category === 'pcbuild') {
        // Handle PC builds with specs
        featuresList.innerHTML = product.specs.map(spec => `<li>${spec}</li>`).join('');
        // Remove type and size for PC builds
        document.querySelector('.product-features li:first-child').remove();
        document.querySelector('.product-features li:first-child').remove();
    } else {
        // Handle mousepads with features
        featuresList.innerHTML = `
            <li><strong>Type:</strong> ${product.type}</li>
            <li><strong>Size:</strong> <span id="selected-size">${product.sizes[0].dimensions}</span></li>
            ${product.features.map(feat => `<li>${feat}</li>`).join('')}
        `;
    }
    
    // Update size options (only for mousepads)
    const sizeButtons = document.querySelector('.size-buttons');
    if (product.sizes) {
        sizeButtons.innerHTML = product.sizes.map((size, i) => `
            <button class="size-btn ${i === 0 ? 'active' : ''}" 
                    data-size="${size.dimensions}" 
                    data-sku="${size.sku}">
                ${size.dimensions}
            </button>
        `).join('');
        
        // Add size selection handler
        document.querySelectorAll('.size-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.size-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                document.getElementById('selected-size').textContent = this.dataset.size;
            });
        });
    } else {
        // Hide size options for PC builds
        document.querySelector('.size-options').style.display = 'none';
    }
    
    // Update meta information
    document.querySelector('.product-meta p:first-child').innerHTML = `<strong>SKU:</strong> ${product.sizes ? product.sizes[0].sku : product.id}`;
    document.querySelector('.product-meta p:nth-child(2)').innerHTML = `<strong>Categories:</strong> ${product.categories.join(', ')}`;
    document.querySelector('.product-meta p:last-child').innerHTML = `<strong>Tags:</strong> ${product.tags.join(', ')}`;
    
    // Update add to cart button
    const addToCartBtn = document.querySelector('.add-to-cart');
    addToCartBtn.dataset.id = product.id;
    addToCartBtn.dataset.name = product.name;
    addToCartBtn.dataset.price = product.price;
    addToCartBtn.dataset.image = product.image;
    addToCartBtn.addEventListener('click', addToCartHandler);
}

function addToCartHandler() {
    const product = {
        id: this.dataset.id,
        name: this.dataset.name,
        price: parseFloat(this.dataset.price),
        image: this.dataset.image,
        quantity: 1
    };
    
    addToCart(product);
}

// In renderProducts.js
function setupCategoryFilter() {
    document.querySelectorAll('.category-item').forEach(button => {
        button.addEventListener('click', function() {
            // Update active button
            document.querySelectorAll('.category-item').forEach(btn => 
                btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter products
            const category = this.dataset.category;
            document.querySelectorAll('.product-card').forEach(card => {
                if (category === 'all' || card.dataset.category === category) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

// Call this in your DOMContentLoaded event
setupCategoryFilter();