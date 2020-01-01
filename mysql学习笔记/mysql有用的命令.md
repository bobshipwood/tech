[TOC]



# 1 创建用户并授权

grant all privileges on dnname.* to username@localhost identified by 'passwd';（创建用户并授权）

# 2 查询用户执行的语句（full）

show full processlist

# 3 查询系统所设置的变量

show variables like ‘key_buffer%’

# 4 设置系统变量

## 1 被人登录也有效。重启后就失效，只有my.cnf下改重启不失效

set global key_buffer_size = 32*1024*1024

## 2 当前自己的会话才有效

 set session key_buffer_size = 32*1024*1024（当前会话有效）

# 5 分析并做好监控，里面记录了查询，插入等总次数

show global status

# 6 命令末尾加\G显示更详细，更好看

