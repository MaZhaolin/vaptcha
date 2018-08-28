<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

require_once dirname(dirname(__FILE__)) . '/lib/helper.class.php';
require_once dirname(dirname(__FILE__)) . '/lib/vaptcha.class.php';


class Main {
    
    private $vaptcha;

    public function __construct(){
        global $_G;
        $vid = Helper::config('vid');
        $key = Helper::config('key');
        $this->vaptcha = new Vaptcha($vid, $key);
    }

    public function challenge() {
        return $this->vaptcha->getChallenge($_REQUEST['scene']);
    }

    public function downtime() {
        return $this->vaptcha->downTime($_GET['data']);
    }
}