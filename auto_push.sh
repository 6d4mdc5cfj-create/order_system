#!/bin/bash
# ===== 自動pushスクリプト =====

WATCH_DIR="/Volumes/UserData/haru/Nextcloud/site_new/order_system"
cd "$WATCH_DIR" || exit 1   # ← ココ重要！

echo "🟢 自動push監視を開始しました（終了するには Ctrl + C）"

fswatch -o "$WATCH_DIR" | while read num
do
    echo "🔍 変更検知 at $(date)"
    git status
    git add --all
    git commit -m "auto commit on $(date '+%Y-%m-%d %H:%M:%S')"
    git push origin main
    echo "✅ Auto pushed at $(date)"
done
