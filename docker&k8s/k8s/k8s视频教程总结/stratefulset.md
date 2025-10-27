[toc]

### 1 说明

deployment主要是部署业务代码，stratefulset主要是部署mysql等中间件，因为这类中间件有网络访问唯一，挂载volume唯一的要求

stratefulset需要和service指定一起来实现功能

### 2 yaml

```Set
apiVersion: apps/v1
kind: StratefulSet
metadata:
 name:starteful-nginx
 namespace:default
spec:
 selector:
  matchLabels:
   app: ss-nginx
  serviceName："nginx" ## 服务名，指定加到那个service里面
  replicas:3
  template:
   metadata:
    labels:
     app:ss-nginx
    spec:
     containers:
     - name:nginx
       image:nginx
-------------------------------------------------------------------------
创建一个名字为nginx的service：

apiVersion: v1
kind: Service
metadata:
 name:nginx   ## 和上面的servicename必须相同
 namespace:default
spec:
 selector:
   app:ss-nginx   ##  选择器，选择pods的标签
 type:ClusterIP
 clusterIP：None  ## 不分配ip
 ports:
   - name: http
     port: 80
     targetPort：80
```

### 3 其他pod访问stratefulset里面的pod

curl strateful-nginx-0(kubectl get pods得到的名字).nginx（service的名字）

