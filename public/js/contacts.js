(function($, window){
    function Personal(){
    }

    Personal.prototype.validateForm = function(){
        $("#personal_contacts").validate({
            rules:{
                email:{
                    required: true,
                    email: true
                }
            },
            messages:{
                email:{
                    required: "Это поле обязательно для заполнения",
                    email: 'Не коректный email'
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

