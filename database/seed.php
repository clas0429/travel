<?php
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

$pdo = gm_v2_db();

try {
    $count = gm_v2_seed_database_from_local_config($pdo);
    if ($count > 0) {
        echo "已從 config/local-data.php 匯入 {$count} 筆地點資料。" . PHP_EOL;
    } else {
        echo "config/local-data.php 未提供可匯入的地點資料。" . PHP_EOL;
    }
} catch (Throwable $e) {
    fwrite(STDERR, '匯入失敗: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
