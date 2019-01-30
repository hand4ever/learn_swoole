<?php
/**
 * Created by PhpStorm.
 * User: panlong
 * Date: 2019/1/30
 * Time: 09:41
 * <memo>
 * 1. 查看进程信息
 * htop -p $(echo $(ps -ef|grep main.php|grep -v grep|awk '{print $2}')|tr ' ' ',')
 * </memo>
 */

include "HelloProcess.php";
use \myswoole\process\HelloProcess;

$hp = new \myswoole\process\HelloProcess();
