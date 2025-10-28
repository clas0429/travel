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
        <span class="gm-badge">Local Photos</span>
        <h1 class="gm-section__title"><?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['name'] ?? ''); ?> — 旅途相片</h1>
        <p class="gm-section__subtitle">影像改為引用本地 SVG 與圖片檔案，支援離線展示與說明文字。</p>
      </div>
    </section>
    <?php if (!empty($GM_V2_CURRENT_LOCATION['photos'])): ?>
    <section class="gm-grid gm-grid--gallery">
      <?php foreach ($GM_V2_CURRENT_LOCATION['photos'] as $photo): ?>
      <figure class="gm-photo-card">
        <?php if (!empty($photo['image'])): ?>
        <img src="<?php echo gm_v2_escape($photo['image']); ?>" alt="<?php echo gm_v2_escape($photo['title'] ?? '示範相片'); ?>">
        <?php endif; ?>
        <figcaption>
          <h4><?php echo gm_v2_escape($photo['title'] ?? '示範相片'); ?></h4>
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
    <section class="gm-empty">尚未設定任何地點資料，請更新 <code>config/local-data.php</code>。</section>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
