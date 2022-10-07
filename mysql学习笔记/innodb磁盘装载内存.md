[toc]

# innodb中页面装载过程

## 1 从磁盘装入内存(Freelist)

从freeList减去一块，装载进Lrulist

## 2 如果freeList没有了，那将发生页面淘汰(Lrulist)

从Lrulist中淘汰一部分冷数据,使得内存可以装

## 3 如果Lrulist中都是加锁的数据，那内存将发生Lruflush操作(Dirtylist)

将脏页的数据落盘（Dirtylist），使得空闲出的内存可以装