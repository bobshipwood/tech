[TOC]

# 1 安装

#### 1 composer.phar   linux/win通用  2进制归档文件



#### 2 linux/mac 全局安装

sudo mv composer.phar /usr/local/bin/composer

#### 3 win全局安装

a 将composer.phar 拷贝到php.exe同级目录
b 新建composer.bat,并拷贝代码至文件中
@php "%~dp0composer.phar" %*



# 2 简易流程说明

#### 1在当前目录初始化一个composer.json，按照提示一步步填写即可，会在当前目录生成文件
composer init

#### 2 根据当前composer.json 生成依赖关系，默认安装到vendor
composer install

#### 3 在当前项目引入特定的依赖包(xxx/xxx)
composer require xxx/xxx 

composer require symfony/httpfoundation

#### 4 从dist安装，用特定的包(xxx/xxx)创建项目（(直接下载框架项目，其中dist为composer的缓存)）
composer create-project --prefer-dist xxx/xxx 你的项目目录

composer create-project --prefer-dist topthink/think think_composer

#### 5 更新某个依赖包，并更新composer.lock
composer update xxx/xxx

#### 6 该操作会升级你项目所有可能的依赖包，并更新composer.lock **小心操作**
composer update

####  7 更新composer版本
composer self-update

#### 8 优化一下自动加载
composer dump-autoload --optimize



# 3 优化

#### 1 全国镜像

全局配置中国镜像，命令行输入
composer config -g repo.packagist composer https://packagist.phpcomposer.com



# 4 其他操作

#### 1 composer search  XXX（monolog） (寻找某个包)

#### 2 composer show --all monolog/monolog (查看这个包的信息)



# 5 疑惑点

#### 1 删除包引申的疑惑

在json里面删除依赖，直接执行composer install，他提示要执行 composer update，执行composer update 后，这个包才被删除。我想install与update的区别是，install是以composer.lock 为基础，保证这个包下载回来的版本一致性。updata 则是更新composer.lock的操作？

#### 2 composer selfupdate  所产生的疑惑

会不会更新完之后，项目里的composer.lock也会跟着更新，导致全部依赖包都更新？

#### 3 composer只是引入类，不能引入全局数组？

我在自己的框架中，"files": ["functions/bob.func.php"]通过这样的方式，发现只能写一下helpes函数。在这里定义的全局数组，发觉不生效

#### 4 为啥要开启优化？开启优化的结果是啥？

开启优化，这个命令的本质是将 PSR-4/PSR-0 的规则转化为了 classmap 的规则，因为 classmap 中包含了所有类名与类文件路径的对应关系，所以加载器不再需要到文件系统中查找文件了。可以从 classmap 中直接找到类文件的路径。

否则在每次在json写好psr-4自动加载后，都要composer dump-autoload一次，以便生成一次json文件？



# 6 与自己框架的结合

#### １先在自己的类目录里定义好命名空间
namespace easys\web;

#### 2 写入json文件

然后往composer.json
写入
"autoload": {
		"psr-4": {
			"easys\\web\\": "src/"
		}
	}

#### 3 最后在主体文件中做导入类操作
导入类的情况

use easys\web\response;

echo response::json(20002,'sadsad');

不导入类的情况

echo easys\web\pdoobj::one();