$.Model('Article_seo_model', {

    get_article_seo: function(data, success){
        $.ajax({
            url: base_url+'admin/articles/get_article_seo',
            data: data,
            success: this.callback(success)
        });
    },

    set_article_seo: function(data, success){
        $.ajax({
            url: base_url+'admin/articles/set_article_seo',
            data: data,
            success: this.callback(success)
        });
    }

}, {});