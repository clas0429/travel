<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

function gm_admin_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_secure' => !empty($_SERVER['HTTPS']),
            'cookie_samesite' => 'Lax',
        ]);
    }
}

function gm_admin_current_user(): ?array
{
    gm_admin_session();
    return $_SESSION['admin_user'] ?? null;
}

function gm_admin_require_login(): array
{
    $user = gm_admin_current_user();
    if ($user !== null) {
        return $user;
    }

    $redirect = $_SERVER['REQUEST_URI'] ?? 'index.php';
    $query = http_build_query(['redirect' => $redirect]);
    header('Location: login.php?' . $query);
    exit;
}

function gm_admin_attempt_login(string $username, string $password): bool
{
    gm_admin_session();

    if ($username === '' || $password === '') {
        return false;
    }

    $pdo = gm_v2_db();
    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $record = $stmt->fetch();

    if (!$record || !password_verify($password, $record['password_hash'])) {
        return false;
    }

    $_SESSION['admin_user'] = [
        'id' => (int) $record['id'],
        'username' => $record['username'],
    ];

    return true;
}

function gm_admin_logout(): void
{
    gm_admin_session();
    unset($_SESSION['admin_user']);
}

function gm_admin_flash(string $type, string $message): void
{
    gm_admin_session();
    $_SESSION['admin_flash'][] = [
        'type' => $type,
        'message' => $message,
    ];
}

function gm_admin_get_flash(): array
{
    gm_admin_session();
    $messages = $_SESSION['admin_flash'] ?? [];
    unset($_SESSION['admin_flash']);
    return $messages;
}

function gm_admin_store_upload(array $file, string $subdir, array $allowedMime): array
{
    $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error !== UPLOAD_ERR_OK) {
        throw new RuntimeException('檔案上傳失敗，錯誤代碼：' . $error);
    }

    $tmpPath = $file['tmp_name'] ?? null;
    if (!$tmpPath || !is_uploaded_file($tmpPath)) {
        throw new RuntimeException('找不到暫存檔案。');
    }

    $mime = mime_content_type($tmpPath) ?: 'application/octet-stream';
    $extension = strtolower(pathinfo($file['name'] ?? '', PATHINFO_EXTENSION));

    if (!in_array($mime, $allowedMime, true)) {
        $isSvgFallback = $mime === 'text/plain' && $extension === 'svg' && in_array('image/svg+xml', $allowedMime, true);
        if (!$isSvgFallback) {
            throw new RuntimeException('不支援的檔案格式：' . $mime);
        }
        $mime = 'image/svg+xml';
    }

    $root = realpath(__DIR__ . '/..');
    if ($root === false) {
        throw new RuntimeException('無法取得專案根目錄。');
    }

    $targetDir = $root . '/uploads/' . $subdir;
    if (!is_dir($targetDir) && !mkdir($targetDir, 0775, true) && !is_dir($targetDir)) {
        throw new RuntimeException('無法建立上傳目錄。');
    }

    $filename = bin2hex(random_bytes(12));
    if ($extension !== '') {
        $filename .= '.' . $extension;
    }

    $destination = $targetDir . '/' . $filename;
    if (!move_uploaded_file($tmpPath, $destination)) {
        throw new RuntimeException('無法儲存上傳檔案。');
    }

    $relative = 'uploads/' . $subdir . '/' . $filename;

    return [$relative, $mime];
}

function gm_admin_normalize_datetime(?string $value): ?string
{
    if (!$value) {
        return null;
    }

    try {
        $date = new DateTime($value);
        return $date->format('Y-m-d H:i:s');
    } catch (Throwable $e) {
        return null;
    }
}
