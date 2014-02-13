$.Model('Placeholders_model', {

    get_placeholder: function(id, success){
        $.ajax({
            url: base_url+'admin/placeholders/get_placeholder',
            data: {
                id: id
            },
            success: this.callback(success)
        });
    },

    get_placeholders: function(success){
        $.ajax({
            url: base_url+'admin/placeholders/get_placeholders',
            success: this.callback(success)
        });
    },

    delete_placeholder: function(id, success){
        $.ajax({
            url: base_url+'admin/placeholders/delete_placeholder',
            data: {
                placeholder_id: id
            },
            success: this.callback(success)
        });
    }

}, {});