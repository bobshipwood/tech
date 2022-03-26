[toc]

## 基本概念

### 1 git merge
```
a--b---c               topic			
  /
 d--e--f---g           master
```

在master分支下，执行git merge topic命令后（实际意思是包含另外一个分支锁做的修改），在两个分支内产生一个新节点h

```
a--b---c                   topic			
  /     \
d--e--f---g----h           master
```

### 2 git rebase

```
d----e---f      master    指向f
      \	
       g---h     feature  指向h
```

在feature分支下，执行git rebase master,将会变成一条直线

```
d---e---f---g`---h`   master指向f，  feature指向h`
```

## 高级概念

### 1 git rebase

git rebase 分支名（a）   ----> 路径存在分叉时候，在当前另一个分支（b）下操作，使得b末尾移动到a的末尾

git rebase 分支名（a）  分支名（b） -----> 路径存在分叉时候，以a分支为标的物，把b分支的内容加到a分支的末尾

## 当远程分支不让push的时候（实际应用）

### 1 假设,本地push不上去

```
本地                                远程

c0                                    c0   
|                                     |
|                                     | 
|                                     |
c1 < o/main                           c1
|                                     |
|                                     |
|                                     |
c3  < main *					      c2  < main
```

### 2  解决方案 ：git fetch； git rebase o/main; git push;

简写命令   git pull -rebase；git push

执行后，本地会变成

```
C0
|
|
|
c1
|
|
|
c2  
|
|
|
c3` < main*,o/main
```

### 3 解决方案：  git fetch;  git merge o/main; git push;

简写命令   git pull；git push

执行后，将会变成

```git
c0
|
|
|
c1
|\
| \
|  \ 
|   \
c3   c2
|    /
|   /
|  /
c4  main* o/main
```