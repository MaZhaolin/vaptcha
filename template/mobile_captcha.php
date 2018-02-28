<?php

if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
function get_captcha($form,$button, $script = '') {
    global $_G;
    $isHttps = $_G['cache']['plugin']['vaptcha']['is_https'] ? 'true' : 'false';
    if ($isHttps == 'false' && $_SERVER['SERVER_PROTOCOL'] == 'HTTP/2.0') $isHttps = 'true';
    $Https = $isHttps == 'true' ? 'https' : 'http';
    $please_finish_validation = lang('plugin/vaptcha',  'please_finish_validation');
    $Provide_technical_support_by_vaptcha = lang('plugin/vaptcha',  'Provide_technical_support_by_vaptcha');
    $scene = Helper::getScene();
    $lang = Helper::config('lang');
$result = <<<HTML
<style id="dzVaptcha">
    #discuz-vp-container{
        position: fixed;
        z-index:99999;
        left: 0;
        right: 0;
        bottom: 0;
        top: 0;
        background: rgba(0, 0, 0, 0.3);
    }
    #discuz-vp-container .vp-content, #discuz-vp-container .vp-header{
        width: 100%;
        margin: 0 auto;
    }
    #discuz-vp-container .vp-header{
        line-height: 30px;
        background: #212B39;
        padding-left: 10px;
        color: #fff;
        box-sizing: border-box
    }

    @keyframes bounceDelay {
        0%,
        80%,
        to {
            transform: scale(0)
        }
        40% {
            transform: scale(1)
        }
    }
    #discuz-vp-container .vp-content{
        background: #fff;
        position: relative;
    }
    #discuz-vp-container .vp-content .loading {
        padding-top: 25%;
        position: absolute;
        width: 100%;
        height: 100%;
        background: #fff;
        text-align: center;
        box-sizing: border-box;
    }
    #discuz-vp-container .vp-content .loading span {
        width: 10px;
        height: 10px;
        background-color: #347eff;
        border-radius: 50%;
        display: inline-block;
        animation: bounceDelay 1s infinite ease-in-out;
        animation-fill-mode: both;
        margin-left: 4px;
    }
    #discuz-vp-container .vp-header a{
        float: right;
        padding-right: 10px;
        color: #404955;
    }
</style>
<script type="text/javascript">
    (function() {
        var v = document.createElement('script');
        v.src = 'https://cdn.vaptcha.com/v.js';
        v.onload = function() {
            document.head.appendChild(document.getElementsByTagName('style')['dzVaptcha']);
            var initVaptcha = function(element, form, type, successCallback){
                var xmlHttp;
                type = type || 'popup';
                function createxmlHttpRequest() {
                    if (window.ActiveXObject) {
                        xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
                    } else if (window.XMLHttpRequest) {
                        xmlHttp = new XMLHttpRequest();
                    }
                }
                createxmlHttpRequest();
                xmlHttp.open("GET", './plugin.php?id=vaptcha&type=challenge&scene={$scene}&t=' + (new Date()).getTime());
                xmlHttp.send(null);
                xmlHttp.onreadystatechange = function(result) {
                    if ((xmlHttp.readyState === 4) && (xmlHttp.status === 200)) {
                        var data = JSON.parse(xmlHttp.responseText);
                        var options = {
                            vid: data.vid,
                            challenge: data.challenge,
                            container: element,
                            type: type,
                            https: $isHttps,
                            lang: '{$lang}',
                            outage: './plugin.php?id=vaptcha&type=downtime',
                            success: function (token, challenge) {
                                var inputs = form.getElementsByTagName('input');
                                inputs['vaptcha_challenge'].value = challenge;
                                inputs['vaptcha_token'].value = token;
                                successCallback && successCallback();
                            }
                        }
                        window.vaptcha(options, function (obj) {
                            var inputs = form.getElementsByTagName('input');
                            inputs['vaptcha_challenge'].value = '';
                            inputs['vaptcha_token'].value = '';
                            obj.init();
                        });
                    }
                }
            }
            function createInput(name) {
                var input = document.createElement('input');
                input.setAttribute('name', name);
                input.setAttribute('type', 'hidden');
                return input;
            }
            function stopPropagation(e) {
                if(e && e.stopPropagation) { 
                    e.stopPropagation(); 
                    e.preventDefault();
                } else {
                    window.event.cancelBubble = true; 
                } 
            }
            function closeDialog() {
                var mask = document.getElementById('mask');
                var dialog = document.getElementById('ntcmsg_popmenu');
                mask && (mask.style.display = 'none');
                dialog && (dialog.style.display = 'none');
            }
            var dzForm = document.getElementById('$form');
            var button = document.getElementById('$button');
            !button && (button = document.getElementsByClassName('$button')[0].getElementsByTagName('button')[0]);
            var vpButton = document.createElement('div');
            var vaptchaContainer = document.getElementById('discuz-vp-container');
            'pmsubmit_btn' == '$button' && (button = button.getElementsByTagName('span')[0]);
            button.addEventListener('click', function(e) {
                var token = dzForm.getElementsByTagName('input')['vaptcha_token'];
                if(token && token.value) return ;
                stopPropagation(e);
                var html = '$please_finish_validation<a href="https://www.vaptcha.com" target="_blank">$Provide_technical_support_by_vaptcha</a>'
                if(vaptchaContainer) {
                    vaptchaContainer.style.display = 'block';
                    return ;
                }
                vaptchaContainer = document.createElement('div');
                var challengeInput = createInput('vaptcha_challenge');
                var tokenInput = createInput('vaptcha_token');
                var vpHeader = document.createElement('div');
                vpHeader.innerHTML = html;
                dzForm.appendChild(challengeInput);
                dzForm.appendChild(tokenInput);
                vaptchaContainer.setAttribute('id', 'discuz-vp-container');
                vpButton.classList.add('vp-content');
                vpHeader.classList.add('vp-header');
                vpButton.innerHTML = '<div class="loading"><span style="animation-delay: -.32s"></span><span style="animation-delay: -.16s"></span><span></span></div>';
                vpButton.addEventListener('click', stopPropagation);
                vpHeader.addEventListener('click', stopPropagation);
                document.body.appendChild(vaptchaContainer); 
                vpButton.style.height = vaptchaContainer.clientWidth * 23/40 + 'px';
                vpHeader.style.marginTop = (vaptchaContainer.clientHeight - vaptchaContainer.clientWidth * 23/40 - 30) / 2 + 'px';
                vaptchaContainer.appendChild(vpHeader);
                vaptchaContainer.appendChild(vpButton);
                vaptchaContainer.addEventListener('click', function(){
                    vaptchaContainer.style.display = 'none';
                    closeDialog();
                });
                initVaptcha(vpButton, dzForm, 'embed', function(){
                    button.setAttribute('disable', false);
                    button.click();
                    closeDialog();
                    vaptchaContainer.style.display = 'none';
                })
            })
            window.refreshVaptcha = function() {
                closeDialog();
                var token = dzForm.getElementsByTagName('input')['vaptcha_token'];
                if (!token.value) return ;
                token.value = '';
                vaptchaContainer = document.getElementById('discuz-vp-container');
                initVaptcha(vpButton, dzForm, 'embed', function(){
                    button.setAttribute('disable', false);
                    button.click();
                    closeDialog();
                    vaptchaContainer.style.display = 'none';            
                })
                vaptchaContainer.style.display = 'block';
            }
            $script
        }
        document.head.appendChild(v);
    })()
</script>
HTML;
    return $result;
}?>
