(function($, window){
    function Personal(){
        this.day = false;
        this.month = false;
        this.year = false;

        this.$wrapper = $('.personal');
        this.$error_data = $('.data', this.$wrapper);
        this.$nationality = $('#nationality',this.$wrapper);
        this.$work_permit = $('#work_permit',this.$wrapper);
        this.$birth = $('#birth', this.$wrapper);
    }

    Personal.prototype.validateForm = function(){
        $("#personal_form").validate({
            rules:{
                surname:{
                    required: true
                },

                first_name:{
                    required: true
                }
            },
            messages:{

                surname:{
                    required: "Это поле обязательно для заполнения"
                },

                first_name:{
                    required: "Это поле обязательно для заполнения"
                }
            }
        });
    };

    Personal.prototype.birth = function(){
        var d = new Date();
        if( this.day && this.month && this.year){
            this.$error_data.text("года("+ (d.getFullYear()-this.year - 1)+" лет)").css({visibility: 'visible', color: 'black'});
        }else if(!this.day && !this.month && !this.year){
            this.$error_data.css({visibility: 'hidden'});
        }else if(!this.day || !this.month || !this.year){
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
        this.$birth.on('click',{self:this}, function(event){
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

    Personal.prototype.addEventListenerWorkPermit = function(){
        this.$work_permit.on('click',{self:this}, function(event){
            var $other_work_permit_other = $('.work_permit_other', event.data.self.$work_permit);
            if(event.target.tagName.toLowerCase() === 'input' &&
                $(event.target).data('work_permit') === 'other' && $other_work_permit_other.length === 0){
                event.data.self.$work_permit.append('<input type="text" class="work_permit_other" name="work_permit_other">');
            }else if(event.target.tagName.toLowerCase() === 'input'
                && $(event.target).data('work_permit') === 'bel' ){

                if($other_work_permit_other.length === 1){
                    $other_work_permit_other.remove();
                }
            }
        });
    };


    Personal.prototype.addEventListenerNationality = function(){
        this.$nationality.on('click',{self:this}, function(event){
            var $other_country = $('.other_country', event.data.self.$nationality);
            if(event.target.tagName.toLowerCase() === 'input' &&
                $(event.target).data('nationality') === 'other' &&
                $other_country.length === 0){
                event.data.self.$nationality.append('<input type="text" class="other_country" name="nationality_other">');
            }else if(event.target.tagName.toLowerCase() === 'input' && $(event.target).data('nationality') === 'bel'){

                if($other_country.length === 1){
                    $other_country.remove();
                }
            }
        });
    };

    Personal.prototype.init = function(){
        this.validateForm();
        this.addEventListenerNationality();
        this.addEventListenerWorkPermit();
        this.addEventListenerBirth();
    };


    var personal = new Personal();
    personal.init();

})(jQuery, window)