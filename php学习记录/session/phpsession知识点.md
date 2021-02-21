[TOC]

# 1 session_start()知识点（自己总结的）

## 1 session_start 前不能有任何输出

## 2 脚本首次运行session_start时候，这个函数向客户端发送一个session_id保存在客户端中，还在服务器创建一个和客户端同名的session文件。（首次访问还不能使用session数组）

## 3 当客户端再次访问相同脚本的时候，先判断客户端有没有sessionid，如果有，则直接以头函数携带这个session来使用这个session_id开启会话。

### 3.1 如果此刻服务端找到同名session文件，则可以使用session数组了

 ### 3.2 如果此刻服务端找不到同名的session文件（被垃圾回收或者其他情况），则服务端再次创建同名文件之后，才可以使用session数组。



# 2 session在linux下会比windows好

## 1 session在cookie禁用的情况下，可以通过url来传。

## 2 在使用linux服务器时候，如果在编译php时候使用了enable-trans-sid，在运行时候使用session.use_trans_sid被激活，那么相对url将被自动修改为包含sessionid

## 3 如果没有配置以SID或者window做服务器，则使用常量SID

## 4 SID使用例子(=session_name()=session_id())

```
<a href="one.php?<?php echo SID;?>">one</a>
```

```
$sid = !empty($_GET[session_name()]?$_GET[session_name()]):'';
if($sid!=''){
    session_id($sid);//设置值，不传值的时候为活期session_id
}
session_start();
```



