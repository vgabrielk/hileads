#!/bin/bash

echo "🚀 Setting up Laravel Scheduler for automatic subscription activation..."

# Get the current directory
PROJECT_PATH=$(pwd)

echo "📁 Project path: $PROJECT_PATH"

# Create the cron entry
CRON_ENTRY="* * * * * cd $PROJECT_PATH && php artisan schedule:run >> /dev/null 2>&1"

echo "⏰ Adding cron entry: $CRON_ENTRY"

# Add to crontab
(crontab -l 2>/dev/null; echo "$CRON_ENTRY") | crontab -

echo "✅ Cron job added successfully!"

echo ""
echo "🔍 To verify the cron job is working:"
echo "1. Check crontab: crontab -l"
echo "2. Check scheduler logs: tail -f storage/logs/scheduler.log"
echo "3. Test manually: php artisan scheduler:check"
echo ""
echo "🎯 The scheduler will now:"
echo "• Process pending subscriptions every 2 minutes"
echo "• Update entitlements every 5 minutes"
echo "• Clean up old subscriptions every hour"
echo ""
echo "✨ Your subscription activation is now fully automated!"
