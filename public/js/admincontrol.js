(function($, BASE_URL,window){
    function Resume(){
        this.$html = $('html');
        this.$conclusion = $('.conclusion');
        this.conclusion_text;
        this.cache = {};
    }

    Resume.prototype.conclusionClose = function($element){
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

    Resume.prototype.addEventListenerBody = function(){
        this.$html.on('click',{self:this},function(event){
            event.data.self.conclusionClose($(event.target));
        });
    };



    Resume.prototype.addEventListenerConclusion = function(){
        this.$conclusion.on('click', {self:this}, function(event){

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
                        }
                    });
            }else{
                $('textarea', event.data.self.$resume).css({border: '1px solid black'});
            }
            event.preventDefault();
            event.stopPropagation();
        });
    };

    Resume.prototype.init = function(){
        this.addEventListenerConclusion();
        this.addEventListenerBody();
    };

    var resume = new Resume();
    resume.init();

})(jQuery,BASE_URL ,window)

