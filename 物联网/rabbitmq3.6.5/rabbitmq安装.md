[toc]

### 一、准备依赖包

```
~~yum install build-essential openssl openssl-devel unixODBC unixODBC-devel make gcc gcc-c++ kernel-devel m4 ncurses-devel tk tc xz
```

### 二、下载erlang-18.3-1

```
wget www.rabbitmq.com/releases/erlang/erlang-18.3-1.el7.centos.x86_64.rpm
```

直接在centos7下yum install erlang，  版本为erlang.x86_64 0:R16B-03.18.el7，根据https://www.rabbitmq.com/which-erlang.html，可以适用于rabbitmq3.6.5

然后再yum install -y erlang-sd_notify

### 三、下载socat-1.7.3.2

```
wget http://repo.iotti.biz/CentOS/7/x86_64/socat-1.7.3.2-5.el7.lux.x86_64.rpm
```

### 四、下载rabbitmq-server3.6.5

```
wget www.rabbitmq.com/releases/rabbitmq-server/v3.6.5/rabbitmq-server-3.6.5-1.noarch.rpm
```

 https://cbs.centos.org/kojifiles/packages/rabbitmq-server/3.6.5/1.el7/noarch/rabbitmq-server-3.6.5-1.el7.noarch.rpm，这个才行

### 五、安装

#### 1、安装erlang（直接在centos yum 安装）

```
rpm -ivh erlang-18.3-1.el7.centos.x86_64.rpm
```

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415101022568-670979321.png)

 

#### 2、安装rabbitmq-server

```
rpm -ivh rabbitmq-server-3.6.5-1.noarch.rpm
```

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415101128307-970270093.png)

提示需要一个socat依赖库

#### 3、安装socat

```
rpm -ivh socat-1.7.3.2-5.el7.lux.x86_64.rpm
```

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415101251499-958707140.png)

 

#### 4、再安装rabbitmq-server

```
rpm -ivh rabbitmq-server-3.6.5-1.noarch.rpm
```

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415101327600-1610226906.png)

安装成功

###  六、修改配置

rpm安装，默认目录为：

/usr/lib/rabbitmq

进入rabbirmq目录

```
cd /usr/lib/rabbitmq/lib/rabbitmq_server-3.6.5/ebin
vim rabbit.app  
```

rabbit.app：核心配置文件

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415104326274-1991247548.png)

端口默认：5672

 

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415104424488-570926455.png)

将{loopback_users, [<<"guest">>]} ,把guest用户打开，才能登录管控台(**我这里用虚拟机安装的，不用这一个设置**)

```
{loopback_users, [guest]}
```

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415104611521-1305936265.png)

保存、退出

### 七、启动rebbitmq

```
rabbitmq-server start &
```

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415105148332-1597136548.png)

 显示日志文件路径

我们打开日志文件

```
vim /var/log/rabbitmq/rabbit\@zabbix_server.log
```

 

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415105339643-2003492435.png)

里面记录启动时的一些步骤，最后一行显示启动完毕。

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415105611882-1969310413.png)

5672端口已经被rabbitmq监听

### 八、安装管控台插件

```
rabbitmq-plugins  enable rabbitmq_management
```

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415105837665-760046368.png)

安装成功，管控台默认端口号：15672

```
rabbitmq-plugins  enable rabbitmq_web_stomp
```

安装web-storm插件



### 九、登录管控台

浏览器打开

```
http://172.28.18.75:15672/
```

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415110002562-1538198906.png)

 

显示登录页面,用guest guest登录即可，这里最好我们使用rabbitmqctl命令添加一个管理员用户

![img](https://img2018.cnblogs.com/blog/1636520/201904/1636520-20190415110501105-256010457.png)

目前只有guest用户

添加一个admin用户

```
rabbitmqctl add_user admin password
```

为用户设置管理员标记

```
rabbitmqctl set_user_tags admin administrator
```

为用户设置权限

```
rabbitmqctl set_permissions -p / admin '.*' '.*' '.*'
```

用新建的 admin可以登录管控台了

后台启动rabbitmq 

```
rabbitmq-server -deched --后台启动节点
```

 rabbitmqctl stop_app --关闭节点上的应用

 rabbitmqctl start_app --启动节点上的应用

 rabbitmqctl stop --关闭节点

### 十、设置开机自启动

systemctl enable rabbitmq-server