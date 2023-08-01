[toc]



## 1 普通函数就看谁调用他，如果没人，就是window，如果有人，就是那个调用者本身。

```
   var obj = {
            fn1: function() {
                console.log(this)
            }
        }
        obj.fn1();//输出的结果等同于console.log(obj)
        var used = obj.fn1;
        used();//输出window
```



如果函数作为对象去使用，那么this将变成调用者本身

```
function Ren () {
	this.age =18;
	this.name = 'www'
	console.log(this)
}
var ren = new Ren()//输出ren
```



## 2 箭头函数没有this，箭头函数的this是继承父执行上下文里面的this

1 箭头函数只能创建匿名函数

```
var aaa = () => {
	console.log(this)
}
```

```
var obj = {
            fn1: function() {
                console.log(this)
                var test = () => {
                    console.log(this)
                }
                test()
            }
        }
        obj.fn1();//输出两个,结果等同于console.log(obj)
        var used = obj.fn1;
        used();//输出两个window
```

```
var obj = {
            fn1: () => {
                console.log(this)
                var test = () => {
                    console.log(this)
                }
                test()
            }
        }
        obj.fn1();//输出两个window的对象
        var used = obj.fn1;
        used();//输出两个window
```

2 加深理解：（**注意：简单对象（非函数）是没有执行上下文的**！）

```
let num = 11;
const obj1 = {
	num: 22,
	fn1: function() {
		let num = 33;
		const obj2 = {
			num: 44,
			fn2: () => {
				console.log(this.num);
			}
		}
		obj2.fn2();
	}
}
obj1.fn1(); // 22
————————————————
fn2中得到的结果为：22

原因箭头函数没有this，箭头函数的this是继承父执行上下文里面的this ，这里箭头函数的执行上下文是函数fn1()，所以它就继承了fn1()的this，obj1调用的fn1，所以fn1的this指向obj1， 所以obj1.num 为 22。
```

3 如果fn1也是箭头函数的情况

```
let num = 11; // 改为var 才会得到11的结果
const obj1 = {
	num: 22,
	fn1: () => {
		let num = 33;
		const obj2 = {
			num: 44,
			fn2: () => {
				console.log(this.num);
			}
		}
		obj2.fn2();
	}
}
obj1.fn1();
————————————————
上述结果为undefined，因为fn1也是一个箭头函数，所以它就只能继续向上找也就是window了；
换句话说，this.num==window.num,因为使用let，所以不会有window.num=11，除非用第一行改用var num = 11。

那为什么是undefined而不是11呢？

这里涉及到var和let声明变量的一个区别：使用 let 在全局作用域中声明的变量不会成为 window 对象的属性，var 声明的变量则会(不过，let 声明仍然是在全局作用域中发生的，相应变量会在页面的生命周期内存续，所以使用window访问会为undefined)：
```

