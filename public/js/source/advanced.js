/**
 * Created by root on 1/24/15.
 */
(function($, BASE_URL,HBS,window){
    function Advanced(){
        this.$targetRadio = $('#targetRadio');
        this.$radioBlock = $('.radioBlock');

        this.$templateAdvancedLanguage = $("#template_advanced_language").html();

        this.$blockLanguage = $("#blockLanguage");
        this.$language = $("#languages");

        this.$deduce = $(".deduce");
        this.$interval = $("#interval");

        this.$targetCheckbox = $('#targetCheckbox');
        this.$checkboxBlock = $('.checkboxBlock');

        this.$professionalAreaLink = $('.professionalAreaLink');
        this.$professional_area = $('#professional_area');
        this.$inputProfessionalAreaBlock = $('#inputProfessionalAreaBlock');
        this.$professionalAreaList = $('.professionalAreaList');

        this.$containerCity = $('#containerCity');
        this.$city = $('#city');
        this.$cityBlock = $('.cityBlock');


        this.$names_institutions = $('.names_institutions');
        this.$institutionsBlock = $('.institutionsBlock');


        this.$linkSpan = $('.linkSpan');



        this.wordKeyRussianValue = {
            allWorlds:'Все слова',
            someWorlds:'Любое из слов',
            exactWorlds:'Точная фраза',
            noWorld:'Не встречаются'
        }

        this.languages = ['абхазский',
            'аварский',
            'азербайджанский',
            'албанский',
            'амхарский',
            'английский',
            'арабский',
            'армянский',
            'африкаанс',
            'баскский',
            'башкирский',
            'белорусский',
            'бенгальский',
            'болгарский',
            'боснийский',
            'бурятский',
            'бенгерский',
            'вьетнамский',
            'голландский',
            'греческий',
            'грузинский',
            'дагестанский',
            'даргинский',
            'дари',
            'датский',
            'езидский',
            'иврит',
            'ингушский',
            'индонезийский',
            'ирландский',
            'исландский',
            'испанский',
            'итальянский',
            'кабардино-черкесский',
            'казахский',
            'карачаево-балкарский',
            'карельский',
            'каталанский',
            'кашмирский',
            'китайский',
            'коми',
            'корейский',
            'креольский (Сейшельские острова)',
            'кумыкский',
            'курдский',
            'кхмерский (Камбоджийский)',
            'кыргызский',
            'лакский',
            'лаосский',
            'латинский',
            'латышский',
            'лезгинский',
            'литовский',
            'македонский',
            'малазийский',
            'мансийский',
            'марийский',
            'молдавский',
            'монгольский',
            'немецкий',
            'непальский',
            'ногайский',
            'норвежский',
            'осетинский',
            'панджаби',
            'персидский',
            'польский',
            'португальский',
            'пушту',
            'румынский',
            'русский',
            'санскрит',
            'сербский',
            'словацкий',
            'словенский',
            'сомалийский',
            'суахили',
            'тагальский',
            'таджиксТалышский',
            'тамильский',
            'татарский',
            'тибетский',
            'тувинский',
            'турецкий',
            'туркменский',
            'узбекский',
            'уйгурский',
            'украинский',
            'урду',
            'фарси',
            'финский',
            'фламандский',
            'французский',
            'хинди',
            'хорватский',
            'чеченский',
            'чешский',
            'чувашский',
            'шведский',
            'эсперанто',
            'эстонский',
            'якутский',
            'японский'];
    };

    Advanced.prototype.addEventListenerTargetRadio = function(){
        this.$targetRadio.on('click',{self:this},function(event){
            var $element =  $(this);
            var $resumeSearchSort = $('.resumeSearchSort' ,$element);
            var $radioBlock = $resumeSearchSort.siblings('.radioBlock');
            var $targetElement = $(event.target);

            if($targetElement.hasClass('resumeSearchSort')){
                $radioBlock.toggle();
            }

            if($targetElement.is('input[type=radio]')){
                $resumeSearchSort.text(event.data.self.wordKeyRussianValue[$targetElement.val()]);
                $radioBlock.hide();
            }

            event.data.self.$checkboxBlock.hide();

        });
    };

    Advanced.prototype.addEventListenerTargetCheckbox = function(){
        this.$targetCheckbox.on('click',{self:this},function(event){
            var $element =  $(this);
            var $resumeSearchSort = $('.resumeSearchSort' ,$element);
            var $checkboxBlock = $resumeSearchSort.siblings('.checkboxBlock');
            var $targetElement = $(event.target);
            var $listCheckbox = $('input[type=checkbox]',$element);

            if($targetElement.hasClass('resumeSearchSort')){
                $checkboxBlock.toggle();
            }

            if($targetElement.is('input[type=checkbox]')){
                if($targetElement.attr('id') == 'placeSearchAll'){
                    $targetElement.prop('checked',true);
                    $listCheckbox.not('#placeSearchAll').prop('checked',false);
                }else{

                    if($targetElement.attr('id') === 'workExperience' && $targetElement.prop('checked')){
                        $('input[type=checkbox]' ,$targetElement.parent().siblings('.childrenUl')).prop('checked', true);
                    }else if($targetElement.attr('id') === 'workExperience' && !$targetElement.prop('checked')){
                        $('input[type=checkbox]' ,$targetElement.parent().siblings('.childrenUl')).prop('checked', false);
                    }

                    if($targetElement.hasClass('work-experience')){
                        var $elem = $targetElement.parents('.childrenUl').parent('li');
                        if($('.childrenUl input[type=checkbox]', $elem).filter(':checked').length){
                            $('input[type=checkbox]#workExperience' , $elem).prop('checked', true);
                        }else{
                            $('input[type=checkbox]#workExperience' , $elem).prop('checked', false);
                        }
                    }

                    if($listCheckbox.not('#placeSearchAll').length ===  $listCheckbox.filter(":checked").length){
                        $listCheckbox.filter("#placeSearchAll").prop('checked',true);
                        $listCheckbox.not('#placeSearchAll').prop('checked',false);
                    }else if($listCheckbox.filter(":checked").length === 0 ){
                        $listCheckbox.filter("#placeSearchAll").prop('checked',true);
                    }else{
                        $listCheckbox.filter("#placeSearchAll").prop('checked',false);
                    }
                }
                var text = '';
                $listCheckbox.filter(':checked').parent('label').each(function(index, value){
                    if(index > 0)
                        text += ', '+$(value).text();
                    else
                        text += $(value).text();

                    $resumeSearchSort.text(text);
                });

            }else if($targetElement.hasClass('buttonCheckboxLi')){
                event.data.self.$checkboxBlock.hide();
            }


            event.data.self.$radioBlock.hide();
        });
    };

    Advanced.prototype.addEventListenerProfessionalArea = function(){
        this.$professionalAreaLink.on('click',{self: this}, function(event){
            if($(event.target).hasClass('link')){
                $(this).hide();
                event.data.self.$inputProfessionalAreaBlock.show();
                $('input',event.data.self.$inputProfessionalAreaBlock).prop('disabled', false);
            }
        });
    };

    Advanced.prototype.getJsonProfessionalArea = function(){
        var self = this;
        $.getJSON(BASE_URL+'/public/json/professional_area.json', function(data){
            var dataArray = [];
            for(var index in data){
                dataArray.push(data[index]['title']);
                for(var i  in data[index]['children']){
                    dataArray.push(data[index]['children'][i]['title']);
                }
            }
            self.$professional_area.autocomplete({
                source: dataArray
            });
        });
    };

    Advanced.prototype.autocompltiteInput = function(data){
        this.$city.autocomplete({
            source: data
        });
        $('#nationality').autocomplete({
            source: data
        });
        $('#work_permit').autocomplete({
            source: data
        });
    };


    Advanced.prototype.autocompletePost = function(){
        var self = this;
        $.post( BASE_URL+"/admincontrol/autocomplete",
            { autocomplete: "autocomplete"})
            .done(function( data ) {
                data = JSON.parse(data);
                self.autocompltiteInput(data);
                self.autocompltiteInput(data);
                self.autocompltiteInput(data);
            });
    };

    Advanced.prototype.addEventListenerTempateLanguage = function(){
        this.$blockLanguage.on('click',{self: this}, function(event){
            if(event.target.id === 'linkLanguage'){
                HBS.registerHelper('ifCond', function(v1, v2, options) {

                    if (v1 === v2) {
                        return options.fn(this);
                    }
                    return options.inverse(this);
                });
                var template = HBS.compile(event.data.self.$templateAdvancedLanguage);
                var html = template({'languages':event.data.self.languages});
                event.data.self.$language.append(html);
            }else if($(event.target).hasClass('closeBlock')){
                $(event.target).parent('li').remove();
            }
        });

    };

    Advanced.prototype.addEventListenerProfessionalBlock = function(){
        this.$inputProfessionalAreaBlock.on('click',{self: this}, function(event){
            var $target = $(event.target);
            if($target.attr('id') === 'cancelProfessionalAreaButton'){
                $(this).hide();
                event.data.self.$professionalAreaLink.show();
                $('input',event.data.self.$inputProfessionalAreaBlock).prop('disabled', true);

            }else if($target.attr('id') === 'addProfessionalAreaButton' && $.trim(event.data.self.$professional_area.val())){
                event.data.self.$professionalAreaList.append('<span><span class="closeBlock" title="удалить">' +
                '<input type="hidden" name="advancedForm[professional_area][]" value="' +
                $.trim(event.data.self.$professional_area.val())+'"></span>' +
                $.trim(event.data.self.$professional_area.val()) +
                '</span>');
                event.data.self.$professional_area.val('');
            }

            if($target.hasClass('closeBlock')){
                $target.parent('span').remove();
            }
        });
    };
    Advanced.prototype.addEventListenerCityBlock = function(){
        this.$containerCity.on('click',{self: this}, function(event){
            var $target = $(event.target);
            if($target.attr('id') === 'addCityButton' && $.trim(event.data.self.$city.val())){
                event.data.self.$cityBlock.append('<span><span class="closeBlock" title="удалить">' +
                '<input type="hidden" name="advancedForm[city][]" value="' +
                $.trim(event.data.self.$city.val())+'"></span>' +
                $.trim(event.data.self.$city.val()) +
                '</span>');
                event.data.self.$city.val('');
            }

            if($target.hasClass('closeBlock')){
                $target.parent('span').remove();
            }
        });
    };

    Advanced.prototype.addEventListenerNamesInstitutions = function(){
        this.$names_institutions.on('click',{self: this}, function(event) {
            var $target = $(event.target);
            if($target.hasClass('link')){
                $('input',event.data.self.$institutionsBlock).prop('disabled', false);
                event.data.self.$institutionsBlock.show();
                $target.hide()
            }
        });
    };
    Advanced.prototype.addEventListenerLinkSpan = function(){
        this.$linkSpan.on('click',{self: this}, function(event) {
            var $target = $(event.target);
            if($target.hasClass('link')){
                var $parent = $target.parent('.linkSpan');
                var $hideElement = $parent.siblings('.hideDiv');
                $('input[type=text]', $hideElement).prop('disabled', false);
                $hideElement.show()
                $parent.hide();
            }
        });
    };
    Advanced.prototype.addEventListenerDatetimepicker = function(){
        $('#datetimepickerFrom,#datetimepickerBefore').datetimepicker({
                lang:'ru',
                i18n:{
                    de:{
                        months:[
                            'Январь','Февраль','Март','Апрель',
                            'Май','Июнь','Июль','Август',
                            'Сентябрь','Октябрь','Ноябрь','Сентябрь',
                        ],
                        dayOfWeek:[
                            "Вск.", "Пнд", "Втр", "Срд",
                            "Чтв", "Птн", "Сбт",
                        ]
                    }
                },
                datepicker: true,
                timepicker:false,
                inline:false,
                format:'Y-m-d'}
        );

        this.$deduce.on('change',{self:this} ,function(event){

           if($('#showInterval').prop('checked')){
               $('input[type=text]',event.data.self.$interval).prop('disabled',false);
               event.data.self.$interval.show();
           }else{
               $('input[type=text]',event.data.self.$interval).prop('disabled',true);
               event.data.self.$interval.hide();
           }


        });

    };


    Advanced.prototype.init = function(){
        this.addEventListenerTargetRadio();
        this.addEventListenerTargetCheckbox();
        this.addEventListenerProfessionalArea();
        this.addEventListenerProfessionalBlock();

        this.addEventListenerCityBlock();

        this.addEventListenerLinkSpan();

        this.autocompletePost();

        this.addEventListenerTempateLanguage();

        this.addEventListenerNamesInstitutions();

        this.addEventListenerDatetimepicker();

        this.getJsonProfessionalArea();
    };

    var advanced = new Advanced();
    advanced.init();

})(jQuery,BASE_URL,Handlebars, window)

