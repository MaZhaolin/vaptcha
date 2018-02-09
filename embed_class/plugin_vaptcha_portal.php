<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class plugin_vaptcha_portal extends plugin_vaptcha {
	function portalcp_bottom() {
		if (!$this->has_authority() || !$this->_cur_mod_is_valid() || !$this->vaptcha_open) return;
		return $this->get_captcha('post_style', 'articleform', 'issuance');
	}

	function view_article_op_extra() {
        if (!$this->has_authority() || !$this->_cur_mod_is_valid() || !$this->vaptcha_open) return;
        $script = <<<JS
        addEvent(document.getElementById('commentsubmit_btn'), 'click', function(e){
            if (!_vaptcha.isPass) {
                _vaptcha.notify();
                if(e && e.stopPropagation) { 
                    e.stopPropagation(); 
                    e.preventDefault();
                } else {
                    window.event.cancelBubble = true; 
                } 
            }
        })
JS;
		return $this->get_captcha('comment_style', 'cform', 'commentsubmit_btn', $script, true);
	}

	function portalcp_reode() {
		if (!$this->has_authority() || !$this->_cur_mod_is_valid() || !$this->vaptcha_open) return;
		
		$challenge = $_GET['vaptcha_challenge'];
		$token = $_GET['vaptcha_token'];
        if (submitcheck('articlesubmit', 1) || submitcheck('replysubmit')) {
            if(!$token) {
                showmessage(lang('plugin/vaptcha', 'Please click the verify button below to man-machine validation'));
            }
            $validatePass = $this->vaptcha->validate($challenge, $token);
            if (!$validatePass) {
                showmessage(lang('plugin/vaptcha', 'The second validation fails, please try refresh'));     
            }
        }
	}
}