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

    Personal.prototype.init = function(){
        this.validateForm();
    };


    var personal = new Personal();
    personal.init();

})(jQuery, window)

