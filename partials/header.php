<?php
/** @var array $GM_V2_LOCATION_IDS */
/** @var string|null $GM_V2_CURRENT_LOCATION_ID */
/** @var array|null $GM_V2_CURRENT_LOCATION */
?>
<header class="gm-nav">
  <div class="gm-container gm-nav__inner">
    <a href="<?php echo gm_v2_nav_url('index.php'); ?>" class="gm-logo">Guide Magnets v2</a>
    <nav class="gm-nav-links">
      <a href="<?php echo gm_v2_nav_url('index.php'); ?>" class="gm-nav-link<?php echo gm_v2_is_active('index.php') ? ' is-active' : ''; ?>">總覽</a>
      <a href="<?php echo gm_v2_nav_url('diary.php'); ?>" class="gm-nav-link<?php echo gm_v2_is_active('diary.php') ? ' is-active' : ''; ?>">旅遊日誌</a>
      <a href="<?php echo gm_v2_nav_url('photos.php'); ?>" class="gm-nav-link<?php echo gm_v2_is_active('photos.php') ? ' is-active' : ''; ?>">旅途相片</a>
      <a href="<?php echo gm_v2_nav_url('videos.php'); ?>" class="gm-nav-link<?php echo gm_v2_is_active('videos.php') ? ' is-active' : ''; ?>">影音花絮</a>
    </nav>
  </div>
</header>
