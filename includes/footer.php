<?php
// includes/footer.php
$currentScript = basename($_SERVER['PHP_SELF']);
$flash = getFlash();
?>
  <!-- Bottom Navigation Bar (Gojek Style) -->
  <nav class="bottom-nav">
    <a href="index.php" class="nav-item <?= $currentScript === 'index.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-house"></i>
      <span>Beranda</span>
    </a>
    <a href="lapor.php" class="nav-item <?= $currentScript === 'lapor.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-bullhorn"></i>
      <span>GoLapor</span>
    </a>
    <a href="surat.php" class="nav-item <?= $currentScript === 'surat.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-file-signature"></i>
      <span>GoSurat</span>
    </a>
    <a href="agenda.php" class="nav-item <?= $currentScript === 'agenda.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-calendar-days"></i>
      <span>GoAgenda</span>
    </a>
    <a href="profil.php" class="nav-item <?= $currentScript === 'profil.php' ? 'active' : '' ?>">
      <i class="fa-solid fa-user-gear"></i>
      <span>Profil</span>
    </a>
  </nav>

</div> <!-- End .app-shell -->

<!-- MaoneArt Modal Container -->
<div id="maoneartModalContainer" class="maoneart-modal-backdrop"></div>

<!-- Core JS -->
<script src="assets/js/app.js"></script>

<?php if ($flash): ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  showAlertModal({
    title: '<?= $flash['type'] === 'success' ? 'Berhasil!' : ($flash['type'] === 'danger' ? 'Perhatian!' : 'Informasi') ?>',
    message: '<?= addslashes($flash['message']) ?>',
    type: '<?= $flash['type'] ?>',
    icon: '<?= $flash['type'] === 'success' ? 'fa-circle-check' : ($flash['type'] === 'danger' ? 'fa-triangle-exclamation' : 'fa-circle-info') ?>'
  });
});
</script>
<?php endif; ?>

</body>
</html>
