<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
include_once template('vaptcha:member_captcha');

class plugin_vaptcha_member extends plugin_vaptcha{

    public function logging_input() { 
        if(!$this->vaptcha_open || !$this->_cur_mod_is_valid() || $_GET['action'] == "logout") return;
        $id = uniqid();
        return tpl_vaptcha('login_style', tpl_input_script($id, true), tpl_input_nodes($id, 'login_style'), false, $id);
    }

    public function logging_code() {
        if(!$this->vaptcha_open || !$this->_cur_mod_is_valid() || CURMODULE != 'logging' || $_GET['action'] == "logout") return;
        global $_G;
        $challenge = $_GET['vaptcha_challenge'];
        $token = $_GET['vaptcha_token'];
        if($_GET['username'] && $_GET['password'] && $_GET['lssubmit'] == "yes"){
            $pass_count = $_SESSION['vp_login_pass_count'];
            if($pass_count == null){
                $_SESSION['vp_login_pass_count'] = 0;
                $pass_count = 0;
            }
            $count = $_G['cache']['plugin']['vaptcha']['is_ai'] ? 6 : 1;
            if ($pass_count > 0 && $pass_count < $count) {
                $_SESSION['vp_login_pass_count'] = $pass_count + 1;
                return;
            }
            if(!$token){
                $this->get_embed_captcha('lsform', 'form.getElementsByClassName("vm")[2]');
                return;
            } else {
                $validatePass = $this->vaptcha->Validate($challenge, $token);
                if (!$validatePass) {
                    $this->get_embed_captcha('lsform', 'form.getElementsByClassName("vm")[2]', true);
                    // showmessage(lang('plugin/vaptcha', 'The second validation fails, please try refresh'));     
                } else {
                    $_SESSION['vp_login_pass_count'] = 1;
                }
                return ;
            }
        }

        if( ! $this->has_authority() ){
            return;
        }

        if (submitcheck('loginsubmit', 1, $seccodestatus)) {
            if(!$token) {
                showmessage(lang('plugin/vaptcha', 'Please click the verify button below to man-machine validation')); 
            }
            $validatePass = $this->vaptcha->validate($challenge, $token);
            if (!$validatePass) {
                showmessage(lang('plugin/vaptcha', 'The second validation fails, please try refresh'));     
            }
        }
    }

    public function register_input_output() {
        if(!$this->_cur_mod_is_valid()) return;
        $id = uniqid();
        return tpl_vaptcha('register_style', tpl_input_script($id), tpl_input_nodes($id, 'register_style'), false, $id);
    }

    public function register_code() {
        if(!$this->vaptcha_open || !$this->_cur_mod_is_valid() || CURMODULE != 'register') return;
        if (submitcheck('regsubmit')) {
            $challenge = $_GET['vaptcha_challenge'];
            $token = $_GET['vaptcha_token'];
            if(!$token) {
                showmessage(lang('plugin/vaptcha', 'Please click the verify button below to man-machine validation')); 
            }
            $validatePass = $this->vaptcha->Validate($challenge, $token);
            if (!$validatePass) {
                showmessage(lang('plugin/vaptcha', 'The second validation fails, please try refresh'));     
            }
        }
    }

    function lostpasswd_code(){
        // showmessage(lang('plugin/vaptcha', 'The second validation fails, please try refresh'));     
        
        if(submitcheck('lostpwsubmit')){
			$challenge = $_GET['vaptcha_challenge'];
			$token = $_GET['vaptcha_token'];
	
			$pass_count = $_SESSION['vp_lost_passwd_count'];
			if($pass_count == null){
				$_SESSION['vp_lost_passwd_count'] = 0;
				$pass_count = 0;
			}
			global $_G;
			$count = $_G['cache']['plugin']['vaptcha']['is_ai'] ? 5 : 1;
			if ($pass_count > 0 && $pass_count < $count) {
				$_SESSION['vp_lost_passwd_count'] = $pass_count + 1;
				return;
			}
			if(!$token){
				$this->get_embed_captcha('document.getElementsByTagName("button")["lostpwsubmit"]', 'document.getElementsByTagName("button")["lostpwsubmit"]');
				return;
			} else {
				$validatePass = $this->vaptcha->Validate($challenge, $token);
				if (!$validatePass) {
					$this->get_embed_captcha('document.getElementsByTagName("button")["lostpwsubmit"]', 'document.getElementsByTagName("button")["lostpwsubmit"]', true);
				} else {
					$_SESSION['vp_lost_passwd_count'] = 1;
				}
				return ;
			}
		}
    }
}