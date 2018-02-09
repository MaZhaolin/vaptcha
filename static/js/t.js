var Vp = function() {
    this.isPass = false;
    this.vaptcha = null;
}
Vp.prototype = {
    constructor: Vp,
    init: function() {

    },
    initVaptcha: function() {
        var self = this;
        this.get({
            url: '',
            success() {
                var data = JSON.parse(xmlHttp.responseText);
                var options = {
                    vid: data.vid,
                    challenge: data.challenge,
                    container: element,
                    type: 'click',
                    effect: "$style['type']",
                    style: "$style['style']",
                    https: $isHttps,
                    color:"$style['color']",
                    lang: 'zh-CN',
                    ai: $isAi,
                    outage: './plugin.php?id=vaptcha&type=downtime',
                    success: function (token, challenge) {
                        self.isPass = true;
                        sefl.msg && (self.msg.style.display = 'none');
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
                    self.vaptcha = obj;
                    document.getElementById('vaptcha_container_$id').getElementsByClassName('vp-tip')[0].innerHTML = ' 点击验证登录'
                });            
            }
        })
    },
    get: function() {
        var xmlHttp;
        function createxmlHttpRequest() {
            if (window.ActiveXObject) {
                xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
            } else if (window.XMLHttpRequest) {
                xmlHttp = new XMLHttpRequest();
            }
        }
        createxmlHttpRequest();
        xmlHttp.open("GET", './plugin.php?id=vaptcha&type=challenge&t=' + (new Date()).getTime());
        xmlHttp.send(null);
        xmlHttp.onreadystatechange = function(result) {
            if ((xmlHttp.readyState === 4) && (xmlHttp.status === 200)) {
                success && success(); 
            }
        }
    },
    refresh: function() {

    }
}