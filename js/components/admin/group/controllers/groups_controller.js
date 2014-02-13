$.Controller.extend('Groups',{
    defaults: {
        viewpath:'//components/admin/group/views/',
        pref: getPref(),
        wind_opt: {
            width: findWindowWidth()
        }
    }

},{
    init:function(selector) {

        this.elementId = this.element.attr('id');

        this.load_groups();

    },

    /*
     * ------------------------------------------------------------------------- Get groups
     */

    load_groups: function(){

        Groups_model.get_all_groups(this.callback('groupsLoaded'));

    },

    groupsLoaded: function(data){

        if (data.message) {
            show_error(data.message);
        }

        if (data.success) {

            var html = $.View(this.Class.defaults.viewpath+'all_groups.tmpl', {
                our_data: data,
                pref: this.Class.defaults.pref
            });

            $('#'+this.elementId).html(html);

        } else if (data.message == false) {
            show_error(lang.error);
        }


        componentLoaded(this.element);

    },

    /*
     * ------------------------------------------------------------------------- Set group
     */

    '.add_group click': function(el){

        this.setGroupCallback(false);

    },

    '.edit_group click': function(el){
        var id = {
            group_id: $(el).parents(".group_icon_wrap").data('group_id')
        };

        Groups_model.get_group(id, this.callback('setGroupCallback'));
    },

    setGroupCallback: function(response){

        if (response && response.message){
            show_error(response.message);
        }

        var message = response && response.data && typeof response.data != 'undefined' ? lang.group.upd : lang.group.add;

        loadWindow('group', this.Class.defaults.wind_opt,  message, $.View(this.Class.defaults.viewpath+'set_group.tmpl', {
            site_url: base_url,
            our_data: response ? response.data : false
        }));

        $('#group_window').group({
            data: response ? response.data : false,
            full_functionality:true,
            group_wrap: '#'+this.elementId,
            group_id: response && response.data && typeof response.data != 'undefined' ? response.data.id : false
        });

    },

    /*
     * ------------------------------------------------------------------------- Delete groups
     */

    '.delete_group click': function(el){
        var id = {
            group_id: $(el).parents(".group_icon_wrap").data('group_id')
        };

        if(confirm(lang.group.conf_remove+'?')){
            Groups_model.delete_group(id, this.callback('groupDeleted', el));
        }
    },

    groupDeleted: function(el, data){

        if (data.message) {
            show_error(data.message);
        }

        if (data.success === true) {

            show_success(lang.removed);

            el.parents('tr').slideUp(300, function(){
                el.parents('tr').remove();
            });

        } else if (data.message == false) {
            show_error(lang.error);
        }
    }

});