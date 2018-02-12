function init(config, groups){
    var app = new Vue({
        el: '#app',
        data: function() {
            return {
                config: config,
                modules: ["\u767b\u5f55", "\u6ce8\u518c", "\u53d1\u5e16", "\u56de\u5e16","\u7f16\u8f91\u5e16","\u5e7f\u64ad","\u65e5\u5fd7","\u7559\u8a00","\u5145\u503c","\u95e8\u6237"," \u627e\u56de\u5bc6\u7801"],
                groups: groups,
                groupsAll: false,
                modulesAll: false
            }
        },
        mounted() {
            for (module in this.config.modules) {
                this.stylefactory(module);
            }
        },
        watch: {
            groupsAll(v) {
                if(v) {
                    var enableGroups = [];
                    for(var i in this.groups) {
                        enableGroups.push(this.groups[i].groupid);
                    }
                    this.config.enableGroups = enableGroups;
                } else {
                    this.config.enableGroups = [];
                }
            },
            modulesAll(v) {
                if(v) {
                    this.config.enableModules = ['1','2','3','4','5','6','7','8','9','10','11'];
                } else {
                    this.config.enableModules = [];
                }
            },
        },
        methods: {
            submitSettig: function() {
                this.appendFormValue('config', this.config);
                $('#configForm').submit();
            },
            appendFormValue(name, value) {
                for(key in value) {
                    var val = value[key];
                    var _name = name + '[' + key + ']'
                    if (typeof val === 'object') {
                        this.appendFormValue(_name, val);
                    } else {
                        var input= $('<input>').attr('type', 'hidden').attr('name', _name);
                        input.val(val);
                        $('#configForm').append(input);
                    }
                }
            },
            setDefaultConfig() {
                this.config.modules = JSON.parse('{"0":{"width":"234","height":"36","color":"57ABFF","label":"\u4eba\u673a\u9a8c\u8bc1:","style":"dark","type":"float","required":true},"1":{"width":"209","height":"36","color":"57ABFF","label":"\u4eba\u673a\u9a8c\u8bc1:","style":"dark","type":"float","required":false},"2":{"width":"310","height":"36","color":"57ABFF","label":"","style":"dark","type":"float","required":false},"3":{"width":"306","height":"36","color":"57ABFF","label":"","style":"dark","type":"float","required":false},"6":{"width":"306","height":"36","color":"57ABFF","label":"","style":"dark","type":"float","required":false},"7":{"width":"310","height":"36","color":"57ABFF","label":"","style":"dark","type":"float","required":false},"8":{"width":"234","height":"36","color":"57ABFF","label":"","style":"dark","type":"float","required":false}}');
                for (module in this.config.modules) {
                    this.stylefactory(module);                
                }
            },
            stylefactory: function(module) {
                var self = this;
                var _$ = function(selector) {
                    return $('.' + module + ' ' + selector);
                }
                _$('.picker').colpick({
                    layout:'hex',
                    submit:0,
                    colorScheme:'dark',
                    onChange:function(hsb,hex,rgb,el,bySetColor) {
                        $(el).css('border-color','#'+hex);
                        _$('.vp-light-btn.vp-default-btn .vp-shield')[0].style.backgroundColor = '#' + hex;
                        _$('.vp-light-btn.vp-default-btn .vp-about')[0].style.backgroundColor = '#' + hex;
                        _$('.vp-dark-btn.vp-default-btn')[0].style.backgroundColor = '#' + hex;
                        _$('.vp-light-btn .vp-circle-h')[0].style.borderColor = '#' + hex;
                        if(!bySetColor) self.config.modules[module].color = hex;
                    }
                }).keyup(function(){
                    $(this).colpickSetColor(this.value);
                }).colpickSetColor(self.config.modules[module].color);
            },
            limitNumber(target, key, minValue, maxValue) {
                var v = target[key];
                !Number(v) && (v = parseInt(v) ? parseInt(v) : '');
                minValue && v < minValue && (v = minValue);
                maxValue && v > maxValue && (v = maxValue);
                target[key] = v;
            }
        },
    })
}

