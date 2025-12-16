<!-- Review & Rating Widget -->
<style>
.review-section {
    background: #f8f9fa;
    padding: 40px 0;
    margin-top: 50px;
}
.rating-overview {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}
.rating-score {
    font-size: 3.5rem;
    font-weight: 800;
    color: #FFD700;
}
.rating-bar {
    height: 8px;
    background: #e0e0e0;
    border-radius: 10px;
    overflow: hidden;
}
.rating-bar-fill {
    height: 100%;
    background: linear-gradient(90deg, #FFD700, #FFA500);
    transition: width 0.5s ease;
}
.review-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    transition: transform 0.3s;
}
.review-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.stars {
    color: #FFD700;
    font-size: 1.2rem;
}
.btn-review {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 50px;
    padding: 12px 30px;
    font-weight: 600;
    transition: all 0.3s;
}
.btn-review:hover {
    transform: scale(1.05);
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
}
.star-rating {
    display: flex;
    gap: 10px;
    cursor: pointer;
    justify-content: center;
    padding: 15px 0;
}
.star-rating i {
    font-size: 2.5rem;
    color: #ddd;
    transition: all 0.2s ease;
    cursor: pointer;
    user-select: none;
}
.star-rating i:hover {
    color: #FFD700;
    transform: scale(1.3);
}
.star-rating i.active {
    color: #FFD700;
    transform: scale(1.3);
}
.review-form {
    background: white;
    border-radius: 15px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.2rem;
}
</style>

<section class="review-section">
    <div class="container">
        <h2 class="fw-bold mb-4">⭐ Review & Rating</h2>

        <div class="row">
            <!-- Rating Overview -->
            <div class="col-lg-4 mb-4">
                <div class="rating-overview text-center">
                    <div class="rating-score" id="avgRating">0.0</div>
                    <div class="stars mb-2" id="avgStars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <p class="text-muted mb-4"><span id="totalReviews">0</span> Reviews</p>

                    <!-- Rating Bars -->
                    <div id="ratingStats"></div>

                    <?php if (isset($_SESSION['login']) && $_SESSION['login']): ?>
                    <button class="btn btn-review mt-3 w-100" data-bs-toggle="modal" data-bs-target="#reviewModal">
                        <i class="bi bi-pencil-square"></i> Tulis Review
                    </button>
                    <?php else: ?>
                    <button class="btn btn-review mt-3 w-100" onclick="alert('Login terlebih dahulu untuk menulis review')">
                        <i class="bi bi-pencil-square"></i> Tulis Review
                    </button>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Reviews List -->
            <div class="col-lg-8">
                <div id="reviewsList">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Review Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">✍️ Tulis Review Anda</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <div class="text-center mb-4">
                        <label class="form-label fw-semibold">Rating Anda:</label>
                        <div class="star-rating" id="starRating">
                            <i class="bi bi-star-fill" data-rating="1"></i>
                            <i class="bi bi-star-fill" data-rating="2"></i>
                            <i class="bi bi-star-fill" data-rating="3"></i>
                            <i class="bi bi-star-fill" data-rating="4"></i>
                            <i class="bi bi-star-fill" data-rating="5"></i>
                        </div>
                        <input type="hidden" name="rating" id="ratingInput" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Review:</label>
                        <textarea class="form-control" name="review" rows="5" 
                                  placeholder="Ceritakan pengalaman Anda..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-review w-100">
                        <i class="bi bi-send"></i> Submit Review
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const idPaket = <?= $id_paket ?? 1 ?>;

// Star Rating Interaction
function setupStarRating() {
    const starElements = document.querySelectorAll('#starRating i');
    const ratingInput = document.getElementById('ratingInput');
    
    starElements.forEach((star, index) => {
        // Click event
        star.addEventListener('click', function(e) {
            e.stopPropagation();
            const rating = index + 1;
            ratingInput.value = rating;
            
            // Update visual state
            starElements.forEach((s, idx) => {
                if (idx < rating) {
                    s.classList.add('active');
                    s.style.color = '#FFD700';
                } else {
                    s.classList.remove('active');
                    s.style.color = '#ddd';
                }
            });
        });
        
        // Hover effect
        star.addEventListener('mouseenter', function() {
            const hoverRating = index + 1;
            starElements.forEach((s, idx) => {
                if (idx < hoverRating) {
                    s.style.color = '#FFD700';
                } else {
                    s.style.color = '#ddd';
                }
            });
        });
    });
    
    // Remove hover when leaving
    document.getElementById('starRating')?.addEventListener('mouseleave', function() {
        const currentRating = parseInt(ratingInput.value) || 0;
        starElements.forEach((s, idx) => {
            if (idx < currentRating) {
                s.style.color = '#FFD700';
            } else {
                s.style.color = '#ddd';
            }
        });
    });
}

// Setup stars when page loads and when modal opens
setupStarRating();
document.getElementById('reviewModal')?.addEventListener('shown.bs.modal', function() {
    setupStarRating();
});

// Submit Review - Setup listener
function setupReviewForm() {
    const form = document.getElementById('reviewForm');
    if (!form) return;
    
    form.removeEventListener('submit', submitReview);
    form.addEventListener('submit', submitReview);
}

function submitReview(e) {
    e.preventDefault();
    
    const rating = document.getElementById('ratingInput').value;
    const review = document.querySelector('#reviewForm textarea[name="review"]').value;
    
    console.log('Rating value:', rating);
    console.log('Review value:', review);
    
    if (!rating || parseInt(rating) == 0) {
        alert('Pilih rating terlebih dahulu!');
        return;
    }
    
    if (!review.trim()) {
        alert('Tulis review terlebih dahulu!');
        return;
    }
    
    const params = new URLSearchParams();
    params.append('action', 'submit_review');
    params.append('id_paket', idPaket);
    params.append('rating', parseInt(rating));
    params.append('review', review);
    
    fetch('review_system.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: params.toString()
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Review berhasil dikirim!');
            const modal = bootstrap.Modal.getInstance(document.getElementById('reviewModal'));
            modal.hide();
            document.getElementById('reviewForm').reset();
            document.getElementById('ratingInput').value = '0';
            document.querySelectorAll('#starRating i').forEach(s => {
                s.classList.remove('active');
                s.style.color = '#ddd';
            });
            loadReviews();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(err => {
        console.error('Error:', err);
        alert('Terjadi kesalahan saat mengirim review');
    });
}

// Setup form listener when modal is shown
document.getElementById('reviewModal')?.addEventListener('shown.bs.modal', function() {
    setupStarRating();
    setupReviewForm();
});

// Initial setup on page load
setupReviewForm();
    fetch('review_system.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_reviews&id_paket=' + idPaket
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update rating overview
            document.getElementById('avgRating').textContent = data.avg_rating || '0.0';
            document.getElementById('totalReviews').textContent = data.total_reviews;
            
            // Update stars
            const avgStars = document.getElementById('avgStars');
            avgStars.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                avgStars.innerHTML += `<i class="bi bi-star${i <= Math.round(data.avg_rating) ? '-fill' : ''}"></i>`;
            }
            
            // Display reviews
            const list = document.getElementById('reviewsList');
            if (data.reviews.length == 0) {
                list.innerHTML = '<div class="text-center text-muted py-5"><i class="bi bi-chat-dots fs-1"></i><p class="mt-3">Belum ada review. Jadilah yang pertama!</p></div>';
            } else {
                list.innerHTML = data.reviews.map(r => `
                    <div class="review-card">
                        <div class="d-flex gap-3">
                            <div class="avatar-circle">${r.nama_lengkap?.charAt(0) || 'U'}</div>
                            <div class="flex-fill">
                                <h6 class="fw-bold mb-1">${r.nama_lengkap || 'Anonymous'}</h6>
                                <div class="stars mb-2">
                                    ${'<i class="bi bi-star-fill"></i>'.repeat(r.rating)}
                                    ${'<i class="bi bi-star"></i>'.repeat(5 - r.rating)}
                                </div>
                                <p class="mb-1">${r.review_text}</p>
                                <small class="text-muted"><i class="bi bi-clock"></i> ${new Date(r.created_at).toLocaleDateString('id-ID')}</small>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }
    });
    
    // Load stats
    fetch('review_system.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_stats&id_paket=' + idPaket
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const total = Object.values(data.stats).reduce((a, b) => a + b, 0);
            const statsHtml = Object.keys(data.stats).sort((a, b) => b - a).map(star => {
                const count = data.stats[star];
                const percentage = total > 0 ? (count / total * 100) : 0;
                return `
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <small class="text-nowrap">${star} <i class="bi bi-star-fill text-warning"></i></small>
                        <div class="rating-bar flex-fill">
                            <div class="rating-bar-fill" style="width: ${percentage}%"></div>
                        </div>
                        <small class="text-muted">${count}</small>
                    </div>
                `;
            }).join('');
            document.getElementById('ratingStats').innerHTML = statsHtml;
        }
    });

// Initial load
loadReviews();
</script>
