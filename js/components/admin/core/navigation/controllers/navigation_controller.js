$.Controller('Navigation',{

    currentHistoryStep: 0,
    nextLinks: [],
    nextOpenedWindows: [],
    prevOpenedWindows: [null, null],
    quantWindows: 0,
    currentUrl: '',
    currentTitel: '',
    currentHistoryId: '',
    flightControllers : [],
    registeredComponents: [],

    addCookies: function(compObj){

        if (Navigation.isSuchCookie(compObj) == true){

            return;
        }

        var openedComponents ='';
        var cookieName = 'openedComponents';
        if ($.cookie(cookieName)) {
            openedComponents = $.cookie(cookieName);
        }

        openedComponents += compObj.component_id+';'+compObj.title+';'+compObj.href+'|';

        $.cookie(cookieName, openedComponents);

    },

    isSuchCookie: function(checkedCookie){
        var cookieArray = $.cookie('openedComponents') ? $.cookie('openedComponents').split(/\|/) : false;
        var isSuchCookie = false;

        if (cookieArray) {
            $.each(cookieArray, function(){
                if (this.split(';')[0] == checkedCookie.component_id)
                    isSuchCookie = true;
            });
        }

        return isSuchCookie;

    },

    showComponent: function(idElem, title, fromHistory){

        if (fromHistory == undefined)
            fromHistory = false;

        if ($('a[href="'+idElem+'"]').attr('title') !== title)
            $('a[href="'+idElem+'"]').html(title+'<i class="glyphicon glyphicon-remove-sign close_tab"></i>')

        if (fromHistory == false) {
            if (!$('a[href="'+idElem+'"]').parents('li').is(':visible'))
                $('a[href="'+idElem+'"]').parents('li').fadeIn();

            $('#'+idElem).data("display", "block");

            $('a[href="'+idElem+'"]').parents('li').click();

        } else {
            $.each($('.tab_component_content'), function(){
                $(this).data('display', 'none');
            });

            $('#'+idElem).data('display', 'block');

            $('#work_space .nav_tab').removeClass('active');
            $('a[href="'+idElem+'"]').parents('li').addClass('active');

            $('.tab_component_content').removeClass('curr');
            $('#'+idElem).addClass('curr');
        }

        OpenAjax.hub.publish('component.loaded');
    },

    addComponentFromRegistered: function(suchComponent){

        suchComponent.component_id = suchComponent.id;

        var href = 'component-'+suchComponent.id;

        $('a[href="'+href+'"]', '#tab_navigation').parents('li').show();

        $('#'+href).data('closed', 'opened');

        Navigation.addCookies(suchComponent);

    },

    deleteFromCache: function(componentId){


        var openedWindows = $.cookie('openedComponents').split(/\|/);

        var newCookie = '';

        $.each(openedWindows, function(){

            if(this.split(';')[0] != componentId)
                newCookie += this+'|';


        });
        if(newCookie[newCookie.length-1]=='|'){

            newCookie = newCookie.substr(0, newCookie.length-1);

        }
        $.cookie('openedComponents', newCookie);

    },

    deleteComponent: function(id){
        var href = 'component-'+id;
        $('a[href="'+href+'"]', '#tab_navigation').parents('li').hide();

        $('#'+href).data('closed', 'closed');

        Navigation.deleteFromCache(id);
    }
},
{
    init: function(el, options){

        window.onpopstate = function(event) {

            //            var pageLoc = ''+document.location;
            var back = false;
            var willComponents, wasComponents;

            $('title').html(event.state.title);

            if(event.state.history_step >= 1){

                if(Navigation.currentHistoryStep - event.state.history_step>0){
                    back = true;
                    wasComponents = Navigation.nextOpenedWindows[Navigation.currentHistoryStep];
                    Navigation.currentHistoryStep = event.state.history_step;
                    willComponents = Navigation.nextOpenedWindows[Navigation.currentHistoryStep];

                }
                else{

                    wasComponents = Navigation.nextOpenedWindows[Navigation.currentHistoryStep];
                    Navigation.currentHistoryStep = event.state.history_step;
                    willComponents = Navigation.nextOpenedWindows[Navigation.currentHistoryStep];

                }

                var componentsToOpen = [],
                componentsToClose = [],
                componentToShow;

                $(willComponents).each(function(){

                    var suchComponent = jlinq.from(wasComponents).equals("id", this.id).select();
                    var closed='closed';
                    if(suchComponent.length!=0)
                        closed=suchComponent[0].closed;


                    if(this.closed=='opened'&&closed=='closed'){

                        componentsToOpen[componentsToOpen.length] = this;

                    }

                });

                $(wasComponents).each(function(){

                    var suchComponent = jlinq.from(willComponents).equals("id", this.id).select();
                    var closed = true;
                    if(suchComponent.length>0)
                        closed = suchComponent[0].closed;
                    if(this.closed=='opened'&&suchComponent.length==0||(this.closed=='opened'&&closed=='closed')){

                        componentsToClose[componentsToClose.length] = this;

                    }

                });

                //alert('to_open'+$.dump(componentsToOpen));

                $(componentsToOpen).each(function(){

                    var suchComponent = jlinq.from(Navigation.registeredComponents).equals("id", this.id).select()[0];

                    Navigation.addComponentFromRegistered(suchComponent);

                });
                //alert('to_close'+$.dump(componentsToClose));
                $(componentsToClose).each(function(){

                    Navigation.deleteComponent(this.id, true);

                });

                $(willComponents).each(function(){

                    if(this.closed=='opened'&&this.display=='block'){

                        componentToShow = this.id;

                        var title = '';

                        $.each(Navigation.registeredComponents, function(){
                            if (this.id == componentToShow)
                                title = this.title;
                        });

                        Navigation.showComponent('component-'+this.id, title, true);
                    }

                });

                if(Navigation.currentHistoryStep==1&&back==true){

                    alert("Вы достигли первой страницы истории Вашей навигации,\n при дальнейшем нажатии на кнопку назад вы окажитесь\n на другом ресурсе!");

                }
            }

        };

        $.cookie.defaults = {
            path: '/'
        };

        Navigation_model.get_componen_types(this.callback('componentTypesLoaded'), this.callback('error'));

    },

    componentTypesLoaded: function(data){
        component_types = data.data;

        this.setComponentsArray();
    },

    setComponentsArray: function(){
        var componentArray = new Array();
        var cookieName = 'openedComponents';

        if($.cookie(cookieName)) {
            var cookiesComponents = $.cookie(cookieName).split(/\|/);

            $(cookiesComponents).each(function(){

                var suchInComponents = jlinq.from(componentArray).equals("id", this.split(';')[0]).select();
                if(suchInComponents.length==0 && this.length!=0){

                    componentArray[componentArray.length]={
                        id: this.split(';')[0],
                        title: this.split(';')[1],
                        href: this.split(';')[2],
                        source: 'cookie'
                    }

                }

            });
        }

        this.component_link = document.location.href.split(base_url+'admin/')[1];

        if (this.component_link !== undefined && this.component_link.length > 0) {

            if (this.component_link.indexOf('\/') === -1) {
                suchInComponents = jlinq.from(componentArray).equals("id", this.component_link).select();
            } else {
                suchInComponents = jlinq.from(componentArray).equals("id", this.component_link.replace(/[\/]+/g,'-')).select();
            }

            if(suchInComponents.length == 0) {
                componentArray[componentArray.length] = {
                    id: this.component_link.indexOf('\/') === -1 ? this.component_link : this.component_link.replace(/[\/]+/g,'-'),
                    title: $('a[href="'+base_url+'admin/'+this.component_link+'"]').text(),
                    href: base_url+'admin/'+this.component_link,
                    source: 'direct'
                };
            } else {

                var obj = this;

                $.each(componentArray, function(){
                    if (this.id == obj.component_link.replace(/[\/]+/g,'-'))
                        this.source = 'direct';
                });
            }

        }

        this.loadComponentsArray(componentArray);

    },

    loadComponentsArray: function(componentArray){
        var object = this;
        $(componentArray).each(function(key){
            var component_id,
                ourObject,
                id;

            object.href = this.href;

            if (this.id.indexOf('-') !== -1) {

                component_id = object.href.split(base_url)[1].split('/')[1];
                ourObject = jlinq.from(component_types).equals("id", parseInt(component_id)).select()[0];
                id = this.href.split(base_url+'admin/')[1].replace(/[\/]+/g,'-');


            } else {

                ourObject = jlinq.from(component_types).equals("name", this.id).select()[0];
                component_id = ourObject.id;
                id = this.id;

            }

            var idElem = 'component-'+id;

            object.admin_client_controller = ourObject.admin_client_controller;
            object.name = ourObject.name;
            object.component_type_id = component_id;
            object.component_id = id;
            object.title = this.title;
            object.idElem = idElem;
            object.source = this.source;

            var params = this.id.split('-');

            var i=0;
            object.param = [0];

            if (this.id.indexOf('-') !== -1) {

                $(params).each(function(key){

                    if(key > 1 && this !== '' ){

                        object.param[i] = this;
                        i++;

                    }

                });

            }

            var show_comp = false;

            if (object.href === base_url+'admin/'+object.component_link)
                show_comp = true;

            object.addComponent(show_comp);

        });


    },

    '.new_component click': function(el, ev){
        ev.preventDefault();
        ev.stopPropagation();

        var $el = $(el),
            component_id,
            ourObject = false,
            id = $el.attr('href').split(base_url+'admin/')[1];

        $el.parent().parent().find('li').removeClass('active');
        $el.parent().addClass('active');

        if (id == '#' || id == 0) {
            return;
        }

        id = id.replace(/[\/]+/g,'-');


        if ($el.attr('href').split(base_url+'admin/')[1].indexOf('\/') !== -1) {

            component_id = $el.attr('href').split(base_url)[1].split('/')[1];

        } else {

            ourObject = jlinq.from(component_types).equals("name", $el.attr('href').split(base_url+'admin/')[1]).select()[0];
            component_id = ourObject.id;

        }

        if (parseInt(component_id) !== 0){

            var idElem = 'component-'+id;

            ourObject = ourObject ? ourObject : jlinq.from(component_types).equals("id", parseInt(component_id)).select()[0];

            this.admin_client_controller = ourObject.admin_client_controller;
            this.name = ourObject.name;
            this.component_type_id = component_id;
            this.component_id = id;
            this.title = $el.attr('title');
            this.idElem = idElem;
            this.href = $el.attr('href');
            this.source = 'click';

            var params = id.split('-');
            var i=0;
            var object=this;
            this.param = [0];

            $(params).each(function(key){

                if(key > 1 && this != '' ){

                    object.param[i] = this;
                    i++;

                }

            });

            this.addComponent(true);

        } else {
            init_pnotify(6000, 'Этот пункт не связан ни с каким из компонентов', 'error');
        }
    },

    addComponent: function(showComp){
        var componentObject = {};

        componentObject.admin_client_controller = this.admin_client_controller;
        componentObject.name = this.name;
        componentObject.component_type_id = this.component_type_id;
        componentObject.component_id = this.component_id;
        componentObject.title = this.title;
        componentObject.idElem = this.idElem;
        componentObject.href = this.href;
        componentObject.param = this.param;
        componentObject.source = this.source;

        if (Navigation.flightControllers[this.idElem]!==true) {

            if (componentObject.source == 'click')
                OpenAjax.hub.publish('component.load');

            this.stealClientApp(componentObject, showComp);
        }
        else{
            Navigation.addCookies(componentObject);
            $('#'+componentObject.idElem).data("closed", "opened");
            Navigation.showComponent(componentObject.idElem, componentObject.title, false);
            var link = base_url+'admin/'+componentObject.href.split(base_url+'admin/')[1];
            this.setHistoryLink(link, componentObject.title, 'start', null, false);
        }

    },

    stealClientApp: function(componentObject, showComp){

        var object = this;

        steal(componentObject.admin_client_controller).then(function(){

            object.initComponent(componentObject, showComp);

        });

    },

    initComponent: function(component, showComp){

        if(Navigation.flightControllers[component.idElem]!=true){

            $('#tab_navigation').append('<li class="nav_tab"><a href="'+component.idElem+'">'+component.title+'<i class="glyphicon glyphicon-remove-sign close_tab"></i></a></li>');

            $('#tab_container').append('<div style="padding: 10px 10px 0;" class="tab_content tab_component_content" id="'+component.idElem+'"></div>');

            $('#'+component.idElem).data("closed", "opened");
            $('#'+component.idElem).data("display", "none");

            var clientController = component.name;
            clientController = clientController.substr(0, 1).toUpperCase()+clientController.substr(1, clientController.length-1);

            var rand = Math.floor(Math.random() * (10000 - parseInt(component.component_type_id) + 1)) + parseInt(component.component_type_id);
            var controllerOnFlight = clientController+".extend('"+clientController+rand+"',{},{})";

            eval(controllerOnFlight);
            Navigation.flightControllers[component.idElem] = true;

            var initComponentString = '$("#'+component.idElem+'").'+component.name+rand+'('+component.param+', '+component.component_type_id+');';

            if (showComp && (component.source == 'click' || component.source == 'direct' )) {

                if (component.source == 'direct')
                    Navigation.showComponent(component.idElem, component.title, true);
                else
                    Navigation.showComponent(component.idElem, component.title, false);
            }

            eval(initComponentString);

            Navigation.registeredComponents[Navigation.registeredComponents.length] = {
                id: component.component_id,
                href: component.href,
                title: component.title
            };

            Navigation.addCookies(component);

        }
        else{
            steal.dev.log('There is such controller!');

            Navigation.showComponent(component.idElem, component.title);

        }

    },

    setHistoryLink: function(link, title, action_type, id, replaceState){

        if(typeof replaceState =="undefined"){

            replaceState = false;

        }

        if(document.location.href!=link||action_type=='start'||action_type=='close'||action_type=='minimize'){

            var openedComponents = [];

            $.each($('.tab_component_content'), function(){

                var compId = $(this).attr('id').split('component-')[1];
                openedComponents[openedComponents.length] = {

                    closed: $(this).data('closed'),
                    display: $(this).data('display'),
                    id: compId

                }

            });

            Navigation.currentHistoryStep++;
            Navigation.nextOpenedWindows[Navigation.currentHistoryStep] = openedComponents;

            var data={

                history_step: Navigation.currentHistoryStep,
                id: id,
                title: title

            }

            if(replaceState==false){

                history.pushState(data, '', link);
                Navigation.currentHistoryId = id;

            }
            else{

                history.replaceState(data, '', link);

            }

        }
    },

    '#tab_navigation .nav_tab click': function(el, ev) {
        ev.preventDefault();
        ev.stopPropagation();

        if (!$(el).hasClass('active')){

            tab_navigation(el, 'nav_tab', 'active', '#tab_container', 'curr', 'tab_content', '#work_space');

            var href = $(el).find('a').attr('href'),
            title = $(el).text(),
            obj = this;

            $.each($('.tab_component_content'), function(){
                $(this).data('display', 'none');
            });

            $('#'+href).data('display', 'block');
            var link = base_url+'admin/'+href.split('component-')[1].replace(/[-]+/g,'/');
            obj.setHistoryLink(link, title, 'start', null, false);


            setTimeout(function(){
                OpenAjax.hub.publish('component.tabclick', href);
            }, 200);

        }
    },

    '.close_tab click': function(el, ev){
        ev.preventDefault();
        ev.stopPropagation();

        if ($('#tab_navigation .close_tab:visible').length >= 2) {

            var component_id = $(el).parents('a').attr('href').split('component-')[1];

            var href = $(el).parents('a').attr('href');

            $('#'+href).data('closed', 'closed');

            Navigation.deleteFromCache(component_id);

            $(el).parents('li').fadeOut(300);

            $('#'+href).fadeOut(300, function(){
                $('#'+href).removeClass('curr');
            });

            if ($(el).parents('li').hasClass('active')){

                $(el).parents('li').removeClass('active');

                var tab_count = 0,
                elements_indexes = [];

                $.each($('#tab_navigation .nav_tab'), function(){
                    if ($(this).is(':visible')){
                        if (!$(this).hasClass('active')) {
                            elements_indexes[elements_indexes.length] = $('#tab_navigation .nav_tab').index(this);
                            tab_count++;
                        }
                    }
                });

                if (tab_count > 0) {
                    href = $('#tab_navigation .nav_tab:eq('+elements_indexes[0]+')').find('a').attr('href');
                    var title = $('#tab_navigation .nav_tab:eq('+elements_indexes[0]+')').find('a').text();

                    Navigation.showComponent(href, title, false);
                }
            }

            this.setRestUrlTitle('close');

        }

    },

    setRestUrlTitle: function(reason){
        if(typeof reason=="undefined"){
            reason = 'minimize';
        }

        var obj = this;
        var newTitle = false;

        $('.tab_component_content').each(function(){

            if($(this).data("closed")!='closed' && $(this).data("display")=='block'){

                var id = $(this).attr('id');

                newTitle = $('a[href="'+id+'"]').text();
                $('title').html(newTitle);

                var link = base_url+'admin/'+id.split('component-')[1].replace(/[-]+/g,'/');

                obj.setHistoryLink(link, newTitle, true, reason, false);
//                obj.setHistoryLink(link, newTitle, true, reason, true);

                return false;

            }

        });

        if(newTitle===false){

            $('title').html(Navigation.currentTitel);
            this.setHistoryLink(base_url+'admin/', '', true, reason, false);

        }

    },

    /**
     * Preloader handler
     */

    'page.load subscribe': function(){

        showPreload();

    },

    'page.loaded subscribe': function(){

        hidePreload();

    },

    'component.load subscribe': function(){

        showCompPreload();

    },

    'component.loaded subscribe': function(){

        hideCompPreload();

    },

    /*
     * Helper functions
     */

     '{window} resize': function() {

         if ($('.window-container').length) {
             $('.window-container').css({
                 width: findWindowWidth(),
                 height: findWindowHeight()
             });
         }

     },

     tab: function(a) {
        return $(a.attr("href"));
    },
    // hides old active tab, shows new one

    ".tab click": function(el, ev) {
        ev.preventDefault();

        if (!$(el).hasClass('disabled')) {
            var $wrap = $(el).parent();

            this.tab($wrap.find('.active').removeClass('active')).hide();
            this.tab(el.addClass('active')).show();
        }

    },

    error: function(){
        alert('Undefined error')
    }

});