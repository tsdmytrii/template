$.Controller.extend('Component_types',{
    defaults: {
        viewpath:'//components/admin/component_type/views/',
        wind_opt: {
            width: findWindowWidth()
        }
    }

},{

    init:function(selector) {

        var obj = this;

        this.limit = 20;
        this.items = 0;
        this.offset = 0;

        this.elementId = this.element.attr('id');

        this.paginatorOptions = {
            itemsOnPage: this.limit,
            edges: 1,
            currentPage: 1,
            onPageClick: function(offset){
                obj.getOffset(offset);
            }
        };

        var html = $.View(this.Class.defaults.viewpath+'index.tmpl', {});

        this.element.html(html);

        this.loadComponentTypes();
    },

    /*
     * GET component types
     */

    loadComponentTypes: function() {

        Component_types_model.get_all_component_type({
            limit: this.limit,
            offset: this.offset
        }, this.callback('componentTypesGeted'));

    },

    componentTypesGeted: function(data) {

        if (data.success == false) {

            show_error('Пожалуйста добавьте несколько типов компонентов');

        } else {

            if (data.message) {

                $('#componentTypesList', '#'+this.elementId).html('<h3>'+data.message+'</h3>');

            } else {

                if (this.items === 0 || this.items !== parseInt(data.data.quantity)) {

                    this.items = parseInt(data.data.quantity);

                    this.refreshPaginator();

                }

                var html = $.View(this.Class.defaults.viewpath+'all_component_type.tmpl', {
                    our_data: data.data.data,
                    pref: this.Class.defaults.pref
                });

                $('#componentTypesList', '#'+this.elementId).html(html);

            }

            componentLoaded(this.element);
        }
    },

    /*
     * SET component types
     */

    '#addComponentType click': function() {

        this.setComponentTypeCallback(false);

    },

    '.editComponentType click': function(el) {
        var id = {
            component_type_id: $(el).parents(".component_type_icon_wrap").data('component_type_id')
        };

        Component_types_model.get_component_type(id, this.callback('setComponentTypeCallback'));
    },

    setComponentTypeCallback: function(data) {

        if (data && data.message) {
            show_error(data.message);
            return;
        }

        var html = $.View(this.Class.defaults.viewpath+'set_component_type.tmpl', {
                our_data: data ? data.data : false
            }),
            msg = data === false ? lang.comp_type.add : lang.comp_type.upd;

        loadWindow('set_component_type', this.Class.defaults.wind_opt, msg, html);

        $('#set_component_type_window').component_type({
            data: data ? data.data : false,
            elementId: this.elementId
        });

    },

    /*
     * DELETE component types
     */

    '.deleteComponentType click': function(el){
        var id = {
            component_type_id: $(el).parents(".component_type_icon_wrap").data('component_type_id')
        };

        if(confirm(lang.comp_type.conf_remove+'?')){

            Component_types_model.delete_component_type(id, this.callback('componentTypeDeleted', el));

        }
    },

    componentTypeDeleted: function(el, data){

        if (data.message) {
            show_error(data.message);
        }

        if (data.success && data.data) {

            show_success(lang.removed);

            var tr = el.parents('tr');

            tr.slideUp(300, function(){

                tr.remove();

            });

            this.items--;

            this.refreshPaginator();

        } else if (data.message == false) {
            show_error('Delete error');
        }
    },

    /*
     * LIST MANIPULATION
     */

    getOffset: function(offset) {

        this.offset = offset;

        this.loadComponentTypes();

    },

    '.quantity click': function(el) {

        if (!$(el).hasClass('active')) {
            $(el).parents('div').find('.quantity').removeClass('active');
            $(el).addClass('active');

            this.limit = $(el).data('limit');
            this.offset = 0;

            this.refreshPaginator();

            this.loadComponentTypes();
        }

    },

    refreshPaginator: function() {

        if ($('#paginator', '#'+this.elementId).hasClass('paginator'))
            $('#paginator', '#'+this.elementId).paginator('destroy');

        this.paginatorOptions.items = this.items;

        this.paginatorOptions.itemsOnPage = this.limit;

        $('#paginator', '#'+this.elementId).paginator(this.paginatorOptions);

//        alert('paginatorInit');

     }

});