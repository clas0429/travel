<?php

function gm_v2_locations(bool $forceRefresh = false): array
{
    static $locations = null;

    if ($forceRefresh || $locations === null) {
        $locations = gm_v2_fetch_locations_from_db();
    }

    return $locations;
}

function gm_v2_location_ids(): array
{
    return array_keys(gm_v2_locations());
}

function gm_v2_default_location_id(): ?string
{
    $ids = gm_v2_location_ids();
    return $ids[0] ?? null;
}

function gm_v2_requested_location_id(): ?string
{
    $requested = $_GET['loc'] ?? null;
    $locations = gm_v2_locations();
    if ($requested && isset($locations[$requested])) {
        return $requested;
    }
    return gm_v2_default_location_id();
}

function gm_v2_current_location(): ?array
{
    $id = gm_v2_requested_location_id();
    if (!$id) {
        return null;
    }
    $locations = gm_v2_locations();
    return $locations[$id] ?? null;
}

function gm_v2_escape(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function gm_v2_format_date(?string $value): ?string
{
    if (!$value) {
        return null;
    }
    try {
        $date = new DateTime($value);
        return $date->format('Y-m-d H:i');
    } catch (Throwable $e) {
        return $value;
    }
}

function gm_v2_build_url(string $path, array $params = []): string
{
    $query = http_build_query($params);
    return $query ? $path . '?' . $query : $path;
}

function gm_v2_nav_url(string $path, array $extra = []): string
{
    $params = $extra;
    $loc = gm_v2_requested_location_id();
    if ($loc) {
        $params['loc'] = $loc;
    }
    return gm_v2_build_url($path, $params);
}

function gm_v2_is_active(string $path): bool
{
    $current = $_SERVER['SCRIPT_NAME'] ?? '';
    $currentBase = basename($current);
    $targetBase = basename($path);
    return $currentBase === $targetBase;
}

function gm_v2_count(array $items): int
{
    return count($items);
}

function gm_v2_fetch_locations_from_db(): array
{
    $pdo = gm_v2_db();

    $locations = gm_v2_fetch_locations_with_relations($pdo);
    if (!empty($locations)) {
        return $locations;
    }

    if (gm_v2_seed_database_from_local_config($pdo) > 0) {
        return gm_v2_fetch_locations_with_relations($pdo);
    }

    return [];
}

function gm_v2_create_location(array $data): string
{
    if (empty($data['id']) || empty($data['name'])) {
        throw new InvalidArgumentException('location 需要提供 id 與 name');
    }

    $pdo = gm_v2_db();
    $sql = 'INSERT INTO locations (id, name, tagline, description, map_url, cover, sort_order) VALUES (:id, :name, :tagline, :description, :map_url, :cover, :sort_order)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $data['id'],
        ':name' => $data['name'],
        ':tagline' => $data['tagline'] ?? null,
        ':description' => $data['description'] ?? null,
        ':map_url' => $data['mapUrl'] ?? null,
        ':cover' => $data['cover'] ?? null,
        ':sort_order' => isset($data['sortOrder']) ? (int) $data['sortOrder'] : 0,
    ]);

    gm_v2_locations(true);

    return $data['id'];
}

function gm_v2_update_location(string $id, array $data): bool
{
    if (empty($data['name'])) {
        throw new InvalidArgumentException('location 更新需要提供 name');
    }

    $pdo = gm_v2_db();
    $sql = 'UPDATE locations SET name = :name, tagline = :tagline, description = :description, map_url = :map_url, cover = :cover, sort_order = :sort_order WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':id' => $id,
        ':name' => $data['name'],
        ':tagline' => $data['tagline'] ?? null,
        ':description' => $data['description'] ?? null,
        ':map_url' => $data['mapUrl'] ?? null,
        ':cover' => $data['cover'] ?? null,
        ':sort_order' => isset($data['sortOrder']) ? (int) $data['sortOrder'] : 0,
    ]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_delete_location(string $id): bool
{
    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('DELETE FROM locations WHERE id = :id');
    $stmt->execute([':id' => $id]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_create_highlight(string $locationId, string $content, int $sortOrder = 0): int
{
    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('INSERT INTO location_highlights (location_id, content, sort_order) VALUES (:location_id, :content, :sort_order)');
    $stmt->execute([
        ':location_id' => $locationId,
        ':content' => $content,
        ':sort_order' => $sortOrder,
    ]);

    gm_v2_locations(true);

    return (int) $pdo->lastInsertId();
}

function gm_v2_update_highlight(int $highlightId, array $data): bool
{
    if (!array_key_exists('content', $data)) {
        throw new InvalidArgumentException('highlight 更新需要提供 content');
    }

    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('UPDATE location_highlights SET content = :content, sort_order = :sort_order WHERE id = :id');
    $stmt->execute([
        ':id' => $highlightId,
        ':content' => $data['content'],
        ':sort_order' => isset($data['sortOrder']) ? (int) $data['sortOrder'] : 0,
    ]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_delete_highlight(int $highlightId): bool
{
    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('DELETE FROM location_highlights WHERE id = :id');
    $stmt->execute([':id' => $highlightId]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_create_diary(string $locationId, array $data): int
{
    if (empty($data['title'])) {
        throw new InvalidArgumentException('diary 需要提供 title');
    }

    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('INSERT INTO diaries (location_id, slug, title, content, published_at, sort_order) VALUES (:location_id, :slug, :title, :content, :published_at, :sort_order)');
    $stmt->execute([
        ':location_id' => $locationId,
        ':slug' => $data['slug'] ?? null,
        ':title' => $data['title'],
        ':content' => $data['content'] ?? null,
        ':published_at' => $data['createdAt'] ?? null,
        ':sort_order' => isset($data['sortOrder']) ? (int) $data['sortOrder'] : 0,
    ]);

    gm_v2_locations(true);

    return (int) $pdo->lastInsertId();
}

function gm_v2_update_diary(int $diaryId, array $data): bool
{
    if (empty($data['title'])) {
        throw new InvalidArgumentException('diary 更新需要提供 title');
    }

    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('UPDATE diaries SET slug = :slug, title = :title, content = :content, published_at = :published_at, sort_order = :sort_order WHERE id = :id');
    $stmt->execute([
        ':id' => $diaryId,
        ':slug' => $data['slug'] ?? null,
        ':title' => $data['title'],
        ':content' => $data['content'] ?? null,
        ':published_at' => $data['createdAt'] ?? null,
        ':sort_order' => isset($data['sortOrder']) ? (int) $data['sortOrder'] : 0,
    ]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_delete_diary(int $diaryId): bool
{
    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('DELETE FROM diaries WHERE id = :id');
    $stmt->execute([':id' => $diaryId]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_create_photo(string $locationId, array $data): int
{
    if (empty($data['title'])) {
        throw new InvalidArgumentException('photo 需要提供 title');
    }

    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('INSERT INTO photos (location_id, slug, title, description, image_path, attribution, sort_order) VALUES (:location_id, :slug, :title, :description, :image_path, :attribution, :sort_order)');
    $stmt->execute([
        ':location_id' => $locationId,
        ':slug' => $data['slug'] ?? null,
        ':title' => $data['title'],
        ':description' => $data['description'] ?? null,
        ':image_path' => $data['image'] ?? null,
        ':attribution' => $data['attribution'] ?? null,
        ':sort_order' => isset($data['sortOrder']) ? (int) $data['sortOrder'] : 0,
    ]);

    gm_v2_locations(true);

    return (int) $pdo->lastInsertId();
}

function gm_v2_update_photo(int $photoId, array $data): bool
{
    if (empty($data['title'])) {
        throw new InvalidArgumentException('photo 更新需要提供 title');
    }

    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('UPDATE photos SET slug = :slug, title = :title, description = :description, image_path = :image_path, attribution = :attribution, sort_order = :sort_order WHERE id = :id');
    $stmt->execute([
        ':id' => $photoId,
        ':slug' => $data['slug'] ?? null,
        ':title' => $data['title'],
        ':description' => $data['description'] ?? null,
        ':image_path' => $data['image'] ?? null,
        ':attribution' => $data['attribution'] ?? null,
        ':sort_order' => isset($data['sortOrder']) ? (int) $data['sortOrder'] : 0,
    ]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_delete_photo(int $photoId): bool
{
    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('DELETE FROM photos WHERE id = :id');
    $stmt->execute([':id' => $photoId]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_create_video(string $locationId, array $data): int
{
    if (empty($data['title'])) {
        throw new InvalidArgumentException('video 需要提供 title');
    }

    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('INSERT INTO videos (location_id, slug, title, description, type, embed_url, svg_content, poster_path, mime_type, sort_order) VALUES (:location_id, :slug, :title, :description, :type, :embed_url, :svg_content, :poster_path, :mime_type, :sort_order)');
    $stmt->execute([
        ':location_id' => $locationId,
        ':slug' => $data['slug'] ?? null,
        ':title' => $data['title'],
        ':description' => $data['description'] ?? null,
        ':type' => $data['type'] ?? 'inlineSvg',
        ':embed_url' => $data['url'] ?? $data['embedUrl'] ?? null,
        ':svg_content' => $data['svg'] ?? $data['svgContent'] ?? null,
        ':poster_path' => $data['poster'] ?? $data['posterPath'] ?? null,
        ':mime_type' => $data['mime'] ?? $data['mimeType'] ?? null,
        ':sort_order' => isset($data['sortOrder']) ? (int) $data['sortOrder'] : 0,
    ]);

    gm_v2_locations(true);

    return (int) $pdo->lastInsertId();
}

function gm_v2_update_video(int $videoId, array $data): bool
{
    if (empty($data['title'])) {
        throw new InvalidArgumentException('video 更新需要提供 title');
    }

    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('UPDATE videos SET slug = :slug, title = :title, description = :description, type = :type, embed_url = :embed_url, svg_content = :svg_content, poster_path = :poster_path, mime_type = :mime_type, sort_order = :sort_order WHERE id = :id');
    $stmt->execute([
        ':id' => $videoId,
        ':slug' => $data['slug'] ?? null,
        ':title' => $data['title'],
        ':description' => $data['description'] ?? null,
        ':type' => $data['type'] ?? 'inlineSvg',
        ':embed_url' => $data['url'] ?? $data['embedUrl'] ?? null,
        ':svg_content' => $data['svg'] ?? $data['svgContent'] ?? null,
        ':poster_path' => $data['poster'] ?? $data['posterPath'] ?? null,
        ':mime_type' => $data['mime'] ?? $data['mimeType'] ?? null,
        ':sort_order' => isset($data['sortOrder']) ? (int) $data['sortOrder'] : 0,
    ]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_delete_video(int $videoId): bool
{
    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('DELETE FROM videos WHERE id = :id');
    $stmt->execute([':id' => $videoId]);

    gm_v2_locations(true);

    return $stmt->rowCount() > 0;
}

function gm_v2_fetch_locations_with_relations(PDO $pdo): array
{
    $sql = 'SELECT id, name, tagline, description, map_url, cover, sort_order FROM locations ORDER BY sort_order ASC, name ASC';
    $stmt = $pdo->query($sql);
    $locations = [];

    while ($row = $stmt->fetch()) {
        $locations[$row['id']] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'tagline' => $row['tagline'],
            'description' => $row['description'],
            'mapUrl' => $row['map_url'],
            'cover' => $row['cover'],
            'sortOrder' => (int) $row['sort_order'],
            'highlights' => [],
            'diaries' => [],
            'photos' => [],
            'videos' => [],
        ];
    }

    if (empty($locations)) {
        return [];
    }

    $ids = array_keys($locations);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));

    $highlightSql = "SELECT id, location_id, content FROM location_highlights WHERE location_id IN ($placeholders) ORDER BY sort_order ASC, id ASC";
    $highlightStmt = $pdo->prepare($highlightSql);
    $highlightStmt->execute($ids);
    foreach ($highlightStmt as $highlight) {
        $locations[$highlight['location_id']]['highlights'][] = [
            'id' => (int) $highlight['id'],
            'content' => $highlight['content'],
        ];
    }

    $diarySql = "SELECT id, location_id, slug, title, content, published_at, sort_order FROM diaries WHERE location_id IN ($placeholders) ORDER BY sort_order ASC, published_at ASC, id ASC";
    $diaryStmt = $pdo->prepare($diarySql);
    $diaryStmt->execute($ids);
    foreach ($diaryStmt as $diary) {
        $locations[$diary['location_id']]['diaries'][] = [
            'recordId' => (int) $diary['id'],
            'id' => $diary['slug'] ?: (string) $diary['id'],
            'slug' => $diary['slug'],
            'title' => $diary['title'],
            'createdAt' => $diary['published_at'],
            'content' => $diary['content'],
        ];
    }

    $photoSql = "SELECT id, location_id, slug, title, description, image_path, attribution, sort_order FROM photos WHERE location_id IN ($placeholders) ORDER BY sort_order ASC, id ASC";
    $photoStmt = $pdo->prepare($photoSql);
    $photoStmt->execute($ids);
    foreach ($photoStmt as $photo) {
        $locations[$photo['location_id']]['photos'][] = [
            'recordId' => (int) $photo['id'],
            'id' => $photo['slug'] ?: (string) $photo['id'],
            'slug' => $photo['slug'],
            'title' => $photo['title'],
            'description' => $photo['description'],
            'image' => $photo['image_path'],
            'attribution' => $photo['attribution'],
        ];
    }

    $videoSql = "SELECT id, location_id, slug, title, description, type, embed_url, svg_content, sort_order FROM videos WHERE location_id IN ($placeholders) ORDER BY sort_order ASC, id ASC";
    $videoStmt = $pdo->prepare($videoSql);
    $videoStmt->execute($ids);
    foreach ($videoStmt as $video) {
        $entry = [
            'recordId' => (int) $video['id'],
            'id' => $video['slug'] ?: (string) $video['id'],
            'slug' => $video['slug'],
            'title' => $video['title'],
            'description' => $video['description'],
            'type' => $video['type'],
            'poster' => $video['poster_path'],
            'mime' => $video['mime_type'],
        ];

        if ($video['type'] === 'inlineSvg') {
            $entry['svg'] = $video['svg_content'];
        } elseif ($video['type'] === 'localVideo') {
            $entry['source'] = $video['embed_url'];
        } else {
            $entry['url'] = $video['embed_url'];
        }

        $locations[$video['location_id']]['videos'][] = $entry;
    }

    return $locations;
}

function gm_v2_seed_database_from_local_config(?PDO $pdo = null): int
{
    static $seeding = false;

    if ($seeding) {
        return 0;
    }

    $dataFile = __DIR__ . '/../config/local-data.php';
    if (!is_file($dataFile)) {
        return 0;
    }

    $data = require $dataFile;
    if (!is_array($data)) {
        return 0;
    }

    $locations = $data['locations'] ?? [];
    if (empty($locations)) {
        return 0;
    }

    $pdo = $pdo ?? gm_v2_db();

    $seeding = true;
    $inserted = 0;

    try {
        $pdo->beginTransaction();

        $pdo->exec('DELETE FROM location_highlights');
        $pdo->exec('DELETE FROM diaries');
        $pdo->exec('DELETE FROM photos');
        $pdo->exec('DELETE FROM videos');
        $pdo->exec('DELETE FROM locations');

        $locationStmt = $pdo->prepare('INSERT INTO locations (id, name, tagline, description, map_url, cover, sort_order) VALUES (:id, :name, :tagline, :description, :map_url, :cover, :sort_order)');
        $highlightStmt = $pdo->prepare('INSERT INTO location_highlights (location_id, content, sort_order) VALUES (:location_id, :content, :sort_order)');
        $diaryStmt = $pdo->prepare('INSERT INTO diaries (location_id, slug, title, content, published_at, sort_order) VALUES (:location_id, :slug, :title, :content, :published_at, :sort_order)');
        $photoStmt = $pdo->prepare('INSERT INTO photos (location_id, slug, title, description, image_path, attribution, sort_order) VALUES (:location_id, :slug, :title, :description, :image_path, :attribution, :sort_order)');
        $videoStmt = $pdo->prepare('INSERT INTO videos (location_id, slug, title, description, type, embed_url, svg_content, poster_path, mime_type, sort_order) VALUES (:location_id, :slug, :title, :description, :type, :embed_url, :svg_content, :poster_path, :mime_type, :sort_order)');

        $position = 0;
        foreach ($locations as $locationId => $location) {
            $locationStmt->execute([
                ':id' => $locationId,
                ':name' => $location['name'] ?? $locationId,
                ':tagline' => $location['tagline'] ?? null,
                ':description' => $location['description'] ?? null,
                ':map_url' => $location['mapUrl'] ?? null,
                ':cover' => $location['cover'] ?? null,
                ':sort_order' => $position++,
            ]);

            $inserted++;

            foreach (($location['highlights'] ?? []) as $index => $highlight) {
                $content = is_array($highlight) ? ($highlight['content'] ?? '') : (string) $highlight;
                if ($content === '') {
                    continue;
                }
                $highlightStmt->execute([
                    ':location_id' => $locationId,
                    ':content' => $content,
                    ':sort_order' => $index,
                ]);
            }

            foreach (($location['diaries'] ?? []) as $index => $diary) {
                $diaryStmt->execute([
                    ':location_id' => $locationId,
                    ':slug' => $diary['id'] ?? $diary['slug'] ?? null,
                    ':title' => $diary['title'] ?? '未命名日誌',
                    ':content' => $diary['content'] ?? null,
                    ':published_at' => $diary['createdAt'] ?? null,
                    ':sort_order' => $index,
                ]);
            }

            foreach (($location['photos'] ?? []) as $index => $photo) {
                $photoStmt->execute([
                    ':location_id' => $locationId,
                    ':slug' => $photo['id'] ?? $photo['slug'] ?? null,
                    ':title' => $photo['title'] ?? '示範相片',
                    ':description' => $photo['description'] ?? null,
                    ':image_path' => $photo['image'] ?? null,
                    ':attribution' => $photo['attribution'] ?? null,
                    ':sort_order' => $index,
                ]);
            }

            foreach (($location['videos'] ?? []) as $index => $video) {
                $videoStmt->execute([
                    ':location_id' => $locationId,
                    ':slug' => $video['id'] ?? $video['slug'] ?? null,
                    ':title' => $video['title'] ?? '示範影音',
                    ':description' => $video['description'] ?? null,
                    ':type' => $video['type'] ?? 'inlineSvg',
                    ':embed_url' => $video['url'] ?? $video['embedUrl'] ?? $video['source'] ?? null,
                    ':svg_content' => $video['svg'] ?? $video['svgContent'] ?? null,
                    ':poster_path' => $video['poster'] ?? $video['posterPath'] ?? null,
                    ':mime_type' => $video['mime'] ?? $video['mimeType'] ?? null,
                    ':sort_order' => $index,
                ]);
            }
        }

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();
        throw $e;
    } finally {
        $seeding = false;
    }

    return $inserted;
}
