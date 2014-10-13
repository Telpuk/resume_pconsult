(function($, Handlebars ,window){
    function Personal(){
        this.$form = $('.personal.education .form');
        this.count_base_education = $('*[data-table-base-education-id]:last').data()['tableBaseEducationId'];
        this.count_traning_course = $('*[data-table-training-courses-id]:last').data()['tableTrainingCoursesId'];
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

    Personal.prototype.addEventListenerFORM = function() {
        this.$form.on('click', {self:this}, function(event){
            switch (event.target.className){
                case 'add_education':{
                    var html = event.data.self.tableTemplateBaseEducation();
                    $(event.target).before(html);
                    break;
                }
                case 'add_training_courses':{
                    var html = event.data.self.tableTemplateTrainingCourse();
                    $(event.target).before(html);
                    break;
                }
                case 'delete training courses':{
                    $('*[data-table-training-courses-id="'+$(event.target).
                        data()['tableTrainingCoursesId']+'"]').
                        remove();
                    break;
                }
                case 'delete education base':{
                    $('*[data-table-base-education-id="'+$(event.target).
                        data()['tableBaseEducationId']+'"]').
                        remove();
                    break;
                }

            }

        });
    };

    Personal.prototype.init = function(){
        this.addEventListenerFORM();
    };

    var personal = new Personal();
    personal.init();

})(jQuery, Handlebars ,window)