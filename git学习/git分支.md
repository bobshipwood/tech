[toc]

# 1 git branch

## 1 git branch name 创建分支。head不动

## 2 git branch -u origin/severfix 在当前的本地分支上创建远程跟踪分支

git branch --set-upstream-to  <branch-name>   origin/<branch-name>

## 3 git branch -d 删除分支

# 2 git checkout

## 1 git checkout name  切换分支，head动，工作区也随之切换

##  2 git checkout -b name 等于两条命令(git branch name, git checkout name)

## 3 等于3条命令的用法

git checkout -b serverfix origin/serverfix     创建本地分支serverfix并跟踪远程分支origin/serverfix

# 3 git fetch

##  1  作用1，拉取pb/ticgit,以及pb/master等所有分支拉取到本地上，git fetch pb

## 2  作用2，不断的拉取以便及时更新远程仓库引用 

git fetch origin, 只有不断的拉取，origin/master等引用才会向前走。

## 3  抓取并更新所有的远程仓库 git fetch --all

# 4 git log 及git pull的说明

git log --oneline --decorate  查看各个分支当前所指的对象（包括head）

git log --oneline  --decorate  --graph --all  查看分叉历史及各个分支的指向情况

git pull =git fetch + git merge，一般不用git fetch

# 5 git push用法

## 1 远程和本地分支同名git push origin severfix

## 2 不同名，git push origin severfix(本地分支):awesomebranch（远程分支）
## 3 删除远端分支 git push origin --delete serverfix  

# 6 分支合并

##　1 简单分支合并（fastforward）git只是简单的将指针向前移动

git checkout master
git merge hotfix

## 2 三方合并（三方是指共同祖先，及两个分支末端所指的快照）做了个新的快照并且自动创建了一个新的提交指向他

git checkout master
git merge iss53

## 3 遇到冲突时候的分支合并（多人对同一个文件修改）

### 1 屏幕上出现CONFLICT字样，提示Automatic merge failed
### 2 此时git做了合并，但是没有自动创建一个新的合并提交
### 3 git status 在unmerged paths提示有那些文件冲突
### 4 手动打开冲突的文件，并解决冲突，并通过git add 命令将其标示为冲突已解决，然后commit上去。

## 4 变基一般情况（新手远离）

### 1 先把修改复制到master，因为这个时候experimentl的log来看领先master的一次，所以在master执行merge

git checkout experiment
git rebase master

git checkout master
git merge experiment 

### 2 也可以直接变基，git rebase master[目标分支，把变化应用到该分支下] server[特性分支]
git checkout master
git merge server

## 5 变基特殊情况

分支情况：先master，再server，最后client

git rebase --onto master server client
取出client分支，找出处于client分支和server分支的共同祖先之后的修改，然后把他们在master分支上重放一遍。
git checkout master
git merge client

## 6 缓解变基的痛点（团队协助中一人用变基，另一个人不用，将会产生冗余）

## 1 所有人执行 git pull rebase命令

习惯用git pull，可以这样设置git config --global pull.rebase true

## 2 先git fetch，再git rebase teamone/master