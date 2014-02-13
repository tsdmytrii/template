$.Model('UsersModel', {

    getUsers: function(success){
            $.ajax({
                    url:base_url+'admin/users/get_users',
                    success: this.callback(success)
            });
    },

    getGroups: function(success){
            $.ajax({
                    url:base_url+'admin/users/get_all_group',
                    success: this.callback(success)
            });
    }

}, {});