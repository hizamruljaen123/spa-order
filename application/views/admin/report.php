<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Laporan Pendapatan']);
?>
<!-- Filters -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <div>
    <h2 class="text-base font-semibold text-gray-900">Laporan Pendapatan</h2>
    <p class="text-sm text-gray-600">Analisis pendapatan bulanan dan jumlah booking.</p>
  </div>
  <div class="flex items-center gap-3">
    <label for="yearSelect" class="text-sm text-gray-700">Tahun</label>
    <select id="yearSelect" class="rounded-md border-gray-300 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm">
      <?php
      $currentYear = isset($year) ? (int)$year : (int)date('Y');
      for ($y = $currentYear - 4; $y <= $currentYear + 1; $y++):
      ?>
        <option value="<?= $y; ?>" <?= $y === $currentYear ? 'selected' : ''; ?>><?= $y; ?></option>
      <?php endfor; ?>
    </select>
    <button id="refreshBtn" class="inline-flex items-center rounded-md bg-sky-600 text-white px-3 py-2 text-sm hover:bg-sky-700">Refresh</button>
  </div>
</div>

<!-- Summary cards -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
  <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-emerald-600 font-semibold">Total Pendapatan Tahun Ini</div>
    <div id="sumRevenue" class="mt-2 text-4xl font-bold text-gray-900">Rp 0</div>
    <p class="mt-1 text-gray-500 text-sm">Total dari semua bulan pada tahun terpilih.</p>
  </div>
  <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-sky-600 font-semibold">Total Booking Tahun Ini</div>
    <div id="sumBooking" class="mt-2 text-4xl font-bold text-gray-900">0</div>
    <p class="mt-1 text-gray-500 text-sm">Jumlah pesanan sepanjang tahun.</p>
  </div>
</div>

<!-- Chart -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm mb-6">
  <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
    <span class="text-sm font-medium text-gray-900">Grafik Pendapatan per Bulan</span>
    <label class="inline-flex items-center gap-2 text-sm text-gray-700">
      <input type="checkbox" id="toggleBooking" class="rounded border-gray-300 text-sky-600 focus:ring-sky-500" checked>
      Tampilkan Jumlah Booking
    </label>
  </div>
  <div class="p-5">
    <canvas id="revChart" height="120"></canvas>
  </div>
</div>

<!-- Table data -->
<div class="rounded-lg border border-gray-200 bg-white shadow-sm">
  <div class="px-5 py-3 border-b border-gray-200">
    <h3 class="text-sm font-medium text-gray-900">Rincian Bulanan</h3>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto" id="detailTable">
        <thead class="bg-gray-50">
          <tr class="text-left text-sm text-gray-600">
            <th class="px-5 py-3 w-20">Bulan</th>
            <th class="px-5 py-3 text-right w-56">Pendapatan</th>
            <th class="px-5 py-3 text-center w-40">Booking</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200"><!-- dynamic --></tbody>
      </table>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
(function() {
  const yearSelect = document.getElementById('yearSelect');
  const refreshBtn = document.getElementById('refreshBtn');
  const sumRevenueEl = document.getElementById('sumRevenue');
  const sumBookingEl = document.getElementById('sumBooking');
  const toggleBooking = document.getElementById('toggleBooking');
  const tableBody = document.querySelector('#detailTable tbody');

  const ctx = document.getElementById('revChart');
  let chart;

  function formatRupiah(num) {
    try {
      const n = typeof num === 'number' ? num : parseFloat(num || 0);
      return 'Rp ' + n.toLocaleString('id-ID', { maximumFractionDigits: 0 });
    } catch (e) {
      return 'Rp ' + (num || 0);
    }
  }

  function renderTable(labels, revenue, booking) {
    tableBody.innerHTML = '';
    let totalRev = 0;
    let totalBook = 0;

    for (let i = 0; i < labels.length; i++) {
      const r = Number(revenue[i] || 0);
      const b = Number(booking[i] || 0);
      totalRev += r;
      totalBook += b;

      const tr = document.createElement('tr');
      tr.className = 'text-sm';
      tr.innerHTML = `
        <td class="px-5 py-3">${labels[i]}</td>
        <td class="px-5 py-3 text-right">${formatRupiah(r)}</td>
        <td class="px-5 py-3 text-center">${b}</td>
      `;
      tableBody.appendChild(tr);
    }

    sumRevenueEl.textContent = formatRupiah(totalRev);
    sumBookingEl.textContent = String(totalBook);
  }

  function renderChart(labels, revenue, booking) {
    if (chart) chart.destroy();

    chart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            type: 'bar',
            label: 'Pendapatan (Rp)',
            data: revenue,
            backgroundColor: 'rgba(16, 185, 129, 0.6)',  // emerald-500
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 1,
            yAxisID: 'y'
          },
          {
            type: 'line',
            label: 'Jumlah Booking',
            data: booking,
            backgroundColor: 'rgba(14, 165, 233, 0.4)', // sky-500
            borderColor: 'rgba(14, 165, 233, 1)',
            borderWidth: 2,
            tension: 0.2,
            yAxisID: 'y1',
            hidden: !toggleBooking.checked
          }
        ]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        stacked: false,
        plugins: {
          legend: { position: 'top' },
          tooltip: {
            callbacks: {
              label: function(ctx) {
                if (ctx.dataset.label.includes('Pendapatan')) {
                  return ctx.dataset.label + ': ' + formatRupiah(ctx.parsed.y);
                }
                return ctx.dataset.label + ': ' + ctx.parsed.y;
              }
            }
          }
        },
        scales: {
          y: {
            type: 'linear',
            position: 'left',
            ticks: {
              callback: value => formatRupiah(value)
            }
          },
          y1: {
            type: 'linear',
            position: 'right',
            grid: { drawOnChartArea: false },
            beginAtZero: true
          }
        }
      }
    });
  }

  function loadData() {
    const y = yearSelect.value;
    const url = '<?= site_url('report/monthly'); ?>?year=' + encodeURIComponent(y);

    fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(resp => {
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        return resp.json();
      })
      .then(data => {
        const labels = data.labels || [];
        const revenue = data.total_pendapatan || [];
        const booking = data.total_booking || [];
        renderTable(labels, revenue, booking);
        renderChart(labels, revenue, booking);
      })
      .catch(err => {
        console.error('Gagal memuat data laporan:', err);
        alert('Gagal memuat data laporan. Coba lagi.');
      });
  }

  refreshBtn.addEventListener('click', loadData);
  toggleBooking.addEventListener('change', () => {
    if (chart && chart.data.datasets[1]) {
      chart.data.datasets[1].hidden = !toggleBooking.checked;
      chart.update();
    }
  });

  // Initial load
  loadData();
})();
</script>

<?php $this->load->view('admin/layout/footer'); ?>