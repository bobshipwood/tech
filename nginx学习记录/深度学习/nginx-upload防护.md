1 文件上传漏洞

http://www.imocc.com/upload/1.jpg/1.php

本身属于静态资源，但是将调用php解析器来解析执行

所以加入

location ^~ ./upload{

​	root /opt/app/images;

​	if($request_filename ~* (.*)\\.php){

​		return 403;

​	}

}

