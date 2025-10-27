<?php
require_once __DIR__ . '/../setup.php';
require_once __DIR__ . '/../db_connect.php';

$message = '';
if (isset($_SESSION['flash_message'])) {
    $msg = is_array($_SESSION['flash_message'])
        ? ($_SESSION['flash_message']['text'] ?? '')
        : $_SESSION['flash_message'];
    if ($msg) $message = htmlspecialchars($msg, ENT_QUOTES);
    unset($_SESSION['flash_message']);
}
?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>お客様情報入力</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        tr.past-reservation {
            background-color: #e9ecef !important;
            color: #777 !important;
        }

        .flash-message {
            animation: fadeOut 2s 4s forwards;
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                visibility: hidden;
            }
        }

    </style>
</head>

<body class="bg-light">
    <div class="container py-4">
        <h4 class="mb-4 text-center">お客様情報入力</h4>

        <?php if ($message): ?>
        <div class="alert alert-success text-center py-2 mb-3 flash-message">
            <?= $message ?>
        </div>
        <?php endif; ?>

        <div id="search_area" class="card p-3 shadow-sm">
            <label for="phone" class="form-label">電話番号（ハイフンなし）</label>
            <div class="input-group">
                <input type="tel" id="phone" class="form-control" placeholder="例：09012345678">
                <button id="search_btn" class="btn btn-primary">検索</button>
            </div>
        </div>

        <div id="result_area" class="mt-4"></div>
    </div>

    <!-- 分離したJSファイルを読み込み -->
    <script src="js/customer_index.js"></script>
</body>

</html>
