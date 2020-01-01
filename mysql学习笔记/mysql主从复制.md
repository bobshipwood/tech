[TOC]

# 1 mysql 主从复制步骤

##　１说明

采用单机多实例方法配置，master主机成为m机，slave从机成为s机。

## 2 先行配置

### 1 m机配置

####　1  开启log-bin server-id

在my.cnf中打开

#### 2  授权s机用户

grant replication slave on *.* to '用户名'@‘10.0.0.%’ identified by ‘密码’

### 2 s机配置

#### 1 开启server-id

在my.cnf中打开

## 3 拷贝主库到从库

mysqldump -uroot -p‘123445’ -S /data/3306/mysql.sock -A -B --master-data=1  >rep.sql(已经记录了分割点的位置)

mysql -uroot -p1234455 -S /data/3307/mysql.sock <rep.sql

## 4  从库进行配置

### 1 先登录从，进行change master

CHANGE MASTER TO
MASTER_HOST = '10.0.0.2',
MASTER_PORT = 3306,
MASTER_USER = 'REP',
MASTER_PASSWORD  '1223332';
*（MASTER_LOG_FILE = 'mysql-bin.000003',*
*MASTER_LOG_POS = 333;*）

### 2 开启主从复制（start slave）      

在从库中 开启主从

## 5 检查主从是否生效

### 1 show slave status

检测从库的线程状态 io 和 sql 两个线程是否为yes

### 2 实际情况在主机中插入一条语句试一试

# 2 mysql 主从复制原理

## 1 开启主从复制

Slave服务器上执行start slave，开启主从复制。

## 2 s服务器间的io线程发送请求（change master to）

s服务器的io线程会通过在m服务器上已经授权的用户去请求连接m服务器，并请求从binlog文件指定位置（日志文件名和位置就是在配置主从服务时执行change master命令时指定的）传输

## 3 m服务器的io线程接收请求，向s服务器的io线程返回（日志内容和位置点）

m服务器接收s服务器的io线程请求后，m服务器的复制io线程根据s服务器的io请求的信息去读取指定位置之后的binlog日志信息。返回的信息除了日志内容外，还有本次返回内容后在m服务器新的binlog文件及binlog文件下一个指定更新位置。

## 4 s服务器的io线程接受请求后（relay-log,master_info）

s服务器的io线程获取到m服务器的io线程发送的日志内容，日志及位置点后，将日志内容写进s服务器的Relay LOG的最末端。并将新的binlog文件名和位置记录到master_info文件中，以便下一次读取时候，能告诉m服务器从哪一个文件那一段开始请求最新的binlog日志内容。

## 5 s服务器的sql线程处理

s服务器的sql线程会实时的检测本地relay-log中新增的日志内容，然后将其解析成sql语句，并在自身的s服务器上按照顺序去执行sql语句，应用完毕后清理应用过的日志。

cat relay-log.info

/data/3307/relay-bin.000005(从库读到哪儿了)

340

mysql-bin.000004（同master.info，主库传过来的从库信息）

420

# 3 从库提升为主库的步骤（主库坏了）

## 1 确保所有的relay log 全部更新完毕
在从库执行 stop slave io_thread;show processlist;
直到看到 has read all relay log;表示从库更新完毕；

## 2 登录从库执行stop slave，reset master
mysql -uroot -p123445 -S /data/mysql.sock
stop slave;
reset master;
quit;

## 3 删除master.info,relay-log.info文件

rm -rf master.info relay-log.info

## 4 检测my.cnf
### 1 开启log-bin

### 2 如果存在log-slave-updates read-only等一定要注释它



## 5 如果主库服务器没当，则拉主库的binlog来补全从库。(补救措施)

## 6 其他从库操作
stop slave；
change master to master_host = '192.168.1.32';//如果不同步就指定位置点
start slave；



# 4 mysql 关于主从复制的其他信息

## 1 从库需要打开binlog的情况

### 1 级联
### 2 从库做备份
## 2 从库的binlog配置（log-slave-updates）

my.cnf中
log-bin = /data/3307/mysql-bin
log-slave-updates

## 2 my.cnf 中的[mysqld] 加入read-only（只允许从服务器线程和具有super权限如root的更新 ）




