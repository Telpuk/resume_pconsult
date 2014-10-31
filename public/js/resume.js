(function($, BASE_URL,window){
    function Resume(){
        this.$resume = $('.resume');
        this.$favorite = $('.favorite');
        this.$favorite_folds = $('#favorite_folds');
        this.$input_text= $('#input_text');
        this.$new_checkbox = $('#new_checkbox');
        this.$checkbox_list_ul = $('#checkbox_list');
        this.$ajax_loader = $('#ajax_loader');
        this.$conclusion_text = $('.conclusion_text');
    }


    Resume.prototype.addEventListener = function(){
        this.$resume.on('click', {self:this}, function(event){
            //var text = event.data.self.$conclusion_text.text();
            //if(event.target.className === 'editConclusion' || event.target.className === 'conclusion_text'){
            //    $(event.target).text('отменить');
            //    $('.editConclusion').addClass('cancel');
            //    event.data.self.$conclusion_text.html(
            //        "<form action='"+BASE_URL+"/index/conclusion' method='post'>"+
            //        "<textarea class='conclusion_textarea' name='conclusion'>"+text+"</textarea>"+
            //        "<input class='button_conclusion' type='submit' name='updateConclusion' value='обновить'>"+
            //        "</form>");
            //}else if(event.target.className != 'conclusion_textarea' && event.target.className != 'button_conclusion'){
            //    $('.editConclusion').removeClass('editConclusion cancel').addClass('editConclusion');
            //    $('.editConclusion').html("<img src='"+BASE_URL+"/public/img/edit.png'>редактировать");
            //    event.data.self.$conclusion_text.text(text);
            //}

            //event.data.self.$favorite_folds.toggle();
            event.data.self.$favorite_folds.hide();

        });
    };
    Resume.prototype.addEventListenerInputText = function(){
        this.$input_text.on('keydown change blur', {self:this}, function(event){
            if($.trim(event.data.self.$input_text.val()) === ''){
                event.data.self.$new_checkbox.prop('checked', false);
            }else{
                event.data.self.$new_checkbox.prop('checked', true);
            }
            event.stopPropagation();
        })
    };

    Resume.prototype.getFoldersLI = function(user_folders,folders){
        var li='',
            checked = '';
        for(var i in folders) {
            checked = '';
            if(user_folders[folders[i]['id']] == folders[i]['id']){
                checked = 'checked'
            }
            li += '<li><input type="checkbox" value="'+folders[i]['id']+'" '+checked+'>'+folders[i]['name']+'</li>';
        }
        this.$checkbox_list_ul.html(li);
        this.$input_text.val('');
        this.$new_checkbox.prop( "checked", false);
    };


    Resume.prototype.ajaxQuery = function(){
        var folders = {},
            user_folders,
            folders,
            self = this;
        $('#favorite_folds input[type=checkbox]').each(function() {
            var $self = $(this);
            if($self.filter(":checkbox:checked").length  !== 0){
                folders[$self.val()] = $self.val();
            }

            if(folders['new_folder']){
                folders['new_folder'] = self.$input_text.val();
            }

        });
        if(Object.keys( folders ).length !== 0){
            self.$ajax_loader.css('visibility', 'visible');
            $.post(BASE_URL + "/admincontrol/ajaxfolders", {ajax: "ajax", folders: folders})
                .done(function (data) {
                    self.$ajax_loader.css('visibility', 'hidden');
                    data = $.parseJSON(data);
                    user_folders = $.parseJSON(data['user_folders']);
                    folders = $.parseJSON(data['folders'])
                    self.getFoldersLI(user_folders,folders);
                });
        }
    };

    Resume.prototype.addEventListenerFavoriteFolds = function(){
        this.$favorite_folds.on('click', {self:this}, function(event){
            if(event.target.id === 'button'){
                event.data.self.ajaxQuery();
            }
            event.stopPropagation();
        })
    };
    Resume.prototype.addEventListenerFavorite = function(){
        this.$favorite.on('click', {self:this}, function(event){
            event.data.self.$favorite_folds.toggle();
            event.stopPropagation();
        });

    };

    Resume.prototype.init = function(){
        this.addEventListener();
        this.addEventListenerFavoriteFolds();
        this.addEventListenerFavorite();
        this.addEventListenerInputText();
    };

    var resume = new Resume();
    resume.init();

})(jQuery,BASE_URL ,window)

