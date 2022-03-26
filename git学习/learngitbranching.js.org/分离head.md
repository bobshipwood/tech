[toc]

## 1 head分离

正常情况下， head指针指向master分支，分支指针指向哈希提交

当执行 git checkout 哈希分支时候，head指针指向了哈希分支，从而分类出head指针和master指针

## 2 相对引用^和~num

1 git checkout main^      

main分支指针指向的哈希记录，往前移动一格，被HEAD指针指向。

2 把指针分离后，也可以用git checkout HEAD^，表示HEAD指针向上移动一格

3 git checkout HEAD~4 表示HEAD指针向上移动4格

## 3 强制修改分支位置

git branch -f main HEAD~3

表示HEAD分支指向的哈希提交向往前移动3格，然后用main指针分支指向他，原先HEAD分支指针指向不变。



其实也可以

git branch -f main 哈希提交记录

## 4 撤销变更

1 git reset HEAD~1

使得分支指针和HEAD指针同时指向 由HEAD指针指向的上一条记录,，同时将保留原先的记录。如果再次执行 git commit时候，他会避开原先保留的记录，新建一个新的记录。原先保留的记录将会一直存在。



对于使用远程分支的小伙伴而言，这种“改写历史”方法是无效的---我觉得不可能直接撤销远程分支某个提交，因为你不要的东西，不代表别人不要。

2 git revert HEAD

直接在后面产生了一个新的哈希提交，该提交表示撤销，相当于撤销就是一种更改，