(function($, BASE_URL ,window){
    function Personal(){

        this.$day_birth = $('#day_birth');
        this.$month_birth = $('#month_birth');
        this.$year_birth = $('#year_birth');

        this.day = this.$day_birth.val();
        this.month =  this.$month_birth.val();
        this.year =  this.$year_birth.val();

        this.$wrapper = $('.personal');
        this.$submitPersonal = $('#submitPersonal',this.$wrapper);
        this.$error_data = $('.data', this.$wrapper);

        this.$city = $('#city');
        this.$nationality = $('#nationality');
        this.$work_permit = $('#work_permit');

    }

    Personal.prototype.validateForm = function(){
        $("#personal_form").validate({
            rules:{
                surname:{
                    required: true
                },

                first_name:{
                    required: true
                },

                city:{
                    required: true
                }
            },
            messages:{

                surname:{
                    required: "Это поле обязательно для заполнения"
                },

                first_name:{
                    required: "Это поле обязательно для заполнения"
                },

                city:{
                    required: "Это поле обязательно для заполнения"
                }
            }
        });
    };

    Personal.prototype.birth = function(){
        var d = new Date();
        if( this.day && this.month && this.year){
            this.$error_data.text("года("+ (d.getFullYear()-this.year)+" лет)").css({visibility: 'visible', color: 'black'});
            $('.data_f').css({visibility: 'hidden'});
            this.$submitPersonal.attr('disabled', false);
        }else if(!this.day && !this.month && !this.year){
            $('.data_f').css({visibility: 'hidden'});
            this.$submitPersonal.attr('disabled', false);
            this.$error_data.css({visibility: 'hidden'});
        }else if(!this.day || !this.month || !this.year){
            $('.data_f').css({visibility: 'visible'});
            this.$submitPersonal.attr('disabled', 'disabled');
            this.$error_data.text('Некорректная дата').css({visibility: 'visible', color: 'red'});
        }
    };

    Personal.prototype.birthDay = function(day){
        this.day = day;
        this.birth();
    };
    Personal.prototype.birthMonth = function(month){
        this.month = month;
        this.birth();
    };
    Personal.prototype.birthYear = function(year){
        this.year = year;
        this.birth();
    };

    Personal.prototype.addEventListenerBirth = function(){
        $('#day_birth,#month_birth,#year_birth').on('change',{self:this}, function(event){
            if(event.target.tagName.toLowerCase()==='select'){
                switch (event.target.name){
                    case 'day_birth':{
                        event.data.self.birthDay(event.target.value);
                        break;
                    }
                    case 'month_birth':{
                        event.data.self.birthMonth(event.target.value);
                        break;
                    }
                    case 'year_birth':{
                        event.data.self.birthYear(event.target.value);
                        break;
                    }

                }
            }
        });
    };

    Personal.prototype.addEventListenerExample = function(){
        $('#city_example, #nationality_example, #work_permit_example').on('click', {self:this},function(event){
            $(event.target).parent().parent().children().get(1).value = $(event.target).text();
        });
    };
    Personal.prototype.autocompCity = function(data){
        this.$city.autocomplete({
            source: data
        });
    };
    Personal.prototype.autocompNationality = function(data){
        this.$nationality.autocomplete({
            source: data
        });
    };
    Personal.prototype.autocompWorkPermit = function(data){
        this.$work_permit.autocomplete({
            source: data
        });
    };


    Personal.prototype.autocompletePost = function(){
        var self = this;
        $.post( BASE_URL+"/profile/autocomplete",
            { autocomplete: "autocomplete"})
            .done(function( data ) {
                data = JSON.parse(data);
                self.autocompCity(JSON.parse(data['city']));
                self.autocompNationality(JSON.parse(data['city']));
                self.autocompWorkPermit(JSON.parse(data['city']));
            });
    };

    Personal.prototype.init = function(){
        this.autocompletePost();
        this.validateForm();
        this.addEventListenerBirth();
        this.addEventListenerExample();
    };


    var personal = new Personal();
    personal.init();

})(jQuery, BASE_URL ,window)
