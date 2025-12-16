<!-- Wishlist Button Component -->
<style>
.wishlist-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 45px;
    height: 45px;
    background: rgba(255,255,255,0.95);
    border: none;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    z-index: 100;
    box-shadow: 0 2px 10px rgba(0,0,0,0.15);
}
.wishlist-btn:hover {
    transform: scale(1.1);
    background: white;
}
.wishlist-btn i {
    font-size: 1.5rem;
    color: #dc3545;
    transition: all 0.3s;
}
.wishlist-btn.active i {
    animation: heartBeat 0.5s;
}
.wishlist-btn.in-wishlist i {
    color: #dc3545;
}
.wishlist-btn:not(.in-wishlist) i {
    color: #ddd;
}
@keyframes heartBeat {
    0%, 100% { transform: scale(1); }
    25% { transform: scale(1.3); }
    50% { transform: scale(1.1); }
    75% { transform: scale(1.2); }
}
.wishlist-badge {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border-radius: 50px;
    padding: 5px 12px;
    font-size: 0.85rem;
    font-weight: 600;
}
</style>

<script>
function toggleWishlist(idPaket, button) {
    <?php if (!isset($_SESSION['login']) || !$_SESSION['login']): ?>
        alert('Login terlebih dahulu untuk menambahkan ke favorit!');
        window.location.href = 'login.php';
        return;
    <?php endif; ?>
    
    fetch('wishlist_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=toggle_wishlist&id_paket=${idPaket}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            button.classList.add('active');
            setTimeout(() => button.classList.remove('active'), 500);
            
            if (data.action === 'added') {
                button.classList.add('in-wishlist');
                showToast('❤️ Ditambahkan ke favorit!');
            } else {
                button.classList.remove('in-wishlist');
                showToast('💔 Dihapus dari favorit');
            }
            
            updateWishlistCount();
        }
    });
}

function checkWishlistStatus(idPaket, button) {
    <?php if (!isset($_SESSION['login']) || !$_SESSION['login']): ?>
        return;
    <?php endif; ?>
    
    fetch('wishlist_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `action=check_wishlist&id_paket=${idPaket}`
    })
    .then(res => res.json())
    .then(data => {
        if (data.in_wishlist) {
            button.classList.add('in-wishlist');
        }
    });
}

function updateWishlistCount() {
    <?php if (!isset($_SESSION['login']) || !$_SESSION['login']): ?>
        return;
    <?php endif; ?>
    
    fetch('wishlist_handler.php?action=get_count')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('wishlistCount');
            if (badge) {
                badge.textContent = data.count;
                badge.style.display = data.count > 0 ? 'inline-block' : 'none';
            }
        });
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: white;
        padding: 15px 25px;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        z-index: 9999;
        animation: slideIn 0.3s;
    `;
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s';
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// Initial count update
updateWishlistCount();
</script>

<style>
@keyframes slideIn {
    from { transform: translateX(400px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
@keyframes slideOut {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(400px); opacity: 0; }
}
</style>
