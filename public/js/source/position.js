(function($, window){

    function Position(){
        this.$desired_position = $('#desired_position');
    }

    Position.prototype.validateForm = function(){
        $("#position_form").validate({
            rules:{
                desired_position:{
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
                employment:{
                    required: "Это поле обязательно для заполнения"
                }
            }
        });
    };
    Position.prototype.messageColor = function(){
        $('div.message').filter(function(){
            return $(this).text()
        }).parent().css({
            'border':'1px red solid',
            'padding':'20px',
            'marginBottom': '30px',
            'color':'red'
        });

    };

    Position.prototype.autocompCareerObjective = function(data){
        this.$desired_position.autocomplete({
            source: data
        });
    };

    Position.prototype.autocompletePost = function(){
        var self = this;
        $.post( BASE_URL+"/side/aucposition",
            { autocomplete: "autocomplete"})
            .done(function( data ) {
                data = JSON.parse(data);
                self.autocompCareerObjective(data);
            });
    };

    Position.prototype.init = function(){
        this.autocompletePost();
        this.messageColor();
        this.validateForm();
    };

    var position = new Position();
    position.init();

})(jQuery, window)

