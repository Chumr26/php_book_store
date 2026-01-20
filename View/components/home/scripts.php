<script>
    $(document).ready(function() {
        // Counter Animation for Redesigned Stats
        function animateCounter() {
            $('.stat-number-modern').each(function() {
                const $this = $(this);
                const target = parseInt($this.data('target'));

                $({
                    counter: 0
                }).animate({
                    counter: target
                }, {
                    duration: 2000,
                    easing: 'swing',
                    step: function() {
                        $this.text(Math.ceil(this.counter).toLocaleString());
                    },
                    complete: function() {
                        $this.text(target.toLocaleString());
                    }
                });
            });
        }

        // Trigger counter animation when section is visible
        const statsSection = $('.statistics-section-redesign');
        if (statsSection.length) {
            const observer = new IntersectionObserver(function(entries) {
                if (entries[0].isIntersecting) {
                    animateCounter();
                    observer.disconnect();
                }
            }, {
                threshold: 0.5
            });

            observer.observe(statsSection[0]);
        }

        // Newsletter Form Submission
        $('#newsletterForm').on('submit', function(e) {
            e.preventDefault();
            const email = $('#newsletterEmail').val();

            // Simple validation
            if (!email || !email.includes('@')) {
                if (typeof window.showMessageModal === 'function') {
                    window.showMessageModal('Thông báo', 'Vui lòng nhập email hợp lệ!');
                }
                return;
            }

            // TODO: Add AJAX call to save newsletter subscription
            if (typeof window.showMessageModal === 'function') {
                window.showMessageModal('Thông báo', 'Cảm ơn bạn đã đăng ký nhận tin! Chúng tôi sẽ gửi thông tin mới nhất đến email của bạn.');
            }
            $('#newsletterEmail').val('');
        });

        // Auto-play testimonials carousel
        $('#testimonialsCarousel').carousel({
            interval: 5000,
            ride: 'carousel'
        });

        // Fetch author images from Open Library
        const authorImages = document.querySelectorAll('.author-img');

        authorImages.forEach(img => {
            const authorName = img.getAttribute('data-author-name');
            if (authorName) {
                // Search for author to get OLID
                fetch(`https://openlibrary.org/search/authors.json?q=${encodeURIComponent(authorName)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.numFound > 0 && data.docs && data.docs.length > 0) {
                            // Sort by work_count descending to get the most popular author profile
                            data.docs.sort((a, b) => (b.work_count || 0) - (a.work_count || 0));

                            // Get the most relevant result (first one after sorting)
                            const authorDoc = data.docs[0];
                            const olid = authorDoc.key;

                            // Check if image exists by trying to load it
                            // Size L for large quality
                            const imageUrl = `https://covers.openlibrary.org/a/olid/${olid}-L.jpg`;
                            img.src = imageUrl;
                        }
                    })
                    .catch(err => {
                        console.log('Error fetching author image:', err);
                        // Default icon will stay visible due to onerror handler
                    });
            }
        });
    });
</script>