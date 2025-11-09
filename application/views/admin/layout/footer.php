    </main>
    <!-- End main content -->
  </div>
  <!-- End content wrapper -->
</div>
<!-- End app shell -->

<!-- Global scripts (optional) -->
<script>
  // Simple helper: close any open dialogs on ESC, improve accessibility
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const openDialog = document.querySelector('[role="dialog"][aria-hidden="false"]');
      if (openDialog) {
        openDialog.setAttribute('aria-hidden', 'true');
      }
    }
  });
</script>
</body>
</html>