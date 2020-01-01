[TOC]

# 1 普通查询日志

general_log = 1
genrral_log_file = /data/3306/oldboy.log

# 2 错误日志

log-error="D:/mysql56/error.err"

# 3 二进制日志

log-bin=/data/3306/mysql-bin
bin_log_format = "STATEMENT"(默认值)
bin_log_format = "ROW"
bin_log_format = "MIXED"

# 4 慢查询日志

slow-query-log=1
slow_query_log_file="D:/mysql56/slow.log"
long_query_time=5  
log_queries_not_using_indexes