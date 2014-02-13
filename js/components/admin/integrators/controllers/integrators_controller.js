$.Controller.extend('Integrators',{
    defaults: {
        viewpath:'//components/admin/integrators/views/',
        pref: getPref(),
        window: {
            width: findWindowWidth()
        }
    }
},{

    init:function()
    {
        this.elementId = this.element.attr('id');
        this.data = false;
        this.getPlaceholders = true;

        this.loadIntegrators();

        var html = $.View(this.options.viewpath+'index.tmpl', {}),
            obj = this;
        this.element.append(html);
    },

    loadIntegrators: function ()
    {
        Integrators_model.get_integrators({
            get_placeholders: this.getPlaceholders
        }, this.callback('integratorsLoaded'));
    },

    integratorsLoaded: function(data)
    {
        this.data = data.data;

        if (data.message)
        {
            show_error(data.message);

        } else
        {
            if (data.success)
            {
                var html = $.View(this.Class.defaults.viewpath+'all_integrators.tmpl', {
                    our_data: data.data,
                    lang: lang,
                    pref: this.Class.defaults.pref
                });
                $('#integratorsTableBody', '#'+this.elementId).html(html);

            } else if (data.message == false)
            {
                show_error(lang.error);
            }

            componentLoaded(this.element);
        }
    },

    'integratorSaved subscribe': function ()
    {
        this.loadIntegrators();
    },

    '#addIntegrator click': function(el)
    {
        this.setIntegratorCallback(false);
    },

    '.editIntegrator click': function(el, ev)
    {
        ev.stopPropagation();

        var id = $(el).parents('.integrator_icon_wrap').data('id');

        Integrators_model.get_integrators({
            id: id,
            get_placeholders: true
        }, this.callback('setIntegratorCallback'));
    },

    setIntegratorCallback: function(data)
    {
        if (data && data.message)
        {
            show_error(data.message);
            return;
        }

        var msg = data ? lang.page.upd : lang.page.add,
            html = $.View(this.Class.defaults.viewpath+'set_integrator.tmpl', {
                our_data: data ? data.data : false,
                event: msg
            });
        loadWindow('setIntegrator', this.options.window, msg, html);

        $('#setIntegrator_window').integrator({
            data: data ? data.data : false,
            elementId: this.elementId
        });
    },

    '.deleteIntegrator click': function(el, ev)
    {
        ev.stopPropagation();
        var id = $(el).parents('.integrator_icon_wrap').data('id'),
        obj = this;
        if (confirm('Вы действительно хотите удалить '+$(el).parents('tr').find('.integratorName').text()+'?')){
            Integrators_model.delete_integrator({
                id: id
            }, this.callback('componentDeleted', el));
        }
    },

    componentDeleted: function(el, data)
    {
        if (data.message) {
            show_error(data.message);
            return;
        }

        if (data.success == true) {

            show_success(lang.removed);

            $(el).parents('tr').fadeOut(300, function(){
                oTable.fnDeleteRow($(el).parents('tr'));
            });

        } else if (data.message == false)
            show_error(lang.error);
    },


    /**
     * Integrator Components' functions
     */

    '.integratorComponent click': function (el, ev)
    {
        var integrator_id = $(el).data('id');
        var placeholderData = this.getPlaceholderById( integrator_id );

        if (placeholderData && placeholderData[0].msg)
        {
            show_error(placeholderData[0].msg);

        } else
        {
            var html = $.View(this.Class.defaults.viewpath+'index_placeholder.tmpl', {});
            $('#integratorPlaceholders', '#'+this.elementId).html(html);

            $('#integratorMiniBlocksBody', '#'+this.elementId).html(false);
            $('#integratorProductBlocksBody', '#'+this.elementId).html(false);

            $('#placeholdersWrapper', '#'+this.elementId).placeholders({
                data: placeholderData,
                integrator_id: integrator_id,
                elementId: this.elementId
            });
        }
    },

    getPlaceholderById: function (id)
    {
        var result = jLinq.from(this.data).equals("id", id).select(function(rec) {
            if (rec && rec.placeholders) {
                return rec.placeholders;
            } else {
                return false;
            }
        });

        return result;
    }
});