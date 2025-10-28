<?php
declare(strict_types=1);

/**
 * database/seed.php
 * 可於 CLI 或 瀏覽器執行的資料匯入腳本。
 * - CLI：用 STDERR / STDOUT 輸出訊息與退出碼
 * - Web：用 echo 顯示訊息、error_log() 記錄錯誤，並設定 HTTP 狀態碼
 *
 * 執行範例（建議）：
 *   php /var/www/lioho/travel/database/seed.php
 */

ini_set('display_errors', '1');
error_reporting(E_ALL);

$IS_CLI = (PHP_SAPI === 'cli');

/** 統一輸出：一般訊息 */
function out(string $msg) : void {
    global $IS_CLI;
    $msg = rtrim($msg, "\r\n");
    if ($IS_CLI) {
        // CLI 直接輸出到 STDOUT
        echo $msg . PHP_EOL;
    } else {
        // Web 模式：轉成 HTML 安全並換行
        echo nl2br(htmlspecialchars($msg, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) . "\n";
    }
}

/** 統一輸出：錯誤訊息（優先 STDERR，否則用 error_log） */
function err(string $msg) : void {
    global $IS_CLI;
    $msg = rtrim($msg, "\r\n");
    if (defined('STDERR')) {
        // CLI 下可用 STDERR
        fwrite(STDERR, $msg . PHP_EOL);
    } else {
        // Web/FPM 下沒有 STDERR，改用 error_log
        error_log($msg);
        // 同時在頁面上顯示（避免白畫面）
        echo nl2br(htmlspecialchars($msg, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) . "\n";
    }
}

/** 失敗時結束（CLI 給退出碼 1；Web 設 500） */
function fail_exit(int $code = 1) : void {
    global $IS_CLI;
    if (!$IS_CLI) {
        if (!headers_sent()) {
            http_response_code(500);
            header('Content-Type: text/plain; charset=UTF-8');
        }
    }
    exit($IS_CLI ? $code : 0);
}

require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';

$pdo = gm_v2_db();

try {
    // 匯入地點資料
    $count = gm_v2_seed_database_from_local_config($pdo);
    if ($count > 0) {
        out("已從 config/local-data.php 匯入 {$count} 筆地點資料。");
    } else {
        out("config/local-data.php 未提供可匯入的地點資料。");
    }

    // 建立預設管理者帳號
    $adminCount = (int) $pdo->query('SELECT COUNT(*) FROM admin_users')->fetchColumn();
    if ($adminCount === 0) {
        $passwordPlain = 'admin1234';
        $passwordHash  = password_hash($passwordPlain, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare('INSERT INTO admin_users (username, password_hash) VALUES (:username, :password_hash)');
        $stmt->execute([
            ':username'      => 'admin',
            ':password_hash' => $passwordHash,
        ]);

        out("已建立預設管理帳號 admin / {$passwordPlain}，請儘速變更密碼。");
    } else {
        out("管理者帳號已存在，略過建立預設帳號。");
    }

    // 成功結束
    if (!$IS_CLI && !headers_sent()) {
        header('Content-Type: text/plain; charset=UTF-8');
    }
    out("匯入流程完成。");

} catch (Throwable $e) {
    $msg = sprintf(
        '匯入失敗: [%s] %s (code=%d)',
        get_class($e),
        $e->getMessage(),
        (int) $e->getCode()
    );
    err($msg);

    // 如需除錯可打開下一行（避免洩漏敏感資訊，預設關閉）
    // err($e->getTraceAsString());

    fail_exit(1);
}
