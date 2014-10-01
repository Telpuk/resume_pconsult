(function($, window){
    function Personal(){
        this.$wrapper = $('.personal');
        this.$nationality = $('#nationality',this.$wrapper);
        this.$work_permit = $('#work_permit',this.$wrapper);
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
    };


    var personal = new Personal();
    personal.init();

})(jQuery, window)