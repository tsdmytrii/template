$.Model('Groups_model', {

    get_all_groups: function(success){
        $.ajax({
            url: base_url+'admin/groups/get_all_group',
            success: this.callback(success)
        });
    },

    get_group: function(data, success){
        $.ajax({
            url: base_url+'admin/groups/get_group',
            data: data,
            success: this.callback(success)
        });
    },

    delete_group: function(data, success){
        $.ajax({
            url: base_url+'admin/groups/delete_group',
            data: data,
            success: this.callback(success)
        });
    }

}, {});