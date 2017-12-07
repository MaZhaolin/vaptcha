<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

loadcache('plugin');
require_once dirname(__FILE__) . '/lib/vaptcha.php';
global $_G;

$vid = $_G['cache']['plugin']['vaptcha']['vid'];
$key = $_G['cache']['plugin']['vaptcha']['key'];
$vp = new Vaptcha($vid, $key);
$type = $_GET['type'];

if($type == 'downtime'){
    echo $vp->downTime($_GET['data']);
} else {
    echo $vp->getChallenge();    
}
