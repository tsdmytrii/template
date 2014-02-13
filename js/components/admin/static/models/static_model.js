$.Model('Static_model', {

    get_static: function(data, success){
        $.ajax({
            url: base_url+'admin/statics/get_static_component',
            data: data,
            success: this.callback(success)
        });
    },

    set_static_component: function(data, success){
        $.ajax({
            url: base_url+'admin/statics/set_static_component',
            data: data,
            success: this.callback(success)
        });
    }

}, {});