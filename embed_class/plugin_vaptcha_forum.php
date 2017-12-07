<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}


class plugin_vaptcha_forum extends plugin_vaptcha {
	function viewthread_fastpost_btn_extra() {
		global $_G;
		if(!$_G['uid'] || !$this->_cur_mod_is_valid()) return;
		return $this->forumdisplay_fastpost_btn_extra();
	}
	function post_btn_extra() {
		global $_G;
		if(!$_G['uid'] || !$this->_cur_mod_is_valid()) return;
		$script = <<<JS
		vaptcha.style.margin = "10px 0";
		document.getElementById('post_extra_tb').style.zIndex = 0;
JS;
		return $this->get_captcha('post_style', 'postform', 'postsubmit', $script);
	}
	
	function forumdisplay_fastpost_btn_extra() {
		global $_G;
		if(!$_G['uid'] || !$this->_cur_mod_is_valid()) return;
		return $this->get_captcha('comment_style', 'fastpostform', 'fastpostsubmit',  tpl_ajax_captcha(), true);
	}

	function post_infloat_middle_output() {
		$script = <<<JS
		initVaptcha(vaptcha, form, 'float');
		vaptcha.style.marginLeft = '10px';
JS;
		return $this->get_captcha('comment_style', 'postform', 'postsubmit',  tpl_ajax_captcha($script), true);
	}
	
	function post_recode() {
		if (!$this->has_authority() || !$this->_cur_mod_is_valid() || !$this->vaptcha_open) {
            return;
		}

	/*	if ($_GET['action'] == 'reply' && $_GET['inajax'] == '1' && $_GET['handlekey'] != 'reply' && $_GET['infloat'] != 'yes') {
			
			return '<root>'.$this->get_embed_captcha().'';
		}*/

        global $_G;
		if (submitcheck('topicsubmit') || submitcheck('replysubmit') || submitcheck('editsubmit')) {
			$challenge = $_GET['vaptcha_challenge'];
			$token = $_GET['vaptcha_token'];
			if(!$token) {
				if ($_GET['handlekey'] == 'vfastpost') {
					$this->get_embed_captcha('vfastpostform', 'document.getElementById("vreplysubmit")');
					return;
				}
				showmessage(lang('plugin/vaptcha', 'Please click the verify button below to man-machine validation'));
			}
            $validatePass = $this->vaptcha->Validate($challenge, $token);
            if (!$validatePass) {
				if ($_GET['handlekey'] == 'vfastpost') {
					$this->get_embed_captcha('vfastpostform', 'document.getElementById("vreplysubmit")', true);
					return;
				}
				showmessage(lang('plugin/vaptcha', 'The second validation fails, please try refresh'));		
            } else {
            	$post_count = $_G['cookie']['pc_size_c'];
	            $post_count = ($post_count + 1);
	            dsetcookie('pc_size_c', $post_count);
            }
        }
        
	}
}
