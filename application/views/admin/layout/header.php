<?php
$title = isset($title) ? $title : 'Admin';
$uri = function_exists('uri_string') ? uri_string() : '';
$isActive = function($patterns) use ($uri) {
    foreach ((array)$patterns as $p) {
        if (strpos($uri, $p) === 0) {
            return 'bg-white/10 text-white';
        }
    }
    return 'text-gray-300 hover:text-white hover:bg-white/10';
};
?>
<!DOCTYPE html>
<html lang="ms">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title); ?> - Admin</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { brand: { DEFAULT: '#0ea5e9' } }
        }
      }
    };
  </script>

  <style>
    html, body { height: 100%; }
    /* Print-friendly: hide chrome, expand content */
    @media print {
      aside, header, .no-print { display: none !important; }
      main { margin: 0 !important; padding: 0 !important; }
      body { background: #ffffff !important; }
    }
  </style>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
<div class="min-h-screen flex">
  <!-- Sidebar -->
  <aside class="w-64 bg-slate-900 text-slate-100 fixed inset-y-0 left-0 flex flex-col">
    <div class="h-14 flex items-center px-4 border-b border-slate-800">
      <span class="font-semibold tracking-wide">Spa Admin</span>
    </div>

    <nav class="p-3 space-y-1 text-sm">
      <a href="<?= site_url('admin'); ?>"
         class="block px-3 py-2 rounded-md <?= ($uri === 'admin' || $uri === 'admin/index') ? 'bg-white/10 text-white' : 'text-gray-300 hover:text-white hover:bg-white/10'; ?>">
        Dashboard
      </a>
      <a href="<?= site_url('admin/therapists'); ?>"
         class="block px-3 py-2 rounded-md <?= $isActive(['admin/therapists','admin/therapist']); ?>">
        Therapists
      </a>
      <a href="<?= site_url('admin/packages'); ?>"
         class="block px-3 py-2 rounded-md <?= $isActive(['admin/packages','admin/package']); ?>">
        Packages
      </a>
      <a href="<?= site_url('admin/schedule'); ?>"
         class="block px-3 py-2 rounded-md <?= $isActive(['admin/schedule']); ?>">
        Schedule
      </a>
      <a href="<?= site_url('admin/report'); ?>"
         class="block px-3 py-2 rounded-md <?= $isActive(['admin/report']); ?>">
        Reports
      </a>
    </nav>

    <div class="mt-auto p-4 text-xs text-slate-400">
      © <?= date('Y'); ?> Spa Management
    </div>
  </aside>

  <!-- Content wrapper -->
  <div class="flex-1 min-h-screen ml-64 flex flex-col">
    <!-- Topbar -->
    <header class="h-14 bg-white border-b border-gray-200 flex items-center justify-between px-6">
      <h1 class="font-semibold text-gray-900"><?= htmlspecialchars($title); ?></h1>
      <div class="text-xs text-gray-500">Admin Panel</div>
    </header>

    <!-- Main content -->
    <main class="flex-1 p-6">