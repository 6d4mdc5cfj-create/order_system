// ===============================
// 顧客・予約フォーム共通スクリプト
// ===============================

const MODE = window.MODE || "new_customer";
const PEOPLE_DATA = window.PEOPLE_DATA || [];

// ===============================
// 施術エリア表示制御
// ===============================
const rental = document.querySelector('[name="service_rental"]');
const kitsuke = document.querySelector('[name="service_kitsuke"]');
const hairmake = document.querySelector('[name="service_hairmake"]');
const peopleArea = document.getElementById('people_area');

function togglePeopleArea() {
    const show = (rental?.checked || kitsuke?.checked || hairmake?.checked);
    peopleArea.style.display = show ? 'block' : 'none';
}

[rental, kitsuke, hairmake].forEach(cb => {
    if (cb) cb.addEventListener('change', togglePeopleArea);
});
togglePeopleArea();

// ===============================
// 同行者ブロック生成関数
// ===============================
function createPersonBlock(num, data = {}) {
    // 年齢セレクト生成（先頭に「-」を追加）
    let ageOptions = `<option value="" ${!data.age ? "selected" : ""}>-</option>`;
    for (let i = 0; i <= 19; i++) {
        ageOptions += `<option value="${i}" ${data.age == i ? "selected" : ""}>${i}</option>`;
    }
    ageOptions += `
    <option ${data.age == "20代" ? "selected" : ""}>20代</option>
    <option ${data.age == "30代" ? "selected" : ""}>30代</option>
    <option ${data.age == "40代" ? "selected" : ""}>40代</option>
    <option ${data.age == "50代" ? "selected" : ""}>50代</option>
    <option ${data.age == "60代" ? "selected" : ""}>60代</option>
    <option ${data.age == "70以上" ? "selected" : ""}>70以上</option>`;

    return `
  <div class="person-block border rounded p-2 mb-3">
    <button type="button" class="btn btn-sm btn-danger remove-person float-end">削除</button>
    <h6 class="person-number">${num}人目</h6>

    <!-- お名前 -->
    <div class="mb-2">
      <label>お名前</label>
      <input type="text" class="form-control" name="people[${num}][name]" value="${data.name || ''}">
    </div>

    <!-- ふりがな -->
    <div class="mb-2">
      <label>ふりがな</label>
      <input type="text" class="form-control" name="people[${num}][furigana]" value="${data.furigana || ''}">
    </div>

    <!-- 性別 -->
    <div class="mb-2">
      <label>性別</label>
      <select class="form-select" name="people[${num}][gender]">
        <option value="">選択</option>
        <option ${data.gender == "男性" ? "selected" : ""}>男性</option>
        <option ${data.gender == "女性" ? "selected" : ""}>女性</option>
        <option ${data.gender == "その他" ? "selected" : ""}>その他</option>
      </select>
    </div>

    <!-- 年齢 -->
    <div class="mb-2">
      <label>年齢</label>
      <select class="form-select" name="people[${num}][age]">${ageOptions}</select>
    </div>

    <!-- レンタル・着付 -->
    <div class="mb-2">
      <label>レンタル・着付</label>
      <select class="form-select rental-kitsuke" name="people[${num}][rental_kitsuke]">
        <option value="無し" ${data.rental_kitsuke === "無し" ? "selected" : ""}>無し</option>
        <option value="レンタル&着付" ${data.rental_kitsuke === "レンタル&着付" ? "selected" : ""}>レンタル&着付</option>
        <option value="レンタルのみ" ${data.rental_kitsuke === "レンタルのみ" ? "selected" : ""}>レンタルのみ</option>
        <option value="持ち込み着付" ${data.rental_kitsuke === "持ち込み着付" ? "selected" : ""}>持ち込み着付</option>
      </select>
    </div>

    <!-- 持ち出しレンタル -->
    <div class="mb-2 rental-options" style="display:none;">
      <label>持ち出しレンタル</label>
      <select class="form-select" name="people[${num}][rental_out]">
        <option value="持ち出し有り" ${data.rental_out === "持ち出し有り" ? "selected" : ""}>持ち出し有り</option>
        <option value="スタジオ内のみ" ${data.rental_out === "スタジオ内のみ" ? "selected" : ""}>スタジオ内のみ</option>
        <option value="未定" ${data.rental_out === "未定" ? "selected" : ""}>未定</option>
      </select>
    </div>

    <!-- ヘアメイク -->
    <div class="mb-2">
      <label>ヘアメイク</label>
      <select class="form-select" name="people[${num}][hairmake]">
        <option value="無し" ${data.hairmake === "無し" ? "selected" : ""}>無し</option>
        <option value="ヘアセット" ${data.hairmake === "ヘアセット" ? "selected" : ""}>ヘアセット</option>
        <option value="ヘアセット&メイク" ${data.hairmake === "ヘアセット&メイク" ? "selected" : ""}>ヘアセット&メイク</option>
      </select>
    </div>

    <!-- 身長 -->
    <div class="mb-2 height-area" style="display:none;">
      <label>身長</label>
      <input type="text" class="form-control" name="people[${num}][height]" value="${data.height || ''}">
    </div>

    <!-- 足サイズ -->
    <div class="mb-2 foot-area" style="display:none;">
      <label>足サイズ</label>
      <input type="text" class="form-control" name="people[${num}][foot_size]" value="${data.foot_size || ''}">
    </div>

    <!-- 備考 -->
    <div class="mb-2">
      <label>備考</label>
      <textarea class="form-control" name="people[${num}][note]" rows="2" placeholder="特記事項などを入力">${data.note || ''}</textarea>
    </div>
  </div>`;
}

// ===============================
// 初期同行者設定
// ===============================
const list = document.getElementById("people_list");
let personCount = 0;

if (PEOPLE_DATA && PEOPLE_DATA.length > 0) {
    PEOPLE_DATA.forEach(p => {
        personCount++;
        list.insertAdjacentHTML("beforeend", createPersonBlock(personCount, p));
    });
} else {
    personCount++;
    list.insertAdjacentHTML("beforeend", createPersonBlock(personCount));
}
attachHandlers();

// ===============================
// 同行者追加・削除
// ===============================
document.getElementById("add_person").addEventListener("click", () => {
    personCount++;
    list.insertAdjacentHTML("beforeend", createPersonBlock(personCount));
    attachHandlers();
});

// ===============================
// ハンドラ登録・動的制御
// ===============================
function attachHandlers() {
    // 削除
    list.querySelectorAll(".remove-person").forEach(btn => {
        btn.onclick = () => {
            btn.closest(".person-block").remove();
            updatePersonNumbers();
        };
    });

    // レンタル関連制御
    list.querySelectorAll(".rental-kitsuke").forEach(select => {
        select.onchange = () => toggleRentalFields(select.closest(".person-block"));
        toggleRentalFields(select.closest(".person-block"));
    });
}

// ===============================
// レンタル連動表示
// ===============================
function toggleRentalFields(block) {
    const rentalSelect = block.querySelector(".rental-kitsuke");
    const show = ["レンタル&着付", "レンタルのみ"].includes(rentalSelect.value);
    block.querySelector(".rental-options").style.display = show ? "block" : "none";
    block.querySelector(".height-area").style.display = show ? "block" : "none";
    block.querySelector(".foot-area").style.display = show ? "block" : "none";
}

// ===============================
// 通し番号更新
// ===============================
function updatePersonNumbers() {
    const blocks = list.querySelectorAll(".person-block");
    personCount = 0;
    blocks.forEach((block, i) => {
        personCount = i + 1;
        block.querySelector(".person-number").textContent = `${personCount}人目`;
    });
}
