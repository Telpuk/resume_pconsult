(function($, BASE_URL,window){
    function Resume(){
        this.$resume = $('.resume');
        this.$conclusion_text = $('.conclusion_text');
    }


    Resume.prototype.addEventListener = function(){
        this.$resume.on('click', {self:this}, function(event){
            var text = event.data.self.$conclusion_text.text();
            console.log(event.target);
            if(event.target.className === 'editConclusion'){
                $(event.target).text('отменить');
                $('.editConclusion').addClass('cancel');
                event.data.self.$conclusion_text.html(
                    "<form id='conclusion_form' action='"+BASE_URL+"/index/conclusion' method='post'>"+
                    "<textarea name='conclusion'>"+text+"</textarea>"+
                    "<input type='submit' name='updateConclusion' value='обновить'>"+
                    "</form>");
            }else if(event.target.className === 'editConclusion cancel'){
                $('.editConclusion').removeClass('editConclusion cancel').addClass('editConclusion');
                $('.editConclusion').html("<img src='"+BASE_URL+"/public/img/edit.png'>редактировать");
                event.data.self.$conclusion_text.text(text);
            }
        });
    }
    Resume.prototype.init = function(){
        this.addEventListener();
    };

    var resume = new Resume();
    resume.init();

})(jQuery,BASE_URL ,window)

