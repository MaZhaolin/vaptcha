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
    $vid = Helper::config('vid');
$result = <<<HTML
<script type="text/javascript">
    (function() {
        var v = document.createElement('script');
        v.src = 'https://cdn.vaptcha.com/v2.js';
        v.onload = function() {
            var style = document.createElement('style');
            style.innerHTML = '.vaptcha-mobile{position:fixed;top:0;z-index: 20000;}.vaptcha-mobile .vaptcha-mask{position:fixed;width:100%;height:100%;top:0;left:0;background-color:#fff;opacity:.95;filter:alpha(opacity=95)}.vaptcha-mobile .vaptcha-mobile-main{position:fixed;width:100%;height:100%}.vaptcha-mobile .vaptcha-mobile-close{cursor:pointer;position:absolute;top:-32px;right:-32px;width:64px;height:64px;background-color:#000;border-radius:50%;-webkit-border-radius:50%;-moz-border-radius:50%;-o-border-radius:50%}.vaptcha-mobile .vaptcha-mobile-close .vaptcha-mobile-close-img{position:absolute;left:13px;bottom:13px;display:inline-block;width:12px;height:12px;background-image:url(http://cdn.vaptcha.com/v1.0.1beat.png);background-repeat:no-repeat;background-position:-4px -281px}.vaptcha-mobile .vaptcha-mobile-title{position:absolute;top:-47px;width:100%;font-size:24px;font-weight:700;color:#898989;text-align:center}.vaptcha-mobile .vaptcha-mobile-support{position:absolute;right:10px;bottom:10px;font-size:12px;color:#CDCDCE;text-align:right}.vaptcha-mobile .vaptcha-pop{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);-webkit-transform:translate(-50%,-50%);-moz-transform:translate(-50%,-50%);-ms-transform:translate(-50%,-50%);width:100%;font-size:0;box-shadow:0 0 20px rgba(0,0,0,.15);-moz-box-shadow:0 0 20px rgba(0,0,0,.15);-webkit-box-shadow:0 0 20px rgba(0,0,0,.15);transition:height 212ms linear;-webkit-transition:height 212ms linear;-moz-transition:height 212ms linear;-ms-transition:height 212ms linear}.vaptcha-mobile .vp-main{text-align:center}@keyframes bounceDelay{0%,80%,to{transform:scale(0)}40%{transform:scale(1)}}.vaptcha-mobile .loading{padding-top:25%;position:absolute;width:100%;height:100%;background:#fff;text-align:center;box-sizing:border-box}.vaptcha-mobile .loading span{width:10px;height:10px;background-color:#347eff;border-radius:50%;display:inline-block;animation:bounceDelay 1s infinite ease-in-out;animation-fill-mode:both;margin-left:4px}';
            document.head.appendChild(style);
            var initVaptcha = function(element, form, type, successCallback){
                type = type || 'popup';
                var options = {
                    vid: '$vid',
                    container: element,
                    type: type,
                    https: $isHttps,
                    lang: '{$lang}',
                    outage: './plugin.php?id=vaptcha&type=downtime',
                }
                window.vaptcha(options).then(function (obj) {
                    var inputs = form.getElementsByTagName('input');
                    var isPass = false;
                    obj.renderTokenInput(form);
                    obj.listen('pass', function () {
                        var inputs = form.getElementsByTagName('input');
                        successCallback && successCallback();
                        isPass = true
                    })
                    obj.render();
                    window.refreshVaptcha = function() {
                        closeDialog();
                        if(!isPass) return;
                        isPass = false
                        obj.reset()
                        vaptchaContainer = document.getElementById('discuz-vp-container');
                        vaptchaContainer.style.display = 'block';
                    }
                })
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
            var vpButton = null;
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
                // var challengeInput = createInput('vaptcha_challenge');
                // var tokenInput = createInput('vaptcha_token');
                var vpHeader = document.createElement('div');
                vpHeader.innerHTML = html;
                // dzForm.appendChild(challengeInput);
                // dzForm.appendChild(tokenInput);
                vaptchaContainer.setAttribute('id', 'discuz-vp-container');
                vaptchaContainer.className = 'vaptchaNavDiv vaptcha-mobile'
                vaptchaContainer.innerHTML = '<div class="vaptcha-mask"></div><div class="vaptcha-mobile-main"><div class="vaptcha-mobile-close"><span class="vaptcha-mobile-close-img"></span></div><div class="vaptcha-pop"><div class="vaptcha-mobile-title">$please_finish_validation</div><div id="vaptcha" class="vaptcha"><div class="loading"><span style="animation-delay: -.32s"></span><span style="animation-delay: -.16s"></span><span></span></div></div></div><div class="vaptcha-mobile-support"><a href="https://www.vaptcha.com" target="_blank">$Provide_technical_support_by_vaptcha</a></div></div>';
                document.body.appendChild(vaptchaContainer);
                vpButton = vaptchaContainer.getElementsByClassName('vaptcha');
                var bodyWidth =document.body.offsetWidth;
                var popWidth = 400,popHeight = 230;
                var popDiv = vaptchaContainer.getElementsByClassName('vaptcha-pop')[0];                
                if(bodyWidth < 400){
                    popWidth = bodyWidth;
                    popHeight  = bodyWidth*(230/400);
                }
                popDiv.style.width = popWidth + 'px';
                popDiv.style.height = popHeight + 'px';
                vaptchaContainer.getElementsByClassName('vaptcha-mobile-close')[0]
                .addEventListener('click', function() {
                    vaptchaContainer.style.display = 'none'; 
                    closeDialog();                   
                })
                initVaptcha(vpButton, dzForm, 'embed', function(){
                    button.setAttribute('disable', false);
                    button.click();
                    closeDialog();
                    vaptchaContainer.style.display = 'none';
                })
            })
            $script
        }
        document.head.appendChild(v);
    })()
</script>
HTML;
    return $result;
}?>
