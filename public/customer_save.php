<?php
require_once __DIR__ . '/../setup.php';
require_once __DIR__ . '/../db_connect.php';

header('Content-Type: text/html; charset=utf-8');

$mode = $_POST['mode'] ?? '';
$id = intval($_POST['id'] ?? 0);

$name = trim($_POST['name'] ?? '');
$furigana = trim($_POST['furigana'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$zipcode = trim($_POST['zipcode'] ?? '');
$address1 = trim($_POST['address1'] ?? '');
$address2 = trim($_POST['address2'] ?? '');

if ($name === '' || $phone === '') {
    echo "<script>alert('お名前と電話番号は必須です');history.back();</script>";
    exit;
}

try {
    if ($mode === 'edit_customer' && $id > 0) {
        $stmt = $pdo->prepare("
            UPDATE customers SET 
                name = ?, furigana = ?, phone = ?, email = ?, 
                zipcode = ?, address1 = ?, address2 = ?, 
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([$name, $furigana, $phone, $email, $zipcode, $address1, $address2, $id]);

        $_SESSION['flash_message'] = '顧客情報を更新しました。';
        header('Location: index.php');
        exit;

    } else {
        $stmt = $pdo->prepare("
            INSERT INTO customers 
                (name, furigana, phone, email, zipcode, address1, address2, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([$name, $furigana, $phone, $email, $zipcode, $address1, $address2]);

        $newCustomerId = $pdo->lastInsertId();
        $_SESSION['flash_message'] = '顧客情報を登録しました。';
        header("Location: customer_input.php?mode=new_reservation&customer_id={$newCustomerId}");
        exit;
    }
} catch (Exception $e) {
    error_log("Customer save error: " . $e->getMessage());
    echo "<div style='color:red; padding:1em;'>エラーが発生しました：" . htmlspecialchars($e->getMessage()) . "</div>";
}
