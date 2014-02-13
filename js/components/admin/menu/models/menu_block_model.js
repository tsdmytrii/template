$.Model('Menu_block_model', {

    set_menu_block: function(data, success) {
        $.ajax({
            url: base_url+'admin/menu/set_menu_block',
            data: data,
            success: this.callback(success)
        });
    }

}, {});