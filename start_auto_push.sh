#!/bin/bash
# 自動push監視スクリプトをバックグラウンドで起動する

cd /Volumes/UserData/haru/Nextcloud/site_new/order_system

# すでに動いているauto_push.shを停止（重複防止）
pkill -f auto_push.sh

# 自動pushをバックグラウンドで起動
nohup ./auto_push.sh > auto_push.log 2>&1 &

echo "🚀 自動pushスクリプトを起動しました！（ログ: auto_push.log）"

