<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
session_start();
loadcache('plugin');

include_once dirname(__FILE__) . '/lib/vaptcha.class.php';
include_once dirname(__FILE__) . '/lib/helper.class.php';
include_once dirname(__FILE__) . '/template/mobile_captcha.php';

class mobileplugin_vaptcha {

    public $vaptcha_open = false;
    public $modules = array();
    public $vaptcha;

    public function __construct(){
        global $_G;
        $this->vaptcha_open = Helper::config('mobile');
        $vid = Helper::config('vid');
        $key = Helper::config('key');
        $this->modules = Helper::config('enableModules');
        if (!in_array($_G['groupid'], Helper::config('enableGroups'))) {
            $this->vaptcha_open = false;
        }
        $this->vaptcha = new Vaptcha($vid, $key);
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

class mobileplugin_vaptcha_forum extends mobileplugin_vaptcha {
    
    function post_bottom_mobile() {
        if(!$this->vaptcha_open || !$this->_cur_mod_is_valid()) return;
    }


    function viewthread_bottom_mobile() {
        if(!$this->vaptcha_open || !$this->_cur_mod_is_valid()) return;         
    }

    function post_recode() {
        if (!$this->has_authority() || !$this->_cur_mod_is_valid() || !$this->vaptcha_open) {
            return;
        }
        global $_G;
        if (submitcheck('topicsubmit') || submitcheck('replysubmit') || submitcheck('editsubmit')) {
            $challenge = $_GET['vaptcha_challenge'];
            $token = $_GET['vaptcha_token'];
            if(!$token) {
                if($_GET['handlekey'] === 'postform') showmessage(get_captcha('postform', 'postsubmit', 'button.click()'));
                if($_GET['handlekey'] === 'fastpost') showmessage(get_captcha('fastpostform', 'fastpostsubmit', 'button.click()'));
                if($_GET['handlekey'] === 'fastpostform') showmessage(get_captcha('fastpostform', 'postsubmit', 'button.click()'));
            }
            $validatePass = $this->vaptcha->Validate($challenge, $token);
            if (!$validatePass) {
                showmessage('<script type="text/javascript" reload="1">refreshVaptcha();</script>');    
            } else {
                $post_count = $_G['cookie']['pc_size_c'];
                $post_count = ($post_count + 1);
                dsetcookie('pc_size_c', $post_count);
            }
        }
        
    }
}

class mobileplugin_vaptcha_member extends mobileplugin_vaptcha{

    public function logging_bottom_mobile() { 
        if(!$this->vaptcha_open || !$this->_cur_mod_is_valid()) return;
        return get_captcha('loginform', 'btn_login');
    }

    public function logging_code() {
        if(!$this->vaptcha_open || !$this->_cur_mod_is_valid() || $_GET['action'] == "logout") return;
        global $_G;
        $challenge = $_GET['vaptcha_challenge'];
        $token = $_GET['vaptcha_token'];
        
        if (submitcheck('loginsubmit', 1, $seccodestatus) && empty($_GET['lssubmit'])) {
            if(!$token) {
                showmessage(get_captcha('loginform', 'btn_login', 'button.click()'));
            }
            $validatePass = $this->vaptcha->validate($challenge, $token);
            if (!$validatePass) {
                showmessage('<script>refreshVaptcha();</script>');
            }
        }
    }

    public function register_code() {
        if(!$this->vaptcha_open || !$this->_cur_mod_is_valid() || CURMODULE != 'register') return;
        if (submitcheck('regsubmit')) {
            $challenge = $_GET['vaptcha_challenge'];
            $token = $_GET['vaptcha_token'];
            if(!$token) {
                showmessage(get_captcha('registerform', 'btn_register', 'button.click()'));
            }
            $validatePass = $this->vaptcha->Validate($challenge, $token);
            if (!$validatePass) {
                showmessage('<script>refreshVaptcha();</script>');
            }
        }
    }
}

class mobileplugin_vaptcha_home extends mobileplugin_vaptcha{
    function spacecp_pm_recode() {
        if(!submitcheck('pmsubmit') || !$this->vaptcha_open || $_GET['mobile'] != 2) return ;
        $challenge = $_GET['vaptcha_challenge'];
        $token = $_GET['vaptcha_token'];
        if(!$token) {
            if($_GET['handlekey'] == 'pmform_0') {
                showmessage(get_captcha('pmform_0', 'pmsubmit_btn', 'button.click()'));                
            } else {
                showmessage(get_captcha('pmform', 'pmsubmit', 'button.click()'));            
            }
        }
        $validatePass = $this->vaptcha->Validate($challenge, $token);
        if (!$validatePass) {
            showmessage('<script>refreshVaptcha();</script>');
        }
    }
}
