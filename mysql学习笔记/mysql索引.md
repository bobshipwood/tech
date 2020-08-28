[toc]

# 1 索引分类及创建索引的方式

## 1.添加主键索引 
ALTER TABLE `table_name` ADD PRIMARY KEY (`column`) 

## 2.添加唯一索引
ALTER TABLE `table_name` ADD UNIQUE (`column`) 

## 3.添加全文索引
ALTER TABLE `table_name` ADD FULLTEXT (`column`) 

## 4.添加普通索引
ALTER TABLE `table_name` ADD INDEX index_name (`column` ) 

## 5.添加组合索引 
ALTER TABLE `table_name` ADD INDEX index_name (`column1`, `column2`, `column3`)

# 2 需要建立索引的情况

## 1.主键自动建立唯一索引。

## 2.频繁作为查询条件的字段。

## 3.查询中与其他表关联的字段，外键关系建立索引。(一般开发中不用外键)

## 4.高并发下趋向创建组合索引。

## 5.查询中排序的字段，排序字段若通过索引去访问将大大提高排序速度。

## 6.查询中统计或分组字段。

# 3 不需要创建索引的情况

## 1.表记录太少。（数据量太少MySQL自己就可以搞定了）

## 2.经常增删改的表。

## 3.数据重复且平均分配的字段，如国籍、性别，不适合创建索引。

## 4.频繁更新的字段不适合建立索引。

## 5.Where条件里用不到的字段不创建索引。

