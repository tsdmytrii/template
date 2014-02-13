$.Controller.extend('Product_blocks',{

    defaults: {
        viewpath:'//components/admin/product_block/views/',
        lang_id: 2,
        pref: 'ru',
        wind_opt: {
            width: 550,
            height: 480,
            minimize: true,
            maximise: true,
            resize: true,
            status_bar: true,
            modal: true
        }
    }

},{
    init:function(selector, data)
    {
        this.elementId = $('.'+this.Class.fullName.toLowerCase()).attr('id');
        if (data && data.data) {
            this.data = data.data;
        } else {
            this.data = false;
        }

        this.loadProductBlocks(this.data);
    },

    loadProductBlocks: function(data)
    {
        if (data) {
            this.renderAllProductBlocks(data);
        } else {
            Product_blocks_model.get_all_product_blocks(this.callback('productBlocksReceived'));
        }
    },

    renderAllProductBlocks: function (data)
    {
        var html = $.View(this.Class.defaults.viewpath+'all_product_blocks.tmpl', {
            our_data: data,
            lang: lang,
            pref: this.Class.defaults.pref
        });
        $('#productBlocksTableBody').html(html);
    },

    productBlocksReceived: function(data)
    {
        var html = $.View(this.Class.defaults.viewpath+'index.tmpl', {});
        this.element.append(html);

        if (data.message) {

            show_error('Код ошибки: '+data.message);

        } else {

            var html = $.View(this.Class.defaults.viewpath+'product_blocks_list.tmpl', {
                our_data: data.data,
                pref: this.Class.defaults.pref,
                site_url: base_url
            });

            $('#productBlockContent').html(html);

        }

        componentLoaded(this.element);
    },

    /*
     * Set product_block
     */
    '.addProductBlock click': function(){
        this.setProductBlockCallback();
    },

    '.editProductBlock click': function(el){

        Product_blocks_model.get_product_block($(el).parents('.product_block_icon_wrap').data('product_block_id'), this.callback('setProductBlockCallback'));

    },

    setProductBlockCallback: function(response){

        if (response && response.message) {
            show_error('Код ошибки: '+response.message);
            return;
        }

        var html = $.View(this.Class.defaults.viewpath+'set_product_block.tmpl', {

            our_data: response ? response.data : response,
            lang: lang,
            site_url: base_url,
            element_id: this.elementId

        });

        loadWindow('set_product_block', this.Class.defaults.wind_opt, 'Задать блок', html);

        $('#set_product_block_window').product_block({
            data: response ? response.data : false,
            edit: response ? true : false ,
            elementId: this.elementId,
            className: this.Class.fullName.toLowerCase()
        });

    },

    /*
     * Delete
     */

    '.deleteProductBlock click': function(el){
        var id = $(el).parents(".product_block_icon_wrap").data('product_block_id');

        if(confirm('Вы действительно хотите удалить мини блок?')){
            Product_blocks_model.delete_product_block(id, this.callback('productBlockDeleted', el));
        }
    },

    productBlockDeleted: function(el, data){

        if (data.success) {

            if (data.message) {
                show_error('Код ошибки: '+data.message);
                return;
            }

            show_success('Успешно удалено');

            var selector = $(el).parents('tr');

            selector.fadeOut(300, function(){
                selector.remove();
            });

        } else {
            show_error('Ошибка');
        }

    }

});