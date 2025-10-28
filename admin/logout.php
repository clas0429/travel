<?php
require __DIR__ . '/../includes/admin.php';

gm_admin_logout();
gm_admin_flash('success', '您已安全登出。');

$redirect = $_POST['redirect'] ?? 'login.php';
header('Location: ' . $redirect);
exit;
