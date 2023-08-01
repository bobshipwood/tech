[toc]

# 1 路由文件

```
import SayHi2 from "@/views/Sayhi"


const routes = [
 
  /* 下面的name: 'SayHi' 可以省略, 在view中 这样调用即可
   * <router-link to="/say_hi"> SayHi </router-link>
   */
    {
      path: '/say_hi',//对应url，
      name: 'SayHi',//vue.js内部使用的名称
      component: SayHi2
    }, 


1 import 后面的SayHi2,就是路由定义里面的component的SayHi2，

2 @表示在本地文件系统中引入文件，@代表源码目录，一般是src，以前用的都是'../../views/Sayhi",推荐使用@。
```

# 2 路由跳转的2种方式

## 2.0 代码

```
<template>
  <div >
    <table>
      <tr v-for="blog in blogs">
        <td>
	 //2 使用v-link
          <router-link :to="{name: 'Blog', query: {id: blog.id}}">
            {{blog.id}}
          </router-link>
        </td>
	//1 使用事件方法
        <td @click='show_blog(blog.id)'>{{blog.title }}</td>
      </tr>
    </table>
  </div>
</template>

<script>

import MyLogo from '@/components/Logo'
const axios = require('axios');

export default {
  data: function() {
    return {
      title: '博客列表页',
      blogs: [
      ]
    }
  },
  methods: {
    show_blog: function(blog_id) {
      console.info("blog_id:" + blog_id)
      this.$router.push({name: 'Blog', query: {id: blog_id}})
    },
   
  },
```

## 2.1 使用方法事件(预先在router/index.js,定义好name属性，)

定义show_blog方法，然后使用this.$router.push来实现跳转，this.$router表示vue的内置对象，表示路由。跳转到name:Blog对应的vue页面，参数是blog_id

## 2.2 用v-link，（因为在html5 history模式中，router-link会拦截单击事件，让浏览器不再重新加载页面）

# 3 跳转到某个路由时，带上参数

## 3.1 对于普通的参数

```
routes:[
{
path:'/blog',
name: 'Blog'
},
]
在视图中，我们可以这样做
<router-link :to"{name: 'Blog', query: {id: 3}}" >USer</router-link>
在<script>中，可以这样做
this.$router.push({name:'blog', query:{id:3}})
```

都会跳转到/blog?id=3

## 3.2 对于路由中声明的参数

```
routes:[
{
path:'/blog/:id',
name: 'Blog'
},
]
在视图中，我们可以这样做
<router-link :to"{name: 'Blog', params: {id: 3}}" >USer</router-link>
在<script>中，可以这样做
this.$router.push({name:'blog', params:{id:3}})
```

都会跳转到/blog/3

# 4 获取路由参数的2中方式

## 4.1 对于/blogs?id=3

```
this.$route.query.id   //返回结果3
```

## 4.2 对于路由中定义好的参数

```
routes:[
{
path:'/blog/:id',     //注意助理的 ： id
name: 'Blog'
},

this.$route.params.id  //返回结果3
```

