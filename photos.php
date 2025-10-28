<?php
require __DIR__ . '/includes/bootstrap.php';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/header.php';
?>
<main class="gm-page">
  <div class="gm-container gm-stack">
    <?php if ($GM_V2_CURRENT_LOCATION): ?>
    <section class="gm-card gm-section">
      <div class="gm-section__header">
        <span class="gm-badge">Travel Photos</span>
        <h1 class="gm-section__title"><?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['name'] ?? ''); ?> — 旅途相片</h1>
        <p class="gm-section__subtitle">精選行程影像與專業說明，協助旅人掌握最佳取景時刻。</p>
      </div>
    </section>
    <?php if (!empty($GM_V2_CURRENT_LOCATION['photos'])): ?>
    <section class="gm-grid gm-grid--gallery">
      <?php foreach ($GM_V2_CURRENT_LOCATION['photos'] as $photo): ?>
      <figure class="gm-photo-card">
        <?php if (!empty($photo['image'])): ?>
        <img src="<?php echo gm_v2_escape($photo['image']); ?>" alt="<?php echo gm_v2_escape($photo['title'] ?? '旅程相片'); ?>">
        <?php endif; ?>
        <figcaption>
          <h4><?php echo gm_v2_escape($photo['title'] ?? '旅程相片'); ?></h4>
          <?php if (!empty($photo['description'])): ?>
          <p><?php echo gm_v2_escape($photo['description']); ?></p>
          <?php endif; ?>
          <?php if (!empty($photo['attribution'])): ?>
          <p class="gm-attribution">攝影：<?php echo gm_v2_escape($photo['attribution']); ?></p>
          <?php endif; ?>
        </figcaption>
      </figure>
      <?php endforeach; ?>
    </section>
    <?php else: ?>
    <section class="gm-empty">尚未為此地點加入相片。</section>
    <?php endif; ?>
    <?php else: ?>
    <section class="gm-empty">尚未設定任何地點資料，請確認資料庫內容或執行 <code>php database/seed.php</code>。</section>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
