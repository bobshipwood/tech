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