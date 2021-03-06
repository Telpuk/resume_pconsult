(function($,BASE_URL ,window){
    function Personal(){
        this.$personal = $('.personal.contacts');
        this.$personal_contacts = $('#personal_contacts');
        this.$add = $('#add', this.$personal_contacts);
        this.$add_connect_user = $('#add_connect_user', this.$personal_contacts);
        this.$connect_list = $('#connect', this.$personal_contacts);

        this.$mobilePhone = $('#mobile_phone', this.$personal_contacts);
        this.$addMobilePhoneTd = $('#add_mobile_phone_td', this.$personal_contacts);

        this.connectLabelFull = {
            skype: "Skype",
            icq:"ICQ",
            free_lance:"Free-lance",
            my_circle:"Мой круг",
            linkedln:"Linkedln",
            facebook: "Facebook",
            live_journal:"LiveJournal",
            other_site:"Другой сайт"
        }

        this.connectLabel = {}
    }

    Personal.prototype.setConnectionLabel = function(){
        var self = this;
        $('p[data-connect]',this.$add_connect_user).each(function(o,element){
            if($(element).data('connect') in self.connectLabelFull){
                self.connectLabel[$(element).data('connect')] = self.connectLabelFull[$(element).data('connect')];
            }
        });
    };

    Personal.prototype.validateForm = function(){
        $("#personal_contacts").validate({
            rules:{
                email:{
                    required: true,
                    email: true
                }
            },
            messages:{
                email:{
                    required: "Это поле обязательно для заполнения",
                    email: 'Не коректный email'
                }
            }
        });
    };
    Personal.prototype.messageColor = function(){
        $('span.required',this.$personal_contacts).filter(function(){
            return $(this).text();
        }).parent().parent().find("input[type=tel]").css('border', '1px red solid');
    };
    Personal.prototype.addEventListenerAddConnect = function(){
        this.$add.on('click',{self:this},function(event){
            event.data.self.$connect_list.slideToggle(200);
            event.stopPropagation();
        });

    };


    Personal.prototype.addEventListenerConnect = function(){
        this.$personal.on('click',{self:this},function(event){
            event.data.self.$connect_list.hide();
        });
    };
    Personal.prototype.addEventListenerList = function(){
        this.$connect_list.on('click',{self:this},function(event){
            var name = $(event.target).data('connect');

            if(event.data.self.connectLabel[name]) {
                $(event.target).remove();
                var source = $("#contacts_list").html();

                var template = Handlebars.compile(source);
                var html = template({'name': name, 'label': event.data.self.connectLabel[name], 'BASE_URL': BASE_URL});
                event.data.self.$add_connect_user.parent().before(html);
                delete event.data.self.connectLabel[name];
                if(Object.keys(event.data.self.connectLabel).length === 0){
                    event.data.self.$add_connect_user.remove();
                }
            }else{
                event.stopPropagation();
            }
        });
    };
    Personal.prototype.maskedInputInit = function(){
        $("#mobile_phone").mask("375 (99) 999-99-99");
        $("#work_phone").mask("375 (99) 999-99-99");
        $("#mobile_phone_other").mask("375 (99) 999-99-99");
        $("#home_phone").mask("375 (99) 999-99-99");
    };

    Personal.prototype.addEventListenerMobilePhone = function(){

        if(this.$mobilePhone.val()){
            this.$addMobilePhoneTd.show();
        }

        this.$mobilePhone.on('keydown change blur',{self:this},function(event){
            var $inputPhone = $(this);

            if( $inputPhone.val() && $inputPhone.val() !== '375 (__) ___-__-__' && !(/_/.test($inputPhone.val()))){
                event.data.self.$addMobilePhoneTd.fadeIn(500);
            }else{
                event.data.self.$addMobilePhoneTd.hide();
                $('tr[data-mobile-phone="delete"]').remove();
                event.data.self.$addMobilePhoneTd.removeClass('delete_phone');
                $('#add_mobile_phone',event.data.self.$addMobilePhoneTd).html('&nbsp;&nbsp;<img src="'+BASE_URL+'/public/img/add.png">&nbsp;&nbsp;Дополнительный&nbsp;мобильный&nbsp;телефон').css({color: 'green'});
            }
        });
    };

    Personal.prototype.addEventListenerMobilePhoneTd = function(){

        this.$addMobilePhoneTd.on('click',{self:this},function(event){
            if(!($(this).hasClass('delete_phone'))) {
                var source = $("#tr-template-mobilePhone").html();
                var template = Handlebars.compile(source);
                var html = template({'BASE_URL': BASE_URL});
                $(this).after(html);
                $("#mobile_phone_other").mask("375 (99) 999-99-99");
                $(this).addClass('delete_phone');
                $('#add_mobile_phone',$(this)).html('&nbsp;&nbsp;<img src="'+BASE_URL+'/public/img/delete.png">&nbsp;&nbsp;Удалить&nbsp;мобильный&nbsp;телефон').css({color: 'red'});
            }else{
                $('tr[data-mobile-phone="delete"]').remove();
                $(this).removeClass('delete_phone');
                $('#add_mobile_phone',$(this)).html('&nbsp;&nbsp;<img src="'+BASE_URL+'/public/img/add.png">&nbsp;&nbsp;Дополнительный&nbsp;мобильный&nbsp;телефон').css({color: 'green'});
            }
        });
    };


    Personal.prototype.init = function(){
        this.maskedInputInit();
        this.setConnectionLabel();
        this.addEventListenerMobilePhone();
        this.addEventListenerMobilePhoneTd();
        this.validateForm();
        this.addEventListenerConnect();
        this.addEventListenerAddConnect();
        this.addEventListenerList();
        this.messageColor();

    };

    var personal = new Personal();
    personal.init();

})(jQuery,BASE_URL ,window)

