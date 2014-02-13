$.Controller.extend('Placeholders',{
    defaults: {
        viewpath:'//components/admin/placeholder/views/',
        window: {
            width: findWindowWidth()
        }
    }
},{
    init:function(selector, data)
    {
        console.log(data);
        this.elementId = $('.'+this.Class.fullName.toLowerCase()).attr('id');
        this.integrator_id = false;

        if (data && data.data && data.data[0]) {
            this.data = data.data[0].result;
        }
        if (data && data.integrator_id) {
            this.integrator_id = data.integrator_id;
        }

        this.loadPlaceholders(this.data);
    },

    loadPlaceholders: function(data)
    {
        if (data) {
            this.renderAllPlaceholders(data);
        } else {
            Placeholders_model.get_placeholders(this.callback('placeholdersLoaded'));
        }
    },

    renderAllPlaceholders: function (data)
    {
        var html = $.View(this.Class.defaults.viewpath+'all_placeholders.tmpl', {
            our_data: data,
            lang: lang
        });
        $('#placeholdersTableBody', + this.elementId).html(html);
    },

    placeholdersLoaded: function(data)
    {
        var html = $.View(this.Class.defaults.viewpath+'get_placeholders.tmpl', {
            our_data: data ? data.data : data
        });

        this.element.html(html);

        componentLoaded(this.element);
    },

    '#addPlaceholder click': function()
    {
        this.setPlaceholderCallback(false);
    },

    '.editPlaceholder click': function(el)
    {
        var id = $(el).parents('.placeholder_icon_wrap').data('placeholder_id');

        Placeholders_model.get_placeholder(id, this.callback('setPlaceholderCallback'));
    },

    setPlaceholderCallback: function(data)
    {
        if (data && data.message) {
            show_error('Код ошибки: '+data.message);
            return;
        }

        var html = $.View(this.Class.defaults.viewpath+'set_placeholder.tmpl', {
                our_data: data ? data.data : false,
                integrator_id: this.integrator_id,
                lang: lang
            }),
            obj = this;
        loadWindow('set_placeholder', this.options.window, lang.placeholder.set, html);

        $('#set_placeholder_window').placeholder({
            elementId: obj.elementId,
            className: this.Class.fullName.toLowerCase()
        });
    },

    '.deletePlaceholder click': function(el)
    {
        var id = $(el).parents('.placeholder_icon_wrap').data('placeholder_id');

        if (confirm(lang.placeholder.removePlaceholderConfirm)){
            Placeholders_model.delete_placeholder(id, this.callback('placeholderDeleted', el));
        }
    },

    placeholderDeleted: function(el, data)
    {
        if (data.success === true)
        {
            if (data && data.message) {
                show_error(data.message);
                return;
            }

            show_success(lang.placeholder.removeSuccess);

            var selector = $(el).parents('tr');

            selector.fadeOut(300, function(){
                selector.remove();
            });

        } else
            show_error(lang.error);
    },

    '.placeholderComponent click': function (el, ev)
    {
        var placeholder_id = $(el).data('placeholder_id');
        var placeholderData = this.getDataById( placeholder_id );

        if (placeholderData && placeholderData.msg)
        {
            show_error(placeholderData.msg);

        } else
        {
            this.miniBlocksControllerInit(placeholderData[0].mini_blocks, placeholder_id);
            this.productBlocksControllerInit(placeholderData[0].product_blocks, placeholder_id);
        }
    },

    miniBlocksControllerInit: function (data, placeholder_id)
    {
        var html = $.View(this.Class.defaults.viewpath+'index_mini_block.tmpl', {});
        $('#integratorMiniBlocks').html(html);

        $('#miniBlocksWrapper').mini_blocks({
            data: data,
            placeholder_id: placeholder_id,
            elementId: this.elementId
        });
    },

    productBlocksControllerInit: function (data, placeholder_id)
    {
        var html = $.View(this.Class.defaults.viewpath+'index_product_block.tmpl', {});
        $('#integratorProductBlocks').html(html);

        $('#productBlocksWrapper').product_blocks({
            data: data,
            placeholder_id: placeholder_id,
            elementId: this.elementId
        });
    },

    getDataById: function (id)
    {
        var result = jLinq.from(this.data).equals("id", id).select(function(rec) {
            if (rec) {
                return {
                    product_blocks: rec.product_blocks,
                    mini_blocks: rec.mini_blocks
                }
            } else {
                return false;
            }
        });

        return result;
    }
});