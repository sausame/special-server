<?php

session_start();

$now = time();

if (isset($_SESSION['lastTimeStamp']) and $now < ($_SESSION['lastTimeStamp'] + 600)) {
    die('You are too quick, please try it after a while.');
}

$_SESSION['lastTimeStamp'] = $now;

?>
