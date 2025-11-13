<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Product Detail'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { DEFAULT: '#0ea5e9' }, // sky-500
            brand: { DEFAULT: '#14b8a6' } // teal-500
          }
        }
      }
    }
  </script>

  <!-- Modern font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    html, body { font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, 'Helvetica Neue', sans-serif; }

    /* Modal Styles */
    .modal {
      display: none;
      position: fixed;
      z-index: 9999;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.5);
    }

    .modal-content {
      background-color: transparent;
      margin: 15% auto;
      padding: 20px;
      border: none;
      width: 80%;
      max-width: 600px;
      text-align: center;
      position: relative;
    }

    .modal-content img {
      max-width: 100%;
      max-height: 400px;
      border-radius: 8px;
      cursor: pointer;
      transition: transform 0.3s ease;
    }

    .modal-content img:hover {
      transform: scale(1.05);
    }

    .close {
      position: absolute;
      top: -10px;
      right: -10px;
      color: #fff;
      font-size: 28px;
      font-weight: bold;
      background: rgba(0,0,0,0.7);
      border-radius: 50%;
      width: 40px;
      height: 40px;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      z-index: 10001;
    }

    .close:hover,
    .close:focus {
      background: rgba(0,0,0,0.9);
    }

    /* Slider styles for multiple ads */
    .slider {
      position: relative;
      overflow: hidden;
    }

    .slider-track {
      display: flex;
      transition: transform 0.5s ease;
    }

    .slide {
      flex: 0 0 100%;
      width: 100%;
    }

    .slider-dots {
      position: absolute;
      bottom: 10px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      gap: 8px;
    }

    .dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: rgba(255,255,255,0.5);
      cursor: pointer;
      transition: background 0.3s;
    }

    .dot.active {
      background: rgba(255,255,255,1);
    }

    /* Product detail styles */
    .product-image {
      transition: transform 0.3s ease;
    }

    .product-image:hover {
      transform: scale(1.05);
    }
  </style>
</head>
<body class="bg-slate-50">

<!-- Advertisement Modal -->
<?php if (!empty($active_ads)): ?>
<div id="adModal" class="modal">
  <div class="modal-content">
    <span class="close" onclick="closeModal()">&times;</span>
    <div class="slider">
      <div class="slider-track" id="sliderTrack">
        <?php foreach ($active_ads as $ad): ?>
          <div class="slide">
            <a href="<?php echo $ad['link_url']; ?>" target="_blank">
              <img src="<?php echo base_url($ad['image_url']); ?>" alt="<?php echo htmlspecialchars($ad['title']); ?>">
            </a>
          </div>
        <?php endforeach; ?>
      </div>
      <?php if (count($active_ads) > 1): ?>
        <div class="slider-dots" id="sliderDots">
          <?php for ($i = 0; $i < count($active_ads); $i++): ?>
            <div class="dot <?php echo $i === 0 ? 'active' : ''; ?>" onclick="goToSlide(<?php echo $i; ?>)"></div>
          <?php endfor; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endif; ?>

  <!-- App-like top bar -->
  <header class="sticky top-0 z-30 bg-white/90 backdrop-blur border-b border-slate-200">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <a href="<?= site_url(); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-teal-500 text-white hover:bg-teal-600">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M10.707 4.293a1 1 0 010 1.414L6.414 10H19a1 1 0 110 2H6.414l4.293 4.293a1 1 0 01-1.414 1.414l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 0z"/></svg>
        </a>
        <div>
          <div class="text-base font-bold text-slate-800">Detail Produk</div>
          <div class="text-xs text-slate-500">SPA Management</div>
        </div>
      </div>
      <a href="<?= site_url('booking/form'); ?>" class="hidden sm:inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-600">
        Tempah Sekarang
      </a>
    </div>
  </header>

  <!-- Main Content -->
  <main class="mx-auto max-w-6xl px-4 py-8">
    <?php if (!empty($product)): ?>
      <!-- Product Detail Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
        <!-- Product Image -->
        <div class="bg-white rounded-2xl overflow-hidden shadow-sm ring-1 ring-slate-200">
          <div class="aspect-square overflow-hidden">
            <?php if (!empty($product['image_url'])): ?>
              <img
                src="<?= base_url($product['image_url']); ?>"
                alt="<?= htmlspecialchars($product['name']); ?>"
                class="w-full h-full object-cover product-image cursor-pointer"
                onclick="openWhatsApp('<?= htmlspecialchars($product['name']); ?>', '<?= number_format($product['price'], 2); ?>', '<?= htmlspecialchars($product['currency']); ?>')"
              >
            <?php else: ?>
              <div class="w-full h-full bg-slate-200 flex items-center justify-center cursor-pointer" onclick="openWhatsApp('<?= htmlspecialchars($product['name']); ?>', '<?= number_format($product['price'], 2); ?>', '<?= htmlspecialchars($product['currency']); ?>')">
                <span class="text-slate-500 text-lg">No Image</span>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <!-- Product Info -->
        <div class="bg-white rounded-2xl p-6 shadow-sm ring-1 ring-slate-200">
          <div class="mb-4">
            <h1 class="text-2xl md:text-3xl font-bold text-slate-800 mb-2"><?= htmlspecialchars($product['name']); ?></h1>
            <?php if (!empty($product['category'])): ?>
              <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-slate-100 text-slate-700">
                <?= htmlspecialchars($product['category']); ?>
              </span>
            <?php endif; ?>
          </div>

          <div class="mb-6">
            <div class="text-3xl font-bold text-primary mb-2">
              <?= htmlspecialchars($product['currency']); ?> <?= number_format($product['price'], 2, ',', '.'); ?>
            </div>
            <?php if (!empty($product['description'])): ?>
              <div class="text-slate-600 leading-relaxed">
                <?= nl2br(htmlspecialchars($product['description'])); ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Action Buttons -->
          <div class="flex flex-col sm:flex-row gap-3">
            <button
              onclick="openWhatsApp('<?= htmlspecialchars($product['name']); ?>', '<?= number_format($product['price'], 2); ?>', '<?= htmlspecialchars($product['currency']); ?>')"
              class="flex-1 inline-flex items-center justify-center rounded-lg bg-green-600 px-6 py-3 text-sm font-semibold text-white hover:bg-green-700 transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
              </svg>
              WhatsApp Inquiry
            </button>
            <a
              href="<?= site_url('booking/form'); ?>"
              class="flex-1 inline-flex items-center justify-center rounded-lg bg-primary px-6 py-3 text-sm font-semibold text-white hover:bg-sky-600 transition-colors"
            >
              <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
              Book Appointment
            </a>
          </div>
        </div>
      </div>

      <!-- Related Products Section -->
      <section class="mb-12">
        <div class="text-center mb-8">
          <h2 class="text-2xl font-bold text-slate-800 mb-2">Produk Lain Yang Menarik</h2>
          <p class="text-slate-600">Terokai produk lain yang mungkin anda minati</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
          <!-- This would be populated with related products in a real implementation -->
          <!-- For now, just show a placeholder -->
          <div class="bg-white rounded-xl p-4 shadow-sm ring-1 ring-slate-200 text-center">
            <div class="w-full h-32 bg-slate-200 rounded-lg mb-3 flex items-center justify-center">
              <span class="text-slate-500">Coming Soon</span>
            </div>
            <h3 class="font-semibold text-slate-800 mb-1">Produk Lain</h3>
            <p class="text-sm text-slate-600">Akan datang...</p>
          </div>
        </div>
      </section>

    <?php else: ?>
      <!-- Product Not Found -->
      <div class="text-center py-12">
        <div class="w-24 h-24 mx-auto mb-4 bg-slate-200 rounded-full flex items-center justify-center">
          <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.29-1.01-5.625-2.594C6.738 10.832 9.246 10 12 10s5.262.832 6.625 2.406z"/>
          </svg>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 mb-2">Produk Tidak Ditemui</h2>
        <p class="text-slate-600 mb-6">Maaf, produk yang anda cari tidak tersedia atau telah dipindahkan.</p>
        <a href="<?= site_url('products'); ?>" class="inline-flex items-center rounded-lg bg-primary px-6 py-3 text-white font-semibold hover:bg-sky-600">
          Kembali ke Produk
        </a>
      </div>
    <?php endif; ?>

    <!-- Back to Products CTA -->
    <div class="text-center">
      <a href="<?= site_url('products'); ?>" class="inline-flex items-center rounded-lg bg-slate-100 px-6 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-200">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        Lihat Semua Produk
      </a>
    </div>
  </main>

  <!-- Bottom sticky CTA (mobile-first) -->
  <div class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 backdrop-blur">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center">
      <div class="flex-1 text-sm">
        <div class="font-semibold text-slate-800">Ada soalan? Hubungi kami</div>
        <div class="text-slate-500">Kami sedia membantu</div>
      </div>
      <a href="tel:+60380619349" class="inline-flex items-center rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-600">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
        </svg>
        Hubungi
      </a>
    </div>
  </div>

  <!-- WhatsApp Script -->
  <script>
    function openWhatsApp(productName, price, currency) {
      const message = encodeURIComponent(
        `Hi! Saya berminat dengan produk: ${productName}\n\n` +
        `Harga: ${currency} ${price}\n\n` +
        `Boleh saya dapat maklumat lanjut?`
      );
      const phoneNumber = '+60123456789'; // Replace with actual WhatsApp number
      window.open(`https://wa.me/${phoneNumber}?text=${message}`, '_blank');
    }
  </script>

  <!-- Modal Scripts -->
  <script>
    // Get the modal
    var modal = document.getElementById("adModal");

    // Auto-show modal when page loads
    window.addEventListener('load', function() {
        if (modal) {
            modal.style.display = "block";
        }
    });

    // Close modal function
    function closeModal() {
        if (modal) {
            modal.style.display = "none";
        }
    }

    // Slider functionality
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const totalSlides = slides.length;

    function updateSlider() {
        const sliderTrack = document.getElementById('sliderTrack');
        if (sliderTrack) {
            sliderTrack.style.transform = `translateX(-${currentSlide * 100}%)`;
        }

        // Update dots
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    }

    function goToSlide(slideIndex) {
        currentSlide = slideIndex;
        updateSlider();
    }

    // Auto slide with configurable interval if multiple slides exist
    if (totalSlides > 1) {
        const interval = <?= isset($slider_interval) ? $slider_interval : 2000; ?>;
        setInterval(() => {
            currentSlide = (currentSlide + 1) % totalSlides;
            updateSlider();
        }, interval);
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
  </script>
</body>
</html>