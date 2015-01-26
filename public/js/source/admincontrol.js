(function($, BASE_URL,HBS,window){
    function AdminControl(){
        this.$html = $('html');
        this.$conclusion = $('.conclusion');
        this.$commentAndConclusion = $('.commentAndConclusion');

        this.templateComment = $("#template-comment").html();
        this.$commentBlock = $('.commentBlock');

        this.$addComment = $('.addComment');

        this.conclusion_text;
        this.cache = {};

        this.$download = $('.download');
        this.$download_content = $('.download_content');

        this.$widget_right = $('#widget_right');
        this.$icon_user = $('#icon_user');
        this.$edit_admin = $('#edit_admin');
        this.$form_user_admin = $('#form_user_admin');
        this.$show_input_password = $('#show_input_password');
        this.$password_content = $('#password_content');
        this.$ajax_loader_admin = $('#ajax_loader_admin');
    }

    AdminControl.prototype.resetChecked = function(){
        $('input[type=checkbox]',this.$download_content).each(function () {
            var $self = $(this);
            if ($self.filter(":checkbox:checked").length !== 0) {
                $self.prop('checked', false);
            }
        });
    };

    AdminControl.prototype.conclusionClose = function($element){
        for(var i in  this.cache){
            if(i != $element.parent().parent().data('idConclusion')){
                this.cache[i]['element'].html(
                    "<h1>Заключение <span class='editConclusion'>" +
                    "<img src='"+BASE_URL+"/public/img/edit.png'>редактировать</span>" +
                    "<span class='deleteConclusion'><a class='a_deleteConclusion' href='#'>" +
                    "<img src='"+BASE_URL+"/public/img/delete.png'>удалить</a></span></h1>"+
                    "<div class='conclusion_text'>"+this.cache[i]['text']+"</div>"+
                    "<div class='clear'></div>"
                );
            }
        }
    };

    AdminControl.prototype.addEventListenerBody = function(){
        this.$html.on('click',{self:this},function(event){
            var $elem = $(event.target);
            if($elem.hasClass('exampleClick')){
                $( 'input[type=search]',$elem.parents('.search')).val($elem.text());
            }
            event.data.self.conclusionClose($(event.target));
            event.data.self.$download_content.hide();
            event.data.self.resetChecked();
        });
    };


    AdminControl.prototype.addEventListenerConclusion = function(){
        this.$conclusion.on('click', {self:this}, function(event){
            var $self = $(this);

            if(event.target.className === 'button_conclusion'){
                var $button = $(event.target);
                var $textarea =  $button.siblings(".conclusion_textarea");

                if($textarea.val() === ''){
                    $textarea.css({border:'2px solid red'})
                }else if($textarea.val() !== 0){
                    $.post( BASE_URL+"/admincontrol/conclusion",
                        {
                            updateConclusion: "updateConclusion",
                            conclusion: $textarea.val(),
                            id_user: $textarea.parent().data('idConclusion')
                        }).done(function( data ) {
                            if (data === 'true') {
                                $textarea.parent().html(
                                    "<h1>Заключение <span class='editConclusion'>" +
                                    "<img src='"+BASE_URL+"/public/img/edit.png'>редактировать</span>" +
                                    "<span class='deleteConclusion'>" +
                                    "<a class='a_deleteConclusion' href=''>" + "<img src='"+BASE_URL+"/public/img/delete.png'>удалить</a></span></h1>"+
                                    "<div class='conclusion_text'>"+$textarea.val()+"</div>"+
                                    "<div class='clear'></div>"
                                );
                                $('.download_content',$self.parent()).prepend(
                                    "<p><input type='checkbox' class='without_conclusion' value='conclusion'>Без заключения</p>"
                                );
                            }
                        });
                }
            }else if(event.target.className === 'editConclusion'){
                var $element = $(event.target);
                var $textarea =  $element.parent().siblings(".conclusion_text");
                event.data.self.conclusion_text = $textarea.text();

                event.data.self.cache[$element.parent().parent().data('idConclusion')] = {
                    element:$element.parent().parent(),
                    text: event.data.self.conclusion_text
                };

                event.data.self.conclusionClose($element);

                $element.parent().parent().html(
                    "<h1>Заключение <span class='editConclusion back'>отменить</span><span class='deleteConclusion'><a class='a_deleteConclusion' href='#'><img src='"+BASE_URL+"/public/img/delete.png'>удалить</a></span></h1>"+
                    "<textarea class='conclusion_textarea' name='conclusion'>"+$textarea.text()+"</textarea>"+
                    "<button class='button_conclusion'>обновить</button>"+
                    "<div class='clear'></div>"
                );
            }else if(event.target.className === 'editConclusion back'){
                $(event.target).parent().parent().html(
                    "<h1>Заключение <span class='editConclusion'><img src='"+BASE_URL+"/public/img/edit.png'>редактировать</span><span class='deleteConclusion'><a class='a_deleteConclusion' href='#'><img src='"+BASE_URL+"/public/img/delete.png'>удалить</a></span></h1>"+
                    "<div class='conclusion_text'>"+event.data.self.conclusion_text+"</div>"+
                    "<div class='clear'></div>"
                );
            }else if(event.target.className === 'a_deleteConclusion'){
                var $element = $(event.target);
                $.post( BASE_URL+"/admincontrol/conclusion",
                    {
                        updateConclusion: "updateConclusion",
                        conclusion: '',
                        id_user: $element.parent().parent().parent().data('idConclusion')
                    }).done(function( data ) {
                        if (data === 'true') {
                            $element.parent().parent().parent().html(
                                "<h1>Заключение</h1>"+
                                "<textarea class='conclusion_textarea' name='conclusion'></textarea>"+
                                "<button class='button_conclusion'>сохранить</button>"+
                                "<div class='clear'></div>"
                            );
                            $('.without_conclusion',$self.parent()).parent().remove();
                        }
                    });

            }else{
                $('textarea', event.data.self.$resume).css({border: '1px solid black'});
            }
            event.data.self.$download_content.hide();
            event.preventDefault();
            event.stopPropagation();
        });
    };

    AdminControl.prototype.addEventListenerDownloadWord = function () {
        this.$download.on('click', {self: this}, function (event) {

            var   href_export = '/id/' + $('.download_content',$(this)).data('idUser');

            if(!$('input[type=checkbox]', $('.download_content',$(this))).length){
                location.href = BASE_URL + "/excel/index" + href_export;
                $('.download_content',$(this)).hide();
            }else{
                $('.download_content',$(this)).toggle();
            }
            event.stopPropagation();
        });
    };

    AdminControl.prototype.addEventListenerDownloadContent = function () {
        this.$download_content.on('click', {self: this}, function (event) {
            var checkbox = {},
                href_export = '/id/' + $(this).data('idUser');
            if (event.target.className.toLocaleLowerCase() === 'button') {
                $('input[type=checkbox]',$(this)).each(function () {
                    var $self = $(this);
                    if ($self.filter(":checkbox:checked").length !== 0) {
                        checkbox[$self.val()] = $self.val();
                    }
                });

                for (var i  in checkbox) {
                    if (checkbox.hasOwnProperty(i)) {
                        href_export += "/" + checkbox[i] + "/false";
                    }
                }
                location.href = BASE_URL + "/excel/index" + href_export;
                event.data.self.$download_content.hide();
                event.data.self.resetChecked();
            }
            event.stopPropagation();
        });
    };

    AdminControl.prototype.addEventListenerWidgetRight = function(){
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

    AdminControl.prototype.addEventListenerFIOContent= function() {
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

    AdminControl.prototype.removeWithoutComments = function($userBlock){
        var $commentBlock = $('.commentBlock',$userBlock);
        var $download_content = $('.download_content',$userBlock);
        var $inputWithoutComments = $('input.without_comments[type=checkbox]',$download_content);
        if($.trim($commentBlock.html())){
            if(!$inputWithoutComments.length){
                $download_content.prepend('<p><input type="checkbox" class="without_comments" value="comments">Без комментариев</p>');
            }
        }else{
            $inputWithoutComments.parent('p').remove();
        }

    };


    AdminControl.prototype.buildCommentBlock = function(id, $container){
        var self = this;

        $container.css({
            'background': 'url('+BASE_URL+'/public/img/ajax-loader.gif) 100% 100% no-repeat',
            'background-position': 'center'
        });

        $.post(BASE_URL+'/admincontrol/getcomment', {'id':id} ,function(data){
            var data = $.parseJSON(data);


            HBS.registerHelper('equal', function(lvalue, rvalue, options) {
                if (arguments.length < 3)
                    throw new Error("Handlebars Helper equal needs 2 parameters");
                if( lvalue!=rvalue ) {
                    return options.inverse(this);
                } else {
                    return options.fn(this);
                }
            });


            var template = HBS.compile(self.templateComment);
            var html = template({'object': data});

            $('.commentBlock', $container).html(html);


            $container.css({
                'background': 'white'
            });

            self.removeWithoutComments($container.parents('.user_id'));
        });
    };


    AdminControl.prototype.addEventListenerCommentAndConclusion = function(){
        this.$commentAndConclusion.on('click', {self:this},function(event){
            var $element = $(event.target);
            if($element.hasClass('showComment')){
                var $container = $('.containerComment' ,$element.parents('.user_id'));

                if($container.is(':hidden')){
                    event.data.self.buildCommentBlock($container.data('userId'), $container);
                }
                $container.toggle(100);

                $('.conclusion' ,$element.parents('.user_id')).hide();
            }else if($element.hasClass('showConclusion')){
                $('.containerComment' ,$element.parents('.user_id')).hide();
                $('.conclusion' ,$element.parents('.user_id')).toggle(100);

            }

            return false;
        });
    };
    AdminControl.prototype.addEventListenerCommentBlock = function(){
        this.$commentBlock.on('click', {self:this} ,function(event){

            var $element =  $(event.target);
            $('.inputComment',$element.parent('.commentBlock').siblings('.addComment')).hide();

            if($element.hasClass('closeBlock') && $element.data('idComment')){
                var $block = $element.parents('.containerComment');

                $block.css({
                    'background': 'url('+BASE_URL+'/public/img/ajax-loader.gif) 100% 100% no-repeat',
                    'background-position': 'center'
                });

                $.post(BASE_URL+'/admincontrol/dcomment', {'id':$element.data('idComment')} ,function(data){
                    $element.parents('fieldset').remove();

                    event.data.self.removeWithoutComments($block.parent('.user_id'));

                    $block.css({
                        'background': 'white'
                    });
                });
            }

        });
    };

    AdminControl.prototype.addEventListenerAddComment = function(){

        this.$addComment.on('click', {self:this} ,function(event){
            var $element = $(event.target);
            var self = $(this);
            if($element.hasClass('addSpam')){
                $element.siblings('.inputComment').toggle();
            }else if(event.target.tagName.toLocaleLowerCase() === 'button'){
                var $ele =$('textarea',$element.parent('div').siblings('div'));

                if($ele.val()){
                    $.post(BASE_URL+'/admincontrol/addcomment', {
                        'comment':$ele.val(),
                        'id_user': $element.data('userId')
                    } ,function(data){
                        $('.inputComment',self).hide();
                        $ele.val('');
                        event.data.self.buildCommentBlock($element.data('userId'), self.parent('.containerComment'));
                    });
                }
            }
        });
    };

    AdminControl.prototype.init = function(){

        this.addEventListenerDownloadWord();

        this.addEventListenerWidgetRight();
        this.addEventListenerFIOContent();

        this.addEventListenerCommentBlock();

        this.addEventListenerCommentAndConclusion();

        this.addEventListenerAddComment();

        this.addEventListenerDownloadContent();
        this.addEventListenerConclusion();
        this.addEventListenerBody();
    };

    var resume = new AdminControl();
    resume.init();

})(jQuery,BASE_URL,Handlebars, window)

