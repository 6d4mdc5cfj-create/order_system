#!/bin/bash
# 保存を検知して自動でGitHubにpushするスクリプト

WATCH_DIR="/Volumes/UserData/haru/Nextcloud/site_new/order_system"

fswatch -o "$WATCH_DIR" | while read num
do
    git add .
    git commit -m "auto commit on $(date '+%Y-%m-%d %H:%M:%S')" >/dev/null 2>&1
    git push origin main >/dev/null 2>&1
    echo "✅ Auto pushed at $(date)"
done

