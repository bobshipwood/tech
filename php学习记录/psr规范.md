```
namespace  Vendor\Model;//namespace后有空行

use FooClass;
use BarClass as Bar;
use OtherVendor\OtherPackage\BazClass;// use 后面要有空行

class Classname extends Parenet implements 
	\ArrayAccess, 
	\Countable
{	//extends和implements关键字必须和类名在同一行,如果implements后面实现了很多接口导致一行		很长，可以依次将需要的类另起新行并缩进4个空格，
	const DATE_APPROVED  = '2012-06-11';
	
	public $foo = null;//一行只能声明一个属性,且null，true，false必须小写
	
	public function fooBarBaz($arg1, &$arg2, $arg3 = [])
    {
        //控制结构关键词后面的起始括号应该和控制结构关键词写在同一行
		do {
        	$gorilla->beatChest();
        } while ($ibis->isAsleep() === true);
    }
    //abstract、final必须在可见性修饰符之前，static声明必须放在可见性修饰符之后
    abstract protected function zim();

    final public static function bar()
    {
        // method body
    }
}


```

```
//在调用方法和函数时，圆括号必须跟在函数名之后，函数的参数之间有一个空格：
bar();
$foo->bar($arg1);
Foo::bar($arg2, $arg3);
```

```
//声明函数的方法
if (! function_exists('bar')) {
	function foo()
	{
		
 	}
}

$closureWithArgs = function ($arg1, $arg2) {
    // body
};

$closureWithArgsAndVars = function ($arg1, $arg2) use ($var1, $var2) {
    // body
};
```

