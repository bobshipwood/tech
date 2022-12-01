[toc]

# 0 swoole所有的协程必须在协程容器里面创建，也就是协程代码包含在run函数里头

# 1 swoole 启动程序有以下3种：

## 1.1 调用异步风格服务端程序的 start方法

此种启动方式会在事件回调中创建协程容器。就是回调中不需要写run函数,但要写go函数。

## 1.2 调用 Swoole 提供的 2 个进程管理模块 Process 模块和 Process\Pool 的 start 方法

此种启动方式同样会在进程启动的时候创建协程容器

##　1.3　其他直接裸写协程的方式启动程序

需要先创建一个协程容器 (`Coroutine\run()` 函数

# 2 协程客户端与协程函数hook

## 2.1 swoole推荐开启一键协程化，把所有的函数和客服端都转化为异步

Swoole\Runtime::enableCoroutine($flags = SWOOLE_HOOK_ALL);v4.3以后的用法用，在server的onStart里面执行；

但是swoole更推荐Co::set(['hook_flags'=> SWOOLE_HOOK_ALL])，在server的onstart之前就已经申明一键协程化

## 2.2 swoole的协程客户端一般有redis，mysql，但一般推荐开启一键携程化，使得不再需要用到协程客户端，自动转化为异步客户端。

```
//开启一键协程后，mysql，redis就可以用原生的语法去连，但要包在go里头。如果注释掉一键携程，那里面的代码将是原生循序执行的，如果保持开启，那协程里运行的是协程的客户端，当遇到阻塞时，让出控制权。

swoole\Runtime::enableCoroutine(true);
go (function {
	$redis =new Redis;
	$redis-connect('127.0.0.1', 6379);
	echo "我是协程不需要等待";
	//还有类似的mysql客户端等等
})
echo "我是主进程";
go (function {
	echo ‘我是另一个协程’；
})
```
## 2.3 协程http客户端实际代替的是原生的curl去请求接口数据（swoole保留）

可以放在onrequest事件回调内，无需写go包含，如$client = new Swoole/Coroutine/Http/Client(127.0.0.1)；

如果在一个进程事件内，同时有两个请求，他们就要用go隔开。

## 2.4 协程函数有SLEEP，FILE，CURl等

## 2.5 子协程(go)+通道(channel)实现并发请求

 ```
//并发调用例子
1 在 onRequest 中需要并发两个 HTTP 请求，可使用 go 函数创建 2 个子协程，并发地请求多个 URL
并创建了一个 chan，使用 use 闭包引用语法，传递给子协程
2 主协程循环调用 chan->pop，等待子协程完成任务，yield 进入挂起状态
3 并发的两个子协程其中某个完成请求时，调用 chan->push 将数据推送给主协程
4 子协程完成 URL 请求后退出，主协程从挂起状态中恢复，继续向下执行调用 $resp->end 发送响应结果

$serv = new Swoole\Http\Server("127.0.0.1", 9503, SWOOLE_BASE);

$serv->on('request', function ($req, $resp) {
    $chan = new chan(2);
    go(function () use ($chan) {
        $cli = new Swoole\Coroutine\Http\Client('www.qq.com', 80);
            $cli->set(['timeout' => 10]);
            $cli->setHeaders([
            'Host' => "www.qq.com",
            "User-Agent" => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $ret = $cli->get('/');
        $chan->push(['www.qq.com' => $cli->body]);
    });

    go(function () use ($chan) {
        $cli = new Swoole\Coroutine\Http\Client('www.163.com', 80);
        $cli->set(['timeout' => 10]);
        $cli->setHeaders([
            'Host' => "www.163.com",
            "User-Agent" => 'Chrome/49.0.2587.3',
            'Accept' => 'text/html,application/xhtml+xml,application/xml',
            'Accept-Encoding' => 'gzip',
        ]);
        $ret = $cli->get('/');
        $chan->push(['www.163.com' => $cli->body]);
    });

    $result = [];
    for ($i = 0; $i < 2; $i++)
    {
        $result += $chan->pop();
    }
    $resp->end(json_encode($result));
});
$serv->start();
 ```

