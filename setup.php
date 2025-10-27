<?php
/**
 * ===========================================
 *  Studio PhotoArtisan 予約管理システム 設定ファイル
 * ===========================================
 *  - 本ファイルは /order_system/setup.php に設置
 *  - すべての public/*.php から require_once で読み込みます
 */

// setup.php
require_once __DIR__ . '/vendor/autoload.php';

// .env読込
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// Xserverなど getenv()が無効な環境でも対応
$env = $_ENV + getenv();


// セッションを一度だけ開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 共通設定
mb_internal_encoding("UTF-8");
date_default_timezone_set("Asia/Tokyo");

// エラー表示設定（本番環境ではfalseに）
ini_set('display_errors', true);
error_reporting(E_ALL);

// -------------------------------------------
// 会社・システム情報
// -------------------------------------------
define('COMPANY_NAME', 'Studio PhotoArtisan');
define('SYSTEM_NAME', 'Order & Reservation System');
define('SITE_NAME', 'Studio PhotoArtisan');

// -------------------------------------------
// サイトURL（環境に合わせて調整）
// -------------------------------------------
// 例: https://photoartisan.jp/order_system/public
define('BASE_URL', 'https://photoartisan.jp/order_system/public');

// -------------------------------------------
// データベース接続情報
// -------------------------------------------
// phpMyAdmin で設定済み

define('DB_HOST', $env['DB_HOST'] ?? 'localhost');
define('DB_USER', $env['DB_USER'] ?? '');
define('DB_PASS', $env['DB_PASS'] ?? '');
define('DB_NAME', 'stpa_ordersystem');
define('DB_CHARSET', 'utf8mb4');


// -------------------------------------------
// PDO 接続生成関数
// -------------------------------------------
function getPDO(): PDO {
    static $pdo;
    if (!$pdo) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ]);
    }
    return $pdo;
}

// -------------------------------------------
// エスケープ用ユーティリティ関数
// -------------------------------------------
if (!function_exists('h')) {
    function h($str) {
        return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
    }
}

// -------------------------------------------
// タイムゾーン設定
// -------------------------------------------
date_default_timezone_set('Asia/Tokyo');
