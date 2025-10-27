#!/bin/bash

# Optimized Queue Worker Script
# This script runs the queue worker with comprehensive monitoring and auto-resume functionality

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

# Configuration
MEMORY_LIMIT="256M"
MAX_EXECUTION_TIME=300
MAX_MEMORY_MB=200
RESTART_THRESHOLD_MB=180
MONITOR_INTERVAL=30
MAX_RESTARTS=5
RESUME_CHECK_INTERVAL=60 # Check for paused campaigns every 60 seconds

echo -e "${BLUE}🚀 Starting Optimized Queue Worker${NC}"
echo -e "${BLUE}===================================${NC}"

# Function to log with timestamp
log() {
    echo -e "${PURPLE}[$(date '+%Y-%m-%d %H:%M:%S')]${NC} $1"
}

# Function to cleanup on exit
cleanup() {
    log "${YELLOW}🛑 Stopping queue worker...${NC}"
    if [ ! -z "$QUEUE_PID" ]; then
        kill $QUEUE_PID 2>/dev/null || true
        wait $QUEUE_PID 2>/dev/null || true
    fi
    log "${GREEN}✅ Queue worker stopped${NC}"
    exit 0
}

# Set up signal handlers
trap cleanup SIGINT SIGTERM

# Check system resources
log "${BLUE}🔍 Checking system resources...${NC}"
TOTAL_MEMORY=$(free -m | awk 'NR==2{printf "%.0f", $2}')
AVAILABLE_MEMORY=$(free -m | awk 'NR==2{printf "%.0f", $7}')
log "   • Total Memory: ${TOTAL_MEMORY}MB"
log "   • Available Memory: ${AVAILABLE_MEMORY}MB"

if [ $AVAILABLE_MEMORY -lt 100 ]; then
    log "${RED}❌ Insufficient memory available (${AVAILABLE_MEMORY}MB < 100MB)${NC}"
    exit 1
fi

# Set environment variables
export MEMORY_LIMIT=$MEMORY_LIMIT
export MAX_EXECUTION_TIME=$MAX_EXECUTION_TIME

# Test PHP configuration
log "${BLUE}🧪 Testing PHP configuration...${NC}"
php -d memory_limit=$MEMORY_LIMIT -r "
    echo '   • Memory Limit: ' . ini_get('memory_limit') . PHP_EOL;
    echo '   • Max Execution Time: ' . ini_get('max_execution_time') . 's' . PHP_EOL;
    echo '   • GC Probability: ' . ini_get('gc_probability') . PHP_EOL;
    echo '   • GC Divisor: ' . ini_get('gc_divisor') . PHP_EOL;
"

# Test Laravel queue command
log "${BLUE}🧪 Testing Laravel queue command...${NC}"
if ! php artisan queue:work --help > /dev/null 2>&1; then
    log "${RED}❌ Laravel queue command not available${NC}"
    exit 1
fi
log "${GREEN}✅ Laravel queue command available${NC}"

# Initialize counters
RESTART_COUNT=0
START_TIME=$(date +%s)
LAST_RESUME_CHECK=$(date +%s)

# Function to start queue worker
start_worker() {
    log "${GREEN}🚀 Starting queue worker...${NC}"
    
    php -d memory_limit=$MEMORY_LIMIT \
        -d max_execution_time=$MAX_EXECUTION_TIME \
        -d gc_probability=1 \
        -d gc_divisor=100 \
        artisan queue:work \
        --verbose \
        --tries=3 \
        --timeout=300 \
        --memory=200 \
        --sleep=3 \
        --max-jobs=10 \
        --max-time=3600 &
    
    QUEUE_PID=$!
    
    if [ $? -eq 0 ] && kill -0 $QUEUE_PID 2>/dev/null; then
        log "${GREEN}✅ Queue worker started (PID: $QUEUE_PID)${NC}"
        log "${BLUE}💡 Configuration:${NC}"
        log "   • Memory limit: $MEMORY_LIMIT"
        log "   • Max execution time: ${MAX_EXECUTION_TIME}s"
        log "   • Max jobs per worker: 10"
        log "   • Worker timeout: 300s"
        log "   • Restart threshold: ${RESTART_THRESHOLD_MB}MB"
        log "   • Resume check interval: ${RESUME_CHECK_INTERVAL}s"
        return 0
    else
        log "${RED}❌ Failed to start queue worker${NC}"
        return 1
    fi
}

# Function to check and resume paused campaigns
check_paused_campaigns() {
    local current_time=$(date +%s)
    local time_since_last_check=$((current_time - LAST_RESUME_CHECK))
    
    if [ $time_since_last_check -ge $RESUME_CHECK_INTERVAL ]; then
        log "${BLUE}🔍 Checking for paused campaigns to resume...${NC}"
        php artisan campaigns:resume-paused
        LAST_RESUME_CHECK=$current_time
    fi
}

# Start the worker
if ! start_worker; then
    log "${RED}❌ Failed to start queue worker${NC}"
    exit 1
fi

# Monitor the process
log "${BLUE}📊 Starting monitoring...${NC}"
while true; do
    sleep $MONITOR_INTERVAL
    
    # Check if process is still running
    if ! kill -0 $QUEUE_PID 2>/dev/null; then
        log "${RED}❌ Queue worker process died unexpectedly${NC}"
        
        # Check if we've exceeded max restarts
        if [ $RESTART_COUNT -ge $MAX_RESTARTS ]; then
            log "${RED}❌ Maximum restart limit reached (${MAX_RESTARTS})${NC}"
            exit 1
        fi
        
        # Restart the worker
        RESTART_COUNT=$((RESTART_COUNT + 1))
        log "${YELLOW}🔄 Restarting worker (attempt ${RESTART_COUNT}/${MAX_RESTARTS})...${NC}"
        
        sleep 5
        if start_worker; then
            log "${GREEN}✅ Worker restarted successfully${NC}"
        else
            log "${RED}❌ Failed to restart worker${NC}"
            exit 1
        fi
        continue
    fi
    
    # Check memory usage
    MEMORY_USAGE=$(ps -o pid,rss,pcpu,comm -p $QUEUE_PID 2>/dev/null | tail -1 | awk '{print $2}')
    if [ ! -z "$MEMORY_USAGE" ] && [ "$MEMORY_USAGE" != "RSS" ]; then
        MEMORY_MB=$((MEMORY_USAGE / 1024))
        UPTIME=$(( $(date +%s) - START_TIME ))
        
        log "${BLUE}📊 Status: PID=$QUEUE_PID, Memory=${MEMORY_MB}MB, Uptime=${UPTIME}s, Restarts=${RESTART_COUNT}${NC}"
        
        # Check if memory usage is too high
        if [ $MEMORY_MB -gt $RESTART_THRESHOLD_MB ]; then
            log "${YELLOW}⚠️  High memory usage detected (${MEMORY_MB}MB > ${RESTART_THRESHOLD_MB}MB)${NC}"
            
            # Check if we've exceeded max restarts
            if [ $RESTART_COUNT -ge $MAX_RESTARTS ]; then
                log "${RED}❌ Maximum restart limit reached (${MAX_RESTARTS})${NC}"
                exit 1
            fi
            
            # Restart the worker
            RESTART_COUNT=$((RESTART_COUNT + 1))
            log "${YELLOW}🔄 Restarting worker due to high memory usage (attempt ${RESTART_COUNT}/${MAX_RESTARTS})...${NC}"
            
            kill $QUEUE_PID 2>/dev/null || true
            wait $QUEUE_PID 2>/dev/null || true
            sleep 5
            
            if start_worker; then
                log "${GREEN}✅ Worker restarted successfully${NC}"
                START_TIME=$(date +%s)  # Reset uptime counter
            else
                log "${RED}❌ Failed to restart worker${NC}"
                exit 1
            fi
        fi
    else
        log "${YELLOW}⚠️  Could not get memory usage for PID $QUEUE_PID${NC}"
    fi
    
    # Check for paused campaigns to resume
    check_paused_campaigns
done

