$.Model('Languages_model', {

    get_all_languages: function(success){
        $.ajax({
            url: base_url+'admin/languages/get_all_languages',
            success: this.callback(success)
        });
    },

    get_language: function(data, success){
        $.ajax({
            url: base_url+'admin/languages/get_language',
            data: data,
            success: this.callback(success)
        });
    },

    delete_language: function(data, success){
        $.ajax({
            url: base_url+'admin/languages/delete_language',
            data: data,
            success: this.callback(success)
        });
    }

}, {});