$.Controller.extend('Mini_blocks',{

    defaults: {
        viewpath:'//components/admin/mini_block/views/',
        pref: getPref(),
        window: {
            width: findWindowWidth()
        }
    }

},{
    init:function(selector, data)
    {
        this.elementId = $('.'+this.Class.fullName.toLowerCase()).attr('id');
        this.placeholder_id = false;

        if (data && data.data) {
            this.data = data.data.result;
            this.placeholder_id = data.placeholder_id;
            console.log(this.data);
        } else {
            this.data = false;
        }

        this.loadMiniBlocks(this.data);
    },

    loadMiniBlocks: function(data)
    {
        if (data) {
            this.renderAllMiniBlocks(data);
        } else {
            Mini_blocks_model.get_all_mini_blocks(this.callback('miniBlocksReceived'));
        }
    },

    renderAllMiniBlocks: function (data)
    {
        var html = $.View(this.Class.defaults.viewpath+'all_mini_blocks.tmpl', {
            our_data: data,
            lang: lang,
            pref: this.options.pref,
            placeholder_id: this.placeholder_id
        });
        $('#miniBlocksTableBody').html(html);

        componentLoaded(this.element);
    },

    miniBlocksReceived: function(data){

        if (data.message) {

            show_error('Код ошибки: '+data.message);

        } else {
            var html = $.View(this.Class.defaults.viewpath+'mini_blocks_list.tmpl', {
                our_data: data.data,
                pref: this.options.pref,
                site_url: base_url
            });

            this.element.html(html);
        }

        componentLoaded(this.element);
    },

    /*
     * Set mini_block
     */
    '#addMiniBlockButton click': function()
    {
        this.setMiniBlockCallback(false);
    },

    '.editMiniBlock click': function(el)
    {
        Mini_blocks_model.get_mini_block($(el).parents('.mini_block_icon_wrap').data('mini_block_id'), this.callback('setMiniBlockCallback'));
    },

    setMiniBlockCallback: function(response)
    {
        if (response && response.message) {
            show_error(response.message);
            return;
        }

        var html = $.View(this.Class.defaults.viewpath+'set_mini_block.tmpl', {
            our_data: response ? response.data : response,
            placeholder_id: this.placeholder_id,
            lang: lang,
            site_url: base_url,
            element_id: this.elementId
        });
        loadWindow('set_mini_block', this.options.window, lang.miniBlock.set, html);

        $('#set_mini_block_window').mini_block({
            component_type_id: response ? response.data.component.component_type_id : false,
            data: response ? response.data : false,
            edit: response ? true : false ,
            elementId: this.elementId,
            className: this.Class.fullName.toLowerCase()
        });
    },

    /*
     * Delete
     */

    '.deleteMiniBlock click': function(el)
    {
        var id = $(el).parents(".mini_block_icon_wrap").data('mini_block_id');

        if(confirm(lang.miniBlock.removeMiniBlockConfirmation)){
            Mini_blocks_model.delete_mini_block(id, this.callback('miniBlockDeleted', el));
        }
    },

    miniBlockDeleted: function(el, data)
    {
        if (data.success) {

            if (data.message) {
                show_error(data.message);
                return;
            }

            show_success(lang.miniBlock.miniBlockDeleteSuccess);

            var selector = $(el).parents('tr');

            selector.fadeOut(300, function(){
                selector.remove();
            });

        } else {
            show_error(lang.error);
        }
    },

    '.miniBlockComponent click': function (el, ev)
    {
        var mini_block_id = $(el).data('mini_block_id');
        var miniBlockData = this.getDataById( mini_block_id );

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