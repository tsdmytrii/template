$.Model('Product_blocks_model', {

    get_all_product_blocks: function(success){
        $.ajax({
            url: base_url+'admin/product_block_controller/get_all_product_blocks',
            success: this.callback(success)
        });
    },

    delete_product_block: function(id, success) {
        $.ajax({
            url: base_url+'admin/product_block_controller/delete_product_block',
            data: {
                id: id
            },
            success: this.callback(success)
        });
    },

    get_product_block: function(id, success) {
        $.ajax({
            url: base_url+'admin/product_block_controller/get_product_block',
            data: {
                id: id
            },
            success: this.callback(success)
        });
    }

}, {});