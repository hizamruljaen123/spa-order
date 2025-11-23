<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Spa Home'; ?></title>
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
<body class="bg-gray-900 text-gray-100">

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
  <header class="sticky top-0 z-30 bg-gray-800/90 backdrop-blur border-b border-gray-600">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between text-gray-100">
      <div class="flex items-center gap-2">
        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-teal-500 text-white">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 00-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 00-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
        </span>
        <div>
          <div class="text-base font-bold text-gray-100">APITT Man Spa</div>
        </div>
      </div>
      <a href="<?= site_url('booking/form'); ?>" class="hidden sm:inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-600">
        Book Now
      </a>
    </div>
  </header>

  <!-- Hero -->
 <section class="relative">
  <!-- Background image + overlay -->
  <div class="absolute inset-0 -z-10">
    <img
      src="<?= base_url('assets/img/01.jpg'); ?>"
      alt="Spa hero"
      class="w-full h-full object-cover"
    >
    <div class="absolute inset-0"></div>
  </div>

  <!-- Content -->
  <div class="mx-auto max-w-6xl px-4 py-24 md:py-32">
    <div class="max-w-xl text-white">
      <div class="mt-8">
      </div>
    </div>
  </div>
</section>

  <!-- Content -->
  <main class="mx-auto max-w-6xl px-4 pb-12 -mt-6 md:-mt-8">
    <!-- Quick info cards -->

    <!-- Add On Section -->
    <?php if (!empty($addons_grouped)): ?>
    <section class="mt-6 md:mt-8">
      <div class="mb-4 md:mb-6 flex items-center justify-between">
        <h2 class="text-xl md:text-2xl font-bold text-slate-100">Add On</h2>
        <a href="<?= site_url('booking/form'); ?>" class="hidden sm:inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-600">
          Book Now
        </a>
      </div>

      <?php foreach ($addons_grouped as $category => $addons): ?>
        <div class="mb-8">
          <h3 class="text-lg md:text-xl font-semibold text-slate-200 mb-4">
            <?= htmlspecialchars($category); ?>
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
            <?php foreach ($addons as $addon): ?>
              <div class="group rounded-2xl bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-700 hover:shadow-md transition">
                <div class="p-4">
                  <div class="mb-3">
                    <h4 class="font-semibold text-gray-100 mb-1 line-clamp-2">
                      <?= htmlspecialchars($addon->name); ?>
                    </h4>
                    <?php if (!empty($addon->description)): ?>
                      <p class="text-sm text-gray-300 line-clamp-2">
                        <?= htmlspecialchars($addon->description); ?>
                      </p>
                    <?php endif; ?>
                  </div>
                  <div class="flex items-center justify-between">
                    <div class="text-primary font-bold">
                      <?= htmlspecialchars($addon->currency); ?> <?= number_format($addon->price, 2, ',', '.'); ?>
                    </div>
                    <a href="<?= site_url('booking/form'); ?>" class="inline-flex items-center rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-sky-600">
                      Book
                    </a>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <!-- Exclusive Treatments Section -->
    <?php if (!empty($exclusive_treatments_grouped)): ?>
    <section class="mt-6 md:mt-8">
      <div class="mb-4 md:mb-6 flex items-center justify-between">
        <h2 class="text-xl md:text-2xl font-bold text-slate-100">EXCLUSIVE TREATMENT</h2>
      </div>

      <?php 
      // Reorganize treatments in specific order
      $treatment_order = ['Solo Massage', '4Hand Massage', '6Hand Massage', 'Reflexology', 'Bekam'];
      
      // Get all treatments from all categories
      $all_treatments = [];
      foreach ($exclusive_treatments_grouped as $category => $treatments) {
          foreach ($treatments as $treatment) {
              $all_treatments[$treatment['name']] = $treatment;
          }
      }

      // Display treatments in specific order
      foreach ($treatment_order as $treatment_name) {
          if (isset($all_treatments[$treatment_name])) {
              $treatment = $all_treatments[$treatment_name];
              ?>
              <div class="mb-6">
                <h3 class="text-lg md:text-xl font-semibold text-slate-200 mb-4">
                  <?= htmlspecialchars($treatment_name); ?>
                </h3>
                <div class="max-w-md">
                  <div class="group rounded-2xl bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-700 hover:shadow-md transition">
                    <div class="p-4">
                      <div class="mb-3">
                        <h4 class="font-semibold text-gray-100 mb-1 line-clamp-2">
                          <?= htmlspecialchars($treatment['name']); ?>
                        </h4>
                        <?php if (!empty($treatment['description'])): ?>
                          <p class="text-sm text-gray-300 line-clamp-2">
                            <?= htmlspecialchars($treatment['description']); ?>
                          </p>
                        <?php endif; ?>
                      </div>
                      <div class="flex items-center justify-between">
                        <div class="text-primary font-bold">
                          <?= htmlspecialchars($treatment['currency']); ?> <?= number_format($treatment['price'], 2, ',', '.'); ?>
                        </div>
                        <a href="<?= site_url('booking/form'); ?>" class="inline-flex items-center rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-sky-600">
                          Book
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
          }
      }

      // Display any remaining treatments not in the specific order
      foreach ($all_treatments as $treatment_name => $treatment) {
          if (!in_array($treatment_name, $treatment_order)) {
              ?>
              <div class="mb-6">
                <h3 class="text-lg md:text-xl font-semibold text-slate-200 mb-4">
                  <?= htmlspecialchars($treatment['category']); ?>
                </h3>
                <div class="max-w-md">
                  <div class="group rounded-2xl bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-700 hover:shadow-md transition">
                    <div class="p-4">
                      <div class="mb-3">
                        <h4 class="font-semibold text-gray-100 mb-1 line-clamp-2">
                          <?= htmlspecialchars($treatment['name']); ?>
                        </h4>
                        <?php if (!empty($treatment['description'])): ?>
                          <p class="text-sm text-gray-300 line-clamp-2">
                            <?= htmlspecialchars($treatment['description']); ?>
                          </p>
                        <?php endif; ?>
                      </div>
                      <div class="flex items-center justify-between">
                        <div class="text-primary font-bold">
                          <?= htmlspecialchars($treatment['currency']); ?> <?= number_format($treatment['price'], 2, ',', '.'); ?>
                        </div>
                        <a href="<?= site_url('booking/form'); ?>" class="inline-flex items-center rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-sky-600">
                          Book
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <?php
          }
      }
      ?>
    </section>
    <?php endif; ?>

    <!-- Products Section -->
    <section class="mt-6 md:mt-8">
      <div class="mb-4 md:mb-6 flex items-center justify-between">
        <h2 class="text-xl md:text-2xl font-bold text-slate-100">Products & Items</h2>
        <div class="flex gap-2">
          <a href="<?= site_url('products'); ?>" class="hidden sm:inline-flex items-center rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-200 shadow-sm hover:bg-gray-600">
            View All Products
          </a>
          <a href="<?= site_url('booking/form'); ?>" class="hidden sm:inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-600">
            Book Spa
          </a>
        </div>
      </div>

      <div class="space-y-8">
        <!-- First Row: Products 1-4 (2x2 grid) -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
          <?php
          // Products are loaded in controller and passed to view
          $active_products = isset($active_products) ? $active_products : [];
          // Show first 4 products in first row (2x2 grid)
          $first_row_products = array_slice($active_products, 0, 4);
          ?>

          <?php if (!empty($first_row_products)): ?>
            <?php foreach ($first_row_products as $product): ?>
              <div class="group rounded-2xl bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-700 hover:shadow-md transition">
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
                    <a href="<?= site_url('product_shop/detail/' . $product['id']); ?>" class="w-full h-full bg-gray-700 flex items-center justify-center">
                      <span class="text-gray-400">No Image</span>
                    </a>
                  <?php endif; ?>
                </div>

                <div class="p-4">
                  <h3 class="font-semibold text-gray-100 mb-1 line-clamp-1"><?= htmlspecialchars($product['name']); ?></h3>
                  <?php if (!empty($product['description'])): ?>
                    <p class="text-sm text-gray-300 line-clamp-2 mb-3"><?= htmlspecialchars($product['description']); ?></p>
                  <?php endif; ?>
                  <div class="text-center">
                    <div class="text-primary font-bold mb-2">
                      <?= htmlspecialchars($product['currency']); ?> <?= number_format($product['price'], 2, ',', '.'); ?>
                    </div>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>

        <!-- Second Row: Products 5-8 (2x2 grid) -->
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
          <?php
          // Show next 4 products in second row (2x2 grid)
          $second_row_products = array_slice($active_products, 4, 4);
          ?>

          <?php if (!empty($second_row_products)): ?>
            <?php foreach ($second_row_products as $product): ?>
            <div class="group rounded-2xl bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-700 hover:shadow-md transition">
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
                  <a href="<?= site_url('product_shop/detail/' . $product['id']); ?>" class="w-full h-full bg-gray-700 flex items-center justify-center">
                    <span class="text-gray-400">No Image</span>
                  </a>
                <?php endif; ?>
              </div>

              <div class="p-4">
                <h3 class="font-semibold text-gray-100 mb-1 line-clamp-1"><?= htmlspecialchars($product['name']); ?></h3>
                <?php if (!empty($product['description'])): ?>
                  <p class="text-sm text-gray-300 line-clamp-2 mb-3"><?= htmlspecialchars($product['description']); ?></p>
                <?php endif; ?>
                <div class="text-center">
                  <div class="text-primary font-bold mb-2">
                    <?= htmlspecialchars($product['currency']); ?> <?= number_format($product['price'], 2, ',', '.'); ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>

        <?php if (count($active_products) > 8): ?>
          <div class="mt-8 text-center">
            <a href="<?= site_url('products'); ?>" class="inline-flex items-center rounded-lg bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-200 hover:bg-gray-600">
              View All Products (<?= count($active_products); ?>)
              <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
              </svg>
            </a>
          </div>
        <?php endif; ?>
    </section>

    <!-- Packages / Services -->
    <section id="packages" class="mt-8 md:mt-12">
      <div class="mb-4 md:mb-6 flex items-center justify-between">
        <h2 class="text-xl md:text-2xl font-bold text-slate-100">Packages & Services</h2>
        <a href="<?= site_url('booking/form'); ?>" class="hidden sm:inline-flex items-center rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm font-semibold text-gray-200 shadow-sm hover:bg-gray-600">
          Create Booking
        </a>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6">
        <?php if (!empty($packages)): ?>
          <?php foreach ($packages as $p): ?>
            <div class="group rounded-2xl bg-gray-800 overflow-hidden shadow-sm ring-1 ring-gray-700 hover:shadow-md transition">

              <div class="p-4">
                <?php if (!empty($p->description)): ?>
                  <p class="text-sm text-gray-300 line-clamp-2 mb-2"><?= htmlspecialchars($p->description); ?></p>
                <?php else: ?>
                  <p class="text-sm text-gray-300 mb-2">Spa package for body and mind relaxation.</p>
                <?php endif; ?>
                <div class="mb-2 text-xs text-gray-400">
                  <span class="inline-flex items-center gap-2">
                    <span class="rounded-full bg-gray-700 px-2 py-0.5">Category: <?= htmlspecialchars($p->category ?? '-'); ?></span>
                    <span class="rounded-full bg-gray-700 px-2 py-0.5">Therapist: <?= isset($p->hands) ? (int)$p->hands : 1; ?></span>
                  </span>
                </div>
                <div class="flex items-center justify-between">
                  <div class="text-primary font-bold">
                    <?php
                      $curr = isset($p->currency) ? $p->currency : 'RM';
                      $pin  = isset($p->price_in_call) ? (float)$p->price_in_call : null;
                      $pout = isset($p->price_out_call) ? (float)$p->price_out_call : null;
                    ?>
                    <?php if ($pin !== null): ?>
                      <span>At Premise: <?= htmlspecialchars($curr); ?> <?= number_format($pin, 0, ',', '.'); ?></span>
                    <?php else: ?>
                      Contact us
                    <?php endif; ?>
                  </div>
                  <a href="<?= site_url('booking/form') . '?package_id=' . (int)$p->id; ?>" class="inline-flex items-center rounded-lg bg-primary px-3 py-1.5 text-sm font-semibold text-white hover:bg-sky-600">
                    Book
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-span-full rounded-xl border border-dashed border-gray-600 bg-gray-800 p-6 text-center text-gray-400">
            Packages not available yet. Please check back later.
          </div>
        <?php endif; ?>
      </div>
    </section>

    <!-- Price list removed per instruction -->

    <!-- Address & Contact -->
    <section id="contact" class="mt-10 md:mt-14">
  <h2 class="text-xl md:text-2xl font-bold text-slate-100 mb-4 md:mb-6">Address & Contact</h2>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6">
    <!-- Contact Info -->
    <div class="rounded-2xl bg-gray-800 p-5 shadow-sm ring-1 ring-gray-700">
      <div class="text-gray-300">
        <div class="font-semibold text-gray-100">Address</div>
        <p class="text-sm text-gray-300 mt-1">21-1, Jalan Abadi 2/1</p>
        <p class="text-sm text-gray-300">Abadi Heights, Puchong, Selangor 71420</p>
        <p class="text-sm text-gray-300">Malaysia</p>

        <div class="mt-4 font-semibold text-gray-100">Contact</div>
        <p class="text-sm text-gray-300 mt-1">
          Tel:
          <a href="tel:+60380619349" class="text-blue-400 hover:underline">+603 8061 9349</a> /
          <a href="tel:+601123332894" class="text-blue-400 hover:underline">+60 11 2333 2894</a>
        </p>
        <p class="text-sm text-gray-300">
          Email:
          <a href="mailto:apittmenspa@outlook.com" class="text-blue-400 hover:underline">
            apittmenspa@outlook.com
          </a>
        </p>
      </div>

      <a
        href="<?= site_url('booking/form'); ?>"
        class="mt-4 inline-flex items-center rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-teal-600"
      >
        Create Booking
      </a>
    </div>

    <!-- Image -->
       
  </div>
</section>

  </main>

  <!-- Bottom sticky CTA (mobile-first) -->
  <div class="fixed inset-x-0 bottom-0 z-30 border-t border-gray-600 bg-gray-800/95 backdrop-blur">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between">
      <div class="text-sm">
        <div class="font-semibold text-gray-100">Ready to relax?</div>
        <div class="text-gray-300">Book your schedule now</div>
      </div>
      <a href="<?= site_url('booking/form'); ?>" class="inline-flex items-center rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-sky-600">
        Book Now
      </a>
    </div>
  </div>
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

  // Product grid animation
  document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('section:first-of-type .group');
    productCards.forEach((card, index) => {
      card.style.opacity = '0';
      card.style.transform = 'translateY(20px)';
      setTimeout(() => {
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        card.style.opacity = '1';
        card.style.transform = 'translateY(0)';
      }, index * 100);
    });
  });
</script>
</body>
</html>