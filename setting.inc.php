<?php
if (!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
    exit('Access Denied');
}
global $_G;
require_once dirname(__FILE__) . '/lib/helper.class.php';

if (isset($_POST['config'])) { 
    $data = $_POST['config'];
    $data['modules'][0]['label'] = Helper::characet($data['modules'][0]['label']);
    $data['modules'][1]['label'] = Helper::characet($data['modules'][1]['label']);
    $data = json_encode($data);
    $data = str_replace('"true"', 'true', $data);
    $data = str_replace('"false"', 'false', $data);
    $data = json_decode($data, true);
    
    $data['modules'][0]['label'] = Helper::characet($data['modules'][0]['label'], CHARSET, 'utf-8');
    $data['modules'][1]['label'] = Helper::characet($data['modules'][1]['label'], CHARSET, 'utf-8');
    
    if(!isset($data['enableModules'])) $data['enableModules'] = array();
    if(!isset($data['enableGroups'])) $data['enableGroups'] = array();
    
    C::t('common_setting')->update_batch(array("vaptcha" => $data));
    updatecache('setting');
    $landurl = 'action=plugins&operation=config&do='.$pluginid.'&identifier=vaptcha&pmod=setting';
	cpmsg('plugins_edit_succeed', $landurl, 'succeed');
}

$static_path  = Helper::staticUrl();
$config = Helper::config();
$config['modules'][0]['label'] = Helper::characet($config['modules'][0]['label']);
$config['modules'][1]['label'] = Helper::characet($config['modules'][1]['label']);

$groups = C::t('common_usergroup')->fetch_all_by_type('', null, true);
for($i = 0; $i < count( $groups); $i ++) {
    $groups[$i]['grouptitle'] = Helper::characet($groups[$i]['grouptitle']);
}
include template('vaptcha:setting');
?>
