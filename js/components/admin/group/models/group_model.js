$.Model('Group_model', {

    set_group: function(data, success){
        $.ajax({
            url: base_url+'admin/groups/set_group',
            data: data,
            success: this.callback(success)
        });
    },

    get_component_functions: function(data, success){
        $.ajax({
            url: base_url+'admin/groups/get_component_functions',
            data: data,
            success: this.callback(success)
        });
    },

    set_permissions: function(data, success){
        $.ajax({
            url: base_url+'admin/groups/set_permissions',
            data: data,
            success: this.callback(success)
        });
    }

}, {});