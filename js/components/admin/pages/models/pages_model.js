$.Model('Pages_model', {

    set_component: function(data, success){
        $.ajax({
            url: base_url+'admin/components/set_component',
            data: data,
            success: this.callback(success)
        });
    },

    get_component_types: function(success){
        $.ajax({
            url: base_url+'admin/components/get_display_component_types',
            success: this.callback(success)
        });
    },

    get_components: function(data, success){
        $.ajax({
            url: base_url+'admin/components/get_components',
            data: data,
            success: this.callback(success)
        });
    },

    get_component_by_id: function(data, success){
        $.ajax({
            url: base_url+'admin/components/get_component_by_id',
            data: data,
            success: this.callback(success)
        });
    },

    delete_component: function(data, success){
        $.ajax({
            url: base_url+'admin/components/delete_component',
            data: data,
            success: this.callback(success)
        });
    },

    set_conect_menu_item: function(data, success) {
        $.ajax({
            url: base_url+'admin/components/set_conect_menu_item',
            data: data,
            success: this.callback(success)
        });
    },

    disconect_menu_item: function(data, success) {
        $.ajax({
            url: base_url+'admin/components/disconect_menu_item',
            data: data,
            success: this.callback(success)
        });
    }

}, {});