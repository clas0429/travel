<?php

function gm_v2_raw_data(): array
{
    static $data = null;
    if ($data === null) {
        $loaded = require __DIR__ . '/../config/local-data.php';
        $data = is_array($loaded) ? $loaded : [];
    }
    return $data;
}

function gm_v2_locations(): array
{
    $data = gm_v2_raw_data();
    return $data['locations'] ?? [];
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
