<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Product Shop'; ?></title>
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
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-teal-500 text-white">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 00-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 00-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
        </span>
        <div>
          <div class="text-base font-bold text-slate-800">SPA Management</div>
          <div class="text-xs text-slate-500">Shop & Products</div>
        </div>
      </div>
      <div class="flex gap-2">
        <a href="<?= site_url(''); ?>" class="hidden sm:inline-flex items-center rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
          Home
        </a>
        <a href="<?= site_url('booking/form'); ?>" class="hidden sm:inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-600">
          Book Spa
        </a>
      </div>
    </div>
  </header>

  <!-- Hero -->
  <section class="relative">
    <!-- Background image + overlay -->
    <div class="absolute inset-0 -z-10">
      <img
        src="<?= base_url('assets/img/01.jpg'); ?>"
        alt="Shop hero"
        class="w-full h-full object-cover"
      >
      <div class="absolute inset-0 bg-black/40"></div>
    </div>

    <!-- Content -->
    <div class="mx-auto max-w-6xl px-4 py-24 md:py-32">
      <div class="max-w-xl text-white">
        <h1 class="text-3xl md:text-5xl font-bold">Produk & Barang Terbaik</h1>
        <p class="mt-4 text-white/90">
          Temukan produk berkualitas tinggi untuk kebutuhan spa dan relaksasi Anda. Klik gambar untuk hubungi kami via WhatsApp.
        </p>
      </div>
    </div>
  </section>

  <!-- Content -->
  <main class="mx-auto max-w-6xl px-4 pb-24">
    <!-- Products Section -->
    <section class="mt-8 md:mt-12">
      <div class="mb-4 md:mb-6">
        <h2 class="text-xl md:text-2xl font-bold text-slate-800">Katalog Produk</h2>
        <p class="text-slate-600 mt-1">Produk berkualitas untuk kebutuhan spa dan kesehatan Anda</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <?php if (!empty($products)): ?>
          <?php foreach ($products as $product): ?>
            <div class="group rounded-2xl bg-white overflow-hidden shadow-sm ring-1 ring-slate-200 hover:shadow-md transition">
              <div class="aspect-square overflow-hidden">
                <?php if (!empty($product['image_url'])): ?>
                  <a href="<?= site_url('product_shop/detail/' . $product['id']); ?>">
                    <img
                      src="<?= base_url($product['image_url']); ?>"
                      alt="<?= htmlspecialchars($product['name']); ?>"
                      class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                    >
                  </a>
                <?php else: ?>
                  <a href="<?= site_url('product_shop/detail/' . $product['id']); ?>" class="w-full h-full bg-slate-200 flex items-center justify-center">
                    <span class="text-slate-500">No Image</span>
                  </a>
                <?php endif; ?>
              </div>

              <div class="p-4">
                <h3 class="font-semibold text-slate-800 mb-1 line-clamp-1"><?= htmlspecialchars($product['name']); ?></h3>
                <?php if (!empty($product['description'])): ?>
                  <p class="text-sm text-slate-600 line-clamp-2 mb-3"><?= htmlspecialchars($product['description']); ?></p>
                <?php endif; ?>
                <div class="flex items-center justify-between">
                  <div class="text-primary font-bold">
                    <?= htmlspecialchars($product['currency']); ?> <?= number_format($product['price'], 2, ',', '.'); ?>
                  </div>
                  <button
                    onclick="openWhatsApp('<?= htmlspecialchars($product['name']); ?>', '<?= number_format($product['price'], 2); ?>', '<?= htmlspecialchars($product['currency']); ?>')"
                    class="inline-flex items-center rounded-lg bg-green-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-green-700"
                  >
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                      <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
                    </svg>
                    WhatsApp
                  </button>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-span-full rounded-xl border border-dashed border-slate-300 bg-white p-6 text-center text-slate-600">
            Produk belum tersedia. Sila kembali lagi nanti.
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="mt-10 md:mt-14">
      <h2 class="text-xl md:text-2xl font-bold text-slate-800 mb-4 md:mb-6">Hubungi Kami</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
        <!-- Contact Info -->
        <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-slate-200">
          <div class="text-slate-700">
            <div class="font-semibold text-slate-800">Alamat</div>
            <p class="text-sm text-slate-600 mt-1">21-1, Jalan Abadi 2/1</p>
            <p class="text-sm text-slate-600">Abadi Heights, Puchong, Selangor 71420</p>
            <p class="text-sm text-slate-600">Malaysia</p>

            <div class="mt-4 font-semibold text-slate-800">Kontak</div>
            <p class="text-sm text-slate-600 mt-1">
              Tel:
              <a href="tel:+60380619349" class="text-blue-600 hover:underline">+603 8061 9349</a> /
              <a href="tel:+601123332894" class="text-blue-600 hover:underline">+60 11 2333 2894</a>
            </p>
            <p class="text-sm text-slate-600">
              Email:
              <a href="mailto:apittmenspa@outlook.com" class="text-blue-600 hover:underline">
                apittmenspa@outlook.com
              </a>
            </p>
          </div>
        </div>

        <!-- WhatsApp Contact -->
        <div class="rounded-2xl bg-green-50 p-5 shadow-sm ring-1 ring-green-200">
          <div class="text-center">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-green-600 text-white mb-3">
              <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
              </svg>
            </div>
            <h3 class="font-semibold text-slate-800 mb-2">Pesan via WhatsApp</h3>
            <p class="text-sm text-slate-600 mb-4">Untuk informasi produk dan pemesanan</p>
            <a
              href="https://wa.me/601123332894?text=Hai,%20saya%20tertarik%20dengan%20produk%20Anda"
              target="_blank"
              class="inline-flex items-center rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700"
            >
              <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
              </svg>
              Hubungi WhatsApp
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- Auto-show modal when page loads -->
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

    // WhatsApp function
    function openWhatsApp(productName, price, currency) {
        const message = `Hai, saya tertarik dengan produk: ${productName} - ${currency} ${price}. Bisa minta informasi lebih detail?`;
        const encodedMessage = encodeURIComponent(message);
        const whatsappUrl = `https://wa.me/601123332894?text=${encodedMessage}`;
        window.open(whatsappUrl, '_blank');
    }
  </script>
</body>
</html>