// =========================
// Cookie操作ヘルパー
// =========================
function setCookie(name, value, days = 7) {
    const d = new Date();
    d.setTime(d.getTime() + (days * 24 * 60 * 60 * 1000));
    document.cookie = `${name}=${value}; expires=${d.toUTCString()}; path=/`;
}

function getCookie(name) {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? match[2] : '';
}

// =========================
// ページ読み込み後処理
// =========================
document.addEventListener("DOMContentLoaded", () => {
    const phoneInput = document.getElementById('phone');
    const savedPhone = getCookie('customer_phone');
    const searchBtn = document.getElementById('search_btn');

    // 検索ボタンクリックイベント
    searchBtn.addEventListener('click', async () => {
        const phone = phoneInput.value.trim();
        if (!phone) {
            alert("電話番号を入力してください");
            return;
        }

        setCookie('customer_phone', phone);
        const resArea = document.getElementById('result_area');
        resArea.innerHTML = `<div class='text-center text-muted mt-3'>検索中...</div>`;

        try {
            const res = await fetch('customer_search.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'phone=' + encodeURIComponent(phone)
            });
            const data = await res.json();

            if (!data.success) {
                resArea.innerHTML = `
                    <div class="alert alert-warning mt-3 text-center">
                        登録されていません。<br>
                        <a href="customer_input.php?mode=new&phone=${phone}" class="btn btn-success mt-2">新規登録</a>
                    </div>`;
                return;
            }

            const c = data.customer;
            let html = `
                <div class="card p-3 shadow-sm">
                    <h5>顧客情報</h5>
                    <div><strong>氏名：</strong>${c.name || ''}</div>
                    <div><strong>ふりがな：</strong>${c.furigana || ''}</div>
                    <div><strong>電話番号：</strong>${c.phone || ''}</div>
                    <div><strong>メール：</strong>${c.email || ''}</div>
                    <div><strong>住所：</strong>${c.zipcode || ''} ${c.address1 || ''} ${c.address2 || ''}</div>
                    <a href="customer_input.php?mode=edit_customer&id=${c.id}" class="btn btn-outline-primary btn-sm mt-2">顧客情報を編集</a>
                </div>`;

            if (data.reservations && data.reservations.length > 0) {
                html += `
                <div class="card p-3 shadow-sm mt-3">
                    <h5>予約一覧</h5>
                    <table class="table table-sm align-middle">
                        <thead><tr><th>グループ名</th><th>来店日</th><th>時間</th><th>サービス</th><th></th></tr></thead>
                        <tbody>`;

                const today = new Date();
                const todayMidnight = new Date(today.getFullYear(), today.getMonth(), today.getDate());

                function parseYMDToDate(ymd) {
                    if (!ymd || !/^\d{4}-\d{2}-\d{2}$/.test(ymd)) return null;
                    const [y, m, d] = ymd.split('-').map(Number);
                    return new Date(y, m - 1, d);
                }

                data.reservations.forEach(r => {
                    const services = [];
                    if (r.service_photo == 1) services.push("撮影");
                    if (r.service_rental == 1) services.push("レンタル");
                    if (r.service_kitsuke == 1) services.push("着付");
                    if (r.service_hairmake == 1) services.push("ヘアメイク");

                    const visitDateObj = parseYMDToDate(r.visit_date);
                    const isPast = visitDateObj ? (visitDateObj < todayMidnight) : false;
                    const visitDate = r.visit_date && r.visit_date !== "0000-00-00" ? r.visit_date.substring(2) : "-";
                    const visitTime = r.visit_time && r.visit_time !== "00:00:00" ? r.visit_time.slice(0, 5) : "-";

                    html += `
                    <tr class="${isPast ? 'past-reservation' : ''}" data-is-past="${isPast ? '1' : '0'}" data-visit-date="${r.visit_date || ''}">
                        <td>${r.group_name || '-'}</td>
                        <td>${visitDate}</td>
                        <td>${visitTime}</td>
                        <td>${services.join('・') || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="editReservation(${r.id}, this)">編集</button>
                            <button class="btn btn-sm btn-outline-danger ms-1" onclick="deleteReservation(${r.id})">削除</button>
                        </td>
                    </tr>`;
                });

                html += `</tbody></table>
                    <a href="customer_input.php?mode=new_reservation&customer_id=${c.id}" class="btn btn-success mt-2">新規予約を追加</a>
                </div>`;
            } else {
                html += `
                <div class="card p-3 shadow-sm mt-3">
                    <h5>予約はまだありません</h5>
                    <a href="customer_input.php?mode=new_reservation&customer_id=${c.id}" class="btn btn-success mt-2">新規予約を追加</a>
                </div>`;
            }

            resArea.innerHTML = html;
        } catch (err) {
            console.error(err);
            resArea.innerHTML = `<div class="alert alert-danger mt-3">通信エラーが発生しました。</div>`;
        }
    });

    // ページ読み込み後に Cookie があれば自動検索
    if (savedPhone) {
        phoneInput.value = savedPhone;
        setTimeout(() => searchBtn.click(), 300);
    }
});

// =========================
// 予約削除処理（予約IDのみPOST）
// =========================
function deleteReservation(id) {
    if (!confirm('本当にこの予約を削除しますか？')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'reservation_delete.php';

    const inputId = document.createElement('input');
    inputId.type = 'hidden';
    inputId.name = 'id';
    inputId.value = id;
    form.appendChild(inputId);

    document.body.appendChild(form);
    form.submit();
}

// =========================
// 予約編集（過去日アラート付き）
// =========================
function editReservation(id, btnElement) {
    const tr = btnElement.closest('tr');
    const isPast = tr && tr.getAttribute('data-is-past') === '1';
    const visitDate = tr ? tr.getAttribute('data-visit-date') : '';
    if (isPast) {
        if (!confirm(`この予約（${visitDate}）はすでに過去の日付です。編集しますか？`)) return;
    }
    window.location.href = `customer_input.php?mode=edit_reservation&id=${id}`;
}
