[toc]

## swoole协程的发展

1 swoole1.x的时候，提供redis异步回调方法，但容易陷入回调陷阱。

```
$redisClient = new swoole_redis;
$redisClient->connect('127.0.0.1',6379,function(swoole_redis $redisClient,$result)) {
	$redisClient->set('asa', time(),function(swoole_redis $redisClient,$result)	{
		var_dump($result);
	})
}
```

2 swoole2.X时代，提供协程客户端，可以以同步的方法去写，但是需要作用在http服务器的onrequest等

```
$http = new swoole_http_server('0.0.0.0',8001);
$http->on('request',function($request,$response) {
	$redis = new Swoole\Coroutine\Redis();
	$redis->connect('127.0.0.1',6379);
	$value = $redis->get($request->get['a']);
	$response->end($value);
	//如果这里写mysql的协程客户端，这个请求的io时间将会是（max（mysql+redis））的时间
})
$http->start();
```

3 swoole4.X时代,提供了go关键字（可直接运行php Coroutine.php; ）,co::sleep(1)是在协程内调用停止1秒，他最终输出的是"主进程ba"；如注释他打开下面的原生的sleep，他将会在睡眠1秒后，输出"a主进程b".

```
go(	function() {
    co::sleep(1);//是在这个协程内睡眠1秒
	//sleep(1);//执行全局停止
	echo 'a';
});
echo '主进程';
go(	function() {
	echo 'b';
});
```

