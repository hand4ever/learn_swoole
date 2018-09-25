# 安装步骤

## 环境

* MacOS 10.13.6
* Apple LLVM version 10.0.0(clang-1000.11.45.2)
* PHP 7.1.21


## 下载源码

> `git clone https://gitee.com/swoole/swoole.git`
>
> 我本机代码下载到 `/Users/panlong/learn/swoole`

## 编译
> https://wiki.swoole.com/wiki/page/437.html

**此处需要 sudo 权限**

1. cd swoole
2. phpize
3. ./configure --enable-sockets --enable-mysqlnd --enable-openssl --enable-swoole-debug
4. make 
5. make install 

## 安装扩展
* 上步编译完成后，会生成 swoole.so 文件，需要在 php.ini 中引入该扩展
* 下面是我的 mac 上的步骤
    1. 本机编译后的 swoole.so 文件在目录 `/usr/local/Cellar/php@7.1/7.1.21/pecl/20160303/`
    2. 执行 php -i|grep ini，得出 ini 目录所在地 `/usr/local/etc/php/7.1/conf.d`，cd 进去
    3. touch ext-swoole.ini
    4. 输入以下文本
    
    ```
    [swoole]
    extension="/usr/local/Cellar/php@7.1/7.1.21/pecl/20160303/swoole.so"
    ```
* 测试是否安装成功 `php --ri swoole`



（以上）