<?php
require_once 'includes/db.php';
include 'includes/header.php';
?>
<main class="container">
  <h2>Gallery</h2>
  <p>Images of the resort are shown below. Upload images to the images/ directory.</p>
  <div class="grid">
    <?php
    $files = glob('images/*.{jpg,jpeg,png,gif}', GLOB_BRACE);
    if ($files):
      foreach ($files as $f):
    ?>
      <div class="card"><img src="<?=htmlspecialchars($f)?>" alt="" style="max-width:100%"></div>
    <?php
      endforeach;
    else:
    ?>
      <p>No images yet. Put images in the images/ folder.</p>
    <?php endif; ?>
  </div>
</main>
<?php include 'includes/footer.php'; ?>
