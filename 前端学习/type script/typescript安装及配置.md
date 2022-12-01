

[toc]

## 1 安装

```
npm install typescript -g
tsc -v
```

## 2  配置

### 1 vscode打开目录，以终端打开(或直接在cmd下打开并输入)

````
tsc --init
````

### 2 编辑目录下的tsconfig.json

```
complileroption
rootdirs:[./src]
outdir:./dist
```

## 3 编译

```
tsc
```

## 4 运行

```
node ./dist/XXX
```

