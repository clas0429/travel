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
        <span class="gm-badge">Local Videos</span>
        <h1 class="gm-section__title"><?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['name'] ?? ''); ?> — 影音花絮</h1>
        <p class="gm-section__subtitle">影音改以內嵌 SVG 與本地資源呈現，即使離線也能播放示範片段。</p>
      </div>
    </section>
    <?php if (!empty($GM_V2_CURRENT_LOCATION['videos'])): ?>
    <section class="gm-card-list">
      <?php foreach ($GM_V2_CURRENT_LOCATION['videos'] as $video): ?>
      <article class="gm-video-card">
        <header class="gm-section__header">
          <h3 class="gm-section__title" style="font-size:1.2rem;">
            <?php echo gm_v2_escape($video['title'] ?? '示範影片'); ?>
          </h3>
          <?php if (!empty($video['description'])): ?>
          <p class="gm-section__subtitle"><?php echo gm_v2_escape($video['description']); ?></p>
          <?php endif; ?>
        </header>
        <div class="gm-video-frame">
          <?php
          $type = $video['type'] ?? 'inlineSvg';
          if ($type === 'inlineSvg' && !empty($video['svg'])) {
              echo $video['svg'];
          } elseif ($type === 'localVideo' && !empty($video['source'])) {
              $poster = $video['poster'] ?? '/assets/images/v2/video-poster.svg';
              $mime = $video['mime'] ?? 'video/mp4';
          ?>
          <video controls poster="<?php echo gm_v2_escape($poster); ?>">
            <source src="<?php echo gm_v2_escape($video['source']); ?>" type="<?php echo gm_v2_escape($mime); ?>">
            您的瀏覽器不支援影片播放。
          </video>
          <?php
          } else {
          ?>
          <img src="/assets/images/v2/video-poster.svg" alt="影片預覽圖">
          <?php
          }
          ?>
        </div>
      </article>
      <?php endforeach; ?>
    </section>
    <?php else: ?>
    <section class="gm-empty">尚未為此地點加入影音內容。</section>
    <?php endif; ?>
    <?php else: ?>
    <section class="gm-empty">尚未設定任何地點資料，請更新 <code>config/local-data.php</code>。</section>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
