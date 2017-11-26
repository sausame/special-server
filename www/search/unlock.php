<?php

$path = sys_get_temp_dir() . '/'. 'search-lock.dat';

$fp = fopen($path, 'r+b') or die("Unable to open search lock file!");

if(! flock($fp, LOCK_EX)) {
    echo 'Unable to obtain lock';
    exit(-1);
}

fseek($fp, 0, SEEK_SET);
$len = filesize($path);

if ($len > 0) {
    $buf = fread($fp, $len);
} else {
    $buf = NULL;
}

echo('<pre>' . $buf . '</pre>');

$num = 0;
if (! empty($buf)) {
    $num = (int)$buf;
    $num --;
}

if ($num !== 0) {
    fseek($fp, 0, SEEK_SET);
    fwrite($fp, '' . $num);
}

fclose($fp);

if (0 === $num) {
    unlink($path);
}

?>

