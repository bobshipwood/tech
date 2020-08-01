[TOC]

# 1 安装

#### 1 composer.phar   linux/win通用  2进制归档文件

```bash
php -r "copy('https://install.phpcomposer.com/installer', 'composer-setup.php');"
```

```bash
可以指定安装目录，不指定默认在当前目录  php composer-setup.php --install-dir=bin
```

```bash
删除脚本用，可以不删   php -r "unlink('composer-setup.php');"
```

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

composer install --no-plugins --no-scripts(composer因为不能用root而推荐的做法)

#### 3 在当前项目引入特定的依赖包(xxx/xxx)
composer require xxx/xxx 

composer require symfony/httpfoundation

composer update --no-plugins --no-scripts(composer因为不能用root而推荐的做法)

#### 4 从dist安装，用特定的包(xxx/xxx)创建项目（(直接下载框架项目，其中dist为composer的缓存)）
composer create-project --prefer-dist xxx/xxx 你的项目目录

composer create-project --prefer-dist topthink/think think_composer

#### 5 更新某个依赖包，并更新composer.lock
composer update xxx/xxx

#### 6 该操作会升级你项目所有可能的依赖包，并更新composer.lock **小心操作**
composer update

####  7 更新composer版本
composer self-update

# 3 优化

#### 1 全国镜像

全局配置中国镜像，命令行输入
composer config -g repo.packagist composer https://packagist.phpcomposer.com

#### 2 优化的几个级别

##### 1 composer dump-autoload -o（Level-1）

这个命令的本质是将 PSR-4/PSR-0 的规则转化为了 classmap 的规则， 因为 classmap 中包含了所有类名与类文件路径的对应关系，所以加载器不再需要到文件系统中查找文件了。可以从 classmap 中直接找到类文件的路径。

这个命令并没有考虑到当在 classmap 中找不到目标类时的情况，当加载器找不到目标类时，仍旧会根据PSR-4/PSR-0 的规则去文件系统中查找

##### 2 composer dump-autoload -a（Level-2/A）

执行这个命令隐含的也执行了 Level-1 的命令， 即同样也是生成了 classmap，区别在于当加载器在 classmap 中找不到目标类时，不会再去文件系统中查找（即隐含的认为 classmap 中就是所有合法的类，不会有其他的类了，除非法调用）

如果你的项目在运行时会生成类，使用这个优化策略会找不到这些新生成的类。

##### 3 composer dump-autoload --apcu  （Level-2/B）

使用这个策略需要安装 apcu 扩展。

这种策略是为了在 Level-1 中 classmap 中找不到目标类时，将在文件系统中找到的结果存储到共享内存中， 当下次再查找时就可以从内存中直接返回，不用再去文件系统中再次查找。

在生产环境下，**这个策略一般也会与 Level-1 一起使用，** 执行`composer dump-autoload -o --apcu`, 这样，即使生产环境下生成了新的类，只需要文件系统中查找一次即可被缓存 ， **弥补了Level-2/A 的缺陷**。

# 4 其他操作

#### 1 composer search  XXX（monolog） (寻找某个包)

#### 2 composer show --all monolog/monolog (查看这个包的信息)



# 5 疑惑点

#### 1 composer只是引入类，不能引入全局数组？

我在自己的框架中，"files": ["functions/bob.func.php"]通过这样的方式，发现只能写一下helpes函数。在这里定义的全局数组，发觉不生效

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

#### 4 如果需要刷新，运行composer install即可