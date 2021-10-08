[toc]

# 1 安装

## 1  https://www.elastic.co/cn/downloads/logstash 

选择yum下载

## 2 rpm --import https://artifacts.elastic.co/GPG-KEY-elasticsearch

引入GPGKEY

## 3 设置yum源（/etc/yum.repos.d/logstash.repo）

[logstash-7.x]
name=Elastic repository for 7.x packages
baseurl=https://artifacts.elastic.co/packages/7.x/yum
gpgcheck=1
gpgkey=https://artifacts.elastic.co/GPG-KEY-elasticsearch
enabled=1
autorefresh=1
type=rpm-md

## 4 yum安装mysql-java插件及logstash

yum install logstash  yum install mysql-connector-java

## 5  logstash安装目录

/usr/share/logstash   运行bin所在目录

/etc/logstash/      配置文件所在目录

[toc]

# 2 命令行运行命令

## 1 测试配置文件命令 

./bin/logstash -f /etc/logstash/testsql.conf --config.test_and_exit

## 2 直接命令行运行

 ./bin/logstash -f /etc/logstash/testsql.conf 

## 3 腾讯云命令例子

```
nohup ./bin/logstash -f ~/*.conf 2>&1 >/dev/null &
```

| 命令            | 标准输出 | 错误输出 |
| --------------- | -------- | -------- |
| >/dev/null 2>&1 | 丢弃     | 丢弃     |
| 2>&1 >/dev/null | 丢弃     | 屏幕     |

# 3 服务启停

```sh
systemctl start logstash.service
```

```shell
systemctl stop logstash
```

