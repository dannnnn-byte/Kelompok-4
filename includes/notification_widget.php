<?php
ob_start();
?>
<?php ob_start(); ?>
<!-- Notification Bell Widget -->
<style>
.notification-bell {
    position: relative;
    cursor: pointer;
    margin-right: 20px;
}
.notification-icon {
    font-size: 1.5rem;
    color: #333;
    transition: all 0.3s;
}
.notification-icon:hover {
    color: #667eea;
    transform: scale(1.1);
}
.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background: #dc3545;
    color: white;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    font-weight: 700;
    animation: pulse 2s infinite;
}
@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.2); }
}
.notification-dropdown {
    position: absolute;
    top: 50px;
    right: 0;
    width: 380px;
    max-width: 90vw;
    background: white;
    border-radius: 15px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    display: none;
    z-index: 1000;
    animation: slideDown 0.3s;
}
.notification-dropdown.active {
    display: block;
}
@keyframes slideDown {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.notification-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.notification-header h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 700;
}
.notification-list {
    max-height: 400px;
    overflow-y: auto;
}
.notification-item {
    padding: 15px 20px;
    border-bottom: 1px solid #f5f5f5;
    transition: all 0.3s;
    cursor: pointer;
    display: flex;
    gap: 12px;
}
.notification-item:hover {
    background: #f8f9fa;
}
.notification-item:last-child {
    border-bottom: none;
}
.notification-icon-wrapper {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.2rem;
}
.notif-order {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}
.notif-payment {
    background: rgba(255, 193, 7, 0.1);
    color: #ffc107;
}
.notif-confirm {
    background: rgba(0, 123, 255, 0.1);
    color: #007bff;
}
.notification-content {
    flex: 1;
}
.notification-message {
    font-size: 0.9rem;
    color: #333;
    margin-bottom: 5px;
    font-weight: 600;
}
.notification-time {
    font-size: 0.75rem;
    color: #999;
}
.notification-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
    text-align: center;
}
.notification-footer a {
    color: #667eea;
    font-weight: 600;
    text-decoration: none;
    font-size: 0.9rem;
}
.notification-footer a:hover {
    color: #764ba2;
}
.empty-notifications {
    padding: 40px 20px;
    text-align: center;
    color: #999;
}
.empty-notifications i {
    font-size: 3rem;
    margin-bottom: 15px;
    opacity: 0.5;
}
.mark-read-btn {
    background: transparent;
    border: none;
    color: #667eea;
    font-size: 0.85rem;
    cursor: pointer;
    padding: 5px 10px;
}
.mark-read-btn:hover {
    color: #764ba2;
}
</style>

<div class="notification-bell" id="notificationBell">
    <i class="bi bi-bell-fill notification-icon"></i>
    <span class="notification-badge" id="notificationBadge" style="display: none;">0</span>
    
    <div class="notification-dropdown" id="notificationDropdown">
        <div class="notification-header">
            <h5>🔔 Notifikasi</h5>
            <button class="mark-read-btn" onclick="markAllRead()">
                Tandai dibaca
            </button>
        </div>
        
        <div class="notification-list" id="notificationList">
            <div class="empty-notifications">
                <i class="bi bi-bell"></i>
                <p>Belum ada notifikasi</p>
            </div>
        </div>
        
        <div class="notification-footer">
            <a href="<?= ($_SESSION['role'] ?? '') === 'admin' ? 'admin/dashboard.php' : 'riwayat.php' ?>">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
</div>

<script>
let notificationOpen = false;

// Toggle notification dropdown
document.getElementById('notificationBell').addEventListener('click', function(e) {
    e.stopPropagation();
    notificationOpen = !notificationOpen;
    const dropdown = document.getElementById('notificationDropdown');
    dropdown.classList.toggle('active', notificationOpen);
    
    if (notificationOpen) {
        loadNotifications();
    }
});

// Close on outside click
document.addEventListener('click', function(e) {
    if (notificationOpen && !e.target.closest('.notification-bell')) {
        document.getElementById('notificationDropdown').classList.remove('active');
        notificationOpen = false;
    }
});

function loadNotifications() {
    fetch('notification_handler.php?action=get_notifications')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('notificationList');
            
            if (data.notifications.length === 0) {
                list.innerHTML = `
                    <div class="empty-notifications">
                        <i class="bi bi-bell"></i>
                        <p>Belum ada notifikasi</p>
                    </div>
                `;
            } else {
                list.innerHTML = data.notifications.map(notif => {
                    const icon = getNotificationIcon(notif.type, notif.status);
                    const timeAgo = getTimeAgo(notif.created_at);
                    
                    return `
                        <div class="notification-item">
                            <div class="notification-icon-wrapper ${icon.class}">
                                <i class="bi ${icon.icon}"></i>
                            </div>
                            <div class="notification-content">
                                <div class="notification-message">${notif.message}</div>
                                <div class="notification-time">${timeAgo}</div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        });
}

function updateNotificationCount() {
    fetch('notification_handler.php?action=get_unread_count')
        .then(res => res.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (data.count > 0) {
                badge.textContent = data.count > 9 ? '9+' : data.count;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        });
}

function getNotificationIcon(type, status) {
    if (type === 'order') {
        return { icon: 'bi-cart-check-fill', class: 'notif-order' };
    } else if (status === 'pending') {
        return { icon: 'bi-clock-fill', class: 'notif-payment' };
    } else if (status === 'paid' || status === 'lunas') {
        return { icon: 'bi-check-circle-fill', class: 'notif-confirm' };
    }
    return { icon: 'bi-info-circle-fill', class: 'notif-order' };
}

function getTimeAgo(datetime) {
    const now = new Date();
    // Parse datetime and ensure it's interpreted as UTC from server
    const past = new Date(datetime + ' UTC');
    
    // If parsing fails, try alternative format
    if (isNaN(past.getTime())) {
        // Try parsing as ISO format
        const parsed = new Date(datetime.replace(' ', 'T'));
        if (!isNaN(parsed.getTime())) {
            return getTimeAgo(parsed.toISOString());
        }
        return 'Waktu tidak valid';
    }
    
    const diffMs = now - past;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'Baru saja';
    if (diffMins < 60) return `${diffMins} menit lalu`;
    if (diffHours < 24) return `${diffHours} jam lalu`;
    if (diffDays < 7) return `${diffDays} hari lalu`;
    return past.toLocaleDateString('id-ID');
}

function markAllRead() {
    fetch('notification_handler.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=mark_read'
    })
    .then(() => {
        updateNotificationCount();
    });
}

// Auto-update every 30 seconds
setInterval(updateNotificationCount, 30000);

// Initial load
updateNotificationCount();
</script>
<?php ob_end_flush(); ?>
