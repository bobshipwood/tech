[toc]

## 1 mysql 3层架构

### 1 Client Connectors层

负责处理客户端的请求，与客户端创建连接。例如常见的jdbc，go等

### 2 server 层

#### 1 Conection Pool

一个线程负责管理一个连接。包含了用户验证模块等

#### 2 Service & utilities 

包括备份恢复，安全管理，集群管理和工具

#### 3 SQL interface

负责接收客户端发来的各种sql语句，比如DML，DDL和存储过程等。

#### 4 解析器

Praser解析器会对各sql语句进行语法分析生成解析树

#### 5 优化器

Optimizer 查询优化器会根据解析数生成执行计划，并选择合适的索引

#### 6 Caches

包括各个存储引擎的缓存部分，如innodb的buffer pool myisam的key buffer等。，此外cache中也会缓存一些权限，包括一些session级别的缓存。

### 3 存储引擎层

包括myisam，innodb及支持归档的archive，和内存的memory等。

## 2 WAL（ARIES三原则）（Write Ahead Logging）(原子性保证的关键)

### 1 先写日志后写磁盘，日志成功写入事务后就不会丢失，后续由checkpoint机制来保证磁盘物理文件与Redo日志达到一致性；

### 2 用Redo记录变更后的数据，既Redo记录事务数据变更后的值；

### 3 用Undo记录变更前的数据，既Undo记录事务变更前的值，用于回滚和其他事务多版本读。

## 3 4种隔离级别与脏读，不可重复读，幻读的关系

### 1 脏读

一个事务正对一条记录进行修改，这个事务未提交前，这条记录的数据就处于不一致的状态；
如果另一个事务也来读取同一条记录，如果不加控制，则第2个事务读取了“脏”数据。并据此进行进一步的处理，就会产生未提交的数据依赖关系。这种现象称之为“脏读”

### 2 不可重复读（针对update ，delete）

当一个事务再次读取以前读过的数据，却发现数据已经发生了改变，或者删除了，这种现象叫作“不可重复读”

### 3 幻读（针对insert）

一个事务按相同条件重新读取以前读的记录，却发现其他事务插入了新的数据，。这种现象叫“幻读”

### 4 与隔离级别的关系

| 隔离级别                     | 脏读   | 不可重复读 | 幻读                                                         | 自我备注                                                     |
| ---------------------------- | ------ | ---------- | ------------------------------------------------------------ | ------------------------------------------------------------ |
| 未提交读（Read uncommitted） | 可能   | 可能       | 可能                                                         | 基本不会用到                                                 |
| 已提交读（Read commited）    | 不可能 | 可能       | 可能                                                         |                                                              |
| 可重复读（Repeatable Read）  | 不可能 | 不可能     | 可能（通过间歇锁（GAP）解决，update前加锁，拒绝其他事务insert） | 默认级别。但经常发生死锁，低并发等问题                       |
| 可串行化（Serializable）     | 不可能 | 不可能     | 不可能                                                       | 已经不是多版本（mvcc）了，又回到了单版本的状态，因为他所有实现都是通过锁 |
## 4 并发事务控制

### 1 单版本控制（锁）

### 2 多版本控制（MVCC）（MVCC只在COMMITTED READ（读提交）和REPEATABLE READ（可重复读）两种隔离级别下工作）

### 3 一般建议

在RR（repeatable read）的模式下，为了解决幻读问题，会使用间隙性GAP锁，而这种锁由于并行度不够，冲突很多，经常引起死锁。
现在流形的ROW模式可以很大程度避免此类问题，所以一般采用 ROW+RC模式

## 5 mysql锁分类及其他重要知识

### 1 三种锁分类

| 锁分类 | 开销     | 加锁速度 | 死锁可能性 | 锁冲突概率 | 并发度 | 存储引擎                    |
| ------ | -------- | -------- | ---------- | ---------- | ------ | --------------------------- |
| 表级锁 | 小       | 快       | 无         | 大         | 低     | myisam，innodb，memory，BDB |
| 行级锁 | 大       | 慢       | 会出现死锁 | 最低       | 最高   | innodb                      |
| 页级锁 | 两者之间 |          | 会         |            | 一般   | BDB                         |

### 2 innodb 两阶段锁协议

#### 0 死锁不会发生在myisam引擎中

在自动加锁的情况下，MyISAM 总是一次获得 SQL 语句所需要的全部锁，所以 MyISAM 表不会出现死锁。

#### 1 **隐式锁定 **

随时都可以执行锁定，InnoDB会根据隔离级别在需要的时候自动加锁；

#### 2 **显式锁定 **

```text
select ... lock in share mode //共享锁 s
select ... for update //排他锁 x
```

#### 3 select for update 使用场景

在执行这个 select 查询语句的时候，会将对应的索引访问条目进行上排他锁（X 锁），也就是说这个语句对应的锁就相当于update带来的效果。

**select  for update 的使用场景：**为了让自己查到的数据确保是最新数据，并且查到后的数据只允许自己来修改的时候，需要用到 for update 子句。

#### 4 select lock in share mode 使用场景

in share mode 子句的作用就是将查找到的数据加上一个 share 锁，这个就是表示其他的事务只能对这些数据进行简单的select 操作，并不能够进行 DML(UPDATE、INSERT、DELETE) 操作。

**select  lock in share mode 使用场景：**为了确保自己查到的数据没有被其他的事务正在修改，也就是说确保查到的数据是最新的数据，并且不允许其他人来修改数据。但是自己**不一定能够**修改数据，因为有可能其他的事务也对这些数据也使用了 in share mode 的方式上了 S 锁。

#### 5 S锁和X锁性能影响
select for update 语句，相当于一个 update 语句。在业务繁忙的情况下，如果事务没有及时的commit或者rollback 可能会造成其他事务长时间的等待，从而影响数据库的并发使用效率。
select lock in share mode 语句是一个给查找的数据上一个共享锁（S 锁）的功能，它允许其他的事务也对该数据上S锁，但是不能够允许对该数据进行修改。如果不及时的commit 或者rollback 也可能会造成大量的事务等待。

#### 6 S锁和X锁区别：

前一个上的是排他锁（X 锁），一旦一个事务获取了这个锁，其他的事务是没法在这些数据上执行 for update ；

后一个是共享锁，多个事务可以同时的对相同数据执行 lock in share mode。

#### 7 锁释放

锁只有在执行commit或者rollback的时候才会释放，并且所有的锁都是在**同一时刻**被释放

#### 8 加行锁前，必须获得表锁，否则等待innodb_lock_wait_timeout,超时后根据innodb_rockback_on_time_out来决定是否回滚事务

#### 9 S，X，is，ix的sql语句

```csharp
select id from t where id = 1 for update;  # 获取id=1数据行的排它锁

select id from t where id =1 lock in share mode;  # 获取id=1数据行上的共享锁
    
select lock in share mode; #可以获得IS锁

select ... for update;  # 获得IX锁
```

#### 10 冲突关系

| 下列表示当前锁模式，右列请求锁模式 | is   | ix   | s    | x    |
| ---------------------------------- | ---- | ---- | ---- | ---- |
| is                                 | Y    | Y    | Y    | N    |
| ix                                 | Y    | Y    | N    | N    |
| s                                  | Y    | N    | Y    | N    |
| x                                  | N    | N    | N    | N    |

#### 11 S共享锁与X排它锁兼容性示例（使用默认的RR隔离级别，图中数字从小到大标识操作执行先后顺序）：

| 事务A                                                        | 事务B                                                        |
| ------------------------------------------------------------ | ------------------------------------------------------------ |
| 1 start transaction                                          |                                                              |
| 2 select * from stock where id = 1 lock in share model<br />（获取共享锁成功） |                                                              |
|                                                              | 3 start transaction                                          |
|                                                              | 4 select * from stock where id = 1 lock in share model<br />（获取共享锁成功） |
| 5 update stock set amount = amount -10 where id = 1;<br />(尝试获取排他锁失败)<br />error 1205 lock wait timeout exceed；try restarting transaction， |                                                              |
|                                                              | 6 commit;事务B 提交，释放共享锁                              |
| 7 update stock set amout = amount - 10 where id = 1；<br />（获取排他锁成功） |                                                              |
|                                                              | 8 重新开启事务（start transaction）                          |
|                                                              | 9 select * from stock where id = 1 lock in share model（尝试获取共享锁失败） |
|                                                              | 10 update stock set amount = amount - 10 where id = 1尝试获取排他锁失败 |

#### 12 当前读（加锁）

即加锁读，读取记录的最新版本，会加锁保证其他并发事务不能修改当前记录，直至获取锁的事务释放锁；包含以下几种情况

显式加『lock in share mode』与『for update』，『update』『insert 』『delete』

#### 13 快照读（不加锁）

即不加锁读，读取记录的快照版本而非最新版本

InnoDB默认的RR事务隔离级别下，不显式加『lock in share mode』与『for update』的『select』操作都属于快照读，保证事务执行过程中只有第一次读之前提交的修改和自己的修改可见，其他的均不可见；

### 3 innodb行锁实现方式

#### 1 只有通过索引条件检索数据，InnoDB 才使用行级锁，否则，InnoDB 将使用表锁！

InnoDB 行锁是通过给索引上的索引项加锁来实现的，这一点 MySQL 与 Oracle 不同，后者是通过在数据块中对相应数据行加锁来实现的。只有通过索引条件检索数据，InnoDB 才使用行级锁，否则，InnoDB 将使用表锁！

#### 2 不论是使用主键索引、唯一索引或普通索引，InnoDB 都会使用行锁来对数据加锁。

#### 3 只有执行计划真正使用了索引，才能使用行锁(常用explain)：

只有执行计划真正使用了索引，才能使用行锁：即便在条件中使用了索引字段，但是否使用索引来检索数据是由 MySQL 通过判断不同执行计划的代价来决定的，如果 MySQL 认为全表扫描效率更高，比如对一些很小的表，它就不会使用索引，这种情况下 InnoDB 将使用表锁，而不是行锁。因此，在分析锁冲突时，
别忘了检查 SQL 的执行计划（可以通过 explain 检查 SQL 的执行计划），以确认是否真正使用了索引。

#### 4  由于 MySQL 的行锁是针对索引加的锁，不是针对记录加的锁，所以虽然多个session是访问不同行的记录， 但是如果是使用相同的索引键， 是会出现锁冲突的（后使用这些索引的session需要等待先使用索引的session释放锁后，才能获取锁）。 应用设计的时候要注意这一点。

