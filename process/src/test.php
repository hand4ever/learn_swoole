<?php
$filename = '/Users/panlong/learn/test/php_test/myswoole/process/src/test.log';
$fp = fopen($filename, 'a+');
$content = fread($fp, filesize($filename));
var_dump($content);

fclose($fp);
