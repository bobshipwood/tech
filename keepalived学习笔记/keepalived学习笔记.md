[TOC]

# 1 工作原理

## 1 竞选机制

vrrp是一种通过竞选协议来将路由任务交给某台vrrp路由器。

## 2 加密

vrrp为了安全，使用了加密协议。

## 3 ip多播

通过ip多播的方式实现通信。

## 4 工作过程

主发包，从接包，当从接不了包的时候，会启动程序接管主的资源，当有多个从时候，通过优先级来竞选。

# 2 配置详解（keepalived.conf）

``` 
global_defs{
	notification_email{
		4721@ww.com
	}
	notification_email_from  dasdsa@dsad.com
	smtp_server    192.168.200.1
	smtp_connect_timeout   30
	router_id   lvs_1(LVS_2)  //相当于mysql的sever_id，主从间必须不一样
}

vrrp_instance VI_1{           //VI_1可以改名字
	state MASTER（BACKER）     //主从设置，主从不一致--------唯二的主从不一致的地方
	interface eth0       // 下面的vip地址绑在eth0的接口上
	virtual_router_id 51  //这个值需要和从设置为一致
        priority      100(50)     //权重值,和从要不一致------唯二的主从不一致的地方
	authentication{
	     auth_type  PASS
	     auth_pass  1111   //设置通讯的密码，一般不用改，主从要一致
	}
	virtual_ipaddress{
		192.168.2.1
		192.168.2.3
		192.168.2.5		
	}
}

```



