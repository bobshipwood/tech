[toc]

# 1 mysql覆盖索引与回表

## 1 InnoDB**聚集索引**的叶子节点存储行记录，因此， InnoDB必须要有，且只有一个聚集索引：

（1）如果表定义了PK，则PK就是聚集索引；

（2）如果表没有定义PK，则第一个not NULL unique列是聚集索引；

（3）否则，InnoDB会创建一个隐藏的row-id作为聚集索引；

*画外音：所以PK查询非常快，直接定位行记录。*

## 2 InnoDB**普通索引**的叶子节点存储主键值。

  不妨设有表：

   t(id PK, name KEY, sex, flag);

（1）id为PK，聚集索引，叶子节点存储行记录；

（2）name为KEY，普通索引，叶子节点存储PK（主键id）值，即id；

## 3 回表查询

这就是所谓的**回表查询**，先定位主键值（先走普通索引），再定位行记录（再走聚簇索引），它的性能较扫一遍索引树更低。

## 4 何为覆盖索引

只需要在一棵索引树上就能获取SQL所需的所有列数据，无需回表，速度更快。

### 1*select id,name from user where name='shenjian';*

一次普通索引就能命中，无需回表

*Extra：**Using index**。*

### 2 *select id,name**,sex** from user where name='shenjian';*

能够命中name索引，索引叶子节点存储了主键id，但sex字段必须回表查询才能获取到，不符合索引覆盖，需要再次通过id值扫码聚集索引获取sex字段，效率会降低。

Extra：**Using index condition**。*

# 2 explain的信息

## 1 表的读取顺序。（对应id）--重点关注

id的值表示select子句或表的执行顺序，id相同，执行顺序从上到下，id不同，值越大的执行优先级越高。

## 2 数据读取操作的操作类型。（对应select_type）

### 1.SIMPLE

简单的select查询，查询中不包含子查询或union查询。

### 2.PRIMARY

查询中若包含任何复杂的子部分，最外层查询为PRIMARY，也就是最后加载的就是PRIMARY。

### 3.SUBQUERY

在select或where列表中包含了子查询，就为被标记为SUBQUERY。

### 4.DERIVED

在from列表中包含的子查询会被标记为DERIVED(衍生)，MySQL会递归执行这些子查询，将结果放在临时表中。

### 5.UNION

若第二个select出现在union后，则被标记为UNION，若union包含在from子句的子查询中，外层select将被标记为DERIVED。

### 6.UNION RESULT

从union表获取结果的select。

## 3 哪些索引可以使用。（对应possible_keys）

显示可能应用在表中的索引，可能一个或多个。查询涉及到的字段若存在索引，则该索引将被列出，但不一定被查询实际使用。

## 4 哪些索引被实际使用。（对应key）---重点关注

实际中使用的索引，如为NULL，则表示未使用索引。若查询中使用了覆盖索引，则该索引和查询的select字段重叠。

## 5 对应（key_len）

表示索引中所使用的字节数，可通过该列计算查询中使用的索引长度。在不损失精确性的情况下，长度越短越好。key_len显示的值为索引字段的最大可能长度，并非实际使用长度，即key_len是根据表定义计算而得，并不是通过表内检索出的。

## 6 表直接的引用。对应（ref）----重点关注

显示关联的字段。如果使用常数等值查询（tb_emp.name='rose'为一个常量，所以ref=const），则显示const，如果是连接查询，则会显示关联的字段。

## 7 每张表有多少行被优化器查询。对应(rows)

根据表统计信息及索引选用情况大致估算出找到所需记录所要读取的行数。当然该值越小越好。

## 8 对应(filtered)

百分比值，表示存储引擎返回的数据经过滤后，剩下多少满足查询条件记录数量的比例。

## 9（对应type)----重点关注

### 1 表示查询所使用的访问类型，type的值主要有八种，该值表示查询的sql语句好坏，从最好到最差依次为：system>const>eq_ref>ref>range>index>ALL。

注：对于system和const可能实际意义并不是很大，因为单表单行查询本来就快，意义不大。

注：一般来说，需保证查询至少达到**range**级别，最好能达到**ref**。

### 2 system

### 3 const

### 4 eq_ref

唯一索引扫描，对于每个索引键，表中只有一条记录与之匹配。常见主键或唯一索引扫描。(不常见，如经理只有一人，is_manager = 1)

### 5 ref

非唯一性索引扫描，返回匹配某个单独值的所有行。本质上也是一种索引访问，返回匹配某值（某条件）的多行值，属于查找和扫描的混合体。

### 6 range

只检索给定范围的行，使用一个索引来检索行，可以在key列中查看使用的索引，一般出现在where语句的条件中，如使用between、>、<、in等查询。

这种索引的范围扫描比全索引扫描要好，因为索引的开始点和结束点都固定，范围相对较小。

### 7 index

全索引扫描，index和ALL的区别：index只遍历索引树，通常比ALL快，因为索引文件通常比数据文件小。虽说index和ALL都是全表扫描，但是index是从索引中读取，ALL是从磁盘中读取。

### 8 All

全表扫描。

## 10 Extra (显示十分重要的额外信息)----重点关注

### 1.Using filesort

Using filesort表明mysql会对数据使用一个外部的索引排序，而不是按照表内的索引顺序进行读取。

mysql中无法利用索引完成的排序操作称为“文件排序”。

出现Using filesort就非常危险了，在数据量非常大的时候几乎“九死一生”。

出现Using filesort尽快优化sql语句。

### 2.Using temporary

使用了临时表保存中间结果，常见于排序order by和分组查询group by。非常危险，“十死无生”，急需优化。

### 3.Using index

表明相应的select操作中使用了**覆盖索引**，避免访问表的额外数据行，效率不错。

如果同时出现了Using where，表明索引被用来执行索引键值的查找。（where deptid=1）

如果没有同时出现Using where，表明索引用来读取数据而非执行查找动作。

# 3 创建索引的规则

## 1 **left join（左连接）：右表创建索引。**

## 2 **right join（右连接）：左表创建索引。**

## 3 最佳左前缀法
### 1 没火车头不能跑

### 2 火车头单独跑没问题，火车头与直接相连的车厢一起跑也没问题，但是火车头与车尾，如果中间没有车厢，只能火车头自己跑。

### 3  火车头加车厢加车尾，三者串联，就变成了奔跑的小火车。

### 4  当三者串联，组成覆盖索引，全部是=的时候，顺序不重要

## 4 在索引列上做任何操作（计算、函数、（自动or手动）类型转换），会导致索引失效从而转向全表扫描。

### 1 where left(name,3) = 'tom'

### 2 name=123，使用sql后，发生了类型转换，type=ALL，导致全表扫描。

## 4 范围右边（>,<）的索引列会失效。

### 1 name age gender 建立组合索引

### 2 where name='jack' and age = 27 and gender = 'femail' (key_lenth =128)

### 3 where name='jack' and age > 27 and gender = 'femail' (key_lenth =86) ,没有使用到gender索引

### 4 范围右边索引列失效，当使用上全部条件（c1,c2,c3,c4）的时候，where 出现的顺序不重要（底层会优化）,但是失效是有顺序的：c1,c2,c3,c4，如果c3有范围，则c4失效；如果c4有范围，则没有失效的索引列，从而会使用全部索引。

## 5 使用（!=,<>,is null is not null）会导致索引失效

name is null  name != 'jack'  

## 6 尽量使用覆盖索引

### 1 查询列和索引列尽量一致，通俗说就是对A、B列创建了索引，然后查询中也使用A、B列，减少select *的使用。

### 2 实际语句分析

name age gender 建立组合索引

select age from t where name  name like '%jack%'

select name，age from t where name  name like '%jack%'

select name，age，gender from t where name  name like '%jack%'

select id name，age，gender from t where name  name like '%jack%'

以上sql都能使用上覆盖索引。
当覆盖索引列全部串联，且全部是=的时候，顺序不重要


## 7 like通配符以%开头，会导致索引失效

出现这种情况解决方法是使用覆盖索引

## 8 少用or，用or连接会使索引失效

where name = 'jack' or name = 'mary'

## 9 order by 索引规则（覆盖索引）（避免using filesort）

### 0 key  a_b_c（a,b,c）

### 1 能使用索引最左前缀（避免using filesort）

order by a

order by a，b

order by a,b,c

order by a desc,b desc,c desc

#### 2 如果where 使用最左前缀定义为常量，则能使用索引

where a = const order by b ，c

where a = const and b = const order by c

where a = const and b > const order by b c

### 3 不能使用索引的情况

order by a asc, b desc,c desc         排序不一致

where g = const order by b,c        丢失a索引

where a = const  order by c           丢失b索引

where a =const order by a，d     d不是索引的一部分 

where a in （。。。）  order by b，c   **对于排序来说，多个相等条件也是范围查询**