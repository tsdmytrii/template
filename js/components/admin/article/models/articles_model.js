$.Model('Articles_model', {

    get_all_article: function(data, success){
        $.ajax({
            url: base_url+'admin/articles/get_all_article',
            data: data,
            success: this.callback(success)
        });
    },

    get_article: function(data, success){
        $.ajax({
            url: base_url+'admin/articles/get_article',
            data: data,
            success: this.callback(success)
        });
    },

    delete_article: function(data, success){
        $.ajax({
            url: base_url+'admin/articles/delete_article',
            data: data,
            success: this.callback(success)
        });
    }

}, {});