[TOC]

## 1 切分binlog

mysqladmin -uroot -p123454 flush-log；

把现在还在写的和之前的bin.000001分割出来(分割成另外的.000002,保留原来的.000001文件)

## 2 恢复库

mysql -uroot -p123456 oldboy < /opt/old_bak.sql

## 3 用mysqlbinlog转换成sql文件

#### 1 mysqlbinlog -d oldboy mysqlbin_oldboy.000001 > bin.sql

#### 2  -d  截取指定库的binlog

#### 3 按照位置截取 （-r   --result-file=name  将输入转储到文件 ）

mysqlbinlog -d oldboy mysqlbin_oldboy.000001 --start-position=360 --stop-position=430 -r position.sql

#### 4 按照时间点截取

mysqlbinlog -d oldboy mysqlbin_oldboy.000001 --start-datetime=‘2019-01-01 22:22:22’ --stop-datetime=‘2019-02-02 22:22:22’ -r time.sql

## 4 恢复bin.sql

mysql -uroot -p123456 oldboy < /opt/bin.sql

