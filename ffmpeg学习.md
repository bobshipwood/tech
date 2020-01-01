

1 合成视频

这种方法成功率很高，也是最好的，但是需要 FFmpeg 1.1 以上版本。先创建一个文本文件`filelist.txt`：

> ```
> file 'input1.mkv'
> file 'input2.mkv'
> file 'input3.mkv'
> ```

然后：

> ```
> ffmpeg -f concat -safe 0 -i files.txt  -c copy output.mkv
> ```

注意：使用 FFmpeg concat 分离器时，如果文件名有奇怪的字符，要在 `filelist.txt` 中转义。



2 图片到视频

文件夹存放1-30张同样的图片

```
D:\app.easys.co\public>ffmpeg -f image2 -i D:\app.easys.co\public\aaa\%d.jpg -vcodec libx264 -r 1  test.mp4
```



3 视频转图片

```
ffmpeg -i toolba.mkv -r 1 -ss 00:00:26 -t 00:00:07 %03d.png
意思是给 ffmpeg 输入一个叫 toolba.mkv 的文件，让它以每秒一帧的速度，从第 26 秒开始一直截取 7 秒长的时间，截取到的每一幅图像，都用 3 位数字自动生成从小到大的文件名。
```

```
ffmpeg -i "*.mp4" -r 1 -q:v 2 -f image2 %d.jpeg
（注：上述代码中， 
-i 是用来获取输入的文件，-i “*.mp4” 就是获取这个叫做星号的mp4视频文件； 
-r 是设置每秒提取图片的帧数，-r 1的意思就是设置为每秒获取一帧； 
-q:v 2 这个据说是提高抽取到的图片的质量的，具体的也没懂； 
-f 据说是强迫采用格式fmt 
） 
```

