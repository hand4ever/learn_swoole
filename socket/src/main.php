<?php
/**
 * Created by PhpStorm.
 * User: panlong
 * Date: 2018/9/20
 * Time: 20:25
 */
use myswoole\socket;

$ip = '0.0.0.0';
$port = 9999;
$setting = [
    'worker_num' => 1,
    'daemonize'  => false,
    'debug_mode' => 1
];
$echoServer = new socket\EchoServer($ip, $port, $setting);