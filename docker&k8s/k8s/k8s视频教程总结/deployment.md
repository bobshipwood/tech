[toc]

## 1 基本作用

deployment 使pod具有多副本（起多个相同的pod），自愈（删除单个pod后在节点上会自动复活），扩縮容的能力

deployment管理replicate， replicate又管理pod（副本的数量以及历史记录）。



## 2 命令解释（滚动升级的命令应该不常用，因为有更好的方式）

### 1 kubectl delete deployment my-tomcat

真的的删除了deployment，也就删除了pods

### 2 kubectl create deplotment my-tomcat --image=tomcat:9.0.55 --replicas=3

最后的--replicas=3体现了多副本

### 3 kubectl scale --replicas=5 deploment my-tomcat

扩容到5个pod

### 4 kubectl scale --replicas=3 deploment my-tomcat 

又缩到3个pod 

### 5 kubectl set image deployment my-tomcat tomcat-tomcat：10.1.11 --record

滚动升级，加入record是为了使得replicate有记录可看，但是这个滚动升级没啥意义，

### 6 kubectl apply --filename=deploy_git.yaml --record=true

在最后面加的record，最终会在replicateSet的历史记录，然后deploy_git一般由git管理的镜像版本文件

### 7 kubectl rollout history deployment my-tomcat

查看该deployment有多少个历史记录版本

### 8 kubectl rollout undo deployment my-tomcat

回退上一个版本

### 9 kubectl rollout undo deployment my-tomcat --to-revision=2

回退指定版本

## 3 标准写法注意

### 1 labels的 标签理应和spec的selector的标签一致

```
apiVersion: apps/v1
kind: Deployment
metadata:
 labels:   
    app: my-tomcat  // 标签
  name: my-tomcat
spec: // 描述是spec的
   replicas: 3
   selector:  // selector选择器，选中所有副本的标签，从而实现3个副本，因每副本都会拥有这个标签
       matchLabels:
                  app: my-tomcat
   template:
    metadata:
      labels:
    	app: my-tomcat
    spec:
      containers:
        - image: tomcat:9.0.55
          name: tomcat     	

```

## 4 金丝雀升级案例

一个service 只用标签app=ngnix， 部署有两个deployment，  deployment1 下面的pod的标签是app=nginx v=1 deployment2下面的pod标签是app=nginx v=2，
这样就可以缓慢的控制升级版本（deployment2）的数量及时长，待升级稳定后，就可以干掉deployment1了

```
apiVesionn: v1
kind: Service
metadata:
 name: canary-test
 namespace:default
spec:
 selector:
 	app: canary-nginx
 type: Nodeport ### 浏览器可以直接访问
 ports：
  - name： canary-test
    port： 80
    targetPort： 80 ### pod访问的端口
    protocol： TCP
    nodePort：31666 ### 机器上开的端口，浏览器访问
    
-------------------------------------------------------------------------------    
apiVersion： apps/v1
kind： Deployment
metadata:
 name: canary-dep-v1
 namespace: default
 labels:
   app:canary-dep-v1
spec:
	selector:
	 matchLabels:
	 	app: canary-nginx
	 	v:v111
	 replicas: 2
     template:
      metadata:
      	labels:
      		app：canary-nginx
      		v:v111
      spec:
      - name: nginx
        image: MYAPP:latest
        env:
        - name: msg
          value: v1111111
 -----------------------------------------------------------------------------
 升级时候，再部署一次，当觉得v2ok的时候，就杀掉v1
apiVersion： apps/v1
kind： Deployment
metadata:
 name: canary-dep-v2
 namespace: default
 labels:
   app:canary-dep-v2
spec:
	selector:
	 matchLabels:
	 	app: canary-nginx
	 	v:v222
	 replicas: 1 //先让其有一个副本
     template:
      metadata:
      	labels:
      		app：canary-nginx
      		v:v222
      spec:
      - name: nginx
        image: nginx
 
 
```

