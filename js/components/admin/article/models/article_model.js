$.Model('Article_model', {

    set_article: function(data, success){
        $.ajax({
            url: base_url+'admin/articles/set_article',
            data: data,
            success: this.callback(success)
        });
    },

    delete_article_img: function(data, success, error){
        $.ajax({
            url: base_url+'files/delete_article_image',
            type: 'post',
            dataType: 'json',
            data: data,
            success: this.callback(success),
            cache: false,
            error: this.callback(error)
        });
    },

}, {});