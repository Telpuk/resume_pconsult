(function($, window){
    function Personal(){
    }

    Personal.prototype.validateForm = function(){
        $("#position_form").validate({
            rules:{
                desired_position:{
                    required: true
                },

                professional_area:{
                    required: true
                },

                employment:{
                    required: true
                }
            },
            messages:{

                desired_position:{
                    required: "Это поле обязательно для заполнения"
                },

                professional_area:{
                    required: "Это поле обязательно для заполнения"
                },

                employment:{
                    required: "Это поле обязательно для заполнения"
                }
            }
        });
    };
    Personal.prototype.messageColor = function(){

        console.log(  $('div.message').filter(function(){
            return $(this).text()
        }));
        $('div.message').filter(function(){
            return $(this).text()
        }).parent().css({
            'border':'1px red solid',
            'padding':'20px',
            'marginBottom': '30px',
            'color':'red'
        });


    };



    Personal.prototype.init = function(){
        this.messageColor();
        this.validateForm();
    };





    var personal = new Personal();
    personal.init();

})(jQuery, window)

