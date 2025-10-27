[toc]

## 1 卷描述

### 1 容器里面申明挂载卷

目的是说明容器里面的那些位置给做出去(containers.volumesMounts)

### 2 pod里面申明卷详情

目的是为了说明位置到底挂载在哪里(containers同级别的secret)

### 3 挂载方向

可以这样理解，容器里面的是一个申明挂载的目录，比如说将容器内的这个目录挂载出去/usr/share/nginx/html
如果是一个emptyDir类型，那这个emptydir的增删改查，会直接作用于/usr/share/nginx/html

如果是一个secret类型，则方向是volumes------->（containers）volumemounts，

事实上，所有的类型都应该是这个方向，volumes的东西应用在volumemounts中

## 2 卷类型

### 1 secret

```
apiVession: v1
kind: Pod
metadata：
 name: "pod-secret"
 namespace: default
 labels:
   app: "pod-secret"
spec:
  containers:
   - name: pod-secret
     image: "busybox"
     command: ["/binsh", "-c", "sleep 3600"]
     env:
     - name: MY_USR  ## 将来进去容器后，可以echo $MY_USR看到结果
     valueFrom：
       secretKeyRef:
       name： “自己创建的secret的名字”
       key: username ### username的值会自动base64解码
	   	       
```

卷挂载的方式,secret里面的所有key都是文件名，内容就是文件

```
apiVersion： v1
kind： Pod
metadata：
 name：“pod-secret-volume”
 namespace: default
 labels:
  app: "pod-secret-volume"
spec:
 volumes:
 - name:app
  secret:
  	secretName: db-user-pass
 containers:
  - name: pod-secret-volume
  image: "busybox"
  command: ["/bin/sh", "-c", "sleep 3600"]
  volumesMounts:
  - name: app
   mountPath: /app
```

### 2 configmap

和secret类似，都是些k-v值，只是没有加密而已

### 3 emptyDir

pod删除，emptydir也跟着删除，pod无论怎么重启，也不会导致卷消失

以下为两个容器共用一个卷，虽然容器中挂载的路径也可能不同，但是可以共用文件

```
apiVersion: v1
kind: Pod
metadata:
  name: multi-container-pod
spec:
  volumes:
    - name: nginx-vol
      emptyDir: {}
  containers:
    - name: nginx-container
      image: "nginx"
      volumeMounts:
        - name: nginx-vol
          mountPath: /usr/share/nginx/html
    - name: content-container
      image: "alpine"
      command: ["/bin/sh", "-c", "while true;do sleep 1; date > /app/index.html;done"]
      volumeMounts:
        - name: nginx-vol
          mountPath: /app
```

### 4 hostpath（时区对齐，只能单节点读写）

```
spec:
 volumes:
 - name: localtime
   hostPath:
     path: /usr/share/zoneinfo/Asia/Shanghai ## 单个主机中的文件
 containers:
 - name:MYAPP
   image: "debian-slim:latest"
   volumeMounts:
   - name: localtime
     mountPath: /etc/localtime   ## 挂到容器中的这个位置
```

### 5 nfs

```
containers:
- name: pod-nfs-01
  image: "nginx"
  volumeMounts:
  - name:html
  mountPath: /use/share/nginx/html
volumes:
 - name: html
 nfs:
   server: 10.171.11.9 ## 指定nfs的服务端
   path： /nfs/data/nginx  ## 这里的/是相当于服务器的/
```

## 3 pv/pvc

### 1 pv

```
apiversion:v1
kind: PersitentVolume
metadata:
 name:pv-volume-10m
 labels:
   type: local
spec:
 storageclassName: my-nfs-storage ## 定义存储类的名字
 capacity:
   storage:10m
 accessModes:
  - ReadWriteOnce
 nfs：
  server:10.0.0.2
  path: /nfs/data
```

### 2 申请pvc（申请书）

```
volumeMounts:
 - name: html
   mountpath: /usr/share/nginx/html
volumes:
  -name:html
  persistenVolumeClaim:
   claimName:nginx-pvc###你的申请书的名字
-------- 以下是申请书
apiversion:v1
kind: PersistentVolumeClaim
metadata:
 name:nginx-pvc  ## 申请书的名字
 namespace:
 labels:
  app:nginx-pvc
spec:
 storageClassName： my-nfs-storage  ## 存储类的名字，第一步创建pv时候指定
 accessModes:
 - ReadWriteOnce
 resources:
   requests:
    storage: 50m
```

