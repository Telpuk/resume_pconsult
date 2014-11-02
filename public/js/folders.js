(function($, BASE_URL,window){
    function Folders(){
        this.$html = $('html');
        this.$addFolder = $('#addFolder');
        this.$input_new_folder_block = $('#input_new_folder_block');
        this.$inout_text = $('#new_folder');
        this.$ajax_loader = $('.ajax_loader');
        this.$folders_list_li = $('#folders_list_li');
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
            i=0;
        for(i = 0, len = folders.length; i<len; ++i){
            li += '<li>' +
            '<a href="'+BASE_URL+'/admincontrol/folders/delete/'+folders[i]['id']+'" ><img src="'+BASE_URL+'/public/img/folder_remove.png"></a>'+
            '<a href="'+BASE_URL+'/admincontrol/folders/id/'+folders[i]['id']+'" >'+folders[i]['name']+'</a>' +
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



    Folders.prototype.init = function(){
        this.addEventListenerHtml();
        this.addEventListenerAddFolder();
        this.addEventListenerInputNewFolderBlock();

    };

    var folders = new Folders();
    folders.init();

})(jQuery,BASE_URL ,window)

