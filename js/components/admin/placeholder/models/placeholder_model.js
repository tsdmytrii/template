$.Model('Placeholder_model', {

    set_placeholder: function(data, success){
        $.ajax({
            url: base_url+'admin/placeholders/set_placeholder',
            data: data,
            success: this.callback(success),
        });
    },

    set_placeholder_attribute: function(data, success){
        $.ajax({
            url: base_url+'admin/placeholders/set_placeholder_attribute',
            data: data,
            success: this.callback(success)
        });
    },

    delete_placeholder_attribute: function(id, success){
        $.ajax({
            url: base_url+'admin/placeholders/delete_placeholder_attribute',
            data: {
                placeholder_attribute_id: id
            },
            success: this.callback(success)
        });
    },

    set_mini_block: function(data, success){
        $.ajax({
            url: base_url+'admin/placeholders/set_mini_block',
            data: data,
            success: this.callback(success)
        });
    },

    delete_miniblock: function(data, success){
        $.ajax({
            url: base_url+'admin/placeholders/delete_miniblock',
            data: data,
            success: this.callback(success)
        });
    },

    set_product_block: function(data, success){
        $.ajax({
            url: base_url+'admin/placeholders/set_product_block',
            data: data,
            success: this.callback(success)
        });
    },

    delete_product_block: function(data, success){
        $.ajax({
            url: base_url+'admin/placeholders/delete_product_block',
            data: data,
            success: this.callback(success)
        });
    }

}, {});