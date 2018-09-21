<?php
/**
 * Created by PhpStorm.
 * User: panlong
 * Date: 2018/9/20
 * Time: 19:33
 */

namespace myswoole\socket;

class BaseServer extends Server
{
    /**
     * @var \Swoole\Server
     */
    protected $serv;

    /**
     * 初始化 server 设置
     * @param $ip
     * @param $port
     * @param array $setting
     */
    public function init($ip, $port, array $setting)
    {
        $this->serv = new \Swoole\Server($ip, $port);
        $this->serv->set($setting);
        $this->serv->on('Start', [$this, 'onStart']);
        $this->serv->on('Connect', [$this, 'onConnect']);
        $this->serv->on('Receive', [$this, 'onReceive']);
        $this->serv->on('Close', [$this, 'onClose']);
    }


    /**
     * 启动 server，init 过后，执行此方法启动 server
     * @return mixed
     */
    public function run()
    {
        $this->serv->start();
    }

    /**
     * 绑定 start
     * @return mixed
     */
    public function onStart()
    {
        ilog('Start');
    }

    /**
     * 绑定 connect
     * @return mixed
     */
    public function onConnect()
    {
        ilog('connect');
    }

    /**
     * 绑定 receive
     * @return mixed
     */
    public function onReceive()
    {
        ilog('receive');
    }

    /**
     * 绑定 close
     * @return mixed
     */
    public function onClose()
    {
        ilog('close');
    }
}