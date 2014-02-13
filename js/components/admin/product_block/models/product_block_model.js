$.Model('Product_block_model', {

    set_product_block: function(data, success){
        $.ajax({
            url: base_url+'admin/product_block_controller/set_product_block',
            data: data,
            success: this.callback(success)
        });
    },

    set_product_relation: function(data, success){
        $.ajax({
            url: base_url+'admin/product_block_controller/set_product_relation',
            data: data,
            success: this.callback(success)
        });
    },

    delete_product_relation: function(data, success){
        $.ajax({
            url: base_url+'admin/product_block_controller/delete_product_relation',
            data: data,
            success: this.callback(success)
        });
    }


}, {});