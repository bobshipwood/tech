<?php

// require 'function'.DIRECTORY_SEPARATOR.'setup.func.php';
 require 'function'.DIRECTORY_SEPARATOR.'user.func.php';
// require 'function'.DIRECTORY_SEPARATOR.'wechat.func.php';
// require 'function'.DIRECTORY_SEPARATOR.'sql.func.php';


//初始化
$curl = curl_init();
curl_setopt($curl,CURLOPT_URL,"http://www.baidu.com");

//设置请求头
$header[] ="api-key: ksdddsdssdsdsdey";
curl_setopt($curl,CURLOPT_HTTPHEADER,$header);

//设置请求的useragent
$user_agent = "sdsadsddsdssssssssssss";
curl_setopt($curl, CURLOPT_USERAGENT,$user_agent);


//  该选项非常重要,返回 response_header,如果不为 true, 只会获得html
curl_setopt($curl, CURLOPT_HEADER, true);
// 是否不需要响应的正文,影响output,为了节省带宽及时间,在只需要响应头的情况下可以不要正文,就是output中没有baidu.html
curl_setopt($curl, CURLOPT_NOBODY, false);

//返回直接输出
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);

//至关重要，CURLINFO_HEADER_OUT选项可以拿到自定义请求头信息
curl_setopt($curl, CURLINFO_HEADER_OUT, true);


//响应信息在此
$output = curl_exec($curl);
// // 获得响应结果里的：头大小
$headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
// // 根据头大小去获取头信息内容
$head = substr($output, 0, $headerSize);


//通过curl_getinfo()可以得到请求头的信息(带自定义信息)
$qingqiu = curl_getinfo($curl);
curl_close($curl);

p($qingqiu);//获取请求的信息

?>