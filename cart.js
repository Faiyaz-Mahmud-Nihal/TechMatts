// cart.js
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Update cart counter in navbar
function updateCartCounter() {
    const count = cart.reduce((total, item) => total + item.quantity, 0);
    document.querySelectorAll('.cart-count').forEach(el => el.textContent = count);
}

// Add item to cart (called from product pages)
function addToCart(product) {
    // Check if the product already exists in cart (including size for mousepads)
    const existingItem = cart.find(item => 
        item.id === product.id && 
        (product.size ? item.size === product.size : true)
    );
    
    if (existingItem) {
        existingItem.quantity += product.quantity;
    } else {
        cart.push({ ...product, quantity: product.quantity });
    }
    
    saveCart();
    updateCartCounter();
}

// Remove item from cart
function removeFromCart(productId) {
    cart = cart.filter(item => item.id !== productId);
    saveCart();
    renderCartItems();
}

// Update quantity
function updateQuantity(productId, newQuantity) {
    const item = cart.find(item => item.id === productId);
    if (item) {
        item.quantity = Math.max(1, newQuantity);
        saveCart();
        renderCartItems();
    }
}

// Save cart to localStorage
function saveCart() {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCounter();
}

// Render cart items (for cart.html)
function renderCartItems() {
    const container = document.getElementById('cart-items');
    if (!container) return;

    container.innerHTML = cart.length > 0 
        ? cart.map(item => `
            <tr class="cart-item" data-id="${item.id}">
                <td><img src="${item.image}" alt="${item.name}"></td>
                <td>${item.name}${item.size ? `<br><small>Size: ${item.size}</small>` : ''}</td>
                <td>${item.id}</td>
                <td>
                    <div class="quantity-controls">
                        <button class="quantity-btn" data-action="decrease">−</button>
                        <span class="quantity">${item.quantity}</span>
                        <button class="quantity-btn" data-action="increase">+</button>
                    </div>
                </td>
                <td>${item.price}৳</td>
                <td>${(item.price * item.quantity).toFixed(2)}৳</td>
                <td><button class="remove-item"><i class="fas fa-trash"></i></button></td>
            </tr>
        `).join('')
        : '<tr><td colspan="7" class="empty-cart">Your cart is empty. <a href="shop.html">Start shopping!</a></td></tr>';

    // Add event listeners
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.closest('.cart-item').dataset.id;
            const action = this.dataset.action;
            const item = cart.find(item => item.id === itemId);
            updateQuantity(itemId, action === 'increase' ? item.quantity + 1 : item.quantity - 1);
        });
    });

    document.querySelectorAll('.remove-item').forEach(btn => {
        btn.addEventListener('click', function() {
            const itemId = this.closest('.cart-item').dataset.id;
            removeFromCart(itemId);
        });
    });

    updateTotals();
}

// Calculate totals
function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    if (document.getElementById('subtotal')) {
        document.getElementById('subtotal').textContent = `${subtotal.toFixed(2)}৳`;
        document.getElementById('total').textContent = `${subtotal.toFixed(2)}৳`;
    }
}

// Initialize cart
document.addEventListener('DOMContentLoaded', function() {
    updateCartCounter();
    if (document.getElementById('cart-items')) {
        renderCartItems();
    }

    // Checkout button
    document.getElementById('checkout-btn')?.addEventListener('click', () => {
        alert('Proceeding to checkout!'); // Replace with actual checkout logic
    });

    // Coupon/voucher buttons (placeholder)
    document.getElementById('apply-coupon')?.addEventListener('click', () => {
        alert('Coupon applied! (Demo)');
    });

    document.getElementById('apply-voucher')?.addEventListener('click', () => {
        alert('Voucher applied! (Demo)');
    });
});

// Make addToCart globally available
window.addToCart = addToCart;
window.updateCartCounter = updateCartCounter;