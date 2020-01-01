[TOC]



# 1 git图

![1549450201176](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1549450201176.png)

工作区有一个隐藏目录`.git`，这个不算工作区，而是Git的版本库。

Git的版本库里存了很多东西，其中最重要的就是称为stage（或者叫index）的暂存区，还有Git为我们自动创建的第一个分支`master`，以及指向`master`的一个指针叫`HEAD`。

# 2 git 安装及基本操作

## 2.1 gitconfig(--system  --gloabl  --local)

/etc/gitconfig  系统中对所有用户都普遍适用的配置。若使用 git config 时用 --system 选项，读写的就是这个文件。
~/.gitconfig    用户目录下的配置文件只适用于该用户。若使用 git config 时用 --global 选项，读写的就是这个文件。
工作目录中的 .git/config 文件       这里的配置仅仅针对当前项目有效

每一个级别的配置都会覆盖上层的相同配置，所以 .git/config 里的配置会覆盖 /etc/gitconfig 中的同名变量。

## 2.2 设置email和地址

因为Git是分布式版本控制系统，所以，每个机器都必须自报家门

git config --global user.name 'runoob'
git config --global user.email test@runoob.com



## 2.3 查看当前的配置项目

git config user.name



## 2.4 检出项目 （如果先在本地有项目，需要push到远程服务器，则选择git init）

git clone --depth=1（克隆最新的代码） url 目录名

```
$ git clone git://github.com/schacon/grit.git mygrit
```



## 2.5 git status 查看本地工作区的状态

git status （-s 简短输出）

"AM" 状态的意思是，这个文件在我们将它添加到缓存之后又有改动。
改动后我们再执行 git add 命令将其添加到缓存中：A代表添加到缓存中(git add) 



## 2.6 git add  建立起文件跟踪

git add 新文件
git add . （添加当前项目的所有文件）



## 2.7 git diff 

尚未缓存的改动：git diff（工作区已修改，但未git add的差异？）（查看工作区与暂存区的不同。）



查看已缓存的改动： git diff --cached（已git add， 但未git commit的差异？）（查看暂存区与指定提交版本的不同，版本可缺省）



**用git diff HEAD -- readme.txt命令可以查看工作区和版本库里面最新版本的区别**

## 2.8 git commit 

git commit -m '第一次版本提交'
git commit -am '修改 hello.php 文件'（-a 省略掉git add的步奏）



## 2.9 git checkout -- readme.txt

在工作区更改了readme后。

`git checkout`-- readme.txt  其实是用版本库里的版本替换工作区的版本，无论工作区是修改还是删除，都可以“一键还原”

**如没有被放到缓存存区，现在，撤销修改就回到和版本库一模一样的状态；(从版本库中回退)。**

如已经添加到暂存区后，又作了修改，现在，撤销修改就回到添加到暂存区后的状态。（从暂存区中回退）

总之，就是让这个文件回到最近一次`git commit`或`git add`时的状态。

**在工作区更改readme后，且已经到了缓存区**

用命令git reset HEAD <file>可以把暂存区的修改撤销掉（unstage,未缓存）(用HEAD，表示最新的版本？)

丢弃工作区的修改，用 git checkout -- readme.txt



## 2.10 git reset head(文件名)

简而言之，以取消之前 git add 添加缓存区的内容（从版本库中还原文件至缓存区）。


用于提交版本库之前，指定缓存区的文件还原（从版本库中还原该文件），再提交版本库。



## 2.11 总结回退文件的方法

**场景1：当你改乱了工作区某个文件的内容，想直接丢弃工作区的修改时，用命令git checkout -- file。**

**场景2：当你不但改乱了工作区某个文件的内容，还添加到了暂存区时，想丢弃修改，分两步，第一步用命令git reset HEAD <file>，就回到了场景1，第二步按场景1操作。**

**场景3：已经提交了不合适的修改到版本库时，想要撤销本次提交，那就版本回退，不过前提是没有推送到远程库。**



## 2.12 git rm  <file>

删除之前修改过并且已经放到暂存区域的话，则必须要用强制删除选项 -f

git rm -f <file>

如果把文件从暂存区域移除，但仍然希望保留在当前工作目录中，换句话说，仅是从跟踪清单中删除，使用 --cached 选项即可
**git rm --cached <file>**

可以递归删除，即如果后面跟的是一个目录做为参数，则会递归删除整个目录中的所有子目录和文件
git rm –r * 



# 3 git 连接远程仓库

 ## 3.1 先有本地库，后有远程库的时候，如何关联远程库：

1 在github上新建仓库

 2 检测连上github没有？用 ssh -T git@github.com

 2 本地先定义目录，执行git init，然后添加文件至版本库

 4 执行以下命令    git remote add origin git@github.com:michaelliao/learngit.git

 5 git push -u origin master（由于远程库是空的，我们第一次推送`master`分支时，加上了`-u`参数，Git不但会把本地的`master`分支内容推送的远程新的`master`分支，还会把本地的`master`分支和远程的`master`分支关联起来，在以后的推送或者拉取时就可以简化命令。）之后，可以git push origin master，把本地`master`分支的最新修改推送至GitHub。

 6 *当远程库有更新时候。先git fetch （origin），然后在执行git merge （origin/master）（其中origin为本地所指定的主机别名？）*

## 3.2 是先创建远程库，然后，从远程库克隆：

git clone git@github.com:michaelliao/gitskills.git



## 3.3 git remote 其他命令

删除已有的GitHub远程库：

```
git remote rm origin
```

关联码云的远程库（注意路径中需要填写正确的用户名）：

```
git remote add origin git@gitee.com:liaoxuefeng/learngit.git
```

## 3.4 远程连接库的其他分支

建远程`origin`的`dev`分支到本地，于是他用这个命令创建本地`dev`分支：

```
$ git checkout -b dev origin/dev
```

当`git pull`失败，原因是没有指定本地`dev`分支与远程`origin/dev`分支的链接，根据提示，设置`dev`和`origin/dev`的链接：

```
$ git branch --set-upstream-to=origin/dev dev
Branch 'dev' set up to track remote branch 'dev' from 'origin'
```

# 4.1 分支管理

1 一开始的时候

![1549464226289](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1549464226289.png)



2 当我们创建新的分支，例如`dev`时，Git新建了一个指针叫`dev`，指向`master`相同的提交，再把`HEAD`指向`dev`，就表示当前分支在`dev`上：（git check out -b dev）



![1549464303994](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1549464303994.png)

3 不过，从现在开始，对工作区的修改和提交就是针对`dev`分支了，比如新提交一次后，`dev`指针往前移动一步，而`master`指针不变（git commit -m ‘d’）

![1549464360703](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1549464360703.png)

4 我们在`dev`上的工作完成了，就可以把`dev`合并到`master`上。Git怎么合并呢？最简单的方法，就是直接把`master`指向`dev`的当前提交，就完成了合并；所以Git合并分支也很快！就改改指针，工作区内容也不变！

（git chechout master；git merge dev）

![1549464532716](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1549464532716.png)

5 合并完分支后，甚至可以删除`dev`分支。删除`dev`分支就是把`dev`指针给删掉，删掉后，我们就剩下了一条`master`分支：（git branch -d dev）

![1549464585130](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1549464585130.png)

6 基本命令

查看分支：`git branch`           `git branch`命令会列出所有分支，当前分支前面会标一个`*`号。

创建分支：`git branch <name>`



切换分支：`git checkout <name>`

当你切换分支的时候，Git 会用该分支的最后提交的快照替换你的工作目录的内容， 所以多个分支不需要多个目录。



创建+切换分支：`git checkout -b <name>` 相当于

```
git branch dev
git checkout dev
```



合并某分支到当前分支：`git merge <name>`

***在主分支状态下，执行git merge <name>后，会把<name>中删除文件（test.php），(test.php是在创建分支前主分支就存在的文件)，也一并合并过来，即在主分支中删除掉。*******

-------------------------以下是用于实际分支开发---------------------------------------------------------------------------

在dev分支中提交文件

```
$ git add readme.txt 
$ git commit -m "branch test"
[dev b17d20e] branch test
 1 file changed, 1 insertion(+)
```

切换回master分支

```
$ git checkout master
Switched to branch 'master'
```

切换回`master`分支后，再查看一个readme.txt文件，刚才添加的内容不见了！因为那个提交是在`dev`分支上，而`master`分支此刻的提交点并没有变：

现在，我们把`dev`分支的工作成果合并到`master`分支上：

```
$ git merge dev
Updating d46f35e..b17d20e
Fast-forward
 readme.txt | 1 +
 1 file changed, 1 insertion(+)
```

git merge`命令用于合并指定分支到当前分支。合并后，再查看readme.txt的内容，就可以看到，和`dev`分支的最新提交是完全一样的。

合并完成后，就可以放心地删除`dev`分支了：

```
$ git branch -d dev
Deleted branch dev (was b17d20e).
```

因为创建、合并和删除分支非常快，**所以Git鼓励你使用分支完成某个任务***，合并后再删掉分支，这和直接在`master`分支上工作效果是一样的，但过程更安全.

-------------------------------------以上是用于实际分支开发------------------------------------------------------------------------

删除分支：`git branch -d <name>`

# 4.2 分支冲突

1 在feature1分支修改了a文件，并commit

2 在主分支也修改了a文件，也commit

![1549504812284](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1549504812284.png)

3 这种情况下，Git无法执行“快速合并”，只能试图把各自的修改合并起来，但这种合并就可能会有冲突，我们试试看：在主分支执行 git merge feature，命令行提示冲突文件（Automatic merge failed; fix conflicts and then commit the result.）

```
$ git merge feature1
Auto-merging readme.txt
CONFLICT (content): Merge conflict in readme.txt
Automatic merge failed; fix conflicts and then commit the result.
```

4 果然冲突了！Git告诉我们，`readme.txt`文件存在冲突，必须手动解决冲突后再提交。

5 `git status`也可以告诉我们冲突的文件：

```
$ git status
On branch master
Your branch is ahead of 'origin/master' by 2 commits.
  (use "git push" to publish your local commits)

You have unmerged paths.
  (fix conflicts and run "git commit")
  (use "git merge --abort" to abort the merge)

Unmerged paths:
  (use "git add <file>..." to mark resolution)

    both modified:   readme.txt

no changes added to commit (use "git add" and/or "git commit -a")
```

6 手动修改冲突文件。（vim）

```
Git is a distributed version control system.
Git is free software distributed under the GPL.
Git has a mutable index called stage.
Git tracks changes of files.
<<<<<<< HEAD
Creating a new branch is quick & simple.
=======
Creating a new branch is quick AND simple.
>>>>>>> feature1
```

7 用git add readme.txt文件要告诉 Git 文件冲突已经解决

```
$ git add readme.txt 
$ git commit -m "conflict fixed"
```

8 图示，合并后删除`feature1`分支：

![1549505651327](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1549505651327.png)

9 当Git无法自动合并分支时，就必须首先解决冲突。解决冲突后，再提交，合并完成。

解决冲突就是把Git合并失败的文件手动编辑为我们希望的内容，再提交。

用`git log --graph`命令可以看到分支合并图。

# 5 分支管理策略

1 在实际开发中，我们应该按照几个基本原则进行分支管理：

首先，`master`分支应该是非常稳定的，也就是仅用来发布新版本，平时不能在上面干活；

那在哪干活呢？干活都在`dev`分支上，也就是说，`dev`分支是不稳定的，到某个时候，比如1.0版本发布时，再把`dev`分支合并到`master`上，在`master`分支发布1.0版本；

你和你的小伙伴们每个人都在`dev`分支上干活，每个人都有自己的分支，时不时地往`dev`分支上合并就可以了。

所以，团队合作的分支看起来就像这样：

![git-br-policy](https://cdn.liaoxuefeng.com/cdn/files/attachments/001384909239390d355eb07d9d64305b6322aaf4edac1e3000/0)

2  通常，合并分支时，如果可能，Git会用`Fast forward`模式，也就是直接把`master`指向`dev`的当前提交，所以合并速度非常快。但这种模式下，删除分支后，会丢掉分支信息。

![git-br-dev-fd](https://cdn.liaoxuefeng.com/cdn/files/attachments/0013849088235627813efe7649b4f008900e5365bb72323000/0)

![1549509365164](C:\Users\bob\AppData\Roaming\Typora\typora-user-images\1549509365164.png)

合并分支时，加上`--no-ff`参数就可以用普通模式合并，合并后的历史有分支，能看出来曾经做过合并，而`fast forward`合并就看不出来曾经做过合并。

```
1 $ git checkout -b dev
Switched to a new branch 'dev'

2 $ git add readme.txt 
$ git commit -m "add merge"
[dev f52c633] add merge
 1 file changed, 1 insertion(+)
 
 
3 $ git checkout master
Switched to branch 'master'

因为本次合并要创建一个新的commit，所以加上-m参数，把commit描述写进去。

$ git merge --no-ff -m "merge with no-ff" dev
Merge made by the 'recursive' strategy.
 readme.txt | 1 +
 1 file changed, 1 insertion(+)

4 $ git log --graph --pretty=oneline --abbrev-commit
*   e1e9c68 (HEAD -> master) merge with no-ff
|\  
| * f52c633 (dev) add merge
|/  
*   cf810e4 conflict fixed
```

可以看到，不使用`Fast forward`模式，merge后就像这样：

![git-no-ff-mode](https://cdn.liaoxuefeng.com/cdn/files/attachments/001384909222841acf964ec9e6a4629a35a7a30588281bb000/0)

# 6 bug 分支

当你接到一个修复一个代号101的bug的任务时，很自然地，你想创建一个分支`issue-101`来修复它，但是，等等，当前正在`dev`上进行的工作还没有提交：

并不是你不想提交，而是工作只进行到一半，还没法提交，预计完成还需1天时间。但是，必须在两个小时内修复该bug，怎么办？ 

幸好，Git还提供了一个`stash`功能，可以把当前工作现场“储藏”起来，等以后恢复现场后继续工作：

```
$ git stash
Saved working directory and index state WIP on dev: f52c633 add merge
```

现在，用`git status`查看工作区，就是干净的（除非有没有被Git管理的文件），因此可以放心地创建分支来修复bug。

首先确定要在哪个分支上修复bug，假定需要在`master`分支上修复，就从`master`创建临时分支：

```
$ git checkout master
Switched to branch 'master'
Your branch is ahead of 'origin/master' by 6 commits.
  (use "git push" to publish your local commits)

$ git checkout -b issue-101
Switched to a new branch 'issue-101'
```

修复完成后，切换到`master`分支，并完成合并，最后删除`issue-101`分支：

```
$ git checkout master
Switched to branch 'master'
Your branch is ahead of 'origin/master' by 6 commits.
  (use "git push" to publish your local commits)

$ git merge --no-ff -m "merged bug fix 101" issue-101
Merge made by the 'recursive' strategy.
 readme.txt | 2 +-
 1 file changed, 1 insertion(+), 1 deletion(-)
```

太棒了，原计划两个小时的bug修复只花了5分钟！现在，是时候接着回到`dev`分支干活了！

工作区是干净的，刚才的工作现场存到哪去了？用`git stash list`命令看看：

```
$ git stash list
stash@{0}: WIP on dev: f52c633 add merge
```

工作现场还在，Git把stash内容存在某个地方了，但是需要恢复一下，有两个办法：

一是用`git stash apply`恢复，(git stash apply stash@{0})但是恢复后，stash内容并不删除，你需要用`git stash drop`来删除；

另一种方式是用`git stash pop`，恢复的同时把stash内容也删了：

```
$ git stash pop
On branch dev
Changes to be committed:
  (use "git reset HEAD <file>..." to unstage)

    new file:   hello.py

Changes not staged for commit:
  (use "git add <file>..." to update what will be committed)
  (use "git checkout -- <file>..." to discard changes in working directory)

    modified:   readme.txt

Dropped refs/stash@{0} (5d677e2ee266f39ea296182fb2354265b91b3b2a)
```



# 7 多人协作

查看当前的远程库

git remote（-v，你还可以看到每个别名的实际链接地址） 查看当前有哪些远程仓库

推送时，要指定本地分支，这样，Git就会把该分支推送到远程库对应的远程分支上：

```
$ git push origin master
```

如果要推送其他分支，比如`dev`，就改成：

```
$ git push origin dev
```



现在，你的小伙伴要在`dev`分支上开发，就必须把远程`origin`的`dev`分支映射到本地，于是他用这个命令创建本地`dev`分支

在本地创建和远程分支对应的分支，使用`git checkout -b branch-name origin/branch-name`，本地和远程分支的名称最好一致；

```
$ git checkout -b dev origin/dev
```

当我`git pull`失败时候，原因是没有指定本地`dev`分支与远程`origin/dev`分支的链接，根据提示，设置`dev`和`origin/dev`的链接：

建立本地分支和远程分支的关联，使用`git branch --set-upstream-to=origin/<branch> <branch>`；

```
$ git branch --set-upstream-to=origin/dev dev
Branch 'dev' set up to track remote branch 'dev' from 'origin'.
```

**处理多人协作的冲突**

1 当我git push的时候，git可以检测到冲突。

```
$ git push origin dev
To github.com:michaelliao/learngit.git
 ! [rejected]        dev -> dev (non-fast-forward)
error: failed to push some refs to 'git@github.com:michaelliao/learngit.git'
hint: Updates were rejected because the tip of your current branch is behind
hint: its remote counterpart. Integrate the remote changes (e.g.
hint: 'git pull ...') before pushing again.
hint: See the 'Note about fast-forwards' in 'git push --help' for details.
```

2 我在git pull命令 拉下来看看冲突的文件。

```
$ git pull
Auto-merging env.txt
CONFLICT (add/add): Merge conflict in env.txt
Automatic merge failed; fix conflicts and then commit the result.
```

3 `git status`也可以告诉我们冲突的文件

4 解决冲突

```
$ vim readme.txt
$ git add readme.txt 
$ git commit -m "conflict fixed"
```

```
$ git push origin dev
Counting objects: 6, done.
Delta compression using up to 4 threads.
Compressing objects: 100% (4/4), done.
Writing objects: 100% (6/6), 621 bytes | 621.00 KiB/s, done.
Total 6 (delta 0), reused 0 (delta 0)
To github.com:michaelliao/learngit.git
   7a5e5dd..57c53ab  dev -> dev
```

# 8 标签管理（无需记住长串commit号）

git tag -a v1.0 创建一个带注解的标签

git tag -a v0.9 85fc7e7 给某一个提交发布追加标签（通过git log --oneline --decorate --graph，查看提交历史）

```
git tag -a <tagname> -m "runoob.com标签"
```

git tag -d v1.1 删除标签

git show <tagname>  查看标签信息

```
$ git show v0.9
commit f52c63349bc3c1593499807e5c8e972b82c8f286 (tag: v0.9)
Author: Michael Liao <askxuefeng@gmail.com>
Date:   Fri May 18 21:56:54 2018 +0800

    add merge

diff --git a/readme.txt b/readme.txt
```

git tag 查看标签

```
$ git tag
v0.9
v1.0
```

如果要推送某个标签到远程，使用命令`git push origin <tagname>`：

```
$ git push origin v1.0
Total 0 (delta 0), reused 0 (delta 0)
To github.com:michaelliao/learngit.git
 * [new tag]         v1.0 -> v1.0
```

一次性推送全部尚未推送到远程的本地标签：

```
$ git push origin --tags
Total 0 (delta 0), reused 0 (delta 0)
To github.com:michaelliao/learngit.git
 * [new tag]         v0.9 -> v0.9
```

如果标签已经推送到远程，要删除远程标签就麻烦一点，先从本地删除：

```
$ git tag -d v0.9
Deleted tag 'v0.9' (was f52c633)
```

然后，从远程删除。删除命令也是push，但是格式如下：

```
$ git push origin :refs/tags/v0.9
To github.com:michaelliao/learngit.git
 - [deleted]         v0.9
```

# 9 .gitignore文件

在Git工作区的根目录下创建一个特殊的`.gitignore`文件，然后把要忽略的文件名填进去，Git就会自动忽略这些文件。



配置例子（文件或目录）

```
# Windows:
Thumbs.db
ehthumbs.db
Desktop.ini

# Python:
*.py[cod]
*.so
*.egg
*.egg-info
dist
build

# My configurations:
db.ini
deploy_key_rsa
```



强制添加文件：

```
$ git add -f App.class
```

`git check-ignore`命令检查：

```
$ git check-ignore -v App.class
.gitignore:3:*.class    App.class
```

# 10 回到过去，跳回未来，建立里程碑

回到过去（当前的代码不见，需要从版本库中恢复）

1 git log 查看之前的commit，查看之前的提交id

2 git reset --hard  commitid，回滚。

跳到未来

1 回到过去后，通过git reflog 可以查看到回到过去时候的提交的commit id （最上面的是最新的？）

$ git reflog
e475afc HEAD@{1}: reset: moving to HEAD^
1094adb (HEAD -> master) HEAD@{2}: commit: append GPL
e475afc HEAD@{3}: commit: add distributed
eaadf4e HEAD@{4}: commit (initial): wrote a readme file

2 通过git reset --hard  commitid，跳转到未来

建立里程碑
在github上项目页有个release按钮，点击它，输入版本号，描述，然后点击 publish release

# 11 自定义服务器

centos 服务器配置：

## 1 安装git（centos）

yum info git  //查看 yum 源仓库的 Git 信息

//依赖库安装
yum install curl-devel expat-devel gettext-devel openssl-devel zlib-devel perl-devel

yum install gcc perl-ExtUtils-MakeMaker

 //卸载低版本的 Git

yum remove git

//下载新版的 Git 源码包（我放的了  /usr/local/git 的目录下了，git是我自己mkdir的目录）

cd git

wget https://github.com/git/git/archive/v2.9.2.tar.gz

tar -xzvf v2.9.2.tar.gz

make prefix=/usr/local/git all

make prefix=/usr/local/git install

//添加到环境变量

vim /etc/profile

//添加这一条

export PATH="/usr/local/git/bin:$PATH"

source /etc/profile   //使配置立即生效

git --version  //查看版本号

将git设置为默认路径，不然后面克隆时会报错

ln -s /usr/local/git/bin/git-upload-pack /usr/bin/git-upload-pack

ln -s /usr/local/git/bin/git-receive-pack /usr/bin/git-receive-pack

创建用户名和组

groupadd git

useradd git -g git

passwd git  #参数是用户名

su - git  //切换git用户,最好切换到git用户 不然后面新建的git仓库都要改权限

禁止Shell登录

vim etc/passwd

将git:x:502:502::/home/git:/bin/bash
改为 git:x:502:502::/home/git:/usr/local/git/bin/git-shell



## 2 密钥对配置

Git服务器打开RSA认证 。在Git服务器上首先需要将/etc/ssh/sshd_config中的RSA认证打开，即将sshd_config文件中下面几个的注释解开：

1.RSAAuthentication yes
2.PubkeyAuthentication yes
3.AuthorizedKeysFile .ssh/authorized_keys

/home/git下创建.ssh目录，然后创建authorized_keys文件
~~~
cd /home/git/

mkdir .ssh #新建文件夹

chmod 755 .ssh

touch .ssh/authorized_keys  #新建文件

chmod 644 .ssh/authorized_keys
~~~

ssh-keygen -t rsa -C "472164571@qq.com" 连着按几次回车

windows下在C:\Users\bob\\.ssh\id_rsa,id_rsa.pub

linux（root用户）下在/root/.ssh/id_rsa,id_rsa.pub

将生成的公钥文件拷贝进/home/git/.ssh/authorized_keys

公钥文件（id_rsa.pub）示例

ssh-rsa AAAAdsdsdsadasdasdadasdasdasdasdasdasdadasdasdsadsadsadasdasdsa 472164571@qq.com

## 3 初始化配置

假定是/home/gitrepo/runoob.git，在/home/gitrepo目录下(可以自定义至其他目录)输入命令：

```
cd /home
$ mkdir gitrepo
$ chown git:git gitrepo/
$ cd gitrepo

$ git init --bare runoob.git
Initialized empty Git repository in /home/gitrepo/runoob.git/
Git就会创建一个裸仓库，裸仓库没有工作区，因为服务器上的Git仓库纯粹是为了共享，所以不让用户直接登录到服务器上去改工作区，并且服务器上的Git仓库通常都以.git结尾。
$ chown -R git:git runoob.git
```

## 4 git clone

``` 
$ git clone git@192.168.45.4:/home/gitrepo/runoob.git
Cloning into 'runoob'...
warning: You appear to have cloned an empty repository.
Checking connectivity... done.
```

## 5 生产环境自动部署（利用git的钩子来写脚本）

远程服务器仓库设置:

vim 仓库路径/hooks/post-update:



unset GIT_DIR
WEB=/data/wwwroot/sample/
cd $WEB
git init
git remote add origin /cangku/sample2.git
git clean -df
git pull origin master

远程服务器web 仓库设置

mkdir /data/wwwroot/sample/

chown -R git:git sample