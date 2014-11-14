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

        this.$download = $('#download');
        this.$download_content = $('#download_content');

    }


    Resume.prototype.addEventListener = function(){
        this.$resume.on('click', {self:this}, function(event){
            var text = event.data.self.$conclusion_text.text();
            if(event.target.className === 'editConclusion' || event.target.className === 'conclusion_text'){
                $(event.target).text('отменить');
                $('.editConclusion').addClass('cancel');
                event.data.self.$conclusion_text.html(
                    "<form action='"+BASE_URL+"/index/conclusion' method='post'>"+
                    "<textarea class='conclusion_textarea' name='conclusion'>"+text+"</textarea>"+
                    "<input class='button_conclusion' type='submit' name='updateConclusion' value='обновить'>"+
                    "</form>");
            }else if(event.target.className != 'conclusion_textarea' && event.target.className != 'button_conclusion'){
                $('.editConclusion').removeClass('editConclusion cancel').addClass('editConclusion');
                $('.editConclusion').html("<img src='"+BASE_URL+"/public/img/edit.png'>редактировать");
                event.data.self.$conclusion_text.text(text);
            }

            event.data.self.$favorite_folds.hide();
            event.data.self.$download_content.hide();

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
            if($.inArray(folders[i]['id'],user_folders)>=0){
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

        self.$ajax_loader.css('visibility', 'visible');
        $.post(BASE_URL + "/index/ajaxfoldersusers", {'ajax': "ajax", 'folders': folders})
            .done(function (data) {
                self.$ajax_loader.css('visibility', 'hidden');
                data = $.parseJSON(data);
                self.getFoldersLI($.parseJSON(data['user_folders']),$.parseJSON(data['folders']));
                self.$favorite_folds.toggle();
            });

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
            if(event.data.self.$favorite_folds.is(':visible')){
                $.post(BASE_URL + "/index/ajaxfoldersusers", {'ajax': "ajax",'all_checkbox':'true'})
                    .done(function (data) {
                        event.data.self.$ajax_loader.css('visibility', 'hidden');
                        data = $.parseJSON(data);
                        event.data.self.getFoldersLI($.parseJSON(data['user_folders']),$.parseJSON(data['folders']));
                    });

            }
            event.stopPropagation();
        });

    };
    Resume.prototype.addEventListenerDownloadContent = function () {
        this.$download_content.on('click', {self: this, id: this.$download_content.data('idUser')}, function (event) {
            var checkbox = {},
                href_export = '/id/' + event.data.id;
            if (event.target.id.toLocaleLowerCase() === 'button') {
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

                $('input[type=checkbox]',$(this)).each(function () {
                    var $self = $(this);
                    if ($self.filter(":checkbox:checked").length !== 0) {
                        $self.prop('checked', false);
                    }
                });
            }
            event.stopPropagation();
        });
    };

    Resume.prototype.addEventListenerDownloadWord = function () {
        this.$download.on('click', {self: this}, function (event) {
            event.data.self.$download_content.toggle();
            event.stopPropagation();
        });
    };

    Resume.prototype.init = function(){
        this.addEventListenerDownloadWord();
        this.addEventListenerDownloadContent()
        this.addEventListener();
        this.addEventListenerFavoriteFolds();
        this.addEventListenerFavorite();
        this.addEventListenerInputText();
    };

    var resume = new Resume();
    resume.init();

})(jQuery,BASE_URL ,window)

