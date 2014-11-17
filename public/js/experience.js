(function($, BASE_URL, Handlebars ,window){
    function Personal(){
        this.$form = $('.personal.experience .form');
        this.count_table_experience = $('*[data-table-experience-id]:last').data()['tableExperienceId'];
        this.count_table_recommendations = $('*[data-table-recommendations-id]:last').data()['tableRecommendationsId'];
        this.$key_skill = $('#key_skill');
        this.$skills_td = $('.skills_td');
        this.$add_skills_hid = $('.add_skills_hid');
    }

    Personal.prototype.validateForm = function(){

        $("#experience_form").validate({

            rules: {

                'organizations[0]': {
                    required: true
                },
                'organizations[1]': {
                    required: true
                },
                'organizations[2]': {
                    required: true
                },

                'regions[0]': {
                    required: true
                },
                'regions[1]': {
                    required: true
                },
                'regions[2]': {
                    required: true
                },

                'functions[0]': {
                    required: true
                },
                'functions[1]': {
                    required: true
                },
                'functions[2]': {
                    required: true
                },

                'positions[0]':{
                    required: true
                },
                'positions[1]':{
                    required: true
                },
                'positions[2]':{
                    required: true
                },
                'positions[3]':{
                    required: true
                },
                'getting_starteds[0][year]':{
                    required: true
                }

            },
            messages: {

                'organizations[0]': {
                    required: "Это поле обязательно для заполнения"
                }, 'organizations[1]': {
                    required: "Это поле обязательно для заполнения"
                }, 'organizations[2]': {
                    required: "Это поле обязательно для заполнения"
                },
                'regions[0]': {
                    required: "Это поле обязательно для заполнения"
                }, 'regions[1]': {
                    required: "Это поле обязательно для заполнения"
                }, 'regions[2]': {
                    required: "Это поле обязательно для заполнения"
                },

                'functions[0]': {
                    required: "Это поле обязательно для заполнения"
                },
                'functions[1]': {
                    required: "Это поле обязательно для заполнения"
                },
                'functions[2]': {
                    required: "Это поле обязательно для заполнения"
                },
                'positions[0]': {
                    required: "Это поле обязательно для заполнения"
                },
                'positions[1]': {
                    required: "Это поле обязательно для заполнения"
                },
                'positions[2]': {
                    required: "Это поле обязательно для заполнения"
                },

                'getting_starteds[0][year]':{
                    required: "Это поле обязательно для заполнения"
                }
            }
        });

    };


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

    Personal.prototype.tableTemplateRecommendations = function(){
        var source = $("#table-template-recommendations").html();
        var template = Handlebars.compile(source);
        return template({'i':++this.count_table_recommendations,'BASE_URL':BASE_URL});

    };

    Personal.prototype.addEventListenerFORM = function() {
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
    Personal.prototype.addEventListenerSkills = function(){
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
                    if($('span',event.data.self.$add_skills_hid).length === 0 && !event.data.self.$key_skill.val()){
                        $('.message',event.data.self.$skills_td).text('Обязательно для заполнения');
                    }
                    break;
                }
            }

        });

    };


    Personal.prototype.autocompSkills = function(data){
        this.$key_skill.autocomplete({
            source: data
        });
    };

    Personal.prototype.autocompRegions = function(data){
        $('.regions').autocomplete({
            source: data
        });
    };

    Personal.prototype.autocompOrganization = function(data){
        $('.organizations').autocomplete({
            source: data
        });
    };
    Personal.prototype.autocomPositions = function(data){
        $('.positions').autocomplete({
            source: data
        });
    };

    Personal.prototype.autocompFieldActivities = function(data){
        $('.field_activities').autocomplete({
            source: data
        });
    };

    Personal.prototype.autocompletePost = function(){
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
    Personal.prototype.addEventListenerMessage = function(){
       $('div.message',this.$form).filter(function(){
            return $(this).text()
        }).siblings("input").css('border', '1px red solid');
        console.log( $('div.message',this.$form).filter("text").css({"border": "1px  red solid"}));
    };


    Personal.prototype.init = function(){
        this.validateForm();
        this.autocompletePost();
        this.addEventListenerSkills();
        this.addEventListenerFORM();
        this.addEventListenerMessage();
    };

    var personal = new Personal();
    personal.init();

})(jQuery, BASE_URL, Handlebars ,window)

