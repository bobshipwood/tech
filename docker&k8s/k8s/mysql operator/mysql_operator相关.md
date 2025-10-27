[toc]

### 1  mysql密码相关

#### 1 创建密码

 kubectl create secret generic mypwds \
        --from-literal=rootUser=root \
        --from-literal=rootHost=% \
        --from-literal=rootPassword="sakila"

#### 2 查询密码

kubectl  get secret   secert名字（mysqlpwds） -o jsonpath='{.data.rootPassword}' |base64 --decode

### 2 mysql-operator安装

#### 1 安装crds

```
$> kubectl apply -f https://raw.githubusercontent.com/mysql/mysql-operator/trunk/deploy/deploy-crds.yaml

// Output is similar to:
customresourcedefinition.apiextensions.k8s.io/innodbclusters.mysql.oracle.com created
customresourcedefinition.apiextensions.k8s.io/mysqlbackups.mysql.oracle.com created
customresourcedefinition.apiextensions.k8s.io/clusterkopfpeerings.zalando.org created
customresourcedefinition.apiextensions.k8s.io/kopfpeerings.zalando.org created
```

#### 2 安装operator

```
$> kubectl apply -f https://raw.githubusercontent.com/mysql/mysql-operator/trunk/deploy/deploy-operator.yaml

// Output is similar to:
clusterrole.rbac.authorization.k8s.io/mysql-operator created
clusterrole.rbac.authorization.k8s.io/mysql-sidecar created
clusterrolebinding.rbac.authorization.k8s.io/mysql-operator-rolebinding created
clusterkopfpeering.zalando.org/mysql-operator created
namespace/mysql-operator created
serviceaccount/mysql-operator-sa created
deployment.apps/mysql-operator created
```

#### 3 验证是否安装成功（应该是operator.yaml文件定义了资源，有个pods和deployment，）

```
kubectl get deployment -n mysql-operator

NAME             READY   UP-TO-DATE   AVAILABLE   AGE
mysql-operator   1/1     1            1           37s

```

### 3 使用operator

#### 1 创建yaml文件

```
apiVersion: mysql.oracle.com/v2
kind: InnoDBCluster
metadata:
  name: mycluster 
spec:
  secretName: mysqlpwds # secret name must match k8s create secret`s name
  imagePullPolicy: IfNotPresent # 重要，因为防火墙的原因需要预先下载本地镜像，默认的策略是从网上下载
  tlsUseSelfSigned: true
  instances: 3
  router:
    instances: 1
```

```
$> kubectl apply -f mycluster.yaml
```

#### 2 创建静态pv（pv1.yaml,pv2.yaml,pv3.yaml）

```
apiVersion: v1
kind: PersistentVolume
metadata:
  name: worker1-pv-1
spec:
  capacity:
    storage: 50Gi
  volumeMode: Filesystem
  accessModes:
  - ReadWriteOnce
  persistentVolumeReclaimPolicy: Delete # i think use Retain will be better.cause pvc delete.pv retain
  #storageClassName: local-storage # offical`s case is named local-storage,and must create storageclass before  
  storageClassName: ""
  local:
    path: /mysql_data_dir
  nodeAffinity:
    required:
      nodeSelectorTerms:
      - matchExpressions:
        - key: kubernetes.io/hostname
          operator: In
          values:
          - worker1

```

#### 3 在节点上设置pv的目录(可选)

mkdir mysql_data_dir

chmod -R 700 mysql_data_dir
chown -R 27:sudo mysql_data_dir

#### 4 检查安装过程

```
$> kubectl get innodbcluster --watch
```

```
NAME          STATUS    ONLINE   INSTANCES   ROUTERS   AGE
mycluster     PENDING   0        3           1         10s
```

```
NAME        STATUS   ONLINE   INSTANCES   ROUTERS   AGE
mycluster   ONLINE   3        3           1         2m6s
```

### 4 检查集群状态

#### 1 连接mysql的router

```
kubectl run --rm -it test --image=mysql/mysql-operator:8.0.32-2.0.8  -- mysqlsh
或者
kubectl exec -it mycluster-0 -c mysql -- mysqlsh


\connect root@mycluster.default.svc.cluster.local
```



#### 2 查看状态

```
 var cluster = dba.getCluster()
 MySQL  mycluster.default.svc.cluster.local:33060+ ssl  JS > cluster.status();
{
    "clusterName": "mycluster", 
    "defaultReplicaSet": {
        "name": "default", 
        "primary": "mycluster-0.mycluster-instances.default.svc.cluster.local:3306", 
        "ssl": "REQUIRED", 
        "status": "OK", 
        "statusText": "Cluster is ONLINE and can tolerate up to ONE failure.", 
        "topology": {
            "mycluster-0.mycluster-instances.default.svc.cluster.local:3306": {
                "address": "mycluster-0.mycluster-instances.default.svc.cluster.local:3306", 
                "memberRole": "PRIMARY", 
                "mode": "R/W", 
                "readReplicas": {}, 
                "replicationLag": "applier_queue_applied", 
                "role": "HA", 
                "status": "ONLINE", 
                "version": "8.0.32"
            }, 
            "mycluster-1.mycluster-instances.default.svc.cluster.local:3306": {
                "address": "mycluster-1.mycluster-instances.default.svc.cluster.local:3306", 
                "memberRole": "SECONDARY", 
                "mode": "R/O", 
                "readReplicas": {}, 
                "replicationLag": "applier_queue_applied", 
                "role": "HA", 
                "status": "ONLINE", 
                "version": "8.0.32"
            }, 
            "mycluster-2.mycluster-instances.default.svc.cluster.local:3306": {
                "address": "mycluster-2.mycluster-instances.default.svc.cluster.local:3306", 
                "memberRole": "SECONDARY", 
                "mode": "R/O", 
                "readReplicas": {}, 
                "replicationLag": "applier_queue_applied", 
                "role": "HA", 
                "status": "ONLINE", 
                "version": "8.0.32"
            }
        }, 
        "topologyMode": "Single-Primary"
    }, 
    "groupInformationSourceMember": "mycluster-0.mycluster-instances.default.svc.cluster.local:3306"
}
```

```
\sql

use performance_schema;

MySQL  mycluster.default.svc.cluster.local:33060+ ssl  performance_schema  SQL > select MEMBER_HOST,MEMBER_ROLE,MEMBER_VERSION,MEMBER_STATE from replication_group_members;
+-----------------------------------------------------------+-------------+----------------+--------------+
| MEMBER_HOST                                               | MEMBER_ROLE | MEMBER_VERSION | MEMBER_STATE |
+-----------------------------------------------------------+-------------+----------------+--------------+
| mycluster-1.mycluster-instances.default.svc.cluster.local | SECONDARY   | 8.0.32         | ONLINE       |
| mycluster-2.mycluster-instances.default.svc.cluster.local | SECONDARY   | 8.0.32         | ONLINE       |
| mycluster-0.mycluster-instances.default.svc.cluster.local | PRIMARY     | 8.0.32         | ONLINE       |
+-----------------------------------------------------------+-------------+----------------+--------------+
3 rows in set (0.0016 sec)

```

### 5 连接mysql集群（连mysql router）

因为mysql8大力在推**caching_sha2_password 认证方式**，所以以前用navicat连不上，
解决方法有两种，一种是降为原先的认证方式，另一种是直接用HeidiSQL等工具支持这种认证方式的。

```
-- 创建用户（允许从任意主机访问，按需替换 `%` 为具体 IP）  
CREATE USER 'laravel_test'@'%' IDENTIFIED WITH mysql_native_password BY 'whd2025whd';  

-- 授予所有数据库的完全权限（等效于 root）  
GRANT ALL PRIVILEGES ON *.* TO 'laravel_test'@'%' WITH GRANT OPTION;  

-- 刷新权限使配置生效  
FLUSH PRIVILEGES;  
```



另外，原先的是以service部署的，集群外无法访问，那只能创建一个新的nodeport的类型来使用，直接连到mysql router上，

```
apiVersion: v1 
kind: Service 
metadata:
  name: mysql-router-nodeport 
spec:
  type: NodePort 
  selector:
    component: mysqlrouter                   # 匹配Router Pod标签 
    mysql.oracle.com/cluster:  mycluster      # 匹配集群标识 
    tier: mysql                              # 层级标签 
  ports:
  - name: mysql-rw 
    port: 3306                              # 服务监听的端口（集群内部访问用）
    targetPort: 6446                         # Router实际监听的端口 
    nodePort: 31001                          # 节点暴露的端口（范围30000-32767）
```

或者，官网上有写，直接在创建的yaml文件中写入nodeport类型(InnoDBCluster.spec.service)，默认是cluster ip，但是没有验证过

| Name          | Type   | Description                                                  | Required |
| :------------ | :----- | :----------------------------------------------------------- | :------- |
| `annotations` | object | Custom annotations for the Service                           | false    |
| `defaultPort` | enum   | Target for the Service's default (3306) port. If mysql-rw traffic will go to the primary and allow read and write operations, with mysql-ro traffic goes to the replica and allows only read operations, with mysql-rw-split the router's read-write-splitting will be targetedEnum: mysql-rw, mysql-ro, mysql-rw-split`Default`: mysql-rw | false    |
| `labels`      | object | Custom labels for the Service                                | false    |
| `type`        | enum   | Enum: ClusterIP, NodePort, LoadBalancer`Default`: ClusterIP  | false    |

### 6 后记

#### 1 在mysql router的新版本中，不止读写端口，只读端口，
还支持另外一种透明代理端口，支持自动将写的请求分发到读写节点，读的请求自动分发到只读节点

#### 2 fpm7.4.4下的pdo，无法连接caching_sha2_password的认证方式的用户 

经测试，fpm7.4.4以下的pdo根本无法连接mysql集群

fpm7.4.4可以连接，但要认证方式降级

```
#CREATE USER 'laravel_test'@'%' IDENTIFIED WITH mysql_native_password BY 'whd2025whd';
#GRANT ALL ON *.* TO 'laravel_test'@'%';
#FLUSH privileges
```

#### 3 local-storage的类(理论上应该用这个类的，因为WaitForFirstConsumer的特性)

```
apiVersion: storage.k8s.io/v1
kind: StorageClass
metadata:
  name: local-storage
provisioner: kubernetes.io/no-provisioner # indicates that this StorageClass does not support automatic provisioning
volumeBindingMode: WaitForFirstConsumer
```

