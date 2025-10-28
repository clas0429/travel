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

    $adminCount = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    if ($adminCount === 0) {
        $password = password_hash('admin1234', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (:username, :password_hash)');
        $stmt->execute([
            ':username' => 'admin',
            ':password_hash' => $password,
        ]);
        echo "已建立預設管理帳號 admin / admin1234，請儘速變更密碼。" . PHP_EOL;
    }
} catch (Throwable $e) {
    fwrite(STDERR, '匯入失敗: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}
