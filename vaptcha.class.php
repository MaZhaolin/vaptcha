<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

session_start();

require_once dirname(__FILE__) . '/lib/vaptcha.class.php';
require_once dirname(__FILE__) . '/lib/helper.class.php';
require_once template('vaptcha:vaptcha_tpl');
require_once template('vaptcha:click_captcha');

loadcache('plugin');

class plugin_vaptcha {

    public $vaptcha_open = false;
    public $modules = array();
    public $vaptcha;

    public function __construct(){
        global $_G;
        $this->vaptcha_open = Helper::config('pc');
        $vid = Helper::config('vid');
        $key = Helper::config('key');
        $this->modules = Helper::config('enableModules');
        if (!in_array($_G['groupid'], Helper::config('enableGroups'))) {            
            $this->vaptcha_open = false;
        }
        $this->vaptcha = new Vaptcha($vid, $key);
    }

    public function vaptcha_template($style, $script, $nodes = '') {
        if(!$this->vaptcha_open || !$this->_cur_mod_is_valid()) return;
        return tpl_vaptcha($style, $script, $nodes);
    }

    public function get_captcha($style, $form, $submit, $script = '', $ie = false) {
        return $this->vaptcha_template($style, tpl_click_captcha($form, $submit, $script, $ie));
    }

    public function return_script($script){
        include template('common/header_ajax');
        echo $script;
        include template('common/footer_ajax');
        dexit();
    }

    public function get_embed_captcha($form, $btn, $refresh = false) {
        $refresh = $refresh ? 'true' : 'false'; 
        global $_G;
        $isHttps = Helper::config('https') ? 'true' : 'false';
        if ($isHttps == 'false' && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/2.0') $isHttps = 'true';
        $Https = $isHttps == 'true' ? 'https' : 'http';
        $pos = CURMODULE;
        $scene = Helper::getScene();
        $lang = Helper::config('lang');
        include template('vaptcha:embed_captcha'); 
        dexit();
    }

    public function _cur_mod_is_valid() {
        return in_array(Helper::getCurrentModule(), $this->modules);
    }

    function has_authority() {
        
        include_once (DISCUZ_ROOT . '/source/discuz_version.php');
        if (DISCUZ_VERSION == "X2.5" && $_GET['handlekey'] == "vfastpost") {
            return false;
        }
        
        if ($_GET['mobile'] == 'no' && $_GET['module'] == 'sendreply') {
            return false;
        }
        if ($_GET['mobile'] == 'no' && $_GET['module'] == 'newthread') {
            return false;
        }
        
        if ($_GET['action'] == 'reply' && $_GET['inajax'] == '1' && $_GET['handlekey'] != 'reply' && $_GET['infloat'] != 'yes') {
            
            // return false;
        }
        
        global $_G;
        
        $action = $_GET['action'];
        
        if ($action == 'newthread' && $_G['group']['allowpost'] != 1) {
            
            return false;
        } 
        else if ($action == 'reply' && $_G['group']['allowreply'] != 1) {
            
            return false;
        }
        
        return true;
    }
}

include('embed_class/plugin_vaptcha_forum.php');
include('embed_class/plugin_vaptcha_member.php');
include('embed_class/plugin_vaptcha_home.php');
include('embed_class/plugin_vaptcha_group.php');
include('embed_class/plugin_vaptcha_portal.php');


