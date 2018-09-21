<?php
/**
 * Created by PhpStorm.
 * User: panlong
 * Date: 2018/9/20
 * Time: 20:44
 */

namespace myswoole;

/**
 * @param $o
 * @param string $prefix
 */
function ilog($o, $prefix='INFO') {
    if(empty($o)) {
        $logStr = '<nil>';
    } elseif(!is_string($o)) {
        $logStr = var_export($o, true);
    } else {
        $logStr = $o;
    }
    $content = '[' . $prefix . ']<' . date("Y-m-d H:i:s") . '> '. $logStr . PHP_EOL;
    echo $content;
}