<?php
require_once __DIR__ . '/../setup.php';
require_once __DIR__ . '/../db_connect.php';

// ====================================
// モード・初期データ判定
// ====================================
$mode = $_GET['mode'] ?? 'new';
$customer = [];
$reservation = [];
$people = [];

// 顧客編集・予約編集モード時に既存データを取得
if (in_array($mode, ['edit_customer', 'edit_reservation', 'new_reservation'])) {
    if ($mode === 'edit_customer' && !empty($_GET['id'])) {
        $id = (int)$_GET['id'];
        $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
        $stmt->execute([$id]);
        $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    if (in_array($mode, ['edit_reservation', 'new_reservation']) && !empty($_GET['customer_id'] ?? $_GET['id'])) {
        $reservation_id = $mode === 'edit_reservation' ? (int)($_GET['id']) : null;
        $customer_id   = $mode === 'new_reservation' ? (int)($_GET['customer_id']) : ($reservation['customer_id'] ?? null);
        if ($reservation_id) {
            $stmt = $pdo->prepare("SELECT * FROM reservations WHERE id = ?");
            $stmt->execute([$reservation_id]);
            $reservation = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        if (!empty($customer_id)) {
            $stmt = $pdo->prepare("SELECT * FROM customers WHERE id = ?");
            $stmt->execute([$customer_id]);
            $customer = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        if ($reservation_id) {
            $stmt = $pdo->prepare("SELECT * FROM reservation_people WHERE reservation_id = ?");
            $stmt->execute([$reservation_id]);
            $people = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>顧客・予約入力</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* モバイル最適化スタイル */
        @media (max-width: 768px) {
            body {
                font-size: 14px;
            }

            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }

            .person-block {
                border: 1px solid #ddd;
                border-radius: 10px;
                padding: 10px;
                margin-bottom: 10px;
                background-color: #fff;
            }

            .form-label {
                font-weight: 500;
                font-size: 0.9rem;
            }

            input,
            select,
            textarea {
                font-size: 1rem;
            }
        }

    </style>
    <script>
        // PHPデータをJSへ安全に受け渡し
        window.MODE = window.MODE ?? "<?= htmlspecialchars($mode, ENT_QUOTES) ?>";
        window.PEOPLE_DATA = window.PEOPLE_DATA ?? <?= json_encode($people) ?>;

    </script>
</head>

<body class="bg-light">

    <div class="container-fluid py-4">

        <h4 class="mb-4 text-center">
            <?php
      if ($mode === 'new') echo "新規顧客登録";
      elseif ($mode === 'edit_customer') echo "顧客情報編集";
      elseif ($mode === 'new_reservation') echo "新規予約";
      elseif ($mode === 'edit_reservation') echo "予約編集";
    ?>
        </h4>

        <form method="POST" action="<?php
          echo in_array($mode, ['new', 'edit_customer']) ? 'customer_save.php' : 'reservation_save.php';
        ?>" class="card p-4 shadow-sm bg-white">
            <input type="hidden" name="mode" value="<?= htmlspecialchars($mode, ENT_QUOTES) ?>">
            <?php if (!empty($reservation['id'])): ?>
            <input type="hidden" name="reservation_id" value="<?= htmlspecialchars($reservation['id'], ENT_QUOTES) ?>">
            <?php endif; ?>

            <?php
$cid = $customer['id'] ?? $reservation['customer_id'] ?? null;
if (!empty($cid)): ?>
            <input type="hidden" name="customer_id" value="<?= htmlspecialchars($cid, ENT_QUOTES) ?>">
            <?php endif; ?>

            <!-- 顧客情報フォーム -->
            <?php if (in_array($mode, ['new', 'edit_customer'])): ?>
            <h5 class="mb-3 border-bottom pb-2">顧客情報</h5>
            <div class="mb-3">
                <label class="form-label">お名前</label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($customer['name'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">ふりがな</label>
                <input type="text" name="furigana" class="form-control" value="<?= htmlspecialchars($customer['furigana'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">電話番号</label>
                <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($customer['phone'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">メールアドレス</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($customer['email'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">郵便番号</label>
                <input type="text" name="zipcode" class="form-control" value="<?= htmlspecialchars($customer['zipcode'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">住所1</label>
                <input type="text" name="address1" class="form-control" value="<?= htmlspecialchars($customer['address1'] ?? '', ENT_QUOTES) ?>">
            </div>
            <div class="mb-3">
                <label class="form-label">住所2</label>
                <input type="text" name="address2" class="form-control" value="<?= htmlspecialchars($customer['address2'] ?? '', ENT_QUOTES) ?>">
            </div>
            <?php endif; ?>

            <!-- 予約情報フォーム -->
            <?php if (in_array($mode, ['new', 'new_reservation', 'edit_reservation'])): ?>
            <h5 class="mt-4 mb-3 border-bottom pb-2">予約情報</h5>

            <div class="mb-3">
                <label class="form-label" for="group_name">予約グループ名</label>
                <input type="text" id="group_name" name="group_name" class="form-control" placeholder="お連れ様がいる場合は同じグループ名を入力" value="<?= htmlspecialchars($reservation['group_name'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">来店日</label>
                <input type="date" name="visit_date" class="form-control" value="<?= htmlspecialchars($reservation['visit_date'] ?? '', ENT_QUOTES) ?>">
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">来店時刻（時）</label>
                    <select name="visit_hour" class="form-select">
                        <option value="">-</option>
                        <?php for ($h = 0; $h < 24; $h++): ?>
                        <option value="<?= $h ?>" <?= isset($reservation['visit_time']) && (int)substr($reservation['visit_time'], 0, 2) === $h ? 'selected' : '' ?>>
                            <?= sprintf('%02d', $h) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">来店時刻（分）</label>
                    <select name="visit_minute" class="form-select">
                        <?php for ($m = 0; $m < 60; $m += 5): ?>
                        <option value="<?= sprintf('%02d', $m) ?>" <?= isset($reservation['visit_time']) && (int)substr($reservation['visit_time'], 3, 2) === $m ? 'selected' : ($m === 0 ? 'selected' : '') ?>>
                            <?= sprintf('%02d', $m) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-6">
                    <label class="form-label">退店時刻（時）</label>
                    <select name="leave_hour" class="form-select">
                        <option value="">-</option>
                        <?php for ($h = 0; $h < 24; $h++): ?>
                        <option value="<?= $h ?>" <?= isset($reservation['leave_time']) && (int)substr($reservation['leave_time'], 0, 2) === $h ? 'selected' : '' ?>>
                            <?= sprintf('%02d', $h) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-6">
                    <label class="form-label">退店時刻（分）</label>
                    <select name="leave_minute" class="form-select">
                        <?php for ($m = 0; $m < 60; $m += 5): ?>
                        <option value="<?= sprintf('%02d', $m) ?>" <?= isset($reservation['leave_time']) && (int)substr($reservation['leave_time'], 3, 2) === $m ? 'selected' : ($m === 0 ? 'selected' : '') ?>>
                            <?= sprintf('%02d', $m) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="service_photo" name="service_photo" value="1" <?= !empty($reservation['service_photo']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="service_photo">撮影</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="service_rental" name="service_rental" value="1" <?= !empty($reservation['service_rental']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="service_rental">レンタル</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="service_kitsuke" name="service_kitsuke" value="1" <?= !empty($reservation['service_kitsuke']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="service_kitsuke">着付</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="service_hairmake" name="service_hairmake" value="1" <?= !empty($reservation['service_hairmake']) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="service_hairmake">ヘアメイク</label>
                </div>
            </div>

            <div id="people_area" style="display:none;">
                <h6 class="mb-3">施術内容（同行者）</h6>
                <div id="people_list"></div>
                <div class="text-end mt-2">
                    <button type="button" id="add_person" class="btn btn-sm btn-outline-primary">＋同行者を追加</button>
                </div>
            </div>
            <?php endif; ?>

            <div class="mt-4 text-center">
                <button type="submit" class="btn btn-primary px-5">保存</button>
                <a href="index.php" class="btn btn-secondary ms-2">戻る</a>
            </div>

        </form>
    </div>

    <script src="js/customer_input.js?v=20251026"></script>
</body>

</html>
