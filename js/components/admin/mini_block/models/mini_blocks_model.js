$.Model('Mini_blocks_model', {

    get_all_mini_blocks: function(success){
        $.ajax({
            url: base_url+'admin/layout_controller/get_all_mini_blocks',
            success: this.callback(success)
        });
    },

    delete_mini_block: function(id, success) {
        $.ajax({
            url: base_url+'admin/layout_controller/delete_mini_block',
            data: {
                id: id
            },
            success: this.callback(success)
        });
    },

    get_mini_block: function(id, success) {
        $.ajax({
            url: base_url+'admin/layout_controller/get_mini_block',
            data: {
                id: id
            },
            success: this.callback(success)
        });
    }

}, {});