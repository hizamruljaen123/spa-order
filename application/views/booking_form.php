<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? htmlspecialchars($title) : 'Spa Booking'; ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { DEFAULT: '#0ea5e9' } // sky-500
          }
        }
      };
      // Off Premise Price

  </script>

  <!-- Modern, clean font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

  <style>
    html, body {
      font-family: 'Inter', system-ui, -apple-system, Segoe UI, Roboto, Arial, 'Helvetica Neue', sans-serif;
    }
    .required:after { content: ' *'; color: #ef4444; }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-teal-50 via-white to-sky-50">
  <!-- Soft decorative blobs -->
  <div class="relative overflow-hidden">
    <div class="pointer-events-none absolute -top-24 -left-24 h-72 w-72 rounded-full bg-teal-200 opacity-40 blur-2xl"></div>
    <div class="pointer-events-none absolute -bottom-24 -right-24 h-72 w-72 rounded-full bg-sky-200 opacity-40 blur-2xl"></div>
  </div>

  <div class="max-w-5xl mx-auto px-4 py-10 md:py-14">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <div>
        <h1 class="text-3xl md:text-4xl font-bold text-slate-800">Spa Booking</h1>
        <p class="mt-2 text-slate-500">Fill out the form below to make a spa service booking.</p>
      </div>
      <div class="hidden md:block">
        <span class="inline-flex items-center gap-2 rounded-full bg-white/70 px-3 py-2 shadow-sm ring-1 ring-slate-200">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-teal-500" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a7 7 0 00-7 7c0 5 7 13 7 13s7-8 7-13a7 7 0 00-7-7zm0 9.5a2.5 2.5 0 110-5 2.5 2.5 0 010 5z"/></svg>
          <span class="text-sm font-medium text-slate-700">Relax & Refresh</span>
        </span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
      <!-- Visual / Hero -->
      <div class="hidden md:block">
        <div class="relative overflow-hidden rounded-2xl shadow-lg ring-1 ring-slate-200">
          <img src="https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&w=1200&q=60" alt="Spa ambience" class="h-full w-full object-cover">
          <div class="absolute inset-0 bg-gradient-to-t from-black/30 via-black/10 to-transparent"></div>
          <div class="absolute bottom-0 left-0 right-0 p-5 text-white">
            <div class="text-lg font-semibold">Premium Spa Experience</div>
            <p class="text-sm opacity-90">Enjoy the best service with professional therapists.</p>
          </div>
        </div>
      </div>

      <!-- Form card -->
      <div>
        <div class="rounded-2xl bg-white shadow-xl ring-1 ring-slate-200">
          <div class="p-6 md:p-8">
            <?php if (!empty($error)): ?>
              <div class="mb-4 rounded-md bg-red-50 p-4 ring-1 ring-red-200">
                <p class="text-sm text-red-700"><?= htmlspecialchars($error); ?></p>
              </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
              <div class="mb-4 rounded-md bg-green-50 p-4 ring-1 ring-green-200">
                <p class="text-sm text-green-700"><?= htmlspecialchars($success); ?></p>
              </div>

              <!-- Optional payment info -->
              <div class="mb-6 rounded-lg border border-green-200 bg-white p-5">
                <div class="font-semibold text-slate-700 mb-2">Deposit Payment Information (Optional)</div>
                <ul class="text-sm text-slate-600 space-y-1">
                  <li>Bank: BCA</li>
                  <li>Account No: 1234567890</li>
                  <li>Account Name: PT Spa Sejahtera</li>
                </ul>
                <p class="mt-2 text-xs text-slate-500">Payment confirmation can be done via admin WhatsApp.</p>
              </div>
            <?php endif; ?>

            <?php if (!empty($validation)): ?>
              <div class="mb-4 rounded-md bg-yellow-50 p-4 ring-1 ring-yellow-200">
                <?= $validation; ?>
              </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('booking/submit'); ?>" novalidate>
              <div class="mb-4">
                <label class="required block text-sm font-medium text-slate-700" for="customer_name">Name</label>
                <input type="text" class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary" id="customer_name" name="customer_name" required minlength="2" placeholder="Full name">
              </div>

              <div class="mb-4">
                <label class="required block text-sm font-medium text-slate-700" for="address">Address</label>
                <textarea class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary" id="address" name="address" rows="3" required minlength="5" placeholder="Full address"></textarea>
              </div>

              <!-- Optional phone number used for WhatsApp link in Telegram notification -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700" for="phone">Phone Number (optional)</label>
                <input
                  type="tel"
                  class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                  id="phone"
                  name="phone"
                  maxlength="20"
                  placeholder="Example: 6281234567890">
                <p class="mt-1 text-xs text-slate-500">Number will be attached as WhatsApp link in notification.</p>
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Therapist Selection (optional)</label>
                <input type="hidden" id="therapist_id" name="therapist_id" value="">
                <div class="mt-2 flex items-center justify-between gap-3">
                  <button type="button" onclick="window.therapistModalOpen()" class="inline-flex items-center rounded-md bg-teal-600 text-white px-3 py-2 text-sm hover:bg-teal-700 focus:outline-none focus:ring-2 focus:ring-teal-500">
                    Choose Therapist
                  </button>
                  <div id="therapist_summary" class="text-xs text-slate-600 ml-3 flex-1 text-right truncate">
                    No therapist selected.
                  </div>
                </div>
                <p class="mt-1 text-xs text-slate-500">Click to view and select from available therapists.</p>
              </div>

              <!-- Therapist Selection Modal -->
              <div id="therapistModalOverlay" class="fixed inset-0 z-40 bg-black/40 hidden"></div>
              <div id="therapistModal"
                   role="dialog"
                   aria-modal="true"
                   aria-hidden="true"
                   class="fixed inset-0 z-50 hidden items-center justify-center p-4">
                <div class="w-full max-w-4xl bg-white rounded-xl shadow-xl ring-1 ring-gray-200">
                  <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Choose Your Therapist</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="window.therapistModalClose()" aria-label="Close">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </div>

                  <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <?php if (!empty($therapists)): ?>
                      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <?php foreach ($therapists as $t): ?>
                          <div class="therapist-card cursor-pointer border-2 border-transparent rounded-lg p-4 hover:border-teal-300 hover:bg-teal-50 transition-colors"
                               data-therapist-id="<?= (int)$t->id; ?>"
                               data-therapist-name="<?= htmlspecialchars($t->name); ?>"
                               data-therapist-status="<?= htmlspecialchars($t->status); ?>">
                            <div class="text-center">
                              <div class="mb-3">
                                <?php if (!empty($t->photo)): ?>
                                  <img src="<?= base_url($t->photo); ?>"
                                       alt="<?= htmlspecialchars($t->name); ?>"
                                       class="w-24 h-24 mx-auto rounded-full object-cover border-2 border-gray-200">
                                <?php else: ?>
                                  <div class="w-24 h-24 mx-auto rounded-full bg-gray-200 flex items-center justify-center border-2 border-gray-300">
                                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                  </div>
                                <?php endif; ?>
                              </div>
                              <div class="text-sm font-medium text-slate-800"><?= htmlspecialchars($t->name); ?></div>
                              <?php if (!empty($t->status)): ?>
                                <?php
                                  $statusClass = 'bg-gray-100 text-gray-700';
                                  if ($t->status === 'available') $statusClass = 'bg-green-100 text-green-700';
                                  elseif ($t->status === 'busy') $statusClass = 'bg-amber-100 text-amber-700';
                                  elseif ($t->status === 'off') $statusClass = 'bg-gray-100 text-gray-600';
                                ?>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?= $statusClass; ?>">
                                  <?= htmlspecialchars($t->status); ?>
                                </span>
                              <?php endif; ?>
                              <?php if (!empty($t->phone)): ?>
                                <div class="text-xs text-slate-500 mt-1"><?= htmlspecialchars($t->phone); ?></div>
                              <?php endif; ?>
                            </div>
                          </div>
                        <?php endforeach; ?>
                      </div>
                    <?php else: ?>
                      <p class="text-sm text-slate-600 text-center py-8">No therapists currently available.</p>
                    <?php endif; ?>
                  </div>

                  <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                    <button type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50" onclick="window.therapistModalClose()">Cancel</button>
                    <button type="button" class="inline-flex items-center rounded-md bg-teal-600 text-white px-4 py-2 text-sm hover:bg-teal-700" onclick="window.therapistModalClear()">Clear Selection</button>
                  </div>
                </div>
              </div>

              <div class="mb-4">
                <label class="required block text-sm font-medium text-slate-700" for="package_id">Package</label>
                <select class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary" id="package_id" name="package_id" required>
                  <option value="">Select package</option>
                  <?php if (!empty($packages)): ?>
                    <?php foreach ($packages as $p): ?>
                      <option value="<?= (int)$p->id; ?>" <?= (isset($selected_package_id) && (int)$selected_package_id === (int)$p->id) ? 'selected' : ''; ?>>
                        <?php
                          $curr = isset($p->currency) ? $p->currency : 'Rp';
                          $pin  = isset($p->price_in_call) ? (float)$p->price_in_call : (isset($p->price) ? (float)$p->price : 0);
                        ?>
                        <?= htmlspecialchars($p->name); ?>
                        <?php if (isset($p->duration)): ?> - <?= (int)$p->duration; ?> minutes<?php endif; ?>
                        <?php if ($pin): ?> - <?= htmlspecialchars($curr); ?> <?= number_format($pin, 0, ',', '.'); ?><?php endif; ?>
                      </option>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
              </div>
              <!-- Add-on selection (optional) -->
              <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700">Additional Add-on (optional)</label>
                <input type="hidden" id="addon_ids" name="addon_ids" value="">
                <div class="mt-2 flex items-center justify-between gap-3">
                  <button type="button" onclick="window.aoSelOpen()" class="inline-flex items-center rounded-md bg-emerald-600 text-white px-3 py-2 text-sm hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    Select Add-on
                  </button>
                  <div id="addon_summary" class="text-xs text-slate-600 ml-3 flex-1 text-right truncate">
                    No add-on selected.
                  </div>
                </div>
              </div>

              <!-- Add-on Modal -->
              <div id="aoSelOverlay" class="fixed inset-0 z-40 bg-black/40 hidden"></div>
              <div id="aoSelModal"
                   role="dialog"
                   aria-modal="true"
                   aria-hidden="true"
                   class="fixed inset-0 z-50 hidden items-center justify-center p-4">
                <div class="w-full max-w-3xl bg-white rounded-xl shadow-xl ring-1 ring-gray-200">
                  <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Select Add-on</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-600" onclick="window.aoSelClose()" aria-label="Close">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                      </svg>
                    </button>
                  </div>

                  <div class="p-6 max-h-[70vh] overflow-y-auto">
                    <?php if (!empty($addons_grouped)): ?>
                      <?php foreach ($addons_grouped as $cat => $items): ?>
                        <div class="mb-6">
                          <div class="text-sm font-semibold text-slate-800"><?= htmlspecialchars((string)$cat); ?></div>
                          <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 gap-2">
                            <?php foreach ($items as $it): ?>
                              <label class="flex items-start gap-3 rounded-lg border border-slate-200 bg-white px-3 py-2 hover:bg-slate-50">
                                <input type="checkbox"
                                       class="mt-1 h-4 w-4 text-emerald-600 border-slate-300 rounded ao-item focus:ring-emerald-500"
                                       data-id="<?= (int)$it->id; ?>"
                                       data-name="<?= htmlspecialchars((string)($it->name ?? '')); ?>"
                                       data-price="<?= (float)($it->price ?? 0); ?>"
                                       data-currency="<?= htmlspecialchars((string)($it->currency ?? 'RM')); ?>"
                                       />
                                <div class="min-w-0">
                                  <div class="text-sm font-medium text-slate-800">
                                    <?= htmlspecialchars((string)($it->name ?? '-')); ?>
                                    <span class="text-xs text-slate-500">
                                      (<?= htmlspecialchars((string)($it->currency ?? 'RM')); ?>
                                      <?= number_format((float)($it->price ?? 0), 0, ',', '.'); ?>)
                                    </span>
                                  </div>
                                  <?php if (!empty($it->description)): ?>
                                    <div class="text-xs text-slate-500"><?= htmlspecialchars((string)$it->description); ?></div>
                                  <?php endif; ?>
                                </div>
                              </label>
                            <?php endforeach; ?>
                          </div>
                        </div>
                      <?php endforeach; ?>
                    <?php else: ?>
                      <p class="text-sm text-slate-600">No add-ons currently available.</p>
                    <?php endif; ?>
                  </div>

                  <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-end gap-3">
                    <button type="button" class="inline-flex items-center rounded-md border border-gray-300 bg-white text-gray-700 px-4 py-2 text-sm hover:bg-gray-50" onclick="window.aoSelClose()">Cancel</button>
                    <button type="button" class="inline-flex items-center rounded-md bg-emerald-600 text-white px-4 py-2 text-sm hover:bg-emerald-700" onclick="window.aoSelApply()">Save</button>
                  </div>
                </div>
              </div>

              <script>
                (function(){
                  // Add-on modal elements
                  var addonHidden = document.getElementById('addon_ids');
                  var addonOverlay = document.getElementById('aoSelOverlay');
                  var addonModal = document.getElementById('aoSelModal');
                  var addonSummary = document.getElementById('addon_summary');

                  // Therapist modal elements
                  var therapistHidden = document.getElementById('therapist_id');
                  var therapistOverlay = document.getElementById('therapistModalOverlay');
                  var therapistModal = document.getElementById('therapistModal');
                  var therapistSummary = document.getElementById('therapist_summary');

                  // Add-on modal functions
                  function openAddonModal() {
                    if (!addonModal || !addonOverlay) return;
                    addonModal.classList.remove('hidden');
                    addonModal.classList.add('flex');
                    addonOverlay.classList.remove('hidden');
                    addonModal.setAttribute('aria-hidden', 'false');
                  }
                  function closeAddonModal() {
                    if (!addonModal || !addonOverlay) return;
                    addonModal.classList.add('hidden');
                    addonModal.classList.remove('flex');
                    addonOverlay.classList.add('hidden');
                    addonModal.setAttribute('aria-hidden', 'true');
                  }
                  function applyAddonSelection() {
                    if (!addonModal) return;
                    var boxes = addonModal.querySelectorAll('.ao-item:checked');
                    var ids = [];
                    var names = [];
                    var total = 0;
                    var currency = 'RM';
                    boxes.forEach(function(b){
                      var id = parseInt(b.getAttribute('data-id') || '0', 10);
                      if (!isNaN(id) && id > 0) {
                        ids.push(id);
                        names.push(b.getAttribute('data-name') || '');
                        var pr = parseFloat(b.getAttribute('data-price') || '0');
                        if (!isNaN(pr)) total += pr;
                        currency = b.getAttribute('data-currency') || currency;
                      }
                    });
                    if (addonHidden) addonHidden.value = ids.join(',');
                    if (addonSummary) {
                      if (ids.length) {
                        try {
                          var fmt = new Intl.NumberFormat('id-ID');
                          addonSummary.textContent = names.join(', ') + ' • Add-on: ' + currency + ' ' + fmt.format(Math.round(total));
                        } catch (e) {
                          addonSummary.textContent = names.join(', ') + ' • Add-on: ' + currency + ' ' + Math.round(total);
                        }
                      } else {
                        addonSummary.textContent = 'No add-on selected.';
                      }
                    }
                    closeAddonModal();
                  }

                  // Therapist modal functions
                  function openTherapistModal() {
                    if (!therapistModal || !therapistOverlay) return;
                    therapistModal.classList.remove('hidden');
                    therapistModal.classList.add('flex');
                    therapistOverlay.classList.remove('hidden');
                    therapistModal.setAttribute('aria-hidden', 'false');
                  }
                  function closeTherapistModal() {
                    if (!therapistModal || !therapistOverlay) return;
                    therapistModal.classList.add('hidden');
                    therapistModal.classList.remove('flex');
                    therapistOverlay.classList.add('hidden');
                    therapistModal.setAttribute('aria-hidden', 'true');
                  }
                  function clearTherapistSelection() {
                    if (therapistHidden) therapistHidden.value = '';
                    if (therapistSummary) therapistSummary.textContent = 'No therapist selected.';
                    
                    // Remove selection styling from all cards
                    var cards = therapistModal.querySelectorAll('.therapist-card');
                    cards.forEach(function(card) {
                      card.classList.remove('border-teal-500', 'bg-teal-50');
                      card.classList.add('border-transparent');
                    });
                  }
                  function selectTherapist(card) {
                    var therapistId = card.getAttribute('data-therapist-id');
                    var therapistName = card.getAttribute('data-therapist-name');
                    var therapistStatus = card.getAttribute('data-therapist-status');
                    
                    if (therapistHidden) therapistHidden.value = therapistId;
                    if (therapistSummary) therapistSummary.textContent = therapistName + ' (' + therapistStatus + ')';
                    
                    // Remove selection styling from all cards
                    var cards = therapistModal.querySelectorAll('.therapist-card');
                    cards.forEach(function(c) {
                      c.classList.remove('border-teal-500', 'bg-teal-50');
                      c.classList.add('border-transparent');
                    });
                    
                    // Add selection styling to selected card
                    card.classList.remove('border-transparent');
                    card.classList.add('border-teal-500', 'bg-teal-50');
                    
                    // Close modal after selection
                    closeTherapistModal();
                  }

                  // Global functions
                  window.aoSelOpen = openAddonModal;
                  window.aoSelClose = closeAddonModal;
                  window.aoSelApply = applyAddonSelection;
                  window.therapistModalOpen = openTherapistModal;
                  window.therapistModalClose = closeTherapistModal;
                  window.therapistModalClear = clearTherapistSelection;

                  // Add-on modal event listeners
                  document.addEventListener('click', function(e){
                    if (e.target && e.target.id === 'aoSelOverlay') closeAddonModal();
                  });
                  document.addEventListener('keydown', function(e){
                    if (e.key === 'Escape') closeAddonModal();
                  });

                  // Therapist modal event listeners
                  document.addEventListener('click', function(e){
                    if (e.target && e.target.id === 'therapistModalOverlay') closeTherapistModal();
                    
                    // Handle therapist card clicks
                    if (e.target.closest('.therapist-card')) {
                      var card = e.target.closest('.therapist-card');
                      selectTherapist(card);
                    }
                    
                    // Handle time slot clicks
                    if (e.target.closest('.time-slot') && !e.target.closest('.time-slot').classList.contains('booked')) {
                      var slot = e.target.closest('.time-slot');
                      selectTimeSlot(slot);
                    }
                  });
                  document.addEventListener('keydown', function(e){
                    if (e.key === 'Escape') closeTherapistModal();
                  });

                  // Time availability checking
                  var bookedTimes = [];
                  
                  function checkTimeAvailability() {
                    var dateInput = document.getElementById('date');
                    if (!dateInput || !dateInput.value) return;
                    
                    var selectedDate = dateInput.value;
                    var message = document.getElementById('timeSlotMessage');
                    
                    // Show loading state
                    if (message) {
                      message.innerHTML = '<div class="text-blue-600">Checking availability...</div>';
                    }
                    
                    // Fetch availability from backend
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', '<?= site_url('booking/availability'); ?>?date=' + encodeURIComponent(selectedDate), true);
                    xhr.onreadystatechange = function() {
                      if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                          try {
                            var response = JSON.parse(xhr.responseText);
                            bookedTimes = response.booked || [];
                            updateAvailabilityMessage();
                          } catch (e) {
                            console.error('Error parsing availability response:', e);
                            showTimeMessage('Error checking availability. Please try again.', 'error');
                          }
                        } else {
                          console.error('Error fetching availability:', xhr.status);
                          showTimeMessage('Error checking availability. Please try again.', 'error');
                        }
                      }
                    };
                    xhr.send();
                  }
                  
                  function validateTimeSelection() {
                    var timeInput = document.getElementById('time');
                    if (!timeInput || !timeInput.value) return;
                    
                    var selectedTime = timeInput.value; // Format: HH:MM
                    var isBooked = bookedTimes.indexOf(selectedTime) !== -1;
                    
                    if (isBooked) {
                      alert('This time slot is already booked. Please select another time.');
                      timeInput.value = '';
                      showTimeMessage('Selected time is not available. Please choose another time.', 'error');
                    } else {
                      showTimeMessage('Selected time: ' + selectedTime + ' - Available', 'success');
                    }
                  }
                  
                  function updateAvailabilityMessage() {
                    var message = document.getElementById('timeSlotMessage');
                    if (!message) return;
                    
                    if (bookedTimes.length > 0) {
                      var bookedList = bookedTimes.join(', ');
                      showTimeMessage('Booked times: ' + bookedList + '. Please choose an available time.', 'warning');
                    } else {
                      showTimeMessage('All time slots are available for this date.', 'success');
                    }
                  }
                  
                  function showTimeMessage(text, type) {
                    var message = document.getElementById('timeSlotMessage');
                    if (!message) return;
                    
                    var className = '';
                    switch (type) {
                      case 'success':
                        className = 'text-green-600';
                        break;
                      case 'error':
                        className = 'text-red-600';
                        break;
                      case 'warning':
                        className = 'text-amber-600';
                        break;
                      default:
                        className = 'text-slate-600';
                    }
                    
                    message.innerHTML = '<div class="' + className + '">' + text + '</div>';
                  }
                  
                  // Add date change listener
                  var dateInput = document.getElementById('date');
                  if (dateInput) {
                    dateInput.addEventListener('change', function() {
                      // Clear time selection when date changes
                      var timeInput = document.getElementById('time');
                      if (timeInput) timeInput.value = '';
                      
                      // Check availability for new date
                      checkTimeAvailability();
                    });
                  }
                  
                  // Add time change listener for validation
                  var timeInput = document.getElementById('time');
                  if (timeInput) {
                    timeInput.addEventListener('change', function() {
                      validateTimeSelection();
                    });
                  }
                })();
              </script>

              


              <!-- Schedule - Select Date then Time (based on availability) -->
              <div class="mb-4">
                <label class="required block text-sm font-medium text-slate-700" for="date">Date</label>
                <input
                  type="date"
                  class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                  id="date"
                  name="date"
                  required
                  min="<?= date('Y-m-d'); ?>"
                >
                <p class="mt-1 text-xs text-slate-500">Please select a date first.</p>
              </div>

              <div class="mb-4">
                <label class="required block text-sm font-medium text-slate-700" for="time">Select Time</label>
                <input
                  type="time"
                  class="mt-1 w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-slate-900 shadow-sm focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary"
                  id="time"
                  name="time"
                  required
                  step="3600"
                  min="09:00"
                  max="21:00"
                >
                <div id="timeSlotMessage" class="mt-3 text-sm"></div>
                <p class="mt-1 text-xs text-slate-500">Select a time. If the time is already booked, you will be alerted.</p>
              </div>

              <!-- No dynamic slot loading needed anymore -->

              <div class="mt-6">
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-sky-500 px-4 py-3 text-base font-semibold text-white shadow-sm transition hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-500">
                  Submit Booking
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer note -->
    <div class="mt-10 text-sm text-slate-500">
      <p class="mb-1">After the booking is submitted, notifications will be forwarded to admin via Telegram.</p>
      <p class="mb-0">Admin will confirm the availability of schedule and therapist.</p>
    </div>
  </div>
</body>
</html>