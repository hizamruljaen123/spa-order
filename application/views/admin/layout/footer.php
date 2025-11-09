    </main>
    <!-- End main content -->
  </div>
  <!-- End content wrapper -->
</div>
<!-- End app shell -->

<!-- Global scripts (optional) -->
<script>
  // Improved modal handling: support ESC and overlay click for pkg edit modal (if present)
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      if (typeof window.pkgEditClose === 'function') {
        window.pkgEditClose();
        return;
      }
      const openDialog = document.querySelector('[role="dialog"][aria-hidden="false"]');
      if (openDialog) {
        openDialog.setAttribute('aria-hidden', 'true');
      }
      const overlay = document.getElementById('pkgEditModalOverlay');
      if (overlay) overlay.classList.add('hidden');
    }
  });
  document.addEventListener('click', function(e){
    if (e.target && e.target.id === 'pkgEditModalOverlay') {
      if (typeof window.pkgEditClose === 'function') {
        window.pkgEditClose();
      } else {
        const dlg = document.querySelector('#pkgEditModal[role="dialog"]');
        if (dlg) dlg.setAttribute('aria-hidden', 'true');
        e.target.classList.add('hidden');
      }
    }
  });
</script>
</body>
</html>