-- MySQL Optimization for Laravel WhatsApp Project
-- Run these commands in MySQL to optimize performance

-- Increase sort buffer size to handle large ORDER BY operations
SET GLOBAL sort_buffer_size = 16777216; -- 16MB

-- Increase read buffer size
SET GLOBAL read_buffer_size = 8388608; -- 8MB

-- Increase read rnd buffer size
SET GLOBAL read_rnd_buffer_size = 16777216; -- 16MB

-- Increase join buffer size
SET GLOBAL join_buffer_size = 8388608; -- 8MB

-- Increase tmp table size
SET GLOBAL tmp_table_size = 67108864; -- 64MB

-- Increase max heap table size
SET GLOBAL max_heap_table_size = 67108864; -- 64MB

-- Optimize query cache (if enabled)
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL query_cache_limit = 2097152; -- 2MB

-- Show current settings
SHOW VARIABLES LIKE 'sort_buffer_size';
SHOW VARIABLES LIKE 'read_buffer_size';
SHOW VARIABLES LIKE 'read_rnd_buffer_size';
SHOW VARIABLES LIKE 'join_buffer_size';
SHOW VARIABLES LIKE 'tmp_table_size';
SHOW VARIABLES LIKE 'max_heap_table_size';
