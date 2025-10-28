<?php

function gm_v2_db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $pdo = require __DIR__ . '/../config/database.php';

    if (!$pdo instanceof PDO) {
        throw new RuntimeException('Database configuration 必須回傳 PDO 實例');
    }

    return $pdo;
}
