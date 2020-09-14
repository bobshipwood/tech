[toc]

## psr 1

### 1 函数名 小写驼峰

```
function fnTest()
{
    //函数主体
}
```

### 2 命名空间 大写驼峰

```
namespace Vendor\Model; 
```

### 3 类名----大写驼峰

```
class Test
```

### 4 常量 全部大写，分割符号用_

```
const PI=3.14； 
const BATE_VERSION = '2.1.3';
```

### 5 类中的属性--选择下划线

```
protected $web_name = '西西欢迎你们';
```



## psr 2

### 1 PHP文件必须要以一个空白行作为结束；

### 2 纯PHP代码文件必须省略最后的?>结束标签。

### 3 PHP所有的关键字必须全部小写（包括了true、false和null）；

### 4 namespace use规范

```
<?php
namespace Psr\Model;

use Controller;
use AbcAccess as Abc;

//下面开始代码
```

### 5 关键字extends和implements必须写在类名称的同一行；

```
//继承和实现
class MyPerson extend Person implements Action
{
//类主体
}
```

### 6 类中方法的参数(有默认值的放在最后)

```
//标准参数
public function run($key, $value, $arr = [])
{
//方法主体
}
```

### 7 需要添加abstract和final声明时，必须写在访问修饰符前面，而static则必须写在其后

```
namespace Psr\Model;

abstract class Computer
{
    //在修饰符后面
    protected static $mode;
    
    //抽象写在在修饰符前面
    abstract public function run();
    
    //在修饰符后面
    final public static function bar()
    {
        //方法主体
    }
}
```

### 8 方法调用

```
//标准调用
$p->run($key, $value);
```

### 9 if else elseif

```
<?php

if ($flag) {
    //结构体内部
} elseif ($flag2) {
    //elseif
} else {
    //else
}
```

### 10 switch case

```
<?php
switch ($flag) {
    case 0:
        echo '开始阶段';
        break;
    case 1:
        echo '常规运行';
        //不需要break
    case 2:
    case 3:
    case 4:
        echo '结束阶段';
        break;
    default:
        echo '发生意外';
        break;
}
```

### 11 for foreach trycatch

```
<?php
//for循环
for ($i = 0; $i < 10; $i++) {
    ///for结构体
}

//foreach遍历
foreach ($array as $key => $value) {
    //foreach结构体
}

//try catch
try {
    //try
} catch (Exception $e) {
    //catch
}
```

### 12 闭包

```
<?php
//闭包
$myFn = function ($arg1, $arg2) {
    //匿名函数代码
};

//闭包
$myFn = function ($arg1, $arg2) use ($var1, $var2) {
    //匿名函数代码
};
```