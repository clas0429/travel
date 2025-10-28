<?php
require __DIR__ . '/../includes/admin.php';

$user = gm_admin_require_login();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $redirectLoc = $_POST['location_id'] ?? ($_POST['id'] ?? null);

    try {
        switch ($action) {
            case 'create_location':
                $locationId = trim($_POST['id'] ?? '');
                $name = trim($_POST['name'] ?? '');
                if ($locationId === '' || $name === '') {
                    throw new InvalidArgumentException('地點代碼與名稱不可空白。');
                }

                if (!preg_match('/^[A-Z0-9_-]+$/i', $locationId)) {
                    throw new InvalidArgumentException('地點代碼僅能使用英文、數字、底線或減號。');
                }

                $data = [
                    'id' => $locationId,
                    'name' => $name,
                    'tagline' => trim($_POST['tagline'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                    'mapUrl' => trim($_POST['mapUrl'] ?? ''),
                    'sortOrder' => (int) ($_POST['sort_order'] ?? 0),
                ];

                if (!empty($_FILES['cover']['tmp_name'])) {
                    [$coverPath] = gm_admin_store_upload(
                        $_FILES['cover'],
                        'covers',
                        ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml']
                    );
                    $data['cover'] = $coverPath;
                } elseif (isset($_POST['cover_path']) && $_POST['cover_path'] !== '') {
                    $data['cover'] = trim($_POST['cover_path']);
                }

                gm_v2_create_location($data);
                gm_admin_flash('success', '已新增地點：' . $data['name']);
                $redirectLoc = $locationId;
                break;

            case 'update_location':
                $locationId = trim($_POST['id'] ?? '');
                if ($locationId === '') {
                    throw new InvalidArgumentException('缺少地點代碼。');
                }

                $data = [
                    'name' => trim($_POST['name'] ?? ''),
                    'tagline' => trim($_POST['tagline'] ?? ''),
                    'description' => trim($_POST['description'] ?? ''),
                    'mapUrl' => trim($_POST['mapUrl'] ?? ''),
                    'sortOrder' => (int) ($_POST['sort_order'] ?? 0),
                ];

                if ($data['name'] === '') {
                    throw new InvalidArgumentException('地點名稱不可空白。');
                }

                if (!empty($_FILES['cover']['tmp_name'])) {
                    [$coverPath] = gm_admin_store_upload(
                        $_FILES['cover'],
                        'covers',
                        ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml']
                    );
                    $data['cover'] = $coverPath;
                } elseif (isset($_POST['cover_path']) && $_POST['cover_path'] !== '') {
                    $data['cover'] = trim($_POST['cover_path']);
                }

                gm_v2_update_location($locationId, $data);
                gm_admin_flash('success', '地點資料已更新。');
                $redirectLoc = $locationId;
                break;

            case 'delete_location':
                $locationId = trim($_POST['id'] ?? '');
                if ($locationId === '') {
                    throw new InvalidArgumentException('缺少要刪除的地點代碼。');
                }

                if (gm_v2_delete_location($locationId)) {
                    gm_admin_flash('success', '已刪除地點 ' . $locationId . '。');
                } else {
                    gm_admin_flash('error', '找不到指定的地點。');
                }

                $redirectLoc = null;
                break;

            case 'create_highlight':
                $locationId = trim($_POST['location_id'] ?? '');
                $content = trim($_POST['content'] ?? '');
                if ($locationId === '' || $content === '') {
                    throw new InvalidArgumentException('地點與亮點內容不可空白。');
                }

                gm_v2_create_highlight($locationId, $content, (int) ($_POST['sort_order'] ?? 0));
                gm_admin_flash('success', '已新增亮點。');
                $redirectLoc = $locationId;
                break;

            case 'delete_highlight':
                $highlightId = (int) ($_POST['highlight_id'] ?? 0);
                $locationId = trim($_POST['location_id'] ?? '');
                if ($highlightId <= 0) {
                    throw new InvalidArgumentException('缺少亮點編號。');
                }

                if (gm_v2_delete_highlight($highlightId)) {
                    gm_admin_flash('success', '亮點已刪除。');
                } else {
                    gm_admin_flash('error', '找不到要刪除的亮點。');
                }
                $redirectLoc = $locationId;
                break;

            case 'create_diary':
                $locationId = trim($_POST['location_id'] ?? '');
                $title = trim($_POST['title'] ?? '');
                if ($locationId === '' || $title === '') {
                    throw new InvalidArgumentException('地點與日誌標題不可空白。');
                }

                $data = [
                    'slug' => trim($_POST['slug'] ?? '') ?: null,
                    'title' => $title,
                    'content' => $_POST['content'] ?? null,
                    'createdAt' => gm_admin_normalize_datetime($_POST['published_at'] ?? null),
                    'sortOrder' => (int) ($_POST['sort_order'] ?? 0),
                ];

                gm_v2_create_diary($locationId, $data);
                gm_admin_flash('success', '已新增旅遊日誌。');
                $redirectLoc = $locationId;
                break;

            case 'delete_diary':
                $diaryId = (int) ($_POST['diary_id'] ?? 0);
                $locationId = trim($_POST['location_id'] ?? '');
                if ($diaryId <= 0) {
                    throw new InvalidArgumentException('缺少日誌編號。');
                }

                if (gm_v2_delete_diary($diaryId)) {
                    gm_admin_flash('success', '日誌已刪除。');
                } else {
                    gm_admin_flash('error', '找不到要刪除的日誌。');
                }
                $redirectLoc = $locationId;
                break;

            case 'create_photo':
                $locationId = trim($_POST['location_id'] ?? '');
                $title = trim($_POST['title'] ?? '');
                if ($locationId === '' || $title === '') {
                    throw new InvalidArgumentException('地點與相片標題不可空白。');
                }

                $imagePath = trim($_POST['image_path'] ?? '');
                if (!empty($_FILES['image_file']['tmp_name'])) {
                    [$imagePath] = gm_admin_store_upload(
                        $_FILES['image_file'],
                        'photos',
                        ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml']
                    );
                }

                if ($imagePath === '') {
                    throw new InvalidArgumentException('請上傳或填入相片路徑。');
                }

                $data = [
                    'slug' => trim($_POST['slug'] ?? '') ?: null,
                    'title' => $title,
                    'description' => trim($_POST['description'] ?? ''),
                    'image' => $imagePath,
                    'attribution' => trim($_POST['attribution'] ?? ''),
                    'sortOrder' => (int) ($_POST['sort_order'] ?? 0),
                ];

                gm_v2_create_photo($locationId, $data);
                gm_admin_flash('success', '已新增相片。');
                $redirectLoc = $locationId;
                break;

            case 'delete_photo':
                $photoId = (int) ($_POST['photo_id'] ?? 0);
                $locationId = trim($_POST['location_id'] ?? '');
                if ($photoId <= 0) {
                    throw new InvalidArgumentException('缺少相片編號。');
                }

                if (gm_v2_delete_photo($photoId)) {
                    gm_admin_flash('success', '相片已刪除。');
                } else {
                    gm_admin_flash('error', '找不到要刪除的相片。');
                }
                $redirectLoc = $locationId;
                break;

            case 'create_video':
                $locationId = trim($_POST['location_id'] ?? '');
                $title = trim($_POST['title'] ?? '');
                $type = $_POST['type'] ?? 'inlineSvg';
                if ($locationId === '' || $title === '') {
                    throw new InvalidArgumentException('地點與影音標題不可空白。');
                }

                $data = [
                    'slug' => trim($_POST['slug'] ?? '') ?: null,
                    'title' => $title,
                    'description' => trim($_POST['description'] ?? ''),
                    'type' => $type,
                    'sortOrder' => (int) ($_POST['sort_order'] ?? 0),
                ];

                if ($type === 'inlineSvg') {
                    $svg = trim($_POST['svg_content'] ?? '');
                    if ($svg === '') {
                        throw new InvalidArgumentException('請提供 SVG 內容。');
                    }
                    $data['svgContent'] = $svg;
                } elseif ($type === 'localVideo') {
                    $videoPath = trim($_POST['video_path'] ?? '');
                    $mime = null;
                    if (!empty($_FILES['video_file']['tmp_name'])) {
                        [$videoPath, $mime] = gm_admin_store_upload(
                            $_FILES['video_file'],
                            'videos',
                            ['video/mp4', 'video/webm', 'video/ogg']
                        );
                    }

                    if ($videoPath === '') {
                        throw new InvalidArgumentException('請上傳或填入影片檔案路徑。');
                    }

                    $data['url'] = $videoPath;
                    if ($mime) {
                        $data['mime'] = $mime;
                    } elseif (!empty($_POST['mime_type'])) {
                        $data['mime'] = trim($_POST['mime_type']);
                    }
                } else {
                    $embed = trim($_POST['embed_url'] ?? '');
                    if ($embed === '') {
                        throw new InvalidArgumentException('請提供影音嵌入網址。');
                    }
                    $data['url'] = $embed;
                    $data['type'] = 'external';
                }

                $posterPath = trim($_POST['poster_path'] ?? '');
                if (!empty($_FILES['poster_file']['tmp_name'])) {
                    [$posterPath] = gm_admin_store_upload(
                        $_FILES['poster_file'],
                        'videos',
                        ['image/jpeg', 'image/png', 'image/webp', 'image/gif', 'image/svg+xml']
                    );
                }

                if ($posterPath !== '') {
                    $data['poster'] = $posterPath;
                }

                gm_v2_create_video($locationId, $data);
                gm_admin_flash('success', '已新增影音內容。');
                $redirectLoc = $locationId;
                break;

            case 'delete_video':
                $videoId = (int) ($_POST['video_id'] ?? 0);
                $locationId = trim($_POST['location_id'] ?? '');
                if ($videoId <= 0) {
                    throw new InvalidArgumentException('缺少影音編號。');
                }

                if (gm_v2_delete_video($videoId)) {
                    gm_admin_flash('success', '影音內容已刪除。');
                } else {
                    gm_admin_flash('error', '找不到要刪除的影音內容。');
                }
                $redirectLoc = $locationId;
                break;

            default:
                gm_admin_flash('error', '未支援的操作。');
                break;
        }
    } catch (Throwable $e) {
        gm_admin_flash('error', $e->getMessage());
    }

    $query = $redirectLoc ? ('?loc=' . urlencode($redirectLoc)) : '';
    header('Location: index.php' . $query);
    exit;
}

$messages = gm_admin_get_flash();
$locations = gm_v2_locations(true);
$locationIds = array_keys($locations);
$currentLocationId = $_GET['loc'] ?? ($locationIds[0] ?? null);
$currentLocation = $currentLocationId && isset($locations[$currentLocationId])
    ? $locations[$currentLocationId]
    : null;
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Guide Magnets 後台</title>
  <link rel="stylesheet" href="../assets/css/local-tailwind.css">
  <link rel="stylesheet" href="../assets/css/tokens.css">
  <style>
    body { background: var(--surface); color: var(--ink); font-family: 'Noto Sans TC', system-ui, sans-serif; }
    .admin-container { max-width: 1080px; margin: 0 auto; padding: 2.5rem 1.5rem 4rem; display: flex; flex-direction: column; gap: 2rem; }
    h1 { font-size: 2rem; font-weight: 700; }
    h2 { font-size: 1.4rem; font-weight: 600; margin-top: 1.5rem; }
    form { display: grid; gap: 1rem; background: rgba(255,255,255,0.92); padding: 1.5rem; border-radius: 1rem; box-shadow: 0 18px 48px -24px rgba(15,23,42,0.22); }
    fieldset { border: 0; padding: 0; margin: 0; display: grid; gap: 1rem; }
    label { display: grid; gap: 0.35rem; font-size: 0.95rem; }
    input[type="text"], input[type="number"], input[type="datetime-local"], textarea, select {
      padding: 0.65rem 0.8rem; border: 1px solid rgba(15,23,42,0.18); border-radius: 0.75rem; background: white; font-size: 0.95rem;
    }
    textarea { min-height: 160px; }
    .button-row { display: flex; gap: 0.75rem; }
    button, .link-button {
      display: inline-flex; align-items: center; justify-content: center; gap: 0.4rem; padding: 0.65rem 1.4rem; border-radius: 999px;
      border: none; background: var(--accent); color: white; font-weight: 600; cursor: pointer; text-decoration: none;
    }
    button.is-danger { background: #dc2626; }
    .flash { padding: 0.9rem 1.1rem; border-radius: 0.9rem; font-size: 0.95rem; }
    .flash.success { background: rgba(34,197,94,0.18); color: #166534; }
    .flash.error { background: rgba(248,113,113,0.18); color: #7f1d1d; }
    table { width: 100%; border-collapse: collapse; background: rgba(255,255,255,0.92); border-radius: 1rem; overflow: hidden; }
    th, td { padding: 0.75rem 1rem; border-bottom: 1px solid rgba(15,23,42,0.12); text-align: left; }
    th { background: rgba(15,23,42,0.05); font-weight: 600; }
    tr:last-child td { border-bottom: none; }
    .section-card { background: rgba(255,255,255,0.92); padding: 1.5rem; border-radius: 1.25rem; box-shadow: 0 18px 48px -24px rgba(15,23,42,0.18); display: grid; gap: 1.25rem; }
    .section-card header { display: flex; flex-direction: column; gap: 0.35rem; }
    .stack { display: grid; gap: 1.5rem; }
    .nav-bar { display: flex; justify-content: space-between; align-items: center; gap: 1rem; }
    .nav-bar a { color: var(--accent); text-decoration: none; font-weight: 600; }
    .item-actions { display: flex; gap: 0.5rem; }
    .muted { color: rgba(15,23,42,0.65); font-size: 0.9rem; }
    .inline-form { display: inline-block; margin: 0; }
    .inline-form button { padding: 0.45rem 1rem; font-size: 0.85rem; }
  </style>
</head>
<body>
  <div class="admin-container">
    <div class="nav-bar">
      <h1>Guide Magnets 後台管理</h1>
      <div class="button-row">
        <a class="link-button" href="../index.php">返回前台</a>
        <form class="inline-form" method="post" action="logout.php">
          <button type="submit" class="is-danger">登出</button>
        </form>
      </div>
    </div>

    <?php foreach ($messages as $message): ?>
    <div class="flash <?php echo gm_v2_escape($message['type']); ?>"><?php echo gm_v2_escape($message['message']); ?></div>
    <?php endforeach; ?>

    <section class="section-card">
      <header>
        <h2>新增地點</h2>
        <p class="muted">建立新的旅遊地點，並可立即加入亮點、日誌或媒體內容。</p>
      </header>
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="action" value="create_location">
        <fieldset>
          <label>地點代碼
            <input type="text" name="id" placeholder="例如：TAIPEI101" required>
          </label>
          <label>地點名稱
            <input type="text" name="name" required>
          </label>
          <label>標語 / Tagline
            <input type="text" name="tagline" placeholder="一句話描述">
          </label>
          <label>描述
            <textarea name="description" placeholder="地點詳細介紹"></textarea>
          </label>
          <label>地圖網址
            <input type="text" name="mapUrl" placeholder="https://maps.google.com/...">
          </label>
          <label>排序值
            <input type="number" name="sort_order" value="0">
          </label>
          <label>封面圖片上傳
            <input type="file" name="cover" accept="image/*">
          </label>
          <label>或使用現有封面路徑
            <input type="text" name="cover_path" placeholder="assets/images/v2/taipei-cover.svg 或 uploads/covers/...">
          </label>
        </fieldset>
        <div class="button-row">
          <button type="submit">新增地點</button>
        </div>
      </form>
    </section>

    <section class="section-card">
      <header>
        <h2>地點管理</h2>
        <p class="muted">選擇地點後即可維護亮點、日誌、相片與影音內容。</p>
      </header>
      <form method="get" class="stack" style="grid-template-columns:minmax(0,1fr);">
        <label>選擇地點
          <select name="loc" onchange="this.form.submit()">
            <?php foreach ($locationIds as $locId): ?>
            <option value="<?php echo gm_v2_escape($locId); ?>" <?php echo $locId === $currentLocationId ? 'selected' : ''; ?>>
              <?php echo gm_v2_escape($locations[$locId]['name'] ?? $locId); ?>
            </option>
            <?php endforeach; ?>
          </select>
        </label>
        <noscript>
          <div class="button-row"><button type="submit">切換地點</button></div>
        </noscript>
      </form>

      <?php if ($currentLocation): ?>
      <article class="stack">
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="update_location">
          <input type="hidden" name="id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
          <fieldset>
            <label>地點名稱
              <input type="text" name="name" value="<?php echo gm_v2_escape($currentLocation['name'] ?? ''); ?>" required>
            </label>
            <label>標語 / Tagline
              <input type="text" name="tagline" value="<?php echo gm_v2_escape($currentLocation['tagline'] ?? ''); ?>">
            </label>
            <label>描述
              <textarea name="description"><?php echo gm_v2_escape($currentLocation['description'] ?? ''); ?></textarea>
            </label>
            <label>地圖網址
              <input type="text" name="mapUrl" value="<?php echo gm_v2_escape($currentLocation['mapUrl'] ?? ''); ?>">
            </label>
            <label>排序值
              <input type="number" name="sort_order" value="<?php echo gm_v2_escape((string)($currentLocation['sortOrder'] ?? 0)); ?>">
            </label>
            <label>替換封面圖片
              <input type="file" name="cover" accept="image/*">
            </label>
            <label>或設定封面路徑
              <input type="text" name="cover_path" value="<?php echo gm_v2_escape($currentLocation['cover'] ?? ''); ?>">
            </label>
          </fieldset>
          <div class="button-row">
            <button type="submit">更新地點資訊</button>
          </div>
        </form>
        <form method="post" class="inline-form" onsubmit="return confirm('確定要刪除此地點？相關內容將一併刪除。');">
          <input type="hidden" name="action" value="delete_location">
          <input type="hidden" name="id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
          <button type="submit" class="is-danger">刪除地點</button>
        </form>
      </article>

      <article class="stack">
        <header>
          <h3>亮點</h3>
          <p class="muted">目前共有 <?php echo gm_v2_escape((string) gm_v2_count($currentLocation['highlights'] ?? [])); ?> 筆亮點。</p>
        </header>
        <table>
          <thead>
            <tr>
              <th>內容</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($currentLocation['highlights'])): ?>
              <?php foreach ($currentLocation['highlights'] as $highlight): ?>
              <?php $content = is_array($highlight) ? ($highlight['content'] ?? '') : (string) $highlight; ?>
              <tr>
                <td><?php echo gm_v2_escape($content); ?></td>
                <td>
                  <?php if (is_array($highlight) && !empty($highlight['id'])): ?>
                  <form method="post" class="inline-form" onsubmit="return confirm('確定要刪除此亮點？');">
                    <input type="hidden" name="action" value="delete_highlight">
                    <input type="hidden" name="highlight_id" value="<?php echo gm_v2_escape((string) $highlight['id']); ?>">
                    <input type="hidden" name="location_id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
                    <button type="submit" class="is-danger">刪除</button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="2">尚未建立亮點。</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <form method="post">
          <input type="hidden" name="action" value="create_highlight">
          <input type="hidden" name="location_id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
          <fieldset>
            <label>亮點內容
              <textarea name="content" placeholder="輸入亮點敘述" required></textarea>
            </label>
            <label>排序值
              <input type="number" name="sort_order" value="0">
            </label>
          </fieldset>
          <div class="button-row">
            <button type="submit">新增亮點</button>
          </div>
        </form>
      </article>

      <article class="stack">
        <header>
          <h3>旅遊日誌</h3>
          <p class="muted">共有 <?php echo gm_v2_escape((string) gm_v2_count($currentLocation['diaries'] ?? [])); ?> 篇。</p>
        </header>
        <table>
          <thead>
            <tr>
              <th>標題</th>
              <th>建立時間</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($currentLocation['diaries'])): ?>
              <?php foreach ($currentLocation['diaries'] as $diary): ?>
              <tr>
                <td><?php echo gm_v2_escape($diary['title'] ?? ''); ?></td>
                <td><?php echo gm_v2_escape(gm_v2_format_date($diary['createdAt'] ?? null) ?? ''); ?></td>
                <td>
                  <?php if (!empty($diary['recordId'])): ?>
                  <form method="post" class="inline-form" onsubmit="return confirm('確定要刪除這篇日誌？');">
                    <input type="hidden" name="action" value="delete_diary">
                    <input type="hidden" name="diary_id" value="<?php echo gm_v2_escape((string) $diary['recordId']); ?>">
                    <input type="hidden" name="location_id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
                    <button type="submit" class="is-danger">刪除</button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="3">尚未建立旅遊日誌。</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <form method="post">
          <input type="hidden" name="action" value="create_diary">
          <input type="hidden" name="location_id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
          <fieldset>
            <label>標題
              <input type="text" name="title" required>
            </label>
            <label>Slug（選填，URL 友善代號）
              <input type="text" name="slug" placeholder="例如：day-1-arrival">
            </label>
            <label>建立時間
              <input type="datetime-local" name="published_at">
            </label>
            <label>排序值
              <input type="number" name="sort_order" value="0">
            </label>
            <label>內容（支援 HTML）
              <textarea name="content" placeholder="可貼上段落或列表 HTML"></textarea>
            </label>
          </fieldset>
          <div class="button-row">
            <button type="submit">新增日誌</button>
          </div>
        </form>
      </article>

      <article class="stack">
        <header>
          <h3>相片</h3>
          <p class="muted">共有 <?php echo gm_v2_escape((string) gm_v2_count($currentLocation['photos'] ?? [])); ?> 張。</p>
        </header>
        <table>
          <thead>
            <tr>
              <th>標題</th>
              <th>圖片</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($currentLocation['photos'])): ?>
              <?php foreach ($currentLocation['photos'] as $photo): ?>
              <tr>
                <td><?php echo gm_v2_escape($photo['title'] ?? ''); ?></td>
                <td><?php echo gm_v2_escape($photo['image'] ?? ''); ?></td>
                <td>
                  <?php if (!empty($photo['recordId'])): ?>
                  <form method="post" class="inline-form" onsubmit="return confirm('確定要刪除這張相片？');">
                    <input type="hidden" name="action" value="delete_photo">
                    <input type="hidden" name="photo_id" value="<?php echo gm_v2_escape((string) $photo['recordId']); ?>">
                    <input type="hidden" name="location_id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
                    <button type="submit" class="is-danger">刪除</button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="3">尚未新增相片。</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="create_photo">
          <input type="hidden" name="location_id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
          <fieldset>
            <label>標題
              <input type="text" name="title" required>
            </label>
            <label>Slug（選填）
              <input type="text" name="slug" placeholder="sunset-view">
            </label>
            <label>描述
              <textarea name="description"></textarea>
            </label>
            <label>攝影 / 來源註記
              <input type="text" name="attribution">
            </label>
            <label>排序值
              <input type="number" name="sort_order" value="0">
            </label>
            <label>上傳圖片
              <input type="file" name="image_file" accept="image/*">
            </label>
            <label>或使用既有圖片路徑
              <input type="text" name="image_path" placeholder="assets/images/... 或 uploads/photos/...">
            </label>
          </fieldset>
          <div class="button-row">
            <button type="submit">新增相片</button>
          </div>
        </form>
      </article>

      <article class="stack">
        <header>
          <h3>影音內容</h3>
          <p class="muted">共有 <?php echo gm_v2_escape((string) gm_v2_count($currentLocation['videos'] ?? [])); ?> 筆。</p>
        </header>
        <table>
          <thead>
            <tr>
              <th>標題</th>
              <th>類型</th>
              <th>來源</th>
              <th>操作</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($currentLocation['videos'])): ?>
              <?php foreach ($currentLocation['videos'] as $video): ?>
              <tr>
                <td><?php echo gm_v2_escape($video['title'] ?? ''); ?></td>
                <td><?php echo gm_v2_escape($video['type'] ?? 'inlineSvg'); ?></td>
                <td>
                  <?php if (($video['type'] ?? '') === 'inlineSvg'): ?>SVG 內嵌<?php elseif (($video['type'] ?? '') === 'localVideo'): ?><?php echo gm_v2_escape($video['source'] ?? ''); ?><?php else: ?><?php echo gm_v2_escape($video['url'] ?? ''); ?><?php endif; ?>
                </td>
                <td>
                  <?php if (!empty($video['recordId'])): ?>
                  <form method="post" class="inline-form" onsubmit="return confirm('確定要刪除此影音？');">
                    <input type="hidden" name="action" value="delete_video">
                    <input type="hidden" name="video_id" value="<?php echo gm_v2_escape((string) $video['recordId']); ?>">
                    <input type="hidden" name="location_id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
                    <button type="submit" class="is-danger">刪除</button>
                  </form>
                  <?php endif; ?>
                </td>
              </tr>
              <?php endforeach; ?>
            <?php else: ?>
            <tr><td colspan="4">尚未新增影音內容。</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
        <form method="post" enctype="multipart/form-data">
          <input type="hidden" name="action" value="create_video">
          <input type="hidden" name="location_id" value="<?php echo gm_v2_escape($currentLocationId); ?>">
          <fieldset>
            <label>標題
              <input type="text" name="title" required>
            </label>
            <label>Slug（選填）
              <input type="text" name="slug">
            </label>
            <label>描述
              <textarea name="description"></textarea>
            </label>
            <label>排序值
              <input type="number" name="sort_order" value="0">
            </label>
            <label>影音類型
              <select name="type">
                <option value="inlineSvg">SVG 動畫</option>
                <option value="localVideo">上傳影片檔案</option>
                <option value="external">外部嵌入（YouTube、Vimeo 等）</option>
              </select>
            </label>
            <label>外部嵌入網址（僅限外部嵌入類型）
              <input type="text" name="embed_url" placeholder="https://www.youtube.com/embed/...">
            </label>
            <label>SVG 內容（僅限 SVG 類型）
              <textarea name="svg_content" placeholder="貼上 SVG 程式碼"></textarea>
            </label>
            <label>上傳影片檔案（僅限影片類型）
              <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg">
            </label>
            <label>或使用既有影片路徑
              <input type="text" name="video_path" placeholder="uploads/videos/...">
            </label>
            <label>影片 MIME 類型（選填）
              <input type="text" name="mime_type" placeholder="video/mp4">
            </label>
            <label>上傳海報圖片（選填）
              <input type="file" name="poster_file" accept="image/*">
            </label>
            <label>或使用既有海報路徑
              <input type="text" name="poster_path" placeholder="assets/images/... 或 uploads/videos/...">
            </label>
          </fieldset>
          <div class="button-row">
            <button type="submit">新增影音</button>
          </div>
        </form>
      </article>
      <?php else: ?>
      <p class="muted">目前尚未有任何地點，可先於上方建立。</p>
      <?php endif; ?>
    </section>
  </div>
</body>
</html>
