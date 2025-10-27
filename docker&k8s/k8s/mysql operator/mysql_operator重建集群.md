[toc]

# 1 重新配置一个节点

## 1.1 进入其中一个节点

```
kubectl exec -it mycluster-0 -c mysql -- mysqlsh

\connect root@mycluster-0
```

## 1.2 查看原先的集群名字

```
查询自己原先的集群名字
MySQL  mycluster-0:33060+ ssl  JS > \sql
Switching to SQL mode... Commands end with ;

MySQL  mycluster-0:33060+ ssl  SQL > USE mysql_innodb_cluster_metadata; 
Default schema set to `mysql_innodb_cluster_metadata`.
Fetching global names, object names from `mysql_innodb_cluster_metadata` for auto-completion... Press ^C to stop.

MySQL  mycluster-0:33060+ ssl  mysql_innodb_cluster_metadata  SQL > SELECT cluster_name FROM clusters; 
+--------------+
| cluster_name |
+--------------+
| mycluster    |
+--------------+
1 row in set (0.0007 sec)
```



## 1.3 配置为主节点，如果有只读限制，则解除只读后再配置

```
dba.configureInstance('root@mycluster-0.mycluster-instances.default.svc.cluster.local:3306')

# 执行 SQL 解除只读限制
\sql
SET GLOBAL super_read_only = 0; 
# 再次配置
\js
 MySQL  mycluster-0:33060+ ssl  JS > dba.configureInstance('root@mycluster-0.mycluster-instances.default.svc.cluster.local:3306')
 
Please provide the password for 'root@mycluster-0.mycluster-instances.default.svc.cluster.local:3306': **********
Save password for 'root@mycluster-0.mycluster-instances.default.svc.cluster.local:3306'? [Y]es/[N]o/Ne[v]er (default No): 
Configuring local MySQL instance listening at port 3306 for use in an InnoDB cluster...

This instance reports its own address as mycluster-0.mycluster-instances.default.svc.cluster.local:3306

applierWorkerThreads will be set to the default value of 4.

The instance 'mycluster-0.mycluster-instances.default.svc.cluster.local:3306' is valid to be used in an InnoDB cluster.

The instance 'mycluster-0.mycluster-instances.default.svc.cluster.local:3306' is already ready to be used in an InnoDB cluster.
```

## 1.4 检查刚才的配置，如有异常，则修复异常

```
dba.checkInstanceConfiguration('root@mycluster-0.mycluster-instances.default.svc.cluster.local:3306')
此时会输出不兼容的表
Checking whether existing tables comply with Group Replication requirements...
ERROR: The following tables do not have a Primary Key or equivalent column: 
sydb.password_resets

Group Replication requires tables to use InnoDB and have a PRIMARY KEY or PRIMARY KEY Equivalent (non-null unique key). Tables that do not follow these requirements will be readable but not updateable when used with Group Replication. If your applications make updates (INSERT, UPDATE or DELETE) to these tables, ensure they use the InnoDB storage engine and have a PRIMARY KEY or PRIMARY KEY Equivalent.
If you can't change the tables structure to include an extra visible key to be used as PRIMARY KEY, you can make use of the INVISIBLE COLUMN feature available since 8.0.23: https://dev.mysql.com/doc/refman/8.0/en/invisible-columns.html

Checking instance configuration...

{
    "status": "error"
}
```

## 1.5 修复异常（可选）

```
\sql下，先确认下确实无主键
SHOW CREATE TABLE sydb.password_resets;
增加个隐形主键，因为mysql8要求所有表都需要有主键
ALTER TABLE sydb.password_resets ADD COLUMN gr_pk BIGINT UNSIGNED AUTO_INCREMENT INVISIBLE PRIMARY KEY COMMENT '组复制隐式主键'; 
再次确认下，有无添加主键
SHOW CREATE TABLE sydb.password_resets;
```



# 2 用原先的配置信息，重建集群

## 2.1 根据集群的元数据重建(dba.rebootClusterFromCompleteOutage())

```
MySQL  mycluster-0:33060+ ssl  JS > dba.rebootClusterFromCompleteOutage()
Restoring the Cluster 'mycluster' from complete outage...

Cluster instances: 'mycluster-0.mycluster-instances.default.svc.cluster.local:3306' (OFFLINE), 'mycluster-1.mycluster-instances.default.svc.cluster.local:3306' (UNREACHABLE), 'mycluster-2.mycluster-instances.default.svc.cluster.local:3306' (UNREACHABLE)

//输出警告消息，mycluster-1和mycluster-2无法到达
WARNING: One or more instances of the Cluster could not be reached and cannot be rejoined nor ensured to be OFFLINE: 'mycluster-1.mycluster-instances.default.svc.cluster.local:3306', 'mycluster-2.mycluster-instances.default.svc.cluster.local:3306'. Cluster may diverge and become inconsistent unless all instances are either reachable or certain to be OFFLINE and not accepting new transactions. You may use the 'force' option to bypass this check and proceed anyway.
// 输出错误消息
ERROR: Could not determine if Cluster is completely OFFLINE
Dba.rebootClusterFromCompleteOutage: Could not determine if Cluster is completely OFFLINE (RuntimeError)

```

## 2.2 强制重建

```
// 第一个参数是集群名字，由1.2来获取
mysqlsh> dba.rebootClusterFromCompleteOutage('mycluster', {force: true});  

// 然后通过配置文件将集群降为1，记得pv要手动清楚，最后再改为3,这个时候查看状态
kubectl exec -it mycluster-0 -c mysql -- mysqlsh
\connect root@mycluster.default.svc.cluster.local
var cluster = dba.getCluster()
cluster.status()

// 如果status状态还不行，则需要rescan一下，重新加入节点
cluster.rescan()
```

## 2.3 删除元数据dba.dropMetadataSchema()（可选）

```
如果上一步失败，并且群集元数据已严重损坏，则可能需要删除元数据并从头开始再次创建群集。可以使用dba.dropMetadataSchema()删除集群元数据。dba.dropMetadataSchema方法应仅用作无法还原群集时的最后手段，并且删除的元数据是不可恢复的。
```

# 3 参考自https://www.cnblogs.com/kingdevops/articles/17868941.html

## 3.1 有用的命令

```
 MySQL  mycluster.default.svc.cluster.local:33060+ ssl  SQL > select @@hostname;
+-------------+
| @@hostname  |
+-------------+
| mycluster-0 |
+-------------+
```

# 4 第二次恢复过程(主节点正常)

## 4.1 强行清掉节点上的pod

```
kubectl apply -f myclustey.yaml 将实例的个数改为1后执行


kubectl delete pod mycluster1 
即有可能需要edit pod mycluster1 然后清除掉XXX

强行删除磁盘的文件
chown worker1 /mysql_data_dir
cd mysql_data_dir
rm -rf ./*

chown 27 /mysql_data_dir

kubectl apply -f myclustey.yaml 将实例的个数改为3后执行
```

## 4.2 在主节点上执行 

```
kubectl exec -it mycluster-0 -c mysql -- mysqlsh

\connect root@mycluster-0

 cluster.addInstance("mycluster-2.mycluster-instances.default.svc.cluster.local:3306")## 开启漫长的复制过程，要优先删除这个1号节点和2号节点

add完之后，rescan检查一下

```

## 4.3 检查从节点的复制过程

```
在线节点上，执行语句，如果为 RECOVERING，表示仍在同步数据
SELECT * FROM performance_schema.replication_group_members;
```

```
太漫长时候，需要对比复制过程
主节点上执行
SHOW MASTER STATUS;
| File | Position | Binlog_Do_DB | Binlog_Ignore_DB | Executed_Gtid_Set |
+------------------+----------+--------------+------------------+-------------------------------------------------------------------------------------------------------------------------------------------------------+
| mycluster.000010 | 1319770 | | | 71731bc8-e842-11ef-afdb-ba8dd8779cb0:1-9,
97cd7183-e842-11ef-bfaf-ba8dd8779cb0:1-484169:1003562-1003593,
97cd75a8-e842-11ef-bfaf-ba8dd8779cb0:1-29248 |

从节点上执行
SELECT @@GLOBAL.gtid_executed;

| @@GLOBAL.gtidgtid_executed |
+----------------------------------------------------------------------------------------------------------------------------------------------------+
| 71731bc8-e842-11ef-afdb-ba8dd8779cb0:1-8,
97cd7183-e842-11ef-bfaf-ba8dd8779cb0:1-143875:1003562-1003593,
97cd75a8-e842-11ef-bfaf-ba8dd8779cb0:1-18 |
+----------------------------------------------------------------------------------------------------------------------------------------------------+
1 row in set (0.0005 sec)
||


可以看出从节点落后主节点 340,294 个事务，复制延迟极高
```

## 4.4 未试过的方案

```
-- 在恢复节点执行
SET GLOBAL group_replication_applier_threads = 4;
```

# 5 第3次恢复方案（放弃，直接重装）

```
kubectl exec -it mycluster-0 -c mysql -- mysqlsh

\connect root@mycluster-0

STOP GROUP_REPLICATION;
SET GLOBAL group_replication_bootstrap_group=OFF;
dba.rebootClusterFromCompleteOutage('mycluster', {force: true}); // 已经开始重启并以当前节点为主节点，开始组复制
cluster.rescan()  // 清掉2个节点


cluster.addInstance('mycluster-2.mycluster-instances.default.svc.cluster.local:3306') 

 2025-03-18 11:04:56.794111 [Error] [MY-010596] Error reading relay log event for channel 'group_replication_applier': corrupted data in log event
  2025-03-18 11:04:56.794155 [Error] [MY-013121] Slave SQL for channel 'group_replication_applier': Relay log read failure: Could not parse relay log event entry. The possible reasons are: the master's binary log is corrupted (you can check this by running 'mysqlbinlog' on the binary log), the slave's relay log is corrupted (you can check this by running 'mysqlbinlog' on the relay log), a network problem, the server was unable to fetch a keyring key required to open an encrypted relay log file, or a bug in the master's or slave's MySQL code. If you want to check the master's binary log or slave's relay log, you will be able to know their names by issuing 'SHOW SLAVE STATUS' on this slave. Error_code: MY-013121
  2025-03-18 11:04:56.794191 [Error] [MY-011451] Plugin group_replication reported: 'The applier thread execution was aborted. Unable to process more transactions, this member will now leave the group.'
  2025-03-18 11:04:56.794252 [Error] [MY-011452] Plugin group_replication reported: 'Fatal error during execution on the Applier process of Group Replication. The server will now leave the group.'
  2025-03-18 11:04:56.794372 [Error] [MY-011735] Plugin group_replication reported: '[GCS] The member is already leaving or joining a group.'
  2025-03-18 11:04:56.794427 [Error] [MY-011644] Plugin group_replication reported: 'Unable to confirm whether the server has left the group or not. Check performance_schema.replication_group_members to check group membership information.'
  2025-03-18 11:04:56.7ror] [MY-011712] Plugin group_replication reported: 'The server was automatically set into read only mode after an error was detected.'
  2025-03-18 11:04:56.794526 [System] [MY-011565] Plugin group_replication reported: 'Setting super_read_only=ON.'
  2025-03-18 11:04:56.794592 [Error] [MY-010586] Error running query, slave SQL thread aborted. Fix the problem, and restart the slave SQL thread with "SLAVE START". We stopped at log 'FIRST' position 0


查询本机的select @@GLOBAL.gtid_execute


```
