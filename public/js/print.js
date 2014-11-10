(function($, BASE_URL,window){
    function Print(){
        this.$checkbox_print = $('.checkbox_print');
        this.$conclusion = $('#conclusion');
        this.$comments = $('#comments');
        $('.page-wrapper').hide();
    }

    Print.prototype.init = function(){
        this.$checkbox_print.on('click',{self:this},function(event){
            switch (event.target.id.toLocaleLowerCase()){
                case 'without_comments':{
                    event.data.self.$comments.toggle();
                    break;
                }
                case 'without_conclusion':{
                    event.data.self.$conclusion.toggle();
                    break;
                }
                case 'button':{
                    event.data.self.$checkbox_print.toggle();
                    window.print();
                    event.data.self.$checkbox_print.toggle();
                    break;
                }
            }
        });
    };

    var print = new Print();
    print.init();

})(jQuery,BASE_URL ,window)

