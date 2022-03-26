[TOC]

## 1 安装及忘记密码

### 1 data文件夹设置（多实例）

/data/3306/my.cnf    3306的配置文件

/data/3306/mysql   3306实例的启动文件（自制造）

/data/3307/my.cnf

/data/3307/mysql

### 2 给data文件夹下的mysql脚本授权（+x）

find /data/ -type f -name "mysql" | xargs ls -l

find /data/ -type f -name "mysql" | xargs chmod +x

find /data/ -type f -name "mysql" | xargs ls -l

### 3 初始化命令(往data文件夹放入初始化的数据库等文件)

./mysql_install_db  --basedir=/application/mysql  --datadir=/data/3306/data  --user=mysql

./mysql_install_db  --basedir=/application/mysql  --datadir=/data/3307/data  --user=mysql

### 4 mysql服务的启动及关闭

#### 1 单实例启动

1 /etc/init.d/mysqld （start）(/etc/init.d/mysqld start)(官方提供的脚本)
2 mysqld_safe --user=mysql &(初始化数据库时候用)

#### 2 单实例关闭
1 mysqladmin -uroot -pXXX shutdown
2 /etc/init.d/mysqld stop

#### 3 多实例下的启动与关闭实质

启动实质

mysql_safe --defaults-file=/data/3306/my.cnf 2>&1 > /dev/null &

mysql_safe --defaults-file=/data/3307/my.cnf 2>&1 > /dev/null &

关闭实质

mysqladmin -u root -pXXX -S /data/3306/mysql.sock shutdown

mysqladmin -u root -pXXX -S /data/3307/mysql.sock shutdown

### 5 登录数据库

mysql -uroot -pXXX -S /data/3307/mysql.sock(本机)

mysql -h 127.0.0.1 -uroot -pXXX -P3307(远程)



### 6 设置密码

#### 1 mysqladmin -u root （-p xxx） password‘XXXX’

#### 2 mysqladmin -u root （-p xxx） password‘XXXX’ -S /data/3309/mysql.sock（多实例）

#### 3 （忘记密码方法）update mysql.user set password = password("123456") where user = 'root' and host='localhost' ； flush privileges

### 7 忘记密码

#### 1 单实例下

1 /etc/init.d/mysqld stop

2 mysqld_safe --skip-grant-tables --user=mysql &（直接登录）

3 update mysql.user set password = password("ol222") where user = 'root' and host = 'localhost';

4 flush privileges;

5 mysqladmin -uroot -pol222 shutdown(不能用/etc/init.d/mysqld stop 因为启动都没有用这个命令，且没有sock文件)

#### 2 多实例

1 killall mysqld

2 mysqld_safe --defaults-file=/data/3308/my.cnf --skip-grant-tables --user=mysql &

3 mysql -u root -p -S /data/3306/mysql.sock(登录时候空密码)