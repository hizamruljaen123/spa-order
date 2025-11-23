<?php
$this->load->view('admin/layout/header', ['title' => isset($title) ? $title : 'Revenue Report']);
?>
<!-- Filters -->
<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
  <div>
    <h2 class="text-base font-semibold text-gray-200">Revenue Report</h2>
    <p class="text-sm text-gray-300">Monthly revenue analysis and total bookings.</p>
  </div>
  <div class="flex items-center gap-3">
    <label for="yearSelect" class="text-sm text-gray-300">Year</label>
    <select id="yearSelect" class="rounded-md border-gray-600 bg-gray-700 shadow-sm focus:border-sky-500 focus:ring-sky-500 text-sm text-slate-100">
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
  <div class="rounded-lg border border-gray-600 bg-gray-800 p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-emerald-400 font-semibold">Total Revenue This Year</div>
    <div id="sumRevenue" class="mt-2 text-4xl font-bold text-gray-200">RM 0</div>
    <p class="mt-1 text-gray-400 text-sm">Total from all months in selected year.</p>
  </div>
  <div class="rounded-lg border border-gray-600 bg-gray-800 p-5 shadow-sm">
    <div class="text-xs uppercase tracking-wide text-sky-400 font-semibold">Total Bookings This Year</div>
    <div id="sumBooking" class="mt-2 text-4xl font-bold text-gray-200">0</div>
    <p class="mt-1 text-gray-400 text-sm">Total bookings throughout the year.</p>
  </div>
</div>

<!-- Chart -->
<div class="rounded-lg border border-gray-600 bg-gray-800 shadow-sm mb-6">
  <div class="px-5 py-3 border-b border-gray-700 flex items-center justify-between">
    <span class="text-sm font-medium text-gray-200">Monthly Revenue Chart</span>
    <label class="inline-flex items-center gap-2 text-sm text-gray-300">
      <input type="checkbox" id="toggleBooking" class="rounded border-gray-600 text-sky-600 focus:ring-sky-500 bg-gray-700" checked>
      Show Total Bookings
    </label>
  </div>
  <div class="p-5">
    <canvas id="revChart" height="120"></canvas>
  </div>
</div>

<!-- Table data -->
<div class="rounded-lg border border-gray-600 bg-gray-800 shadow-sm">
  <div class="px-5 py-3 border-b border-gray-700">
    <h3 class="text-sm font-medium text-gray-200">Monthly Details</h3>
  </div>
  <div class="p-0">
    <div class="overflow-x-auto">
      <table class="min-w-full table-auto" id="detailTable">
        <thead class="bg-gray-700">
          <tr class="text-left text-sm text-gray-200">
            <th class="px-5 py-3 w-20">Month</th>
            <th class="px-5 py-3 text-right w-56">Revenue</th>
            <th class="px-5 py-3 text-center w-40">Bookings</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-700 bg-gray-800"><!-- dynamic --></tbody>
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
      return 'RM ' + n.toLocaleString('ms-MY', { maximumFractionDigits: 0 });
    } catch (e) {
      return 'RM ' + (num || 0);
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
            label: 'Revenue (RM)',
            data: revenue,
            backgroundColor: 'rgba(16, 185, 129, 0.6)',  // emerald-500
            borderColor: 'rgba(16, 185, 129, 1)',
            borderWidth: 1,
            yAxisID: 'y'
          },
          {
            type: 'line',
            label: 'Total Bookings',
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
                if (ctx.dataset.label.includes('Revenue')) {
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
        console.error('Failed to load report data:', err);
        alert('Failed to load report data. Please try again.');
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

<!-- Additional Analytics: Peak Hours/Days, Heatmap, Top Therapists & Packages -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-12 gap-6">
  <!-- Peak hours (annual average) -->
  <div class="rounded-lg md:col-span-6 border border-gray-600 bg-gray-800 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-700 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-medium text-gray-200">Order Distribution per Hour</h3>
        <p class="text-xs text-gray-400">Average number of orders per hour throughout the selected year.</p>
      </div>
      <div class="text-xs">
        <span class="text-gray-300">Peak Hour:</span>
        <span id="busiestHourLabel" class="ml-2 inline-flex items-center rounded bg-amber-900/50 px-2 py-1 font-semibold text-amber-300">-</span>
      </div>
    </div>
    <div class="p-5">
      <canvas id="hourChart" height="90"></canvas>
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full table-auto text-sm" id="hourTable">
          <thead class="bg-gray-700">
            <tr class="text-left text-gray-200">
              <th class="px-5 py-3 w-32">Hour</th>
              <th class="px-5 py-3 text-center w-40">Total Orders</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-700 bg-gray-800"></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Peak order day -->
  <div class="rounded-lg md:col-span-6 border border-gray-600 bg-gray-800 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-700 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-medium text-gray-200">Order Distribution per Day</h3>
        <p class="text-xs text-gray-400">Number of orders per day (Monday to Sunday) throughout the selected year.</p>
      </div>
      <div class="text-xs">
        <span class="text-gray-300">Peak Day:</span>
        <span id="busiestDayLabel" class="ml-2 inline-flex items-center rounded bg-emerald-900/50 px-2 py-1 font-semibold text-emerald-300">-</span>
      </div>
    </div>
    <div class="p-5">
      <canvas id="weekdayChart" height="90"></canvas>
      <div class="mt-4 overflow-x-auto">
        <table class="min-w-full table-auto text-sm" id="weekdayTable">
          <thead class="bg-gray-700">
            <tr class="text-left text-gray-200">
              <th class="px-5 py-3 w-48">Day</th>
              <th class="px-5 py-3 text-center w-40">Total Orders</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-700 bg-gray-800"></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Day x Hour Heatmap -->
  <div class="rounded-lg md:col-span-12 border border-gray-600 bg-gray-800 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-700 flex items-center justify-between">
      <div>
        <h3 class="text-sm font-medium text-gray-200">Day x Hour Heatmap</h3>
        <p class="text-xs text-gray-400">Order density for each day and hour combination.</p>
      </div>
      <div class="text-xs">
        <span class="text-gray-300">Peak Slot:</span>
        <span id="busiestSlotLabel" class="ml-2 inline-flex items-center rounded bg-rose-900/50 px-2 py-1 font-semibold text-rose-300">-</span>
      </div>
    </div>
    <div class="p-5">
      <div id="heatmapContainer" class="overflow-x-auto"></div>
      <p class="mt-2 text-[11px] text-gray-400">Darker colors indicate higher order volume for that time slot.</p>
    </div>
  </div>

  <!-- Top Therapists -->
  <div class="rounded-lg md:col-span-6 border border-gray-600 bg-gray-800 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-700">
      <h3 class="text-sm font-medium text-gray-200">Top Therapists by Orders</h3>
    </div>
    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <canvas id="topTherapistChart" height="120"></canvas>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto text-sm" id="topTherapistTable">
          <thead class="bg-gray-700">
            <tr class="text-left text-gray-200">
              <th class="px-5 py-3">Therapist</th>
              <th class="px-5 py-3 text-center w-40">Total Orders</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-700 bg-gray-800"></tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Top Packages -->
  <div class="rounded-lg md:col-span-6 border border-gray-600 bg-gray-800 shadow-sm">
    <div class="px-5 py-3 border-b border-gray-700">
      <h3 class="text-sm font-medium text-gray-200">Top Packages by Orders</h3>
    </div>
    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-6">
      <div>
        <canvas id="topPackageChart" height="120"></canvas>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto text-sm" id="topPackageTable">
          <thead class="bg-gray-700">
            <tr class="text-left text-gray-200">
              <th class="px-5 py-3">Package</th>
              <th class="px-5 py-3 text-center w-40">Total Orders</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-700 bg-gray-800"></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  // Elemen rujukan
  const yearSelect = document.getElementById('yearSelect');
  const refreshBtn = document.getElementById('refreshBtn');

  // Chart instances
  let hourChartInst = null;
  let weekdayChartInst = null;
  let topTherChartInst = null;
  let topPackChartInst = null;

  // Util: Bar chart factory
  function barChart(ctx, labels, data, labelText, color) {
    const cfg = {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          type: 'bar',
          label: labelText,
          data: data,
          backgroundColor: color.bg,
          borderColor: color.border,
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: true, position: 'top' } },
        scales: { y: { beginAtZero: true } }
      }
    };
    return new Chart(ctx, cfg);
  }

  // Util: Build table body
  function fillTable(tbody, rows) {
    tbody.innerHTML = '';
    rows.forEach(r => {
      const tr = document.createElement('tr');
      tr.className = 'text-sm';
      tr.innerHTML = r;
      tbody.appendChild(tr);
    });
  }

  // Util: Heat color (green -> yellow -> orange -> red)
  function colorFor(val, max) {
    if (!max || max <= 0) return 'rgba(241,245,249,1)'; // slate-100
    const ratio = Math.min(1, val / max);
    const hue = 120 - (120 * ratio); // 120 (green) -> 0 (red)
    const light = 90 - (50 * ratio); // lighter -> darker
    return 'hsl(' + hue + ', 85%, ' + light + '%)';
  }

  // Hourly
  function loadHourly() {
    const y = yearSelect ? yearSelect.value : (new Date()).getFullYear();
    const url = '<?= site_url('report/hourly'); ?>?year=' + encodeURIComponent(y);
    return fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(payload => {
        const labels = payload.labels || [];
        const data = payload.total_booking || [];
        // Peak hour
        let maxIdx = 0, maxVal = -1;
        data.forEach((v, i) => { if (Number(v) > maxVal) { maxVal = Number(v); maxIdx = i; } });
        const busiestHourEl = document.getElementById('busiestHourLabel');
        if (busiestHourEl) busiestHourEl.textContent = (labels[maxIdx] || '-') + ':00 (' + (maxVal || 0) + ' orders)';

        // Chart
        const ctx = document.getElementById('hourChart');
        if (hourChartInst) hourChartInst.destroy();
        hourChartInst = barChart(ctx, labels, data, 'Total Orders', { bg: 'rgba(14,165,233,0.6)', border: 'rgba(14,165,233,1)' });

        // Table (top 10 hours)
        const pairs = labels.map((l, i) => ({ label: l + ':00', val: Number(data[i] || 0) }));
        pairs.sort((a,b) => b.val - a.val);
        const top = pairs.slice(0, 10);
        const tbody = document.querySelector('#hourTable tbody');
        fillTable(tbody, top.map(item => `
          <td class="px-5 py-3">${item.label}</td>
          <td class="px-5 py-3 text-center">${item.val}</td>
        `));
      });
  }

  // Weekday
  function loadWeekday() {
    const y = yearSelect ? yearSelect.value : (new Date()).getFullYear();
    const url = '<?= site_url('report/weekday'); ?>?year=' + encodeURIComponent(y);
    return fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(payload => {
        const labels = payload.labels || ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        const data = payload.total_booking || [];
        // Peak day
        let maxIdx = 0, maxVal = -1;
        data.forEach((v, i) => { if (Number(v) > maxVal) { maxVal = Number(v); maxIdx = i; } });
        const busiestDayEl = document.getElementById('busiestDayLabel');
        if (busiestDayEl) busiestDayEl.textContent = (labels[maxIdx] || '-') + ' (' + (maxVal || 0) + ' orders)';

        // Chart
        const ctx = document.getElementById('weekdayChart');
        if (weekdayChartInst) weekdayChartInst.destroy();
        weekdayChartInst = barChart(ctx, labels, data, 'Total Orders', { bg: 'rgba(16,185,129,0.6)', border: 'rgba(16,185,129,1)' });

        // Table (descending order)
        const pairs = labels.map((l, i) => ({ label: l, val: Number(data[i] || 0) }));
        pairs.sort((a,b) => b.val - a.val);
        const tbody = document.querySelector('#weekdayTable tbody');
        fillTable(tbody, pairs.map(item => `
          <td class="px-5 py-3">${item.label}</td>
          <td class="px-5 py-3 text-center">${item.val}</td>
        `));
      });
  }

  // Heatmap
  function loadHeatmap() {
    const y = yearSelect ? yearSelect.value : (new Date()).getFullYear();
    const url = '<?= site_url('report/heatmap'); ?>?year=' + encodeURIComponent(y);
    return fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(payload => {
        const days = payload.days || ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        const hours = payload.hours || Array.from({length:24}, (_,i)=>String(i).padStart(2,'0'));
        const matrix = payload.matrix || [];
        const busiest = payload.busiest || null;

        // Update label
        if (busiest) {
          const lbl = document.getElementById('busiestSlotLabel');
          if (lbl) lbl.textContent = `${busiest.day} ${busiest.hour}:00 (${busiest.count} orders)`;
        }

        // Find max for scaling
        let max = 0;
        for (let r = 0; r < matrix.length; r++) {
          for (let c = 0; c < (matrix[r] || []).length; c++) {
            max = Math.max(max, Number(matrix[r][c] || 0));
          }
        }

        // Build table
        const container = document.getElementById('heatmapContainer');
        const tbl = document.createElement('table');
        tbl.className = 'text-xs border-collapse';
        // THEAD
        const thead = document.createElement('thead');
        const htr = document.createElement('tr');
        htr.innerHTML = ['<th class="p-1 text-gray-500">Day/Hour</th>']
          .concat(hours.map(h => `<th class="px-1 py-1 text-gray-600">${h}</th>`))
          .join('');
        thead.appendChild(htr);
        tbl.appendChild(thead);
        // TBODY
        const tbody = document.createElement('tbody');
        for (let r = 0; r < days.length; r++) {
          const tr = document.createElement('tr');
          const row = [`<td class="px-2 py-1 text-gray-700 whitespace-nowrap">${days[r]}</td>`];
          for (let c = 0; c < hours.length; c++) {
            const v = Number((matrix[r] && matrix[r][c]) || 0);
            const bg = colorFor(v, max);
            const color = v > (max * 0.6) ? '#111827' : '#374151'; // darker text for high density
            row.push(`<td class="px-1 py-1 text-center" style="background:${bg}; color:${color}; min-width:28px">${v || ''}</td>`);
          }
          tr.innerHTML = row.join('');
          tbody.appendChild(tr);
        }
        tbl.appendChild(tbody);

        container.innerHTML = '';
        container.appendChild(tbl);
      });
  }

  // Top therapists
  function loadTopTherapists() {
    const y = yearSelect ? yearSelect.value : (new Date()).getFullYear();
    const url = '<?= site_url('report/top_therapists'); ?>?year=' + encodeURIComponent(y) + '&limit=7';
    return fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(payload => {
        const labels = payload.labels || [];
        const data = payload.total_booking || [];
        // Chart
        const ctx = document.getElementById('topTherapistChart');
        if (topTherChartInst) topTherChartInst.destroy();
        topTherChartInst = barChart(ctx, labels, data, 'Total Orders', { bg: 'rgba(99,102,241,0.6)', border: 'rgba(99,102,241,1)' });
        // Table
        const tbody = document.querySelector('#topTherapistTable tbody');
        const rows = labels.map((l, i) => ({ label: l, val: Number(data[i] || 0) }))
                           .sort((a,b) => b.val - a.val)
                           .map(item => `
                              <td class="px-5 py-3">${item.label}</td>
                              <td class="px-5 py-3 text-center">${item.val}</td>
                           `);
        fillTable(tbody, rows);
      });
  }

  // Top packages
  function loadTopPackages() {
    const y = yearSelect ? yearSelect.value : (new Date()).getFullYear();
    const url = '<?= site_url('report/top_packages'); ?>?year=' + encodeURIComponent(y) + '&limit=7';
    return fetch(url, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
      .then(payload => {
        const labels = payload.labels || [];
        const data = payload.total_booking || [];
        // Chart
        const ctx = document.getElementById('topPackageChart');
        if (topPackChartInst) topPackChartInst.destroy();
        topPackChartInst = barChart(ctx, labels, data, 'Total Orders', { bg: 'rgba(244,114,182,0.6)', border: 'rgba(244,114,182,1)' });
        // Table
        const tbody = document.querySelector('#topPackageTable tbody');
        const rows = labels.map((l, i) => ({ label: l, val: Number(data[i] || 0) }))
                           .sort((a,b) => b.val - a.val)
                           .map(item => `
                              <td class="px-5 py-3">${item.label}</td>
                              <td class="px-5 py-3 text-center">${item.val}</td>
                           `);
        fillTable(tbody, rows);
      });
  }

  function loadAll() {
    Promise.all([
      loadHourly(),
      loadWeekday(),
      loadHeatmap(),
      loadTopTherapists(),
      loadTopPackages()
    ]).catch(err => {
      console.error('Gagal memuatkan analitik:', err);
      alert('Gagal memuatkan analitik. Cuba lagi.');
    });
  }

  if (refreshBtn) {
    refreshBtn.addEventListener('click', loadAll);
  }

  // Initial load
  loadAll();
})();
</script>
<?php $this->load->view('admin/layout/footer'); ?>