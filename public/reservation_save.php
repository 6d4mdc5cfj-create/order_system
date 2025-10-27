<?php
require_once __DIR__ . '/../setup.php';
require_once __DIR__ . '/../db_connect.php';

header('Content-Type: text/html; charset=utf-8');

$mode = $_POST['mode'] ?? '';
$customer_id = intval($_POST['customer_id'] ?? 0);
$reservation_id = intval($_POST['reservation_id'] ?? 0);

if ($customer_id <= 0) {
    echo "<script>alert('顧客情報が不正です'); history.back();</script>";
    exit;
}

$group_name = trim($_POST['group_name'] ?? '');
$visit_date = $_POST['visit_date'] ?? null;
$visit_hour = $_POST['visit_hour'] ?? null;
$visit_minute = $_POST['visit_minute'] ?? null;
$leave_hour = $_POST['leave_hour'] ?? null;
$leave_minute = $_POST['leave_minute'] ?? null;
$service_photo = intval($_POST['service_photo'] ?? 0);
$service_rental = intval($_POST['service_rental'] ?? 0);
$service_kitsuke = intval($_POST['service_kitsuke'] ?? 0);
$service_hairmake = intval($_POST['service_hairmake'] ?? 0);

$visit_time = ($visit_hour !== '' && $visit_minute !== '') ? sprintf("%02d:%02d:00", $visit_hour, $visit_minute) : null;
$departure_time = ($leave_hour !== '' && $leave_minute !== '') ? sprintf("%02d:%02d:00", $leave_hour, $leave_minute) : null;

try {
    $pdo->beginTransaction();

    if ($mode === 'edit_reservation' && $reservation_id > 0) {
        // 予約更新
        $stmt = $pdo->prepare("
            UPDATE reservations
            SET 
                customer_id = ?, 
                group_name = ?, 
                visit_date = ?, 
                visit_time = ?, 
                departure_time = ?, 
                service_photo = ?, 
                service_rental = ?, 
                service_kitsuke = ?, 
                service_hairmake = ?, 
                updated_at = NOW()
            WHERE id = ?
        ");
        $stmt->execute([
            $customer_id,
            $group_name,
            $visit_date,
            $visit_time,
            $departure_time,
            $service_photo,
            $service_rental,
            $service_kitsuke,
            $service_hairmake,
            $reservation_id
        ]);

        // 既存同行者削除 → 再登録
        $pdo->prepare("DELETE FROM reservation_people WHERE reservation_id = ?")->execute([$reservation_id]);

    } else {
        // 新規予約登録
        $stmt = $pdo->prepare("
            INSERT INTO reservations 
                (customer_id, group_name, visit_date, visit_time, departure_time,
                 service_photo, service_rental, service_kitsuke, service_hairmake,
                 created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");
        $stmt->execute([
            $customer_id,
            $group_name,
            $visit_date,
            $visit_time,
            $departure_time,
            $service_photo,
            $service_rental,
            $service_kitsuke,
            $service_hairmake
        ]);

        $reservation_id = $pdo->lastInsertId();
    }

    // 同行者登録
    if (!empty($_POST['people']) && is_array($_POST['people'])) {
        $stmtP = $pdo->prepare("
            INSERT INTO reservation_people
                (reservation_id, person_no, name, furigana, gender, age,
                 rental_kitsuke, rental_out, hairmake, height, foot_size, note,
                 created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
        ");

        foreach ($_POST['people'] as $no => $p) {
            $stmtP->execute([
                $reservation_id,
                $no,
                $p['name'] ?? '',
                $p['furigana'] ?? '',
                $p['gender'] ?? '',
                $p['age'] ?? '',
                $p['rental_kitsuke'] ?? '',
                $p['rental_out'] ?? '',
                $p['hairmake'] ?? '',
                $p['height'] ?? '',
                $p['foot_size'] ?? '',
                $p['note'] ?? ''
            ]);
        }
    }

    $pdo->commit();

    $_SESSION['flash_message'] = ($mode === 'edit_reservation')
        ? '予約を更新しました。'
        : '予約を登録しました。';

    // 保存後の遷移先（必要に応じて変更可）
    header("Location: index.php");
    exit;

} catch (Exception $e) {
    $pdo->rollBack();
    error_log("reservation_save.php error: " . $e->getMessage());
    echo "<script>alert('エラーが発生しました: " . htmlspecialchars($e->getMessage(), ENT_QUOTES) . "'); history.back();</script>";
    exit;
}
