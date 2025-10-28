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
        <span class="gm-badge">Local Diaries</span>
        <h1 class="gm-section__title"><?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['name'] ?? ''); ?> — 旅遊日誌</h1>
        <p class="gm-section__subtitle">
          內容直接取自本地設定的 HTML 區塊，支援多段落格式與列表項目。
        </p>
      </div>
      <?php if (!empty($GM_V2_CURRENT_LOCATION['mapUrl'])): ?>
      <a class="gm-map-link" href="<?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['mapUrl']); ?>" target="_blank" rel="noopener">查看示範地圖</a>
      <?php endif; ?>
    </section>
    <?php if (!empty($GM_V2_CURRENT_LOCATION['diaries'])): ?>
    <section class="gm-card-list">
      <?php foreach ($GM_V2_CURRENT_LOCATION['diaries'] as $diary): ?>
      <article class="gm-entry">
        <header>
          <h4><?php echo gm_v2_escape($diary['title'] ?? '未命名日誌'); ?></h4>
          <?php $formatted = gm_v2_format_date($diary['createdAt'] ?? null); ?>
          <?php if ($formatted): ?>
          <time datetime="<?php echo gm_v2_escape($diary['createdAt']); ?>"><?php echo gm_v2_escape($formatted); ?></time>
          <?php endif; ?>
        </header>
        <div class="gm-rich-text">
          <?php echo $diary['content'] ?? '<p>目前沒有內容。</p>'; ?>
        </div>
      </article>
      <?php endforeach; ?>
    </section>
    <?php else: ?>
    <section class="gm-empty">尚未為此地點建立日誌內容。</section>
    <?php endif; ?>
    <?php else: ?>
    <section class="gm-empty">尚未設定任何地點資料，請更新 <code>config/local-data.php</code>。</section>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
