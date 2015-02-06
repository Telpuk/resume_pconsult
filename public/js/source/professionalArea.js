(function($, Handlebars ,doc, win, BASE_URL){
    function ProfessionalArea() {
        this.$buttonClick = null;
        this.$wrapperProfessArea = null;
        this.$listProfessArea = null;

        this.template = Handlebars.compile($("#professionalArea-template").html());
        this.templateBlock = Handlebars.compile($("#professionalAreaBlock-template").html());

        this.$professionalAreaBlock = $("#professionalAreaBlock");

        this.$wrapperFooter = $(".wrapperFooter");
        this.$closeGif = $("#closeGif");

        this.countCheckboxChecked = null;

        this.$textSearch = null;

        this.dataJson = null;
    }

    ProfessionalArea.prototype.addEventListenerProfArea = function(){
        this.$buttonClick.on('click', {self:this}, function(event){
            event.data.self.$wrapperProfessArea.show();
            return false;
        });
        this.$closeGif.on('click', {self:this}, function(event){
            event.data.self.$wrapperProfessArea.hide();
            return false;
        });

        this.$professionalAreaBlock.on('click', {self:this}, function(event){
            if(event.target.className === 'closeBlock' && $(event.target).parent('p').length){
                $(event.target).parents('#professionalAreaBlock').html('');
            }else if(event.target.className === 'closeBlock' && $(event.target).parent('li').length){
                $(event.target).parent('li').remove();
            }
            return false;
        });
        this.$wrapperFooter.on('click', {self:this}, function(event){
            if(event.target.id == 'cancel'){
                event.data.self.$wrapperProfessArea.fadeOut(100);
            }else if(event.target.id == 'enter'){
                var listCheckboxChecked = $('#listProfessArea > ul > li input[type=checkbox]:checked');
                var object = [];
                if(listCheckboxChecked.length > 0){
                    for(var i = 0; i < listCheckboxChecked.length;++i){
                        object.push({
                            'id': $(listCheckboxChecked[i]).data('professionalAreaChildrenId'),
                            'title':  $(listCheckboxChecked[i]).val()
                        })
                    }
                    event.data.self.$professionalAreaBlock.html(event.data.self.templateBlock({
                        'id':$(listCheckboxChecked.get(0)).data('professionalAreaId'),
                        'title':$(listCheckboxChecked.get(0)).parents('ul').siblings('span').text(),
                        'object':object
                    }));
                    event.data.self.$wrapperProfessArea.hide();
                }
            }
            return false;
        });
    };

    ProfessionalArea.prototype.addEventListenerListProfessArea = function(){
        this.$listProfessArea.on('click',{self:this}, function(event){
            var $element = $(event.target);

            if(event.target.tagName.toLowerCase() === 'span'){
                $element.siblings('ul').toggle();
                $element.parent('li').toggleClass('selector_open');
            }else if(event.target.tagName.toLowerCase() === 'input'){
                var activeTitle = $element.data('professionalAreaId');
                var listCheckboxChecked = $('#listProfessArea > ul > li input[type=checkbox][data-professional-area-id="' + activeTitle + '"]:checked');

                if(event.data.self.countCheckboxChecked === listCheckboxChecked.length){
                    $('#listProfessArea > ul > li input[type=checkbox][data-professional-area-id="' + activeTitle + '"]')
                        .not(':checked')
                        .prop("disabled", true);
                }else if(listCheckboxChecked.length === 1){
                    $('#listProfessArea > ul > li input[type=checkbox][data-professional-area-id!="' + activeTitle + '"]')
                        .not(':checked')
                        .prop("disabled", true);
                }else if(event.data.self.countCheckboxChecked >=  listCheckboxChecked.length &&  listCheckboxChecked.length > 1){
                    $('#listProfessArea > ul > li input[type=checkbox][data-professional-area-id="' + activeTitle + '"]')
                        .not(':checked')
                        .prop("disabled", false);
                }else if(listCheckboxChecked.length  === 0){
                    $('#listProfessArea > ul > li input[type=checkbox]').prop("disabled", false);
                }

            }

        });
    };

    ProfessionalArea.prototype.addEventListenerTextSearch = function(){

        //подсветка
        this.$textSearch.on('keyup search',{self:this}, function(event) {
            if(event.data.self.dataJson) {
                var temp = [];
                // pull in the new value
                var searchTerm = $(this).val().trim();

                // remove any old highlighted terms
                $('#listProfessArea').removeHighlight();

                // disable highlighting if empty
                if (searchTerm) {
                    for(var i = 0, len = event.data.self.dataJson.length; i < len; ++i){
                        if(event.data.self.dataJson[i].title.toLowerCase().indexOf(searchTerm.toLowerCase()) >=0 ){
                            temp.push(event.data.self.dataJson[i]);
                        }else{
                            var searchChildren = {
                                title:event.data.self.dataJson[i].title,
                                children: [],
                                id: event.data.self.dataJson[i].id,
                                name: event.data.self.dataJson[i].name
                            };
                            var children = event.data.self.dataJson[i].children;
                            for(var j = 0, l = children.length; j < l; ++j){
                                if(children[j].title.toLowerCase().indexOf(searchTerm.toLowerCase()) >= 0){
                                    searchChildren.children.push(children[j]);
                                }
                            }
                            if(searchChildren.children.length){
                                temp.push(searchChildren);
                            }
                        }
                    }
                    //построение по поиску
                    $('ul',event.data.self.$listProfessArea).html(event.data.self.template({'object':temp, 'status':'selector_open'}));

                    // highlight the new term
                    $('#listProfessArea').highlight(searchTerm);
                }else{
                    $('ul',event.data.self.$listProfessArea).html(event.data.self.template({'object':event.data.self.dataJson}));
                }
            }
        });
    };


    ProfessionalArea.prototype.init = function(buttonClick, nameWrapper, countCheckboxChecked){
        var self = this;

        self.$buttonClick = buttonClick?$(buttonClick):$('#profArea');
        self.$wrapperProfessArea = nameWrapper?$(nameWrapper):$('#wrapperProfessArea');

        self.countCheckboxChecked = countCheckboxChecked?countCheckboxChecked:3;

        self.$listProfessArea = $('#listProfessArea',self.$wrapperProfessArea);
        self.$textSearch = $('#text-search',self.$wrapperProfessArea);

        $.getJSON(BASE_URL+'/public/json/professional_area.json').done(function(data) {
            self.dataJson = data;
            if(self.dataJson){
                $('ul',self.$listProfessArea).html(self.template({'object':self.dataJson}));
                self.addEventListenerListProfessArea();
                self.addEventListenerProfArea();
                self.addEventListenerTextSearch();
            }
        }).fail(function(){
            console.log('No upload json');
        });


    };

    var  professionalArea  = new ProfessionalArea();
    professionalArea.init(null, null, null);

})(jQuery, Handlebars ,document, window, BASE_URL);

