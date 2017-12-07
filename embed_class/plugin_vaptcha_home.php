<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_vaptcha_home extends plugin_vaptcha
{

	//pay
	function spacecp_credit_bottom() {
		global $_G;
		if(!$_G['uid'] || !$this->_cur_mod_is_valid() || $_GET['op'] != 'buy') return;
		return $this->get_captcha('credit_style', 'addfundsform', 'addfundssubmit_btn', tpl_ajax_captcha());
	}

	//blog
	function spacecp_blog_bottom() {
		global $_G;
		if(!$_G['uid'] || !$this->_cur_mod_is_valid()) return;
		return $this->get_captcha('blog_style', 'ttHtmlEditor', 'issuance');
	}

	//comment
	public function space_blog_face_extra() {
		global $_G;
		if(!$_G['uid'] || !$this->_cur_mod_is_valid()) return;
		return $this->get_captcha('blog_style', 'quickcommentform_1', 'commentsubmit_btn', tpl_ajax_captcha());
	}

	public function space_wall_face_extra() {
		global $_G;
		if(!$_G['uid'] || !$this->_cur_mod_is_valid()) return;
		return $this->get_captcha('space_style', 'quickcommentform_1', 'commentsubmit_btn', tpl_ajax_captcha(), true);
	}

	 //handle validation
    public function spacecp_follow_recode(){
    	if( ! $this->has_authority() || !$this->_cur_mod_is_valid() || !$this->vaptcha_open)return;
        $challenge = $_GET['vaptcha_challenge'];
        $token = $_GET['vaptcha_token'];

        $pass_count = $_SESSION['vp_follow_pass_count'];
        if($pass_count == null){
            $_SESSION['vp_follow_pass_count'] = 0;
            $pass_count = 0;
        }
        global $_G;
        $count = $_G['cache']['plugin']['vaptcha']['is_ai'] ? 6 : 1;
        if ($pass_count > 0 && $pass_count < $count) {
            $_SESSION['vp_follow_pass_count'] = $pass_count + 1;
            return;
        }
        if(!$token){
            $this->get_embed_captcha('fastpostform', 'document.getElementById("fastpostsubmit")');
        } else {
            $validatePass = $this->vaptcha->Validate($challenge, $token);
            if (!$validatePass) {
                $this->get_embed_captcha('fastpostform', 'document.getElementById("fastpostsubmit")', true);
				// showmessage(lang('plugin/vaptcha', 'The second validation fails, please try refresh'));     
            } else {
                $_SESSION['vp_follow_pass_count'] = 1;
            }
        }
    }
    public function spacecp_blog_recode(){
    	$this->spacecp_recode();
    }
    public function spacecp_comment_recode(){
    	$this->spacecp_recode();
    }

    public function space_wall_recode() {
    	$this->spacecp_recode();
    }

    public function spacecp_credit_recode(){
    	if($_GET['op'] != 'buy') return ;
    	$this->spacecp_recode();
    }

	function spacecp_recode() {
		if( ! $this->has_authority() || !$this->_cur_mod_is_valid() || !$this->vaptcha_open)return;
        global $_G;
		$challenge = $_GET['vaptcha_challenge'];
		$token = $_GET['vaptcha_token'];
        if ($_POST['handlekey'] == 'buycredit'&& $_GET['op'] == 'buy' ) {
			if(!$token) {
                return $this->msg(lang('plugin/vaptcha', 'Please click the verify button below to man-machine validation'));
            }
            $validatePass = $this->vaptcha->Validate($challenge, $token);
            if (!$validatePass) {
                return $this->msg(lang('plugin/vaptcha', 'The second validation fails, please try refresh'));
            }
        }

		if(submitcheck('topicsubmit') || submitcheck('blogsubmit') || submitcheck('commentsubmit')) {
				if(!$token) {
	                showmessage(lang('plugin/vaptcha', 'Please click the verify button below to man-machine validation')); 
	            }
	            $validatePass = $this->vaptcha->Validate($challenge, $token);
	            if (!$validatePass) {
	                showmessage(lang('plugin/vaptcha', 'The second validation fails, please try refresh'));     
	            }
			}
	}

 
	function has_authority(){ 
        if( $_GET['mobile'] == 'no' && $_GET['submodule'] == 'checkpost' ){
            return false;
        }
        
		global $_G;
		
        $action = $_GET['ac']; 
        if($action == 'follow' && $_G['group']['allowpost'] != 1 ){
            return false;
        }else if($action == 'blog' && $_G['group']['allowblog'] != 1 ){
			return false;
        }else if($action == 'comment' && $_G['group']['allowcomment'] != 1 ){
			return false;
        }

        return true;
	}

	private function msg($msg) {
		$script = <<<HTML
		<script type='text/javascript' reload='1'>
		typeof errorhandle_buycredit=='function' && errorhandle_buycredit('$msg', {});
		hideWindow('buycredit');
		showDialog('$msg', 'alert', null, null, 0, null, null, null, null, 2, null);
		updateseccode('');
		</script>
HTML;
		return $this->return_script($script);
	}
}