<?php
ini_set('display_errors', 1);
require __DIR__ . '/includes/bootstrap.php';
include __DIR__ . '/partials/head.php';
include __DIR__ . '/partials/header.php';
?>
<main class="gm-page">
  <div class="gm-container gm-stack">
    <?php if ($GM_V2_CURRENT_LOCATION): ?>
    <section class="gm-hero">
      <div class="gm-hero__text">
        <span class="gm-badge">Featured Route</span>
        <h1 class="gm-hero__title"><?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['name'] ?? ''); ?></h1>
        <?php if (!empty($GM_V2_CURRENT_LOCATION['tagline'])): ?>
        <p class="gm-hero__description"><?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['tagline']); ?></p>
        <?php endif; ?>
        <div class="gm-hero-tags" aria-hidden="true">
          <span class="gm-hero-tag">✈️ 城市飛行</span>
          <span class="gm-hero-tag">🏞️ 自然祕境</span>
          <span class="gm-hero-tag">🍽️ 旅途美食</span>
        </div>
        <div class="gm-hero__meta">
          <form method="get" class="gm-select">
            <label for="loc">選擇地點</label>
            <select id="loc" name="loc" onchange="this.form.submit()">
              <?php foreach ($GM_V2_LOCATION_IDS as $locId): ?>
              <option value="<?php echo gm_v2_escape($locId); ?>" <?php echo $locId === $GM_V2_CURRENT_LOCATION_ID ? 'selected' : ''; ?>><?php echo gm_v2_escape($GM_V2_LOCATIONS[$locId]['name'] ?? $locId); ?></option>
              <?php endforeach; ?>
            </select>
            <noscript>
              <button type="submit" class="gm-button is-outline">切換</button>
            </noscript>
          </form>
          <div class="gm-summary-list">
            <span class="gm-summary-item"><span class="gm-summary-icon" aria-hidden="true">🗒️</span>日誌 <strong><?php echo gm_v2_count($GM_V2_CURRENT_LOCATION['diaries'] ?? []); ?></strong> 篇</span>
            <span class="gm-summary-item"><span class="gm-summary-icon" aria-hidden="true">📸</span>相片 <strong><?php echo gm_v2_count($GM_V2_CURRENT_LOCATION['photos'] ?? []); ?></strong> 張</span>
            <span class="gm-summary-item"><span class="gm-summary-icon" aria-hidden="true">🎞️</span>影音 <strong><?php echo gm_v2_count($GM_V2_CURRENT_LOCATION['videos'] ?? []); ?></strong> 筆</span>
          </div>
        </div>
        <p class="gm-hero__description"><?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['description'] ?? ''); ?></p>
        <?php if (!empty($GM_V2_CURRENT_LOCATION['highlights'])): ?>
        <ul class="gm-highlight-list">
          <?php foreach ($GM_V2_CURRENT_LOCATION['highlights'] as $highlight): ?>
          <?php $content = is_array($highlight) ? ($highlight['content'] ?? '') : (string) $highlight; ?>
          <?php if ($content === '') { continue; } ?>
          <li><?php echo gm_v2_escape($content); ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <?php if (!empty($GM_V2_CURRENT_LOCATION['mapUrl'])): ?>
        <a class="gm-map-link" href="<?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['mapUrl']); ?>" target="_blank" rel="noopener"><span aria-hidden="true">🗺️</span>查看路線地圖</a>
        <?php endif; ?>
      </div>
      <div class="gm-hero__art">
        <span class="gm-hero__glow" aria-hidden="true"></span>
        <?php if (!empty($GM_V2_CURRENT_LOCATION['cover'])): ?>
        <div class="gm-hero__image">
          <img src="<?php echo gm_v2_escape($GM_V2_CURRENT_LOCATION['cover']); ?>" alt="<?php echo gm_v2_escape(($GM_V2_CURRENT_LOCATION['name'] ?? '') . ' 封面'); ?>">
        </div>
        <?php endif; ?>
        <span class="gm-floating-icon gm-floating-icon--plane" aria-hidden="true">✈️</span>
        <span class="gm-floating-icon gm-floating-icon--camera" aria-hidden="true">📷</span>
        <span class="gm-floating-icon gm-floating-icon--pin" aria-hidden="true">📍</span>
      </div>
    </section>

    <section class="gm-section gm-section--accent">
      <div class="gm-section__header">
        <h2 class="gm-section__title">功能捷徑</h2>
        <p class="gm-section__subtitle">導覽資料庫完整收錄旅遊日誌、相片集與影音花絮，資訊即時同步。</p>
      </div>
      <div class="gm-grid gm-grid--thirds">
        <article class="gm-card gm-category-card">
          <span class="gm-category-card__icon" aria-hidden="true">🗺️</span>
          <div>
            <h3>旅遊日誌</h3>
            <p>以圖文並茂的方式呈現行程亮點，支援段落、列表與標註。</p>
          </div>
          <a class="gm-button" href="<?php echo gm_v2_nav_url('diary.php'); ?>">前往日誌<span class="gm-button__icon" aria-hidden="true">↗</span></a>
        </article>
        <article class="gm-card gm-category-card">
          <span class="gm-category-card__icon" aria-hidden="true">📸</span>
          <div>
            <h3>旅途相片</h3>
            <p>高解析度相片記錄旅程精華，提供完整說明與攝影資訊。</p>
          </div>
          <a class="gm-button" href="<?php echo gm_v2_nav_url('photos.php'); ?>">查看相片<span class="gm-button__icon" aria-hidden="true">↗</span></a>
        </article>
        <article class="gm-card gm-category-card">
          <span class="gm-category-card__icon" aria-hidden="true">🎬</span>
          <div>
            <h3>影音花絮</h3>
            <p>多媒體花絮以動態影音呈現導覽片段，帶來沉浸式體驗。</p>
          </div>
          <a class="gm-button" href="<?php echo gm_v2_nav_url('videos.php'); ?>">播放花絮<span class="gm-button__icon" aria-hidden="true">↗</span></a>
        </article>
      </div>
    </section>
    <?php else: ?>
    <section class="gm-empty">
      尚未設定任何地點資料，請確認資料庫內容或執行 <code>php database/seed.php</code>。
    </section>
    <?php endif; ?>
  </div>
</main>
<?php include __DIR__ . '/partials/footer.php'; ?>
