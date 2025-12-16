<!-- Floating Chat Widget -->
<style>
.chat-widget {
    position: fixed;
    bottom: 20px;
    right: 20px;
    z-index: 9999;
}
.chat-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    font-size: 1.8rem;
    cursor: pointer;
    box-shadow: 0 4px 20px rgba(102, 126, 234, 0.4);
    transition: all 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}
.chat-button:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 30px rgba(102, 126, 234, 0.6);
}
.chat-button .badge-notification {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    border: 2px solid white;
}
.chat-box {
    position: absolute;
    bottom: 80px;
    right: 0;
    width: 350px;
    max-width: calc(100vw - 40px);
    height: 500px;
    background: white;
    border-radius: 20px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    display: none;
    flex-direction: column;
    overflow: hidden;
    animation: slideUp 0.3s;
}
.chat-box.active {
    display: flex;
}
@keyframes slideUp {
    from { transform: translateY(20px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}
.chat-header-avatar {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.chat-header-info h5 {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
}
.chat-header-info p {
    margin: 0;
    font-size: 0.75rem;
    opacity: 0.9;
}
.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f8f9fa;
}
.chat-message {
    margin-bottom: 15px;
    display: flex;
    gap: 10px;
}
.chat-message.sent {
    flex-direction: row-reverse;
}
.chat-message-bubble {
    max-width: 75%;
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 0.9rem;
    line-height: 1.4;
}
.chat-message.received .chat-message-bubble {
    background: white;
    color: #333;
    border-bottom-left-radius: 4px;
}
.chat-message.sent .chat-message-bubble {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}
.chat-message-time {
    font-size: 0.7rem;
    color: #999;
    margin-top: 4px;
}
.chat-input-area {
    padding: 15px;
    background: white;
    border-top: 1px solid #eee;
    display: flex;
    gap: 10px;
}
.chat-input {
    flex: 1;
    border: 1px solid #ddd;
    border-radius: 25px;
    padding: 10px 18px;
    font-size: 0.9rem;
    outline: none;
}
.chat-input:focus {
    border-color: #667eea;
}
.chat-send-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    transition: all 0.3s;
}
.chat-send-btn:hover {
    transform: scale(1.1);
}
.chat-quick-replies {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    padding: 15px;
    border-top: 1px solid #eee;
}
.quick-reply-btn {
    background: white;
    border: 1px solid #667eea;
    color: #667eea;
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.3s;
}
.quick-reply-btn:hover {
    background: #667eea;
    color: white;
}
.typing-indicator {
    display: flex;
    gap: 4px;
    padding: 12px 16px;
    background: white;
    border-radius: 18px;
    width: fit-content;
}
.typing-indicator span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #999;
    animation: typing 1.4s infinite;
}
.typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
.typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); }
    30% { transform: translateY(-10px); }
}
</style>

<div class="chat-widget">
    <button class="chat-button" onclick="toggleChat()">
        <i class="bi bi-chat-dots-fill"></i>
        <span class="badge-notification" id="chatBadge" style="display: none;">1</span>
    </button>

    <div class="chat-box" id="chatBox">
        <div class="chat-header">
            <div class="chat-header-avatar">
                🎯
            </div>
            <div class="chat-header-info">
                <h5>JawaTrip Support</h5>
                <p><i class="bi bi-circle-fill text-success" style="font-size: 0.5rem;"></i> Online</p>
            </div>
            <button class="btn-close btn-close-white ms-auto" onclick="toggleChat()"></button>
        </div>

        <div class="chat-messages" id="chatMessages">
            <div class="chat-message received">
                <div>
                    <div class="chat-message-bubble">
                        Halo! 👋 Selamat datang di JawaTrip. Ada yang bisa kami bantu?
                    </div>
                    <div class="chat-message-time"><?= date('H:i') ?></div>
                </div>
            </div>
        </div>

        <div class="chat-quick-replies">
            <button class="quick-reply-btn" onclick="sendQuickReply('Cara booking?')">
                📋 Cara Booking
            </button>
            <button class="quick-reply-btn" onclick="sendQuickReply('Info harga')">
                💰 Info Harga
            </button>
            <button class="quick-reply-btn" onclick="sendQuickReply('Kontak CS')">
                📞 Kontak CS
            </button>
        </div>

        <div class="chat-input-area">
            <input type="text" class="chat-input" id="chatInput" placeholder="Ketik pesan..." 
                   onkeypress="if(event.key==='Enter') sendMessage()">
            <button class="chat-send-btn" onclick="sendMessage()">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>
</div>

<script>
let chatOpen = false;

function toggleChat() {
    chatOpen = !chatOpen;
    const chatBox = document.getElementById('chatBox');
    const badge = document.getElementById('chatBadge');
    
    if (chatOpen) {
        chatBox.classList.add('active');
        badge.style.display = 'none';
    } else {
        chatBox.classList.remove('active');
    }
}

function sendMessage() {
    const input = document.getElementById('chatInput');
    const message = input.value.trim();
    
    if (message) {
        addMessage(message, 'sent');
        input.value = '';
        
        // Simulate bot response
        setTimeout(() => {
            showTyping();
            setTimeout(() => {
                removeTyping();
                const response = getBotResponse(message);
                addMessage(response, 'received');
            }, 1500);
        }, 500);
    }
}

function sendQuickReply(text) {
    addMessage(text, 'sent');
    
    setTimeout(() => {
        showTyping();
        setTimeout(() => {
            removeTyping();
            const response = getBotResponse(text);
            addMessage(response, 'received');
        }, 1500);
    }, 500);
}

function addMessage(text, type) {
    const messagesDiv = document.getElementById('chatMessages');
    const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${type}`;
    messageDiv.innerHTML = `
        <div>
            <div class="chat-message-bubble">${text}</div>
            <div class="chat-message-time">${time}</div>
        </div>
    `;
    
    messagesDiv.appendChild(messageDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function showTyping() {
    const messagesDiv = document.getElementById('chatMessages');
    const typingDiv = document.createElement('div');
    typingDiv.className = 'chat-message received';
    typingDiv.id = 'typingIndicator';
    typingDiv.innerHTML = `
        <div class="typing-indicator">
            <span></span><span></span><span></span>
        </div>
    `;
    messagesDiv.appendChild(typingDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function removeTyping() {
    const typing = document.getElementById('typingIndicator');
    if (typing) typing.remove();
}

function getBotResponse(message) {
    const msg = message.toLowerCase();
    
    if (msg.includes('booking') || msg.includes('pesan')) {
        return 'Untuk booking, pilih paket wisata yang diinginkan, lalu klik tombol "Book Now". Isi data lengkap dan ikuti instruksi pembayaran. 🎫';
    } else if (msg.includes('harga') || msg.includes('biaya')) {
        return 'Harga paket mulai dari Rp 250.000 per orang. Harga sudah termasuk transportasi, tiket masuk, dan guide. Cek halaman paket untuk detail lengkap! 💰';
    } else if (msg.includes('kontak') || msg.includes('cs') || msg.includes('wa')) {
        return 'Customer Service kami siap membantu! 📞\nWhatsApp: 0812-3456-7890\nEmail: info@jawatrip.com\nJam operasional: 08:00 - 20:00 WIB';
    } else if (msg.includes('lokasi') || msg.includes('alamat')) {
        return 'Kantor kami ada di Malang, Jawa Timur. Untuk meeting point setiap trip akan disesuaikan dengan paket yang dipilih. 📍';
    } else if (msg.includes('pembayaran') || msg.includes('bayar')) {
        return 'Pembayaran bisa via transfer bank (BCA, Mandiri, BNI) atau e-wallet (GoPay, OVO, Dana). Upload bukti transfer setelah pembayaran ya! 💳';
    } else {
        return 'Terima kasih atas pertanyaannya! Tim CS kami akan segera merespons. Atau hubungi WhatsApp kami di 0812-3456-7890 untuk respon lebih cepat. 😊';
    }
}

// Show badge notification on first visit
setTimeout(() => {
    if (!chatOpen) {
        document.getElementById('chatBadge').style.display = 'flex';
    }
}, 3000);
</script>
