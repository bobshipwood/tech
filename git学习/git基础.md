[toc]

# 1 初识git

## 1 向git自报家门

git config  --global  user.name "bobshipwood"
git config  --global  user.email "472164571@qq.com"

## 2 show下git的配置

git config --list

# 2 获取远程仓库的两种方法

## 1 在现有仓库中推送至远程仓库

git init （会在当前目录下生成 ***.git隐藏目录***）

git add *.c

git commit -m 'first commit'

git push origin master

## 2 克隆远程仓库到本地 

git clone git@github.com:bobshipwood/test.git aaa

他会在当前目录下 新建一个aaa目录，并已存在.***git隐藏目录***，如果后面不加aaa,则会新建一个test目录,并且当前分支名命名为master

# 3 查看文件状态 git status -s

如果是刚clone完毕的状态，没有*untracked*的文件，没有*modified*的文件，没有*staged*的文件，全部都是*unmodified*.

# 4 git diff 以文件补丁格式查看更详细的文件状态

## 1 git diff 比较工作区和暂存区的文件差异

## 2 git diff --cached （--staged）比较暂存区和版本库的文件差异

# 5 git add 命令

## 1 git 目录 文件名

git 目录时候，会把目录下面所有的文件都一并add

## 2 合并时候把有冲突的文件标记为已解决状态

# 6 git commit 加入-a 会把所有跟踪过的文件自动添加至缓存区一并提交

# 7 .gitignore 忽略文件（不想文件出现在*untracked*列表）

## 1 所有空行或以#号开头的都会被git忽略

## 2 使用标准的glob模式匹配(与正则不同)

### 1 * 匹配0个或多个任意字符

### 2 [abc]匹配任何一个在方括号内的字符

### 3 ？只匹配一个字符

### 4 [0-9]表示匹配任何一个所有在0-9范围内的字符

### 5 a/**/b 表示匹配任意中间目录，如a/cc/dd/b,a/b,a/cc/b

## *3 匹配模式可以以/开头，防止递归*

## *4 匹配模式可以以/结尾，指定目录*

## 5 要忽略匹配模式以外的文件或目录，可以在匹配模式内取反（！）

# 8 git rm 的选项及应用场景

## 1 -f  强制删除在缓存区的文件

## 2 -cached 删除暂存区的文件，保留工作目录的。（当忘记.gitignore时候,这一个命令尤其有用）

# 9 git mv 改名

git mv相关与三个操作

mv 源文件  目标文件

git rm 源文件

git add 目标文件

# 10 git log 命令行

## 1 -p -2

-p显示每次的提交的内容差异，-2 显示最近两次提交

## 2 --stat

查看每个commit的简略信息

## 3 --pretty=oneline  --graph

每个提交放在一行显示，--graphy'

# 11 文件回滚

## 1 取消暂存区的文件

git reset head 文件名

## 2 撤销文件修改（工作区还原修改）

这里分两种情况：

一种是自修改后还没有被放到暂存区，现在，撤销修改就回到和版本库一模一样的状态；

一种是已经添加到暂存区后，又作了修改，现在，撤销修改就回到添加到暂存区后的状态。

git checkout -- 文件名

# 12 git remote 

## 1 -v(查看简写及url)

## 2 git remote add 简写 url

git remote add pb https://github.com/paulboone/ticgit

## 3 git remote show 简写(查看远程详细信息,例如git pull git push等缩写信息) 

## 4 git remote rename 原简写 修改后简写

git remote rename pb paul

## 5 删除远程git remote rm
git remote rm 缩写



# 14 打标签

## 1 git tag 列出标签

git tag -l ‘v1.8.4*'

## 2 创建附注标签

git tag -a v1.4 -m ‘my version 1.4’

## 3 创建轻量标签

git tag v1.4-lw

## 4 查看附注标签-m添加的内容及查看轻量标签

git show v1.4

列出很详细的信息，包括-m的内容

git show v1.4-lw

列出校验和等信息

## 5 指定的提交上创建标签

git tag -a v1.2 9fceb02

## 6 推送标签至远程

git push origin --tags

## 7 删除本地标签及同步远程

git tag -d v1.4-lw

git push origin: refs/tags/v1.4-lw