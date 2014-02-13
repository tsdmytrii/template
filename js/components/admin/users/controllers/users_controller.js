$.Controller('Users', {
    defaults: {
        viewpath: '//components/admin/users/views/',
        wind_opt: {
            width: findWindowWidth()
        }
    }
}, {
    //param full_functionality boolean, if true, renders view on ceter column, if false - just listens to events on adding_user, updating user

    init: function(element, options) {

        this.elementId = this.element.attr('id');

        if (typeof this.options.full_functionality == 'undefined') {
            this.options.full_functionality = true;
        }

        if (this.options.full_functionality === true) {

            this.element.html($.View(this.options.viewpath + 'index.tmpl', {}));

            UsersModel.getGroups(this.callback('groupsLoaded'));

            this.loadUsers();
        }
    },

    groupsLoaded: function(data) {

        if (data && data.message) {
            show_error(data.message);
        }

        if (data && data.data) {
            this.groups = data.data;
        }

    },

    /*
     * Get user functions
     */

    loadUsers: function() {
        UsersModel.getUsers(this.callback('usersLoaded'));
    },

    usersLoaded: function(response) {

        if (response && response.message) {
            show_error(response.message);
        }

        if (response.success) {
            var obj = this;

            var center_column = $.View(this.options.viewpath + 'center_column.tmpl', {
                viewpath: obj.options.viewpath,
                users: response.data.users
            });

            $('#usersContent').html(center_column);

        } else if (response.message == false) {
            show_error(lang.error);
        }

        componentLoaded(this.element);

    },

    /*
     * Set user functions
     */

    '#add_user click': function() {
        this.setUserCallback(false);
    },

    '.edit_user click': function(el) {
        var id = el.attr('id').split('_')[2];
        UserModel.getUser(id, this.callback('setUserCallback'));
    },

    setUserCallback: function(response) {
        if (response && response.message) {
            show_error(response.message);
            return;
        }

        var html = $.View(this.options.viewpath + 'set_user.tmpl', {
                user: response ? response.data : false,
                groups: this.groups,
                pref: 'ru'
            });

        if (response && response.message) {
            show_error("Код ошибки: "+response.message);

            return;
        }

        loadWindow('set_user', this.options.wind_opt, 'Пользователь', html);

        $('#set_user_window').user_widget({
            full_functionality: this.options.full_functionality,
            data: response ? response.data : false,
            elementId: this.elementId
        });

    },

    /*
     * Delete user functions
     */

    '.delete_user click': function(el) {
        //alert($.dump(this.options))
        if (this.options.full_functionality == true) {

            var id = el.attr('id').split('_')[2];

            if (confirm(lang.user.conf_remove+'"' + el.parents('tr').children('td:eq(1)').text() + '"'))
                UserModel.deleteUser(id, this.callback('userDeleted', el));

        }

    },

    userDeleted: function(el, response) {

        if (response.message) {
            show_error(response.message);
        }

        if (response.success == true) {


            show_success('User deleted successfully');

            $(el).parents('tr').fadeOut(300, function(){

                $(el).parents('tr').remove();

            });

        } else if (response.message) {
            show_error(lang.error);
        }
    }

});