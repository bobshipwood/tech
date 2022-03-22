[TOC]

# 1 备份

### 1 基础语法

mysqldump -u用户名 -p'密码' -B （-F） （-d）（-t） 数据库名（空格 数据库名）  > 备份的文件名

### 2 各参数解释

#### 1 -B

把数据库的名字也备份出来

#### 2 -d

只备份表结构

#### 3 -t 

只备份数据

#### 4 -F

刷新binlog参数

#### 5 --single-transaction(重点)

适合innodb事务数据库备份，原理是将本次会话的隔离级别为 REPEATABLE READ，以确保本次备份不会看到已经提交了的数据

#### 6 --master-data=1(重点)

拆分binlog，用于主从复制，无需在从服务器输入位置信息。（等于2的时候仅仅是注释CHANGE，不执行CHANGE MASTER TO MASTER_LOG_FILE = 'mysql-bin.000020',MASTER_LOG_POS=1191）

CHANGE MASTER TO
MASTER_HOST = '10.0.0.2',
MASTER_PORT = 3306,
MASTER_USER = 'REP',
MASTER_PASSWORD  '1223332'；
*（MASTER_LOG_FILE = 'mysql-bin.000003',*
*MASTER_LOG_POS = 333;）*   

# 2 恢复

### 1 加库名

mysql -uroot -p'1223456' 数据库名<'*.sql';（备份的sql文件不加-B的时候，必须加库名）

### 2 不加库名

mysql -uroot -p'1223456' <'*.sql';