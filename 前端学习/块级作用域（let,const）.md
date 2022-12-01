1 let ，const是块级作用域

```
{
            let a = 1;
            var b =2;
            const c = 3;
}
//在块外无法访问let，const定义的变量（如console.log(a)）； 
题外话，变量提升最多只能是提升到script标签内；
b因为有变量提升，提升到块外，相当于var b；{b=2};另外，这里{var b =2 }还是操作着全局变量
最后，函数能封住var的变量提升，封闭在函数内的变量应该叫函数局部变量，在函数运行完毕时候，应该会自动销毁
```

let，const（在同一级别作用域下？），无法重复申明
```
{
            let a = 1;
            const c = 3;
             let a = 3；
             const c = 1；
}
//最后2行会报错误
```

2 es6规定，在块级作用域内，如果有用let或者const申明的变量，那么此代码块会形成一个封闭的作用域，在他们两个申明前的变量，去引用同名变量，都会出错。（也既不可能像var一样会提升变量）

```
var a = 1;
 if (true) {
 			//直接会报错Cannot access 'a' before initialization，并不会引用全局变量a
            console.log(a);
            
            const a = 3;
            console.log(a);
 }
```

3 let const就算放在全局作用域下，也不会绑定window对象，而var会



4 const是常量的概念，不能只声明而不赋值。赋值后的语句就锁死了这个变量

```
const b;//报错，Missing initializer in const declaration

```

 ```
const a = 1;
a = 2;//报错。Assignment to constant variable.
 ```

```
//以下的错误，无论用const或者let都会报同样的错误,不能对同一个变量声明2次
const a = 1;
const a = 2;//报错。Identifier 'a' has already been declared
let a = 2;//报错。Identifier 'a' has already been declared
```

5 最佳实践，直接用let代替var，需要保护的变量用const