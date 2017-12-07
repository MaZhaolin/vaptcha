<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

session_start();
function parse_style($str){
    $styles =  explode(';', $str);
    $result = array(
        'color' => '',
        'style' => '',
        'label' => ''
    );
    for ($i = 0; $i < count($styles); $i ++) {
        $style = explode(':', $styles[$i]);
        if(trim($style[0]) == 'color'){
            $result['color'] = trim($style[1]);
            array_splice($styles, $i, 1);
        }
        if(trim($style[0]) == 'label'){ 
            $result['label'] = substr($styles[$i], 6);
            array_splice($styles, $i, 1);
        }
    }
    $result['style'] = implode($styles, ';');
    return $result;
}

    
include_once dirname(__FILE__) . '/lib/vaptcha.php';
include_once template('vaptcha:vaptcha_tpl');
include_once template('vaptcha:click_captcha');

loadcache('plugin');


class plugin_vaptcha {

    public $vaptcha_open = false;
    public $modules = array();
    public $vaptcha;

    public function __construct(){
        global $_G;
        $web = $_G['cache']['plugin']['vaptcha']['web'];
        $vid = $_G['cache']['plugin']['vaptcha']['vid'];
        $key = $_G['cache']['plugin']['vaptcha']['key'];
        $this->modules = unserialize($_G['cache']['plugin']['vaptcha']['modules']);
        $this->vaptcha = new Vaptcha($vid, $key);
        if ($web == '1') {
            if (CURMODULE == "logging" || CURMODULE == "register") {
                $this->vaptcha_open = true;
            } 
            else if (in_array($_G['groupid'], unserialize($_G['cache']['plugin']['vaptcha']['group_id']))) {
                $this->vaptcha_open = true;
            } 
            else {
                $this->vaptcha_open = false;
            }
        } else {
            $this->vaptcha_open = false;
        }

        $post_count = $_SESSION['pc_size_c'];
        if ($post_count == null) {
            $_SESSION['pc_size_c'] = 0;
        }
        else {
            $post_num = $_G['cache']['plugin']['vaptcha']['post_num'];
            if ($post_num != 0 && $post_count >= $post_num) {
                $this->vaptcha_open = false;
            }
        }
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
        $isHttps = $_G['cache']['plugin']['vaptcha']['is_https'] ? 'true' : 'false';
        if ($isHttps == 'false' && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/2.0') $isHttps = 'true';
        $Https = $isHttps == 'true' ? 'https' : 'http';
        $pos = CURMODULE;
        include template('vaptcha:embed_captcha'); 
        dexit();
    }

    public function _cur_mod_is_valid() {
        $cur = CURMODULE;
        switch (CURMODULE) {
            case "logging":
                $mod = "2";
                break;

            case "register":
                $mod = "1";
                break;

            case "post":
                if ($_GET["action"] == "reply") {
                    $mod = "4";
                } 
                else if ($_GET["action"] == "newthread") {
                    $mod = "3";
                } 
                else if ($_GET["action"] == "edit") {
                    $mod = "5";
                }
                break;

            case "forumdisplay":
                $mod = "3";
                break;

            case "viewthread":
                $mod = "4";
                break;

            case "follow":
                $mod = "6";
                break;

            case "spacecp":
                if ($_GET["ac"] == "blog") {
                    $mod = "7";
                }
                if ($_GET["ac"] == "comment") {
                    $mod = "8";
                }
                if ($_GET["ac"] == "follow") {
                    $mod = "6";
                }
                if ($_GET["ac"] == "credit") {
                    $mod = "9";
                }
                break;

            case "space":
                if ($_GET["do"] == "wall") {
                    $mod = "8";
                }
                if ($_GET["do"] == "blog" || $_GET["do"] == "index") {
                    $mod = "7";
                } 
                else {
                    $mod = "4";
                }
                
                break;

            case "connect":
                $mod = "1";
                break;

            case "index":
                $mod = "2";
                break;
            case 'portalcp':
                $mod = '10';
                break;

            default:
                return 1;
        }
        return in_array($mod, $this->modules);
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


