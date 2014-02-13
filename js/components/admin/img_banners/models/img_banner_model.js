$.Model('Img_banner_model', {

    set_banner: function(data, success){
        $.ajax({
            url: base_url+'admin/banners/set_banner',
            data: data,
            success: this.callback(success)
        });
    },

    delete_banner_img: function(data, success){
        $.ajax({
            url: base_url+'admin/banners/delete_img_banner',
            data: data,
            success: this.callback(success)
        });
    }

}, {});