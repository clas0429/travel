<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

$data = require __DIR__ . '/../config/local-data.php';
$locations = $data['locations'] ?? [];

$pdo = gm_v2_db();
$pdo->beginTransaction();

try {
    $pdo->exec('DELETE FROM location_highlights');
    $pdo->exec('DELETE FROM diaries');
    $pdo->exec('DELETE FROM photos');
    $pdo->exec('DELETE FROM videos');
    $pdo->exec('DELETE FROM locations');

    $position = 0;
    foreach ($locations as $locationId => $location) {
        gm_v2_create_location([
            'id' => $locationId,
            'name' => $location['name'] ?? $locationId,
            'tagline' => $location['tagline'] ?? null,
            'description' => $location['description'] ?? null,
            'mapUrl' => $location['mapUrl'] ?? null,
            'cover' => $location['cover'] ?? null,
            'sortOrder' => $position++,
        ]);

        foreach (($location['highlights'] ?? []) as $index => $highlight) {
            gm_v2_create_highlight($locationId, $highlight, $index);
        }

        foreach (($location['diaries'] ?? []) as $index => $diary) {
            gm_v2_create_diary($locationId, [
                'slug' => $diary['id'] ?? null,
                'title' => $diary['title'] ?? '未命名日誌',
                'content' => $diary['content'] ?? null,
                'createdAt' => $diary['createdAt'] ?? null,
                'sortOrder' => $index,
            ]);
        }

        foreach (($location['photos'] ?? []) as $index => $photo) {
            gm_v2_create_photo($locationId, [
                'slug' => $photo['id'] ?? null,
                'title' => $photo['title'] ?? '示範相片',
                'description' => $photo['description'] ?? null,
                'image' => $photo['image'] ?? null,
                'attribution' => $photo['attribution'] ?? null,
                'sortOrder' => $index,
            ]);
        }

        foreach (($location['videos'] ?? []) as $index => $video) {
            gm_v2_create_video($locationId, [
                'slug' => $video['id'] ?? null,
                'title' => $video['title'] ?? '示範影音',
                'description' => $video['description'] ?? null,
                'type' => $video['type'] ?? 'inlineSvg',
                'svg' => $video['svg'] ?? null,
                'url' => $video['url'] ?? null,
                'sortOrder' => $index,
            ]);
        }
    }

    $pdo->commit();
} catch (Throwable $e) {
    $pdo->rollBack();
    throw $e;
}
