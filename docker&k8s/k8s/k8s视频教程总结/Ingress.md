[toc]

### 1 实际案例

```
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: hello
  namespace: default
  #annotations： ingress.kubernerts.io/rewrite-target: /$2
  #              nginx.ingress.kubernetes.io/limit-rqs: "1"  限流的配置
  #              nginx.ingress.kubernetes.io/affinity: "cookie"   
spec:
  #tls:  ## 结合创建secret，来配置https
  - hosts:
   - itdachang.com
   secretName: itdachang-tls
  rules:
  - host: it666.com ## 指定监听的主机域名
    http:
      paths:
      - pathType: Prefix ## 前缀匹配，it666.com/abc也能匹配到这个
        pathType: Extra ## 精确匹配，it666.com/abc也能匹配到这个
        path: "/"
        backend:
          service:
            name: <Service> ## kubectl get service的名字
            port: 
              number: <Port>  ## service的端口号
```

### 2 ingress规则生效了会自动同步到所有安装了ingresscontroller的机器

### 3 他的应用就分为两块

#### 1 在yaml文件中写好annotations

#### 2 在yaml文件中写好rules

### 4 金丝雀升级（ingress版本）

#### 1 先部署v1版本的ingress

#### 2 拷贝v1版本，然后在此基础上加上注解,形成v2版本（不加注解会报错，因为相同的配置不能存在）

```
annotations:
	nginx.ingress.kubernates.io/canary: "true"
	nginx.ingress.kubernates.io/canary-by-header： “haha” // 自定义头名 always是永远路由到v2版本，never是永远路由到v1版本
	#nginx.ingress.kubernates.io/canary-by-header-value： “haaaaa” // 带这个头永远路由到v2，不带这个头就路由到v1
	#nginx.ingress.kubernates.io/canary-by-cookie： “ccc” // 自定义cookies，always是永远路由到v2版本，never是永远路由到v1版本
    #nginx.ingress.kubernates.io/canary-weight: 80  // 80%的流量会流入v2,20%的流量会流入v1
    ## 优先级如下：canary-by-header > canary-by-cookie > canary-weight
```

