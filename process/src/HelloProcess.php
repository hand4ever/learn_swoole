<?php
/**
 * Created by PhpStorm.
 * User: panlong
 * Date: 2019/1/30
 * Time: 09:41
 */

namespace myswoole\process;

use \Swoole\Process as SwooleProcess;
use \Swoole\Table as SwooleTable;


class HelloProcess {
    //测试标记
    const TASK_CASE_CWD      = 1;//当前工作目录
    const TASK_CASE_GID      = 2;//当前 PHP 脚本拥有者的用户组 ID
    const TASK_CASE_UID      = 3;//当前 PHP 脚本所有者的 UID
    const TASK_CASE_NICE     = 4; // 进程 nice 值
    const TASK_CASE_FP       = 5;//文件描述符（文件句柄、fp）
    const TASK_CASE_MEM      = 6;//内存
    const TASK_CASE_MYSQL    = 7;//mysql句柄
    const TASK_CASE_FILELOCK = 8;//文件锁

    /**
     * 测试文件名
     */
    const FILENAME = '/Users/panlong/learn/test/php_test/myswoole/process/src/test.log';
    /**
     * 文件句柄
     * @var resource
     */
    public $fp = null;
    /**
     * 共享内存块
     * @var null
     */
    public $swtable = null;
    /**
     * @var bool false:父进程 true：子进程
     */
    public $isFather = false;
    /**
     * @var int 当前进程的 pid
     */
    public $currentPid = 0;
    /**
     * @var array 存放子进程 pid
     */
    public $pids = [];

    /**
     * 测试任务
     *
     * @param $case
     * @param int $step
     */
    public function task($case=null) {
        $prefix = $this->isFather ? '子进程-' : '父进程-';
        $prefix = "{$case} {$prefix}";
        switch($case) {
            case self::TASK_CASE_CWD:
                if(!$this->isFather) {//父进程更改当前工作目录，看看子进程会不会更改
                    chdir('/Users/panlong/');
                }
                $this->info($this->currentPid, "{$prefix}当前工作目录：" . getcwd());
                break;
            case self::TASK_CASE_GID:
                $this->info($this->currentPid, "{$prefix}用户组 GID：" . getmygid());
                break;
            case self::TASK_CASE_UID:
                $this->info($this->currentPid, "{$prefix}用户组 UID：" . getmyuid());
                break;
            case self::TASK_CASE_NICE:
                if(!$this->isFather) {//父进程更改当前工作目录，看看子进程会不会更改
                    pcntl_setpriority(20, $this->currentPid);
                }
                $this->info($this->currentPid, "{$prefix} nice值：" . pcntl_getpriority($this->currentPid));
                break;
            case self::TASK_CASE_FP://{对于父进程在fork（）之前打开的文件来说，子进程都会继承，与父进程共享相同的文件偏移量。}
                if(!$this->isFather) {//父进程更改当前工作目录，看看子进程会不会更改
                    $this->fp = fopen(self::FILENAME, 'r');
                }
                $this->info($this->currentPid, "{$prefix} 文件描述符：" . print_r($this->fp, true));
                if ($this->isFather) {//子进程写文件
//                    $str = "[" . date('Y-m-d H:i:s') . "-pid=" . $this->currentPid . "-子进程写入]" . PHP_EOL;
//                    fwrite($this->fp, $str, strlen($str));
                    fseek($this->fp, 5);//这里的确影响到了父进程的fp的位置，【说明指向的同一块区域】
//                    $this->info($this->currentPid, "{$prefix} 写入完成！");
                    fclose($this->fp);//这步并不影响父进程的句柄，【说明句柄并不是同一个】
                }
                break;
            case self::TASK_CASE_MEM://模拟子进程写入，父进程读取
                if ($this->isFather) {//父进程
                    //初始化一块内存块 [1,'a'];
                    $this->swtable = new SwooleTable(8);//初始化一个 pow(2,3) 行的内存
                    $this->swtable->column('id', SwooleTable::TYPE_INT, 1);
                    $this->swtable->column('name', SwooleTable::TYPE_STRING, 1);


                    $this->info($this->currentPid, "{$prefix}内存：" . print_r($this->swtable, true));
                } else {//子进程
                    $this->info($this->currentPid, "{$prefix}内存：" . print_r($this->swtable, true));
                }
                break;
            case self::TASK_CASE_MYSQL:

                break;
            default:
                $this->info($this->currentPid, "{$prefix} test！");
        }
    }

    /**
     * 测试 父进程子进程 继承
     */
    public function testInherit() {
        //task 1 {{{ 子进程继承了什么
//        $this->task(self::TASK_CASE_CWD);
//        $this->task(self::TASK_CASE_GID);
//        $this->task(self::TASK_CASE_UID);
//        $this->task(self::TASK_CASE_NICE);
        $this->task(self::TASK_CASE_FP);
//        $this->task(self::TASK_CASE_MEM);
//        $this->task();
        //task 1 }}}
    }
    /**
     * HelloProcess constructor.
     * 构造器 初始化配置
     */
    public function __construct()
    {
        //init 初始化
        $pid = getmypid();
        $this->currentPid = $pid;
        $config = include "process_config.php";
        $this->info($pid, '我是父进程');
        $subNum = $config['sub_num'];



        //test case {{{
        $this->testInherit();

        //test case }}}

        //case a: 生成子进程
        for($i = 0; $i < $subNum; $i++) {
            $process = new SwooleProcess([$this, 'subProcess']);
            $process->start();
            $this->pids[$process->pid] = 1;
            $this->info($pid, '第' . $i . '个子进程启动，他的pid=' . $process->pid);
        }
        $this->info($pid, '生成子进程完毕！pid=' . json_encode(array_keys($this->pids)));

        sleep(2);//让子进程飞一会儿

        //test case
        //test case A:测试文件句柄
        if($this->fp) {//父进程读取文件测试
            $content = fread($this->fp, 1);

            $this->info($this->currentPid, "父进程读文件当前位置的内容：" . $content);
            fclose($this->fp);
        }
        //test case B:测试共享内存
        if ($this->swtable) {//读取内存值
            //TODO
        }



        //case b: 回收子进程
        $this->info($pid, '我开始等待回收子进程');
        while(!empty($this->pids)) {
            $ret = \Swoole\Process::wait();
            $subPid = $ret['pid'];
            $this->info($pid, '回收子进程pid=' . $subPid);
            if (isset($this->pids[$subPid])) {
                unset($this->pids[$subPid]);
            }
        }

        $this->info($pid, '所有子进程回收完毕!');

    }

    /**
     * 子进程
     *
     * @param SwooleProcess $process
     */
    public function subProcess(SwooleProcess $process)
    {
        //init 初始化
        $this->isFather   = true;
        $pid              = $process->pid;
        $this->currentPid = $pid;
        $this->info($pid, '我是子进程');


        //test case {{{
        $this->testInherit();
        //test case }}}

    }


    /**
     * 打印日志方法
     *
     * @param int $processId
     * @param $msg
     * @param $color string
     */
    public function info(int $processId, $msg, $color='red')
    {
        if ($this->isFather) {
            $color = 'red';
        } else {
            $color = 'blue';
        }
        echo "[INFO][" . date("Y-m-d H:i:s") . "][pid=" . self::color($processId, $color) . "]";
        if (empty($msg)) {
            echo "<nil>";
        } else {
            if (is_string($msg)) {
                echo $msg;
            } else {
                print_r($msg);
            }
        }
        echo PHP_EOL;

    }

    /**
     * 给文字加颜色
     * @param $msg
     * @param string $color
     * @return string
     */
    public static function color($msg, $color='red')
    {
        switch($color) {
            case 'red':
                $msg =  "\033[31m" . $msg . "\033[0m";
                break;
            case 'green':
                $msg =  "\033[32m" . $msg . "\033[0m";
                break;
            case 'yellow':
                $msg =  "\033[33m" . $msg . "\033[0m";
                break;
            case 'blue':
                $msg =  "\033[34m" . $msg . "\033[0m";
                break;
        }
        return $msg;
    }
}