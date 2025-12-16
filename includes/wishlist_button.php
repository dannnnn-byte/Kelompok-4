<!-- Wishlist Button Component -->
<style>
/* Luxury Love Button for Wishlist - SIMPLE & CLEAN */
.wishlist-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 56px;
    height: 56px;
    background: white;
    border: 3px solid #cdaa7d;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 1000;
    box-shadow: 0 6px 20px rgba(0,0,0,0.18);
    padding: 0;
    line-height: 1;
}
.wishlist-btn:hover {
    transform: translateY(-4px) scale(1.1);
    box-shadow: 0 10px 28px rgba(0,0,0,0.25);
}
.wishlist-btn i {
    font-size: 1.9rem;
    color: #999;
    transition: all 0.3s ease;
    display: block;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.wishlist-btn.active i {
    animation: heartBeat 600ms ease;
}
.wishlist-btn.in-wishlist {
    border-color: #ff1744;
    box-shadow: 0 8px 24px rgba(255, 23, 68, 0.3);
}
.wishlist-btn.in-wishlist i {
    color: #ff1744;
    text-shadow: 0 0 16px rgba(255, 23, 68, 0.6);
}
.wishlist-btn:hover i {
    transform: scale(1.2);
}
@keyframes heartBeat {
    0%, 100% { transform: scale(1); }
    14% { transform: scale(1.3); }
    28% { transform: scale(1.1); }
    42% { transform: scale(1.25); }
    56% { transform: scale(1.15); }
}
.wishlist-btn::after {
    content: 'Favorit';
    position: absolute;
    right: 60px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(20,92,67,0.9);
    color: #fff;
    padding: 6px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: .2px;
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s ease, transform .2s ease;
    box-shadow: 0 6px 16px rgba(0,0,0,0.18);
}
.wishlist-btn:hover::after { opacity: 1; transform: translateY(-50%) translateX(-2px); }

.wishlist-badge {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 65%, #ffd479 100%);
    color: white;
    border-radius: 50px;
    padding: 5px 12px;
    font-size: 0.85rem;
    font-weight: 700;
    box-shadow: 0 6px 18px rgba(245, 87, 108, 0.35);
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
