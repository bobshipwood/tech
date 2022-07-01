[toc]

# 页面层级最大为10层

| 方法                            | 当前页面栈                                                   | 方法之后页面栈                 | 限制                 | 补充                                                         |
| ------------------------------- | ------------------------------------------------------------ | ------------------------------ | -------------------- | ------------------------------------------------------------ |
| wx.navigateTo({ url: 'pageD' }) | [ pageA, pageB, pageC ]                                      | [ pageA, pageB, pageC, pageD ] | 只能打开非TabBar页面 | 此时当前页调用页面的onHide参数，页面没有被微信客户端回收     |
| wx.navigateBack()               | [ pageA, pageB, pageC, pageD ]                               | [ pageA, pageB, pageC ]        |                      | 此时当前页被微信客户端回收，调用页面的onUnload               |
| wx.redirectTo({ url: 'pageE' }) | [ pageA, pageB, pageC ]                                      | [ pageA, pageB, pageE ]        | 只能打开非TabBar页面 | 此时当前页被微信客户端回收，调用页面的onUnload               |
| wx.switchTab({ url: 'pageF' })  | 此时原来的页面栈会被清空（除了已经声明为Tabbar页pageA外其他页面会被销毁） | [ pageF ]                      | 只能打开Tabbar页面   | 此时点击Tab1切回到pageA时，pageA不会再触发onLoad，因为pageA没有被销毁。 |
| wx. reLaunch({ url: 'pageH' })  | 重启小程序                                                   | [ pageH ]                      |                      |                                                              |

# 页面tabbar

小程序提供了原生的Tabbar支持，我们可以在app.json声明tabBar字段来定义Tabbar页

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

Tab 切换对应的生命周期（以 A、B 页面为 Tabbar 页面，C 是从 A 页面打开的页面，D 页面是从 C 页面打开的页面为例）如表3-6所示，注意Tabbar页面初始化之后不会被销毁。

| 当前页面        | 触发后的页面  | 触发的生命周期（按顺序）                |
| --------------- | ------------- | --------------------------------------- |
| A               | A             | 无                                      |
| A               | B             | A.onHide，B.onLoad，B.onshow            |
| A               | B（再次打开） | A.onHide,B.onShow                       |
| C               | A             | c.onUnload,A.onShow                     |
| C               | B             | c.onUnload,B.onload,B.onshow            |
| D               | B             | d.onunload,c.onunload,B.onload,B.onshow |
| D(从转发进入)   | A             | d.onunload,a.onshow,a.onshow            |
| D（从转发进入） | B             | d.onunload,b.onshow,b.onshow            |

