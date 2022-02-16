

[toc]

# 1 Nginx的总体匹配规则

是先 匹 配 普 通 location ，再 匹 配 正 则 表 达 式.

# 2 对于匹配普通 location

## 1 匹配 URI 的前缀部分

## 2 遵循最大匹配原则

因为 location 不是 “严格匹配”，而是 “前缀匹配”，就会产生一个 HTTP 请求，可以 “前缀匹配” 到多个普通 location，例如：

请求：/prefix/mid/t.html；

配置:

```
location /prefix/mid/ {} 
location /prefix/ {}
```

前缀匹配的话两个 location 都满足，选哪个？根据最大匹配原则 ，于是选的是 location /prefix/mid/ {} 

# 3 对于正则表达式的匹配(引出 ^~和=可以禁止正则匹配)

通常的规则是匹配完了 “普通 location” 指令，还需要继续匹配 “正则 location”。

不过可以告诉 nginx 匹配到了 “普通 location” 后，不再需要继续匹配 “正则 ” 了。

要做到这一点只要在 “普通 location” 前面加上 “^~ ” 符号（ ^ 表示 “非”，~ 表示 “正则”，意思是：不要继续匹配正则 ）。

除了 “^~ ” 可以阻止继续搜索正则 location 外，还可以加 “=”。 

# 4  ^~ 与 = 的区别

## 1 共同点是它们都能阻止继续搜索正则 location

## 2 不同点是 “^~ ” 依然遵守 “最大前缀” 匹配规则，然而 “=” 不是 “最大前缀”，而是严格匹配 ( exact match )

例如，***location / {}*** 和 ***location = / {}*** 的区别：

location / {} 遵守普通 location 的最大前缀匹配。
由于任何 URI 都必然以“/ ”根开头，所以对于一个 URI，如果有更精确的匹配，那自然是选这个更精确的；如果没有，“/ ” 一定能为这个 URI 垫背（ 至少能匹配到“/ ”）。
也就是说 location / {} 有点默认配置的味道，其他更精确的配置能覆盖这个默认配置（ 这也是为什么我们总能看到 location / {} 这个配置的一个很重要的原因）。

location = / {}遵守的是 “严格精确匹配 exact match”。
也就是只能匹配对根目录的请求，同时会禁止继续搜索 正则 location。

# 5 另外一种隐含的禁止正则匹配的模式,当 “最大前缀” 匹配恰好就是一个“严格精确 ( exact match )”匹配，照样会停止后面的搜索

原文字面意思是：只要遇到 “精确匹配 exact match”，即使普通 location 没有带 “=” 或 “^~ ” 前缀，也一样会终止后面的匹配。

假设当前配置是：location /exact/match/test.html { 配置指令块1}，location /prefix/ { 配置指令块2} 和 location ~ .html$ { 配置指令块3}

- 如果我们请求 GET /prefix/index.html ，则会被匹配到指令块3 ，因为普通 location /prefix/ 依据最大匹配原则能匹配当前请求，但是会被后面的正则 location 覆盖；
- 当请求 GET /exact/match/test.html ，会匹配到指令块1 ，因为这个是普通 location 的 exact match ，会禁止继续搜索正则 location