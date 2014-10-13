(function($, Handlebars ,window){
    function Personal(){
        this.$form = $('.personal.experience .form');
        this.count_table_experience = $('*[data-table-experience-id]:last').data()['tableExperienceId'];
        this.count_table_recommendations = $('*[data-table-recommendations-id]:last').data()['tableRecommendationsId'];
    }


    Personal.prototype.tableTemplatePosition = function(){

        var source = $("#table-template-experience").html();

        Handlebars.registerHelper('month', function(n) {
            var out='';
            var month ={
                1:"январь",
                2:"февраль",
                3:"март",
                4:"апрель",
                5:"май",
                6:"июнь",
                7:"июль",
                8:"август",
                9:"сентябрь",
                10:"октябрь",
                11:"ноябрь",
                12:"декабрь"
            };
            for(var i= 1; i<=n; i++) {
                out += "<option value='"+i+"'>" + month[i] + "</option>";
            }

            return out;
        });

        Handlebars.registerHelper('year_start', function(n) {
            var out="<option value='0'></option>";
            var date = new Date();

            for(var i=n; i < date.getFullYear(); ++i) {
                out += "<option value='"+i+"'>"+i+"</option>";
            }
            return out;
        });

        Handlebars.registerHelper('year_finish', function(n) {
            var out="<option value='0'></option>";
            var date = new Date();

            for(var i=date.getFullYear()+50; i > n; i--) {
                out += "<option value='"+i+"'>"+i+"</option>";
            }

            return out;
        });
        var template = Handlebars.compile(source);
        return template({'i':++this.count_table_experience});
    };

    Personal.prototype.tableTemplateRecommendations = function(){
        var source = $("#table-template-recommendations").html();
        var template = Handlebars.compile(source);
        return template({'i':++this.count_table_recommendations});

    };

    Personal.prototype.addEventListenerFORM = function() {
        this.$form.on('click', {self:this}, function(event){
            switch (event.target.className){
                case 'add_position':{
                    var html = event.data.self.tableTemplatePosition();
                    $(event.target).before(html);
                    break;
                }
                case 'add_recommendations':{
                    var html = event.data.self.tableTemplateRecommendations();
                    $(event.target).before(html);
                    break;
                }
                case 'delete experience':{
                    $('*[data-table-experience-id="'+$(event.target).
                        data()['tableExperienceId']+'"]').
                        remove();
                    break;
                }
                case 'delete recommendations':{
                    $('*[data-table-recommendations-id="'+$(event.target).
                        data()['tableRecommendationsId']+'"]').
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

