[toc]

## 1 搭建私有仓库（私服）

### 1 在本地服务器下载私服镜像

```
docker pull registry
```

### 2 运行私有镜像

```
docker run -d -p 5000:5000 --name bob-registry registry
{以5000端口来设立私有镜像服务器，存放的镜像可以在http://192.168.0.188:5000/v2/_catalog查看的到}
```

### 3 从私有仓库下载，上传镜像

#### 1 新增或修改/etc/docker/daemon.json

```
echo '{ "insecure-registries":["192.168.0.188:5000"] }' > /etc/docker/daemon.json
```

#### 2 重启docker

```
systemctl daemon-reload-----这条命令没试过，应该可以不运行
systemctl restart docker----这条命令一定要用
```

#### 3 镜像到私服

```
docker push 192.168.0.188:5000/hello-registry:v2.0
```

#### 4 私服到镜像

```
docker pull 192.168.0.188:5000/hello-registry:v2.0
```