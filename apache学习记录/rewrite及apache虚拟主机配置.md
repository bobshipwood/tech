

[TOC]

# 1 要在在directory 下写

因为htaccess文件会产生性能问题

RewriteEngine on 

RewriteRule      ^(.*)\\.htm$     $1.html

![1550126548991](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1550126548991.png)

# 2 rewrite基本概念 

RewriteBase  定义基准目录

RewriteRule   实现匹配

RewriteMap   现阶段不学，感觉有点多余

RewriteCond  实现匹配之前，还追加的条件

内部重定向 从外部url看不出来，因为url不发生变化(不加标签R则为内部重定向)

外部重定向  针对http协议的，可以看得出变化

# 3 rewrite rule 规则说明

RewriteRule 模式匹配 替换的url [flags]

多个flags用逗号隔开 [R=302,C]

## 1 rewrite rule R 标签说明

RewriteRule      ^(.*)\\.htm$     /$1.html  [R=301]

强制**外部重定向**，后年可以加301,302，进行跳转。直接写R默认302,

301 永久重定向， 之前的seo评分一并带过来直接给新的地址。seo优化选他

302 临时重定向



## 2 rewrite rule C 标签说明

链接下一规则

与下一条规则成为一个整体，如果这一条不匹配，下一条就不进入了。

RewriteRule ^(.*)\\.htm$     /$1.html  [C]

RewriteRule ^(.*)\\.html$     /$1.php 

如果x.htm,则会找x.php

如果x.html，则不会变成x.php



## 3 rewrite rule L flag说明

结尾规则，立即停止重写操作，并不在应用其他重写规则(只有匹配到了这条规则，才会有L的功能)

RewriteRule  ^(.*)    first.php?req = $1 [L]

RewriteRule  ^(.*)    second.php?req = $1 

这条默认第一条就已经结束，

如果不加L，则继续匹配到second.php中



## 4 rewrite rule NE flag说明

不对url的特殊字符进行hexcode转码(如#,转换为%23)

RewriteRule ^(.*)\\.htm$   /index.html#$1  [R,NE]



## 5 rewrite rule NC flag说明

不区分大小写(针对第一个参数，正则部分)

RewriteRule      ^test/(.*)\\.htm     /tmp/$1.htm     [NC]

如果不加NC，则TEst/1.htm不参与匹配，apache对大小写敏感



## 6 rewrite rule G flag说明

请求的网页已经失效了（Gone）

对应http代码410



## 7 rewrite rule QSA flag说明

用于在URI中截取字符串

RewriteRule ^per/(.*)$   /per.php?person_id=$1[QSA R]

当访问/per/123.php?name=xiaom,QSA把name=xiaom，截取下来。

默认是不截取name=xiaom的



# 4  rewrite_base

RewriteBase URL-path

设置了目录级重写的基准URL，（默认在那个目录下找）

RewriteBase /test

RewriteRule ^(.*)\\.shtml$     $1.html  

当不加R的时候，为内部重定向，以上表示在相同目录下找文件

当加R的时候，就不行了，所以要配置基准的url（或者在$1.html  加/）



# 5 RewriteCond语法说明

## 1 基本原则

RewriteCond TestString CondPatterm [flags]

在RewriteRule指令之前有一个或多个RewriteCond指令

RewriteCond $1 'test'

RewriteRule ^(.*)\\.htm$     $1.html  

先判断是否匹配Rewriterule，然后再判断RewriteCond ，本例中只有test.htm（$1= 'test'）才会变成test.html



TestString$1-$9

$1 -$9引用紧跟着RewriteCond 后面的RewriteRule中模板匹配的数据



## 2 %{NAME_OF_VARIABLE}服务器变量(TestString直接调取服务器变量)

RewriteCond %{HTTP_HOST}  127.0.0.1

![1550132718143](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1550132718143.png)





RewriteCond  ${HTTP_HOST}  '127.0.0.1'

RewriteRule ^(.*)\\.htm http://localhost/$1.html [R] 

**以上只能是R，非R内部访问只能是通过文件相对路径访问。**

**这个时候￥1代表xxx.htm(我猜测的)**

当输入127.0.0.1/test.htm时候，他会重定向为localhost/test.html

## 3 多条件下执行情况（默认and）

RewriteCond  ${HTTP_HOST}  '127.0.0.(.*)'

RewriteCond  $1 '2'

RewriteRule ^(.*)\\.htm http://localhost/$1.html [R] 

当输入127.0.0.1/test.htm时候，他不会重定向，只有127.0.0.2时候才会重定向,意思是要满足两个cond才行

## 4 CondPatterm 使用说明

-d 是否是目录 -f是否是文件

RewriteCond C:/wamp/www -d

**-F 文件存在并且可以访问**

## 5 [flags] 使用说明：

[NC] 大小写不敏感

**[OR] 条件判断的或，如果不加默认是and**

RewriteCond c:/wamp/www  -d [OR]

RewriteCond c:/wamp/license.txt -F

# 6 实际应用

## 1 临时和永久重定向

![1550280833436](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1550280833436.png)

保险起见，甚至可以不用302.

## 2 防止盗链（HTTP_REFERER）

![1550281683078](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1550281683078.png)



## 3 thinkphp 重写例子

RewriteCond ${REQUEST_FILENAME} !-d   //当访问的url不是实际的目录时候

RewriteCond ${REQUEST_FILENAME} !-f   //当访问的url不是实际的文件时候

RewriteRule  ^(.*)$   /think/public/index.php/$1 [QSA,PT,L]

在RewriteRule中的目标串（被替换完的路径）都会被看做是文件路径，使用[PT]选项能够让apache将其看作是URI来对待。这也就是说，使用了[PT]选项，能够使得RewriteRule的结果重新加入到URL的匹配当中去，让那些基于本地匹配的，例如Alias, Redirect, or ScriptAlias,能够有生效的机会。



# 7 apache   实际虚拟主机配置

<VirtualHost *:80>

ServerAdmin webmaster@dummy-host.example.com
DocumentRoot "/data/wwwroot/0759wy.com"  
ServerName 0759wy.com
ServerAlias www.0759wy.com

<IfModule mod_rewrite.c>//需要mod_rewrite模块支持

RewriteEngine on //打开rewrite功能

//定义条件，当主机名不是www.hao123.com时候，满足条件
RewriteCond %{HTTP_HOST} !^www.hao123.com$  

//执行规则
RewriteRule  ^/(.*)$ http://www.123.com[R=301,L]

</IfModule>




//配置静态文件过期时间，Cache-control:max-age=？
<IfModule mod_expires.c>
ExpiresActive on
ExpiresByType image/gif "access plus 24 hours"
ExpiresByType image/jpg "access plus 24 hours"
ExpiresByType image/png "access plus 24 hours"
ExpiresByType text/css  "now plus 7 days"
ExpiresByType application/x-javascript "now plus 7 days"
ExpiresByType application/javascript "now plus 7 days"
ExpiresByType application/x-shockwave-flash "now plus 7 days"
ExpiresDefault  "now plus 0 min"
</IfModule>

//将上传文件目录设置为静止解析php代码
<Directory /data/wwwroot/0759wy.com/uploadfile>
php_admin_flag engine off
</Directory>

//在虚拟主机的目录下配置，不用在php.ini文件配置。意思是只在当前目录运行php，防止黑客入侵后。在其他目录下搞破坏
php_admin_value open_basedir "/data/wwwroot/0759wy.com/:/tmp/"



SetEnvIf Request_URI  ".*\.gif$" image-request	
SetEnvIf Request_URI  ".*\.jpg$" image-request	
SetEnvIf Request_URI  ".*\.bmp$" image-request
SetEnvIf Request_URI  ".*\.png$" image-request	
SetEnvIf Request_URI  ".*\.js$" image-request	
SetEnvIf Request_URI  ".*\.css$" image-request	
SetEnvIf Request_URI  ".*\.swf$" image-request
//配置以上访问不记录在访问日志里头
CustomLog "|/usr/local/apache2.4/bin/rotatelogs -l logs/0759wy.com-access_%Y%m%d.log 86400" combined env=!image-request

//配置错误日志文件
ErrorLog "logs/0759wy.com-error_log"
</VirtualHost>





















