#!/bin/bash

# Production Queue Worker Starter
# This script provides options to start the queue worker with different configurations

set -e

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
PURPLE='\033[0;35m'
NC='\033[0m' # No Color

echo -e "${BLUE}🚀 Queue Worker Starter${NC}"
echo -e "${BLUE}======================${NC}"
echo ""
echo -e "${YELLOW}Available options:${NC}"
echo "1. Simple Queue Worker (basic monitoring)"
echo "2. Optimized Queue Worker (memory optimization)"
echo "3. Robust Queue Worker (comprehensive monitoring)"
echo "4. Custom Queue Worker (manual configuration)"
echo ""

read -p "Choose an option (1-4): " choice

case $choice in
    1)
        echo -e "${GREEN}Starting Simple Queue Worker...${NC}"
        ./run_queue_simple.sh
        ;;
    2)
        echo -e "${GREEN}Starting Optimized Queue Worker...${NC}"
        ./run_queue_optimized.sh
        ;;
    3)
        echo -e "${GREEN}Starting Robust Queue Worker...${NC}"
        ./run_queue_robust.sh
        ;;
    4)
        echo -e "${GREEN}Starting Custom Queue Worker...${NC}"
        echo "Enter custom memory limit (e.g., 256M):"
        read -p "Memory limit: " memory_limit
        echo "Enter max jobs per worker (e.g., 10):"
        read -p "Max jobs: " max_jobs
        echo "Enter sleep time in seconds (e.g., 3):"
        read -p "Sleep time: " sleep_time
        
        echo -e "${BLUE}Starting with custom configuration...${NC}"
        php -d memory_limit=$memory_limit \
            -d max_execution_time=300 \
            -d gc_probability=1 \
            -d gc_divisor=100 \
            artisan queue:work \
            --verbose \
            --tries=3 \
            --timeout=300 \
            --memory=200 \
            --sleep=$sleep_time \
            --max-jobs=$max_jobs \
            --max-time=3600
        ;;
    *)
        echo -e "${RED}Invalid option. Exiting...${NC}"
        exit 1
        ;;
esac
