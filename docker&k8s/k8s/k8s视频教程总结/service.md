[toc]

## 0 service是会基于pod的就绪探针机制，完成pod的自动剔除和上线工作

简单点，就是探测到你不就绪了，访问service就不给流量到你这个pod

## 1 service的名字可以当成域名被解析

进入到pod后，curl svc的名字也可以访问

clusterIP为none的情况下，即使是无头服务，别人pod 不能用ip访问，也是可以用service作为域名访问

## 2 service的type有clusterIP， ExternalName， NodePort，LoadBalancer

### 1 clusterIP

当前service在集群内可以被人发现，默认给这个service分配一个集群内的网络

### 2 NodePort

 外界也可以通过端口来进行访问（nodeport： 不指定应该默认会在30000-32765这个范围内随意分配）

k8s开在机器端口上，默认在所有节点上，包括中心节点和工作节点上都有起相应的端口。

好处是，可以访问任意一个节点，都可以访问到内部的pod

### 3 loadBalance(不常用)

使用云提供商的负载均衡器向外部暴露服务，外部负载均衡器可以将流量路由到自动创建的NodePort服务和ClusterIP服务ip上。

```
apiversion: v1
kind: service
metadata:
 name: service-externamname-test
 namespace:default
spec:
 type: loadBalancer
```



### 4 ExtrenalName（不常用）

下面的例子访问这个service，就是访问baidu.com

curl service-externamname-test,

```
apiversion: v1
kind: service
metadata:
 name: service-externamname-test
 namespace:default
spec:
 type: ExternalName
 externalName: baidu.com
```



## 3 service真正的代理，其实是endpoint

可以来个无selector的service来做实验，现创建个无selector的service，然后在之后创建endpoint，endpinit可以写上ip，甚至baidu.com的ip都行