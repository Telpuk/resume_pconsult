(function($, window){
    function Manager(){
        this.$add_manager = $('#add_manager');
        this.$form_manager = $('.form_manager');

        this.$widget_right = $('#widget_right');
        this.$edit_admin = $('#edit_admin');
        this.$icon_user = $('#icon_user');
        this.$form_user_admin = $('#form_user_admin');
        this.$show_input_password = $('#show_input_password');
        this.$password_content = $('#password_content');
        this.$ajax_loader_admin = $('#ajax_loader_admin');
    }

    Manager.prototype.validateForm = function(){
        $("#manager_form").validate({
            rules:{
                name_first:{
                    required: true
                },
                login_manager:{
                    required: true
                }
            },
            messages:{
                name_first:{
                    required: "Это поле обязательно для заполнения"
                },
                login_manager:{
                    required: "Это поле обязательно для заполнения"
                }
            }
        });
    };

    Manager.prototype.createPassword = function() {
        $.fn.passwordStrength = function (options) {
            return this.each(function () {
                var that = this;
                that.opts = {};
                that.opts = $.extend({}, $.fn.passwordStrength.defaults, options);

                that.div = $(that.opts.targetDiv);
                that.defaultClass = that.div.attr('class');

                that.percents = (that.opts.classes.length) ? 100 / that.opts.classes.length : 100;

                v = $(this)
                    .keyup(function () {
                        if (typeof el == "undefined")
                            this.el = $(this);
                        var s = getPasswordStrength(this.value);
                        var p = this.percents;
                        var t = Math.floor(s / p);

                        if (100 <= s)
                            t = this.opts.classes.length - 1;

                        this.div
                            .removeAttr('class')
                            .addClass(this.defaultClass)
                            .addClass(this.opts.classes[t]);

                    })
                    .after('<a class="generate_password" href="#">Создать пароль</a>')
                    .next()
                    .click(function () {
                        $(this).prev().val(randomPassword()).trigger('keyup');
                        return false;
                    });
            });

            function getPasswordStrength(H) {
                var D = (H.length);
                if (D > 5) {
                    D = 5
                }
                var F = H.replace(/[0-9]/g, "");
                var G = (H.length - F.length);
                if (G > 3) {
                    G = 3
                }
                var A = H.replace(/\W/g, "");
                var C = (H.length - A.length);
                if (C > 3) {
                    C = 3
                }
                var B = H.replace(/[A-Z]/g, "");
                var I = (H.length - B.length);
                if (I > 3) {
                    I = 3
                }
                var E = ((D * 10) - 20) + (G * 10) + (C * 15) + (I * 10);
                if (E < 0) {
                    E = 0
                }
                if (E > 100) {
                    E = 100
                }
                return E
            }

            function randomPassword() {
                var chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$_+";
                var size = 10;
                var i = 1;
                var ret = ""
                while (i <= size) {
                    $max = chars.length - 1;
                    $num = Math.floor(Math.random() * $max);
                    $temp = chars.substr($num, 1);
                    ret += $temp;
                    i++;
                }
                return ret;
            }

        };

        $.fn.passwordStrength.defaults = {
            classes: Array('is10', 'is20', 'is30', 'is40', 'is50', 'is60', 'is70', 'is80', 'is90', 'is100'),
            targetDiv: '#passwordStrengthDiv',
            cache: {}
        }
        $(document)
            .ready(function () {
                $('input[name="password_manager"]').passwordStrength({
                    targetDiv: '#passwordStrengthDiv2',
                    classes: Array('is10', 'is20', 'is30', 'is40')
                });

            });
    }

    Manager.prototype.addEventListenerManager = function(){
       this.$add_manager.on('click', {self:this},function(event){
            event.data.self.$form_manager.slideToggle();
       });
    };

    Manager.prototype.addEventListenerWidgetRight = function(){
        var self = this;

        this.$icon_user.on('click', function() {
            self.$widget_right.toggleClass('open');
            if(self.$widget_right.hasClass('open')){
                self.$widget_right.stop().animate(
                    {
                        'margin-right': '0px'
                    },
                    500,
                    'easeInSine');
            }else{
                self.$form_user_admin.fadeOut();
                self.$widget_right.stop().animate(
                    {
                        'margin-right': '-300px'
                    },
                    1500,
                    'easeOutBounce'
                );
            }

        } );

        this.$edit_admin.on('click',{self:this} ,function(event) {
            event.data.self.$form_user_admin.slideToggle();

            $('input[type=password]',event.data.self.$password_content).each(function(id,el){
                $(el).val('');
            });

        });
        this.$show_input_password.on('click',{self:this} ,function(event) {
            event.data.self.$password_content.slideToggle();
            $('input[type=password]',event.data.self.$password_content).each(function(id,el){
                $(el).val('');
                $(el).css({'border' : '1px solid black'});
            });
        });

    };

    Manager.prototype.addEventListenerFIOContent= function() {
        this.$form_user_admin.on('submit change',{self:this} ,function (event) {
            var $name_first = $('#name_first' ,$(this));
            var $login = $('#login' ,$(this));
            var $name_second = $('#name_second' ,$(this));
            var $patronymic = $('#patronymic' ,$(this));
            var $password= $('#password_content input[type=password]' ,$(this));

            if($name_first.val() == ''){
                $('.valid' ,$name_first.parent()).remove();
                $name_first.css({'border' : '1px solid #ff0000'});
                $name_first.parent().append('<span class="valid">Обязательно для заполнения</span>').css({color:'red'});
                return false;
            }else{
                $name_first.css({'border' : '1px solid black'})
                $('.valid' ,$name_first.parent()).remove();
            }

            if($login.val() != '') {
                var pattern = /^([a-z0-9_\.-])+@[a-z0-9-]+\.([a-z]{2,4}\.)?[a-z]{2,4}$/i;
                if(!pattern.test($login.val())){
                    $('.valid' ,$login.parent()).remove();
                    $login.css({'border' : '1px solid #ff0000'});
                    $login.parent().append('<span  class="valid">Проверьте правильность ввода!</span>');
                    return false;
                } else{
                    $login.css({'border' : '1px solid black'});
                    $('.valid' ,$login.parent()).remove();
                }
            } else {
                $login.css({'border' : '1px solid black'});
                $('.valid' ,$login.parent()).remove();
                $login.parent().append('<span  class="valid">Поле email не должно быть пустым</span>');
                return false;
            }

            if($($password[0]).val()){
                var strongRegex = new RegExp("^(?=.{7,})(((?=.*[A-Z])(?=.*[a-z]))|((?=.*[A-Z])(?=.*[0-9]))|((?=.*[a-z])(?=.*[0-9]))).*$", "g");

                $('.valid' , event.data.self.$password_content).remove();
                $($password[0]).css({'border' : '1px solid black'});
                $($password[1]).css({'border' : '1px solid black'});

                if(!strongRegex.test($($password[0]).val())) {
                    $($password[0]).css({'border' : '1px solid red'});
                    event.data.self.$password_content.append('<p><span class="valid">Слабый пароль</span></p>');
                    return false;
                }
            }
            if($($password[0]).val() && !$($password[1]).val()){
                $('.valid' , event.data.self.$password_content).remove();
                $($password[0]).css({'border' : '1px solid red'});
                $($password[1]).css({'border' : '1px solid red'});
                event.data.self.$password_content.append('<p><span  class="valid">Повторите пароль</span></p>');
                return false
            }
            if($($password[0]).val() && $($password[1]).val()){
                if($($password[0]).val() !== $($password[1]).val()){
                    $($password[0]).css({'border' : '1px solid red'});
                    $($password[1]).css({'border' : '1px solid red'});
                    event.data.self.$password_content.append('<p><span class="valid">Пароли не совпадают</span></p>');
                    return false;
                }
            }

            if(event.target.tagName.toLocaleLowerCase() === 'form'){

                event.data.self.$ajax_loader_admin.show();

                $.post(BASE_URL + "/admincontrol/upadminajax", {
                    ajax: "true", 'admin_info': {
                        'login': $login.val(),
                        'name_first': $name_first.val(),
                        'patronymic': $patronymic.val(),
                        'name_second': $name_second.val(),
                        'password': $($password[0]).val()

                    }
                }).done(function (data) {
                    event.data.self.$ajax_loader_admin.hide();
                });
            }

            return false;

        });
    };

    Manager.prototype.init = function(){
        this.createPassword();
        this.addEventListenerWidgetRight();
        this.addEventListenerFIOContent();
        this.addEventListenerManager();
        this.validateForm();
    };

    var manager = new Manager();
    manager.init();

})(jQuery, window)
