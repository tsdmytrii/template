$.Controller.extend('Group',{
    defaults: {
        viewpath:'js/components/admin/group/views/'
    }

},{

    init: function(element, options) {

        this.elementId = this.element.attr('id');

        this.groupId = 0;

        if(typeof this.options.full_functionality == 'undefined'){
            this.options.full_functionality = false;
        }

        if (this.options.data) {
            this.groupId = this.options.data.id;
        }

        $('.groupForm').validate({
            rules:{
                name:{
                    minlength:2,
                    maxlength: 200,
                    required:true
                },
                clear_name:{
                    minlength:2,
                    maxlength: 200,
                    required:true
                },
                description:{
                    minlength:5,
                    maxlength: 1000,
                    required:true
                }
            },
            messages: {
                name: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                clear_name: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                },
                description: {
                    required: lang.validation.required,
                    minlength: lang.validation.minlength,
                    maxlength: lang.validation.maxlength
                }
            },
            highlight: function(element){
                $(element).parents('.form-group').addClass('has-error');
            },
            unhighlight: function(element){
                $(element).parents('.form-group').removeClass('has-error').find('.help').empty();
            },
            errorPlacement: function(error, element){
                element.parents('.form-group').find('.help').append(error).animate({
                    opacity: "1"
                }, 1000);
            }
        });

        this.getComponentFunctions();

    },

    '.groupForm submit': function(el, ev) {
        ev.preventDefault();

        if(el.valid() !== false){
            Group_model.set_group(el.serialize(), this.callback('group_saved'));
        }
    },

    group_saved: function(response) {
        if (response.message !== false) {
            show_error(response.message);
        }

        if(response.success == true){

            $('.group_id', '#'+this.elementId).val(response.data);

            this.groupId = response.data;

            if(this.options.full_functionality !== false){

                $(this.options.group_wrap).controller().load_groups();

            }

            show_success(lang.saved);

        } else if (data.message == false) {

            show_error(lang.error);

        }
    },

    /*
     * Group permissions
     */

    getComponentFunctions: function(){

        Group_model.get_component_functions({
            group_id: this.groupId
        }, this.callback('componentFunctionsGeted'));

    },

    componentFunctionsGeted: function(data) {

        if (data.message) {
            show_error(data.message);
        }

        if (data.success) {
            var html = $.View(base_url+this.Class.defaults.viewpath+'set_perm.tmpl', {
                our_data: data.data
            });

            $('#permissionWrap').html(html);
        } else if (data.message == false) {

            show_error(lang.error);

        }


    },

    '.allPermissions click': function(el) {
        var $el = $(el);

        if ($el.is(':checked')) {

            $el.parents('ul').find('.component_function_id').each(function(){
                $(this).attr('checked', 'checked');
            });

        } else {

            $el.parents('ul').find('.component_function_id').each(function(){
                $(this).removeAttr('checked');
            });

        }
    },

    '#permissionForm submit': function(el, ev){

        ev.preventDefault();

        if(el.valid() !== false){

            Group_model.set_permissions(el.serialize()+'&group_id='+this.groupId, this.callback('pervissionSaved'));

        }

    },

    pervissionSaved: function(response) {

        if (response.message) {
            show_error(response.message);
        }

        if (response.success == true) {

            show_success(lang.saved);

        } else if (response.message == false)
            show_error(lang.error);

    }

});