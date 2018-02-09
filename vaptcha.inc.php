<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
loadcache('plugin');
require_once dirname(__FILE__) . '/controller/main.php';

$action = $_GET['type'];
$instance = new Main();
if (method_exists($instance, $action)) {
    echo $instance->$action();
} else {
    echo json_encode(array('msg' => 'Not Found', 'version' => VERSION));
}
