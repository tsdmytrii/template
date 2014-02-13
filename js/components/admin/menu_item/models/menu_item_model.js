$.Model('Menu_item_model', {

    set_menu_item: function(data, success){
        $.ajax({
            url: base_url+'admin/menu/set_menu_item',
            data: data,
            success: this.callback(success)
        });
    },


    disconect_menu_item: function(data, success) {
        $.ajax({
            url: base_url+'admin/components/disconect_menu_item',
            data: data,
            success: this.callback(success),
        });
    },


/*
 * ----------------------------------------------------------------------------- CHECK OLD FUNCTIONS
 */

    get_menu_item: function(data, success, error){
        $.ajax({
            url: base_url+'admin/menu/get_menu_item',
            type: 'post',
            dataType: 'json',
            data: data,
            success: this.callback(success),
            cache: false,
            error: this.callback(error)
        });
    },

    get_menu_item_for_parent: function(data, success, error){
        $.ajax({
            url: base_url+'admin/menu/get_menu_item_for_parent',
            type: 'post',
            dataType: 'json',
            data: data,
            success: this.callback(success),
            cache: false,
            error: this.callback(error)
        });
    },

    get_menu_item_by_block: function(data, success, error){
        $.ajax({
            url: base_url+'admin/menu/get_menu_item_by_block',
            type: 'post',
            dataType: 'json',
            data: data,
            success: this.callback(success),
            cache: false,
            error: this.callback(error)
        });
    },

    delete_menu_item: function(data, success, error){
        $.ajax({
            url: base_url+'admin/menu/delete_menu_item',
            type: 'post',
            dataType: 'json',
            data: data,
            success: this.callback(success),
            cache: false,
            error: this.callback(error)
        });
    },

    minus_menu_item: function(data, success, error){
        $.ajax({
            url: base_url+'admin/menu/minus_menu_item',
            type: 'post',
            dataType: 'json',
            data: data,
            success: this.callback(success),
            cache: false,
            error: this.callback(error)
        });
    },

    get_component_by_id: function(data, success, error){
        $.ajax({
            url: base_url+'admin/menu/get_component_by_id',
            type: 'post',
            dataType: 'json',
            data: data,
            success: this.callback(success),
            cache: false,
            error: this.callback(error)
        });
    }

}, {});