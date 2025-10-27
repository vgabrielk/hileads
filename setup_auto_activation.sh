#!/bin/bash

echo "🚀 Setting up automatic subscription activation..."

# Get the current directory
PROJECT_PATH=$(pwd)

echo "📁 Project path: $PROJECT_PATH"

# Create the cron entry for the PHP script
CRON_ENTRY="*/2 * * * * cd $PROJECT_PATH && php auto_activate_subscriptions.php >> storage/logs/auto_activation.log 2>&1"

echo "⏰ Adding cron entry: $CRON_ENTRY"

# Add to crontab
(crontab -l 2>/dev/null; echo "$CRON_ENTRY") | crontab -

echo "✅ Auto-activation configured successfully!"

echo ""
echo "🔍 To verify it's working:"
echo "1. Check crontab: crontab -l"
echo "2. Check logs: tail -f storage/logs/auto_activation.log"
echo "3. Test manually: php auto_activate_subscriptions.php"
echo ""
echo "🎯 The system will now:"
echo "• Check for pending subscriptions every 2 minutes"
echo "• Automatically activate paid subscriptions"
echo "• Log all activity to storage/logs/auto_activation.log"
echo ""
echo "✨ Your subscription activation is now fully automated!"
