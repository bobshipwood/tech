[TOC]

# 1 自定义服务器

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