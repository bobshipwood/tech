[toc]

# 页面层级最大为10层

使用 wx.navigateTo({ url: 'pageD' }) 可以往当前页面栈多推入一个 pageD，此时页面栈变成 [ pageA, pageB, pageC, pageD ]。
使用 wx.navigateBack() 可以退出当前页面栈的最顶上页面，此时页面栈变成 [ pageA, pageB, pageC ]。
使用wx.redirectTo({ url: 'pageE' }) 是替换当前页变成pageE，此时页面栈变成 [ pageA, pageB, pageE ]，当页面栈到达10层没法再新增的时候，往往就是使用redirectTo这个API进行页面跳转。

| 方法                            | 当前页面栈                                                   | 方法之后页面栈                 | 限制                 | 补充                                                         |
| ------------------------------- | ------------------------------------------------------------ | ------------------------------ | -------------------- | ------------------------------------------------------------ |
| wx.navigateTo({ url: 'pageD' }) | [ pageA, pageB, pageC ]                                      | [ pageA, pageB, pageC, pageD ] | 只能打开非TabBar页面 | 此时当前页调用页面的onHide参数，页面没有被微信客户端回收（没有调用onUnload） |
| wx.navigateBack()               | [ pageA, pageB, pageC, pageD ]                               | [ pageA, pageB, pageC ]        |                      | 此时当前页被微信客户端回收，调用页面的onUnload               |
| wx.redirectTo({ url: 'pageE' }) | [ pageA, pageB, pageC ]                                      | [ pageA, pageB, pageE ]        | 只能打开非TabBar页面 | 此时当前页被微信客户端回收，调用页面的onUnload               |
| wx.switchTab({ url: 'pageF' })  | 此时原来的页面栈会被清空（除了已经声明为Tabbar页pageA外其他页面会被销毁） | [ pageF ]                      | 只能打开Tabbar页面   | 此时点击Tab1切回到pageA时，pageA不会再触发onLoad，因为pageA没有被销毁。 |
| wx. reLaunch({ url: 'pageH' })  | 重启小程序                                                   | [ pageH ]                      |                      | pageH调用onUnload，页面被销毁                                |

# 页面tabbar

小程序提供了原生的Tabbar支持，我们可以在app.json声明tabBar字段来定义Tabbar页

**注意Tabbar页面初始化之后不会被销毁**

```json
{
  "tabBar": {
    "list": [
      { "text": "Tab1", "pagePath": "pageA" },
      { "text": "Tab1", "pagePath": "pageF" },
      { "text": "Tab1", "pagePath": "pageG" }
    ]
  }
}
```

# tabbar页面触发方式及生命周期对应关系

Tab 切换对应的生命周期（以 A、B 页面为 Tabbar 页面，C 是从 A 页面打开的页面，D 页面是从 C 页面打开的页面为例）

| 当前页面        | 触发后的页面  | 触发的生命周期（按顺序）                |
| --------------- | ------------- | --------------------------------------- |
| A               | A             | 无                                      |
| A               | B             | A.onHide，B.onLoad，B.onshow            |
| A               | B（再次打开） | A.onHide,B.onShow                       |
| C               | A             | c.onUnload,A.onShow                     |
| C               | B             | c.onUnload,B.onload,B.onshow            |
| D               | B             | d.onunload,c.onunload,B.onload,B.onshow |
| D(从转发进入)   | A             | d.onunload,a.onload,a.onshow            |
| D（从转发进入） | B             | d.onunload,b.onload,b.onshow            |

