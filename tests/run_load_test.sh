#!/bin/bash

# WhatsApp Mass Sending System - Load Test Runner
# This script runs comprehensive load tests to determine system capacity

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Configuration
BASE_URL="http://127.0.0.1:8000"
MAX_USERS=${1:-5000}
TEST_DURATION=${2:-60}
MONITOR_INTERVAL=${3:-1}

echo -e "${BLUE}🚀 WhatsApp Mass Sending System - Load Test${NC}"
echo -e "${BLUE}============================================${NC}"
echo ""
echo -e "📊 Test Configuration:"
echo -e "   • Base URL: ${BASE_URL}"
echo -e "   • Max Users: ${MAX_USERS}"
echo -e "   • Duration: ${TEST_DURATION}s"
echo -e "   • Monitor Interval: ${MONITOR_INTERVAL}s"
echo ""

# Check if required files exist
if [ ! -f "load_test.php" ]; then
    echo -e "${RED}❌ load_test.php not found!${NC}"
    exit 1
fi

if [ ! -f "system_monitor.php" ]; then
    echo -e "${RED}❌ system_monitor.php not found!${NC}"
    exit 1
fi

if [ ! -f "whatsapp_api_test.php" ]; then
    echo -e "${RED}❌ whatsapp_api_test.php not found!${NC}"
    exit 1
fi

# Create results directory
mkdir -p results
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
RESULTS_DIR="results/load_test_${TIMESTAMP}"
mkdir -p "$RESULTS_DIR"

echo -e "${YELLOW}📁 Results will be saved to: ${RESULTS_DIR}${NC}"
echo ""

# Function to cleanup on exit
cleanup() {
    echo -e "\n${YELLOW}🛑 Stopping all tests...${NC}"
    if [ ! -z "$MONITOR_PID" ]; then
        kill $MONITOR_PID 2>/dev/null || true
    fi
    if [ ! -z "$LOAD_TEST_PID" ]; then
        kill $LOAD_TEST_PID 2>/dev/null || true
    fi
    echo -e "${GREEN}✅ Cleanup completed${NC}"
    exit 0
}

# Set up signal handlers
trap cleanup SIGINT SIGTERM

# Start system monitoring in background
echo -e "${BLUE}🔍 Starting system monitoring...${NC}"
php system_monitor.php $MONITOR_INTERVAL > "$RESULTS_DIR/system_monitor.log" 2>&1 &
MONITOR_PID=$!

# Wait a moment for monitoring to start
sleep 2

# Run WhatsApp API tests first
echo -e "${BLUE}📱 Running WhatsApp API tests...${NC}"
php whatsapp_api_test.php > "$RESULTS_DIR/whatsapp_api_test.log" 2>&1

# Run main load test
echo -e "${BLUE}👥 Running main load test...${NC}"
php load_test.php $MAX_USERS $TEST_DURATION > "$RESULTS_DIR/load_test.log" 2>&1 &
LOAD_TEST_PID=$!

# Wait for load test to complete
wait $LOAD_TEST_PID

# Stop system monitoring
echo -e "${YELLOW}🛑 Stopping system monitoring...${NC}"
kill $MONITOR_PID 2>/dev/null || true

# Generate final reports
echo -e "${BLUE}📊 Generating reports...${NC}"

# Generate system performance report
if [ -f "/tmp/system_monitor.log" ]; then
    cp /tmp/system_monitor.log "$RESULTS_DIR/"
    php system_monitor.php --report > "$RESULTS_DIR/system_performance_report.txt"
fi

# Generate combined report
cat > "$RESULTS_DIR/combined_report.txt" << EOF
WhatsApp Mass Sending System - Load Test Report
Generated: $(date)
Test Duration: ${TEST_DURATION}s
Max Users: ${MAX_USERS}

=== LOAD TEST RESULTS ===
EOF

if [ -f "$RESULTS_DIR/load_test.log" ]; then
    cat "$RESULTS_DIR/load_test.log" >> "$RESULTS_DIR/combined_report.txt"
fi

echo "" >> "$RESULTS_DIR/combined_report.txt"
echo "=== WHATSAPP API TEST RESULTS ===" >> "$RESULTS_DIR/combined_report.txt"

if [ -f "$RESULTS_DIR/whatsapp_api_test.log" ]; then
    cat "$RESULTS_DIR/whatsapp_api_test.log" >> "$RESULTS_DIR/combined_report.txt"
fi

echo "" >> "$RESULTS_DIR/combined_report.txt"
echo "=== SYSTEM PERFORMANCE REPORT ===" >> "$RESULTS_DIR/combined_report.txt"

if [ -f "$RESULTS_DIR/system_performance_report.txt" ]; then
    cat "$RESULTS_DIR/system_performance_report.txt" >> "$RESULTS_DIR/combined_report.txt"
fi

# Display summary
echo -e "${GREEN}✅ Load test completed!${NC}"
echo ""
echo -e "${BLUE}📁 Results saved to: ${RESULTS_DIR}${NC}"
echo ""
echo -e "${YELLOW}📋 Quick Summary:${NC}"
echo -e "   • Load test log: ${RESULTS_DIR}/load_test.log"
echo -e "   • API test log: ${RESULTS_DIR}/whatsapp_api_test.log"
echo -e "   • System monitor log: ${RESULTS_DIR}/system_monitor.log"
echo -e "   • Combined report: ${RESULTS_DIR}/combined_report.txt"
echo -e "   • System performance: ${RESULTS_DIR}/system_performance_report.txt"
echo ""

# Show key metrics from the logs
echo -e "${BLUE}🔍 Key Metrics:${NC}"

if [ -f "$RESULTS_DIR/load_test.log" ]; then
    echo -e "${YELLOW}Load Test Results:${NC}"
    grep -E "(Maximum supported RPS|Maximum concurrent users|Recommended max)" "$RESULTS_DIR/load_test.log" | head -5
fi

if [ -f "$RESULTS_DIR/whatsapp_api_test.log" ]; then
    echo -e "${YELLOW}WhatsApp API Results:${NC}"
    grep -E "(Maximum bulk size|Maximum sending rate|Maximum concurrent sessions)" "$RESULTS_DIR/whatsapp_api_test.log" | head -5
fi

if [ -f "$RESULTS_DIR/system_performance_report.txt" ]; then
    echo -e "${YELLOW}System Performance:${NC}"
    grep -E "(Average|Peak|Min)" "$RESULTS_DIR/system_performance_report.txt" | head -5
fi

echo ""
echo -e "${GREEN}🎉 Load test analysis complete!${NC}"
echo -e "${BLUE}💡 Check the detailed reports in ${RESULTS_DIR}/ for full analysis${NC}"
