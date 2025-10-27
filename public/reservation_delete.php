<?php
require_once "../setup.php";
require_once "../db_connect.php";

if (empty($_POST['id'])) {
    $_SESSION['flash_message'] = "予約IDが指定されていません。";
    header("Location: index.php");
    exit;
}

$reservation_id = (int)$_POST['id'];

try {
    $pdo->beginTransaction();

    // 同行者削除
    $stmt = $pdo->prepare("DELETE FROM reservation_people WHERE reservation_id = ?");
    $stmt->execute([$reservation_id]);

    // 予約削除
    $stmt = $pdo->prepare("DELETE FROM reservations WHERE id = ?");
    $stmt->execute([$reservation_id]);

    $pdo->commit();

    $_SESSION['flash_message']  = "予約を削除しました。";
} catch (Exception $e) {
    $pdo->rollBack();
   $_SESSION['flash_message']  = "削除中にエラーが発生しました：" . $e->getMessage();
}

header("Location: index.php");
exit;
?>
