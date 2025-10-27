<?php
// ======================================
// customer_search.php
// 顧客検索API（電話番号で検索）
// ======================================
require_once __DIR__ . '/../setup.php';
require_once __DIR__ . '/../db_connect.php';

// レスポンス初期化
$response = ['success' => false];

try {
    // ------------------------
    // 入力値チェック
    // ------------------------
    $phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
    if ($phone === '') {
        throw new Exception('電話番号が未入力です。');
    }

    // ------------------------
    // 顧客検索
    // ------------------------
    $stmt = $pdo->prepare("SELECT * FROM customers WHERE phone = :phone LIMIT 1");
    $stmt->bindValue(':phone', $phone, PDO::PARAM_STR);
    $stmt->execute();
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$customer) {
        // 顧客なし → success=false のまま
        echo json_encode($response);
        exit;
    }

    // ------------------------
    // 顧客あり → 予約一覧取得
    // ------------------------
    $stmt = $pdo->prepare("
        SELECT id, visit_date, visit_time, departure_time,
               service_photo, service_rental, service_kitsuke, service_hairmake,group_name 
        FROM reservations
        WHERE customer_id = :cid
        ORDER BY visit_date DESC, id DESC
    ");
    $stmt->bindValue(':cid', $customer['id'], PDO::PARAM_INT);
    $stmt->execute();
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ------------------------
    // レスポンス返却
    // ------------------------
    $response = [
        'success' => true,
        'customer' => $customer,
        'reservations' => $reservations
    ];
} catch (Exception $e) {
    $response['error'] = $e->getMessage();
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($response);
