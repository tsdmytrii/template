$.Model('Seo_model', {

    get_seo: function(success){
        $.ajax({
            url: base_url+'admin/seos/get_seo',
            success: this.callback(success)
        });
    },

    set_seo: function(data, success){
        $.ajax({
            url: base_url+'admin/seos/set_seo',
            data: data,
            success: this.callback(success)
        });
    }

}, {});