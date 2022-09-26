[toc]

## 1 镜像和容器显示，停止，删除命令

```
docker ps -l  //显示最新创建的容器
docker ps -a  //显示所有的容器（包括未运行的容器，默认只显示运行的容器）
docker stop XXXXXX  //停止运行容器
docker restart XXXXXX  //重新运行容器
docker start XXXXXX  //开始运行容器
docker rm XXXXXX   //删除容器
docket images //显示所有镜像
docker rmi “镜像名字”  //删除镜像
```

## 2 上传镜像到仓库

### 1 登录（输入hub.docker.com的用户名，密码）

```
docker login
```

### 2 给镜像打上自己的仓库名（docker tag）

```
docker tag hello-world:latest bobshipwood/hello-world:3.0  {hello-world:latest是原先镜像名字，bobshipwood/hello-world:3.0是我新建的一个标签，bobshipwood是我在hub.docker.com注册的一个相当于免费仓库的地址前缀}
```

```
私有仓库：
docker tag hello-world:latest 192.168.0.188:5000/hello-registry:v2.0
```

### 3 上传

```
docker push bobshipwood/hello-world:3.0
docker push 192.168.0.188:5000/hello-registry:v2.0//私有仓库
```

## 3 从仓库下载镜像

```
docker pull bobshipwood/hello-world:tagname //不打标签，则默认标签为latest
docker pull 192.168.0.188:5000/hello-registry:v2.0//私有仓库
```

## 4 镜像运行命令（docker run）

```
1 docker run ubuntu:15.10 /bin/echo "Hello world" //输出个hello world

2 docker run -i -t ubuntu:15.10 /bin/bash   //进入docker的运行环境，-i: 交互式操作，-t: 终端。
runoob@runoob:~$ docker run -i -t ubuntu:15.10 /bin/bash
root@0123ce188bd8:/#

3 如果退出指定容器，则在终端输入exit

4 docker run -d --name springboot-docker-c -p 10000:8080 springboot {-d 是后台运行镜像的意思，前一个name指定了容器的名字，最后的springboot是用哪个镜像的意思,运行这条命令后，下面会出现这个容器运行的一长串id}

5 如果一个容器在后台运行，想进入这个容器，推荐运行exec，因为其退出后不终止容器运行
docker exec -it 243c32535da7 /bin/bash
```

## 5 docker logs 命令

```
docker logs 2b1b7a428627//  在物理机上查询指定id的容器运行日志（运行输出）

runoob@runoob:~$ docker logs -f bf08b7f2cd89
 * Running on http://0.0.0.0:5000/ (Press CTRL+C to quit)
192.168.239.1 - - [09/May/2016 16:30:37] "GET / HTTP/1.1" 200 -
192.168.239.1 - - [09/May/2016 16:30:37] "GET /favicon.ico HTTP/1.1" 404 -

-f: 让 docker logs 像使用 tail -f 一样来输出容器内部的标准输出。
```

## 

