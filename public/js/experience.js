(function($, BASE_URL, Handlebars ,window){
    function Experience(){
        this.$form = $('.personal.experience .form');
        this.count_table_experience = $('*[data-table-experience-id]:last').data()['tableExperienceId'];
        this.count_table_recommendations = $('*[data-table-recommendations-id]:last').data()['tableRecommendationsId'];
        this.$key_skill = $('#key_skill');
        this.$skills_td = $('.skills_td');
        this.$table_organizations = $('#table_organizations');
        this.$no_experience = $('#no_experience input[type=checkbox]');
        this.$add_skills_hid = $('.add_skills_hid');

        //рекомендации по запросу
        this.$questionRecommend = $('#questionRecommend');
        this.$questionRecommendInput = $('input',  this.$questionRecommend);
        this.$addRecommendations = $('.add_recommendations');
    }


    Experience.prototype.tableTemplatePosition = function(){

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

            for(var i=n; i <= date.getFullYear(); ++i) {
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
        return template({'i':++this.count_table_experience,'BASE_URL':BASE_URL});
    };

    Experience.prototype.tableTemplateRecommendations = function(){
        var source = $("#table-template-recommendations").html();
        var template = Handlebars.compile(source);
        return template({'i':++this.count_table_recommendations,'BASE_URL':BASE_URL});

    };

    Experience.prototype.addEventListenerFORM = function() {
        this.$form.on('click', {self:this}, function(event){
            switch (event.target.className){
                case 'add_position':{
                    event.data.self.autocompletePost();
                    var html = event.data.self.tableTemplatePosition();
                    $(event.target).before(html);
                    break;
                }
                case 'add_recommendations':{
                    event.data.self.autocompletePost();
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
    Experience.prototype.addEventListenerSkills = function(){
        this.$skills_td.on('click', {self:this}, function(event) {

            switch (event.target.className){
                case 'button_skill':{
                    if(event.data.self.$key_skill.val()){
                        $('.message',event.data.self.$skills_td).text('');
                        var val_h = event.data.self.$key_skill.val();
                        event.data.self.$key_skill.val('');
                        event.data.self.$add_skills_hid.append(
                            "<span><input type='hidden' name='skills_hidden[]' value='"+val_h+"'>"+val_h+"<img class='delete_skills' src='"+BASE_URL+"/public/img/remove.png'></span>");
                    }
                    break;
                }
                case 'delete_skills':{
                    var value =  $(event.target).parent().text();
                    $(event.target).parent().remove();
                    //if($('span',event.data.self.$add_skills_hid).length === 0 && !event.data.self.$key_skill.val()){
                    //    $('.message',event.data.self.$skills_td).text('Обязательно для заполнения');
                    //}
                    break;
                }
            }

        });

    };


    Experience.prototype.autocompSkills = function(data){
        this.$key_skill.autocomplete({
            source: data
        });
    };

    Experience.prototype.autocompRegions = function(data){
        $('.regions').autocomplete({
            source: data
        });
    };

    Experience.prototype.autocompOrganization = function(data){
        $('.organizations').autocomplete({
            source: data
        });
    };
    Experience.prototype.autocomPositions = function(data){
        $('.positions').autocomplete({
            source: data
        });
    };

    Experience.prototype.autocompFieldActivities = function(data){
        $('.field_activities').autocomplete({
            source: data
        });
    };

    Experience.prototype.autocompletePost = function(){
        var self = this;
        $.post( BASE_URL+"/side/autocomplete",
            { autocomplete: "autocomplete"})
            .done(function( data ) {
                data = JSON.parse(data);
                self.autocompSkills(JSON.parse(data['key_skills']));
                self.autocompRegions(JSON.parse(data['regions']));
                self.autocompOrganization(JSON.parse(data['organizations']));
                self.autocomPositions(JSON.parse(data['positions']));
                self.autocompFieldActivities(JSON.parse(data['field_activities']));
            });
    };
    Experience.prototype.messageColor = function(){
        $('div.message',this.$form).filter(function(){
            return $(this).text()
        }).siblings("input").css('border', '1px red solid');
        $('div.message',this.$form).filter(function(){
            return $(this).text()
        }).siblings("select").css('border', '1px red solid');
        $('div.message',this.$form).filter(function(){
            return $(this).text()
        }).siblings("textarea").css('border', '1px red solid');
    };

    Experience.prototype.addEventListenerNoExperience = function(){
        this.$no_experience.on('click', {self:this}, function(event) {
            if($(this).prop('checked') === true){
                event.data.self.$table_organizations.slideUp(200);
            }else{
                event.data.self.$table_organizations.slideDown(200);
            }

        });
    };

    Experience.prototype.checkNoExperienceChecked = function(){
        if(this.$no_experience.prop('checked') === true){
            this.$table_organizations.hide();
        }
    };

    Experience.prototype.questionRecommend = function(){

        if(this.$questionRecommendInput.prop("checked")){
            $('.recommendations input[type=text]').prop('disabled', true);
            this.$addRecommendations.hide();
        }else{
            $('.recommendations input[type=text]').prop('disabled', false);
            this.$addRecommendations.show();
        }

        this.$questionRecommend.on('click', {self:this}, function(event) {
            if(event.target.tagName.toLowerCase() !== 'input'){
                event.data.self.$questionRecommendInput.prop( "checked", function( i, val ) {
                    return !val;
                });
            }
            if(event.data.self.$questionRecommendInput.prop("checked")){
                $('.recommendations input[type=text]').prop('disabled', true).val('');
                event.data.self.$addRecommendations.hide();
            }else{
                $('.recommendations input[type=text]').prop('disabled', false);
                event.data.self.$addRecommendations.show();
            }

        });
    };

    Experience.prototype.init = function(){
        this.checkNoExperienceChecked();
        this.autocompletePost();
        this.questionRecommend();
        this.addEventListenerSkills();
        this.addEventListenerNoExperience();
        this.addEventListenerFORM();
        this.messageColor();
    };

    var experience = new Experience();
    experience.init();

})(jQuery, BASE_URL, Handlebars ,window)

