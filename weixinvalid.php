<?php
/*
DISPLAY ERROR
 */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(-1);

require_once 'lib/lib.weixin.php';
$weixin = new WeiXin($_GET['signature'], $_GET['timestamp'], $_GET['nonce'], $_GET['echostr']);
if (isset($_GET['echostr'])) {
    $weixin->valid();
//    $weixin->responseMsg();
} else {
    $weixin->responseMsg();
}
