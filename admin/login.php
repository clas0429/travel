<?php
require __DIR__ . '/../includes/admin.php';

$redirect = $_GET['redirect'] ?? 'index.php';
$user = gm_admin_current_user();
if ($user) {
    header('Location: ' . $redirect);
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $redirect = $_POST['redirect'] ?? $redirect;

    if (gm_admin_attempt_login($username, $password)) {
        gm_admin_flash('success', '歡迎回來，' . $username . '！');
        header('Location: ' . $redirect);
        exit;
    }

    $error = '登入失敗，請確認帳號與密碼。';
}

$messages = gm_admin_get_flash();
?>
<!DOCTYPE html>
<html lang="zh-Hant">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Guide Magnets 後台登入</title>
  <link rel="stylesheet" href="../assets/css/local-tailwind.css">
  <link rel="stylesheet" href="../assets/css/tokens.css">
  <style>
    body { background: linear-gradient(120deg, #1d4ed8, #312e81); color: white; min-height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Noto Sans TC', system-ui, sans-serif; }
    .login-card { background: rgba(255,255,255,0.12); backdrop-filter: blur(12px); border-radius: 1.5rem; padding: 2.5rem; width: min(420px, 90vw); display: grid; gap: 1.5rem; box-shadow: 0 24px 60px -30px rgba(15,23,42,0.6); }
    h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }
    p { margin: 0; color: rgba(255,255,255,0.78); }
    label { display: grid; gap: 0.5rem; font-size: 0.95rem; }
    input[type="text"], input[type="password"] { padding: 0.8rem 1rem; border-radius: 0.75rem; border: none; outline: none; font-size: 1rem; }
    button { padding: 0.9rem 1rem; border-radius: 999px; border: none; background: #fbbf24; color: #1f2937; font-weight: 700; cursor: pointer; }
    .flash { padding: 0.9rem 1rem; border-radius: 0.9rem; font-size: 0.95rem; }
    .flash.success { background: rgba(34,197,94,0.3); color: #dcfce7; }
    .flash.error { background: rgba(248,113,113,0.3); color: #fee2e2; }
    .footer { font-size: 0.85rem; color: rgba(255,255,255,0.6); text-align: center; }
    a { color: #fbbf24; text-decoration: none; }
  </style>
</head>
<body>
  <main class="login-card">
    <header>
      <h1>Guide Magnets 後台</h1>
      <p>請輸入管理員帳號以維護旅遊內容。</p>
    </header>

    <?php foreach ($messages as $message): ?>
    <div class="flash <?php echo gm_v2_escape($message['type']); ?>"><?php echo gm_v2_escape($message['message']); ?></div>
    <?php endforeach; ?>

    <?php if ($error): ?>
    <div class="flash error"><?php echo gm_v2_escape($error); ?></div>
    <?php endif; ?>

    <form method="post">
      <input type="hidden" name="redirect" value="<?php echo gm_v2_escape($redirect); ?>">
      <label>帳號
        <input type="text" name="username" autofocus required>
      </label>
      <label>密碼
        <input type="password" name="password" required>
      </label>
      <button type="submit">登入</button>
    </form>

    <p class="footer">預設帳號為 <strong>admin</strong>，密碼 <strong>admin1234</strong>（請登入後立即變更）。</p>
    <p class="footer"><a href="../index.php">回到前台首頁</a></p>
  </main>
</body>
</html>
