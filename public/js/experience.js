(function($, Handlebars ,window){
    function Personal(){
        this.$add = $('.add');
        this.count = 0;
    }

    Personal.prototype.tableTamplate = function(){

        var source   = $("#table-template").html();

        Handlebars.registerHelper('month', function(n) {
            var out;
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
        return template({'i':++this.count});
    }

    Personal.prototype.addEventListenerADD = function(){
        this.$add.on('click', {self:this}, function(event){
            var html = event.data.self.tableTamplate();
            $(event.target).before(html);
        });

    };


    Personal.prototype.init = function(){
        this.addEventListenerADD();
    };




    var personal = new Personal();
    personal.init();

})(jQuery, Handlebars ,window)

