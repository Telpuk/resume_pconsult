(function($, BASE_URL,Handlebars ,window){
    function Education(){
        this.$form = $('.personal.education .form');
        this.$level = $('#level');
        this.count_base_education = $('*[data-table-base-education-id]:last').data()['tableBaseEducationId'];
        this.count_traning_course = $('*[data-table-training-courses-id]:last').data()['tableTrainingCoursesId'];
        this.count_tests_exams = $('*[data-table-tests-exams-id]:last').data()['tableTestsExamsId'];
        this.count_electronic_certificates = $('*[data-table-electronic-certificates-id]:last').data()['tableElectronicCertificatesId'];
        this.count_tr_language = ($('*[data-tr-language-further-id]:last').data() !== undefined)? $('*[ data-tr-language-further-id]:last').data()['trLanguageFurtherId']:0;

    }

    Education.prototype.tableTemplateBaseEducation= function(){

        var source = $("#table-base-education").html();

        Handlebars.registerHelper('years', function() {
            var out="<option value='0'></option>";
            var date = new Date();

            for(var year=date.getFullYear()+10; year >=1950 ; --year) {
                out += "<option value='"+year+"'>"+year+"</option>";
            }
            return out;
        });

        var template = Handlebars.compile(source);
        return template({'i':++this.count_base_education,'BASE_URL':BASE_URL});
    };

    Education.prototype.tableTemplateTrainingCourse = function(){
        var source = $("#table_training_courses").html();

        Handlebars.registerHelper('years', function() {
            var out="<option value='0'></option>";
            var date = new Date();

            for(var year=date.getFullYear()+10; year >=1950 ; --year) {
                out += "<option value='"+year+"'>"+year+"</option>";
            }
            return out;
        });

        var template = Handlebars.compile(source);
        return template({'i':++this.count_traning_course,'BASE_URL':BASE_URL});
    };

    Education.prototype.tableTemplateTestsExams = function(){
        var source = $("#table_tests_exams").html();

        Handlebars.registerHelper('years', function() {
            var out="<option value='0'></option>";
            var date = new Date();

            for(var year=date.getFullYear()+10; year >=1950 ; --year) {
                out += "<option value='"+year+"'>"+year+"</option>";
            }
            return out;
        });

        var template = Handlebars.compile(source);
        return template({'i':++this.count_tests_exams,'BASE_URL':BASE_URL});
    };

    Education.prototype.tableTemplateElectronicCertificates = function(){
        var source = $("#table_electronic_certificates").html();

        Handlebars.registerHelper('years', function() {
            var out="<option value='0'></option>";
            var date = new Date();

            for(var year=date.getFullYear()+10; year >=1950 ; --year) {
                out += "<option value='"+year+"'>"+year+"</option>";
            }
            return out;
        });

        var template = Handlebars.compile(source);
        return template({'i':++this.count_electronic_certificates,'BASE_URL':BASE_URL});
    };

    Education.prototype.tableTemplateTrLanguage = function(){
        var source = $("#tr_language_further").html();
        var languages = ['абхазский',
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

        Handlebars.registerHelper('languages', function() {
            var out="";
            for(var i= 0, len =languages.length; i<= len; ++i) {
                out += "<option value='"+languages[i]+"'>"+languages[i]+"</option>";
            }
            return out;
        });

        var template = Handlebars.compile(source);
        return template({'i':++this.count_tr_language, 'BASE_URL':BASE_URL});
    };

    Education.prototype.addEventListenerFORM = function() {
        this.$form.on('click', {self:this}, function(event){
            switch (event.target.className){
                case 'add_education':{
                    var html = event.data.self.tableTemplateBaseEducation();
                    $(event.target).before(html);
                    break;
                };
                case 'add_training_courses':{
                    var html = event.data.self.tableTemplateTrainingCourse();
                    $(event.target).before(html);
                    break;
                };
                case 'add_tests_exams':{
                    var html = event.data.self.tableTemplateTestsExams();
                    $(event.target).before(html);
                    break;
                };
                case 'add_language':{
                    var html = event.data.self.tableTemplateTrLanguage();
                    $('.possessions_language tbody').append(html);
                    break;
                };
                case 'add_electronic_certificates':{
                    var html = event.data.self.tableTemplateElectronicCertificates();
                    $(event.target).before(html);
                    break;
                };
                case 'delete language':{
                    $('*[data-tr-language-further-id="'+$(event.target).
                        data()['trLanguageFurtherId']+'"]').
                        remove();
                    break;
                };

                case 'delete training courses':{
                    $('*[data-table-training-courses-id="'+$(event.target).
                        data()['tableTrainingCoursesId']+'"]').
                        remove();
                    break;
                };

                case 'delete education base':{
                    $('*[data-table-base-education-id="'+$(event.target).
                        data()['tableBaseEducationId']+'"]').
                        remove();
                    break;
                };
                case 'delete electronic_certificates':{
                    $('*[data-table-electronic-certificates-id="'+$(event.target).
                        data()['tableElectronicCertificatesId']+'"]').
                        remove();
                    break;
                };
                case 'delete test_exam':{
                    $('*[data-table-tests-exams-id="'+$(event.target).
                        data()['tableTestsExamsId']+'"]').
                        remove();
                    break;
                };

            }

        });
    };
    Education.prototype.messageColor = function(){
        $('div.message',this.$personal_contacts).filter(function(){
            return $(this).text();
        }).parent().find("input[type=text]").css('border', '1px red solid');

        $('div.message',this.$personal_contacts).filter(function(){
            return $(this).text();
        }).parent().find("select").css('border', '1px red solid');
    };


    Education.prototype.init = function(){
        this.messageColor();
        this.addEventListenerFORM();
    };

    var education = new Education();
    education.init();

})(jQuery, BASE_URL,Handlebars ,window)