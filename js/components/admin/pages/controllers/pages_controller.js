$.Controller.extend('Pages',{
    defaults: {
        viewpath:'//components/admin/pages/views/',
        window: {
            width: findWindowWidth()
        }
    }
},{

    init:function() {

        this.elementId = '#'+this.element.attr('id');

        Pages_model.get_component_types(this.callback('componentTypesLoaded'));

        var html = $.View(this.options.viewpath+'index.tmpl', {}),
            obj = this;

        this.element.append(html);

        dataTableBootstrap();

        $('#pages_table', this.elementId).dataTable({
            'bAutoWidth': false,
            'bPaginate': true,
            'bLengthChange': true,
            'bInfo': true,
            'iDisplayLength': 10,
            "sPaginationType": "full_numbers",
            "aaSorting": [[0,'desc']],
            "sPaginationType": "bootstrap",
            "aoColumns": [
                null,
                null,
                { "bSortable": false, "aTargets": [ 0 ] },
                { "bSortable": false, "aTargets": [ 0 ] },
                { "bSortable": false, "aTargets": [ 0 ] }
            ],


            "bServerSide": true,
            "sAjaxSource": base_url+"admin/components/get_components",
            "sServerMethod": "POST",
            "fnServerData": function(sSource, aoData, fnSuccess, oSettings) {
                oSettings.jqXHR = $.ajax({
                    "dataType": 'json',
                    "type": "POST",
                    "url": sSource,
                    "data": aoData,
                    "success": function(data) {

                        var aaData = data.data;
                        if (aaData.aaData) {
                            aaData.aaData = jlinq.from(aaData.aaData).select(function(result){
                                var r = [],
                                    menu = '';

                                if (result.menu_item) {
                                    for (var i = 0, ln = result.menu_item.length; i < ln; i++) {
                                        menu += result.menu_item[i].lang.value+'<span class="btn btn-default btn-xs" style="margin: 0 15px 0 5px;"><i data-menu_item_id="'+result.menu_item[i].id+'" title="'+lang.page.dis_connect_m_i+'" class="glyphicon glyphicon-resize-full disconect_menu_item"></i></div>';
                                    }
                                }

                                r.push(result.id);
                                r.push('<a class="component_name new_component" title="'+result.name+'" href="'+base_url+'admin/'+result.component_type_id+'/'+result.component_type.name+'/'+result.content_id+'">'+result.name+'</a>');
                                r.push(result.component_type.psevdo_name);
                                r.push(menu);
                                r.push(
                                '<div class="component_icon_wrap" data-component_id="'+result.id+'">\
                                    <div class="btn btn-xs btn-default"><i title="'+lang.page.connect_m_i+'" class="glyphicon glyphicon-resize-small conect_menu_item"></i></div>\
                                    <div class="btn btn-xs btn-default"><i title="'+lang.page.upd+'" class="glyphicon glyphicon-pencil edit_component"></i></div>\
                                    <div class="btn btn-xs btn-danger"><i title="'+lang.page.remove+'" class="glyphicon glyphicon-trash delete_component"></i></div>\
                                </div>');

                                return r;
                            });
                        }

                        fnSuccess(aaData);
                    }
                });
                componentLoaded(obj.element);
            }

        });

    },

    componentTypesLoaded: function(data){

        this.component_types = data.data;

    },

    load_components: function(){

        var oTable = $('#pages_table').dataTable();

        oTable.fnDraw();
    },

    /*
     * SET component
     */

    '#add_component click': function(el){
        this.setComponentCallback(false);
    },

    '.edit_component click': function(el){
        var component_id = $(el).parents('.component_icon_wrap').data('component_id');

        Pages_model.get_component_by_id({
            component_id: component_id
        }, this.callback('setComponentCallback'));
    },

    setComponentCallback: function(data) {

        if (data && data.message) {
            show_error(data.message);
            return;
        }

        var msg = data ? lang.page.upd : lang.page.add,
            html = $.View(this.Class.defaults.viewpath+'set_component.tmpl', {
                types: this.component_types,
                our_data: data ? data.data : false,
                event: msg
            });

        loadWindow('set_component', this.options.window, msg, html);

        $('#set_component_window').page({
            data: data ? data.data : false,
            elementId: this.elementId
        });

    },

    /*
     * DELETE component
     */

    '.delete_component click': function(el) {
        var component_id = $(el).parents('.component_icon_wrap').data('component_id'),
        obj = this;
        if (confirm('Вы действительно хотите удалить страницу '+$(el).parents('tr').find('.component_name').text()+'?')){
            Pages_model.delete_component({
                component_id: component_id
            }, this.callback('componentDeleted', el));
        }

    },

    componentDeleted: function(el, data) {
        if (data.message) {
            show_error(data.message);
            return;
        }

        if (data.success == true) {

            show_success(lang.removed);

            var oTable = $('#pages_table').dataTable();

            $(el).parents('tr').fadeOut(300, function(){
                oTable.fnDeleteRow($(el).parents('tr'));
            });

        } else if (data.message == false)
            show_error(lang.error);
    },

    /*
     * MENU ITEM connect
     */

    '.conect_menu_item click': function(el){
        var component_id = $(el).parents('.component_icon_wrap').data('component_id'),
            page = $(el).parents('tr').find('.component_name').text(),
            html = $.View(this.Class.defaults.viewpath+'conect_menu_item.tmpl', {
                component_id: component_id
            });

        loadWindow('set_menu_item', this.options.window, lang.page.relate+'"'+page+'"'+lang.page.with_m, html);

        $('#set_menu_item_window').page({
            data: false,
            elementId: this.elementId
        });

    },

    /*
     * MENU ITEM dis-connect
     */

    '.disconect_menu_item click': function(el){
        var component_id = $(el).parents('td').data('component_id'),
        menu_item_id = $(el).data('menu_item_id');

        if (confirm('Вы действительно хотите отвязать пункт меню?')) {
            Pages_model.disconect_menu_item({
                component_id: component_id,
                menu_item_id: menu_item_id
            }, this.callback('disConectedMI'));
        }

    },

    disConectedMI: function(data){
        if (data.message) {
            show_error(data.message);
        }

        if (data.success == true) {

            show_success(lang.page.dis_connected_m_i);
            this.load_components();

        } else if (data.message == false)
            show_error(lang.error);
    }

});