[toc]

## 1 首先拉一个jdk8的镜像 

docker pull java:8

## 2 其次制作自己的镜像

### 1 制作Dockerfile文件

```
vi Dockerfile
```

### 2 编辑Dockerfile文件 

```
from java:8                                       {意思是jar包运行在java8的镜像之上}
ADD /springboot-docker-0.0.1-snapshat.jar //      {意思是将当前目录下的jar包放进镜像的根目录}
ENTRYPOINT ["java", "-jar", "/springboot-docker-0.0.1-snapshat.jar"]
```

### 3 开始打包镜像文件

```
docker build -t springboot .     {最后的.是把当前目录下（也可以是绝对目录）的Dockfile给传送到镜像里面去,-t是指定镜像名字}
```

## 3 将镜像运行成容器

### 1 显示刚才打包的springboot镜像

```
docker images
```

### 2 运行并使之成为镜像

```
docker run -d --name springboot-docker-c -p 10000:8080 springboot {-d 是后台运行镜像的意思，前一个name指定了容器的名字，最后的springboot是用哪个镜像的意思,运行这条命令后，下面会出现这个容器运行的一长串id}
```

## 4 检查镜像运行

```
docker logs -f -t --tail 50 c6x2skksdad442423223dssdffsdf {-f，follow log outputs， -
t show timestamps,--tail 应该是最后的50行的意思，最后面的参数是容器运行的id}
```

## 5 删除容器和镜像（options）

```
docker stop c6x2skksdad442423223dssdffsdf {以id形式，停止容器运行}
docker rmi springboot  {删除原先制作的镜像文件}
```

