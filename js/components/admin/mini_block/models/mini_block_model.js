$.Model('Mini_block_model', {

    set_mini_block: function(data, success){
        $.ajax({
            url: base_url+'admin/layout_controller/set_mini_block',
            data: data,
            success: this.callback(success)
        });
    },

    delete_img: function(data, success){
        $.ajax({
            url: base_url+'logo_controller/delete_mini_block_img',
            data: data,
            success: this.callback(success)
        });
    },

    delete_tooltip: function(data, success){
        $.ajax({
            url: base_url+'logo_controller/delete_mini_block_tooltip',
            data: data,
            success: this.callback(success)
        });
    }

}, {});