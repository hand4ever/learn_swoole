<?php
/**
 * Created by PhpStorm.
 * User: panlong
 * Date: 2018/9/20
 * Time: 20:30
 */

namespace myswoole\socket;

class EchoServer extends BaseServer
{
    public function __construct($ip, $port, array $setting)
    {
        $this->init($ip, $port, $setting);
        $this->run();
    }

}