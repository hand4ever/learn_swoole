<?php
/**
 * Created by PhpStorm.
 * User: panlong
 * Date: 2018/9/20
 * Time: 20:48
 */
namespace myswoole\socket;

abstract class Server
{
    /**
     * 初始化 server 设置
     * @return mixed
     */
    abstract public function init($ip, $port, array $setting);
    /**
     * 启动 server，init 过后，执行此方法启动 server
     * @return mixed
     */
    abstract public function run();

    /**
     * 绑定 start
     * @return mixed
     */
    abstract public function onStart();

    /**
     * 绑定 connect
     * @return mixed
     */
    abstract public function onConnect();

    /**
     * 绑定 receive
     * @return mixed
     */
    abstract public function onReceive();

    /**
     * 绑定 close
     * @return mixed
     */
    abstract public function onClose();
}