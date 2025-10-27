<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

echo '<pre>';
echo "__DIR__ = " . __DIR__ . "\n";
echo "TEST_VALUE (getenv) = " . getenv('TEST_VALUE') . "\n";
echo "TEST_VALUE (_ENV) = " . ($_ENV['TEST_VALUE'] ?? '(not set)') . "\n";
echo '</pre>';
