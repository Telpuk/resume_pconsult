(function($, Handlebars ,window){
    function Personal(){
        this.$form = $('.personal.education .form');
        this.count_base_education = $('*[data-table-base-education-id]:last').data()['tableBaseEducationId'];
        this.count_traning_course = $('*[data-table-training-courses-id]:last').data()['tableTrainingCoursesId'];
        this.count_tests_exams = $('*[data-table-tests-exams-id]:last').data()['tableTestsExamsId'];
        this.count_electronic_certificates = $('*[data-table-electronic-certificates-id]:last').data()['tableElectronicCertificatesId'];
        this.count_tr_language = ($('*[data-tr-language-further-id]:last').data() !== undefined)? $('*[ data-tr-language-further-id]:last').data()['trLanguageFurtherId']:0;

    }

    Personal.prototype.tableTemplateBaseEducation= function(){

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
        return template({'i':++this.count_base_education});
    };

    Personal.prototype.tableTemplateTrainingCourse = function(){
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
        return template({'i':++this.count_traning_course});
    };

    Personal.prototype.tableTemplateTestsExams = function(){
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
        return template({'i':++this.count_tests_exams});
    };

    Personal.prototype.tableTemplateElectronicCertificates = function(){
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
        return template({'i':++this.count_electronic_certificates});
    };

    Personal.prototype.tableTemplateTrLanguage = function(){
        var source = $("#tr_language_further").html();
        var languages = ['Абхазский',
            'Аварский',
            'Азербайджанский',
            'Албанский',
            'Амхарский',
            'Английский',
            'Арабский',
            'Армянский',
            'Африкаанс',
            'Баскский',
            'Башкирский',
            'Белорусский',
            'Бенгальский',
            'Болгарский',
            'Боснийский',
            'Бурятский',
            'Венгерский',
            'Вьетнамский',
            'Голландский',
            'Греческий',
            'Грузинский',
            'Дагестанский',
            'Даргинский',
            'Дари',
            'Датский',
            'Езидский',
            'Иврит',
            'Ингушский',
            'Индонезийский',
            'Ирландский',
            'Исландский',
            'Испанский',
            'Итальянский',
            'Кабардино-черкесский',
            'Казахский',
            'Карачаево-балкарский',
            'Карельский',
            'Каталанский',
            'Кашмирский',
            'Китайский',
            'Коми',
            'Корейский',
            'Креольский (Сейшельские острова)',
            'Кумыкский',
            'Курдский',
            'Кхмерский (Камбоджийский)',
            'Кыргызский',
            'Лакский',
            'Лаосский',
            'Латинский',
            'Латышский',
            'Лезгинский',
            'Литовский',
            'Македонский',
            'Малазийский',
            'Мансийский',
            'Марийский',
            'Молдавский',
            'Монгольский',
            'Немецкий',
            'Непальский',
            'Ногайский',
            'Норвежский',
            'Осетинский',
            'Панджаби',
            'Персидский',
            'Польский',
            'Португальский',
            'Пушту',
            'Румынский',
            'Русский',
            'Санскрит',
            'Сербский',
            'Словацкий',
            'Словенский',
            'Сомалийский',
            'Суахили',
            'Тагальский',
            'ТаджиксТалышский',
            'Тамильский',
            'Татарский',
            'Тибетский',
            'Тувинский',
            'Турецкий',
            'Туркменский',
            'Узбекский',
            'Уйгурский',
            'Украинский',
            'Урду',
            'Фарси',
            'Финский',
            'Фламандский',
            'Французский',
            'Хинди',
            'Хорватский',
            'Чеченский',
            'Чешский',
            'Чувашский',
            'Шведский',
            'Эсперанто',
            'Эстонский',
            'Якутский',
            'Японский'];

        Handlebars.registerHelper('languages', function() {
            var out="";
            for(var i= 0, len =languages.length; i<= len; ++i) {
                out += "<option value='"+languages[i]+"'>"+languages[i]+"</option>";
            }
            return out;
        });

        var template = Handlebars.compile(source);
        return template({'i':++this.count_tr_language});
    };

    Personal.prototype.addEventListenerFORM = function() {
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

    Personal.prototype.init = function(){
        this.addEventListenerFORM();
    };

    var personal = new Personal();
    personal.init();

})(jQuery, Handlebars ,window)