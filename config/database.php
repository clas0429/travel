<?php
$DBNAME   = "travel";
$DBUSER   = "root";
$DBPASSWD = "kuma@42749128";
$DBHOST   = "localhost";

$dsn = "mysql:host=$DBHOST;dbname=$DBNAME;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $DBUSER, $DBPASSWD, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    throw new RuntimeException('無法連結資料庫: ' . $e->getMessage(), 0, $e);
}

return $pdo;
