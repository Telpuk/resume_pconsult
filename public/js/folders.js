(function($, BASE_URL,window){
    function Folders(){
        this.$html = $('body');
        this.$addFolder = $('#addFolder');
        this.$input_new_folder_block = $('#input_new_folder_block');
        this.$inout_text = $('#new_folder');
        this.$ajax_loader = $('.ajax_loader');
        this.$folders_list_li = $('#folders_list_li');
        this.lastActive = 0;
        this.$resume = $('.conclusion');
        this.conclusion_text;
        this.cache = {};
    }

    Folders.prototype.addEventListenerHtml = function(){
        this.$html.on('click',{self:this},function(event){
            event.data.self.$input_new_folder_block.hide();
            event.data.self.$addFolder.show();
            event.data.self.$inout_text.val('');
        });
    };

    Folders.prototype.addEventListenerAddFolder = function(){
        this.$addFolder.on('click',{self:this},function(event){
            event.data.self.$input_new_folder_block.show();
            $(this).hide();
            event.stopPropagation();
        });
    };
    Folders.prototype.buildFoldersLI = function(folders){
        var li='',
            len=0,
            i= 0,
            active;
        for(i = 0, len = folders.length; i<len; ++i){
            active = '';
            if(this.lastActive == folders[i]['id']){
                active =  "class='folders_active'";
            }
            li += '<li>' +
            '<a href="'+BASE_URL+'/admincontrol/folders/delete/'+folders[i]['id']+'"><img src="'+BASE_URL+'/public/img/folder_remove.png" title="удалить"></a>'+
            '<a '+active+' data-list-id="'+folders[i]['id']+'" href="'+BASE_URL+'/admincontrol/folders/id/'+folders[i]['id']+'" >'+folders[i]['name']+'</a>' +
            '</li>';
        }
        this.$folders_list_li.html(li);
    };

    Folders.prototype.ajaxQuestion = function(text){
        var self = this;
        $.post(BASE_URL + "/admincontrol/ajaxfolders", {'ajax': "ajax", 'folder_name': text})
            .done(function (data) {
                self.$ajax_loader.hide();
                self.$addFolder.show();
                self.$input_new_folder_block.hide();
                self.$inout_text.val('');
                data = $.parseJSON(data);
                self.buildFoldersLI(data);
            });
    };

    Folders.prototype.addEventListenerInputNewFolderBlock = function(){
        this.$input_new_folder_block.on('click',{self:this},function(event){

            switch (event.target.id){
                case 'button':{
                    var text = event.data.self.$inout_text.val();
                    if($.trim(text) !== '') {
                        event.data.self.$ajax_loader.show();
                        event.data.self.ajaxQuestion(text);
                        event.data.self.lastActive =  $('a',event.data.self.$folders_list_li).filter( '.folders_active').data('listId');
                    }
                    event.stopPropagation();
                    break;
                }
                case 'new_folder':{
                    event.stopPropagation();
                    break
                }
            }


        });
    };


    Folders.prototype.addEventListenerConclusion = function(){
        this.$resume.on('click', {self:this}, function(event){

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
                                    "<h1>Заключение <span class='editConclusion'><img src='"+BASE_URL+"/public/img/edit.png'>редактировать</span><span class='deleteConclusion'><a class='a_deleteConclusion' href='#'><img src='"+BASE_URL+"/public/img/delete.png'>удалить</a></span></h1>"+
                                    "<div class='conclusion_text'>"+$textarea.val()+"</div>"+
                                    "<div class='clear'></div>"
                                );
                            }
                        })
                }
            }else if(event.target.className === 'editConclusion'){
                var $element = $(event.target);
                var $textarea =  $element.parent().siblings(".conclusion_text");
                event.data.self.conclusion_text = $textarea.text();

                event.data.self.cache[$element.parent().parent().data('idConclusion')] = {
                    element:$element.parent().parent(),
                    text: event.data.self.conclusion_text
                };

                for(var i in  event.data.self.cache){
                    if(i != $element.parent().parent().data('idConclusion')){
                        event.data.self.cache[i]['element'].html(
                            "<h1>Заключение <span class='editConclusion'>" +
                            "<img src='"+BASE_URL+"/public/img/edit.png'>редактировать</span>" +
                            "<span class='deleteConclusion'><a class='a_deleteConclusion' href='#'>" +
                            "<img src='"+BASE_URL+"/public/img/delete.png'>удалить</a></span></h1>"+
                            "<div class='conclusion_text'>"+event.data.self.cache[i]['text']+"</div>"+
                            "<div class='clear'></div>"
                        );
                    }
                }

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
                        }
                    })

            }else{
                $('textarea', event.data.self.$resume).css({border: '1px solid black'});
            }


        });
    }



    Folders.prototype.init = function(){
        this.addEventListenerHtml();
        this.addEventListenerAddFolder();
        this.addEventListenerConclusion();
        this.addEventListenerInputNewFolderBlock();

    };

    var folders = new Folders();
    folders.init();

})(jQuery,BASE_URL ,window)

