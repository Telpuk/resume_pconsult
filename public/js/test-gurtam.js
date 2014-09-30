(function($, window,HOST){
    function App(){
        this.$wrapper = $('.wrapper');
        this.$black = $('.black', this.$wrapper);
        this.$sidebar = $('.sidebar', this.$wrapper);
        this.$figure = $('.figure', this.$wrapper);
        this.pictureGuid;
    }


    App.prototype.deletePicture = function(){
        var self = this;
        var $parent = $(self.pictureGuid).parent().parent();
        $.ajax({
            type: "GET",
            url: self.pictureGuid.href,
            statusCode:{
                404:function(){
                    self.$sidebar.html(
                        '<div class="message">' +
                            '<div class="message_er">'+
                            '<p class="error">Произошла ошибка, не возможно удалить файл!</p>'+
                            '</div>' +
                            '</div>'
                    );
                }
            },

            success: function(){
                $parent.remove();
                self.$black.hide();
                self.$sidebar.html(
                    '<div class="message">' +
                        '<div class="message_suc">'+
                        '<p class="successfully">Файл успешно удален!</p>'+
                        '</div>'+
                        '</div>'
                );
            }
        });
    };


    App.prototype.viewPicture = function(){
        var url  = $(this.pictureGuid).data('guid');
        var self = this;
        $.ajax({
            type: "GET",
            url: url+"/ajax/true",
            dataType: 'html',
            statusCode:{
                404:function(){
                    self.$sidebar.html(
                        '<div class="message">' +
                            '<div class="message_er">'+
                            '<p class="error">Произошла ошибка, не возможно отобразить файл файл!</p>'+
                            '</div>' +
                            '</div>'
                    );
                }
            },
            success: function(data){
                self.$figure.html(data);
            }
        });
    };

    App.prototype.addEventListenerWrapper = function(){

        setInterval(function(){
            var $message =  $('.message');
            if($message.length === 1){
                $message.animate({ height: 'hide', opacity: 'hide' }, 'slow');
            }
        }, 7000);

        this.$wrapper.on('click', {self:this}, function(event){
            if(event.target.className === 'delete' && event.target.tagName.toLowerCase() === 'a'){
                event.data.self.pictureGuid = event.target;
                event.data.self.$black.show();
                event.data.self.$figure.empty();
                event.preventDefault();
            }else if(event.target.className === 'view' && event.target.tagName.toLowerCase() === 'a'){
                event.data.self.pictureGuid = event.target;
                event.data.self.viewPicture();
                event.preventDefault();
            }else if(event.target.className === 'deleteButton' && event.target.tagName.toLowerCase() === 'h1'){
                event.data.self.deletePicture();
            }else if(event.target.className === 'cancel' && event.target.tagName.toLowerCase() === 'h1'){
                event.data.self.$black.hide();
            }
        });
    };

    App.prototype.clearUrl = function(){
        window.history.pushState(null, null, HOST);
    };

    App.prototype.init = function(){
        this.clearUrl();
        this.addEventListenerWrapper();
    };


    var app = new App();
    app.init();

})(jQuery, window,HOST)